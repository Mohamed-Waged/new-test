<?php

namespace Tests\Unit\Auth;

use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    protected $apiUrl = '/api/v1/backend/auth';

    // Test Admin Login - Invalid Credentials
    public function test_admin_login_fails_with_invalid_credentials()
    {
        $response = $this->post($this->apiUrl . '/login', [
            'email' => 'john@example.com',
            'password' => 'password'
        ]);

        $this->assertEquals(401, $response->status());
    }

    // Test Admin Login - Missing fields
    public function test_admin_login_validation_missing_fields()
    {
        $response = $this->post($this->apiUrl . '/login', [
            'password' => 'password'
        ]);

        $this->assertEquals(422, $response->status());

        $response = $this->post($this->apiUrl . '/login', [
            'email' => 'john@example.com'
        ]);

        $this->assertEquals(422, $response->status());
    }

    // Test Admin Login - Can logout
    public function test_admin_can_logout()
    {
        // First, login with an admin account
        $loginResponse = $this->post($this->apiUrl . '/login', [
            'email' => 'admin@' . env('APP_NAME') . '.com',
            'password' => 'admin'
        ]);
        $accessToken = $loginResponse->json()['data']['accessToken'];

        // Now, attempt to log out
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ]);

        $logoutResponse = $this->post($this->apiUrl . '/logout');

        $this->assertEquals(200, $logoutResponse->status());
    }

    // Test Admin Login - Valid Credentials
    public function test_admin_login_valid_credentials()
    {
        $response = $this->post($this->apiUrl . '/login', [
            'email' => 'admin@' . env('APP_NAME') . '.com',
            'password' => 'admin'
        ]);

        $this->assertEquals(200, $response->status());
    }
}
