<?php

namespace Modules\Settings\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'Modules\Settings\Repositories\Contracts\SettingsRepositoryInterface',
            'Modules\Settings\Repositories\SettingsRepository'
        );

        // append

    }
}