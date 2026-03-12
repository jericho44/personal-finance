<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Traits\HasUUID;
use App\Traits\UserTrait;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Wildside\Userstamps\Userstamps;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUUID, Notifiable, SoftCascadeTrait, SoftDeletes, Userstamps, UserTrait;

    protected $guarded = ['id', 'id_hash'];

    protected $hidden = ['password'];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'string',
        'updated_at' => 'string',
        'deleted_at' => 'string',
    ];

    public function notifications()
    {
        return $this->morphMany(CustomDatabaseNotification::class, 'notifiable')->orderBy('created_at', 'desc');
    }

    public function getIsEmailVerifiedAttribute()
    {
        return ! empty($this->email) && ! empty($this->email_verified_at);
    }

    public function getIsGoogle2faEnabledAttribute()
    {
        return ! empty($this->google2fa_secret);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function sendPasswordResetNotification($token)
    {
        $url = url("/reset-password/$token" . '?email=' . $this->email);
        $this->notify(new ResetPasswordNotification($url));
    }

    public function userFirebases()
    {
        return $this->hasMany(UserFirebase::class, 'user_id', 'id');
    }

    public function firebaseTokens()
    {
        return $this->userFirebases()
            ->where('last_active_at', '>=', Carbon::now()->subMinutes(config('env.fcm_pending_notification_expired')));
    }
}
