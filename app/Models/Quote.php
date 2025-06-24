<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_number',
        'client_id',
        'quote_date',
        'valid_until',
        'status',
        'subtotal',
        'tax_amount',
        'total',
        'notes',
        'terms_conditions'
    ];

    protected $casts = [
        'quote_date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'converted_to_invoice_at' => 'datetime'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function isExpired()
    {
        return Carbon::now()->isAfter($this->valid_until);
    }

    public function canConvertToInvoice()
    {
        return $this->status === 'accepted' && !$this->converted_to_invoice_at;
    }

    public function convertToInvoice()
    {
        if (!$this->canConvertToInvoice()) {
            return false;
        }

        $settings = BillingSetting::getSettings();

        $invoice = Invoice::create([
            'invoice_number' => $settings->getNextInvoiceNumber(),
            'client_id' => $this->client_id,
            'quote_id' => $this->id,
            'invoice_date' => Carbon::now()->toDateString(),
            'due_date' => Carbon::now()->addDays(30)->toDateString(),
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'total' => $this->total,
            'notes' => $this->notes,
            'terms_conditions' => $this->terms_conditions
        ]);

        // Copiar itens da cotação para a fatura
        foreach ($this->items as $item) {
            $invoice->items()->create([
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'tax_rate' => $item->tax_rate,
                'total' => $item->total
            ]);
        }

        $this->update([
            'converted_to_invoice_at' => Carbon::now(),
            'invoice_id' => $invoice->id
        ]);

        return $invoice;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quote) {
            if (!$quote->quote_number) {
                $settings = BillingSetting::getSettings();
                $quote->quote_number = $settings->getNextQuoteNumber();
            }
        });
    }
}
