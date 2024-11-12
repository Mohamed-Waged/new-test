<?php

namespace Modules\Users\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'Modules\Users\Repositories\Contracts\UsersRepositoryInterface',
            'Modules\Users\Repositories\UsersRepository'
        );

        // append

    }
}