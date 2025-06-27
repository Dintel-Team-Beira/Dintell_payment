<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'client_id',
        'quote_id',
        'invoice_date',
        'due_date',
        'status',
        'subtotal',
        'tax_amount',
        'total',
        'paid_amount',
        'notes',
        'terms_conditions',
        'payment_terms_days'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'paid_at' => 'datetime'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function isOverdue()
    {
        return $this->status !== 'paid' && Carbon::now()->isAfter($this->due_date);
    }

    public function getRemainingAmountAttribute()
    {
        return $this->total - $this->paid_amount;
    }

    public function markAsPaid($amount = null)
    {
        $amount = $amount ?? $this->total;

        $this->update([
            'paid_amount' => $this->paid_amount + $amount,
            'status' => ($this->paid_amount + $amount) >= $this->total ? 'paid' : 'sent',
            'paid_at' => ($this->paid_amount + $amount) >= $this->total ? Carbon::now() : null
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                $settings = BillingSetting::getSettings();
                $invoice->invoice_number = $settings->getNextInvoiceNumber();
            }
        });

        static::updating(function ($invoice) {
            // Atualizar status automaticamente baseado na data de vencimento
            if ($invoice->isDirty('due_date') || $invoice->isDirty('status')) {
                if ($invoice->status === 'sent' && $invoice->isOverdue()) {
                    $invoice->status = 'overdue';
                }
            }
        });
    }

    // Invoice.php
}