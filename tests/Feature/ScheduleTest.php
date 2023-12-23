<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Schedule;
use Database\Seeders\AdminSeeder;
use Database\Seeders\PackageSeeder;
use Database\Seeders\ScheduleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testGetScheduleByUserSuccess()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class, ScheduleSeeder::class]);

        $result = $this->get('api/user/schedule', headers: [
            'Authorization' => 'user1'
        ])->assertStatus(200)->json();

        self::assertEquals(20, $result['meta']['total']);
        self::assertEquals(10, count($result['data']));
    }

    public function testGetScheduleByUserFailed()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class, ScheduleSeeder::class]);

        $this->get('api/user/schedule', headers: [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }

    public function testGetScheduleById()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class, ScheduleSeeder::class]);

        $schedule = Schedule::query()->limit(1)->first();
        $result = $this->get('api/user/schedule/' . $schedule->schedule_id, headers: [
            'Authorization' => 'user1'
        ])->assertStatus(200)->json();

        self::assertEquals(11, count($result['data']));
    }

    public function testUpdateScheduleById()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class, ScheduleSeeder::class]);

        $old = Schedule::query()->limit(1)->first();
        $this->patch('/api/user/schedule/' . $old->schedule_id, [
            'name_place' => 'nama baru'
        ], [
            'Authorization' => 'user1'
        ])->assertStatus(200)->json();
        $new = Schedule::query()->limit(1)->first();

        self::assertNotEquals($new->name_place, $old->name_place);
    }

    public function testUpdateScheduleByIdFailed()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class, ScheduleSeeder::class]);

        $schedule = Schedule::query()->limit(1)->first();

        $this->patch('/api/user/schedule/' . $schedule->schedule_id, [
            'schedule_date' => '12-22-2023'
        ], [
            'Authorization' => 'user1'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'schedule_date' => [
                        'The schedule date field must match the format Y-m-d.'
                    ]
                ]
            ]);
    }

    public function testStoreScheduleSuccess()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class]);

        $package = Package::where('user_id', 'user_1')->first();

        $result = $this->post('/api/user/schedule', [
            'longitude' => '109.7942546',
            'latitude' => '-7.407899',
            'name_place' => 'wonosobo',
            'schedule_name' => 'padi',
            'schedule_date' => '2023-12-22',
            'package_id' => $package->package_id
        ], [
            'Authorization' => 'user1'
        ])->assertStatus(201)->json();

        self::assertEquals(6, count($result['data']));
        self::assertEquals(6, count($result['data']['schedule_detail']));
    }

    public function testStoreScheduleFailed()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class]);

        $package = Package::where('user_id', 'user_1')->first();
        $this->post('/api/user/schedule', [
            'longitude' => '109.7942546',
            'latitude' => '-7.407899',
            'schedule_name' => 'padi',
            'schedule_date' => '22-12-2023',
            'package_id' => $package->package_id
        ], [
            'Authorization' => 'user1'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'schedule_date' => [
                        'The schedule date field must match the format Y-m-d.'
                    ],
                    'name_place' => [
                        'The name place field is required.'
                    ]
                ]
            ]);
    }
}
