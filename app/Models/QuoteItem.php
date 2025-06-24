<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'description',
        'quantity',
        'unit_price',
        'tax_rate',
        'total'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $subtotal = $item->quantity * $item->unit_price;
            $tax = $subtotal * ($item->tax_rate / 100);
            $item->total = $subtotal + $tax;
        });
    }
}
