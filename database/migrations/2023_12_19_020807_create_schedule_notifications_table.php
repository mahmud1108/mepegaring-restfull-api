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
        Schema::create('schedule_notifications', function (Blueprint $table) {
            $table->uuid('schedule_notification_id')->primary()->unique();
            $table->date('notification_date');
            $table->string('notification_hour', 5);
            $table->string('timezone', 20);
            $table->string('user_id');
            $table->string('schedule_id');

            $table->foreign('user_id')->on('users')->references('user_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('schedule_id')->on('schedules')->references('schedule_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_notifications');
    }
};
