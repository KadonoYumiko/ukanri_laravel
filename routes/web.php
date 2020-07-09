<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'HomeController@index')->name('home');

Auth::routes(['register' => false, 'confirm' => false]);

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'check', 'middleware' => 'auth'], function () {
    Route::post('/', 'KadexeController@post');
});

Route::get('/result', 'ResultController@index');

// Route::group(['prefix' => 'check', 'middleware' => 'web'], function () {
//     Route::get('/', 'KadexeController@index');
//     Route::post('/', 'KadexeController@post');
// });
