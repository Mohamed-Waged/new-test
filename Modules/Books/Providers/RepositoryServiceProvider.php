<?php

namespace Modules\Books\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'Modules\Books\Repositories\Contracts\BooksRepositoryInterface',
            'Modules\Books\Repositories\BooksRepository'
        );

        // append

    }
}
