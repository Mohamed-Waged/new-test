<?php

namespace Tests\Unit\Auth;

use Faker\Factory;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class EmailVerificationTest extends TestCase
{
    protected $apiUrl = '/api/v1/auth';

    // Test Email Verification - Valid Data
    public function test_email_verification_with_valid_data()
    {
        $faker = Factory::create();

        $email = $faker->unique()->email();

        // First - Create new user
        $registerResponse = $this->post($this->apiUrl . '/register', [
            'name' => 'John Doe',
            'email' => $email,
            'password' => 'password'
        ]);

        $this->assertEquals(201, $registerResponse->status());

        $user = DB::table('users')->where('email', $email)->first();

        // Second - Verify user with valid validation code
        $verifyResponse = $this->post($this->apiUrl . '/verify', [
            'email' => $email,
            'validation_code' => $user->validation_code
        ]);

        $this->assertEquals(200, $verifyResponse->status());
    }
}
