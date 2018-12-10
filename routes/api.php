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

/*Route::get('/', function (Request $request) {
    return 132;
});*/

Route::post('register', 'AuthController@register');

Route::post('login', 'AuthController@login');

Route::get('profile', 'UserController@profile')
    ->name('profile')
    ->middleware('jwt.verify');

Route::get('logout', 'AuthController@logout')
    ->name('logout')
    ->middleware('jwt.verify');
