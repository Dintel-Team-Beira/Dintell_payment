<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AdminActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'action',
        'description',
        'model_type',
        'model_id',
        'properties',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'severity',
        'category',
    ];

    protected $casts = [
        'properties' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constantes para severidade
    const SEVERITY_LOW = 'low';
    const SEVERITY_MEDIUM = 'medium';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_CRITICAL = 'critical';

    // Constantes para categorias
    const CATEGORY_USER_MANAGEMENT = 'user_management';
    const CATEGORY_COMPANY_MANAGEMENT = 'company_management';
    const CATEGORY_INVOICE_MANAGEMENT = 'invoice_management';
    const CATEGORY_SYSTEM_CONFIG = 'system_config';
    const CATEGORY_SECURITY = 'security';
    const CATEGORY_DATA_EXPORT = 'data_export';

    // Relacionamentos
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo('model');
    }

    // Scopes
    public function scopeByAdmin($query, $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', self::SEVERITY_CRITICAL);
    }

    public function scopeHigh($query)
    {
        return $query->where('severity', self::SEVERITY_HIGH);
    }

    // Accessors
    public function getSeverityLabelAttribute(): string
    {
        return match($this->severity) {
            self::SEVERITY_LOW => 'Baixa',
            self::SEVERITY_MEDIUM => 'Média',
            self::SEVERITY_HIGH => 'Alta',
            self::SEVERITY_CRITICAL => 'Crítica',
            default => 'Desconhecida'
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            self::CATEGORY_USER_MANAGEMENT => 'Gestão de Usuários',
            self::CATEGORY_COMPANY_MANAGEMENT => 'Gestão de Empresas',
            self::CATEGORY_INVOICE_MANAGEMENT => 'Gestão de Faturas',
            self::CATEGORY_SYSTEM_CONFIG => 'Configuração do Sistema',
            self::CATEGORY_SECURITY => 'Segurança',
            self::CATEGORY_DATA_EXPORT => 'Exportação de Dados',
            default => 'Geral'
        };
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }

    // Métodos estáticos para logging
    public static function log(string $action, string $description, array $properties = [], string $severity = self::SEVERITY_LOW, string $category = null, $subject = null): void
    {
        if (!Auth::check() || !Auth::user()->can_access_admin) {
            return;
        }

        static::create([
            'admin_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'model_type' => $subject ? get_class($subject) : null,
            'model_id' => $subject ? $subject->id : null,
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::url(),
            'method' => Request::method(),
            'severity' => $severity,
            'category' => $category,
        ]);
    }

    public static function logUserAction(string $action, User $user, string $description, array $properties = []): void
    {
        static::log(
            $action,
            $description,
            array_merge($properties, [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role,
            ]),
            self::SEVERITY_MEDIUM,
            self::CATEGORY_USER_MANAGEMENT,
            $user
        );
    }

    public static function logCompanyAction(string $action, Company $company, string $description, array $properties = []): void
    {
        static::log(
            $action,
            $description,
            array_merge($properties, [
                'company_name' => $company->name,
                'company_status' => $company->status,
                'company_plan' => $company->subscription_plan,
            ]),
            self::SEVERITY_HIGH,
            self::CATEGORY_COMPANY_MANAGEMENT,
            $company
        );
    }

    public static function logSecurityAction(string $action, string $description, array $properties = []): void
    {
        static::log(
            $action,
            $description,
            $properties,
            self::SEVERITY_CRITICAL,
            self::CATEGORY_SECURITY
        );
    }

    public static function logSystemAction(string $action, string $description, array $properties = []): void
    {
        static::log(
            $action,
            $description,
            $properties,
            self::SEVERITY_HIGH,
            self::CATEGORY_SYSTEM_CONFIG
        );
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        // Auto-limpar registros antigos (opcional)
        static::created(function () {
            // Limpar registros mais antigos que 90 dias
            static::where('created_at', '<', now()->subDays(90))->delete();
        });
    }
}
