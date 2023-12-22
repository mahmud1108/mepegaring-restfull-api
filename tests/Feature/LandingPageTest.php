<?php

namespace Tests\Feature;

use App\Models\Package;
use Carbon\Carbon;
use Database\Seeders\AdminSeeder;
use Database\Seeders\PackageSeeder;
use Database\Seeders\UserSeeder;
use Nette\Utils\Random;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testGetAdminPackage()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class]);

        $result = $this->get('/api/package-admin')->assertStatus(200)->json();

        self::assertEquals(10, count($result['data']));
    }

    public function testGetForecast()
    {
        $result = $this->get('api/forecast')->assertStatus(200)->json();

        self::assertEquals(Carbon::now()->format('Y-m-d') . 'T00:00', $result['hourly']['time'][0]);
        self::assertEquals(9, count($result));
    }

    public function testPostForecast()
    {
        $result = $this->post('api/forecast', [
            'start_date' => '2023-12-22',
            'end_date' => '2023-12-23'
        ])->assertStatus(200)->json();

        self::assertEquals('2023-12-22T00:00', $result['hourly']['time'][0]);
        self::assertEquals('2023-12-23T23:00', $result['hourly']['time'][47]);
    }

    public function testCreateTemporarySchedule()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class]);

        $package = Package::query()->limit(1)->first();
        $result =  $this->post('api/temporary-schedule', [
            'package_id' => $package->package_id
        ])->assertStatus(201)->json();

        self::assertEquals(36, count($result['data']['schedule_detail']['weather_code']));
    }

    public function testDeleteTetmporarySchedule()
    {
        $this->delete('api/temporary-schedule')->assertStatus(200)
            ->assertJson([
                'status' => true
            ]);
    }
}
