<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LocaleController;
use App\Http\Controllers\Auth\VerifyController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\FcmTokenController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\DeactivateController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\RequestNewCodeController;
use App\Http\Controllers\Auth\UpdatePasswordController;
use App\Http\Controllers\Auth\ResetNewPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which sL=S)WCn|+tG
| is assigned the "api" middleware group. Enjoy building your API! VdHJLWpEat8MzuG4eLSX
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['jwt.verify']], function () {
    //
});

/****** php info ******/
Route::get('phpinfo', function () {
    return phpinfo();
});

/****** Clear Cache ******/
Route::get('v1/clearCache', function () {
    return (ResponseCache::clear());
});

/** Backend Auth **/
Route::group(
    [
        'middleware'    => ['api', 'SetAppLocale'],
        'prefix'        => 'v1/backend/auth'
    ],
    function () {
        Route::post('login', [AdminLoginController::class, 'login']);
        Route::post('logout', [AdminLoginController::class, 'logout']);
        Route::post('refresh', [AdminLoginController::class, 'refresh']);
        Route::get('profile', [ProfileController::class, 'profile']);
    }
);

/** Mobile Auth **/
Route::group(
    [
        'middleware'    => ['api', 'SetAppLocale'],
        'prefix'        => 'v1/auth'
    ],
    function () {
        Route::post('login', [LoginController::class, 'login']);
        Route::post('refresh', [LoginController::class, 'refresh']);
        Route::post('logout', [LoginController::class, 'logout']);
        Route::post('register', [RegisterController::class, 'register']);
        Route::post('verify', [VerifyController::class, 'verify']);
        Route::post('request-new-code', [RequestNewCodeController::class, 'requestNewCode']);
        Route::post('forget-password', [ForgetPasswordController::class, 'forgetPassword']);
        Route::post('reset-new-password', [ResetNewPasswordController::class, 'resetNewPassword']);
        Route::get('profile', [ProfileController::class, 'profile']);
        Route::post('update-profile', [ProfileController::class, 'updateProfile']);
        Route::post('update-password', [UpdatePasswordController::class, 'updatePassword']);
        Route::post('fcm-token', [FcmTokenController::class, 'updateUserFcmToken']);
        Route::post('locale', [LocaleController::class, 'updateUserLocale']);
        Route::post('deactivate', [DeactivateController::class, 'deactivate']);
    }
);

/** Home **/
Route::group(
    [
        'middleware'    => ['api', 'SetAppLocale'],
        'prefix'        => 'v1'
    ],
    function () {
        Route::get('account/verify/{code}', [VerifyController::class, 'verifyUserByEmail']);
    }
);

/** Test APIS **/
Route::group(
    [
        'middleware'    => ['api', 'SetAppLocale'],
        'prefix'        => 'v1/test'
    ],
    function () {
        Route::post('pushNotification/{fcmToken}', [TestController::class, 'testPushNotification']);
        Route::post('sendEmail/{email}', [TestController::class, 'testSendEmail']);
        Route::post('previewEmail/{email}', [TestController::class, 'testPreviewEmail']);
    }
);
