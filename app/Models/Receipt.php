<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'invoice_id',
        'client_id',
        'company_id',
        'amount_paid',
        'payment_method',
        'payment_date',
        'transaction_reference',
        'notes',
        'status',
        'issued_by', // ID do usuário que registrou o pagamento
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    // Constantes para métodos de pagamento
    const PAYMENT_CASH = 'cash';
    const PAYMENT_BANK_TRANSFER = 'bank_transfer';
    const PAYMENT_CHECK = 'check';
    const PAYMENT_CREDIT_CARD = 'credit_card';
    const PAYMENT_MOBILE_MONEY = 'mobile_money'; // M-Pesa, e-Mola, etc.
    const PAYMENT_OTHER = 'other';

    // Constantes para status
    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELLED = 'cancelled';

    public static function getPaymentMethods()
    {
        return [
            self::PAYMENT_CASH => 'Dinheiro',
            self::PAYMENT_BANK_TRANSFER => 'Transferência Bancária',
            self::PAYMENT_CHECK => 'Cheque',
            self::PAYMENT_CREDIT_CARD => 'Cartão de Crédito',
            self::PAYMENT_MOBILE_MONEY => 'Dinheiro Móvel',
            self::PAYMENT_OTHER => 'Outro'
        ];
    }

    // Relacionamentos
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    // Scopes
    public function scopeForCurrentCompany($query)
    {
        $company = session('current_company');
        if ($company) {
            return $query->where('company_id', $company->id);
        }
        return $query;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('payment_date', Carbon::now()->month)
                    ->whereYear('payment_date', Carbon::now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('payment_date', Carbon::now()->year);
    }

    // Accessors
    public function getPaymentMethodLabelAttribute()
    {
        $methods = self::getPaymentMethods();
        return $methods[$this->payment_method] ?? 'Não especificado';
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount_paid, 2, ',', '.') . ' MT';
    }

    public function getFormattedPaymentDateAttribute()
    {
        return $this->payment_date->format('d/m/Y H:i');
    }

    public function getShortPaymentDateAttribute()
    {
        return $this->payment_date->format('d/m/Y');
    }

    // Métodos de negócio
    public function cancel($reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'notes' => $this->notes . "\n\nRecibo cancelado: " . $reason
        ]);

        // Reverter o pagamento na fatura se necessário
        $invoice = $this->invoice;
        if ($invoice) {
            $newPaidAmount = max(0, $invoice->paid_amount - $this->amount_paid);
            $newStatus = $newPaidAmount >= $invoice->total ? 'paid' : 'sent';
            
            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'status' => $newStatus,
                'paid_at' => $newPaidAmount >= $invoice->total ? $invoice->paid_at : null
            ]);
        }
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($receipt) {
            // Definir company_id automaticamente
            $company = session('current_company');
            if ($company && !$receipt->company_id) {
                $receipt->company_id = $company->id;
            }

            // Gerar número do recibo se não existir
            if (!$receipt->receipt_number) {
                $settings = BillingSetting::getSettings();
                $receipt->receipt_number = $settings->getNextReceiptNumber();
            }

            // Definir usuário que registrou
            if (!$receipt->issued_by && auth()->check()) {
                $receipt->issued_by = auth()->id();
            }

            // Status padrão
            if (!$receipt->status) {
                $receipt->status = self::STATUS_ACTIVE;
            }

            // Data de pagamento padrão
            if (!$receipt->payment_date) {
                $receipt->payment_date = now();
            }
        });
    }

    protected static function booted(): void
    {
        // Aplicar scope global para filtrar por empresa
        static::addGlobalScope('company', function (Builder $builder) {
            $company = Config::get('app.current_company');

            if ($company) {
                $builder->where('company_id', $company->id);
            }
        });

        // Auto-definir company_id ao criar
        static::creating(function (Receipt $receipt) {
            $company = Config::get('app.current_company');

            if ($company && !$receipt->company_id) {
                $receipt->company_id = $company->id;
            }
        });
    }

    // Método para resolver route model binding com contexto da empresa
    public function resolveRouteBinding($value, $field = null)
    {
        $company = Config::get('app.current_company');

        if ($company) {
            return $this->where('company_id', $company->id)
                       ->where($field ?? $this->getRouteKeyName(), $value)
                       ->first();
        }

        return parent::resolveRouteBinding($value, $field);
    }
}
