<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\Auth\UserAuthService;
use App\Services\Auth\Contracts\UserAuthServiceContract;

use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Repositories\User\UserRepository;

use App\Services\User\Contracts\UserServiceContract;
use App\Services\User\UserService;

use App\Services\Social\Contracts\FacebookServiceContract;
use App\Services\Social\FacebookService;

use App\Services\Social\Contracts\GoogleServiceContract;
use App\Services\Social\GoogleService;

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
            UserRepositoryContract::class,
            UserRepository::class
        );

        $this->app->bind(
            UserServiceContract::class,
            UserService::class
        );

        $this->app->bind(
            FacebookServiceContract::class,
            FacebookService::class
        );

        $this->app->bind(
            GoogleServiceContract::class,
            GoogleService::class
        );
    }
}
