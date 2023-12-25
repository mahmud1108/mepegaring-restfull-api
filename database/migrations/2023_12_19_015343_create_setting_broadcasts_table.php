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
        Schema::create('setting_broadcasts', function (Blueprint $table) {
            $table->uuid('setting_id')->primary()->unique();
            $table->string('token', 100);
            $table->string('name', 20);
            $table->string('setting_number', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_broadcasts');
    }
};
