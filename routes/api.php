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

Route::middleware('api_auth')->get('/user', 'Api\UserController@index');
Route::post('/auth/login', 'Api\RegisterController@login');
Route::post('/auth/soc', 'Api\RegisterController@socAuth');
Route::post('/auth/registration', 'Api\RegisterController@registration');

//Route::middleware('api')->get('/user', function (Request $request) {
//    return response()->json(['OK'=>200]);
//});
