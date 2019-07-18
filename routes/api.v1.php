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

Route::middleware(['auth:api', 'scope:construct,superuser'])->namespace('Api\V1\Construct')->group(function () {
    Route::apiResource('/construct', 'ConstructController');
});

Route::middleware('client')->namespace('Api\V1\Queries')->group(function () {
    Route::post('/query', 'QueryController@index');
});
