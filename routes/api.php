<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/login','WorkersController@login');
Route::get('/workers/isLocationSet','WorkersProfileController@isLocationSet');
Route::get('/workers/getLocation','WorkersProfileController@returnLocation');
Route::post('/workers/addLocation','WorkersProfileController@addLocation');
Route::get('/jobs/{city}/{district}/{state}','JobsController@showJobs');
Route::post('/jobs/','JobsController@addJob');
Route::delete('/jobs/{id}','JobsController@deletejob');
Route::get('/jobs/uploaded-jobs','JobsController@uploadedJobs');
Route::get('/jobs/active-jobs','JobsController@appliedJobs');
Route::post('/jobs/apply/{id}/{date}','JobsController@applyOnJob');
Route::get('/jobs/isWorking/{date}','JobsController@isAlreadyWorking');
Route::delete('/jobs/removeExpiredJobs','JobsController@removeJobs');
Route::delete('/jobs/cancelAcceptedJob/{jobId}','JobsController@onCancelJob');
Route::post('/workers/updateLocation','WorkersProfileController@updateLocation');
Route::post('/workers/updateContact','WorkersProfileController@updateContact');