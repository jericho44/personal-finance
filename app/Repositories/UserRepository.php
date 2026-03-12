<?php

namespace App\Repositories;

use App\Interfaces\UserInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository implements UserInterface
{
    public function __construct(User $model)
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

        $model->select('users.*');

        if (! empty($withRelations)) {
            $model->with($withRelations);
        }

        $model->join('roles', 'roles.id', 'users.role_id');

        if (is_callable($where)) {
            $model->where($where);
        }

        $model->where('roles.slug', '<>', 'developer');

        $model->when(strtolower($search), function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where(DB::raw('LOWER(users.name)'), 'LIKE', "%{$search}%");
                $query->where(DB::raw('LOWER(users.username)'), 'LIKE', "%{$search}%");
                $query->where(DB::raw('LOWER(users.email)'), 'LIKE', "%{$search}%");
            });
        });

        if ($this->filled($filter, ['startDate', 'endDate'])) {
            $from = Carbon::parse($filter['startDate'])->toDateString();
            $to = Carbon::parse($filter['endDate'])->toDateString();
            $model = $model->whereBetween(DB::raw('DATE(users.created_at)'), [$from, $to]);
        }

        if ($this->filled($filter, 'roleId')) {
            $model->where('roles.id_hash', $filter['roleId']);
        }

        if ($this->filled($filter, 'isActive')) {
            $model->where('users.is_active', $this->boolean($filter, 'isActive'));
        }

        $model->orderBy(
            strtosnake($this->input($sortOption, 'orderCol', 'users.created_at')),
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

    public function findByUsername($username, $withRelations = [])
    {
        return $this->model
            ->with($withRelations)
            ->where('username', $username)
            ->first();
    }

    public function findByEmail($email, $withRelations = [])
    {
        return $this->model
            ->with($withRelations)
            ->where('email', $email)
            ->first();
    }

    public function switchStatus($id)
    {
        $model = $this->resolveIdentity($id);

        return $this->update($model, ['is_active' => ! $model->is_active]);
    }

    public function updatePassword($id, $password)
    {
        $model = $this->resolveIdentity($id);

        return $this->update($model, ['password' => Hash::make($password)]);
    }

    public function resetPassword($id)
    {
        $model = $this->resolveIdentity($id);

        return $this->update($model, ['password' => Hash::make(config('myconfig.default_password'))]);
    }
}
