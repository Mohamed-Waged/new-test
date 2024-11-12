<?php

namespace Tests\Unit\Auth;

use Faker\Factory;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    protected $apiUrl = '/api/v1/auth';

    // Test Registration - Successful
    public function test_registration_successful()
    {
        $faker = Factory::create();

        $email = $faker->unique()->email();

        $response = $this->post($this->apiUrl . '/register', [
            'name' => 'John Doe',
            'email' => $email,
            'password' => 'password123'
        ]);

        $this->assertEquals(201, $response->status());
    }

    // Test Registration - Exiting email
    public function test_registration_with_existing_email()
    {
        $faker = Factory::create();

        $email = $faker->unique()->email();

        // First register the user
        $this->post($this->apiUrl . '/register', [
            'name' => 'Jane Doe',
            'email' => $email,
            'password' => 'password123'
        ]);

        // Attempt to register the same email again
        $response = $this->post($this->apiUrl . '/register', [
            'name' => 'Jane Doe',
            'email' => $email,
            'password' => 'password123'
        ]);

        $this->assertEquals(422, $response->status()); // Assuming 422 Conflict for existing email
    }

    // Test Registration - Invalid email
    public function test_registration_with_invalid_email()
    {
        $response = $this->post($this->apiUrl . '/register', [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password'
        ]);

        $this->assertEquals(422, $response->status()); // Assuming 422 Unprocessable Entity for validation errors
    }

    // Test Registration - Weak password
    public function test_registration_with_weak_password()
    {
        $faker = Factory::create();

        $email = $faker->unique()->email();

        $response = $this->post($this->apiUrl . '/register', [
            'name' => 'John Doe',
            'email' => $email,
            'password' => '123',
        ]);

        $this->assertEquals(422, $response->status()); // Assuming 422 for validation errors
    }

    // Test Registration - Missing fields
    public function test_registration_with_missing_fields()
    {
        $faker = Factory::create();

        $email = $faker->unique()->email();

        $response = $this->post($this->apiUrl . '/register', [
            'email' => $email
        ]);
        $this->assertEquals(422, $response->status()); // Assuming 422 for validation errors

        $response2 = $this->post($this->apiUrl . '/register', [
            'name' => 'John Doe'
        ]);
        $this->assertEquals(422, $response2->status()); // Assuming 422 for validation errors

        $response3 = $this->post($this->apiUrl . '/register', [
            'password' => '123456'
        ]);
        $this->assertEquals(422, $response3->status()); // Assuming 422 for validation errors
    }
}
