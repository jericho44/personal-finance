<?php

namespace App\Repositories;

use App\Interfaces\GoalInterface;
use App\Models\Goal;

class GoalRepository extends BaseRepository implements GoalInterface
{
    public function __construct(Goal $model)
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
            $query->where(function ($q) use ($search) {
                $q->where(\Illuminate\Support\Facades\DB::raw('LOWER(goals.name)'), 'LIKE', "%{$search}%");
            });
        });

        if ($this->filled($filter, 'is_completed')) {
            $model->where('is_completed', $filter['is_completed']);
        }

        $model->orderBy(
            $this->input($sortOption, 'orderCol', 'created_at'),
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

    public function findByIdHash($idHash, $withRelations = [], int $userId = null)
    {
        $query = $this->model->query();
        if ($userId) {
            $query->where('user_id', $userId);
        }
        if (!empty($withRelations)) {
            $query->with($withRelations);
        }
        return $query->where('id_hash', $idHash)->first();
    }

    public function create($data, $userId = null)
    {
        if ($userId) {
            $data['user_id'] = $userId;
        }
        return $this->model->create($data);
    }

    public function update($idHash, $data, $userId = null)
    {
        $model = $this->findByIdHash($idHash, [], $userId);
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }

    public function delete($idHash, $userId = null)
    {
        $model = $this->findByIdHash($idHash, [], $userId);
        if ($model) {
            return $model->delete();
        }
        return false;
    }
}
