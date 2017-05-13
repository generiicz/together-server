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

Route::post('/auth/login', 'Api\RegisterController@login');
Route::post('/auth/soc', 'Api\RegisterController@socAuth');
Route::post('/auth/registration', 'Api\RegisterController@registration');

Route::group(['middleware' => 'api_auth', 'prefix' => 'user'], function () {
    Route::get('/', 'Api\UserController@index');
    Route::post('/', 'Api\UserController@update');

    Route::get('/post', 'Api\PostController@one');
    Route::post('/post/{id}', 'Api\PostController@update');
    Route::post('/post/create', 'Api\PostController@create');
    Route::get('/post/all', 'Api\PostController@index');
    Route::get('/categories', 'Api\PostController@categories');
});

Route::group(['middleware' => 'api_auth', 'prefix' => 'friend'],function(){
    Route::post('/add','Api\FriendController@addFriendAction');
    Route::post('/remove','Api\FriendController@removeFriendAction');
});

//Route::middleware('api')->get('/user', function (Request $request) {
//    return response()->json(['OK'=>200]);
//});
