<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\Auth\UserAuthService;
use App\Services\Auth\Contracts\UserAuthServiceContract;

use App\Repositories\User\Contracts\UserRepoContract;
use App\Repositories\User\UserRepository;

use App\Services\User\Contracts\UserServiceContract;
use App\Services\User\UserService;

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
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app->bind(
            UserAuthServiceContract::class,
            UserAuthService::class
        );

        $this->app->bind(
            UserRepoContract::class,
            UserRepository::class
        );

        $this->app->bind(
            UserServiceContract::class,
            UserService::class
        );
    }
}
