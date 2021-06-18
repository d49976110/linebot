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

Route::get('/', function () {
    return view('welcome');
});


Route::post('/callback', 'LineController@webhook');



Route::get('/liff/create', 'UserController@create');
Route::post('/liff', 'UserController@store')->name('store');
Route::get('/admin', 'UserController@index');
Route::put('/liff/{user}', 'UserController@update');
Route::get('/liff/{user}/send', 'LineController@sendMessage');
Route::get('/liff/{user}/cancel', 'LineController@verifiedFailed');
Route::post('/notify', 'LineController@notify');

Route::get('/file/create', 'FileController@create');
Route::post('/file', 'FileController@store');
