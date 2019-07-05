<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('API\v1')->group(function () {
    Route::get('auth-links', 'AuthController@index');
    Route::post('register', 'AuthController@register');
    Route::get('confirm', 'AuthController@confirmRegister');
    Route::post('login', 'AuthController@login');
    Route::get('auth-callback', 'AuthController@handleSocials');
    Route::get('refresh-token', 'AuthController@refreshToken');
    Route::post('forgot', 'AuthController@forgotPassword');
    Route::post('reset', 'AuthController@resetPassword');

    Route::post('seller/continue-auth', 'SellerController@continueAuth');

    Route::get('search', 'SearchController@search');

    Route::get('reverse-geocoding', 'GeocodingController@reverseGeocoding');

    Route::get('land-for-sale/filters', 'SearchController@getFilters');
    Route::get('land-for-sale/properties', 'ListingController@getAvailableProperties');
    Route::get('land-for-sale/{slug}', 'ListingController@view');

    Route::get('blog', 'PostController@getAllPosts');
    Route::get('blog/{slug}', 'PostController@view');
    Route::get('post/recommend', 'PostController@getRecommendPosts');

    Route::get('share/list', 'ShareController@getNetworks');
    Route::post('share/create', 'ShareController@create');

    Route::get('home/featured', 'HomeController@featured');

    Route::middleware('jwt.verify')->group(function () {
        Route::get('user/profile', 'UserController@profile');
        Route::get('seller/{slug}', 'SellerController@view');

        Route::post('post/create', 'PostController@create');
        Route::post('payment/create', 'PaymentsController@create');
        Route::post('payment/{id}', 'PaymentsController@generateNewPaymentToken');

        Route::post('/user/favorite/action', 'FavoriteController@action');
        Route::get('logout', 'AuthController@logout');

        Route::middleware('role:admin')->group(function () {
            Route::post('seller/create', 'SellerController@create');
            Route::post('admin/verify-seller', 'AdminController@verifySeller');

            Route::get('admin/all-sellers', 'AdminController@getAllSellers');

            Route::get('admin/all-users', 'AdminController@getAllUsers');
            Route::get('admin/user-search', 'AdminController@userSearch');
            Route::post('admin/user-export', 'AdminController@userExport');

            Route::get('admin/all-listings', 'AdminController@getAllListings');
            Route::get('admin/listing-search', 'AdminController@listingSearch');
            Route::post('admin/listing-export', 'AdminController@listingExport');

            Route::post('land-for-sale/create', 'ListingController@create');
            Route::post('land-for-sale/{id}', 'ListingController@update');
            Route::delete('land-for-sale/{id}', 'ListingController@delete');
        });

        Route::middleware('owner')->group(function () {
            Route::get('user/{id}/favorite', 'FavoriteController@view');
        });

        Route::middleware('contentManagerOrAdmin')->group(function () {
            Route::get('admin/all-posts', 'AdminController@getAllPosts');
            Route::post('post/{id}', 'PostController@update');
            Route::delete('post/{id}', 'PostController@delete');
        });

        Route::middleware('ownerOrAdmin')->group(function () {
            Route::post('user/{id}', 'UserController@update');
            Route::delete('user/{id}', 'UserController@delete');

            Route::post('seller/{id}/update', 'SellerController@update');
            Route::delete('seller/{id}/delete', 'SellerController@delete');
        });
    });
});
