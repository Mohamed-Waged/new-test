<?php

namespace Modules\Messages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EloquentRepositoryTest extends TestCase
{
    //use RefreshDatabase;
    protected $apiURL = '/api/v1/backend/messages';

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
}
