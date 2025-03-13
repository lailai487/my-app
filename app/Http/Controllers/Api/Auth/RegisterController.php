<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function __construct()
    {
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // 生成Token, 透過TokenServiceProvider
        //$token = $user->createApiToken();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '驗證失敗',
                'errors' => $validator->errors()
            ], 422);
        }

        // 創建用戶
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 分配默認角色
        // todo: seeder
        // $defaultRole = config('auth.default_role', 'user');
        // $user->assignRole($defaultRole);

        // 創建個人資料
        $user->profile()->create();

        // 生成令牌
        // $token = $this->authService->createToken($user);
        // 使用依賴注入
        $tokenGenerator = app('token.generator');
        $token = $tokenGenerator->createToken($user);

        return response()->json([
            'success' => true,
            'message' => '註冊成功',
            'data' => [
                'user' => $user->only(['id', 'name', 'email', 'created_at', 'updated_at']),
                'access_token' => $token->plainTextToken,
                'token_type' => 'Bearer',
            ]
        ], 201);
    }
}
