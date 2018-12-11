<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\Auth\UserService;
use App\Services\Auth\Contracts\UserServiceContract;

use App\Repositories\Auth\Contracts\UserRepoContract;
use App\Repositories\Auth\UserRepository;

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
            UserRepoContract::class,
            UserRepository::class
        );
    }
}
