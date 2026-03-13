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
                $query->where(\Illuminate\Support\Facades\DB::raw('LOWER(accounts.name)'), 'LIKE', "%{$search}%");
                $query->orWhere(\Illuminate\Support\Facades\DB::raw('LOWER(accounts.type)'), 'LIKE', "%{$search}%");
            });
        });

        if ($this->filled($filter, 'type')) {
            $model->where('type', $filter['type']);
        }

        $model->orderBy(
            $this->input($sortOption, 'orderCol', 'created_at'),
            strtolower($this->input($sortOption, 'orderDir')) === 'desc' ? 'desc' : 'asc'
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
