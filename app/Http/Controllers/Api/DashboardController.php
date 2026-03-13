<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $currentMonthStart = now()->startOfMonth()->format('Y-m-d');
        $currentMonthEnd = now()->endOfMonth()->format('Y-m-d');

        // Total Balance (Net Worth)
        $totalBalance = \App\Models\Account::where('user_id', $userId)->sum('balance');

        // Income & Expense for current month
        $currentMonthIncome = \App\Models\Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount');

        $currentMonthExpense = \App\Models\Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount');

        // Spending by Category (current month)
        $spendingByCategory = \App\Models\Transaction::with('category')
            ->where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->selectRaw('category_id, sum(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get()
            ->map(function ($transaction) {
                return [
                    'category' => $transaction->category ? $transaction->category->name : 'Uncategorized',
                    'color' => $transaction->category ? $transaction->category->color : '#cccccc',
                    'total' => $transaction->total
                ];
            });

        // 5 Recent Transactions
        $recentTransactions = \App\Models\Transaction::with(['account', 'category', 'targetAccount'])
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Budget Summary
        $activeBudgets = \App\Models\Budget::where('user_id', $userId)
            ->where('start_date', '<=', now()->format('Y-m-d'))
            ->where('end_date', '>=', now()->format('Y-m-d'))
            ->get();
        
        $totalBudgetLimit = $activeBudgets->sum('amount');
        $totalBudgetSpent = 0;
        foreach ($activeBudgets as $budget) {
            $spent = \App\Models\Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->where('category_id', $budget->category_id)
                ->whereBetween('date', [$budget->start_date, $budget->end_date])
                ->sum('amount');
            $totalBudgetSpent += $spent;
        }

        // Upcoming Bills
        $upcomingBillsCount = \App\Models\Bill::where('user_id', $userId)
            ->where('is_paid', false)
            ->where('due_date', '<=', now()->addDays(7)->format('Y-m-d'))
            ->where('due_date', '>=', now()->format('Y-m-d'))
            ->count();

        // Goals Summary
        $goalsCount = \App\Models\Goal::where('user_id', $userId)->count();
        $completedGoalsCount = \App\Models\Goal::where('user_id', $userId)->where('is_completed', true)->count();
        $totalGoalTarget = \App\Models\Goal::where('user_id', $userId)->sum('target_amount');
        $totalGoalCurrent = \App\Models\Goal::where('user_id', $userId)->sum('current_amount');

        return response()->json([
            'success' => true,
            'data' => [
                'net_worth' => floatval($totalBalance),
                'current_month' => [
                    'income' => floatval($currentMonthIncome),
                    'expense' => floatval($currentMonthExpense),
                    'balance' => floatval($currentMonthIncome - $currentMonthExpense)
                ],
                'spending_by_category' => $spendingByCategory,
                'recent_transactions' => $recentTransactions,
                'budgets' => [
                    'total_limit' => floatval($totalBudgetLimit),
                    'total_spent' => floatval($totalBudgetSpent),
                    'percentage' => $totalBudgetLimit > 0 ? round(($totalBudgetSpent / $totalBudgetLimit) * 100, 2) : 0
                ],
                'bills' => [
                    'upcoming_count' => $upcomingBillsCount
                ],
                'goals' => [
                    'total_count' => $goalsCount,
                    'completed_count' => $completedGoalsCount,
                    'overall_progress' => $totalGoalTarget > 0 ? round(($totalGoalCurrent / $totalGoalTarget) * 100, 2) : 0
                ]
            ]
        ]);
    }
}
