<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use App\Interfaces\TransactionInterface;
use App\Interfaces\BudgetInterface;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class FinancialInsightController extends Controller
{
    protected $aiService;
    protected $transactionRepository;
    protected $budgetRepository;

    public function __construct(
        AIService $aiService,
        TransactionInterface $transactionRepository,
        BudgetInterface $budgetRepository
        )
    {
        $this->aiService = $aiService;
        $this->transactionRepository = $transactionRepository;
        $this->budgetRepository = $budgetRepository;
    }

    public function getInsights(Request $request)
    {
        $userId = auth()->id() ?? 1; // Fallback for testing, but should be authenticated

        // Cache key for user insights
        $cacheKey = "user_insights_{$userId}";

        // Try to get from cache first (Gemini quota & efficiency)
        return Cache::remember($cacheKey, 60 * 1, function () use ($userId) {

            // 1. Get transactions for last 30 days
            $transactions = $this->transactionRepository->getAll(
                $userId,
            ['date', 'amount', 'type', 'category_id', 'notes'],
            ['category'],
            [],
            [
                'start_date' => Carbon::now()->subDays(30)->format('Y-m-d'),
                'end_date' => Carbon::now()->format('Y-m-d')
            ],
                null,
                null,
            ['orderCol' => 'date', 'orderDir' => 'desc'],
            ['method' => 'all']
            );

            // 2. Get budget progress
            $budgetProgress = $this->budgetRepository->getBudgetProgress($userId);

            // 3. Format data for AI
            $formattedTransactions = $transactions->map(function ($t) {
                    return [
                    'date' => $t->date,
                    'amount' => $t->amount,
                    'type' => $t->type,
                    'category_name' => $t->category ? $t->category->name : 'Uncategorized'
                    ];
                }
                )->toArray();

                $formattedBudgets = collect($budgetProgress)->map(function ($bp) {
                    return [
                    'category_name' => $bp['budget']->category ? $bp['budget']->category->name : 'Unknown',
                    'amount' => $bp['budget']->amount,
                    'spent' => $bp['spent']
                    ];
                }
                )->toArray();

                // 4. Call AI Service
                $insights = $this->aiService->getFinancialInsights($userId, $formattedTransactions, $formattedBudgets);

                return response()->json($insights);
            });
    }

    public function clearCache()
    {
        $userId = auth()->id();
        Cache::forget("user_insights_{$userId}");
        return response()->json(['message' => 'Insights cache cleared.']);
    }
}
