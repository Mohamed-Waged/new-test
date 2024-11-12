<?php

namespace Modules\Pages\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'Modules\Pages\Repositories\Contracts\PagesRepositoryInterface',
            'Modules\Pages\Repositories\PagesRepository'
        );

        // append

    }
}