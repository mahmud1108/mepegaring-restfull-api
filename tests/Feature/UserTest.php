<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\FakeTokenSeeder;
use Database\Seeders\UserOtpSeeder;
use Database\Seeders\UserOtpSeedInvalid;
use Database\Seeders\UserSeeder;
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
        $this->seed(FakeTokenSeeder::class);
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

    public function testSendOtpForResetPasswordSuccess()
    {
        $this->seed([UserSeeder::class, FakeTokenSeeder::class]);

        $this->post('/api/user/forgot-password', [
            'phone' => '123123123'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'phone' => '123123123'
                ]
            ]);
    }

    public function testSendOtpForResetPasswordFailed()
    {
        $this->seed(UserSeeder::class);

        $this->post('/api/user/forgot-password', [
            'phone' => '1029278303'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ]);
    }

    public function testInputOtpSuccess()
    {
        $this->seed([UserSeeder::class, UserOtpSeeder::class, FakeTokenSeeder::class]);

        $this->post('/api/user/otp?phone=123123123', [
            'otp' => '123123'
        ])->assertStatus(200)
            ->assertJson([
                'status' => true
            ]);
    }

    public function testInputOtpPhoneNotFound()
    {
        $this->seed([UserSeeder::class, UserOtpSeeder::class, FakeTokenSeeder::class]);

        $this->post('/api/user/otp?phone=8374926738', [
            'otp' => '123123'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Phone not found'
                    ]
                ]
            ]);
    }

    public function testInputOtpInvalid()
    {
        $this->seed([UserSeeder::class, UserOtpSeeder::class, FakeTokenSeeder::class]);

        $this->post('/api/user/otp?phone=123123123', [
            'otp' => '111111'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'OTP invalid'
                    ]
                ]
            ]);
    }

    public function testInputOtpInvalidExpired()
    {
        $this->seed([UserSeeder::class, UserOtpSeedInvalid::class, FakeTokenSeeder::class]);

        $this->post('/api/user/otp?phone=123123123', [
            'otp' => '123123'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'OTP invalid'
                    ]
                ]
            ]);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class, UserOtpSeeder::class, FakeTokenSeeder::class]);

        $old = User::where('phone', '123123123')->first();
        $this->post('/api/user/update-password?phone=123123123&otp=123123', [
            'password' => '123123',
            'password_confirmation' => '123123'
        ])->assertStatus(200)
            ->assertJson([
                'status' => true
            ]);
        $new = User::where('phone', '123123123')->first();

        self::assertNotEquals($new->password, $old->password);
    }

    public function testUpdatePasswordFailedValidation()
    {
        $this->seed([UserSeeder::class, UserOtpSeeder::class, FakeTokenSeeder::class]);

        $this->post('/api/user/update-password?phone=123123123&otp=123123', [
            'password' => '123123',
            'password_confirmation' => 'salah'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'password' => [
                        'The password field confirmation does not match.'
                    ]
                ]
            ]);
    }

    public function testResetPasswordByAdmin()
    {
        $this->seed([UserSeeder::class, AdminSeeder::class, FakeTokenSeeder::class]);

        $user = User::query()->limit(1)->first();
        $this->put('/api/admin/reset-password/' . $user->user_id, headers: [
            'Authorization' => 'admin'
        ])->assertStatus(200)
            ->assertJson([
                'status' => true
            ]);
        $new = User::query()->limit(1)->first();

        self::assertNotEquals($new->password, $user->password);
    }
}
