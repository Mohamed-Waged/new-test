<?php

namespace Tests\Unit\Auth;

use Faker\Factory;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class ResetNewPasswordTest extends TestCase
{
    protected $apiUrl = '/api/v1/auth';

    // Test Reset New Password - Valid Data
    public function test_reset_new_password_with_valid_data()
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

        $user = DB::table('users')->where('email', $email)->first();

        // Second - Reset New Password with valid data
        $updateResponse = $this->post($this->apiUrl . '/reset-new-password', [
            'email' => $email,
            'validation_code' => $user->validation_code,
            'new_password' => 'new_password',
            'new_password_confirmation' => 'new_password'
        ]);

        $this->assertEquals(200, $updateResponse->status());
    }
}
