<?php

namespace Database\Seeders;

use App\Models\SettingBroadcast;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingBroadcastSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SettingBroadcast::create([
            'setting_id' => 'setting_1',
            'token' => 'eeTq_07FIKFtTYofTNfm',
            'name' => 'first',
            'setting_number' => '085640094098',
        ]);
    }
}
