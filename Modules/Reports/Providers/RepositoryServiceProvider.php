<?php

namespace Modules\Reports\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'Modules\Reports\Repositories\Contracts\ReportsRepositoryInterface',
            'Modules\Reports\Repositories\ReportsRepository'
        );

        // append

    }
}
