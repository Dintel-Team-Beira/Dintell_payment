<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'is_super_admin',
        'role',
        'phone',
        'bio',
        'avatar',
        'timezone',
        'language',
        'is_active',
        'preferences',
        'last_login_at',
        'last_activity_at',
        'login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'is_super_admin' => 'boolean',
        'is_active' => 'boolean',
        'preferences' => 'array',
        'password' => 'hashed',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSuperAdmins($query)
    {
        return $query->where('is_super_admin', true);
    }

    public function scopeCompanyUsers($query)
    {
        return $query->whereNotNull('company_id')->where('is_super_admin', false);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeOnline($query, $minutes = 15)
    {
        return $query->where('last_activity_at', '>=', now()->subMinutes($minutes));
    }

    // Accessors & Mutators
    public function getIsOnlineAttribute(): bool
    {
        return $this->last_activity_at && $this->last_activity_at->diffInMinutes(now()) <= 15;
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->is_super_admin || $this->role === 'admin';
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        return $this->getGravatarUrl();
    }

    public function getInitialsAttribute(): string
    {
        $names = explode(' ', $this->name);
        $initials = '';

        foreach ($names as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
        }

        return substr($initials, 0, 2);
    }

    // Methods
    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin;
    }

    public function isCompanyAdmin(): bool
    {
        return $this->role === 'admin' && !$this->is_super_admin;
    }

    public function canAccessAdmin(): bool
    {
        return $this->is_super_admin;
    }

    public function canManageCompany(): bool
    {
        return $this->is_super_admin || $this->role === 'admin';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasPermission(string $permission): bool
    {
        // Implementar sistema de permissões baseado no role
        $permissions = $this->getRolePermissions();
        return in_array($permission, $permissions);
    }

    public function getRolePermissions(): array
    {
        if ($this->is_super_admin) {
            return ['*']; // Todos os privilégios
        }

        $rolePermissions = [
            'admin' => [
                'manage_company',
                'manage_users',
                'manage_invoices',
                'manage_clients',
                'manage_products',
                'manage_services',
                'view_reports',
                'manage_settings',
            ],
            'user' => [
                'create_invoices',
                'create_clients',
                'create_products',
                'create_services',
                'view_reports',
            ],
            'viewer' => [
                'view_invoices',
                'view_clients',
                'view_products',
                'view_services',
                'view_reports',
            ],
        ];

        return $rolePermissions[$this->role] ?? [];
    }

    public function updateLastActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    public function updateLoginInfo(string $ip): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_activity_at' => now(),
            'login_ip' => $ip,
        ]);
    }

    public function getPreference(string $key, $default = null)
    {
        $preferences = $this->preferences ?? [];
        return $preferences[$key] ?? $default;
    }

    public function setPreference(string $key, $value): void
    {
        $preferences = $this->preferences ?? [];
        $preferences[$key] = $value;
        $this->update(['preferences' => $preferences]);
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function makeAdmin(): void
    {
        $this->update(['role' => 'admin']);
    }

    public function makeSuperAdmin(): void
    {
        $this->update([
            'is_super_admin' => true,
            'role' => 'admin',
            'company_id' => null, // Super admins não pertencem a empresas
        ]);
    }

    public function removeSuperAdmin(): void
    {
        $this->update(['is_super_admin' => false]);
    }

    public function assignToCompany(Company $company, string $role = 'user'): void
    {
        $this->update([
            'company_id' => $company->id,
            'role' => $role,
            'is_super_admin' => false,
        ]);
    }

    private function getGravatarUrl(): string
    {
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=200";
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        // Quando criar usuário, definir valores padrão
        static::creating(function ($user) {
            if (is_null($user->timezone)) {
                $user->timezone = 'Africa/Maputo';
            }
            if (is_null($user->language)) {
                $user->language = 'pt';
            }
        });

        // Quando deletar usuário, limpar dados relacionados
        static::deleting(function ($user) {
            // Se for super admin, não permitir deleção se for o último
            if ($user->is_super_admin) {
                $superAdminCount = static::where('is_super_admin', true)->count();
                if ($superAdminCount <= 1) {
                    throw new \Exception('Não é possível deletar o último super administrador.');
                }
            }
        });
    }

     // Relationships
    public function companies()
    {
        return $this->hasMany(Company::class, 'admin_id');
    }

    public function activities()
    {
        return $this->hasMany(AdminActivity::class, 'admin_id');
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class);
    }

    public function getCompanySlugAttribute()
    {
        return $this->company ? $this->company->slug : 'default';
    }

    public function hasCompany()
    {
        return !is_null($this->company_id) && $this->company !== null;
    }

    public function supportTickets()
{
    return $this->hasMany(SupportTicket::class);
}

public function getUnreadSupportTicketsAttribute()
{
    return $this->supportTickets()
                ->whereDoesntHave('views', function($query) {
                    $query->where('user_id', $this->id);
                })
                ->whereHas('replies', function($query) {
                    $query->where('user_id', '!=', $this->id)
                          ->where('created_at', '>', $this->created_at);
                })
                ->count();
}
}
