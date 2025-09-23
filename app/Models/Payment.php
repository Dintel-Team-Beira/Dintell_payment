<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'plan_id',
        'amount',
        'payment_method',
        'status',
        'type',
        'reference',
        'transaction_reference',
        'payment_proof',
        'notes',
        'due_date',
        'submitted_at',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';

    // Payment method constants
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_MOBILE_MONEY = 'mobile_money';
    const METHOD_CASH = 'cash';
    const METHOD_CARD = 'card';

    // Type constants
    const TYPE_RENEWAL = 'renewal';
    const TYPE_UPGRADE = 'upgrade';
    const TYPE_NEW = 'new';

    /**
     * Relacionamentos
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Verificar se está pendente
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Verificar se foi submetido
     */
    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    /**
     * Verificar se foi aprovado
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Verificar se foi rejeitado
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Verificar se está expirado
     */
    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED ||
               ($this->due_date && $this->due_date->isPast() && $this->isPending());
    }

    /**
     * Verificar se pode ser aprovado
     */
    public function canBeApproved(): bool
    {
        return $this->isSubmitted();
    }

    /**
     * Verificar se pode ser rejeitado
     */
    public function canBeRejected(): bool
    {
        return $this->isSubmitted();
    }

    /**
     * Obter dias restantes para vencimento
     */
    public function getDaysUntilDue(): ?int
    {
        if (!$this->due_date) {
            return null;
        }

        return $this->due_date->diffInDays(now(), false);
    }

    /**
     * Obter nome legível do método de pagamento
     */
    public function getPaymentMethodName(): string
    {
        return match($this->payment_method) {
            self::METHOD_BANK_TRANSFER => 'Transferência Bancária',
            self::METHOD_MOBILE_MONEY => 'Mobile Money',
            self::METHOD_CASH => 'Dinheiro',
            self::METHOD_CARD => 'Cartão',
            default => 'Não especificado'
        };
    }

    /**
     * Obter nome legível do status
     */
    public function getStatusName(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_SUBMITTED => 'Submetido',
            self::STATUS_APPROVED => 'Aprovado',
            self::STATUS_REJECTED => 'Rejeitado',
            self::STATUS_EXPIRED => 'Expirado',
            default => 'Desconhecido'
        };
    }

    /**
     * Obter nome legível do tipo
     */
    public function getTypeName(): string
    {
        return match($this->type) {
            self::TYPE_RENEWAL => 'Renovação',
            self::TYPE_UPGRADE => 'Upgrade',
            self::TYPE_NEW => 'Nova Subscrição',
            default => 'Pagamento'
        };
    }

    /**
     * Obter cor do status para UI
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_SUBMITTED => 'blue',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_EXPIRED => 'gray',
            default => 'gray'
        };
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->where('status', self::STATUS_EXPIRED)
              ->orWhere(function($subQ) {
                  $subQ->where('status', self::STATUS_PENDING)
                       ->where('due_date', '<', now());
              });
        });
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_PENDING)
                     ->where('due_date', '<', now());
    }

    public function scopeAwaitingApproval($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('created_at', now()->year);
    }

    /**
     * Mutators
     */
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = is_string($value) ?
            (float) str_replace([',', ' '], ['', ''], $value) : $value;
    }

    /**
     * Accessors
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2, ',', '.') . ' MT';
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-gerar referência se não fornecida
        static::creating(function ($payment) {
            if (!$payment->reference) {
                $payment->reference = 'PAY' . now()->format('Ymd') . str_pad(
                    self::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });

        // Marcar como expirado automaticamente
        static::updating(function ($payment) {
            if ($payment->isPending() && $payment->due_date && $payment->due_date->isPast()) {
                $payment->status = self::STATUS_EXPIRED;
            }
        });
    }
}
