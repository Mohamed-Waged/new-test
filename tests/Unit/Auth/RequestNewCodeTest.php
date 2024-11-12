<?php

namespace Tests\Unit\Auth;

use Faker\Factory;
use Tests\TestCase;

class RequestNewCodeTest extends TestCase
{
    protected $apiUrl = '/api/v1/auth';

    // Test Request New Code - Valid Data
    public function test_request_new_code_with_valid_data()
    {
        $faker = Factory::create();

        $email = $faker->unique()->email();

        // First - Create new active user
        $registerResponse = $this->post($this->apiUrl . '/register', [
            'name' => 'John Doe',
            'email' => $email,
            'password' => 'password'
        ]);

        $this->assertEquals(201, $registerResponse->status());


        // Second - Request new Code with registered email
        $updateResponse = $this->post($this->apiUrl . '/request-new-code', [
            'email' => $email
        ]);

        $this->assertEquals(200, $updateResponse->status());
    }
}
