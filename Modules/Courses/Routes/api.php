<?php

use Illuminate\Http\Request;
use Modules\Courses\Http\Controllers\CoursesController;
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

Route::middleware('auth:api')->get('/courses', function (Request $request) {
    return $request->user();
});

/** Backend **/
Route::group([
        'middleware' => ['auth:api', 'SetAppLocale', 'checkAppID', 'clearCacheOnChange'],
        'prefix'     => 'v1/backend'],
    function(){
        Route::apiResource('/courses', \CoursesController::class);
        Route::post('courses/export', [CoursesController::class, 'export']);
        Route::get('courses/fetch/keyValue', [CoursesController::class, 'keyValue']);
});

/** Frontend **/
Route::group([
        'middleware' => ['SetAppLocale', 'checkAppID', 'clearCacheOnChange'],
        'prefix'     => 'v1/courses'],
    function(){
        //
});
