<?php

namespace Database\Seeders;

use App\Models\UserOTP;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserOtpSeedInvalid extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserOTP::create([
            'user_id' => 'user_3',
            'otp_code' => '123123',
            'expired_at' => '2023-12-12 00:00:00',
        ]);
    }
}
