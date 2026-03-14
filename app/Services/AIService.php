<?php

namespace App\Services;

use Gemini;
use App\Models\Transaction;
use App\Models\Budget;
use Carbon\Carbon;

class AIService
{
    protected $client;

    public function __construct()
    {
        $apiKey = config('services.gemini.key');
        if ($apiKey) {
            $this->client = Gemini::client($apiKey);
        }
    }

    public function getFinancialInsights(int $userId, array $transactions, array $budgets)
    {
        if (!$this->client) {
            return [
                'status' => 'error',
                'message' => 'Gemini API Key is not configured.'
            ];
        }

        $prompt = $this->buildInsightPrompt($transactions, $budgets);

        try {
            $result = $this->client->generativeModel('gemini-2.5-flash')->generateContent($prompt);
            return [
                'status' => 'success',
                'content' => $result->text(),
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

TRANSACTIONS (Last 30 Days):
{$transactionData}

BUDGET STATUS:
{$budgetData}

Your Insights:
";
    }
}
