<?php

namespace Modules\Coupons\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'Modules\Coupons\Repositories\Contracts\CouponsRepositoryInterface',
            'Modules\Coupons\Repositories\CouponsRepository'
        );

        // append

    }
}
