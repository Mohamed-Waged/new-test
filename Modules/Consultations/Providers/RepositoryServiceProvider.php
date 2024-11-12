<?php

namespace Modules\Consultations\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'Modules\Consultations\Repositories\Contracts\ConsultationsRepositoryInterface',
            'Modules\Consultations\Repositories\ConsultationsRepository'
        );

        // append

    }
}
