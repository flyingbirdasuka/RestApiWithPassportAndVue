<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

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



Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');

Route::post('logout', 'Auth\LoginController@logout')->name('logout');
Route::post('register', 'Auth\LoginController@showRegisterForm')->name('register')->middleware('guest');
Route::post('register', 'Auth\LoginController@store')->middleware('guest');



Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::get('/', function () {
	return view('welcome');
})->middleware('guest');


Auth::routes();

Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home');
Route::get('/home/my-tokens', 'App\Http\Controllers\HomeController@getTokens')->name('personal-tokens');
Route::get('/home/authorized-clients', 'App\Http\Controllers\HomeController@getAuthorizedClients')->name('authorized-clients');
Route::get('/home/my-clients', 'App\Http\Controllers\HomeController@getClients')->name('personal-clients');






