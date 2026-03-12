<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('firebase_pending_notifications', function (Blueprint $table) {
            $table->foreignId('user_firebase_id')->constrained('user_firebases')->cascadeOnDelete();
            $table->uuid('notification_id');
            $table->timestamp('expires_at');
            $table->timestamps();

            // relasi ke tabel notifications
            $table->foreign('notification_id')
                ->references('id')
                ->on('notifications')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firebase_pending_notifications');
    }
};
