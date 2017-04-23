<?php

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

Route::get('/', function () {
    return ['error' => 'See documentation about API usage'];
});

// All requests in this group must be authenticated
// Path: /api/v1
Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function () {

    Route::get('/user', function () {
        return Auth::guard('api')->user();
    });

    // File info
    Route::get('/file/{code}', 'ApiFileController@getFile');
    // File thumbnail (image/png)
    Route::get('/file/{code}/thumb', 'ApiFileController@getFileThumbnail');
    // Trash file
    Route::delete('/file/{code}/trash', 'ApiFileController@trash');

    // File upload
    Route::post('/file', 'ApiFileController@upload');

});
