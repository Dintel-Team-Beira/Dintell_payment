<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price',
        'tax_rate',
        'total_price',
        'name',
        'type',
        'item_id',
        'category',
        'unit',
        'complexity_level',
        'estimated_hours'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'total_price' => 'decimal:2',
        'estimated_hours' => 'decimal:2'
    ];

    // Relacionamentos
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Accessors
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    public function getTaxAmountAttribute()
    {
        return $this->subtotal * ($this->tax_rate / 100);
    }

    public function getTotalAttribute()
    {
        return $this->subtotal + $this->tax_amount;
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

    // Business Logic Methods
    public function calculateTotal()
    {
        $subtotal = $this->quantity * $this->unit_price;
        $tax = $subtotal * ($this->tax_rate / 100);
        return $subtotal + $tax;
    }

    public function updateTotal()
    {
        $this->update(['total_price' => $this->calculateTotal()]);
    }

    // Scope para filtros
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeWithTax($query)
    {
        return $query->where('tax_rate', '>', 0);
    }

    public function scopeWithoutTax($query)
    {
        return $query->where('tax_rate', 0);
    }

    // Event Listeners
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            // Calcular total automaticamente se não foi definido
            if (!$item->total_price) {
                $item->total_price = $item->calculateTotal();
            }
        });
    }
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }
}
