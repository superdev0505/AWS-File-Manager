<?php

Route::get('/list', 'ApiController@list');
Route::get('/info', 'ApiController@info');
Route::get('/download', 'ApiController@download');
Route::get('/edit','ApiController@edit');
Route::get('/make-public','ApiController@makePublic');
Route::get('/make-private','ApiController@makePrivate');
Route::get('/lock-delete','ApiController@lockDelete');
Route::get('/lock-edit','ApiController@lockEdit');


Route::post('/make-directory', 'ApiController@makeDirectory');
Route::post('/remove', 'ApiController@remove');
Route::post('/rename', 'ApiController@rename');
Route::post('/paste', 'ApiController@paste');
Route::post('/upload', 'ApiController@upload');
Route::post('/save','ApiController@save');
Route::post('/new','ApiController@new');