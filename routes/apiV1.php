<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
// send user inviation apis 
Route::group(['prefix' => 'user'], function () {
    Route::post('fblogin', 'UserController@login');
    Route::post('send-invitation', 'UserController@sendInvitation');
    Route::post('confirm', 'UserController@confirmInvite');
    Route::post('list-conversation', 'UserController@listConversation');
    Route::post('settings', 'UserController@settings');
    Route::get('logout', 'UserController@logout');
    Route::post('save-profile-pics', 'UserController@profilePics');
Route::post('get-profile-pics', 'UserController@getLastProfilePics');
});

// Mobile number verification APIs.
Route::group(['prefix' => 'mobile-number'], function () {
    Route::post('save', 'MobileNumberController@save')->name('save-mobile-number');
    Route::post('verify', 'MobileNumberController@verify');
    Route::post('resend-verification-code', 'MobileNumberController@resendVerificationCode');
    Route::post('get-contacts', 'MobileNumberController@getContactsLists');
    Route::post('createConversion', 'UserController@createFirebaseConversion');
 
});

// Group APIs.
Route::group(['prefix' => 'group'], function () {
    Route::post('creategroup', 'GroupController@createFirebaseGroup');
    Route::post('editgroup', 'GroupController@editFirebaseGroup');
    Route::post('delete', 'GroupController@deleteFirebaseGroup');
     Route::post('leave', 'GroupController@leaveFirebaseGroup');
});

