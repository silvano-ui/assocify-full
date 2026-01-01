<?php

namespace App\Core\Users;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Core\Tenant\Tenant;
use App\Core\Permissions\TenantRole;
use App\Core\Permissions\UserTenantRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'settings' => 'array',
            'last_login_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(UserSocialAccount::class);
    }

    public function userRoles(): HasMany
    {
        return $this->hasMany(UserTenantRole::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(TenantRole::class, 'user_tenant_roles', 'user_id', 'tenant_role_id')
            ->withPivot('assigned_by', 'assigned_at', 'expires_at', 'is_primary')
            ->withTimestamps();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->role === 'super_admin' && $this->email === 'admin@assocify.it';
        }
        
        if ($panel->getId() === 'dashboard') {
             // Allow tenant users and super admin (for debugging?)
             // For now allow active users with tenant_id
             return $this->tenant_id !== null && $this->status === 'active';
        }

        return false;
    }
}
