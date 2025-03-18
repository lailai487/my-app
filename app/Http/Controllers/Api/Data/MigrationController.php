<?php

namespace App\Http\Controllers\Api\Data;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class MigrationController extends Controller
{
    /**
     * Handle the incoming request.
     * 列出所有資料庫遷移記錄
     */
    public function __invoke(Request $request)
    {
        // 收集令牌除錯信息
        $bearerToken = $request->bearerToken();
        $debug = [
            'received_bearer_token' => $bearerToken ? substr($bearerToken, 0, 10) . '...' : null,
            'auth_header' => $request->header('Authorization') ? 'present' : 'missing'
        ];

        if (!$bearerToken) {
            return response()->json([
                'success' => false,
                'message' => '未提供令牌'
            ], 401);
        }
        
        $parts = explode('|', $bearerToken);
        if (count($parts) !== 2) {
            return response()->json([
                'success' => false,
                'message' => '令牌格式錯誤'
            ], 401);
        }
        
        $tokenId = $parts[0];
        $tokenText = $parts[1];
        
        // 查找令牌
        $token = DB::table('personal_access_tokens')
            ->where('id', $tokenId)
            ->first();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => '令牌不存在'
            ], 401);
        }
        
        // 驗證令牌哈希
        if (!hash_equals($token->token, hash('sha256', $tokenText))) {
            return response()->json([
                'success' => false,
                'message' => '令牌無效'
            ], 401);
        }
        
        // 檢查令牌是否過期
        if ($token->expires_at && now()->gt($token->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => '令牌已過期'
            ], 401);
        }
        
        // 嘗試進行認證
        //$user = Auth::guard('sanctum')->user();

        // 獲取用戶
        $user = DB::table('users')->find($token->tokenable_id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '用戶不存在'
            ], 401);
        }
        
        if (!$user) {
            // 獲取更多除錯信息
            if ($bearerToken) {
                $parts = explode('|', $bearerToken);
                if (count($parts) === 2) {
                    $tokenId = $parts[0];
                    $token = PersonalAccessToken::find($tokenId);
                    
                    $debug['token_found_in_db'] = $token ? true : false;
                    
                    if ($token) {
                        $debug['token_details'] = [
                            'belongs_to_user_id' => $token->tokenable_id,
                            'created_at' => $token->created_at->format('Y-m-d H:i:s'),
                            'is_expired' => $token->expires_at && $token->expires_at->isPast()
                        ];
                    }
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => '未通過認證，請提供有效的 Bearer Token',
                'debug_info' => $debug
            ], 401);
        }

        // 手動檢查認證，而不使用中間件
        // if (!Auth::guard('sanctum')->check()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => '未通過認證，請提供有效的 Bearer Token',
        //     ], 401);
        // }

        try {
            // 從migrations表獲取所有記錄
            $migrations = DB::table('migrations')->get();
            
            return response()->json([
                'success' => true,
                'message' => '成功獲取遷移記錄',
                'data' => [
                    'migrations' => $migrations
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '獲取遷移記錄失敗',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
