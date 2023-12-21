<?php

namespace Tests\Feature;

use Database\Seeders\AdminSeeder;
use Database\Seeders\PackageSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
}
