<?php
# /app/config/bindings.php
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Repositories\User\UserRepository;
use App\Repositories\Seller\Contracts\SellerRepositoryContract;
use App\Repositories\Seller\SellerRepository;
use App\Repositories\Listing\Contracts\ListingRepositoryContract;
use App\Repositories\Listing\ListingRepository;
use App\Repositories\SearchListing\Contracts\SearchListingRepositoryContract;
use App\Repositories\SearchListing\SearchListingRepository;
use App\Repositories\Favorite\Contracts\FavoriteRepositoryContract;
use App\Repositories\Favorite\FavoriteRepository;
use App\Repositories\Social\Contracts\ShareRepositoryContract;
use App\Repositories\Social\ShareRepository;
use App\Repositories\Admin\Contracts\AdminRepositoryContract;
use App\Repositories\Admin\AdminRepository;

use App\Services\Auth\Contracts\UserAuthServiceContract;
use App\Services\Auth\UserAuthService;
use App\Services\User\Contracts\UserServiceContract;
use App\Services\User\UserService;
use App\Services\Social\Contracts\FacebookServiceContract;
use App\Services\Social\FacebookService;
use App\Services\Social\Contracts\GoogleServiceContract;
use App\Services\Social\GoogleService;
use App\Services\Seller\Contracts\SellerServiceContract;
use App\Services\Seller\SellerService;
use App\Services\Listing\Contracts\ListingServiceContract;
use App\Services\Listing\ListingService;
use App\Services\Social\Contracts\ShareServiceContract;
use App\Services\Social\ShareService;
use App\Services\Favorite\Contracts\FavoriteServiceContract;
use App\Services\Favorite\FavoriteService;
use App\Services\Image\Contracts\ImageServiceContract;
use App\Services\Image\ImageService;
use App\Services\Admin\Contracts\AdminServiceContract;
use App\Services\Admin\AdminService;
use App\Services\Telephone\Contracts\TelServiceContract;
use App\Services\Telephone\TelService;
use App\Services\Address\Contracts\AddressServiceContract;
use App\Services\Address\AddressService;
use App\Services\Image\Contracts\AvatarServiceContract;
use App\Services\Image\AvatarService;

return [
    'bindings' => [
        'repositories' => [
            UserRepositoryContract::class => UserRepository::class,
            SellerRepositoryContract::class => SellerRepository::class,
            ListingRepositoryContract::class => ListingRepository::class,
            SearchListingRepositoryContract::class => SearchListingRepository::class,
            FavoriteRepositoryContract::class => FavoriteRepository::class,
            ShareRepositoryContract::class => ShareRepository::class,
            AdminRepositoryContract::class => AdminRepository::class,
        ],

        'services' => [
            UserAuthServiceContract::class => UserAuthService::class,
            UserServiceContract::class => UserService::class,
            FacebookServiceContract::class => FacebookService::class,
            GoogleServiceContract::class => GoogleService::class,
            SellerServiceContract::class => SellerService::class,
            ListingServiceContract::class => ListingService::class,
            ShareServiceContract::class => ShareService::class,
            FavoriteServiceContract::class => FavoriteService::class,
            ImageServiceContract::class => ImageService::class,
            AdminServiceContract::class => AdminService::class,
            TelServiceContract::class => TelService::class,
            AddressServiceContract::class => AddressService::class,
            AvatarServiceContract::class => AvatarService::class,
        ],
    ],
];
