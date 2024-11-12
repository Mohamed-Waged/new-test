<?php

namespace Tests\Unit\Auth;

use Faker\Factory;
use Tests\TestCase;

class LocaleUpdateTest extends TestCase
{
    protected $apiUrl = '/api/v1/auth';

    // Test Update User Locale - Valid Data
    public function test_update_user_locale_with_valid_data()
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

        // Third - Update user locale with valid data
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $loginResponse->json()['data']['accessToken'],
            'Accept'        => 'application/json'
        ]);

        $updateResponse = $this->post($this->apiUrl . '/locale', [
            'locale' => 'en'
        ]);

        $this->assertEquals(200, $updateResponse->status());
    }
}
