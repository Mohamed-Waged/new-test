<?php

namespace Modules\Roles\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'Modules\Roles\Repositories\Contracts\RolesRepositoryInterface',
            'Modules\Roles\Repositories\RolesRepository'
        );

        $this->app->bind(
            'Modules\Roles\Repositories\Contracts\PermissionsRepositoryInterface',
            'Modules\Roles\Repositories\PermissionsRepository'
        );

        // append

    }
}