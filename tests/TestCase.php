<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();
        $this->getAdminBearerToken = $this->getAdminBearerToken();

        $this->initTestingDatabase();
    }

    protected function initTestingDatabase()
    {
        // Artisan::call('migrate:fresh --seed');
    }

    protected function getAppID()
    {
        return env('APP_ID');
    }

    protected function getAdminBearerToken()
    {
       $data = [
            'email'    => 'root@' . env('APP_NAME') . '.com',
            'password' => 'r00$'
        ];
        $response = $this->post('/api/v1/backend/auth/login', $data);
        return $response['data']['accessToken'] ?? NULL;
    }

    protected function getUserBearerToken()
    {
       $data = [
            'email'    => 'user1@site.com',
            'password' => '123456$'
        ];
        $response = $this->post('/api/v1/auth/login', $data);
        return $response['data']['accessToken'] ?? NULL;
    }
}
