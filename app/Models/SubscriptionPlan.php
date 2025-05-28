<?php
// app/Models/SubscriptionPlan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'setup_fee',
        'billing_cycle',
        'billing_cycle_days',
        'features',
        'is_active',
        'is_featured',
        'max_domains',
        'max_storage_gb',
        'max_bandwidth_gb',
        'color_theme',
        'trial_days',
        'sort_order'
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'setup_fee' => 'decimal:2',
        'max_domains' => 'integer',
        'max_storage_gb' => 'integer',
        'max_bandwidth_gb' => 'integer',
        'trial_days' => 'integer',
        'sort_order' => 'integer'
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function getFormattedPriceAttribute()
    {
        return 'MT ' . number_format($this->price, 2);
    }

    public function getFormattedSetupFeeAttribute()
    {
        return 'MT ' . number_format($this->setup_fee, 2);
    }

    public function getBillingCycleLabelAttribute()
    {
        $labels = [
            'daily' => 'Diário',
            'weekly' => 'Semanal',
            'monthly' => 'Mensal',
            'quarterly' => 'Trimestral',
            'yearly' => 'Anual',
            'lifetime' => 'Vitalício'
        ];

        return $labels[$this->billing_cycle] ?? $this->billing_cycle;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('price', 'asc');
    }

    // Método para verificar se o plano tem trial
    public function hasTrialAttribute()
    {
        return $this->trial_days > 0;
    }

    // Método para calcular o próximo vencimento baseado no ciclo
    public function getNextBillingDate($startDate = null)
    {
        $startDate = $startDate ?? now();

        return match($this->billing_cycle) {
            'daily' => $startDate->copy()->addDays($this->billing_cycle_days),
            'weekly' => $startDate->copy()->addWeeks($this->billing_cycle_days / 7),
            'monthly' => $startDate->copy()->addMonths($this->billing_cycle_days / 30),
            'quarterly' => $startDate->copy()->addMonths($this->billing_cycle_days / 90),
            'yearly' => $startDate->copy()->addYears($this->billing_cycle_days / 365),
            'lifetime' => null, // Vitalício não tem próxima cobrança
            default => $startDate->copy()->addDays($this->billing_cycle_days)
        };
    }
}