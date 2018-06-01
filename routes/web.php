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
Route::get('/', function () {
    return view('welcome');
});
//frontend routes
Route::group(['namespace' => 'Admin','prefix' => 'admin'], function () {
// Admin authentication routes.
Route::get('/', 'AdminLoginController@showLoginForm')->name('admin.login');
Route::get('/login', 'AdminLoginController@showLoginForm')->name('admin.login');
Route::post('/login', 'AdminLoginController@login')->name('admin.login');
Route::post('/logout', 'AdminLoginController@logout')->name('admin.logout');

// Admin dashboard route.
Route::get('/dashboard', 'AdminController@dashboard')->name('admin.dashboard');

// settings
Route::get('/settings', 'SettingsController@index')->name('admin.settings');
Route::post('/settings', 'SettingsController@save')->name('admin.savesettings');

// crud for users
Route::resource('users', 'UserController');
Route::delete('users/delete/{id}', ['uses' => 'UserController@destroy', 'as' => 'users.destroy']);

});