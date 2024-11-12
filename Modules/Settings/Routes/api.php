<?php

use Illuminate\Http\Request;
use Modules\Settings\Http\Controllers\SettingsController;

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

Route::middleware('auth:api')->get('/settings', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => ['auth:api', 'SetAppLocale', 'checkAppID', 'clearCacheOnChange'],
    'prefix' => 'v1/backend'],
    function () {
        Route::apiResource('/settings', \SettingsController::class);
        Route::get('settings/{slug}', [SettingsController::class, 'show']);
        Route::get('settings/fetch/keyValue', [SettingsController::class, 'keyValue']);
    });

Route::group([ //, 'cacheResponse:360000'
    'middleware' => ['SetAppLocale', 'checkAppID'],
    'prefix' => 'v1'
],
    function () {
        Route::get('appSettings', [SettingsController::class, 'appSettings']);
        Route::get('settings/{slug}', [SettingsController::class, 'show']);
        Route::get('isoCountryCodes', [SettingsController::class, 'isoCountryCodes']);
    });
