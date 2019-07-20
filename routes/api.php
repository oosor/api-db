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

Route::middleware('auth:api')->namespace('Api\Auth')->group(function () {
    Route::apiResource('/auth-personal-tokens', 'PersonalTokenController')->only(['store'/*in future version will adding other methods*/]);
});
