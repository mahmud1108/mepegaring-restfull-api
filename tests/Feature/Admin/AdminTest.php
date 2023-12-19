<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\AdminController;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testAdminLoginSuccess()
    {
        $this->seed(AdminSeeder::class);

        $this->post('/api/admin/login', [
            'email' => 'admin@gmail.com',
            'password' => 'admin'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'admin_id' => 'adm_1',
                    'name' => 'mahmud',
                    'email' => 'admin@gmail.com',
                    'phone' => '123',
                ]
            ]);
    }

    public function testAdminLoginFailed()
    {
        $this->seed(AdminSeeder::class);

        $this->post('/api/admin/login', [
            'email' => '',
            'password' => 'admin'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'email' => [
                        'The email field is required.'
                    ]
                ]
            ]);
    }

    public function testGetCurrentAdmin()
    {
        $this->seed(AdminSeeder::class);

        $this->get('api/admin', headers: [
            'Authorization' => 'admin'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'admin_id' => 'adm_1',
                    'name' => 'mahmud',
                    'email' => 'admin@gmail.com',
                    'phone' => '123',
                ]
            ]);
    }

    public function testGetCurrentAdminFailed()
    {
        $this->seed(AdminSeeder::class);

        $this->get('api/admin', headers: [
            'Authorization' => 'asdf'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }

    public function testUpdateAdminSuccess()
    {
        $this->seed(AdminSeeder::class);

        $this->put('api/admin', [
            'name' => 'nama baru'
        ], [
            'Authorization' => 'admin'
        ])->assertStatus(200);
    }

    public function testUpdateAdminFailed()
    {
        $this->seed(AdminSeeder::class);

        $this->put('/api/admin', [
            'password' => 'adsfdd'
        ], [
            'Authorization' => 'admin'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'password' => [
                        'The password field confirmation does not match.'
                    ]
                ]
            ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed(AdminSeeder::class);

        $this->delete('api/admin/logout', headers: [
            'Authorization' => 'admin'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }
}
