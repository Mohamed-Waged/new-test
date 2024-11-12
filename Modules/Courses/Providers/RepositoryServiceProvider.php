<?php

namespace Modules\Courses\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'Modules\Courses\Repositories\Contracts\CoursesRepositoryInterface',
            'Modules\Courses\Repositories\CoursesRepository'
        );

        // append

    }
}
