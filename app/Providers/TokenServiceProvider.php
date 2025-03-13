<?php

namespace App\Providers;

use App\Services\TokenGenerator;
use Illuminate\Support\ServiceProvider;
// use Laravel\Sanctum\NewAccessToken;
// use App\Models\User;

class TokenServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // 註冊 token 生成服務
        $this->app->singleton('token.generator', function ($app) {
            return new TokenGenerator();
        });
        
        // 擴展 User 模型
        // 會導致error: Call to a member function connection() on null
        // 另外不需要在app.php中加入App\Providers\TokenServiceProvider::class,
        // User::macro('createApiToken', function (string $name = 'api-token') {
        //     $expiresAt = config('sanctum.expiration') 
        //         ? now()->addMinutes(config('sanctum.expiration')) 
        //         : null;
                
        //     return $this->createToken($name, expiresAt: $expiresAt);
        // });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
