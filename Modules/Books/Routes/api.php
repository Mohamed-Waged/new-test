<?php

use Illuminate\Http\Request;
use Modules\Books\Http\Controllers\BooksController;
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

Route::middleware('auth:api')->get('/books', function (Request $request) {
    return $request->user();
});

/** Backend **/
Route::group([
        'middleware' => ['auth:api', 'SetAppLocale', 'checkAppID', 'clearCacheOnChange'],
        'prefix'     => 'v1/backend'],
    function(){
        Route::apiResource('/books', \BooksController::class);
        Route::post('books/export', [BooksController::class, 'export']);
        Route::get('books/fetch/keyValue', [BooksController::class, 'keyValue']);
});

/** Frontend **/
Route::group([
        'middleware' => ['SetAppLocale', 'checkAppID', 'clearCacheOnChange'],
        'prefix'     => 'v1/books'],
    function(){
        Route::get('/', [BooksController::class, 'list']);
        Route::get('{book}', [BooksController::class, 'show']);

        Route::group(['middleware' => 'auth:api'], function(){
            Route::get('my-library', [BooksController::class, 'myBooks']);
            Route::post('coupon-check', [BooksController::class, 'couponCheck']);
            Route::post('order-create', [BooksController::class, 'orderCreate']);
            Route::get('order-summary', [BooksController::class, 'orderSummary']);
            Route::post('order-checkout', [BooksController::class, 'orderCheckout']);
        });
});
