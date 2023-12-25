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
        Schema::create('user_o_t_p_s', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('otp_code', 6);
            $table->dateTime('expired_at');

            $table->foreign('user_id')->on('users')->references('user_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_o_t_p_s');
    }
};
