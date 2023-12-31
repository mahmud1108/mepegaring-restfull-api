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
        Schema::create('schedules', function (Blueprint $table) {
            $table->uuid('schedule_id')->primary()->unique();
            $table->string('name_place', 100);
            $table->string('schedule_name', 20);
            $table->string('latitude', 20);
            $table->string('longitude', 20);
            $table->date('schedule_date');
            $table->string('user_id')->nullable();
            $table->string('package_id', 20);

            $table->foreign('user_id')->on('users')->references('user_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('package_id')->on('packages')->references('package_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
