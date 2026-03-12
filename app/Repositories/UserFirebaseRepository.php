<?php

namespace App\Repositories;

use App\Helpers\Firebase;
use App\Interfaces\UserFirebaseInterface;
use App\Models\FirebasePendingNotification;
use App\Models\UserFirebase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserFirebaseRepository extends BaseRepository implements UserFirebaseInterface
{
    public function __construct(UserFirebase $model)
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

        $model->select('user_firebases.*');

        if (! empty($withRelations)) {
            $model->with($withRelations);
        }

        $model->join('users', 'users.id', 'user_firebases.user_id');

        if (is_callable($where)) {
            $model->where($where);
        }

        $model->when(strtolower($search), function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where(DB::raw('LOWER(user_firebases.token)'), 'LIKE', "%{$search}%");
            });
        });

        if ($this->filled($filter, ['startDate', 'endDate'])) {
            $from = Carbon::parse($filter['startDate'])->toDateString();
            $to = Carbon::parse($filter['endDate'])->toDateString();
            $model = $model->whereBetween(DB::raw('DATE(user_firebases.created_at)'), [$from, $to]);
        }

        $model->orderBy(
            strtosnake($this->input($sortOption, 'orderCol', 'user_firebases.created_at')),
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

    public function login($userId, $token)
    {
        if (! config('env.fcm_service_account_path') || ! $token) {
            return;
        }

        $attributes = [
            'user_id' => $userId,
            'token' => $token,
        ];

        $values = [
            'is_login' => true,
            'last_active_at' => Carbon::now(),
        ];

        $model = $this->model->where($attributes)->first() ?: $this->model->newInstance();

        foreach (array_merge($attributes, $values) as $key => $value) {
            $model->$key = $value;
        }

        $model->save();

        // firebase pending notifications
        $notifications = DB::table('notifications as n')
            ->select('n.*')
            ->join('firebase_pending_notifications as fpn', 'fpn.notification_id', 'n.id')
            ->where('fpn.user_firebase_id', $model->id)
            ->where('fpn.expires_at', '>=', Carbon::now())
            ->orderBy('fpn.created_at')
            ->get();

        if ($notifications->isNotEmpty()) {
            foreach ($notifications as $notification) {
                (new Firebase(config('env.fcm_service_account_path')))->send($model, [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => (array) json_decode($notification->data),
                ]);

                FirebasePendingNotification::query()
                    ->where('user_firebase_id', $model->id)
                    ->where('notification_id', $notification->id)
                    ->delete();
            }
        }
    }

    public function logout($userId, $token)
    {
        if (! config('env.fcm_service_account_path') || ! $token) {
            return;
        }

        $attributes = [
            'user_id' => $userId,
            'token' => $token,
        ];

        $model = $this->model->where($attributes)->first();

        if ($model) {
            $model->is_login = false;
            $model->last_active_at = Carbon::now();
            $model->save();
        }
    }
}
