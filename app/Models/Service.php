<?php

// Service.php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'hourly_rate',
        'fixed_price',
        'category',
        'tax_rate',
        'is_active',
        'estimated_hours',
        'complexity_level',
        'requirements',
        'deliverables',
        'company_id', // Adicionar para multi-tenancy,
        'category_id'
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'fixed_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'estimated_hours' => 'decimal:2',
        'requirements' => 'array',
        'deliverables' => 'array',
    ];

    // Relacionamento com empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            $company = session('current_company');
            if ($company && ! $service->company_id) {
                $service->company_id = $company->id;
            }
            if (empty($service->code)) {
                $service->code = 'SERV'.str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
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

    // public function projects()
    // {
    //     return $this->belongsToMany(Project::class);
    // }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByComplexity($query, $level)
    {
        return $query->where('complexity_level', $level);
    }

    // Accessors
    public function getFormattedHourlyRateAttribute()
    {
        return number_format($this->hourly_rate, 2, ',', '.').' MT/hora';
    }

    public function getFormattedFixedPriceAttribute()
    {
        return number_format($this->fixed_price, 2, ',', '.').' MT';
    }

    public function getEstimatedCostAttribute()
    {
        if ($this->fixed_price > 0) {
            return $this->fixed_price;
        }

        return $this->hourly_rate * $this->estimated_hours;
    }

    public function getComplexityBadgeAttribute()
    {
        $badges = [
            'baixa' => 'success',
            'media' => 'warning',
            'alta' => 'danger',
        ];

        return $badges[$this->complexity_level] ?? 'secondary';
    }

    // Methods
    public function calculatePrice($hours = null)
    {
        if ($this->fixed_price > 0) {
            return $this->fixed_price;
        }

        $hoursToUse = $hours ?? $this->estimated_hours;

        return $this->hourly_rate * $hoursToUse;
    }

    public static function getCategories()
    {
        return [
            'desenvolvimento' => 'Desenvolvimento',
            'design' => 'Design UI/UX',
            'consultoria' => 'Consultoria',
            'manutencao' => 'Manutenção',
            'treinamento' => 'Treinamento',
            'suporte' => 'Suporte Técnico',
            'integracao' => 'Integração',
            'migracao' => 'Migração de Dados',
            'seguranca' => 'Segurança',
            'outros' => 'Outros',
        ];
    }

    public static function getComplexityLevels()
    {
        return [
            'baixa' => 'Baixa',
            'media' => 'Média',
            'alta' => 'Alta',
        ];
    }

    public static function getDefaultRequirements()
    {
        return [
            'Reunião inicial para levantamento de requisitos',
            'Acesso aos sistemas existentes (se aplicável)',
            'Aprovação do cliente em cada etapa',
            'Ambiente de desenvolvimento/teste',
        ];
    }

    public static function getDefaultDeliverables()
    {
        return [
            'Documentação técnica',
            'Código fonte',
            'Manual do usuário',
            'Suporte pós-entrega (30 dias)',
        ];
    }

    // protected static function booted()
    // {
    //     static::addGlobalScope(new CompanyScope);
    // }

    /**
     * Serviço pertence a uma categoria.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }


      // Adicionando SCOPE de COMPANY
        /**
     * Boot method - adiciona global scope para multi-tenancy.
     */
    protected static function booted()
    {
        // Sempre filtra pela empresa do usuário logado
        static::addGlobalScope('company', function (Builder $builder) {
            if (auth()->check() && auth()->user()->company_id) {
                $builder->where('company_id', auth()->user()->company_id);
            }
        });

        // Ao criar, adiciona company_id automaticamente
        static::creating(function ($service) {
            if (auth()->check() && !$service->company_id) {
                $service->company_id = auth()->user()->company_id;
            }
        });
    }

}
