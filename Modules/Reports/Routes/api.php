<?php

use Illuminate\Http\Request;
use Modules\Reports\Http\Controllers\ReportsController;
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

Route::middleware('auth:api')->get('/reports', function (Request $request) {
    return $request->user();
});

/** Backend **/
Route::group([
        'middleware' => ['auth:api', 'SetAppLocale', 'checkAppID', 'clearCacheOnChange'],
        'prefix'     => 'v1/backend'],
    function(){
        Route::get('reports', [ReportsController::class, 'index']);
        Route::post('reports/export', [ReportsController::class, 'export']);
});

/** Frontend **/
Route::group([
        'middleware' => ['SetAppLocale', 'checkAppID', 'clearCacheOnChange'],
        'prefix'     => 'v1/reports'],
    function(){
        //
});
