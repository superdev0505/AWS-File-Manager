<?php

Route::get('/', 'UserController@index');
Route::post('/login', 'UserController@login');
// Route::view('/', 'index.start');
Route::group(['middleware' => ['auth']], function () {
    //
	Route::view('/browser','index.browser');
	Route::get('/logout','UserController@logout');
});