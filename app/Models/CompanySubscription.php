<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanySubscription extends Model
{
        use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'plan_id',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'status',
        'amount',
        'currency',
        'billing_cycle',
        'last_payment_at',
        'next_payment_due',
        'payment_method',
        'payment_reference',
        'previous_subscription_id',
        'is_upgrade',
        'is_downgrade',
        'canceled_at',
        'canceled_reason',
        'canceled_by',
        'cancel_at_period_end',
        'suspended_at',
        'suspension_reason',
        'suspension_details',
        'suspension_message',
        'suspended_by',
        'reactivated_at',
        'reactivated_by',
        'can_appeal',
        'suspension_count',
        'auto_renew',
        'renewal_count',
        'plan_limits',
        'plan_features',
        'coupon_code',
        'discount_amount',
        'discount_percentage',
        'notes',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'last_payment_at' => 'datetime',
        'next_payment_due' => 'datetime',
        'canceled_at' => 'datetime',
        'suspended_at' => 'datetime',
        'reactivated_at' => 'datetime',
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'auto_renew' => 'boolean',
        'is_upgrade' => 'boolean',
        'is_downgrade' => 'boolean',
        'cancel_at_period_end' => 'boolean',
        'can_appeal' => 'boolean',
        'plan_limits' => 'array',
        'plan_features' => 'array',
        'metadata' => 'array',
        'renewal_count' => 'integer',
        'suspension_count' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function previousSubscription(): BelongsTo
    {
        return $this->belongsTo(CompanySubscription::class, 'previous_subscription_id');
    }

    public function canceledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'canceled_by');
    }

    public function suspendedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'suspended_by');
    }

    public function reactivatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reactivated_by');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTrialing($query)
    {
        return $query->where('status', 'trialing');
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', 'canceled');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeNeedingRenewal($query)
    {
        return $query->where('auto_renew', true)
            ->whereIn('status', ['active', 'trialing'])
            ->where('ends_at', '<=', now()->addDays(7));
    }

    public function scopePastDue($query)
    {
        return $query->where('status', 'past_due')
            ->orWhere(function($q) {
                $q->where('next_payment_due', '<', now())
                  ->whereIn('status', ['active', 'grace_period']);
            });
    }

    // ==================== STATUS CHECKERS ====================

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at->isFuture();
    }

    public function isTrialing(): bool
    {
        return $this->status === 'trialing' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || $this->ends_at->isPast();
    }

    public function isPastDue(): bool
    {
        return $this->status === 'past_due' || 
               ($this->next_payment_due && $this->next_payment_due->isPast());
    }

    public function canAccess(): bool
    {
        // Pode acessar se estiver ativo, em trial, ou cancelado mas ainda no período pago
        return in_array($this->status, ['active', 'trialing', 'canceled']) && 
               $this->ends_at->isFuture();
    }

    public function isBlocked(): bool
    {
        return in_array($this->status, ['suspended', 'expired']);
    }

    // ==================== DATE HELPERS ====================

    public function daysUntilExpiration(): int
    {
        return $this->ends_at->diffInDays(now(), false);
    }

    public function daysUntilTrialEnd(): ?int
    {
        return $this->trial_ends_at ? $this->trial_ends_at->diffInDays(now(), false) : null;
    }

    public function isExpiringIn(int $days): bool
    {
        // dd($this->ends_at->isFuture(), $this->ends_at->diffInDays(now()), now()->diffInDays($this->ends_at));
        return $this->ends_at->isFuture() && now()->diffInDays($this->ends_at) <=$days;
            //    $this->ends_at->diffInDays(now()) <= $days;
    }

    public function isTrialExpiringIn(int $days): bool
    {
        return $this->isTrialing() &&  now()->diffInDays($this->ends_at) <= $days;
            //    $this->trial_ends_at->diffInDays(now()) <= $days;
    }

    // ==================== CANCELAMENTO ====================

    public function cancel(string $reason = null, bool $immediate = false): bool
    {
        $this->canceled_at = now();
        $this->canceled_reason = $reason;
        $this->canceled_by = auth()->id();
        
        if ($immediate) {
            $this->status = 'canceled';
            $this->ends_at = now();
        } else {
            $this->cancel_at_period_end = true;
            // Status só muda quando expirar
        }
        
        return $this->save();
    }

    public function undoCancel(): bool
    {
        if (!$this->isCanceled()) {
            return false;
        }

        $this->canceled_at = null;
        $this->canceled_reason = null;
        $this->canceled_by = null;
        $this->cancel_at_period_end = false;
        $this->status = 'active';
        
        return $this->save();
    }

    // ==================== SUSPENSÃO ====================

    public function suspend(
        string $reason, 
        string $message = null, 
        string $details = null,
        bool $canAppeal = true
    ): bool {
        $this->status = 'suspended';
        $this->suspended_at = now();
        $this->suspension_reason = $reason;
        $this->suspension_message = $message ?? $this->getDefaultSuspensionMessage($reason);
        $this->suspension_details = $details;
        $this->suspended_by = auth()->id();
        $this->can_appeal = $canAppeal;
        $this->suspension_count++;
        
        return $this->save();
    }

    public function reactivate(string $notes = null): bool
    {
        if (!$this->isSuspended()) {
            return false;
        }

        $this->status = 'active';
        $this->reactivated_at = now();
        $this->reactivated_by = auth()->id();
        
        if ($notes) {
            $this->notes = ($this->notes ? $this->notes . "\n\n" : '') . 
                          "[Reativação] " . $notes;
        }
        
        return $this->save();
    }

    private function getDefaultSuspensionMessage(string $reason): string
    {
        return match($reason) {
            'payment_failed' => 'Sua conta foi suspensa devido a pagamento pendente. Regularize sua situação para recuperar o acesso.',
            'terms_violation' => 'Sua conta foi suspensa por violação dos Termos de Uso. Entre em contato com o suporte.',
            'fraud_suspected' => 'Sua conta foi suspensa por segurança. Nossa equipe está analisando atividades suspeitas.',
            'excessive_usage' => 'Sua conta excedeu os limites do plano e foi suspensa. Faça upgrade ou entre em contato.',
            'chargeback' => 'Sua conta foi suspensa devido a um chargeback. Entre em contato com o financeiro.',
            'abuse_detected' => 'Sua conta foi suspensa por uso indevido do sistema. Contate o suporte para esclarecimentos.',
            default => 'Sua conta foi suspensa. Entre em contato com o suporte para mais informações.',
        };
    }

    // ==================== RENOVAÇÃO ====================

    public function renew(int $months = 1): bool
    {
        if (!$this->auto_renew) {
            return false;
        }

        $this->starts_at = $this->ends_at;
        $this->ends_at = $this->ends_at->addMonths($months);
        $this->next_payment_due = $this->ends_at;
        $this->renewal_count++;
        $this->status = 'active';
        
        return $this->save();
    }

    public function enableAutoRenew(): bool
    {
        $this->auto_renew = true;
        return $this->save();
    }

    public function disableAutoRenew(): bool
    {
        $this->auto_renew = false;
        return $this->save();
    }

    // ==================== UPGRADE/DOWNGRADE ====================

    public function upgradeTo(Plan $newPlan): self
    {
        $newSubscription = $this->replicate();
        $newSubscription->plan_id = $newPlan->id;
        $newSubscription->previous_subscription_id = $this->id;
        $newSubscription->is_upgrade = true;
        $newSubscription->is_downgrade = false;
        $newSubscription->amount = $newPlan->price;
        $newSubscription->starts_at = now();
        $newSubscription->ends_at = now()->addMonth();
        $newSubscription->save();

        // Cancelar assinatura atual
        $this->status = 'canceled';
        $this->save();

        return $newSubscription;
    }

    public function downgradeTo(Plan $newPlan): self
    {
        $newSubscription = $this->replicate();
        $newSubscription->plan_id = $newPlan->id;
        $newSubscription->previous_subscription_id = $this->id;
        $newSubscription->is_upgrade = false;
        $newSubscription->is_downgrade = true;
        $newSubscription->amount = $newPlan->price;
        $newSubscription->starts_at = $this->ends_at; // Começa quando a atual acabar
        $newSubscription->ends_at = $this->ends_at->copy()->addMonth();
        $newSubscription->status = 'pending'; // Aguardando início
        $newSubscription->save();

        return $newSubscription;
    }

    // ==================== PAYMENT ====================

    public function recordPayment(
        float $amount,
        string $method,
        string $reference = null
    ): bool {
        $this->last_payment_at = now();
        $this->payment_method = $method;
        $this->payment_reference = $reference;
        
        if ($this->isPastDue() || $this->status === 'grace_period') {
            $this->status = 'active';
        }
        
        return $this->save();
    }

    public function markAsPastDue(): bool
    {
        $this->status = 'past_due';
        return $this->save();
    }

    // ==================== DISCOUNT ====================

    public function applyDiscount(string $coupon, float $amount = null, float $percentage = null): bool
    {
        $this->coupon_code = $coupon;
        $this->discount_amount = $amount;
        $this->discount_percentage = $percentage;
        
        return $this->save();
    }

    public function getFinalAmount(): float
    {
        $amount = $this->amount;
        
        if ($this->discount_percentage) {
            $amount -= ($amount * $this->discount_percentage / 100);
        }
        
        if ($this->discount_amount) {
            $amount -= $this->discount_amount;
        }
        
        return max(0, $amount);
    }

    // ==================== HELPERS ====================

    public function savePlanSnapshot(): void
    {
        if ($this->plan) {
            $this->plan_limits = [
                'max_users' => $this->plan->max_users,
                'max_invoices_per_month' => $this->plan->max_invoices_per_month,
                'max_clients' => $this->plan->max_clients,
                'max_products' => $this->plan->max_products,
                'max_storage_mb' => $this->plan->max_storage_mb,
            ];
            
            $this->plan_features = $this->plan->features;
        }
    }

    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            'active' => 'green',
            'trialing' => 'blue',
            'canceled' => 'gray',
            'suspended' => 'red',
            'expired' => 'orange',
            'past_due' => 'yellow',
            'grace_period' => 'yellow',
            default => 'gray',
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'active' => 'Ativa',
            'trialing' => 'Em Teste',
            'canceled' => 'Cancelada',
            'suspended' => 'Suspensa',
            'expired' => 'Expirada',
            'past_due' => 'Pagamento Atrasado',
            'grace_period' => 'Período de Graça',
            default => 'Desconhecido',
        };
    }
}
