<?php

namespace Tests\Unit\Auth;

use Faker\Factory;
use Tests\TestCase;

class FcmTokenUpdateTest extends TestCase
{
    protected $apiUrl = '/api/v1/auth';

    // Test Fcm Token Update - Valid Register
    public function test_fcm_token_update_with_valid_register()
    {
        $faker = Factory::create();

        $email = $faker->unique()->email();

        // First - Create new active user
        $response = $this->post($this->apiUrl . '/register', [
            'name' => 'John Doe',
            'email' => $email,
            'password' => 'password',
            'is_active' => true,
            'fcm_token'  => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC+vzVTa3XCO.MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC+vzVTa3XCO'
        ]);

        $this->assertEquals(201, $response->status());

        // Second - Login with that user
        $loginResponse = $this->post($this->apiUrl . '/login', [
            'email' => $email,
            'password' => 'password',
        ]);

        $this->assertEquals(200, $loginResponse->status());

        // Third - Update Fcm Token with valid data
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $loginResponse->json()['data']['accessToken'],
            'Accept'        => 'application/json'
        ]);

        $updateResponse = $this->post($this->apiUrl . '/fcm-token', [
            'fcm_token'  => 'UPDATE_MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC+vzVTa3XCO.MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC+vzVTa3XCO'
        ]);

        $this->assertEquals(200, $updateResponse->status());
    }
}
