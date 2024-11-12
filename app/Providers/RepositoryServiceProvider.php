<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\BaseRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'BaseRepositoryInterface',
            'BaseRepository'
        );
    }
}