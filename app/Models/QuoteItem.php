<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quote_id',
        'type', // 'product' ou 'service'
        'item_id', // ID do produto ou serviço
        'name',
        'description',
        'quantity',
        'unit_price',
        'tax_rate',
        'category',
        'unit',
        'complexity_level',
        'estimated_hours',
        'company_id' // ID da empresa para escopo
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'estimated_hours' => 'decimal:2'
    ];

    // Relacionamentos
    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'item_id')->where('type', 'product');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'item_id')->where('type', 'service');
    }

    // Accessor para o item relacionado (produto ou serviço)
    public function getRelatedItemAttribute()
    {
        if ($this->type === 'product') {
            return $this->product;
        } elseif ($this->type === 'service') {
            return $this->service;
        }

        return null;
    }

    // Calculators
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

    // Formatters
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

    public function getFormattedUnitPriceAttribute()
    {
        return number_format($this->unit_price, 2, ',', '.') . ' MT';
    }

    // Type indicators
    public function isProduct()
    {
        return $this->type === 'product';
    }

    public function isService()
    {
        return $this->type === 'service';
    }

    // Scope for filtering by type
    public function scopeProducts($query)
    {
        return $query->where('type', 'product');
    }

    public function scopeServices($query)
    {
        return $query->where('type', 'service');
    }

    // Get type badge color
    public function getTypeBadgeColorAttribute()
    {
        return $this->type === 'product' ? 'blue' : 'green';
    }

    // Get category badge color
    public function getCategoryBadgeColorAttribute()
    {
        $colors = [
            'software' => 'purple',
            'hardware' => 'orange',
            'consultoria' => 'teal',
            'desenvolvimento' => 'blue',
            'design' => 'pink',
            'suporte' => 'green',
            'manutencao' => 'yellow'
        ];

        return $colors[$this->category] ?? 'gray';
    }

    // Get complexity badge color
    public function getComplexityBadgeColorAttribute()
    {
        $colors = [
            'baixa' => 'green',
            'media' => 'yellow',
            'alta' => 'red'
        ];

        return $colors[$this->complexity_level] ?? 'gray';
    }
     protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }
}
