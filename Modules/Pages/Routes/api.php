<?php

use Illuminate\Http\Request;
use Modules\Pages\Http\Controllers\PagesController;
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

Route::middleware('auth:api')->get('/pages', function (Request $request) {
    return $request->user();
});

/** Backend **/
Route::group([
        'middleware' => ['auth:api', 'SetAppLocale', 'checkAppID', 'clearCacheOnChange'],
        'prefix'     => 'v1/backend'],
    function(){
        Route::apiResource('/pages', \PagesController::class);
        Route::post('pages/export', [PagesController::class, 'export']);
        Route::get('pages/fetch/keyValue', [PagesController::class, 'keyValue']);
});

/** Frontend **/
Route::group([
        'middleware' => ['SetAppLocale', 'checkAppID', 'clearCacheOnChange'],
        'prefix'     => 'v1/pages'],
    function(){
        Route::get('{page}', [PagesController::class, 'show']);
});