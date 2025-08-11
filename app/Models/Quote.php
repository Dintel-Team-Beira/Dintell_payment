<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

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
        'discount_amount',
        'total',
        'status',
        'notes',
        'terms_conditions',
        'sent_at',
        'status_updated_at',
        'converted_to_invoice_at',
        'invoice_id',
        'company_id', // Adicionar para multi-tenancy
    ];

    protected $casts = [
        'quote_date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
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

       // Relacionamento com empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
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

    public function notes()
    {
        return $this->hasMany(QuoteNote::class);
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

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('quote_number', 'like', "%{$search}%")
              ->orWhere('notes', 'like', "%{$search}%")
              ->orWhereHas('client', function($clientQuery) use ($search) {
                  $clientQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
              });
        });
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

    public function getFormattedDiscountAmountAttribute()
    {
        return number_format($this->discount_amount, 2, ',', '.') . ' MT';
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
    public function isExpired()
    {
        return $this->is_expired;
    }

    public function hasNotes()
    {
        return !empty($this->notes) || $this->notes()->exists();
    }

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

    public function canDuplicate()
    {
        return true; // Sempre pode duplicar
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

    public function duplicate()
    {
        $newQuote = $this->replicate();
        $newQuote->quote_number = null; // Será gerado automaticamente
        $newQuote->status = self::STATUS_DRAFT;
        $newQuote->sent_at = null;
        $newQuote->status_updated_at = null;
        $newQuote->converted_to_invoice_at = null;
        $newQuote->invoice_id = null;
        $newQuote->quote_date = Carbon::today();
        $newQuote->valid_until = Carbon::today()->addDays(30);
        $newQuote->save();

        // Copiar itens
        foreach ($this->items as $item) {
            $newItem = $item->replicate();
            $newItem->quote_id = $newQuote->id;
            $newItem->save();
        }

        return $newQuote;
    }

    // public function convertToInvoice()
    // {
    //     if (!$this->canConvertToInvoice()) {
    //         throw new \Exception('Esta cotação não pode ser convertida em fatura.');
    //     }

    //     $invoiceData = [
    //         'client_id' => $this->client_id,
    //         'quote_id' => $this->id,
    //         'invoice_date' => Carbon::today(),
    //         'due_date' => Carbon::today()->addDays(30),
    //         'subtotal' => $this->subtotal,
    //         'tax_amount' => $this->tax_amount,
    //         'discount_amount' => $this->discount_amount ?? 0,
    //         'total' => $this->total,
    //         'status' => 'pending',
    //         'notes' => $this->notes
    //     ];

    //     $invoice = Invoice::create($invoiceData);

    //     // Copiar itens da cotação para a fatura
    //     foreach ($this->items as $quoteItem) {
    //         $invoice->items()->create([
    //             'type' => $quoteItem->type,
    //             'item_id' => $quoteItem->item_id,
    //             'name' => $quoteItem->name,
    //             'description' => $quoteItem->description,
    //             'quantity' => $quoteItem->quantity,
    //             'unit_price' => $quoteItem->unit_price,
    //             'tax_rate' => $quoteItem->tax_rate,
    //             'category' => $quoteItem->category,
    //             'unit' => $quoteItem->unit,
    //             'complexity_level' => $quoteItem->complexity_level,
    //             'estimated_hours' => $quoteItem->estimated_hours
    //         ]);
    //     }

    //     // Marcar cotação como convertida
    //     $this->update([
    //         'converted_to_invoice_at' => now(),
    //         'invoice_id' => $invoice->id
    //     ]);

    //     return $invoice;
    // }


    // nova// No modelo Quote, método convertToInvoice() corrigido:

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
        'discount_amount' => $this->discount_amount ?? 0,
        'total' => $this->total,
        'status' => 'draft',
        'notes' => $this->notes,
        'invoice_number' => $this->generateInvoiceNumber()
    ];

    $invoice = Invoice::create($invoiceData);

    // Copiar itens da cotação para a fatura - MAPEAMENTO CORRETO DOS CAMPOS
    foreach ($this->items as $quoteItem) {
        // Calcular totais
        $subtotalItem = $quoteItem->quantity * $quoteItem->unit_price;
        $taxItem = $subtotalItem * (($quoteItem->tax_rate ?? 0) / 100);
        $totalItem = $subtotalItem + $taxItem;

        // Preparar dados com mapeamento correto
        $itemData = [
            'invoice_id' => $invoice->id,
            'description' => $quoteItem->description ?? $quoteItem->name ?? '',
            'quantity' => $quoteItem->quantity,
            'unit_price' => $quoteItem->unit_price,
            'tax_rate' => $quoteItem->tax_rate ?? 0,
            'total_price' => $totalItem, // ← CORRIGIDO: Usar total_price em vez de total
        ];

        // Adicionar campos opcionais se existirem
        $optionalFields = [
            'name' => $quoteItem->name ?? '',
            'type' => $quoteItem->type ?? 'product',
            'item_id' => $quoteItem->item_id ?? null,
            'category' => $quoteItem->category ?? null,
            'unit' => $quoteItem->unit ?? null,
            'complexity_level' => $quoteItem->complexity_level ?? null,
            'estimated_hours' => $quoteItem->estimated_hours ?? null
        ];

        foreach ($optionalFields as $field => $value) {
            if (Schema::hasColumn('invoice_items', $field)) {
                $itemData[$field] = $value;
            }
        }

        $invoice->items()->create($itemData);
    }

    // Marcar cotação como convertida
    $updateData = [];
    if (Schema::hasColumn('quotes', 'converted_to_invoice_at')) {
        $updateData['converted_to_invoice_at'] = now();
    }
    if (Schema::hasColumn('quotes', 'invoice_id')) {
        $updateData['invoice_id'] = $invoice->id;
    }

    if (!empty($updateData)) {
        $this->update($updateData);
    }

    return $invoice;
}

