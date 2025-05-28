<?php
// app/Models/Subscription.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'subscription_plan_id',
        'domain',
        'subdomain',
        'api_key',
        'status',
        'manual_status',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'suspended_at',
        'cancelled_at',
        'suspension_reason',
        'cancellation_reason',
        'suspension_page_config',
        'amount_paid',
        'total_revenue',
        'last_payment_date',
        'next_payment_due',
        'payment_failures',
        'payment_method',
        'payment_reference',
        'total_requests',
        'monthly_requests',
        'last_request_at',
        'storage_used_gb',
        'bandwidth_used_gb',
        'auto_renew',
        'email_notifications',
        'expiry_warning_days',
        'last_warning_sent'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'suspended_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'last_payment_date' => 'datetime',
        'next_payment_due' => 'datetime',
        'last_request_at' => 'datetime',
        'last_warning_sent' => 'datetime',
        'suspension_page_config' => 'array',
        'amount_paid' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'storage_used_gb' => 'decimal:2',
        'bandwidth_used_gb' => 'decimal:2',
        'auto_renew' => 'boolean',
        'email_notifications' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            if (empty($subscription->api_key)) {
                $subscription->api_key = 'sk_' . Str::random(40);
            }
        });
    }

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function apiLogs()
    {
        return $this->hasMany(ApiLog::class);
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }

    // Status Methods
    public function isActive()
    {
        return $this->status === 'active' &&
            $this->manual_status === 'enabled' &&
            ($this->ends_at === null || $this->ends_at->isFuture()) &&
            !$this->isExpired();
    }

    public function isExpired()
    {
        if ($this->ends_at && $this->ends_at->isPast()) {
            return true;
        }

        if ($this->trial_ends_at && $this->trial_ends_at->isPast() && $this->status === 'trial') {
            return true;
        }

        return false;
    }

    public function isTrial()
    {
        return $this->status === 'trial' &&
            $this->trial_ends_at &&
            $this->trial_ends_at->isFuture();
    }

    public function canAccess()
    {
        // Verificação completa de acesso
        if ($this->manual_status === 'disabled') {
            return false;
        }

        if ($this->status === 'suspended' || $this->status === 'cancelled') {
            return false;
        }

        if ($this->isExpired()) {
            return false;
        }

        return true;
    }

    // Date Calculations
    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->ends_at) return null;
        return (int) now()->diffInDays($this->ends_at, false); // Convertendo para inteiro
    }
    public function getTrialDaysLeftAttribute()
    {
        if (!$this->trial_ends_at || $this->status !== 'trial') return 0;
        return (int) max(0, now()->diffInDays($this->trial_ends_at, false)); // Convertendo para inteiro
    }

    public function getUsagePercentageAttribute()
    {
        if (!$this->plan) return 0;

        // Verificar se os limites do plano são válidos para evitar divisão por zero
        $maxStorage = $this->plan->max_storage_gb ?? 0;
        $maxBandwidth = $this->plan->max_bandwidth_gb ?? 0;

        $storagePercent = 0;
        $bandwidthPercent = 0;

        // Calcular porcentagem de storage apenas se o limite for maior que zero
        if ($maxStorage > 0) {
            $storagePercent = ($this->storage_used_gb / $maxStorage) * 100;
        }

        // Calcular porcentagem de bandwidth apenas se o limite for maior que zero
        if ($maxBandwidth > 0) {
            $bandwidthPercent = ($this->bandwidth_used_gb / $maxBandwidth) * 100;
        }

        return max($storagePercent, $bandwidthPercent);
    }

    // Actions
    public function suspend($reason, $config = null)
    {
        $this->update([
            'status' => 'suspended',
            'suspended_at' => now(),
            'suspension_reason' => $reason,
            'suspension_page_config' => $config
        ]);

        // Enviar email de suspensão
        $this->client->notify(new \App\Notifications\SubscriptionSuspendedNotification($this));

        return $this;
    }

    public function activate()
    {
        $this->update([
            'status' => 'active',
            'suspended_at' => null,
            'suspension_reason' => null
        ]);

        // Enviar email de ativação
        $this->client->notify(new \App\Notifications\SubscriptionActivatedNotification($this));

        return $this;
    }

    public function renew($paymentAmount = null, $paymentMethod = null, $paymentReference = null)
    {
        $nextBilling = $this->calculateNextBillingDate();
        $amount = $paymentAmount ?? $this->plan->price;

        $this->update([
            'status' => 'active',
            'ends_at' => $nextBilling,
            'next_payment_due' => $this->calculateNextBillingDate($nextBilling), // Próximo pagamento após este
            'last_payment_date' => now(),
            'amount_paid' => $amount,
            'total_revenue' => $this->total_revenue + $amount,
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
            'payment_failures' => 0,
            'suspended_at' => null,        // Remove suspensão se houver
            'suspension_reason' => null,   // Remove motivo de suspensão
            'cancelled_at' => null,        // Remove cancelamento se houver
            'cancellation_reason' => null  // Remove motivo de cancelamento
        ]);

        // Log da renovação
        \Log::info('Subscription renewed', [
            'subscription_id' => $this->id,
            'old_ends_at' => $this->getOriginal('ends_at'),
            'new_ends_at' => $nextBilling,
            'amount' => $amount,
            'method' => $paymentMethod
        ]);

        // Enviar email de renovação
        $this->client->notify(new \App\Notifications\SubscriptionRenewedNotification($this));

        return $this;
    }

    public function calculateNextBillingDate($baseDate = null)
    {
        // Use a data fornecida ou a data de término atual, ou agora como fallback
        $startDate = $baseDate ?? $this->ends_at ?? now();

        // IMPORTANTE: Criar uma nova instância Carbon para não modificar a original
        $newDate = $startDate instanceof \Carbon\Carbon
            ? $startDate->copy()
            : \Carbon\Carbon::parse($startDate);

        // Adicionar os dias do ciclo de cobrança
        $billingCycleDays = $this->plan->billing_cycle_days ?? 30;

        return $newDate->addDays($billingCycleDays);
    }

    // Método adicional para debug
