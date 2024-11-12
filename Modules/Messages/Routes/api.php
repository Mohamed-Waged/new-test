<?php

use Illuminate\Http\Request;
use Modules\Messages\Http\Controllers\MessagesController;
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

Route::middleware('auth:api')->get('/messages', function (Request $request) {
    return $request->user();
});

/** Backend **/
Route::group([
        'middleware' => ['auth:api', 'SetAppLocale', 'clearCacheOnChange'],
        'prefix'     => 'v1/backend'],
    function(){
        Route::apiResource('/messages', \MessagesController::class);
        Route::post('messages/export', [MessagesController::class, 'export']);
});



/** Frontend **/
Route::group([
        'middleware' => ['SetAppLocale'],
        'prefix'     => 'v1/messages'],
    function(){
        Route::post('/send', [MessagesController::class, 'store']);
});