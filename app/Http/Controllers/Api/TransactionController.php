<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
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
        $transactions = $this->transactionRepository->getAll(
            userId: $request->user()->id,
            withRelations: ['account', 'targetAccount', 'category'],
            filter: [
                'type' => $request->type,
                'account_id' => $request->account_id,
                'category_id' => $request->category_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ],
            search: $request->search,
            sortOption: [
                'orderCol' => $request->sort_by,
                'orderDir' => $request->order_by
            ],
            paginateOption: [
                'method' => 'paginate',
                'length' => $request->limit ?? 15,
                'page' => $request->page,
            ],
            reformat: function ($models) {
                if ($models instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                    $models->getCollection()->transform(function ($row) {
                        return new \App\Http\Resources\TransactionResource($row);
                    });
                    return $models;
                }
                
                $models->transform(function ($row) {
                    return new \App\Http\Resources\TransactionResource($row);
                });
                return $models;
            }
        );

        return ResponseFormatter::success($transactions, 'Data berhasil ditampilkan');
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
        $transaction->load(['account', 'targetAccount', 'category']);
        return ResponseFormatter::success(new \App\Http\Resources\TransactionResource($transaction), 'Data berhasil ditambahkan');
    }

    public function show(Request $request, $id)
    {
        $transaction = $this->transactionRepository->findByIdHash($id, ['account', 'targetAccount', 'category'], $request->user()->id);
        if (!$transaction) {
            return ResponseFormatter::error(400, 'Data tidak ditemukan');
        }
        return ResponseFormatter::success(new \App\Http\Resources\TransactionResource($transaction), 'Data berhasil ditampilkan');
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
        $transaction->load(['account', 'targetAccount', 'category']);
        return ResponseFormatter::success(new \App\Http\Resources\TransactionResource($transaction), 'Data berhasil diperbarui');
    }

    public function destroy(Request $request, $id)
    {
        $this->transactionRepository->delete($id, $request->user()->id);
        return ResponseFormatter::success(null, 'Data berhasil dihapus');
    }
}
