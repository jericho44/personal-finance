<?php

namespace App\Services;

use Gemini;
use App\Models\Transaction;
use App\Models\Budget;
use Carbon\Carbon;

class AIService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    public function getFinancialInsights(int $userId, array $transactions, array $budgets)
    {
        if (!$this->apiKey) {
            return [
                'status' => 'error',
                'message' => 'Gemini API Key is not configured.'
            ];
        }

        $prompt = $this->buildInsightPrompt($transactions, $budgets);

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(120)
                ->post($this->baseUrl . 'gemini-2.5-flash:generateContent?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->failed()) {
                throw new \Exception('Gemini API Error: ' . $response->body());
            }

            $result = $response->json();
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

            return [
                'status' => 'success',
                'content' => $text,
            ];
        }
        catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'AI Failed: ' . $e->getMessage()
            ];
        }
    }

    private function buildInsightPrompt(array $transactions, array $budgets): string
    {
        $now = Carbon::now()->format('F Y');

        $transactionData = collect($transactions)->map(function ($t) {
            return "{$t['date']}: {$t['category_name']} - {$t['type']} of " . number_format($t['amount'], 0);
        })->implode("\n");

        $budgetData = collect($budgets)->map(function ($b) {
            return "Budget for {$b['category_name']}: " . number_format($b['amount'], 0) . " (Spent: " . number_format($b['spent'], 0) . ")";
        })->implode("\n");

        return "
You are a professional Personal Finance AI Advisor. 
Current Date: {$now}

Below is the user's recent financial activity and budget status.
Please analyze this data and provide:
1. A brief summary of their spending habits this month.
2. At least 3 specific, actionable recommendations to save money or optimize their budget.
3. A 'Financial Health Score' from 1-10 with a one-sentence justification.
4. Flag any anomalies (unusual spending) if detected.

FORMATTING: Use Markdown. Keep it professional, encouraging, and concise. Don't use the user's name. Use currency symbols appropriate for general context or just numbers.
LANGUAGE: Answer in Indonesian (Bahasa Indonesia).

TRANSACTIONS (Last 30 Days):
{$transactionData}

BUDGET STATUS:
{$budgetData}

Your Insights:
";
    }

    public function parseTransactionFromText(string $text, int $userId)
    {
        if (!$this->apiKey) {
            return [
                'status' => 'error',
                'message' => 'Gemini API Key is not configured.'
            ];
        }

        $categories = \App\Models\Category::where('user_id', $userId)->get(['id', 'name']);
        $accounts = \App\Models\Account::where('user_id', $userId)->get(['id', 'name']);

        $prompt = $this->buildTransactionParserPrompt($text, $categories->toArray(), $accounts->toArray());

        try {
            $response = \Illuminate\Support\Facades\Http::post($this->baseUrl . 'gemini-2.0-flash:generateContent?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->failed()) {
                throw new \Exception('Gemini API Error: ' . $response->body());
            }

            $result = $response->json();
            $responseText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // Extract JSON from response
            preg_match('/\{.*\}/s', $responseText, $matches);
            $jsonStr = $matches[0] ?? $responseText;

            $data = json_decode($jsonStr, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'status' => 'error',
                    'message' => 'AI returned invalid JSON: ' . $responseText
                ];
            }

            return [
                'status' => 'success',
                'data' => $data,
            ];
        }
        catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'AI Failed: ' . $e->getMessage()
            ];
        }
    }

    private function buildTransactionParserPrompt(string $text, array $categories, array $accounts): string
    {
        $categoryList = collect($categories)->map(fn($c) => "- ID: {$c['id']}, Name: {$c['name']}")->implode("\n");
        $accountList = collect($accounts)->map(fn($a) => "- ID: {$a['id']}, Name: {$a['name']}")->implode("\n");
        $today = Carbon::now()->format('Y-m-d');

        return "
You are a Financial Transaction Parser. 
Current Date: {$today}
Task: Extract transaction details from the user's message.

USER MESSAGE: \"{$text}\"

AVAILABLE CATEGORIES:
{$categoryList}

AVAILABLE ACCOUNTS:
{$accountList}

RULES:
1. Identify the 'amount' (number only).
2. Map to the closest 'category_id' from the list. If unsure, pick the most logical one.
3. Map to an 'account_id' if mentioned. If no account is mentioned, use the ID of the first account in the list.
4. Identify 'type' (must be 'expense' or 'income').
5. Identify 'date' (format: YYYY-MM-DD). If it says 'today', use {$today}.
6. Provide a short 'description'.

RESPONSE FORMAT (JSON ONLY):
{
    \"amount\": 50000,
    \"category_id\": 1,
    \"account_id\": 1,
    \"type\": \"expense\",
    \"date\": \"2024-03-20\",
    \"description\": \"Coffee at Starbucks\",
    \"status\": \"parsed\"
}
";
    }
}
