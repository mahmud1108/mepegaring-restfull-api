<?php

namespace Database\Seeders;

use App\Models\UserOTP;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserOtpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserOTP::create([
            'user_id' => 'user_3',
            'otp_code' => '123123',
            'expired_at' => Carbon::now()->addMinutes(10)->format('Y-m-d H:i:s'),
        ]);
    }
}
