<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'tax_number',
        'logo',
        'currency',
        'default_tax_rate',
        'bank_accounts',
        'mpesa_number',
        'status',
        'trial_ends_at',
        'subscription_plan',
        'subscription_type',
        'subscription_status',
        'subscription_expires_at',
        'plan_id',
        'suspended_at',
        'suspension_reason',
        'max_users',
        'max_invoices_per_month',
        'max_clients',
        'custom_domain_enabled',
        'api_access_enabled',
        'theme_settings',
        'feature_flags',
        'settings',
        'billing_email',
        'billing_address',
        'payment_method',
        'monthly_fee',
        'last_payment_at',
        'next_payment_due',
        'current_users_count',
        'current_month_invoices',
        'total_invoices',
        'total_clients',
        'total_revenue',
        'created_by',
        'last_activity_at',
        'admin_notes',
        'metadata',
    ];

    protected $casts = [
        'bank_accounts' => 'array',
        'theme_settings' => 'array',
        'feature_flags' => 'array',
        'settings' => 'array',
        'metadata' => 'array',
        'trial_ends_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'suspended_at' => 'datetime',
        'last_payment_at' => 'datetime',
        'next_payment_due' => 'datetime',
        'last_activity_at' => 'datetime',
        'default_tax_rate' => 'decimal:2',
        'monthly_fee' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'custom_domain_enabled' => 'boolean',
        'api_access_enabled' => 'boolean',
        // 'status' => 'boolean',
    ];

    // Constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_TRIAL = 'trial';
     const STATUS_EXPIRED = 'expired';
    const STATUS_PENDING = 'pending';

    const SUBSCRIPTION_TYPE_TRIAL = 'trial';
    const SUBSCRIPTION_TYPE_PAID = 'paid';

    const SUBSCRIPTION_STATUS_ACTIVE = 'active';
    const SUBSCRIPTION_STATUS_EXPIRED = 'expired';
    const SUBSCRIPTION_STATUS_SUSPENDED = 'suspended';

    const SUBSCRIPTION_STATUS_CANCELLED = 'cancelled';
    const SUBSCRIPTION_STATUS_PENDING_PAYMENT = 'pending_payment';


    const SUBSCRIPTION_ACTIVE = 'active';
    const SUBSCRIPTION_SUSPENDED = 'suspended';
    const SUBSCRIPTION_EXPIRED = 'expired';
    const SUBSCRIPTION_CANCELLED = 'cancelled';
    const SUBSCRIPTION_PENDING_PAYMENT = 'pending_payment';





    protected static function boot()
    {
        parent::boot();

        static::creating(function ($company) {
            if (empty($company->slug)) {
                $company->slug = Str::slug($company->name);
            }

            // Garantir slug único
            $originalSlug = $company->slug;
            $counter = 1;
            while (static::where('slug', $company->slug)->exists()) {
                $company->slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Definir valores padrão para nova empresa
            // if (!$company->subscription_type) {
            //     $company->subscription_type = self::SUBSCRIPTION_TYPE_TRIAL;
            // }
            if (!$company->subscription_status) {
                $company->subscription_status = self::SUBSCRIPTION_STATUS_ACTIVE;
            }
            if (!$company->subscription_expires_at && $company->subscription_type === self::SUBSCRIPTION_TYPE_TRIAL) {
                $company->subscription_expires_at = now()->addDays(14); // 14 dias de trial
            }
        });
    }

    /**
     * Relacionamentos
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * MÉTODOS DE SUBSCRIÇÃO
     */

    /**
     * Verificar se a empresa tem plano ativo
     */
    public function hasActivePlan(): bool
    {
        return $this->subscription_status === self::SUBSCRIPTION_STATUS_ACTIVE &&
               $this->plan_id &&
               ($this->subscription_expires_at === null || $this->subscription_expires_at->isFuture());
    }

    /**
     * Verificar se pode criar fatura
     */
    public function canCreateInvoice(): bool
    {
        if (!$this->hasActivePlan()) {
            return false;
        }

        if (!$this->plan) {
            return false;
        }

        $currentMonth = now()->startOfMonth();
        $invoicesThisMonth = $this->invoices()
            ->whereYear('created_at', $currentMonth->year)
            ->whereMonth('created_at', $currentMonth->month)
            ->count();

        return $invoicesThisMonth < ($this->plan->max_invoices_per_month ?? PHP_INT_MAX);
    }

    /**
     * Verificar se pode criar usuário
     */
    public function canCreateUser(): bool
    {
        if (!$this->hasActivePlan()) {
            return false;
        }

        if (!$this->plan) {
            return false;
        }

        return $this->users()->count() < ($this->plan->max_users ?? PHP_INT_MAX);
    }

    /**
     * Verificar se pode criar cliente
     */
    public function canCreateClient(): bool
    {
        if (!$this->hasActivePlan()) {
            return false;
        }

        // Se não há limite de clientes no plano, sempre pode criar
        if (!$this->plan || !$this->plan->max_clients) {
            return true;
        }

        return $this->clients()->count() < $this->plan->max_clients;
    }

    /**
     * Obter uso atual de usuários
     */
    public function getUserUsage(): array
    {
        $current = $this->users()->count();
        $max = $this->plan->max_users ?? 0;

        return [
            'current' => $current,
            'max' => $max,
            'percentage' => $max > 0 ? min(100, ($current / $max) * 100) : 0,
            'remaining' => max(0, $max - $current),
            'exceeded' => $current > $max
        ];
    }

    /**
     * Obter uso atual de faturas mensais
     */
    public function getInvoiceUsage(): array
    {
        $currentMonth = now()->startOfMonth();
        $current = $this->invoices()
            ->whereYear('created_at', $currentMonth->year)
            ->whereMonth('created_at', $currentMonth->month)
            ->count();
            $current = 45;

        $max = $this->plan->max_invoices_per_month ?? 0;

        return [
            'current' => $current,
            'max' => $max,
            'percentage' => $max > 0 ? min(100, ($current / $max) * 100) : 0,
            'remaining' => max(0, $max - $current),
            'exceeded' => $current >= $max
        ];
    }

    /**
     * Verificar se está em período de teste
     */
    public function isTrial(): bool
    {
        return $this->subscription_type === self::SUBSCRIPTION_TYPE_TRIAL;
    }

    /**
     * Verificar se o teste está expirando (próximos 3 dias)
     */
    public function isTrialExpiring(): bool
    {
        return $this->isTrial() &&
               $this->subscription_expires_at &&
               $this->subscription_expires_at->diffInDays(now()) <= 3;
    }

    /**
     * Verificar se está suspenso
     */
    public function isSuspended(): bool
    {
        return $this->subscription_status === self::SUBSCRIPTION_STATUS_SUSPENDED;
    }

/**
     * Verificar se a empresa está ativa
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE &&
               $this->subscription_status === self::SUBSCRIPTION_ACTIVE;
    }

    /**
     * Verificar se está expirado
     */
    public function isExpired(): bool
    {
        return $this->subscription_expires_at && $this->subscription_expires_at->isPast();
    }

    /**
     * Obter dias restantes até expiração
     */
    public function getDaysUntilExpiration(): ?int
    {
        if (!$this->subscription_expires_at) {
            return null;
        }

        $diff = $this->subscription_expires_at->diffInDays(now(), false);
        return $diff > 0 ? $diff : 0;
    }

    /**
     * Obter status de bloqueio
     */
    public function getBlockStatus(): array
    {
        $reasons = [];
        $blocked = false;

        // Verificar se tem plano ativo
        if (!$this->hasActivePlan()) {
            $blocked = true;
            if ($this->isExpired()) {
                $reasons[] = 'Subscrição expirada';
            } elseif ($this->isSuspended()) {
                $reasons[] = 'Conta suspensa';
            } else {
                $reasons[] = 'Sem plano ativo';
            }
        }

        // Verificar limites de usuários
        $userUsage = $this->getUserUsage();
        if ($userUsage['exceeded']) {
            $blocked = true;
            $reasons[] = 'Limite de usuários excedido';
        }

        // Verificar limites de faturas
        $invoiceUsage = $this->getInvoiceUsage();
        if ($invoiceUsage['exceeded']) {
            $blocked = true;
            $reasons[] = 'Limite de faturas mensais atingido';
        }

        return [
            'blocked' => $blocked,
            'reasons' => $reasons,
            'user_usage' => $userUsage,
            'invoice_usage' => $invoiceUsage
        ];
    }

    /**
     * Obter avisos de limite
     */
    public function getUsageWarnings(): array
    {
        $warnings = [];

        if (!$this->hasActivePlan()) {
            return $warnings;
        }

        // Aviso de usuários (80% do limite)
        $userUsage = $this->getUserUsage();
        if ($userUsage['percentage'] >= 80 && !$userUsage['exceeded']) {
            $warnings[] = [
                'type' => 'users',
                'message' => 'Você está próximo do limite de usuários (' . $userUsage['current'] . '/' . $userUsage['max'] . ')',
                'percentage' => $userUsage['percentage'],
                'priority' => $userUsage['percentage'] >= 95 ? 'high' : 'medium'
            ];
        }

        // Aviso de faturas (90% do limite)
        $invoiceUsage = $this->getInvoiceUsage();
        if ($invoiceUsage['percentage'] >= 90 && !$invoiceUsage['exceeded']) {
            $warnings[] = [
                'type' => 'invoices',
                'message' => 'Você está próximo do limite de faturas mensais (' . $invoiceUsage['current'] . '/' . $invoiceUsage['max'] . ')',
                'percentage' => $invoiceUsage['percentage'],
                'priority' => 'high'
            ];
        }

        // Aviso de expiração
        $daysLeft = $this->getDaysUntilExpiration();
        if ($daysLeft !== null && $daysLeft <= 7) {
            $priority = $daysLeft <= 3 ? 'urgent' : ($daysLeft <= 7 ? 'high' : 'medium');
            $warnings[] = [
                'type' => 'expiration',
                'message' => $this->isTrial() ?
                    "Seu período de teste expira em {$daysLeft} dias" :
                    "Sua subscrição expira em {$daysLeft} dias",
                'days_left' => $daysLeft,
                'priority' => $priority
            ];
        }

        return $warnings;
    }

    /**
     * Calcular valor de upgrade pro-rated
     */
    public function calculateUpgradeAmount(Plan $newPlan): float
    {
        if (!$this->plan || !$this->subscription_expires_at) {
            return $newPlan->price;
        }

        $daysLeft = $this->subscription_expires_at->diffInDays(now());
        if ($daysLeft <= 0) {
            return $newPlan->price;
        }

        // Calcular com base no ciclo de cobrança
        $totalDays = match($this->plan->billing_cycle) {
            'yearly' => 365,
            'quarterly' => 90,
            default => 30, // monthly
        };

        $currentPlanDaily = $this->plan->price / $totalDays;
        $newPlanDaily = $newPlan->price / $totalDays;

        $unusedCredit = $currentPlanDaily * $daysLeft;
        $newPlanCost = $newPlanDaily * $daysLeft;

        return max(0, $newPlanCost - $unusedCredit);
    }

    /**
     * SCOPES
     */
    public function scopeActive($query)
    {
        return $query->where('subscription_status', self::SUBSCRIPTION_STATUS_ACTIVE);
    }

    public function scopeExpired($query)
    {
        return $query->where('subscription_expires_at', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('subscription_expires_at', '<=', now()->addDays($days))
                     ->where('subscription_expires_at', '>', now());
    }

    public function scopeTrial($query)
    {
        return $query->where('subscription_type', self::SUBSCRIPTION_TYPE_TRIAL);
    }

    public function scopeSuspended($query)
    {
        return $query->where('subscription_status', self::SUBSCRIPTION_STATUS_SUSPENDED);
    }

    /**
     * ACCESSORS
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->subscription_status) {
            self::SUBSCRIPTION_STATUS_ACTIVE => 'green',
            self::SUBSCRIPTION_STATUS_SUSPENDED => 'red',
            self::SUBSCRIPTION_STATUS_EXPIRED => 'gray',
            self::SUBSCRIPTION_STATUS_CANCELLED => 'gray',
            self::SUBSCRIPTION_STATUS_PENDING_PAYMENT => 'yellow',
            default => 'gray'
        };
    }

    public function getStatusNameAttribute(): string
    {
        return match($this->subscription_status) {
            self::SUBSCRIPTION_STATUS_ACTIVE => 'Ativo',
            self::SUBSCRIPTION_STATUS_SUSPENDED => 'Suspenso',
            self::SUBSCRIPTION_STATUS_EXPIRED => 'Expirado',
            self::SUBSCRIPTION_STATUS_CANCELLED => 'Cancelado',
            self::SUBSCRIPTION_STATUS_PENDING_PAYMENT => 'Pagamento Pendente',
            default => 'Desconhecido'
        };
    }


    // Rascunhos
    /**
     * Suspender empresa
     */
    public function suspend(string $reason)
    {
        $this->update([
            'status' => self::STATUS_SUSPENDED,
            'subscription_status' => self::SUBSCRIPTION_SUSPENDED,
            'suspended_at' => now(),
            'suspension_reason' => $reason
        ]);

        // Log da suspensão (opcional)
        // activity()
        //     ->performedOn($this)
        //     ->withProperties([
        //         'reason' => $reason,
        //         'suspended_by' => auth()->id()
        //     ])
        //     ->log('Company suspended');

        return $this;
    }

     /**
     * Ativar/Reativar empresa
     */
    public function activate()
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'subscription_status' => self::SUBSCRIPTION_ACTIVE,
            'suspended_at' => null,
            'suspension_reason' => null
        ]);

        // Log da ativação (opcional)
        // activity()
        //     ->performedOn($this)
        //     ->withProperties([
        //         'activated_by' => auth()->id()
        //     ])
        //     ->log('Company activated');

        return $this;
    }

     /**
     * Estender período de trial
     */
    public function extendTrial(int $days)
    {
        $currentTrialEnd = $this->trial_ends_at ?? now();
        $newTrialEnd = $currentTrialEnd->addDays($days);

        $this->update([
            'trial_ends_at' => $newTrialEnd
        ]);

        // Log da extensão (opcional)
        // activity()
        //     ->performedOn($this)
        //     ->withProperties([
        //         'days_added' => $days,
        //         'new_trial_end' => $newTrialEnd->toISOString(),
        //         'extended_by' => auth()->id()
        //     ])
        //     ->log('Trial extended');

        return $this;
    }

     /**
     * Verificar se o trial expirou
     */
    public function isTrialExpired(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }



    // NES METHODS

    /**
     * Relacionamento: Uma empresa tem muitas subscrições
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(CompanySubscription::class);
    }

    /**
     * Relacionamento: Uma empresa tem UMA subscrição ativa
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(CompanySubscription::class)
            ->whereIn('status', ['active', 'trialing'])
            ->where('ends_at', '>', now())
            ->latest('created_at');
    }

    /**
     * Relacionamento: Plano atual através da subscrição ativa
     * (Opcional - útil para acessar direto)
     */
    public function currentPlan(): BelongsTo|null
    {
        $subscription = $this->activeSubscription;
        return $subscription ? $subscription->plan() : null;
    }

    /**
     * Helper: Verificar se tem subscrição ativa
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Helper: Verificar se pode acessar o sistema
     */
    public function canAccessSystem(): bool
    {
        $subscription = $this->activeSubscription;
        return $subscription && $subscription->canAccess();
    }

    /**
     * Helper: Obter limites de uso da subscrição atual
     */
    public function getUsageLimits(): array
    {
        $subscription = $this->activeSubscription;
        
        if (!$subscription) {
            return [
                'max_users' => 1,
                'max_invoices_per_month' => 10,
                'max_clients' => 50,
            ];
        }

        return [
            'max_users' => $subscription->plan->max_users ?? null,
            'max_invoices_per_month' => $subscription->plan->max_invoices_per_month ?? null,
            'max_clients' => $subscription->plan->max_clients ?? null,
            'max_products' => $subscription->plan->max_products ?? null,
            'max_storage_mb' => $subscription->plan->max_storage_mb ?? null,
        ];
    }

    /**
     * Helper: Verificar uso de faturas do mês atual
     */
    public function getInvoiceUsageFeatured(): array
    {
        $subscription = $this->activeSubscription;
        $max = $subscription?->plan->max_invoices_per_month ?? 10;
        
        $current = $this->invoices()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        return [
            'current' => $current,
            'max' => $max,
            'percentage' => $max > 0 ? ($current / $max) * 100 : 0,
            'remaining' => max(0, $max - $current),
        ];
    }

    /**
     * Helper: Verificar uso de usuários
     */
    public function getUserUsageFeatured(): array
    {
        $subscription = $this->activeSubscription;
        $max = $subscription?->plan->max_users ?? 1;
        
        $current = $this->users()->count();

        return [
            'current' => $current,
            'max' => $max,
            'percentage' => $max > 0 ? ($current / $max) * 100 : 0,
            'remaining' => max(0, $max - $current),
        ];
    }

}
