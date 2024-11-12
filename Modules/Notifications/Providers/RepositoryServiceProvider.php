<?php

namespace Modules\Notifications\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'Modules\Notifications\Repositories\Contracts\NotificationsRepositoryInterface',
            'Modules\Notifications\Repositories\NotificationsRepository'
        );

        // append

    }
}
