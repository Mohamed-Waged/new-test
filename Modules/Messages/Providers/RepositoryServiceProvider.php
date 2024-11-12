<?php

namespace Modules\Messages\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'Modules\Messages\Repositories\Contracts\MessagesRepositoryInterface',
            'Modules\Messages\Repositories\MessagesRepository'
        );

        // append

    }
}