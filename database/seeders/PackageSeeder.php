<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::query()->limit(1)->first();
        for ($i = 0; $i < 10; $i++) {
            Package::create([
                'package_id' => $i,
                'name_package' => 'name_package' . $i,
                'weather_package' => 'weather_package' . $i,
                'temperature_package' => '12',
                'windspeed_package' => '12',
                'total_hour_package' => '12',
                'user_id' => null,
                'admin_id' => $admin->admin_id,
            ]);
        }

        for ($i = 0; $i < 5; $i++) {
            Package::create([
                'package_id' => $i + 10,
                'name_package' => 'name_package' . $i + 20,
                'weather_package' => 'weather_package' . $i + 20,
                'temperature_package' => '12',
                'windspeed_package' => '12',
                'total_hour_package' => '12',
                'user_id' => 'user_0',
                'admin_id' => null,
            ]);
        }

        for ($i = 0; $i < 5; $i++) {
            Package::create([
                'package_id' => $i + 20,
                'name_package' => 'name_package' . $i + 20,
                'weather_package' => 'weather_package' . $i + 20,
                'temperature_package' => '12',
                'windspeed_package' => '12',
                'total_hour_package' => '12',
                'user_id' => 'user_1',
                'admin_id' => null,
            ]);
        }
    }
}
