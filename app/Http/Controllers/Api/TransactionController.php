<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    private $transactionRepository;

    public function __construct(\App\Interfaces\TransactionInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['type', 'account_id', 'category_id', 'start_date', 'end_date']);
        $transactions = $this->transactionRepository->getAll($request->user()->id, $filters);
        return response()->json(['success' => true, 'data' => $transactions]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'target_account_id' => 'nullable|exists:accounts,id',
            'category_id' => 'nullable|exists:categories,id',
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $transaction = $this->transactionRepository->create($request->all(), $request->user()->id);
        return response()->json(['success' => true, 'data' => $transaction]);
    }

    public function show(Request $request, $id)
    {
        $transaction = $this->transactionRepository->findByIdHash($id, ['account', 'targetAccount', 'category'], $request->user()->id);
        return response()->json(['success' => true, 'data' => $transaction]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'target_account_id' => 'nullable|exists:accounts,id',
            'category_id' => 'nullable|exists:categories,id',
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $transaction = $this->transactionRepository->update($id, $request->all(), $request->user()->id);
        return response()->json(['success' => true, 'data' => $transaction]);
    }

    public function destroy(Request $request, $id)
    {
        $this->transactionRepository->delete($id, $request->user()->id);
        return response()->json(['success' => true, 'message' => 'Transaction deleted']);
    }
}
