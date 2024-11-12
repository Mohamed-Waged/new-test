<?php

use Illuminate\Http\Request;
use Modules\Lecturers\Http\Controllers\LecturersController;
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

Route::middleware('auth:api')->get('/lecturers', function (Request $request) {
    return $request->user();
});

/** Backend **/
Route::group([
        'middleware' => ['auth:api', 'SetAppLocale', 'checkAppID', 'clearCacheOnChange'],
        'prefix'     => 'v1/backend'],
    function(){
        Route::apiResource('/lecturers', \LecturersController::class);
        Route::post('lecturers/export', [LecturersController::class, 'export']);
        Route::get('lecturers/fetch/keyValue', [LecturersController::class, 'keyValue']);
});

/** Frontend **/
Route::group([
        'middleware' => ['SetAppLocale', 'checkAppID', 'clearCacheOnChange'],
        'prefix'     => 'v1/lecturers'],
    function(){
        //
});
