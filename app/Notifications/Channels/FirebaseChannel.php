<?php

namespace App\Notifications\Channels;

use App\Helpers\Firebase;
use App\Models\FirebasePendingNotification;
use Illuminate\Notifications\Notification;
use RuntimeException;

class FirebaseChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! ($config = config('env.fcm_service_account_path'))) {
            return;
        }

        $firebaseTokens = collect($notifiable->firebaseTokens);

        if ($firebaseTokens->isNotEmpty()) {
            $loginFirebaseTokens = $firebaseTokens->where('is_login', true);

            if ($loginFirebaseTokens->isNotEmpty()) {
                (new Firebase($config))->send($firebaseTokens, $this->buildPayload($notifiable, $notification));
            }

            FirebasePendingNotification::createByFirebaseTokens($firebaseTokens->where('is_login', false), $notification);
        }
    }

    /**
     * Build an array payload for the DatabaseNotification Model.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    protected function buildPayload($notifiable, Notification $notification)
    {
        $data = $this->getData($notifiable, $notification);
        if (! array_key_exists('title', $data) || ! array_key_exists('message', $data)) {
            throw new RuntimeException('Notification is required title dan message key.');
        }

        $type = get_class($notification);

        if (method_exists($notification, $method = 'databaseType')) {
            $type = $notification->{$method}($notifiable);
        }

        return [
            'id' => $notification->id,
            'type' => $type,
            'data' => $data,
        ];
    }

    /**
     * Get the data for the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     *
     * @throws \RuntimeException
     */
    protected function getData($notifiable, Notification $notification)
    {
        if (method_exists($notification, $method = 'toFirebase')) {
            return $notification->{$method}($notifiable);
        }

        if (method_exists($notification, $method = 'toArray')) {
            return $notification->{$method}($notifiable);
        }

        throw new RuntimeException('Notification is missing toFirebase / toArray method.');
    }
}
