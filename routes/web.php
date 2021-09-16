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

$commonNamespace = '\App\Http\Controllers';

Route::namespace($commonNamespace)->group(function() {
    Route::get('/', 'HomeController@index')->name('home');

    Route::middleware('allow_service')->group(function() {
        Route::get('/{service_key}', 'ContentController@showByService')->name('contents');
    });
});
