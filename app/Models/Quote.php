<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Quote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quote_number',
        'client_id',
        'quote_date',
        'valid_until',
        'subtotal',
        'tax_amount',
        'total',
        'status',
        'notes',
        'terms_conditions',
        'sent_at',
        'status_updated_at',
        'converted_to_invoice_at',
        'invoice_id'
    ];

    protected $casts = [
        'quote_date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'sent_at' => 'datetime',
        'status_updated_at' => 'datetime',
        'converted_to_invoice_at' => 'datetime'
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_DRAFT => 'Rascunho',
            self::STATUS_SENT => 'Enviada',
            self::STATUS_ACCEPTED => 'Aceita',
            self::STATUS_REJECTED => 'Rejeitada',
            self::STATUS_EXPIRED => 'Expirada'
        ];
    }

    // Relacionamentos
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function products()
    {
        return $this->items()->where('type', 'product');
    }

    public function services()
    {
        return $this->items()->where('type', 'service');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_DRAFT, self::STATUS_SENT, self::STATUS_ACCEPTED]);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED)
                    ->orWhere(function($q) {
                        $q->where('valid_until', '<', Carbon::today())
                          ->whereNotIn('status', [self::STATUS_ACCEPTED, self::STATUS_REJECTED]);
                    });
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Accessors
    public function getIsExpiredAttribute()
    {
        return $this->status !== self::STATUS_ACCEPTED &&
               $this->status !== self::STATUS_REJECTED &&
               Carbon::parse($this->valid_until)->isPast();
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    public function getStatusBadgeColorAttribute()
    {
        $colors = [
            self::STATUS_DRAFT => 'gray',
            self::STATUS_SENT => 'blue',
            self::STATUS_ACCEPTED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_EXPIRED => 'orange'
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getFormattedSubtotalAttribute()
    {
        return number_format($this->subtotal, 2, ',', '.') . ' MT';
    }

    public function getFormattedTaxAmountAttribute()
    {
        return number_format($this->tax_amount, 2, ',', '.') . ' MT';
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->total, 2, ',', '.') . ' MT';
    }

    public function getDaysUntilExpirationAttribute()
    {
        if ($this->status === self::STATUS_ACCEPTED || $this->status === self::STATUS_REJECTED) {
            return null;
        }

        return Carbon::today()->diffInDays(Carbon::parse($this->valid_until), false);
    }

    public function getExpirationStatusAttribute()
    {
        $days = $this->days_until_expiration;

        if ($days === null) {
            return 'completed';
        } elseif ($days < 0) {
            return 'expired';
        } elseif ($days <= 3) {
            return 'expiring_soon';
        } else {
            return 'active';
        }
    }

    // Business Logic Methods
    public function canConvertToInvoice()
    {
        return $this->status === self::STATUS_ACCEPTED &&
               !$this->converted_to_invoice_at &&
               !$this->is_expired;
    }

    public function canEdit()
    {
        return !$this->converted_to_invoice_at &&
               $this->status !== self::STATUS_ACCEPTED;
    }

    public function canDelete()
    {
        return !$this->converted_to_invoice_at;
    }

    public function canSend()
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SENT]) &&
               !$this->is_expired;
    }

    public function markAsExpired()
    {
        if ($this->is_expired && $this->status !== self::STATUS_EXPIRED) {
            $this->update([
                'status' => self::STATUS_EXPIRED,
                'status_updated_at' => now()
            ]);
        }
    }

    public function convertToInvoice()
    {
        if (!$this->canConvertToInvoice()) {
            throw new \Exception('Esta cotação não pode ser convertida em fatura.');
        }

        $invoiceData = [
            'client_id' => $this->client_id,
            'quote_id' => $this->id,
            'invoice_date' => Carbon::today(),
            'due_date' => Carbon::today()->addDays(30),
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'total' => $this->total,
            'status' => 'pending',
            'notes' => $this->notes
        ];

        $invoice = Invoice::create($invoiceData);

        // Copiar itens da cotação para a fatura
        foreach ($this->items as $quoteItem) {
            $invoice->items()->create([
                'type' => $quoteItem->type,
                'item_id' => $quoteItem->item_id,
                'name' => $quoteItem->name,
                'description' => $quoteItem->description,
                'quantity' => $quoteItem->quantity,
                'unit_price' => $quoteItem->unit_price,
                'tax_rate' => $quoteItem->tax_rate,
                'category' => $quoteItem->category,
                'unit' => $quoteItem->unit,
                'complexity_level' => $quoteItem->complexity_level,
                'estimated_hours' => $quoteItem->estimated_hours
            ]);
        }

        // Marcar cotação como convertida
        $this->update([
            'converted_to_invoice_at' => now(),
            'invoice_id' => $invoice->id
        ]);

        return $invoice;
    }

    public function calculateTotals()
    {
        $subtotal = $this->items()->sum(\DB::raw('quantity * unit_price'));
        $taxAmount = $this->items()->sum(\DB::raw('(quantity * unit_price) * (tax_rate / 100)'));

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $subtotal + $taxAmount
        ]);
    }

    // Statistics Methods
    public static function getConversionRate()
    {
        $total = self::count();
        $converted = self::whereNotNull('converted_to_invoice_at')->count();

        return $total > 0 ? round(($converted / $total) * 100, 2) : 0;
    }

    public static function getMonthlyStats()
    {
        return [
            'total' => self::thisMonth()->count(),
            'value' => self::thisMonth()->sum('total'),
            'accepted' => self::thisMonth()->accepted()->count(),
            'pending' => self::thisMonth()->pending()->count()
        ];
    }

    // Event Listeners
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quote) {
            if (!$quote->quote_number) {
                $quote->quote_number = self::generateQuoteNumber();
            }
        });

        static::saving(function ($quote) {
            // Auto-expire check
            if ($quote->is_expired && $quote->status !== self::STATUS_EXPIRED) {
                $quote->status = self::STATUS_EXPIRED;
                $quote->status_updated_at = now();
            }
        });
    }

    private static function generateQuoteNumber()
    {
        $settings = BillingSetting::getSettings();
        $prefix = $settings->quote_prefix ?? 'COT';
        $nextNumber = $settings->next_quote_number ?? 1;

        $settings->increment('next_quote_number');

        return $prefix . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}