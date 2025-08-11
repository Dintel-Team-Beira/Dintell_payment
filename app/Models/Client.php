<?php
// app/Models/Client.php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'company', 'address',
        'tax_number', 'status', 'contact_preferences', 'last_login','company_id',
    ];

    protected $casts = [
        'contact_preferences' => 'array',
        'last_login' => 'datetime'
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }

    public function activeSubscriptions()
    {
        return $this->subscriptions()->where('status', 'active');
    }

    public function totalRevenue()
    {
        return $this->subscriptions()->sum('total_revenue');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

     /**
     * Verificar se tem método de pagamento válido
     */
    public function hasValidPaymentMethod()
    {
        // Implementar lógica específica do seu sistema
        return !empty($this->payment_method_id) || !empty($this->card_token);
    }


      // Relationships
      public function invoices()
      {
          return $this->hasMany(Invoice::class);
      }

      public function quotes()
      {
          return $this->hasMany(Quote::class);
      }

      // Scopes
    //   public function scopeActive($query)
    //   {
    //       return $query->where('is_active', true);
    //   }

      public function scopeWithOverdueInvoices($query)
      {
          return $query->whereHas('invoices', function($q) {
              $q->where('status', 'overdue');
          });
      }

      // Accessors
      public function getOutstandingBalanceAttribute(): float
      {
          return $this->invoices()->whereIn('status', ['sent', 'overdue'])->sum('total');
      }

      public function getOverdueBalanceAttribute(): float
      {
          return $this->invoices()->where('status', 'overdue')->sum('total');
      }

      public function getFormattedTotalInvoicedAttribute(): string
      {
          return number_format($this->total_invoiced, 2) . ' MZN';
      }

      public function getFormattedTotalPaidAttribute(): string
      {
          return number_format($this->total_paid, 2) . ' MZN';
      }

      public function getCollectionRateAttribute(): float
      {
          if ($this->total_invoiced == 0) return 0;
          return round(($this->total_paid / $this->total_invoiced) * 100, 2);
      }

      // Methods
      public function updateTotals()
      {
          $this->update([
              'total_invoiced' => $this->invoices()->sum('total'),
              'total_paid' => $this->invoices()->where('status', 'paid')->sum('total'),
              'last_invoice_date' => $this->invoices()->latest('invoice_date')->value('invoice_date'),
              'last_payment_date' => $this->invoices()->where('status', 'paid')->latest('paid_at')->value('paid_at')
          ]);
      }

      public function canReceiveNewInvoice(): bool
      {
          if ($this->credit_limit <= 0) return true;
          return $this->outstanding_balance < $this->credit_limit;
      }

      public function getPaymentHistory($limit = 10)
      {
          return $this->invoices()
              ->where('status', 'paid')
              ->orderBy('paid_at', 'desc')
              ->limit($limit)
              ->get();
      }


         // Relacionamento com empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

        public function scopeForCurrentCompany($query)
    {
        $company = session('current_company');
        if ($company) {
            return $query->where('company_id', $company->id);
        }
        return $query;
    }
   // Boot method para definir company_id automaticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            $company = session('current_company');
            if ($company && !$client->company_id) {
                $client->company_id = $company->id;
            }
        });
    }
    protected static function booted()
    {
        // static::addGlobalScope(new CompanyScope);
    }
}
