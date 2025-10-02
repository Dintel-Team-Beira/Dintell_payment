<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'billing_cycle',
        'is_popular',
        'is_active',
        'sort_order',
        'max_users',
        'max_companies',
        'max_invoices_per_month',
        'max_clients',
        'max_products',
        'max_storage_mb',
        'features',
        'limitations',
        'trial_days',
        'has_trial',
        'metadata',
        'stripe_price_id',
        'color',
        'icon'
    ];

    protected $casts = [
        'features' => 'array',
        'limitations' => 'array',
        'metadata' => 'array',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'has_trial' => 'boolean',
        'price' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });

        static::updating(function ($plan) {
            if ($plan->isDirty('name') && empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }

    /**
     * Relacionamentos
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(CompanySubscription::class);
    }

    /**
     * Scopes
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular(Builder $query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeOrdered(Builder $query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    public function scopeForBillingCycle(Builder $query, string $cycle)
    {
        return $query->where('billing_cycle', $cycle);
    }

    /**
     * Métodos auxiliares
     */
    public function getFormattedPriceAttribute()
    {
        if ($this->price == 0) {
            return 'Gratuito';
        }

        return number_format($this->price, 2, ',', '.') . ' ' . $this->currency;
    }

    public function getBillingCycleTextAttribute()
    {
        return match($this->billing_cycle) {
            'monthly' => 'Mensal',
            'quarterly' => 'Trimestral',
            'yearly' => 'Anual',
            default => ucfirst($this->billing_cycle)
        };
    }

    public function getPricePerMonthAttribute()
    {
        return match($this->billing_cycle) {
            'monthly' => $this->price,
            'quarterly' => $this->price / 3,
            'yearly' => $this->price / 12,
            default => $this->price
        };
    }

    public function getStorageFormattedAttribute()
    {
        if (is_null($this->max_storage_mb)) {
            return 'Ilimitado';
        }

        if ($this->max_storage_mb >= 1024) {
            return number_format($this->max_storage_mb / 1024, 1) . ' GB';
        }

        return $this->max_storage_mb . ' MB';
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    public function hasLimitation(string $limitation): bool
    {
        return isset($this->limitations[$limitation]);
    }

    public function getLimitationValue(string $limitation, $default = null)
    {
        return $this->limitations[$limitation] ?? $default;
    }

    public function isUnlimited(string $field): bool
    {
        return is_null($this->$field);
    }

    public function getLimit(string $field): string
    {
        $value = $this->$field;

        if (is_null($value)) {
            return 'Ilimitado';
        }

        return number_format($value);
    }

    public function canUpgradeTo(Plan $targetPlan): bool
    {
        return $targetPlan->price > $this->price ||
               ($targetPlan->price == $this->price && $targetPlan->id != $this->id);
    }

    public function canDowngradeTo(Plan $targetPlan): bool
    {
        return $targetPlan->price < $this->price;
    }

    /**
     * Métodos estáticos para facilitar uso
     */
    public static function getFreePlan()
    {
        return static::where('price', 0)->active()->first();
    }

    public static function getPopularPlan()
    {
        return static::popular()->active()->first();
    }

    public static function getMostExpensive()
    {
        return static::active()->orderBy('price', 'desc')->first();
    }

    public static function getCheapest()
    {
        return static::active()->where('price', '>', 0)->orderBy('price')->first();
    }

    /**
     * Validações customizadas
     */
    public function validateCompanyLimits(Company $company): array
    {
        $violations = [];

        if (!is_null($this->max_users) && $company->users()->count() > $this->max_users) {
            $violations[] = "Limite de usuários excedido ({$company->users()->count()}/{$this->max_users})";
        }

        if (!is_null($this->max_clients) && $company->clients()->count() > $this->max_clients) {
            $violations[] = "Limite de clientes excedido ({$company->clients()->count()}/{$this->max_clients})";
        }

        if (!is_null($this->max_products) && $company->products()->count() > $this->max_products) {
            $violations[] = "Limite de produtos excedido ({$company->products()->count()}/{$this->max_products})";
        }

        // Verificar faturas do mês atual
        if (!is_null($this->max_invoices_per_month)) {
            $currentMonthInvoices = $company->invoices()
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();

            if ($currentMonthInvoices > $this->max_invoices_per_month) {
                $violations[] = "Limite de faturas mensais excedido ({$currentMonthInvoices}/{$this->max_invoices_per_month})";
            }
        }

        return $violations;
    }

    /**
     * Método para gerar dados de exemplo
     */
    public static function createDefaultPlans()
    {
        $plans = [
            [
                'name' => 'Básico',
                'slug' => 'basico',
                'description' => 'Ideal para pequenos negócios que estão começando',
                'price' => 0,
                'currency' => 'MZN',
                'billing_cycle' => 'monthly',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
                'max_users' => 2,
                'max_companies' => 1,
                'max_invoices_per_month' => 50,
                'max_clients' => 100,
                'max_products' => 50,
                'max_storage_mb' => 100,
                'features' => [
                    'Faturação básica',
                    'Gestão de clientes',
                    'Relatórios básicos',
                    'Suporte por email'
                ],
                'limitations' => [],
                'trial_days' => 0,
                'has_trial' => false,
                'color' => '#6B7280',
                'icon' => 'rocket'
            ],
            [
                'name' => 'Profissional',
                'slug' => 'profissional',
                'description' => 'Para empresas em crescimento que precisam de mais recursos',
                'price' => 2500,
                'currency' => 'MZN',
                'billing_cycle' => 'monthly',
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 2,
                'max_users' => 10,
                'max_companies' => 3,
                'max_invoices_per_month' => 500,
                'max_clients' => 1000,
                'max_products' => 500,
                'max_storage_mb' => 1024,
                'features' => [
                    'Todas as funcionalidades do Básico',
                    'Múltiplas empresas',
                    'Relatórios avançados',
                    'Backup automático',
                    'API access',
                    'Suporte prioritário'
                ],
                'limitations' => [],
                'trial_days' => 14,
                'has_trial' => true,
                'color' => '#3B82F6',
                'icon' => 'star'
            ],
            [
                'name' => 'Empresarial',
                'slug' => 'empresarial',
                'description' => 'Solução completa para grandes empresas',
                'price' => 7500,
                'currency' => 'MZN',
                'billing_cycle' => 'monthly',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 3,
                'max_users' => null, // Ilimitado
                'max_companies' => null,
                'max_invoices_per_month' => null,
                'max_clients' => null,
                'max_products' => null,
                'max_storage_mb' => null,
                'features' => [
                    'Todas as funcionalidades do Profissional',
                    'Usuários ilimitados',
                    'Empresas ilimitadas',
                    'Customizações avançadas',
                    'Integração personalizada',
                    'Suporte 24/7',
                    'Gerente de conta dedicado'
                ],
                'limitations' => [],
                'trial_days' => 30,
                'has_trial' => true,
                'color' => '#7C3AED',
                'icon' => 'building'
            ]
        ];

        foreach ($plans as $planData) {
            static::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }
    }


    // New methods
     /**
     * Relacionamento: Subscrições ativas deste plano
     */
    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(CompanySubscription::class)
            ->whereIn('status', ['active', 'trialing'])
            ->where('ends_at', '>', now());
    }

    /**
     * Helper: Contar empresas ativas neste plano
     */
    public function getActiveSubscriptionsCount(): int
    {
        return $this->activeSubscriptions()->count();
    }

    /**
     * Helper: Receita mensal deste plano
     */
    public function getMonthlyRevenue(): float
    {
        return $this->activeSubscriptions()
            ->where('billing_cycle', 'monthly')
            ->sum('amount');
    }

    /**
     * Helper: Formatar limite (null = Ilimitado)
     */
    // public function getLimit(string $field): string
    // {
    //     $value = $this->{$field};
    //     return $value ? (string) $value : 'Ilimitado';
    // }

    /**
     * Helper: Verificar se plano pode ser deletado
     */
    public function canBeDeleted(): bool
    {
        return $this->activeSubscriptions()->count() === 0;
    }

    /**
     * Accessor: Preço formatado
     */
    // public function getFormattedPriceAttribute(): string
    // {
    //     if ($this->price == 0) {
    //         return 'Gratuito';
    //     }
    //     return number_format($this->price, 2) . ' MT';
    // }

    /**
     * Accessor: Texto do ciclo
     */
    // public function getBillingCycleTextAttribute(): string
    // {
    //     return match($this->billing_cycle) {
    //         'monthly' => 'Mensal',
    //         'quarterly' => 'Trimestral',
    //         'yearly' => 'Anual',
    //         default => ucfirst($this->billing_cycle),
    //     };
    // }

    /**
     * Accessor: Preço por mês (para comparação)
     */
    // public function getPricePerMonthAttribute(): float
    // {
    //     return match($this->billing_cycle) {
    //         'monthly' => $this->price,
    //         'quarterly' => $this->price / 3,
    //         'yearly' => $this->price / 12,
    //         default => $this->price,
    //     };
    // }
}
