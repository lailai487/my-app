<?php
namespace App\Services;

use App\Models\User;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Str;

class TokenDemoGenerator
{
    /**
     * 為用戶創建令牌，這裡的用法會在DB中創建tokenable_type，以利辨識，達到trait的效果
     *
     * @param User $user
     * @param string $name
     * @return \Laravel\Sanctum\NewAccessToken
     */
    public function createToken(User $user, string $name = 'api-token')
    {
        $expiresAt = config('sanctum.expiration') 
            ? now()->addMinutes(config('sanctum.expiration')) 
            : null;
        
        // return $user->createToken($name, expiresAt: $expiresAt); // 這段要改Model-User,改以其他方法處理
        
        // 生成隨機令牌
        $plainTextToken = Str::random(40);
        
        // 創建 PersonalAccessToken 記錄
        $token = new PersonalAccessToken();
        $token->tokenable_id = $user->id;
        $token->tokenable_type = get_class($user);
        $token->name = $name;
        $token->token = hash('sha256', $plainTextToken);
        $token->abilities = ['*']; // 預設全部權限，您可以根據需要修改
        $token->expires_at = $expiresAt;
        $token->save();
        
        // 使用 NewAccessToken 包裝並返回結果
        return new NewAccessToken($token, $token->getKey().'|'.$plainTextToken);
    }
}