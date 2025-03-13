<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginActivity;
use App\Services\Auth\AuthService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * 處理用戶登入
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '驗證失敗',
                'errors' => $validator->errors()
            ], 422);
        }

        // 嘗試登入
        if (!Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ], $request->remember_me ?? false)) {
            
            // 記錄失敗的登入嘗試
            LoginActivity::create([
                'user_id' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'login_at' => now(),
                'status' => 'failed',
                'details' => json_encode(['email' => $request->email])
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '登入失敗，請檢查您的憑證',
            ], 401);
        }

        $user = Auth::user();
        
        // 刪除所有現有令牌（如果沒有 remember_me）
        if (!($request->remember_me ?? false)) {
            $user->tokens()->delete();
        }
        
        // 生成新令牌
        $tokenResult = $this->authService->createToken($user);
        $token = $tokenResult->plainTextToken;
        $expiresAt = $tokenResult->accessToken->expires_at ?? Carbon::now()->addHours(24);
        
        // 記錄成功的登入活動
        LoginActivity::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'login_at' => now(),
            'status' => 'success'
        ]);
        
        // 獲取用戶的角色和權限
        $roles = $user->roles->pluck('name');
        $permissions = $user->getAllPermissions()->pluck('name');
        
        return response()->json([
            'success' => true,
            'message' => '登入成功',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $roles,
                    'permissions' => $permissions
                ],
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => $expiresAt->toDateTimeString()
            ]
        ]);
    }
}