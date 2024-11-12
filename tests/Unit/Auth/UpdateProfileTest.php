<?php

namespace Tests\Unit\Auth;

use Faker\Factory;
use Tests\TestCase;

class UpdateProfileTest extends TestCase
{
    protected $apiUrl = '/api/v1/auth';

    // Test Update Profile - Valid Data
    public function test_update_profile_valid_data()
    {
        $faker = Factory::create();

        $email = $faker->unique()->email();

        // First - Create new active user
        $registerResponse = $this->post($this->apiUrl . '/register', [
            'name' => 'John Doe',
            'email' => $email,
            'password' => 'password',
            'is_active' => true
        ]);

        $this->assertEquals(201, $registerResponse->status());

        // Second - Login with that user
        $loginResponse = $this->post($this->apiUrl . '/login', [
            'email' => $email,
            'password' => 'password',
        ]);

        $this->assertEquals(200, $loginResponse->status());

        // Third - Update user with valid data
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $loginResponse->json()['data']['accessToken'],
            'Accept'        => 'application/json'
        ]);

        $updateResponse = $this->post($this->apiUrl . '/update-profile', [
            'name' => 'John Doe Updated',
            'email' => $email,
            'password' => 'password-updated'
        ]);

        $this->assertEquals(200, $updateResponse->status());
    }

}
