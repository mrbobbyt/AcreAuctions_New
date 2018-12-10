<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\User\UserService;
use App\Services\User\Contracts\UserServiceContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            UserServiceContract::class,
            UserService::class
        );

        $this->app->bind(
            'App\Repositories\User\Contracts\UserRepoContract',
            'App\Repositories\User\UserRepository'
        );
    }
}
