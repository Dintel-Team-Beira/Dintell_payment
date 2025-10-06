<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
     use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'name',
        'description',
        'type',
        'parent_id',
        'order',
        'color',
        'icon',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * The attributes that should be hidden.
     */
    protected $hidden = [
        'deleted_at',
    ];

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
        static::creating(function ($category) {
            if (auth()->check() && !$category->company_id) {
                $category->company_id = auth()->user()->company_id;
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Categoria pertence a uma empresa.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Categoria pai (para subcategorias).
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Subcategorias (filhas).
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')
                    ->orderBy('order');
    }

    /**
     * Todas as subcategorias recursivamente.
     */
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    /**
     * Produtos desta categoria.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Serviços desta categoria.
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Apenas categorias ativas.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Apenas categorias principais (sem pai).
     */
    public function scopeMain($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Apenas subcategorias.
     */
    public function scopeSubcategories($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Filtrar por tipo.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Ordenar por ordem customizada.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & MUTATORS
    |--------------------------------------------------------------------------
    */

    /**
     * Retorna nome completo com hierarquia.
     * Ex: "Eletrônicos > Smartphones > iPhone"
     */
    public function getFullNameAttribute()
    {
        $names = collect([$this->name]);
        $parent = $this->parent;

        while ($parent) {
            $names->prepend($parent->name);
            $parent = $parent->parent;
        }

        return $names->implode(' > ');
    }

    /**
     * Retorna a profundidade/nível da categoria.
     * 0 = categoria principal, 1 = subcategoria, etc.
     */
    public function getDepthAttribute()
    {
        $depth = 0;
        $parent = $this->parent;

        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }

        return $depth;
    }

    /**
     * Verifica se é categoria principal.
     */
    public function getIsMainAttribute()
    {
        return is_null($this->parent_id);
    }

    /**
     * Verifica se tem subcategorias.
     */
    public function getHasChildrenAttribute()
    {
        return $this->children()->exists();
    }

    /**
     * Conta total de produtos.
     */
    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }

    /**
     * Conta total de serviços.
     */
    public function getServicesCountAttribute()
    {
        return $this->services()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Verifica se pode ser aplicada a produtos.
     */
    public function canBeUsedForProducts()
    {
        return in_array($this->type, ['product', 'both']);
    }

    /**
     * Verifica se pode ser aplicada a serviços.
     */
    public function canBeUsedForServices()
    {
        return in_array($this->type, ['service', 'both']);
    }

    /**
     * Retorna todas as categorias ancestrais (pais, avós, etc).
     */
    public function getAncestors()
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    /**
     * Retorna todos os descendentes (filhos, netos, etc).
     */
    public function getDescendants()
    {
        $descendants = collect();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getDescendants());
        }

        return $descendants;
    }

    /**
     * Retorna IDs de todos os descendentes.
     */
    public function getDescendantIds()
    {
        return $this->getDescendants()->pluck('id')->toArray();
    }

    /**
     * Valida se pode ser movida para outro pai (evita loops).
     */
    public function canBeMovedTo($newParentId)
    {
        if (!$newParentId) {
            return true; // Pode virar categoria principal
        }

        if ($this->id == $newParentId) {
            return false; // Não pode ser pai de si mesma
        }

        $descendantIds = $this->getDescendantIds();
        
        return !in_array($newParentId, $descendantIds); // Não pode ser movida para um filho
    }
}
