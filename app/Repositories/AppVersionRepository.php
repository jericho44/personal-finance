<?php

namespace App\Repositories;

use App\Interfaces\AppVersionInterface;
use App\Models\AppVersion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppVersionRepository extends BaseRepository implements AppVersionInterface
{
    public function __construct(AppVersion $model)
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

        $model->when(strtolower($search), function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where(DB::raw('LOWER(code)'), 'LIKE', "%{$search}%");
            });
        });

        if ($this->filled($filter, 'isForceUpdate')) {
            $model->where('is_force_update', $this->boolean($filter, 'isForceUpdate'));
        }

        if ($this->filled($filter, 'appType')) {
            $model->where('app_type', $this->boolean($filter, 'appType'));
        }

        if ($this->filled($filter, ['startDate', 'endDate'])) {
            $from = Carbon::parse($filter['startDate'])->toDateString();
            $to = Carbon::parse($filter['endDate'])->toDateString();
            $model = $model->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
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

    public function checkCode($code, $appType, $exceptId = null)
    {
        return $this->model->newModelQuery()
            ->where('code', $code)
            ->where('app_type', $appType)
            ->when($exceptId, function ($query, $id) {
                $query->where('id', '<>', $id);
            })
            ->exists();
    }
}
