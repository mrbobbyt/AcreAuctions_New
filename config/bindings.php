<?php
# /app/config/bindings.php

return [

    'bindings' => [

        'repositories' => [
            'App\Repositories\User\Contracts\UserRepositoryContract' =>
                'App\Repositories\User\UserRepository',
            'App\Repositories\Seller\Contracts\SellerRepositoryContract' =>
                'App\Repositories\Seller\SellerRepository',
            'App\Repositories\Listing\Contracts\ListingRepositoryContract' =>
                'App\Repositories\Listing\ListingRepository',
            'App\Repositories\SearchListing\Contracts\SearchListingRepositoryContract' =>
                'App\Repositories\SearchListing\SearchListingRepository',
            'App\Repositories\Favorite\Contracts\FavoriteRepositoryContract' =>
                'App\Repositories\Favorite\FavoriteRepository',
            'App\Repositories\Social\Contracts\ShareRepositoryContract' =>
                'App\Repositories\Social\ShareRepository',
        ],

        'services' => [
            'App\Services\Auth\Contracts\UserAuthServiceContract' =>
                'App\Services\Auth\UserAuthService',
            'App\Services\User\Contracts\UserServiceContract' =>
                'App\Services\User\UserService',
            'App\Services\Social\Contracts\FacebookServiceContract' =>
                'App\Services\Social\FacebookService',
            'App\Services\Social\Contracts\GoogleServiceContract' =>
                'App\Services\Social\GoogleService',
            'App\Services\Seller\Contracts\SellerServiceContract' =>
                'App\Services\Seller\SellerService',
            'App\Services\Listing\Contracts\ListingServiceContract' =>
                'App\Services\Listing\ListingService',
            'App\Services\Social\Contracts\ShareServiceContract' =>
                'App\Services\Social\ShareService',
            'App\Services\Favorite\Contracts\FavoriteServiceContract' =>
                'App\Services\Favorite\FavoriteService',
            'App\Services\Image\Contracts\ImageServiceContract' =>
                'App\Services\Image\ImageService',
        ],

    ],

];