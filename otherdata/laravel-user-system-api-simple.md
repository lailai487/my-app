# Laravel 簡化會員系統 API 設計

## 身份驗證 API

### 註冊
- **請求方式**: `POST`
- **路由**: `/api/auth/register`
- **參數**:
  ```json
  {
    "name": "使用者名稱",
    "email": "user@example.com",
    "password": "密碼",
    "password_confirmation": "確認密碼"
  }
  ```
- **回應**:
  ```json
  {
    "success": true,
    "message": "註冊成功",
    "data": {
      "user": {
        "id": 1,
        "name": "使用者名稱",
        "email": "user@example.com",
        "email_verified_at": null,
        "created_at": "2025-03-12T12:00:00.000000Z",
        "updated_at": "2025-03-12T12:00:00.000000Z"
      },
      "access_token": "JWT令牌",
      "token_type": "Bearer"
    }
  }
  ```

### 登入
- **請求方式**: `POST`
- **路由**: `/api/auth/login`
- **參數**:
  ```json
  {
    "email": "user@example.com",
    "password": "密碼",
    "remember_me": false
  }
  ```
- **回應**:
  ```json
  {
    "success": true,
    "message": "登入成功",
    "data": {
      "user": {
        "id": 1,
        "name": "使用者名稱",
        "email": "user@example.com",
        "roles": ["user"],
        "permissions": ["view-profile"]
      },
      "access_token": "JWT令牌",
      "token_type": "Bearer",
      "expires_at": "2025-03-13T12:00:00.000000Z"
    }
  }
  ```

### 登出
- **請求方式**: `POST`
- **路由**: `/api/auth/logout`
- **標頭**: `Authorization: Bearer {token}`
- **回應**:
  ```json
  {
    "success": true,
    "message": "成功登出"
  }
  ```

### 發送重設密碼郵件
- **請求方式**: `POST`
- **路由**: `/api/auth/password/email`
- **參數**:
  ```json
  {
    "email": "user@example.com"
  }
  ```
- **回應**:
  ```json
  {
    "success": true,
    "message": "密碼重設郵件已發送"
  }
  ```

### 重設密碼
- **請求方式**: `POST`
- **路由**: `/api/auth/password/reset`
- **參數**:
  ```json
  {
    "email": "user@example.com",
    "password": "新密碼",
    "password_confirmation": "確認新密碼",
    "token": "重設令牌"
  }
  ```
- **回應**:
  ```json
  {
    "success": true,
    "message": "密碼已重設"
  }
  ```

## 用戶資料 API

### 獲取個人資料
- **請求方式**: `GET`
- **路由**: `/api/user/profile`
- **標頭**: `Authorization: Bearer {token}`
- **回應**:
  ```json
  {
    "success": true,
    "data": {
      "user": {
        "id": 1,
        "name": "使用者名稱",
        "email": "user@example.com",
        "profile": {
          "phone": "0912345678",
          "address": "台北市大安區",
          "birthday": "1990-01-01",
          "avatar": "uploads/avatars/user1.jpg",
          "gender": "male"
        },
        "roles": ["user"],
        "permissions": ["view-profile", "edit-profile"]
      }
    }
  }
  ```

### 更新個人資料
- **請求方式**: `PUT`
- **路由**: `/api/user/profile`
- **標頭**: `Authorization: Bearer {token}`
- **參數**:
  ```json
  {
    "name": "新使用者名稱",
    "phone": "0912345678",
    "address": "台北市大安區",
    "birthday": "1990-01-01",
    "gender": "male"
  }
  ```
- **回應**:
  ```json
  {
    "success": true,
    "message": "個人資料已更新",
    "data": {
      "user": {
        "id": 1,
        "name": "新使用者名稱",
        "email": "user@example.com",
        "profile": {
          "phone": "0912345678",
          "address": "台北市大安區",
          "birthday": "1990-01-01",
          "avatar": "uploads/avatars/user1.jpg",
          "gender": "male"
        }
      }
    }
  }
  ```

