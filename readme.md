Laravel 12 + Livewire + Vite practice project

1. 建立 model
php artisan make:model Role
php artisan make:model UserProfile
php artisan make:model Permission
php artisan make:model LoginActivity

2. 修改 model後 執行
# 用戶資料表 OK
php artisan make:migration create_user_profiles_table

# 角色表 OK
php artisan make:migration create_roles_table

# 權限表 OK
php artisan make:migration create_permissions_table

# 角色用戶關聯表
php artisan make:migration create_roles_users_table

# 權限角色關聯表
php artisan make:migration create_permissions_roles_table

# 登入活動記錄表
php artisan make:migration create_login_activities_table

3. 修改migrations中，相關格式與內容
4. 先遷移基本表，再遷移有FK的表，按上述指令操作不會出錯。
5. 執行指令 php artisan migrate

6. 建立controller
6.1. 安裝stateless API - php artisan install:api
安裝結束後，說明如何應用
 INFO  API scaffolding installed. Please add the [Laravel\Sanctum\HasApiTokens] trait to your User model.
框架說明： https://livewire.laravel.com/docs/security#snapshot-checksums


