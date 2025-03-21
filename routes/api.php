<?php
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Data\MigrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 需要認證的路由 - 使用 Sanctum 保護
Route::middleware(['auth:sanctum'])->group(function () {
    // 獲取用戶資訊
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // 獲取遷移記錄
    //Route::get('/data/migrations', MigrationController::class);
});

// 特殊處理 - 手動檢查令牌，而不使用中間件
Route::get('/data/migrations', MigrationController::class);

// 公開路由 - 不需要認證
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

