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

Auth::routes();

Route::get('/', 'MainController@index')->name('main');

// Only for authenticated user
Route::group(['middleware' => 'auth'], function () {
    // User control
    Route::get('/me', 'UserController@index')->name('user');
    Route::get('/me/generate_token', 'UserController@generateApiKey')->name('user.generate_token');

    // Administrative operations
    // These could be in their own Controller
    // TODO: Change these to be POST
    Route::get('/admin/truncate', 'FileController@deleteAllFiles')->name('admin.truncate');
    Route::get('/admin/update_stats', 'AdminController@updateStats')->name('admin.update.stats');
    Route::get('/admin/integrity', 'AdminController@checkIntegrity')->name('admin.integrity.check');
    Route::get('/admin/integrity/{code}/delete', 'AdminController@deleteFile')->name('admin.integrity.delete');
    Route::get('/admin/integrity/delete_corrupted', 'AdminController@deleteAllCorruptedFiles')->name('admin.integrity.delete.corrupted');

    // File upload
    Route::get('/upload', 'FileController@upload')->name('file.upload.form');
    Route::post('/upload', 'FileController@create')->name('file.upload');

    // Trash
    Route::get('/trash', 'MainController@trash')->name('main.trash');
    // TODO: Change these to be POST
    Route::get('/trash/restore', 'FileController@restoreAllTrash')->name('trash.restore');
    Route::get('/trash/delete', 'FileController@deleteAllTrash')->name('trash.delete');

    // File visibility
    Route::get('/{code}/show', 'FileController@show')->name('file.show');
    Route::get('/{code}/hide', 'FileController@hide')->name('file.hide');

    // File deletion
    // TODO: Change these to be POST
    Route::get('/{code}/trash', 'FileController@trash')->name('file.trash');
    Route::get('/{code}/restore', 'FileController@restore')->name('file.restore');
    Route::get('/{code}/delete', 'FileController@delete')->name('file.delete');
});

// Apply ETags to these responses (letting the browser cache responses)
Route::group(['middleware' => 'etag'], function () {
    // Thumbnails
    Route::get('/{code}/thumb', 'FileController@getThumbnail')->name('file.thumb');

    // Normal file route
    Route::get('/{code}.{ext}', 'FileController@get')->name('file.get');
    // Code routing (RESTful)
    Route::get('/{code}', 'FileController@getByCode')->name('file.get.code');
    // Backwards compatibility
    // e.g.: "/i/abcde.png"  -> "/abcde.png"
    //       "/v/edcba.webm" -> "/edcba.webm"
    Route::get('/{c}/{code}.{ext}', 'FileController@getCompat')->where('c', 'i|v|a|t|f');
});