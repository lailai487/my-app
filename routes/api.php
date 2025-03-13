<?php
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// 身份驗證 API
Route::prefix('auth')->group(function () {
    Route::post('register', RegisterController::class); // __invoke等校於下面
    // Route::post('register', RegisterController::class)->name('api.auth.register');
    Route::post('login', LoginController::class)->name('api.auth.login');
    Route::post('logout', LogoutController::class)->middleware('auth:sanctum')->name('api.auth.logout');
    
    // 密碼重設
    Route::prefix('password')->group(function () {
        Route::post('email', [PasswordResetController::class, 'sendResetLinkEmail'])->name('api.auth.password.email');
        Route::post('reset', [PasswordResetController::class, 'reset'])->name('api.auth.password.reset');
    });
});