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

Route::put('user/{id}/update', 'API\v1\UserController@update')
    ->middleware('jwt.verify', 'user.permission');

Route::delete('user/{id}/delete', 'API\v1\UserController@delete')
    ->middleware('jwt.verify', 'user.permission');


/********* Seller *********/

Route::get('seller/{slug}', 'API\v1\SellerController@view')
    ->middleware('seller.verification');

Route::post('seller/create', 'API\v1\SellerController@create')
    ->middleware('jwt.verify', 'user.seller');

Route::put('seller/{id}/update', 'API\v1\SellerController@update')
    ->middleware('jwt.verify', 'seller.permission');

Route::delete('seller/{id}/delete', 'API\v1\SellerController@delete')
    ->middleware('jwt.verify', 'seller.permission');


/********* Admin *********/

Route::put('admin/verify-seller', 'API\v1\AdminController@verifySeller')
    ->middleware('jwt.verify', 'admin');


/******** Listing ********/

Route::get('land-for-sale/{slug}', 'API\v1\ListingController@view')
    ->middleware('listing.verification');

Route::post('land-for-sale/create', 'API\v1\ListingController@create')
    ->middleware('jwt.verify');

Route::get('land-foe-sale/create', 'API\v1\ListingController@createWithProperties')
    ->middleware('jwt.verify');

Route::put('land-for-sale/{id}/update', 'API\v1\ListingController@update')
    ->middleware('jwt.verify', 'listing.permission');

Route::delete('land-for-sale/{id}/delete', 'API\v1\ListingController@delete')
    ->middleware('jwt.verify', 'listing.permission');


/********* Search *********/

Route::get('search', 'API\v1\SearchController@search');
