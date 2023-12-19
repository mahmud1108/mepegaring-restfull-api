<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Package;
use Database\Seeders\AdminSeeder;
use Database\Seeders\PackageSeeder;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

class PackageTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testGetPackageUserLogin()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class]);

        $result = $this->get('/api/user/package', headers: [
            'Authorization' => 'user0'
        ])->assertStatus(200)
            ->json();

        self::assertEquals(15, $result['meta']['total']);
        self::assertEquals(10, count($result['data']));
    }

    public function testGetPackageAdmin()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class]);

        $result = $this->get('/api/admin/package', headers: [
            'Authorization' => 'admin'
        ])->assertStatus(200)
            ->json();

        self::assertEquals(10, $result['meta']['total']);
        self::assertEquals(10, count($result['data']));
    }

    public function testCreatePackageAdminSuccess()
    {
        $this->seed(AdminSeeder::class);

        $this->post('api/admin/package', [
            'name_package' => 'name',
            'weather_package' => ['2', '3', '4'],
            'temperature_package' => '12',
            'windspeed_package' => '12',
            'total_hour_package' => '12',
        ], [
            'Authorization' => 'admin'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    "name_package" => "name",
                    "weather_package" => "2,3,4",
                    "temperature_package" => "12",
                    "windspeed_package" => "12",
                    "total_hour_package" => "12",
                    "admin_id" => "adm_1"
                ]
            ]);
    }

    public function testCreatePackageFailed()
    {
        $this->seed(AdminSeeder::class);

        $this->post('api/admin/package', [
            'name_package' => '',
            'weather_package' => ['2', '3', '4'],
            'temperature_package' => '12',
            'windspeed_package' => '12',
            'total_hour_package' => '12',
        ], [
            'Authorization' => 'admin'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    "name_package" => [
                        'The name package field is required.'
                    ]
                ]
            ]);
    }

    public function testCreatePackageUserSuccess()
    {
        $this->seed(UserSeeder::class);

        $this->post('api/user/package', [
            'name_package' => 'name',
            'weather_package' => ['2', '3', '4'],
            'temperature_package' => '12',
            'windspeed_package' => '12',
            'total_hour_package' => '12',
        ], [
            'Authorization' => 'user0'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    "name_package" => "name",
                    "weather_package" => "2,3,4",
                    "temperature_package" => "12",
                    "windspeed_package" => "12",
                    "total_hour_package" => "12",
                    "user_id" => "user_0"
                ]
            ]);
    }

    public function testGetPackageByIdByAdmin()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class]);

        $package = Package::query()->limit(1)->first();
        $this->get('/api/admin/package/' . $package->package_id, headers: [
            'Authorization' => 'admin'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'package_id' => 0,
                    "name_package" => "name_package0",
                    "weather_package" => "weather_package0",
                    "temperature_package" => "12",
                    "windspeed_package" => "12",
                    "total_hour_package" => "12",
                    "admin_id" => "adm_1",
                ]
            ]);
    }

    public function testGetPackageByIdByUser()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class]);

        $package = Package::query()->where('user_id', 'user_0')->limit(1)->first();
        $this->get('/api/user/package/' . $package->package_id, headers: [
            'Authorization' => 'user0'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    "package_id" => "10",
                    "name_package" => "name_package20",
                    "weather_package" => "weather_package20",
                    "temperature_package" => "12",
                    "windspeed_package" => "12",
                    "total_hour_package" => "12",
                    "user_id" => "user_0",
                ]
            ]);
    }

    public function testGetPackageNotFound()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class]);

        $package = Package::query()->limit(1)->first();
        $this->get('/api/user/package/' . $package->package_id, headers: [
            'Authorization' => 'user0'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ]);
    }

    public function testUpdatePackageByIdByAdmin()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class]);

        $old = Package::query()->limit(1)->first();
        $this->put('api/admin/package/' . $old->package_id, [
            'name_package' => 'nama baru'
        ], [
            'Authorization' => 'admin'
        ])->assertStatus(200);
        $new = Package::query()->limit(1)->first();

        self::assertNotEquals($new->name_package, $old->name_package);
    }

    public function testUpdatePackageByIdByUser()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class]);

        $old = Package::query()->where('user_id', 'user_0')->limit(1)->first();
        $this->put('api/user/package/' . $old->package_id, [
            'name_package' => 'nama baru'
        ], [
            'Authorization' => 'user0'
        ])->assertStatus(200);
        $new = Package::query()->where('user_id', 'user_0')->limit(1)->first();

        self::assertNotEquals($new->name_package, $old->name_package);
    }

    public function testUpdateNotFound()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class]);

        $old = Package::query()->limit(1)->first();
        $this->put('api/user/package/' . $old->package_id, [
            'name_package' => 'nama baru'
        ], [
            'Authorization' => 'user0'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ]);
    }

    public function testUpdateFailedValidation()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class]);

        $old = Package::query()->limit(1)->first();
        $this->put('api/admin/package/' . $old->package_id, [
            'temperature_package' => 'asd'
        ], [
            'Authorization' => 'admin'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'temperature_package' => [
                        'The temperature package field must be an integer.'
                    ]
                ]
            ]);
    }

    public function testDeletePackageByIdByAdmin()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class]);

        $package = Package::query()->limit(1)->first();
        $this->delete('api/admin/package/' . $package->package_id, headers: [
            'Authorization' => 'admin'
        ])->assertStatus(200);
    }

    public function testDeletePackageByIdByUser()
    {
        $this->seed([AdminSeeder::class, UserSeeder::class, PackageSeeder::class]);

        $package = Package::query()->where('user_id', 'user_0')->limit(1)->first();
        $this->delete('api/user/package/' . $package->package_id, headers: [
            'Authorization' => 'user0'
        ])->assertStatus(200);
    }
}
