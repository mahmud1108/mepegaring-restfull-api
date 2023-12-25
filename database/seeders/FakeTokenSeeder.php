<?php

namespace Database\Seeders;

use App\Models\SettingBroadcast;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FakeTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 4; $i++) {
            SettingBroadcast::create([
                'setting_id' => 'setting_' . $i + 2,
                'token' => 'eeTq_07FIKFtTYofTNfm',
                'name' => 'first',
                'setting_number' => '085640094098',
            ]);
        }
    }
}
