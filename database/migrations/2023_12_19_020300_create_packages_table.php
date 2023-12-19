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
        Schema::create('packages', function (Blueprint $table) {
            $table->uuid('package_id')->primary()->unique();
            $table->string('name_package', 20);
            $table->string('weather_package', 20);
            $table->string('temperature_package', 3);
            $table->string('windspeed_package', 3);
            $table->string('total_hour_package', 3);
            $table->string('user_id');
            $table->foreign('user_id')->on('users')->references('user_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
