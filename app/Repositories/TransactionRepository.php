<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Interfaces\TransactionInterface;
use Illuminate\Support\Facades\DB;
use App\Models\Account;

class TransactionRepository extends BaseRepository implements TransactionInterface
{
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }

    public function getAll(
        int $userId,
        $select = [],
        $withRelations = [],
        $join = [],
        $filter = [],
        $where = null,
        $search = null,
        $sortOption = [],
        $paginateOption = [],
        $reformat = null
    ) {
        $model = $this->model->query();

        if (!empty($select)) {
            $model->select($select);
        }

        $model->where('user_id', $userId);

        if (! empty($withRelations)) {
            $model->with($withRelations);
        }

        if (is_callable($where)) {
            $model->where($where);
        }

        $model->when(strtolower($search), function ($query, $search) {
            $query->where(function ($query) use ($search) {
                // Search in Notes
                $query->where(\Illuminate\Support\Facades\DB::raw('LOWER(transactions.notes)'), 'LIKE', "%{$search}%");
                // Search via relations could be added here
            });
        });

        if ($this->filled($filter, 'type') && $filter['type'] !== 'all') {
            $model->where('type', $filter['type']);
        }
        
        if ($this->filled($filter, 'account_id')) {
            $model->where(function($q) use ($filter) {
                $q->where('account_id', $filter['account_id'])
                  ->orWhere('target_account_id', $filter['account_id']);
            });
        }
        
        if ($this->filled($filter, 'category_id')) {
            $model->where('category_id', $filter['category_id']);
        }
        
        if ($this->filled($filter, 'start_date')) {
            $model->where('date', '>=', $filter['start_date']);
        }
        
        if ($this->filled($filter, 'end_date')) {
            $model->where('date', '<=', $filter['end_date']);
        }

        $model->orderBy(
            $this->input($sortOption, 'orderCol', 'date'),
            strtolower($this->input($sortOption, 'orderDir', 'desc')) === 'asc' ? 'asc' : 'desc'
        );

        $length = $this->input($paginateOption, 'length', 15);
        if (strtolower($this->input($paginateOption, 'method', 'paginate')) === 'paginate' && $length > 0) {
            $model = $model->paginate($length, ['*'], 'page', $this->input($paginateOption, 'page'));
        } else {
            $model = $model->limit($length > 0 ? $length : null)->get();
        }

        if (is_callable($reformat)) {
            $model = $reformat($model);
        }

        return $model;
    }

    public function findByIdHash($idHash, $withRelations = ['account', 'targetAccount', 'category'], int $userId = null)
    {
        $query = $this->model->where('id_hash', $idHash);
        if ($withRelations) {
            $query->with($withRelations);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return $query->firstOrFail();
    }

    public function create($data, $userId = null)
    {
        if ($userId) {
            $data['user_id'] = $userId;
        }

        return DB::transaction(function () use ($data) {
            $transaction = parent::create($data);
            $this->applyTransactionToAccounts($transaction);
            return $transaction;
        });
    }

    public function update($idHash, $data, $userId = null)
    {
        $transaction = $this->findByIdHash($idHash, [], $userId);

        return DB::transaction(function () use ($transaction, $data) {
            // Revert original transaction effect
            $this->revertTransactionFromAccounts($transaction);

            // Update transaction
            $updatedTransaction = parent::update($transaction, $data);

            // Apply new transaction effect
            $this->applyTransactionToAccounts($updatedTransaction);
            
            return $updatedTransaction->load(['account', 'targetAccount', 'category']);
        });
    }

    public function delete($idHash, $userId = null)
    {
        $transaction = $this->findByIdHash($idHash, [], $userId);

        return DB::transaction(function () use ($transaction) {
            $this->revertTransactionFromAccounts($transaction);
            return parent::delete($transaction);
        });
    }

    private function applyTransactionToAccounts(Transaction $transaction)
    {
        $account = Account::find($transaction->account_id);
        
        if ($transaction->type === 'income') {
            $account->balance += $transaction->amount;
        } elseif ($transaction->type === 'expense') {
            $account->balance -= $transaction->amount;
        } elseif ($transaction->type === 'transfer') {
            $account->balance -= $transaction->amount;
            
            if ($transaction->target_account_id) {
                $targetAccount = Account::find($transaction->target_account_id);
                $targetAccount->balance += $transaction->amount;
                $targetAccount->save();
            }
        }
        
        $account->save();
    }

    private function revertTransactionFromAccounts(Transaction $transaction)
    {
        $account = Account::find($transaction->account_id);
        
        if ($transaction->type === 'income') {
            $account->balance -= $transaction->amount;
        } elseif ($transaction->type === 'expense') {
            $account->balance += $transaction->amount;
        } elseif ($transaction->type === 'transfer') {
            $account->balance += $transaction->amount;
            
            if ($transaction->target_account_id) {
                $targetAccount = Account::find($transaction->target_account_id);
                $targetAccount->balance -= $transaction->amount;
                $targetAccount->save();
            }
        }
        
        $account->save();
    }
}
