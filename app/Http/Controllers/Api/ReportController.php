<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private $transactionRepository;

    public function __construct(\App\Interfaces\TransactionInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @OA\Get(
     *   tags={"Api|Report"},
     *   path="/api/reports/monthly",
     *   summary="Get monthly summary report",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="year", in="query", @OA\Schema(type="integer")),
     *   @OA\Parameter(name="month", in="query", @OA\Schema(type="integer")),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function monthly(Request $request)
    {
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');

        $transactions = DB::table('transactions')
            ->where('user_id', $request->user()->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->select('type', DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->get();

        $income = $transactions->where('type', 'income')->first()->total ?? 0;
        $expense = $transactions->where('type', 'expense')->first()->total ?? 0;

        return ResponseFormatter::success([
            'income' => (float) $income,
            'expense' => (float) $expense,
            'balance' => (float) ($income - $expense)
        ], 'Monthly report');
    }

    /**
     * @OA\Get(
     *   tags={"Api|Report"},
     *   path="/api/reports/yearly",
     *   summary="Get yearly summary report",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="year", in="query", @OA\Schema(type="integer")),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function yearly(Request $request)
    {
        $year = $request->year ?? date('Y');

        $transactions = DB::table('transactions')
            ->where('user_id', $request->user()->id)
            ->whereYear('date', $year)
            ->selectRaw('EXTRACT(MONTH FROM date) as month, type, SUM(amount) as total')
            ->groupByRaw('EXTRACT(MONTH FROM date), type')
            ->get();

        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthTransactions = $transactions->where('month', $i);
            $income = $monthTransactions->where('type', 'income')->first()->total ?? 0;
            $expense = $monthTransactions->where('type', 'expense')->first()->total ?? 0;

            $monthlyData[] = [
                'month' => $i,
                'income' => (float) $income,
                'expense' => (float) $expense
            ];
        }

        return ResponseFormatter::success($monthlyData, 'Yearly report');
    }

    /**
     * @OA\Get(
     *   tags={"Api|Report"},
     *   path="/api/reports/category-expense",
     *   summary="Get expense breakdown by category",
     *   security={{"authBearerToken":{}}},
     *   @OA\Parameter(name="year", in="query", @OA\Schema(type="integer")),
     *   @OA\Parameter(name="month", in="query", @OA\Schema(type="integer")),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function categoryExpense(Request $request)
    {
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');

        $expenses = DB::table('transactions')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $request->user()->id)
            ->where('transactions.type', 'expense')
            ->whereYear('transactions.date', $year)
            ->whereMonth('transactions.date', $month)
            ->select('categories.name as category_name', 'categories.color', DB::raw('SUM(transactions.amount) as total'))
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderByDesc('total')
            ->get();

        return ResponseFormatter::success($expenses, 'Category expense report');
    }
}
