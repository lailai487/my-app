# Laravel 會員系統 API 測試指南

## 使用 Postman 測試 API

### 1. 註冊
- **方法**: POST
- **URL**: `http://localhost:8000/api/auth/register`
- **標頭**: 
  - Content-Type: application/json
- **請求體**:
```json
{
    "name": "測試用戶",
    "email": "test@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

### 2. 登入
- **方法**: POST
- **URL**: `http://localhost:8000/api/auth/login`
- **標頭**: 
  - Content-Type: application/json
- **請求體**:
```json
{
    "email": "test@example.com",
    "password": "password",
    "remember_me": false
}
```
- 記得保存回應中的 `access_token`，用於後續的已授權請求

### 