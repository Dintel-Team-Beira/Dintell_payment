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
        'client_id', 'subscription_plan_id', 'domain', 'subdomain', 'api_key',
        'status', 'manual_status', 'starts_at', 'ends_at', 'trial_ends_at',
        'suspended_at', 'cancelled_at', 'suspension_reason', 'cancellation_reason',
        'suspension_page_config', 'amount_paid', 'total_revenue', 'last_payment_date',
        'next_payment_due', 'payment_failures', 'payment_method', 'payment_reference',
        'total_requests', 'monthly_requests', 'last_request_at', 'storage_used_gb',
        'bandwidth_used_gb', 'auto_renew', 'email_notifications', 'expiry_warning_days',
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

        $storagePercent = ($this->storage_used_gb / $this->plan->max_storage_gb) * 100;
        $bandwidthPercent = ($this->bandwidth_used_gb / $this->plan->max_bandwidth_gb) * 100;

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

    public function renew($paymentAmount = null, $paymentMethod = null)
    {
        $nextBilling = $this->calculateNextBillingDate();
        $amount = $paymentAmount ?? $this->plan->price;

        $this->update([
            'status' => 'active',
            'ends_at' => $nextBilling,
            'next_payment_due' => $nextBilling,
            'last_payment_date' => now(),
            'amount_paid' => $amount,
            'total_revenue' => $this->total_revenue + $amount,
            'payment_method' => $paymentMethod,
            'payment_failures' => 0
        ]);

        // Enviar email de renovação
        $this->client->notify(new \App\Notifications\SubscriptionRenewedNotification($this));

        return $this;
    }

    public function calculateNextBillingDate()
    {
        $baseDate = $this->ends_at ?? now();
        return $baseDate->addDays($this->plan->billing_cycle_days);
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
        return $query->where(function($q) {
            $q->where('ends_at', '<', now())
              ->orWhere(function($q2) {
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
                    ->where(function($q) {
                        $q->whereNull('last_warning_sent')
                          ->orWhere('last_warning_sent', '<', now()->subDays(1));
                    });
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}