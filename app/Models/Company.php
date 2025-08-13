<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
        'metadata',
    ];

    protected $casts = [
        'bank_accounts' => 'array',
        'theme_settings' => 'array',
        'feature_flags' => 'array',
        'settings' => 'array',
        'metadata' => 'array',
        'trial_ends_at' => 'datetime',
        'last_payment_at' => 'datetime',
        'next_payment_due' => 'datetime',
        'last_activity_at' => 'datetime',
        'default_tax_rate' => 'decimal:2',
        'monthly_fee' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'custom_domain_enabled' => 'boolean',
        'api_access_enabled' => 'boolean',
    ];

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
        });
    }

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'company_id');
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOnTrial($query)
    {
        return $query->where('status', 'trial');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeTrialExpired($query)
    {
        return $query->where('status', 'trial')
                    ->where('trial_ends_at', '<', now());
    }

    public function scopePaymentDue($query)
    {
        return $query->where('next_payment_due', '<=', now())
                    ->whereIn('status', ['active', 'trial']);
    }

    // Accessors & Mutators
    public function getIsTrialAttribute(): bool
    {
        return $this->status === 'trial';
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getTrialDaysLeftAttribute(): int
    {
        if (!$this->is_trial || !$this->trial_ends_at) {
            return 0;
        }

        return max(0, $this->trial_ends_at->diffInDays(now()));
    }

    public function getUsagePercentageAttribute(): array
    {
        return [
            'users' => $this->max_users > 0 ? ($this->current_users_count / $this->max_users) * 100 : 0,
            'invoices' => $this->max_invoices_per_month > 0 ? ($this->current_month_invoices / $this->max_invoices_per_month) * 100 : 0,
            'clients' => $this->max_clients > 0 ? ($this->total_clients / $this->max_clients) * 100 : 0,
        ];
    }

    public function getSubdomainUrlAttribute(): string
    {
        $domain = config('app.domain', 'localhost');
        return "https://{$this->slug}.{$domain}";
    }

    public function getUrlAttribute(): string
    {
        if ($this->domain && $this->custom_domain_enabled) {
            return "https://{$this->domain}";
        }

        return $this->subdomain_url;
    }

    // Methods
    public function canCreateUser(): bool
    {
        return $this->current_users_count < $this->max_users;
    }

    public function canCreateInvoice(): bool
    {
        return $this->current_month_invoices < $this->max_invoices_per_month;
    }

    public function canCreateClient(): bool
    {
        return $this->total_clients < $this->max_clients;
    }

    public function hasFeature(string $feature): bool
    {
        $features = $this->feature_flags ?? [];
        return isset($features[$feature]) && $features[$feature] === true;
    }

    public function updateUsageStats(): void
    {
        $this->update([
            'current_users_count' => $this->users()->count(),
            'total_clients' => $this->clients()->count(),
            'total_invoices' => $this->invoices()->count(),
            'current_month_invoices' => $this->invoices()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'total_revenue' => $this->invoices()
                ->where('status', 'paid')
                ->sum('total_amount'),
            'last_activity_at' => now(),
        ]);
    }

    public function suspend(string $reason = null): void
    {
        $this->update([
            'status' => 'suspended',
            'metadata' => array_merge($this->metadata ?? [], [
                'suspension_reason' => $reason,
                'suspended_at' => now()->toISOString(),
            ])
        ]);
    }

    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'metadata' => array_merge($this->metadata ?? [], [
                'activated_at' => now()->toISOString(),
            ])
        ]);
    }

    public function extendTrial(int $days): void
    {
        $currentEndDate = $this->trial_ends_at ?? now();
        $this->update([
            'trial_ends_at' => $currentEndDate->addDays($days)
        ]);
    }
}
