<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginActivity;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * 處理用戶登出
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        // 更新最後一次登入活動的登出時間
        LoginActivity::where('user_id', $request->user()->id)
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first()
            ?->update(['logout_at' => now()]);
        
        // 撤銷當前令牌
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'success' => true,
            'message' => '成功登出'
        ]);
    }
}