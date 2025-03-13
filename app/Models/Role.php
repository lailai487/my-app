<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the users for the role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'roles_users')
            ->withTimestamps();
    }

    /**
     * Get the permissions for the role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permissions_roles')
            ->withTimestamps();
    }

    /**
     * Check if the role has a specific permission.
     *
     * @param string|array $permissions
     * @return bool
     */
    public function hasPermission($permissions)
    {
        if (is_string($permissions)) {
            return $this->permissions->contains('name', $permissions);
        }

        return (bool) $this->permissions->whereIn('name', $permissions)->count();
    }
}
