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

Route::group(['prefix'=>'User'], function() {
    Route::post('gpsUpdate','UserController@UploadGPS');
    Route::post('search','UserController@SearchUser');
    Route::post('Login', 'UserController@userLogin');
    Route::post('register','UserController@userRegister');
    Route::post('delete','UserController@userDelete');
    Route::post('changePassword','UserController@userChangePassword');
});

Route::group(['prefix'=>'Appointment'], function() {
    Route::post('newAppointment','AppointmentController@UploadAppointment');
    Route::post('checkInvite','AppointmentController@CheckInvite');
    Route::post('acceptInvite','AppointmentController@acceptInvite');
    Route::post('getAppointment','AppointmentController@getAppointment');
    Route::post('getAppointment_detail','AppointmentController@getAppointment_detail');

});


