<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FirebasePendingNotification extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = null;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * @param  \Illuminate\Support\Collection<string|int, mixed>  $rows
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public static function createByFirebaseTokens($rows, $notification)
    {
        if ($rows->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($rows, $notification) {
            foreach ($rows as $row) {
                $firebasePendingNotification = new FirebasePendingNotification;
                $firebasePendingNotification->user_firebase_id = $row->id;
                $firebasePendingNotification->notification_id = $notification->id;
                $firebasePendingNotification->expires_at = Carbon::now()->addMinutes(config('env.fcm_pending_notification_expired'));
                $firebasePendingNotification->save();
            }
        });
    }
}
