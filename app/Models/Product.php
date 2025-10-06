<?php

// Product.php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'price',
        'cost',
        'stock_quantity',
        'min_stock_level',
        'category',
        'unit',
        'tax_rate',
        'is_active',
        'image',
        'weight',
        'dimensions',
        'company_id', // Adicionar para multi-tenancy
    ];

    // Relacionamento com empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'weight' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $company = session('current_company');
            if ($company && ! $product->company_id) {
                $product->company_id = $company->id;
            }
            if (empty($product->code)) {
                $product->code = 'PROD'.str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    // Scope para filtrar por empresa atual
    public function scopeForCurrentCompany($query)
    {
        $company = session('current_company');
        if ($company) {
            return $query->where('company_id', $company->id);
        }

        return $query;
    }

    // Relacionamentos
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function quoteItems()
    {
        return $this->hasMany(QuoteItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_level');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2, ',', '.').' MT';
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->stock_quantity <= $this->min_stock_level) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    public function getProfitMarginAttribute()
    {
        if ($this->cost > 0) {
            return (($this->price - $this->cost) / $this->cost) * 100;
        }

        return 0;
    }

    // Methods
    public function updateStock($quantity, $operation = 'subtract')
    {
        if ($operation === 'subtract') {
            $this->decrement('stock_quantity', $quantity);
        } else {
            $this->increment('stock_quantity', $quantity);
        }
    }

    public static function getCategories()
    {
        return [
            'software' => 'Software',
            'hardware' => 'Hardware',
            'consultoria' => 'Consultoria',
            'licencas' => 'Licenças',
            'manutencao' => 'Manutenção',
            'outros' => 'Outros',
        ];
    }

    public static function getUnits()
    {
        return [
            'unidade' => 'Unidade',
            'licenca' => 'Licença',
            'horas' => 'Horas',
            'dias' => 'Dias',
            'mes' => 'Mês',
            'ano' => 'Ano',
        ];
    }

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }

    /**
     * Produto pertence a uma categoria.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
