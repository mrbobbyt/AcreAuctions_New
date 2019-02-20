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

/***** Authentication *****/

Route::post('register', 'API\v1\AuthController@register');

Route::post('login', 'API\v1\AuthController@login');

Route::get('logout', 'API\v1\AuthController@logout')
    ->middleware('jwt.verify');

Route::post('reset', 'API\v1\AuthController@resetPassword')
    ->middleware('jwt.verify');

Route::post('forgot', 'API\v1\AuthController@forgotPassword');

Route::get('', 'API\v1\AuthController@index');

Route::get('auth-callback', 'API\v1\AuthController@handleSocials');


/********** User **********/

Route::get('user/profile', 'API\v1\UserController@profile')
    ->middleware('jwt.verify');

Route::get('user/{id}', 'API\v1\UserController@view');

Route::post('user/{id}/update', 'API\v1\UserController@update')
    ->middleware('jwt.verify', 'user.permission');

Route::delete('user/{id}/delete', 'API\v1\UserController@delete')
    ->middleware('jwt.verify', 'user.permission');


/********* Seller *********/

Route::get('seller/{slug}', 'API\v1\SellerController@view')
    ->middleware('seller.verification');

Route::post('seller/create', 'API\v1\SellerController@create')
    ->middleware('jwt.verify', 'user.seller');

Route::post('seller/{id}/update', 'API\v1\SellerController@update')
    ->middleware('jwt.verify', 'seller.permission');

Route::delete('seller/{id}/delete', 'API\v1\SellerController@delete')
    ->middleware('jwt.verify', 'seller.permission');


/********* Admin *********/

Route::post('admin/verify-seller', 'API\v1\AdminController@verifySeller')
    ->middleware('jwt.verify', 'admin');

Route::get('admin/all-users', 'API\v1\AdminController@getAllUsers')
    ->middleware('jwt.verify', 'admin');

Route::get('admin/user-search', 'API\v1\AdminController@userSearch')
    ->middleware('jwt.verify', 'admin');

Route::post('admin/user-export', 'API\v1\AdminController@userExport')
    ->middleware('jwt.verify', 'admin');

Route::get('admin/all-listings', 'API\v1\AdminController@getAllListings')
    ->middleware('jwt.verify', 'admin');

Route::get('admin/listing-search', 'API\v1\AdminController@listingSearch')
    ->middleware('jwt.verify', 'admin');

Route::post('admin/listing-export', 'API\v1\AdminController@listingExport')
    ->middleware('jwt.verify', 'admin');


/********* Search *********/

Route::get('search', 'API\v1\SearchController@search');

Route::get('land-for-sale/filters', 'API\v1\SearchController@filters');


/******** Listing ********/

Route::get('land-for-sale/properties', 'API\v1\ListingController@createWithProperties');

Route::get('land-for-sale/{slug}', 'API\v1\ListingController@view')
    ->middleware('listing.verification');

Route::post('land-for-sale/create', 'API\v1\ListingController@create')
    ->middleware('jwt.verify');

Route::post('land-for-sale/{id}/update', 'API\v1\ListingController@update')
    ->middleware('jwt.verify', 'listing.permission');

Route::delete('land-for-sale/{id}/delete', 'API\v1\ListingController@delete')
    ->middleware('jwt.verify', 'listing.permission');


/******** Favorite ********/

Route::get('user/{id}/favorite', 'API\v1\FavoriteController@view')
    ->middleware('jwt.verify', 'user.permission');

Route::post('/user/favorite/action', 'API\v1\FavoriteController@action')
    ->middleware('jwt.verify');


/********* Share *********/

Route::get('share/list', 'API\v1\ShareController@getNetworks');

Route::post('share/create', 'API\v1\ShareController@create');


/********** Main **********/

Route::get('home/search', 'API\v1\HomeController@search');

Route::get('home/featured', 'API\v1\HomeController@featured');
