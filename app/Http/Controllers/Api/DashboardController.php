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
                'recent_transactions' => $recentTransactions
            ]
        ]);
    }
}
