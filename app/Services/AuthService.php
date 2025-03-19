<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Config;

class AuthService
{
    /**
     * 為用戶創建令牌
     *
     * @param User $user
     * @param string $name
     * @return \Laravel\Sanctum\NewAccessToken
     */
    public function createToken(User $user, string $name = 'api-token')
    {
        // 取得令牌過期時間配置
        $expiresAt = Config::get('sanctum.expiration') 
            ? now()->addMinutes(Config::get('sanctum.expiration')) 
            : null;
        
        // 創建令牌
        return $user->createToken($name, expiresAt: $expiresAt);
    }
}