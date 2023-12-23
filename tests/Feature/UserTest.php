<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testGetCurrentUser()
    {
        $this->seed(UserSeeder::class);

        $this->get('api/user', headers: [
            'Authorization' => 'user1'
        ])->assertStatus(200);
    }

    public function testUpdateUserSuccess()
    {
        $this->seed(UserSeeder::class);

        $old = User::where('token', 'user1')->first();
        $this->put('api/user/', [
            'name' => 'telah diganti',
            'image' => UploadedFile::fake()->create('asdfasdf.jpg', 123),
        ], [
            'Authorization' => 'user1'
        ])->assertStatus(200)
            ->json();
        $new = User::where('token', 'user1')->first();

        self::assertNotEquals($new->name, $old->name);
    }

    public function testUpdateUserFailed()
    {
        $this->seed(UserSeeder::class);

        $this->put('api/user/', [
            'image' => UploadedFile::fake()->create('asdfasdf.doc', 3000),
        ], [
            'Authorization' => 'user1'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'image' => [
                        'The image field must be a file of type: jpg, png, jpeg.',
                        'The image field must not be greater than 2048 kilobytes.'
                    ]
                ]
            ]);
    }

    public function testUpdateFailedUnique()
    {
        $this->seed(UserSeeder::class);

        $this->put('/api/user', [
            'phone' => '1231230',
            'email' => 'user0@gmail.com'
        ], [
            'Authorization' => 'user1'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'email' => [
                        'The email has already been taken.'
                    ],
                    'phone' => [
                        'The phone has already been taken.'
                    ]
                ]
            ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed(UserSeeder::class);

        $this->delete('api/user', headers: [
            'Authorization' => 'user1'
        ])->assertStatus(200)
            ->assertJson([
                'status' => true
            ]);
    }

    public function testUserRegisterSuccess()
    {
        $result = $this->post('/api/user/register', [
            'phone' => '085640094098'
        ])->assertStatus(201)
            ->json();

        self::assertEquals('085640094098', $result['data']['phone']);
    }

    public function testUserRegisterFailed()
    {
        $this->seed(UserSeeder::class);

        $this->post('/api/user/register', [
            'phone' => '1231231'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'phone' => [
                        'The phone has already been taken.'
                    ]
                ]
            ]);
    }
}
