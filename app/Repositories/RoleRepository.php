<?php

namespace App\Repositories;

use App\Interfaces\RoleInterface;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RoleRepository extends BaseRepository implements RoleInterface
{
    public function __construct(Role $model)
    {
        $this->model = $model;
    }

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

        $model->select('*');

        if (! empty($withRelations)) {
            $model->with($withRelations);
        }

        if (is_callable($where)) {
            $model->where($where);
        }

        $model->where('slug', '<>', 'developer');

        $model->when(strtolower($search), function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where(DB::raw('LOWER(name)'), 'LIKE', "%{$search}%");
            });
        });

        if ($this->filled($filter, ['startDate', 'endDate'])) {
            $from = Carbon::parse($filter['startDate'])->toDateString();
            $to = Carbon::parse($filter['endDate'])->toDateString();
            $model = $model->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
        }

        if ($this->filled($filter, 'isActive')) {
            $model->where('is_active', $filter['isActive']);
        }

        $model->orderBy(
            strtosnake($this->input($sortOption, 'orderCol', 'created_at')),
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

    public function switchStatus($id)
    {
        $model = $this->resolveIdentity($id);

        return $this->update($model, ['is_active' => ! $model->id_active]);
    }
}
