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
Route::post('/last-news-create', "NewsController@lastNewReturn")->name('last-news-return');

//Spot
Route::get('/spot', 'SpotController@SpotShow')->name('spot');
Route::post('/create-spot', 'SpotController@CreateNews')->name('create_spot');
Route::post('/see-spot', "SpotController@seeMessage")->name('see-spot');
Route::post('/see-history-spot', "SpotController@historyNews")->name('see-history-spot');
Route::post('/last-spot', "SpotController@lastNew")->name('last-spot');

// Title
Route::get('/title', 'TitleController@TitleShow')->name('title');
Route::post('/create-title', 'TitleController@CreateNews')->name('create_title');
Route::post('/see-title', "TitleController@seeMessage")->name('see-title');
Route::post('/see-history-title', "TitleController@historyNews")->name('see-history-title');
Route::post('/last-title', "TitleController@lastNew")->name('last-title');


Route::get('/nyt', 'NytController@NytShow')->name('nyt');