// Método auxiliar para descobrir a estrutura da tabela automaticamente
private function getInvoiceItemData($quoteItem, $invoiceId)
{
    // Verificar quais campos existem na tabela
    $columns = Schema::getColumnListing('invoice_items');

    // Calcular totais
    $subtotal = $quoteItem->quantity * $quoteItem->unit_price;
    $tax = $subtotal * (($quoteItem->tax_rate ?? 0) / 100);
    $total = $subtotal + $tax;

    // Mapear campos baseado na estrutura real da tabela
    $data = ['invoice_id' => $invoiceId];

    // Campos obrigatórios com fallbacks
    $fieldMappings = [
        'name' => $quoteItem->name ?? 'Item',
        'description' => $quoteItem->description ?? $quoteItem->name ?? '',
        'quantity' => $quoteItem->quantity,
        'unit_price' => $quoteItem->unit_price,
        'tax_rate' => $quoteItem->tax_rate ?? 0,
        'total' => $total,
        'total_price' => $total, // Ambos os nomes possíveis
        'type' => $quoteItem->type ?? 'product',
        'item_id' => $quoteItem->item_id ?? null,
        'category' => $quoteItem->category ?? null,
        'unit' => $quoteItem->unit ?? null,
        'complexity_level' => $quoteItem->complexity_level ?? null,
        'estimated_hours' => $quoteItem->estimated_hours ?? null
    ];

    // Adicionar apenas campos que existem na tabela
    foreach ($fieldMappings as $field => $value) {
        if (in_array($field, $columns)) {
            $data[$field] = $value;
        }
    }

    return $data;
}
    public function calculateTotals()
    {
        $subtotal = $this->items()->sum(\DB::raw('quantity * unit_price'));
        $taxAmount = $this->items()->sum(\DB::raw('(quantity * unit_price) * (tax_rate / 100)'));
        $discountAmount = $this->discount_amount ?? 0;

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total' => $subtotal + $taxAmount - $discountAmount
        ]);
    }
// Método auxiliar para gerar número da fatura
private function generateInvoiceNumber()
{
    $prefix = 'FAT';
    $lastInvoice = Invoice::orderBy('id', 'desc')->first();

    if ($lastInvoice && $lastInvoice->invoice_number) {
        // Extrair número da última fatura
        preg_match('/\d+$/', $lastInvoice->invoice_number, $matches);
        $nextNumber = isset($matches[0]) ? (int)$matches[0] + 1 : 1;
    } else {
        $nextNumber = 1;
    }

    return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
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

    public static function getStatsForDashboard()
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $currentStats = [
            'total_quotes' => self::thisMonth()->count(),
            'total_amount' => self::thisMonth()->sum('total'),
            'pending_count' => self::pending()->count(),
            'pending_amount' => self::pending()->sum('total'),
            'accepted_count' => self::accepted()->count(),
            'accepted_amount' => self::accepted()->sum('total'),
            'conversion_rate' => self::getConversionRate(),
            'conversion_target' => 75
        ];

        // Calcular crescimento comparado ao mês anterior
        $lastMonthStats = [
            'total_quotes' => self::whereMonth('created_at', $lastMonth->month)
                                 ->whereYear('created_at', $lastMonth->year)
                                 ->count(),
            'total_amount' => self::whereMonth('created_at', $lastMonth->month)
                                 ->whereYear('created_at', $lastMonth->year)
                                 ->sum('total')
        ];

        if ($lastMonthStats['total_quotes'] > 0) {
            $currentStats['quotes_growth'] = (($currentStats['total_quotes'] - $lastMonthStats['total_quotes']) / $lastMonthStats['total_quotes']) * 100;
        } else {
            $currentStats['quotes_growth'] = $currentStats['total_quotes'] > 0 ? 100 : 0;
        }

        if ($lastMonthStats['total_amount'] > 0) {
            $currentStats['amount_growth'] = (($currentStats['total_amount'] - $lastMonthStats['total_amount']) / $lastMonthStats['total_amount']) * 100;
        } else {
            $currentStats['amount_growth'] = $currentStats['total_amount'] > 0 ? 100 : 0;
        }

        return $currentStats;
    }

    // Event Listeners
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quote) {

             $company = session('current_company');
            if ($company && !$quote->company_id) {
                $quote->company_id = $company->id;
            }

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
        // Tentar usar BillingSetting se existir, caso contrário usar lógica padrão
        try {
            $settings = BillingSetting::getSettings();
            $prefix = $settings->quote_prefix ?? 'COT';
            $nextNumber = $settings->next_quote_number ?? 1;
            $settings->increment('next_quote_number');
        } catch (\Exception $e) {
            // Se não existir BillingSetting, usar lógica padrão
            $prefix = 'COT';
            $lastQuote = self::orderBy('id', 'desc')->first();
            $nextNumber = $lastQuote ? (int)substr($lastQuote->quote_number, strrpos($lastQuote->quote_number, '-') + 1) + 1 : 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

     protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }
}
