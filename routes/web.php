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

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/send','SendDataController@send')->name('send');
Route::post('/post_send','SendDataController@postSend')->name('post_send');

Route::get('/api','SendDataController@api')->name('api');
