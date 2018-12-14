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
    ->name('logout')
    ->middleware('jwt.verify');

Route::post('reset', 'API\v1\AuthController@resetPassword')
    ->name('reset')
    ->middleware('jwt.verify');

Route::post('forgot', 'API\v1\AuthController@forgotPassword')
    ->name('forgot');



/********** User **********/

Route::get('profile', 'API\v1\UserController@profile')
    ->name('profile')
    ->middleware('jwt.verify');

Route::get('user/view/{id}', 'API\v1\UserController@view');

Route::post('user/update', 'API\v1\UserController@update')
    ->middleware('jwt.verify');



/*Route::get('reset/{token}', function($token) {
    $model = \App\Models\PasswordResets::where('token', '=', $token)->first();
//    return new \App\Http\Resources\UserResource($model->user);
    return response()->json(['role' => $model->user->getRole]);
});*/
