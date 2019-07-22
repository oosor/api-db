<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/client-construct', 'HomeController@showConstruct')->name('show-construct');
Route::get('/client-queries', 'HomeController@showQueries')->name('show-queries');
Route::get('/client-auth', 'HomeController@showToken')->name('show-token');

Route::get('/passport-clients', 'PassportClientsController@index')->name('passport-clients');
Route::get('/passport-authorized-clients', 'PassportAuthorizedClientsController@index')->name('passport-authorized-clients');
Route::get('/passport-personal-access-tokens', 'PassportPersonalAccessTokensController@index')->name('passport-personal-access-tokens');
