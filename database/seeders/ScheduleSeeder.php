<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Nette\Utils\Random;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 20; $i++) {
            # code...
            Schedule::create([
                'schedule_id' => 'sch_' . Random::generate(),
                'name_place' => 'wonosobo',
                'schedule_name' => 'mepe kayu',
                'latitude' => '-7.407899',
                'longitude' => '109.7942546',
                'schedule_date' => '2023-12-22',
                'user_id' => 'user_1',
                'package_id' => 20
            ]);
        }
    }
}
