<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 2; $i++) {
            User::create([
                'user_id' => 'user_' . $i,
                'name' => 'username' . $i,
                'email' => 'user' . $i . '@gmail.com',
                'phone' => '123123' . $i,
                'password' => Hash::make('user'),
                'address' => 'user',
                'image' => 'user',
                'user_is_active' => 'yes',
                'token' => 'user' . $i,
            ]);
        }
    }
}
