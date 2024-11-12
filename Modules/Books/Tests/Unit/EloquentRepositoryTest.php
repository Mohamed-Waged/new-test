<?php

namespace Modules\Books\Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EloquentRepositoryTest extends TestCase
{
    //use RefreshDatabase;

    protected $apiURL = '/api/v1/backend/books';

    // Test Index - Get all books
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
            'book_type' => ['id' => 1],
            'price' => 100,
            'pages_no' => 50,
            'published_at' => Carbon::now()->toDateString(),
            'en' => ['title' => 'test title', 'body' => 'test body'],
            'ar' => ['title' => 'تست', 'body' => 'تست'],
            'sort' => 0,
            'status' => 'active'
        ];
        $response = $this->post($this->apiURL, $request);
        $this->assertEquals(422, $response->status()); // Expecting a validation error

        // Test with missing 'book_type' field
        $request = [
            'lecturer' => ['id' => 1],
            'price' => 100,
            'pages_no' => 50,
            'published_at' => Carbon::now()->toDateString(),
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

        // Attempt to store a book with an invalid lecturer ID
        $request = [
            'lecturer' => ['id' => 9999], // Assuming 9999 does not exist
            'book_type' => ['id' => 1],
            'price' => 100,
            'pages_no' => 50,
            'published_at' => Carbon::now()->toDateString(),
            'en' => ['title' => 'test title', 'body' => 'test body'],
            'ar' => ['title' => 'تست', 'body' => 'تست'],
            'sort' => 0,
            'status' => 'active'
        ];
        $response = $this->post($this->apiURL, $request);

        $this->assertEquals(422, $response->status()); // Expecting a validation error


        // Attempt to store a book with an invalid book_type ID
        $request = [
            'lecturer' => ['id' => 1],
            'book_type' => ['id' => 9999], // Assuming 9999 does not exist
            'price' => 100,
            'pages_no' => 50,
            'published_at' => Carbon::now()->toDateString(),
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
            'book_type' => ['id' => 1],
            'price' => 100,
            'pages_no' => 50,
            'published_at' => Carbon::now()->toDateString(),
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
            'book_type' => ['id' => DB::table('settings')->whereParentId(DB::table('settings')->whereSlug('books_types')->first()->id)->first()->id],
            'price' => 100,
            'pages_no' => 50,
            'published_at' => Carbon::now()->toDateString(),
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

        // Attempt to retrieve a book with an invalid encryptId
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

        // Attempt to retrieve a book with an valid encryptId
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

        // First, create a book to ensure there's something to update
        $faker = \Faker\Factory::create();
        $request = [
            'lecturer' => ['id' => 1],
            'book_type' => ['id' => 1],
            'price' => 100,
            'pages_no' => 50,
            'published_at' => Carbon::now()->toDateString(),
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
            'price' => 150,
            'pages_no' => 60,
            'published_at' => Carbon::now()->toDateString(),
            'en' => ['title' => 'Updated title', 'body' => 'Updated body'],
            'ar' => ['title' => 'تحديث', 'body' => 'تحديث'],
            'sort' => 1,
            'status' => 'inactive'
        ];
        $response = $this->put($this->apiURL . '/' . $encryptId, $updateRequest);
        $this->assertEquals(422, $response->status()); // Expecting a validation error

        // Test with missing 'book_type' field
        $updateRequest = [
            'lecturer' => ['id' => 1],
            'price' => 150,
            'pages_no' => 60,
            'published_at' => Carbon::now()->toDateString(),
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
            'book_type' => ['id' => 1],
            'price' => 150,
            'pages_no' => 60,
            'published_at' => Carbon::now()->toDateString(),
            'en' => ['title' => 'Updated title', 'body' => 'Updated body'],
            'ar' => ['title' => 'تحديث', 'body' => 'تحديث'],
            'sort' => 1,
            'status' => 'inactive'
        ];

        // Attempt to update a book with an invalid encryptId
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

        // First, create a book to ensure there's something to update
        $faker = \Faker\Factory::create();
        $request = [
            'lecturer' => ['id' => 1],
            'book_type' => ['id' => 1],
            'price' => 100,
            'pages_no' => 50,
            'published_at' => Carbon::now()->toDateString(),
            'en' => ['title' => $faker->sentence(3), 'body' => $faker->paragraph()],
            'ar' => ['title' => $faker->sentence(3), 'body' => $faker->paragraph()],
            'sort' => 0,
            'status' => 'active'
        ];
        $this->post($this->apiURL, $request);

        // Retrieve the created book to get its encryptId
        $response = $this->get($this->apiURL);
        $encryptId = $response['data']['rows'][0]['encryptId'] ?? NULL;

        // Prepare update request with a non-alphabetic value for the title
        $updateRequest = [
            'lecturer' => ['id' => 1],
            'book_type' => ['id' => 1],
            'price' => 150,
            'pages_no' => 60,
            'published_at' => Carbon::now()->toDateString(),
            'en' => ['title' => '!@#$%^&*()', 'body' => 'Updated body'], // Non-alphabetic title
            'ar' => ['title' => '!@#$%^&*()', 'body' => 'تحديث'],
            'sort' => 1,
            'status' => 'inactive'
        ];

        // Attempt to update the book using the valid encryptId
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

        // First, create a book to ensure there's something to update
        $faker = \Faker\Factory::create();
        $request = [
            'lecturer' => ['id' => DB::table('lecturers')->first()->id],
            'book_type' => ['id' => DB::table('settings')->whereParentId(DB::table('settings')->whereSlug('books_types')->first()->id)->first()->id],
            'price' => 100,
            'pages_no' => 50,
            'published_at' => Carbon::now()->toDateString(),
            'en' => ['title' => $faker->sentence(3), 'body' => $faker->paragraph()],
            'ar' => ['title' => $faker->sentence(3), 'body' => $faker->paragraph()],
            'sort' => 0,
            'status' => 'active'
        ];
        $this->post($this->apiURL, $request);

        // Retrieve the created book to get its encryptId
        $response = $this->get($this->apiURL);
        $encryptId = $response['data']['rows'][0]['encryptId'] ?? NULL;

        // Prepare updated data
        $updateRequest = [
            'lecturer' => ['id' => 1],
            'book_type' => ['id' => 1],
            'price' => 150, // Updated price
            'pages_no' => 60, // Updated pages_no
            'published_at' => Carbon::now()->toDateString(),
            'en' => ['title' => $faker->sentence(4), 'body' => $faker->paragraph()],
            'ar' => ['title' => $faker->sentence(4), 'body' => $faker->paragraph()],
            'sort' => 1,
            'status' => 'inactive' // Updated status
        ];

        // Attempt to update the book using the valid encryptId
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

        // Attempt to retrieve a book with an invalid encryptId
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

        // First, create a book to ensure there's something to delete
        $faker = \Faker\Factory::create();
        $request = [
            'lecturer' => ['id' => 1],
            'book_type' => ['id' => 1],
            'price' => 100,
            'pages_no' => 50,
            'published_at' => Carbon::now()->toDateString(),
            'en' => ['title' => $faker->sentence(3), 'body' => $faker->paragraph()],
            'ar' => ['title' => $faker->sentence(3), 'body' => $faker->paragraph()],
            'sort' => 0,
            'status' => 'active'
        ];
        $this->post($this->apiURL, $request);

        // Retrieve the created book to get its encryptId
        $response = $this->get($this->apiURL);
        $encryptId = $response['data']['rows'][0]['encryptId'] ?? NULL;

        // Attempt to delete the book using the valid encryptId
        $response2 = $this->delete($this->apiURL . '/' . $encryptId);
        // Assert that the response status is 200 (OK)
        $this->assertEquals(200, $response2->status());
    }

    // Test keyValue - Get all books as keyValue
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

    // Test Export - export all books as xlsx
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
