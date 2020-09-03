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

Auth::routes();

Route::get('/', function () { return Redirect::to('profiles');});

Route::get('/profiles', 'ProfilesController@index')->name('profiles');

Route::get('/order/create', 'OrdersController@create');
Route::post('/order', 'OrdersController@store');

Route::get('/profiles/{user}', 'ProfilesController@index')->name('profiles.show');

Route::get('/printOrder', 'PrintOrdersController@index');
Route::post('/printOrder', 'PrintOrdersController@store');
