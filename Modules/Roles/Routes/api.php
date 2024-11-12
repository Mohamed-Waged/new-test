<?php

use Illuminate\Http\Request;
use Modules\Roles\Http\Controllers\RolesController;
use Modules\Roles\Http\Controllers\PermissionsController;
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

Route::middleware('auth:api')->get('/roles', function (Request $request) {
    return $request->user();
});

/** Backend **/
Route::group([
        'middleware' => ['auth:api', 'SetAppLocale', 'checkAppID', 'clearCacheOnChange'],
        'prefix'     => 'v1/backend'],
    function() {
        Route::apiResource('/roles', \RolesController::class);
        Route::get('roles/fetch/keyValue', [RolesController::class, 'keyValue']);
        Route::get('permissions', [PermissionsController::class, 'index']);
});