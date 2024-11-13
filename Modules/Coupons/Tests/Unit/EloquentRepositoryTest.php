<?php

namespace Modules\Coupons\Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EloquentRepositoryTest extends TestCase
{
    //use RefreshDatabase;
    protected $apiURL = '/api/v1/backend/coupons';

    // Test Index - Get all coupons
    public function test_api_index()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);
        $response = $this->get($this->apiURL);

        $this->assertEquals(200, $response->status());
    }

    // Test Store - Missing fields
    public function test_api_store_validation_missing_fields()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        // Test with missing 'lecturer' field
        $request = [
            'couponeableId' => 1,
            'couponeableType' => 'book',
            'couponCount' => 100,
            'couponPercentage' => 50,
            'en' => ['title' => 'test title', 'body' => 'test body'],
            'ar' => ['title' => 'تست', 'body' => 'تست'],
            'sort' => 0,
            'status' => 'active'
        ];
        $response = $this->post($this->apiURL, $request);
        $this->assertEquals(422, $response->status()); // Expecting a validation error

        // Test with missing 'couponCount' field
        $request = [
            'lecturer' => ['id' => 1],
            'couponeableId' => 1,
            'couponeableType' => 'book',
            'couponPercentage' => 50,
            'en' => ['title' => 'test title', 'body' => 'test body'],
            'ar' => ['title' => 'تست', 'body' => 'تست'],
            'sort' => 0,
            'status' => 'active'
        ];
        $response = $this->post($this->apiURL, $request);
        $this->assertEquals(422, $response->status()); // Expecting a validation error
    }

    // Test Store - Invalid EncryptId
    public function test_api_store_invalid_lecturer_id()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        // Attempt to store a coupon with an invalid lecturer ID
        $request = [
            'lecturer' => ['id' => 9999], // Assuming 9999 does not exist
            'couponeableId' => 1,
            'couponeableType' => 'book',
            'couponCount' => 100,
            'couponPercentage' => 50,
            'en' => ['title' => 'test title', 'body' => 'test body'],
            'ar' => ['title' => 'تست', 'body' => 'تست'],
            'sort' => 0,
            'status' => 'active'
        ];
        $response = $this->post($this->apiURL, $request);

        $this->assertEquals(422, $response->status()); // Expecting a validation error


        // Attempt to store a coupon with an invalid couponeableId ID
        $request = [
            'lecturer' => ['id' => 1],
            'couponeableId' => 9999,   // Assuming 9999 does not exist
            'couponeableType' => 'book',
            'couponCount' => 100,
            'couponPercentage' => 50,
            'en' => ['title' => 'test title', 'body' => 'test body'],
            'ar' => ['title' => 'تست', 'body' => 'تست'],
            'sort' => 0,
            'status' => 'active'
        ];
        $response = $this->post($this->apiURL, $request);

        $this->assertEquals(422, $response->status()); // Expecting a validation error
    }

    // Test Store - Non-alphabetic title
    public function test_api_store_non_alphabetic_title()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        // Create a request with a non-alphabetic value for the title
        $request = [
            'lecturer' => ['id' => 1],
            'couponeableId' => 1,
            'couponeableType' => 'book',
            'couponCount' => 100,
            'couponPercentage' => 50,
            'en' => ['title' => '!@#$%^&*()', 'body' => 'test body'], // Non-alphabetic title
            'ar' => ['title' => '!@#$%^&*()', 'body' => 'تست'],
            'sort' => 0,
            'status' => 'active'
            
        ];
        $response = $this->post($this->apiURL, $request);

        $this->assertEquals(422, $response->status()); // Expecting a validation error
    }

    // Test Store - Valid data
    public function test_api_store()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        // Create a Faker instance
        $faker = \Faker\Factory::create();
        $request = [
            'lecturer' => ['id' => DB::table('lecturers')->first()->id],
            'couponeableId' => 1,
            'couponeableType' => 'book',
            'couponCount' => 100,
            'couponPercentage' => 50,
            'en' => ['title' => $faker->sentence(3), 'body' => $faker->paragraph()],
            'ar' => ['title' => $faker->sentence(3), 'body' => $faker->paragraph()],
            'sort' => 0,
            'status' => 'active'
        ];
        $response = $this->post($this->apiURL, $request);

        $this->assertEquals(201, $response->status());
    }

    // Test Show - Invalid EncryptId
    public function test_api_show_invalid_encryptId()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        // Attempt to retrieve a coupon with an invalid encryptId
        $invalidEncryptId = 'invalid-id';
        $response = $this->get($this->apiURL . '/' . $invalidEncryptId);

        $this->assertEquals(404, $response->status());
    }

    // Test Show - Valid EncryptId
    public function test_api_show_valid_encryptId()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);
        $response = $this->get($this->apiURL);
        $encryptId = $response['data']['rows'][0]['encryptId'] ?? NULL;

        // Attempt to retrieve a coupon with an valid encryptId
        $response2 = $this->get($this->apiURL . '/' . $encryptId);
        $this->assertEquals(200, $response2->status());
    }

    // Test Update - Missing fields
    public function test_api_update_validation_missing_fields()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        // First, create a coupon to ensure there's something to update
        $faker = \Faker\Factory::create();
        $request = [
            'lecturer' => ['id' => 1],
            'couponeableId' => 1,
            'couponeableType' => 'book',
            'couponCount' => 100,
            'couponPercentage' => 50,
            'en' => ['title' => $faker->sentence(3), 'body' => $faker->paragraph()],
            'ar' => ['title' => $faker->sentence(3), 'body' => $faker->paragraph()],
            'sort' => 0,
            'status' => 'active'
        ];
        $this->post($this->apiURL, $request);

        // Retrieve the created book to get its encryptId
        $response = $this->get($this->apiURL);
        $encryptId = $response['data']['rows'][0]['encryptId'] ?? NULL;

        // Test with missing 'lecturer' field
        $updateRequest = [
            'book_type' => ['id' => 1],
            'couponeableId' => 2,
            'couponeableType' => 'news',
            'couponCount' => 120,
            'couponPercentage' => 70,
            'en' => ['title' => 'Updated title', 'body' => 'Updated body'],
            'ar' => ['title' => 'تحديث', 'body' => 'تحديث'],
            'sort' => 1,
            'status' => 'inactive'
        ];
        $response = $this->put($this->apiURL . '/' . $encryptId, $updateRequest);
        $this->assertEquals(422, $response->status()); // Expecting a validation error

        // Test with missing 'couponCount' field
        $updateRequest = [
            'lecturer' => ['id' => 1],
            'couponeableId' => 2,
            'couponeableType' => 'news',
            'couponPercentage' => 70,
            'en' => ['title' => 'Updated title', 'body' => 'Updated body'],
            'ar' => ['title' => 'تحديث', 'body' => 'تحديث'],
            'sort' => 1,
            'status' => 'inactive'
        ];
        $response = $this->put($this->apiURL . '/' . $encryptId, $updateRequest);
        $this->assertEquals(422, $response->status()); // Expecting a validation error
    }

    // Test Update - Invalid EncryptId
    public function test_api_update_invalid_encryptId()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        // Prepare updated data
        $updateRequest = [
            'lecturer' => ['id' => 1],
            'couponeableId' => 2,
            'couponeableType' => 'news',
            'couponCount' => 120,
            'couponPercentage' => 70,
            'en' => ['title' => 'Updated title', 'body' => 'Updated body'],
            'ar' => ['title' => 'تحديث', 'body' => 'تحديث'],
            'sort' => 1,
            'status' => 'inactive'
        ];

        // Attempt to update a coupon with an invalid encryptId
        $invalidEncryptId = 'invalid-id';
        $response = $this->put($this->apiURL . '/' . $invalidEncryptId, $updateRequest);

        $this->assertEquals(404, $response->status());
    }

    // Test Update - Non-alphabetic title
    public function test_api_update_non_alphabetic_title()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        // First, create a coupon to ensure there's something to update
        $faker = \Faker\Factory::create();
        $request = [
            'lecturer' => ['id' => 1],
            'couponeableId' => 1,
            'couponeableType' => 'book',
            'couponCount' => 100,
            'couponPercentage' => 50,
            'en' => ['title' => $faker->sentence(3), 'body' => $faker->paragraph()],
            'ar' => ['title' => $faker->sentence(3), 'body' => $faker->paragraph()],
            'sort' => 0,
            'status' => 'active'
        ];
        $this->post($this->apiURL, $request);

        // Retrieve the created coupon to get its encryptId
        $response = $this->get($this->apiURL);
        $encryptId = $response['data']['rows'][0]['encryptId'] ?? NULL;

        // Prepare update request with a non-alphabetic value for the title
        $updateRequest = [
            'lecturer' => ['id' => 1],
            'couponeableId' => 1,
            'couponeableType' => 'book',
            'couponCount' => 100,
            'couponPercentage' => 50,
            'en' => ['title' => '!@#$%^&*()', 'body' => 'Updated body'], // Non-alphabetic title
            'ar' => ['title' => '!@#$%^&*()', 'body' => 'تحديث'],
            'sort' => 1,
            'status' => 'inactive'
        ];

        // Attempt to update the coupon using the valid encryptId
        $response2 = $this->put($this->apiURL . '/' . $encryptId, $updateRequest);
        $this->assertEquals(422, $response2->status()); // Expecting a validation error
    }

    // Test Update - Valid data
    public function test_api_update()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        // First, create a coupon to ensure there's something to update
        $faker = \Faker\Factory::create();
        $request = [
            'lecturer' => ['id' => DB::table('lecturers')->first()->id],
            'couponeableId' => 1,
            'couponeableType' => 'book',
            'couponCount' => 100,
            'couponPercentage' => 50,
            'en' => ['title' => $faker->sentence(3), 'body' => $faker->paragraph()],
            'ar' => ['title' => $faker->sentence(3), 'body' => $faker->paragraph()],
            'sort' => 0,
            'status' => 'active'
        ];
        $this->post($this->apiURL, $request);

        // Retrieve the created coupon to get its encryptId
        $response = $this->get($this->apiURL);
        $encryptId = $response['data']['rows'][0]['encryptId'] ?? NULL;

        // Prepare updated data
        $updateRequest = [
            'lecturer' => ['id' => 1],
            'couponeableId' => 1,
            'couponeableType' => 'book',
            'couponCount' => 110,       // Updated couponCount
            'couponPercentage' => 40,   // Updated couponPercentage   
            'en' => ['title' => $faker->sentence(4), 'body' => $faker->paragraph()],
            'ar' => ['title' => $faker->sentence(4), 'body' => $faker->paragraph()],
            'sort' => 1,
            'status' => 'inactive' // Updated status
        ];

        // Attempt to update the coupon using the valid encryptId
        $response2 = $this->put($this->apiURL . '/' . $encryptId, $updateRequest);
        $this->assertEquals(200, $response2->status());
    }

    // Test Destroy - Invalid EncryptId
    public function test_api_destroy_invalid_encryptId()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        // Attempt to retrieve a coupon with an invalid encryptId
        $invalidEncryptId = 'invalid-id';
        $response = $this->delete($this->apiURL . '/' . $invalidEncryptId);

        $this->assertEquals(500, $response->status());
    }

    // Test Destroy - Valid EncryptId
    public function test_api_destroy_valid_encryptId()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        // First, create a coupon to ensure there's something to delete
        $faker = \Faker\Factory::create();
        $request = [
            'lecturer' => ['id' => 1],
            'couponeableId' => 1,
            'couponeableType' => 'book',
            'couponCount' => 100,
            'couponPercentage' => 50,
            'en' => ['title' => $faker->sentence(3), 'body' => $faker->paragraph()],
            'ar' => ['title' => $faker->sentence(3), 'body' => $faker->paragraph()],
            'sort' => 0,
            'status' => 'active'
        ];
        $this->post($this->apiURL, $request);

        // Retrieve the created coupon to get its encryptId
        $response = $this->get($this->apiURL);
        $encryptId = $response['data']['rows'][0]['encryptId'] ?? NULL;

        // Attempt to delete the coupon using the valid encryptId
        $response2 = $this->delete($this->apiURL . '/' . $encryptId);
        // Assert that the response status is 200 (OK)
        $this->assertEquals(200, $response2->status());
    }

    // Test keyValue - Get all coupon as keyValue
    public function test_api_keyValue()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);
        $response = $this->get($this->apiURL . '/fetch/keyValue');

        $this->assertEquals(200, $response->status());
    }

    // Test Export - export all coupon as xlsx
    public function test_api_export_xlsx()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);
        $response = $this->post($this->apiURL . '/export');

        $this->assertEquals(200, $response->status());
    }
}