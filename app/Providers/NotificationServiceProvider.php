<?php

namespace App\Providers;

use App\Notifications\Channels\FirebaseChannel;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Notification::extend('firebase', function ($app) {
            return new FirebaseChannel;
        });
    }
}