public function debugRenewal()
{
    return [
        'current_ends_at' => $this->ends_at?->format('Y-m-d H:i:s'),
        'calculated_next_billing' => $this->calculateNextBillingDate()?->format('Y-m-d H:i:s'),
        'billing_cycle_days' => $this->plan->billing_cycle_days ?? 'NOT SET',
        'plan_price' => $this->plan->price ?? 'NOT SET',
        'is_expired' => $this->isExpired(),
        'can_access' => $this->canAccess()
    ];
}
    public function incrementUsage($requests = 1, $bandwidth = 0)
    {
        $this->increment('total_requests', $requests);
        $this->increment('monthly_requests', $requests);
        $this->increment('bandwidth_used_gb', $bandwidth);
        $this->update(['last_request_at' => now()]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('manual_status', 'enabled');
    }

    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('ends_at', '<', now())
                ->orWhere(function ($q2) {
                    $q2->where('status', 'trial')
                        ->where('trial_ends_at', '<', now());
                });
        });
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('ends_at', '<=', now()->addDays($days))
            ->where('ends_at', '>', now())
            ->where('status', 'active');
    }

    public function scopeNeedsWarning($query)
    {
        return $query->expiringSoon()
            ->where(function ($q) {
                $q->whereNull('last_warning_sent')
                    ->orWhere('last_warning_sent', '<', now()->subDays(1));
            });
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function getStatsAttribute()
    {
        return [
            'total_requests' => $this->total_requests,
            'days_active' => $this->starts_at ? now()->diffInDays($this->starts_at) : 0,
            'payment_failures' => $this->payment_failures,
            'monthly_requests' => $this->monthly_requests,

        ];
    }

    /**
 * Obter estatísticas formatadas da subscription
 */
public function getFormattedStatsAttribute()
{
    return [
        'total_requests' => number_format($this->total_requests),
        'days_active' => $this->starts_at
            ? (int) now()->diffInDays($this->starts_at)
            : 0,
        'payment_failures' => $this->payment_failures,
        'monthly_requests' => number_format($this->monthly_requests ?? 0),
        'storage_used' => round($this->storage_used_gb ?? 0, 2),
        'bandwidth_used' => round($this->bandwidth_used_gb ?? 0, 2),
        'usage_percentage' => round($this->usage_percentage ?? 0, 1),
        'total_revenue' => $this->total_revenue
            ? 'MT ' . number_format($this->total_revenue, 2)
            : 'MT 0.00',
        'last_payment' => $this->last_payment_date
            ? $this->last_payment_date->format('d/m/Y')
            : 'Nunca',
        'next_payment' => $this->next_payment_due
            ? $this->next_payment_due->format('d/m/Y')
            : 'N/A',
        'days_until_expiry' => $this->days_until_expiry ?? 'N/A',
        'trial_days_left' => $this->trial_days_left ?? 0,
        'uptime_days' => $this->getUptimeDays(),
        'avg_daily_requests' => $this->getAverageDailyRequests()
    ];
}
/**
 * Calcular dias de uptime
 */
private function getUptimeDays()
{
    if (!$this->starts_at) return 0;

    $endDate = $this->cancelled_at ?? now();
    return (int) $this->starts_at->diffInDays($endDate);
}

/**
 * Calcular média de requests por dia
 */
private function getAverageDailyRequests()
{
    if (!$this->starts_at || $this->total_requests === 0) return 0;

    $daysActive = now()->diffInDays($this->starts_at);
    if ($daysActive === 0) return 0;

    return round($this->total_requests / $daysActive, 1);
}

/**
 * Obter estatísticas detalhadas para relatórios
 */
public function getDetailedStats()
{
    return [
        'basic' => [
            'id' => $this->id,
            'domain' => $this->domain,
            'status' => $this->status,
            'manual_status' => $this->manual_status,
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'starts_at' => $this->starts_at?->format('d/m/Y H:i'),
            'ends_at' => $this->ends_at?->format('d/m/Y H:i')
        ],
        'usage' => [
            'total_requests' => $this->total_requests,
            'monthly_requests' => $this->monthly_requests,
            'storage_used_gb' => $this->storage_used_gb,
            'bandwidth_used_gb' => $this->bandwidth_used_gb,
            'usage_percentage' => $this->usage_percentage,
            'last_request_at' => $this->last_request_at?->format('d/m/Y H:i')
        ],
        'financial' => [
            'amount_paid' => $this->amount_paid,
            'total_revenue' => $this->total_revenue,
            'payment_method' => $this->payment_method,
            'payment_reference' => $this->payment_reference,
            'last_payment_date' => $this->last_payment_date?->format('d/m/Y H:i'),
            'next_payment_due' => $this->next_payment_due?->format('d/m/Y H:i'),
            'payment_failures' => $this->payment_failures
        ],
        'calculated' => [
            'days_active' => $this->getUptimeDays(),
            'days_until_expiry' => $this->days_until_expiry,
            'trial_days_left' => $this->trial_days_left,
            'avg_daily_requests' => $this->getAverageDailyRequests(),
            'is_active' => $this->isActive(),
            'is_expired' => $this->isExpired(),
            'can_access' => $this->canAccess()
        ]
    ];
}

 /**
     * Armazenar metadados da subscrição
     */
    public function setMetaData($key, $value)
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->update(['metadata' => $metadata]);
    }

    /**
     * Recuperar metadados da subscrição
     */
    public function getMetaData($key, $default = null)
    {
        $metadata = $this->metadata ?? [];
        return $metadata[$key] ?? $default;
    }

    /**
     * Verificar se tem método de pagamento válido
     */
    public function hasValidPaymentMethod()
    {
        // Implementar lógica para verificar se o cliente tem método de pagamento
        return $this->client && $this->client->payment_method_id;
    }
}
