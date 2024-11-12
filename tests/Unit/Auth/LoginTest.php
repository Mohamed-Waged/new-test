<?php

namespace Tests\Unit\Auth;

use Tests\TestCase;

class LoginTest extends TestCase
{
    protected $apiUrl = '/api/v1/auth';

    // Test Login - Invalid Credentials
    public function test_login_fails_with_invalid_credentials()
    {
        $response = $this->post($this->apiUrl . '/login', [
            'email' => 'user@' . env('APP_NAME') . '.com',
            'password' => 'invalid-password'
        ]);

        $this->assertEquals(401, $response->status());
    }

    // Test Login - Valid Credentials
    public function test_login_with_valid_credentials()
    {
        $response = $this->post($this->apiUrl . '/login', [
            'email' => 'user@' . env('APP_NAME') . '.com',
            'password' => 'user'
        ]);

        $this->assertEquals(200, $response->status());
    }

    // Test Login - Logout
    public function test_user_can_logout()
    {
        // First, login with a default user
        $loginResponse = $this->post($this->apiUrl . '/login', [
            'email' => 'user@' . env('APP_NAME') . '.com',
            'password' => 'user'
        ]);
        $accessToken = $loginResponse->json()['data']['accessToken'];

        // Now, attempt to log out
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept'        => 'application/json'
        ]);

        $response = $this->post($this->apiUrl . '/logout');

        $this->assertEquals(200, $response->status());
    }
}
