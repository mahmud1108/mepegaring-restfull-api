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
        Schema::create('schedule_details', function (Blueprint $table) {
            $table->uuid('schedule_details_id')->primary()->unique();
            $table->string('weather_code', 20);
            $table->date('schedule_detail_date');
            $table->string('schedule_detail_hour', 5);
            $table->string('schedule_detail_temperature', 3);
            $table->string('schedule_detail_windspeed', 3);
            $table->string('status', 3)->nullable();
            $table->string('schedule_id', 20);

            $table->foreign('schedule_id')->on('schedules')->references('schedule_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_details');
    }
};
