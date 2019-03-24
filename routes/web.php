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

//Route::get('/', function () {
    //return view('welcome');
//});
//
Route::get('/','HomeController@index');
//Route::get('/dpmptsp','HomeController@first');
Route::get('/dpmptsp/{dateStart?}/{dateEnd?}','HomeController@first');
Route::get('/pdl','HomeController@IRpdl');
Route::get('/pbb','HomeController@IRpbb');
Route::get('/bphtb/{pilihan?}','HomeController@IRbphtb');
