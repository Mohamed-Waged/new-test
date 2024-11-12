<?php

namespace Modules\Lecturers\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'Modules\Lecturers\Repositories\Contracts\LecturersRepositoryInterface',
            'Modules\Lecturers\Repositories\LecturersRepository'
        );

        // append

    }
}
