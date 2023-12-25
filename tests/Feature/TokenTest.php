<?php

namespace Tests\Feature;

use App\Models\SettingBroadcast;
use Database\Seeders\AdminSeeder;
use Database\Seeders\FakeTokenSeeder;
use Database\Seeders\SettingBroadcastSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TokenTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testGetAllToken()
    {
        $this->seed([AdminSeeder::class, FakeTokenSeeder::class]);

        $result = $this->get('/api/admin/token', headers: [
            'Authorization' => 'admin'
        ])->assertStatus(200)
            ->json();

        self::assertEquals(4, count($result['data']));
    }

    public function testGetAllTokenFailed()
    {
        $this->seed(AdminSeeder::class);

        $this->get('api/admin/token', headers: [
            'Authorization' => 'ssss'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }

    public function testStoreTokenSuccess()
    {
        $this->seed(AdminSeeder::class);

        $this->post('api/admin/token', [
            'token' => 'asdfasdfasdf',
            'name' => 'name',
            'setting_number' => 123984
        ], [
            'Authorization' => 'admin'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'token' => 'asdfasdfasdf',
                    'name' => 'name',
                    'setting_number' => '123984'
                ]
            ]);
    }

    public function testStoreTokenFailedValidatioin()
    {
        $this->seed(AdminSeeder::class);

        $this->post('api/admin/token', [
            'token' => 'asdfasdfasdf',
            'name' => 'name',
            'setting_number' => 'asdddss'
        ], [
            'Authorization' => 'admin'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'setting_number' => [
                        'The setting number field format is invalid.'
                    ]
                ]
            ]);
    }

    public function testShowToken()
    {
        $this->seed([AdminSeeder::class, SettingBroadcastSeeder::class]);

        $token = SettingBroadcast::query()->limit(1)->first();
        $this->get('/api/admin/token/' . $token->setting_id, headers: [
            'Authorization' => 'admin'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    "token" => "eeTq_07FIKFtTYofTNfm",
                    "name" => "first",
                    "setting_number" => "085640094098"
                ]
            ]);
    }

    public function testShowTokenNotFound()
    {
        $this->seed([AdminSeeder::class, SettingBroadcastSeeder::class]);

        $this->get('/api/admin/token/123123', headers: [
            'Authorization' => 'admin'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    "message" => [
                        'Not found'
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([AdminSeeder::class, SettingBroadcastSeeder::class]);

        $token = SettingBroadcast::query()->limit(1)->first();
        $this->put('/api/admin/token/' . $token->setting_id, [
            'name' => 'ganti'
        ], [
            'Authorization' => 'admin'
        ])->assertStatus(200);
        $new = SettingBroadcast::query()->limit(1)->first();

        self::assertNotEquals($new->name, $token->name);
    }

    public function testUpdateNotFound()
    {
        $this->seed([AdminSeeder::class, SettingBroadcastSeeder::class]);

        $this->put('/api/admin/token/1234', [
            'name' => 'ganti'
        ], [
            'Authorization' => 'admin'
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
        $this->seed([AdminSeeder::class, SettingBroadcastSeeder::class]);

        $this->put('/api/admin/token/1234', [
            'name' => 'ganti nama baru dengan karakter banyak'
        ], [
            'Authorization' => 'admin'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field must not be greater than 20 characters.'
                    ]
                ]
            ]);
    }

    public function testTokenDeleteSuccess()
    {
        $this->seed([AdminSeeder::class, SettingBroadcastSeeder::class]);

        $token = SettingBroadcast::query()->limit(1)->first();
        $this->delete('/api/admin/token/' . $token->setting_id, headers: [
            'Authorization' => 'admin'
        ])->assertStatus(200)
            ->assertJson([
                'status' => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([AdminSeeder::class, SettingBroadcastSeeder::class]);

        $this->delete('/api/admin/token/12341234', headers: [
            'Authorization' => 'admin'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ]);
    }
}
