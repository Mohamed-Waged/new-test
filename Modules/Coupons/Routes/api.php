<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Coupons\Http\Controllers\CouponsController;
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

Route::middleware('auth:api')->get('/coupons', function (Request $request) {
    return $request->user();
});

/** Backend **/
Route::group([
        'middleware' => ['auth:api', 'SetAppLocale', 'checkAppID', 'clearCacheOnChange'],
        'prefix'     => 'v1/backend'],
    function(){
        Route::apiResource('/coupons', \CouponsController::class);
        Route::post('coupons/export', [CouponsController::class, 'export']);
        Route::get('coupons/fetch/keyValue', [CouponsController::class, 'keyValue']);
});

/** Frontend **/
Route::group([
        'middleware' => ['SetAppLocale', 'checkAppID', 'clearCacheOnChange'],
        'prefix'     => 'v1/coupons'],
    function(){
        Route::get('/', [CouponsController::class, 'list']);
        Route::get('{coupon}', [CouponsController::class, 'show']);
});
