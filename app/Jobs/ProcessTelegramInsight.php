<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessTelegramInsight implements ShouldQueue
{
    use \Illuminate\Foundation\Bus\Dispatchable, \Illuminate\Bus\Queueable, \Illuminate\Queue\InteractsWithQueue, \Illuminate\Queue\SerializesModels;

    public $timeout = 150; // Max time the job can run

    protected $userId;
    protected $chatId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, int $chatId)
    {
        $this->userId = $userId;
        $this->chatId = $chatId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $transactionRepository = app(\App\Interfaces\TransactionInterface::class);
        $budgetRepository = app(\App\Interfaces\BudgetInterface::class);
        $aiService = app(\App\Services\AIService::class);
        $botToken = config('services.telegram.bot_token');

        try {
            // Get transactions from last 30 days
            $transactions = $transactionRepository->getAll($this->userId, [], ['category'], [], [
                'start_date' => now()->subDays(30)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d')
            ], null, null, ['orderCol' => 'date', 'orderDir' => 'desc'], ['length' => 100, 'method' => 'get']);

            $formattedTransactions = $transactions->map(function($t) {
                return [
                    'date' => $t->date,
                    'category_name' => $t->category ? $t->category->name : 'Uncategorized',
                    'type' => $t->type,
                    'amount' => $t->amount
                ];
            })->toArray();

            $budgets = $budgetRepository->getBudgetProgress($this->userId);
            $formattedBudgets = collect($budgets)->map(function($b) {
                return [
                    'category_name' => $b['budget']->category->name,
                    'amount' => $b['budget']->amount,
                    'spent' => $b['spent']
                ];
            })->toArray();

            $result = $aiService->getFinancialInsights($this->userId, $formattedTransactions, $formattedBudgets);

            if ($result['status'] === 'success') {
                $this->sendTelegramMessage($this->chatId, $botToken, $result['content']);
            } else {
                $this->sendTelegramMessage($this->chatId, $botToken, "❌ *AI Insight Failed:* " . $result['message']);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Telegram Insight Job Error: " . $e->getMessage());
            $this->sendTelegramMessage($this->chatId, $botToken, "⚠️ *Timeout or Error:* Looking up your data is taking longer than expected. Please try again or check back in a few minutes.");
        }
    }

    protected function sendTelegramMessage($chatId, $token, $text)
    {
        \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown'
        ]);
    }
}
