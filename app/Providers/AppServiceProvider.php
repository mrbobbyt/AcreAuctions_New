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

use App\Repositories\Seller\Contracts\SellerRepositoryContract;
use App\Repositories\Seller\SellerRepository;

use App\Services\Seller\Contracts\SellerServiceContract;
use App\Services\Seller\SellerService;

use App\Repositories\Listing\Contracts\ListingRepositoryContract;
use App\Repositories\Listing\ListingRepository;

use App\Services\Listing\Contracts\ListingServiceContract;
use App\Services\Listing\ListingService;

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

        $this->app->bind(
            SellerRepositoryContract::class,
            SellerRepository::class
        );

        $this->app->bind(
            SellerServiceContract::class,
            SellerService::class
        );

        $this->app->bind(
            ListingRepositoryContract::class,
            ListingRepository::class
        );

        $this->app->bind(
            ListingServiceContract::class,
            ListingService::class
        );
    }
}
