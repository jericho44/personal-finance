<?php

namespace App\Repositories;

use App\Models\Category;
use App\Interfaces\CategoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CategoryRepository extends BaseRepository implements CategoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    // public function getAll(int $userId)
    // {
    //     return $this->model->where('user_id', $userId)->latest()->get();
    // }

    public function getAll(
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

        if (! empty($withRelations)) {
            $model->with($withRelations);
        }

        if (is_callable($where)) {
            $model->where($where);
        }

        $model->when(strtolower($search), function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where(DB::raw('LOWER(categories.name)'), 'LIKE', "%{$search}%");
            });
        });

        if ($this->filled($filter, ['startDate', 'endDate'])) {
            $from = Carbon::parse($filter['startDate'])->toDateString();
            $to = Carbon::parse($filter['endDate'])->toDateString();
            $model = $model->whereBetween(DB::raw('DATE(categories.created_at)'), [$from, $to]);
        }

        $model->orderBy(
            strtosnake($this->input($sortOption, 'orderCol', 'categories.created_at')),
            strtolower($this->input($sortOption, 'orderDir')) === 'desc' ? 'desc' : 'asc'
        );

        $length = $this->input($paginateOption, 'length', 10);
        if (strtolower($this->input($paginateOption, 'method', 'paginate'))) {
            $model = $model->paginate($length, ['*'], 'page', $this->input($paginateOption, 'page'));
        } else {
            $model = $model->limit($length)->get();
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
        $category = $this->findByIdHash($idHash, [], $userId);
        return parent::update($category, $data);
    }

    public function delete($idHash, $userId = null)
    {
        $category = $this->findByIdHash($idHash, [], $userId);
        return parent::delete($category);
    }
}
