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

// slack
Route::get('/slack', "Api\SlackController@index");
Route::post('/slack/events', "Api\SlackController@events");

// google sheet
Route::get('/google_sheet', "Api\GoogleSheetController@index");

Route::get('/sample/hello', "Sample\SampleController@hello");