### 上傳頭像
- **請求方式**: `POST`
- **路由**: `/api/user/avatar`
- **標頭**: `Authorization: Bearer {token}`
- **參數**: `form-data` 格式，包含 `avatar` 檔案
- **回應**:
  ```json
  {
    "success": true,
    "message": "頭像已更新",
    "data": {
      "avatar_url": "uploads/avatars/user1_updated.jpg"
    }
  }
  ```

### 更改密碼
- **請求方式**: `PUT`
- **路由**: `/api/user/password`
- **標頭**: `Authorization: Bearer {token}`
- **參數**:
  ```json
  {
    "current_password": "當前密碼",
    "password": "新密碼",
    "password_confirmation": "確認新密碼"
  }
  ```
- **回應**:
  ```json
  {
    "success": true,
    "message": "密碼已更新"
  }
  ```

### 獲取登入活動
- **請求方式**: `GET`
- **路由**: `/api/user/login-activities`
- **標頭**: `Authorization: Bearer {token}`
- **回應**:
  ```json
  {
    "success": true,
    "data": {
      "activities": [
        {
          "id": 1,
          "ip_address": "192.168.1.1",
          "user_agent": "Mozilla/5.0...",
          "login_at": "2025-03-12T10:00:00.000000Z",
          "logout_at": "2025-03-12T11:00:00.000000Z",
          "status": "success"
        },
        {
          "id": 2,
          "ip_address": "192.168.1.1",
          "user_agent": "Mozilla/5.0...",
          "login_at": "2025-03-11T10:00:00.000000Z",
          "logout_at": "2025-03-11T11:00:00.000000Z",
          "status": "success"
        }
      ]
    }
  }
  ```

## 管理員 API

### 獲取所有用戶
- **請求方式**: `GET`
- **路由**: `/api/admin/users`
- **標頭**: `Authorization: Bearer {token}`
- **權限要求**: `manage-users`
- **查詢參數**: `page`, `per_page`, `search`, `sort_by`, `sort_dir`
- **回應**:
  ```json
  {
    "success": true,
    "data": {
      "users": [
        {
          "id": 1,
          "name": "使用者1",
          "email": "user1@example.com",
          "created_at": "2025-03-12T12:00:00.000000Z",
          "roles": ["user"]
        },
        {
          "id": 2,
          "name": "使用者2",
          "email": "user2@example.com",
          "created_at": "2025-03-11T12:00:00.000000Z",
          "roles": ["admin"]
        }
      ],
      "pagination": {
        "total": 100,
        "per_page": 10,
        "current_page": 1,
        "last_page": 10
      }
    }
  }
  ```

### 編輯用戶角色
- **請求方式**: `PUT`
- **路由**: `/api/admin/users/{user_id}/roles`
- **標頭**: `Authorization: Bearer {token}`
- **權限要求**: `manage-roles`
- **參數**:
  ```json
  {
    "roles": [1, 2]
  }
  ```
- **回應**:
  ```json
  {
    "success": true,
    "message": "用戶角色已更新",
    "data": {
      "user": {
        "id": 1,
        "name": "使用者1",
        "email": "user1@example.com",
        "roles": ["user", "editor"]
      }
    }
  }
  ```

### 管理角色和權限
- **請求方式**: `GET`
- **路由**: `/api/admin/roles`
- **標頭**: `Authorization: Bearer {token}`
- **權限要求**: `manage-roles`
- **回應**:
  ```json
  {
    "success": true,
    "data": {
      "roles": [
        {
          "id": 1,
          "name": "admin",
          "description": "管理員角色",
          "permissions": [
            {
              "id": 1,
              "name": "manage-users",
              "description": "管理用戶"
            },
            {
              "id": 2,
              "name": "manage-roles",
              "description": "管理角色"
            }
          ]
        },
        {
          "id": 2,
          "name": "user",
          "description": "一般用戶角色",
          "permissions": [
            {
              "id": 3,
              "name": "view-profile",
              "description": "查看個人資料"
            }
          ]
        }
      ]
    }
  }
  ```
