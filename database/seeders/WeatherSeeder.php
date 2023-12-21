<?php

namespace Database\Seeders;

use App\Models\Weather;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WeatherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Weather::create([
            'weather_id' => '0',
            'weather_name' => 'Clear Sky'
        ]);

        Weather::create([
            'weather_id' => '1',
            'weather_name' => 'Mainly Clear'
        ]);

        Weather::create([
            'weather_id' => '2',
            'weather_name' => 'partly cloudy'
        ]);

        Weather::create([
            'weather_id' => '3',
            'weather_name' => 'overcast'
        ]);

        Weather::create([
            'weather_id' => '45',
            'weather_name' => 'fog'
        ]);

        Weather::create([
            'weather_id' => '48',
            'weather_name' => 'depositing rime fog'
        ]);

        Weather::create([
            'weather_id' => '51',
            'weather_name' => 'Drizzle light'
        ]);

        Weather::create([
            'weather_id' => '53',
            'weather_name' => 'Drizzle moderate'
        ]);

        Weather::create([
            'weather_id' => '55',
            'weather_name' => 'Drizzle dense intensity'
        ]);

        Weather::create([
            'weather_id' => '56',
            'weather_name' => 'freezing drizzle light'
        ]);

        Weather::create([
            'weather_id' => '57',
            'weather_name' => 'freezing drizzle dense intensity'
        ]);

        Weather::create([
            'weather_id' => '61',
            'weather_name' => 'Rain slight'
        ]);

        Weather::create([
            'weather_id' => '63',
            'weather_name' => 'rain moderate'
        ]);

        Weather::create([
            'weather_id' => '65',
            'weather_name' => 'rain heavy intensity'
        ]);

        Weather::create([
            'weather_id' => '66',
            'weather_name' => 'freezing rain light'
        ]);

        Weather::create([
            'weather_id' => '67',
            'weather_name' => 'freezing rain heavy intensity'
        ]);

        Weather::create([
            'weather_id' => '80',
            'weather_name' => 'rain showers slight'
        ]);

        Weather::create([
            'weather_id' => '81',
            'weather_name' => 'rain showers moderate'
        ]);

        Weather::create([
            'weather_id' => '82',
            'weather_name' => 'rain showers violent'
        ]);
    }
}
