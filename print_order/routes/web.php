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

//This line need to be fix, but for now just passing the user id, since only 1 user to test
Route::get('/', function () { return Redirect::to('profiles/1');});

Route::get('/profiles', 'ProfilesController@index')->name('profiles');

Route::get('/order/create', 'OrdersController@create');
Route::post('/order', 'OrdersController@store');

Route::get('/profiles/{user}', 'ProfilesController@index')->name('profiles.show');

Route::get('/printOrder', 'PrintOrdersController@index');
Route::post('/printOrder', 'PrintOrdersController@store');
