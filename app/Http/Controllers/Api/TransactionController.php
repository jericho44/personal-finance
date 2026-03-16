<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{

    private $transactionRepository;
    private $accountRepository;
    private $categoryRepository;

    public function __construct(
        \App\Interfaces\TransactionInterface $transactionRepository,
        \App\Interfaces\AccountInterface $accountRepository,
        \App\Interfaces\CategoryInterface $categoryRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->accountRepository = $accountRepository;
        $this->categoryRepository = $categoryRepository;
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
                'orderCol' => 'created_at',
                'orderDir' => 'DESC'
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
            'account_id' => 'required|exists:accounts,id_hash',
            'target_account_id' => 'nullable|exists:accounts,id_hash',
            'category_id' => 'nullable|exists:categories,id_hash',
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $account = $this->accountRepository->findByIdHash(
            idHash: $request->account_id,
            userId: $request->user()->id
        );
        if (!$account) {
            return ResponseFormatter::error(400, 'Akun tidak ditemukan');
        }

        $targetAccount = null;
        if ($request->target_account_id) {
            $targetAccount = $this->accountRepository->findByIdHash(
                idHash: $request->target_account_id,
                userId: $request->user()->id
            );
            if (!$targetAccount) {
                return ResponseFormatter::error(400, 'Akun tujuan tidak ditemukan');
            }
        }

        $category = $this->categoryRepository->findByIdHash(
            idHash: $request->category_id,
            userId: $request->user()->id
        );
        if (!$category) {
            return ResponseFormatter::error(400, 'Kategori tidak ditemukan');
        }
        $data = [
            'account_id' => $account->id,
            'target_account_id' => $targetAccount?->id,
            'category_id' => $category->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'date' => $request->date,
            'notes' => $request->notes,
        ];


        $transaction = $this->transactionRepository->create($data, $request->user()->id);
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
            'account_id' => 'required|exists:accounts,id_hash',
            'target_account_id' => 'nullable|exists:accounts,id_hash',
            'category_id' => 'nullable|exists:categories,id_hash',
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $account = $this->accountRepository->findByIdHash(
            idHash: $request->account_id,
            userId: $request->user()->id
        );
        if (!$account) {
            return ResponseFormatter::error(400, 'Akun tidak ditemukan');
        }

        $targetAccount = null;
        if ($request->target_account_id) {
            $targetAccount = $this->accountRepository->findByIdHash(
                idHash: $request->target_account_id,
                userId: $request->user()->id
            );
            if (!$targetAccount) {
                return ResponseFormatter::error(400, 'Akun tujuan tidak ditemukan');
            }
        }

        $category = $this->categoryRepository->findByIdHash(
            idHash: $request->category_id,
            userId: $request->user()->id
        );
        if (!$category) {
            return ResponseFormatter::error(400, 'Kategori tidak ditemukan');
        }
        $data = [
            'account_id' => $account->id,
            'target_account_id' => $targetAccount?->id,
            'category_id' => $category->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'date' => $request->date,
            'notes' => $request->notes,
        ];

        $transaction = $this->transactionRepository->update($id, $data, $request->user()->id);
        $transaction->load(['account', 'targetAccount', 'category']);
        return ResponseFormatter::success(new \App\Http\Resources\TransactionResource($transaction), 'Data berhasil diperbarui');
    }

    public function destroy(Request $request, $id)
    {
        $this->transactionRepository->delete($id, $request->user()->id);
        return ResponseFormatter::success(null, 'Data berhasil dihapus');
    }
}
