<?php

use Illuminate\Http\Request;

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

Route::post('register', 'API\v1\AuthController@register');

Route::post('login', 'API\v1\AuthController@login');

Route::get('profile', 'API\v1\AuthController@profile')
    ->name('profile')
    ->middleware('jwt.verify');

Route::get('logout', 'API\v1\AuthController@logout')
    ->name('logout')
    ->middleware('jwt.verify');

Route::post('reset', 'API\v1\AuthController@resetPassword')
    ->name('reset')
    ->middleware('jwt.verify');
