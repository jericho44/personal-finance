<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Services\AIService;
use App\Interfaces\TransactionInterface;
use App\Interfaces\BudgetInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TelegramController extends Controller
{
    protected $aiService;
    protected $transactionRepository;
    protected $budgetRepository;
    protected $botToken;

    public function __construct(
        AIService $aiService, 
        TransactionInterface $transactionRepository,
        BudgetInterface $budgetRepository
    ) {
        $this->aiService = $aiService;
        $this->transactionRepository = $transactionRepository;
        $this->budgetRepository = $budgetRepository;
        $this->botToken = config('services.telegram.bot_token');
    }

    public function handle(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Telegram Webhook Received', $request->all());
        $update = $request->all();
        
        if (isset($update['callback_query'])) {
            return $this->handleCallbackQuery($update['callback_query']);
        }

        if (!isset($update['message'])) {
            return response()->json(['status' => 'ok']);
        }

        $message = $update['message'];
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $telegramId = $message['from']['id'];

        // Find user by Telegram ID
        $user = User::where('telegram_id', $telegramId)->first();

        if (!$user) {
            return $this->handleUnlinkedUser($chatId, $telegramId, $text);
        }

        // Check for active manual entry state
        $manualState = Cache::get("tx_manual_state_{$user->id}");
        if ($manualState && !str_starts_with($text, '/')) {
            return $this->handleManualEntryStep($user, $chatId, $text, $manualState);
        }

        // Handle commands
        if (str_starts_with($text, '/')) {
            return $this->handleCommand($user, $chatId, $text);
        }

        // Handle Natural Language Transaction
        return $this->handleTransaction($user, $chatId, $text);
    }

    protected function handleUnlinkedUser($chatId, $telegramId, $text)
    {
        Log::info('Handling Unlinked User', ['chat_id' => $chatId, 'telegram_id' => $telegramId, 'text' => $text]);
        if (preg_match('/\/start\s+([a-zA-Z0-9]+)/', $text, $matches)) {
            $linkCode = $matches[1];
            $user = User::where('telegram_link_code', $linkCode)->first();

            if ($user) {
                $user->update([
                    'telegram_id' => $telegramId,
                    'telegram_chat_id' => $chatId,
                    'telegram_link_code' => null, // Clear the code after use
                ]);

                $this->sendTelegramMessage($chatId, "✅ Account linked successfully! Welcome, {$user->name}. You can now type transactions like 'Lunch 50k' or 'Salary 10M'.");
                return response()->json(['status' => 'ok']);
            }
        }

        $this->sendTelegramMessage($chatId, "Welcome to Smart Finance Bot! 🤖\n\nPlease link your account first from the web application profile settings.");
        return response()->json(['status' => 'ok']);
    }

    protected function handleTransaction($user, $chatId, $text)
    {
        Log::info('Processing Transaction for User', ['user_id' => $user->id, 'text' => $text]);
        $this->sendTelegramAction($chatId, 'typing');
        
        $parseResult = $this->aiService->parseTransactionFromText($text, $user->id);

        if ($parseResult['status'] === 'success') {
            $data = $parseResult['data'];
            
            // Store pending transaction in cache for 10 minutes
            $cacheKey = "pending_tx_{$user->id}";
            Cache::put($cacheKey, $data, 600);

            $this->sendConfirmationMessage($chatId, $data);
        } else {
            Log::warning('AI Parse failed', ['user_id' => $user->id, 'text' => $text, 'result' => $parseResult]);
            $this->sendTelegramMessage($chatId, "🤔 I couldn't understand that. Try 'Lunch 50k' or 'Internet bill 200k'.");
        }

        return response()->json(['status' => 'ok']);
    }

    protected function sendConfirmationMessage($chatId, $data)
    {
        $amountStr = number_format($data['amount'], 0);
        $typeStr = $data['type'] === 'income' ? '💰 Income' : '💸 Expense';
        
        // Find category name (if it's just ID from AI, we'll try to get it from DB if available)
        $category = \App\Models\Category::find($data['category_id']);
        $categoryName = $category ? $category->name : 'Unknown';

        $msg = "📋 *Is this correct?*\n\n";
        $msg .= "*Amount:* {$amountStr}\n";
        $msg .= "*Type:* {$typeStr}\n";
        $msg .= "*Category:* {$categoryName}\n";
        $msg .= "*Desc:* {$data['description']}";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '✅ Confirm & Save', 'callback_data' => 'tx_save'],
                ],
                [
                    ['text' => '🔄 Switch to ' . ($data['type'] === 'income' ? 'Expense' : 'Income'), 'callback_data' => 'tx_switch_type'],
                    ['text' => '📂 Change Category', 'callback_data' => 'tx_categories'],
                ],
                [
                    ['text' => '❌ Cancel', 'callback_data' => 'tx_cancel'],
                ]
            ]
        ];

        $this->sendTelegramMessage($chatId, $msg, 'Markdown', $keyboard);
    }

    protected function handleCallbackQuery($callbackQuery)
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $messageId = $callbackQuery['message']['message_id'];
        $data = $callbackQuery['data'];
        $telegramId = $callbackQuery['from']['id'];

        Log::info('Handling Callback Query', ['data' => $data, 'telegram_id' => $telegramId]);

        $user = User::where('telegram_id', $telegramId)->first();
        if (!$user) return response()->json(['status' => 'ok']);

        $cacheKey = "pending_tx_{$user->id}";
        $pendingTx = Cache::get($cacheKey);

        $isManual = str_starts_with($data, 'tx_m_') || $data === 'tx_cancel';
        $isCategorySetting = str_starts_with($data, 'tx_set_cat_');
        
        if (!$pendingTx && !$isManual && !$isCategorySetting && !str_starts_with($data, 'cmd_')) {
            $this->editTelegramMessage($chatId, $messageId, "❌ Session expired or no pending transaction found.");
            return response()->json(['status' => 'ok']);
        }

        if ($data === 'cmd_manual') {
            $this->showManualTypeSelection($chatId, $messageId);
        } elseif ($data === 'cmd_insight') {
            $this->handleInsight($user, $chatId);
        } elseif ($data === 'cmd_help') {
            $this->handleCommand($user, $chatId, '/help');
        } elseif ($data === 'tx_save') {
            try {
                $transactionData = [
                    'account_id' => $pendingTx['account_id'],
                    'category_id' => $pendingTx['category_id'],
                    'amount' => $pendingTx['amount'],
                    'type' => $pendingTx['type'],
                    'date' => $pendingTx['date'],
                    'notes' => $pendingTx['description'],
                ];

                $this->transactionRepository->create($transactionData, $user->id);
                $this->editTelegramMessage($chatId, $messageId, "✅ *Transaction Saved!*");
                Cache::forget($cacheKey);
            } catch (\Exception $e) {
                Log::error("Telegram Transaction Save Error: " . $e->getMessage());
                $this->editTelegramMessage($chatId, $messageId, "❌ Failed to save transaction.");
            }
        } elseif ($data === 'tx_switch_type') {
            $pendingTx['type'] = ($pendingTx['type'] === 'income' ? 'expense' : 'income');
            Cache::put($cacheKey, $pendingTx, 600);
            $this->editConfirmationMessage($chatId, $messageId, $pendingTx);
        } elseif ($data === 'tx_categories') {
            $this->showCategorySelection($chatId, $messageId, $user->id, 'tx_set_cat_', $pendingTx['type']);
        } elseif (str_starts_with($data, 'tx_set_cat_')) {
            $categoryId = str_replace('tx_set_cat_', '', $data);
            $pendingTx['category_id'] = $categoryId;
            Cache::put($cacheKey, $pendingTx, 600);
            $this->editConfirmationMessage($chatId, $messageId, $pendingTx);
        } elseif ($data === 'tx_m_start') {
            $this->showManualTypeSelection($chatId, $messageId);
        } elseif (str_starts_with($data, 'tx_m_type_')) {
            $type = str_replace('tx_m_type_', '', $data);
            Cache::put("tx_m_temp_type_{$user->id}", $type, 300);
            $this->showCategorySelection($chatId, $messageId, $user->id, 'tx_m_cat_', $type);
        } elseif (str_starts_with($data, 'tx_m_cat_')) {
            $categoryId = str_replace('tx_m_cat_', '', $data);
            Log::info('Setting Category for Manual Entry', ['category_id' => $categoryId]);
            
            $category = \App\Models\Category::find($categoryId);
            if (!$category) {
                $this->editTelegramMessage($chatId, $messageId, "❌ Error: Category not found.");
                return response()->json(['status' => 'ok']);
            }

            $type = Cache::get("tx_m_temp_type_{$user->id}", 'expense');
            $tempState = [
                'category_id' => $categoryId,
                'category_name' => $category->name,
                'type' => $type
            ];
            Cache::put("tx_m_temp_state_{$user->id}", $tempState, 600);
            
            $this->showAccountSelection($chatId, $messageId, $user->id, 'tx_m_acc_');
        } elseif (str_starts_with($data, 'tx_m_acc_')) {
            $accountId = str_replace('tx_m_acc_', '', $data);
            $account = \App\Models\Account::find($accountId);
            $tempState = Cache::get("tx_m_temp_state_{$user->id}");

            Cache::put("tx_manual_state_{$user->id}", [
                'step' => 'amount',
                'category_id' => $tempState['category_id'],
                'category_name' => $tempState['category_name'],
                'account_id' => $accountId,
                'account_name' => $account ? $account->name : 'Default',
                'type' => $tempState['type']
            ], 600);

            $msg = "📥 *Manual Entry*\n";
            $msg .= "Type: *" . ucfirst($tempState['type']) . "*\n";
            $msg .= "Category: *{$tempState['category_name']}*\n";
            $msg .= "Account: *" . ($account ? $account->name : 'N/A') . "*\n\n";
            $msg .= "Please type the *Amount*:";

            $this->editTelegramMessage($chatId, $messageId, $msg, 'Markdown');
        } elseif ($data === 'tx_back_to_confirm') {
            $this->editConfirmationMessage($chatId, $messageId, $pendingTx);
        } elseif ($data === 'tx_cancel') {
            Cache::forget($cacheKey);
            Cache::forget("tx_manual_state_{$user->id}");
            $this->editTelegramMessage($chatId, $messageId, "❌ Transaction cancelled.");
        }

        return response()->json(['status' => 'ok']);
    }

    protected function showManualTypeSelection($chatId, $messageId)
    {
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '💸 Expense (Keluar)', 'callback_data' => 'tx_m_type_expense'],
                    ['text' => '💰 Income (Masuk)', 'callback_data' => 'tx_m_type_income'],
                ],
                [['text' => '❌ Cancel', 'callback_data' => 'tx_cancel']]
            ]
        ];
        $this->editTelegramMessage($chatId, $messageId, "📝 *Manual Entry*\nSelect the transaction type:", 'Markdown', $keyboard);
    }
    protected function showCategorySelection($chatId, $messageId, $userId, $prefix = 'tx_set_cat_', $typeFilter = null)
    {
        $query = \App\Models\Category::where('user_id', $userId);
        if ($typeFilter) {
            $query->where('type', $typeFilter);
        }
        $categories = $query->get();
        
        $buttons = [];
        $row = [];
        foreach ($categories as $category) {
            $row[] = ['text' => $category->name, 'callback_data' => $prefix . $category->id];
            if (count($row) === 2) {
                $buttons[] = $row;
                $row = [];
            }
        }
        if (!empty($row)) $buttons[] = $row;

        $buttons[] = [['text' => '⬅️ Back', 'callback_data' => $prefix === 'tx_m_cat_' ? 'tx_m_start' : 'tx_back_to_confirm']];
        $keyboard = ['inline_keyboard' => $buttons];
        $this->editTelegramMessage($chatId, $messageId, "📂 *Select a category:*", 'Markdown', $keyboard);
    }

    protected function showAccountSelection($chatId, $messageId, $userId, $prefix = 'tx_m_acc_')
    {
        $accounts = \App\Models\Account::where('user_id', $userId)->get();
        
        $buttons = [];
        $row = [];
        foreach ($accounts as $account) {
            $row[] = ['text' => $account->name, 'callback_data' => $prefix . $account->id];
            if (count($row) === 2) {
                $buttons[] = $row;
                $row = [];
            }
        }
        if (!empty($row)) $buttons[] = $row;

        $buttons[] = [['text' => '⬅️ Back', 'callback_data' => 'tx_m_start']];

        $keyboard = ['inline_keyboard' => $buttons];

        $this->editTelegramMessage($chatId, $messageId, "🏦 *Select an Account:*", 'Markdown', $keyboard);
    }

    protected function handleManualEntryStep($user, $chatId, $text, $state)
    {
        if ($state['step'] === 'amount') {
            $amount = (float) filter_var($text, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);
            if ($amount <= 0) {
                $this->sendTelegramMessage($chatId, "⚠️ Invalid amount. Please type a number (e.g. 50000):");
                return response()->json(['status' => 'ok']);
            }

            $state['amount'] = $amount;
            $state['step'] = 'note';
            Cache::put("tx_manual_state_{$user->id}", $state, 600);
            
            $this->sendTelegramMessage($chatId, "✅ Amount: *" . number_format($amount, 0) . "*\n\nNow, type a short *Note* (or type 'skip'):", 'Markdown');
        } elseif ($state['step'] === 'note') {
            $note = $text;
            if (strtolower($text) === 'skip') $note = "Manual entry via Bot";

            try {
                $transactionData = [
                    'account_id' => $state['account_id'],
                    'category_id' => $state['category_id'],
                    'amount' => $state['amount'],
                    'type' => $state['type'],
                    'date' => now()->format('Y-m-d'),
                    'notes' => $note,
                ];

                $this->transactionRepository->create($transactionData, $user->id);
                
                $msg = "✅ *Manual Transaction Saved!*\n\n";
                $msg .= "*Amount:* " . number_format($state['amount'], 0) . "\n";
                $msg .= "*Account:* " . $state['account_name'] . "\n";
                $msg .= "*Category:* " . $state['category_name'] . "\n";
                $msg .= "*Note:* " . $note;

                $this->sendTelegramMessage($chatId, $msg, 'Markdown');
                Cache::forget("tx_manual_state_{$user->id}");
            } catch (\Exception $e) {
                Log::error("Manual Transaction Save Error: " . $e->getMessage());
                $this->sendTelegramMessage($chatId, "❌ Failed to save transaction. Please try again.");
            }
        }

        return response()->json(['status' => 'ok']);
    }

    protected function handleInsight($user, $chatId)
    {
        $this->sendTelegramAction($chatId, 'typing');
        
        $msg = "🔍 *Analyzing your finances...*\n";
        $msg .= "This usually takes about 30-60 seconds. I'll send you the results as soon as they are ready! ⏳";
        
        $this->sendTelegramMessage($chatId, $msg, 'Markdown');

        // Dispatch background job to avoid webhook timeout
        \App\Jobs\ProcessTelegramInsight::dispatch($user->id, $chatId);

        return response()->json(['status' => 'ok']);
    }

    protected function showMenu($chatId)
    {
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '📝 Manual Entry', 'callback_data' => 'cmd_manual'],
                    ['text' => '📊 AI Insight', 'callback_data' => 'cmd_insight'],
                ],
                [
                    ['text' => '❓ Help Guide', 'callback_data' => 'cmd_help'],
                    ['text' => '❌ Cancel Entry', 'callback_data' => 'tx_cancel'],
                ]
            ]
        ];

        $msg = "🛠 *Main Menu*\nSelect an action from the buttons below:";
        $this->sendTelegramMessage($chatId, $msg, 'Markdown', $keyboard);
        return response()->json(['status' => 'ok']);
    }

    protected function editConfirmationMessage($chatId, $messageId, $data)
    {
        $amountStr = number_format($data['amount'], 0);
        $typeStr = $data['type'] === 'income' ? '💰 Income' : '💸 Expense';
        
        $category = \App\Models\Category::find($data['category_id']);
        $categoryName = $category ? $category->name : 'Unknown';

        $msg = "📋 *Is this correct?*\n\n";
        $msg .= "*Amount:* {$amountStr}\n";
        $msg .= "*Type:* {$typeStr}\n";
        $msg .= "*Category:* {$categoryName}\n";
        $msg .= "*Desc:* {$data['description']}";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '✅ Confirm & Save', 'callback_data' => 'tx_save'],
                ],
                [
                    ['text' => '🔄 Switch to ' . ($data['type'] === 'income' ? 'Expense' : 'Income'), 'callback_data' => 'tx_switch_type'],
                    ['text' => '📂 Change Category', 'callback_data' => 'tx_categories'],
                ],
                [
                    ['text' => '❌ Cancel', 'callback_data' => 'tx_cancel'],
                ]
            ]
        ];

        $this->editTelegramMessage($chatId, $messageId, $msg, 'Markdown', $keyboard);
    }

    protected function handleCommand($user, $chatId, $text)
    {
        if ($text === '/help' || $text === '/start') {
            $msg = "Welcome to *Smart Finance Bot*! 🤖💰\n\n";
            $msg .= "Managing your money has never been easier. Use the commands below or type /menu for buttons:\n\n";
            $msg .= "🗣 *Auto Entry*: Just type like you talk!\n";
            $msg .= "   Example: _'Lunch 50k'_ or _'Gaji 10jt'_\n\n";
            $msg .= "📝 *Manual Entry*: /manual (Step-by-step)\n";
            $msg .= "📊 *AI Insights*: /insight (Financial Analysis)\n";
            $msg .= "🧭 *Commands Menu*: /menu (Show all buttons)\n";
            $msg .= "❌ *Clear State*: /cancel";
            
            $this->sendTelegramMessage($chatId, $msg, 'Markdown');
        } elseif ($text === '/manual') {
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '💸 Expense (Keluar)', 'callback_data' => 'tx_m_type_expense'],
                        ['text' => '💰 Income (Masuk)', 'callback_data' => 'tx_m_type_income'],
                    ],
                ]
            ];
            $this->sendTelegramMessage($chatId, "📝 *Manual Entry Mode*\nSelect the transaction type:", 'Markdown', $keyboard);
        } elseif ($text === '/insight') {
            return $this->handleInsight($user, $chatId);
        } elseif ($text === '/menu') {
            return $this->showMenu($chatId);
        } elseif ($text === '/cancel') {
            Cache::forget("tx_manual_state_{$user->id}");
            Cache::forget("pending_tx_{$user->id}");
            $this->sendTelegramMessage($chatId, "✅ Active entry moves has been cleared.");
        } else {
            $this->sendTelegramMessage($chatId, "Unknown command. Type /help for guidance.");
        }
        return response()->json(['status' => 'ok']);
    }

    protected function sendTelegramMessage($chatId, $text, $parseMode = null, $replyMarkup = null)
    {
        if (!$this->botToken) return;

        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $parseMode
        ];

        if ($replyMarkup) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", $params);
    }

    protected function editTelegramMessage($chatId, $messageId, $text, $parseMode = null, $replyMarkup = null)
    {
        if (!$this->botToken) return;

        $params = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => $parseMode
        ];

        if ($replyMarkup) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        Http::post("https://api.telegram.org/bot{$this->botToken}/editMessageText", $params);
    }

    protected function sendTelegramAction($chatId, $action)
    {
        if (!$this->botToken) return;

        Http::post("https://api.telegram.org/bot{$this->botToken}/sendChatAction", [
            'chat_id' => $chatId,
            'action' => $action
        ]);
    }
}
