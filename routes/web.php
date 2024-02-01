<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', 'IndexController@index')->name('index');
Route::post('/loginPost', 'AuthController@login')->name('loginPost');
Route::post('/registerPost', 'AuthController@register')->name('registerPost');
Route::post('/logout', 'AuthController@logout')->name('logoutPost');

// Profil

Route::get('/profil', 'ProfileController@show')->name('profil.pages');
Route::post('/userPost', 'ProfileController@userUpdate')->name('userPost');

// News
Route::get('/news', 'NewsController@NewsShow')->name('news');
Route::post('/create-news', 'NewsController@CreateNews')->name('create_news');
Route::post('/see-news', "NewsController@seeMessage")->name('see-news');
Route::post('/see-history', "NewsController@historyNews")->name('see-history');
Route::post('/last-news', "NewsController@lastNew")->name('last-news');

