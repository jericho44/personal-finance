<?php

namespace App\Repositories;

use App\Models\Account;
use App\Interfaces\AccountInterface;

class AccountRepository extends BaseRepository implements AccountInterface
{
    public function __construct(Account $model)
    {
        parent::__construct($model);
    }

    public function getAll(int $userId)
    {
        return $this->model->where('user_id', $userId)->latest()->get();
    }

    public function findByIdHash($idHash, $withRelations = [], int $userId = null)
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
        return parent::create($data);
    }

    public function update($idHash, $data, $userId = null)
    {
        $account = $this->findByIdHash($idHash, [], $userId);
        return parent::update($account, $data);
    }

    public function delete($idHash, $userId = null)
    {
        $account = $this->findByIdHash($idHash, [], $userId);
        return parent::delete($account);
    }
}
