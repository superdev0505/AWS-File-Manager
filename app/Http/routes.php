<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/
// route to show the login form
Route::get('login', array(
  'uses' => 'MainController@showLogin'
));
// route to process the form
Route::post('login', array(
  'uses' => 'MainController@doLogin'
));
Route::get('logout', array(
  'uses' => 'MainController@doLogout'
));
Route::get('/',
function ()
  {
  return view('welcome');
  });