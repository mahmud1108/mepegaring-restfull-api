<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'admin_id' => 'adm_1',
            'name' => 'mahmud',
            'email' => 'admin@gmail.com',
            'phone' => '123',
            'password' => Hash::make('admin'),
            'token' => 'admin'
        ]);
    }
}
