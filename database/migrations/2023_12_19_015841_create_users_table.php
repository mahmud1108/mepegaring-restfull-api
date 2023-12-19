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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('user_id')->primary()->unique();
            $table->string('name', 30);
            $table->string('email', 30)->unique();
            $table->string('phone', 20)->unique();
            $table->string('password');
            $table->text('address');
            $table->string('image');
            $table->enum('user_is_active', ['yes', 'no']);
            $table->string('token');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
