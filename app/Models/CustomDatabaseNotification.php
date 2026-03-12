<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;
use Ramsey\Uuid\Uuid;

class CustomDatabaseNotification extends DatabaseNotification
{
    protected static function booted()
    {
        static::creating(function ($notification) {
            $notification->id = Uuid::uuid7()->toString();
        });
    }
}
