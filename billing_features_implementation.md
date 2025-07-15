# Implementação de Novas Funcionalidades do Sistema de Faturação

## 1. Migração do Banco de Dados

### Migration para adicionar novos campos
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillingFeaturesToInvoices extends Migration
{
    public function up()
    {
        // Adicionar campos para descontos na tabela invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'credit_card', 'other'])
                  ->default('bank_transfer')
                  ->after('status');
            
            // Desconto comercial
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('tax_amount');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_percentage');
            
            // Para notas de crédito/débito
            $table->enum('document_type', ['invoice', 'credit_note', 'debit_note'])
                  ->default('invoice')
                  ->after('invoice_number');
            $table->unsignedBigInteger('related_invoice_id')->nullable()->after('quote_id');
            $table->text('adjustment_reason')->nullable()->after('notes');
            
            // Campos para venda à dinheiro
            $table->boolean('is_cash_sale')->default(false)->after('payment_method');
            $table->decimal('cash_received', 10, 2)->default(0)->after('paid_amount');
            $table->decimal('change_given', 10, 2)->default(0)->after('cash_received');
            
            // Índices
            $table->index('document_type');
            $table->index('payment_method');
            $table->foreign('related_invoice_id')->references('id')->on('invoices')->onDelete('set null');
        });

        // Adicionar campos na tabela quotes também
        Schema::table('quotes', function (Blueprint $table) {
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('tax_amount');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_percentage');
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['related_invoice_id']);
            $table->dropColumn([
                'payment_method',
                'discount_percentage',
                'discount_amount',
                'document_type',
                'related_invoice_id',
                'adjustment_reason',
                'is_cash_sale',
                'cash_received',
                'change_given'
            ]);
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn([
                'discount_percentage',
                'discount_amount'
            ]);
        });
    }
}
```

## 2. Atualizar Models

### Invoice Model (atualizado)
```php
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
        'payment_method',
        'subtotal',
        'tax_amount',
        'discount_percentage',
        'discount_amount',
        'total',
        'paid_amount',
        'notes',
        'terms_conditions',
        'payment_terms_days',
        'sent_at',
        'document_type',
        'related_invoice_id',
        'adjustment_reason',
        'is_cash_sale',
        'cash_received',
        'change_given'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'cash_received' => 'decimal:2',
        'change_given' => 'decimal:2',
        'paid_at' => 'datetime',
        'is_cash_sale' => 'boolean'
    ];

    // Constantes para tipos de documento
    const TYPE_INVOICE = 'invoice';
    const TYPE_CREDIT_NOTE = 'credit_note';
    const TYPE_DEBIT_NOTE = 'debit_note';

    // Constantes para métodos de pagamento
    const PAYMENT_CASH = 'cash';
    const PAYMENT_BANK_TRANSFER = 'bank_transfer';
    const PAYMENT_CHECK = 'check';
    const PAYMENT_CREDIT_CARD = 'credit_card';
    const PAYMENT_OTHER = 'other';

    // Relacionamentos existentes...
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

    // Novos relacionamentos
    public function relatedInvoice()
    {
        return $this->belongsTo(Invoice::class, 'related_invoice_id');
    }

    public function creditNotes()
    {
        return $this->hasMany(Invoice::class, 'related_invoice_id')
                    ->where('document_type', self::TYPE_CREDIT_NOTE);
    }

    public function debitNotes()
    {
        return $this->hasMany(Invoice::class, 'related_invoice_id')
                    ->where('document_type', self::TYPE_DEBIT_NOTE);
    }

    // Métodos auxiliares para tipos de documento
    public function isInvoice()
    {
        return $this->document_type === self::TYPE_INVOICE;
    }

    public function isCreditNote()
    {
        return $this->document_type === self::TYPE_CREDIT_NOTE;
    }

    public function isDebitNote()
    {
        return $this->document_type === self::TYPE_DEBIT_NOTE;
    }

    public function isCashSale()
    {
        return $this->is_cash_sale || $this->payment_method === self::PAYMENT_CASH;
    }

    // Scopes
    public function scopeInvoices($query)
    {
        return $query->where('document_type', self::TYPE_INVOICE);
    }

    public function scopeCreditNotes($query)
    {
        return $query->where('document_type', self::TYPE_CREDIT_NOTE);
    }

    public function scopeDebitNotes($query)
    {
        return $query->where('document_type', self::TYPE_DEBIT_NOTE);
    }

    public function scopeCashSales($query)
    {
        return $query->where('is_cash_sale', true);
    }

    // Getters formatados
    public function getDocumentTypeLabelAttribute()
    {
        $labels = [
            self::TYPE_INVOICE => 'Factura',
            self::TYPE_CREDIT_NOTE => 'Nota de Crédito',
            self::TYPE_DEBIT_NOTE => 'Nota de Débito'
        ];

        return $labels[$this->document_type] ?? 'Documento';
    }

    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            self::PAYMENT_CASH => 'Dinheiro',
            self::PAYMENT_BANK_TRANSFER => 'Transferência Bancária',
            self::PAYMENT_CHECK => 'Cheque',
            self::PAYMENT_CREDIT_CARD => 'Cartão de Crédito',
            self::PAYMENT_OTHER => 'Outro'
        ];

        return $labels[$this->payment_method] ?? 'Não especificado';
    }

    public function getFormattedDiscountAmountAttribute()
    {
        return number_format($this->discount_amount, 2, ',', '.') . ' MT';
    }

    // Cálculo com desconto
    public function calculateTotalWithDiscount()
    {
        $subtotalWithTax = $this->subtotal + $this->tax_amount;
        
        // Se tem percentual de desconto, calcular
        if ($this->discount_percentage > 0) {
            $this->discount_amount = $subtotalWithTax * ($this->discount_percentage / 100);
        }
        
        $this->total = $subtotalWithTax - $this->discount_amount;
        
        return $this->total;
    }

    // Métodos para venda à dinheiro
    public function processCashPayment($cashReceived)
    {
        if ($cashReceived < $this->total) {
            throw new \Exception('Valor recebido é menor que o total da fatura');
        }

        $this->cash_received = $cashReceived;
        $this->change_given = $cashReceived - $this->total;
        $this->paid_amount = $this->total;
        $this->status = 'paid';
        $this->paid_at = now();
        $this->save();

        return $this->change_given;
    }

    // Métodos existentes...
    public function isOverdue()
    {
        return $this->status !== 'paid' && Carbon::now()->isAfter($this->due_date);
    }

    public function getRemainingAmountAttribute()
    {
        return $this->total - $this->paid_amount;
    }

    // Atualizar método boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                $settings = BillingSetting::getSettings();
                
                // Gerar número baseado no tipo de documento
                switch ($invoice->document_type) {
                    case self::TYPE_CREDIT_NOTE:
                        $invoice->invoice_number = $settings->getNextCreditNoteNumber();
                        break;
                    case self::TYPE_DEBIT_NOTE:
                        $invoice->invoice_number = $settings->getNextDebitNoteNumber();
                        break;
                    default:
                        $invoice->invoice_number = $settings->getNextInvoiceNumber();
                }
            }

            // Para vendas à dinheiro, definir como paga automaticamente
            if ($invoice->is_cash_sale) {
                $invoice->status = 'paid';
                $invoice->paid_at = now();
                $invoice->payment_method = self::PAYMENT_CASH;
            }
        });

        static::saving(function ($invoice) {
            // Recalcular total se houver mudanças
            if ($invoice->isDirty(['subtotal', 'tax_amount', 'discount_amount', 'discount_percentage'])) {
                $invoice->calculateTotalWithDiscount();
            }
        });
    }

    // Métodos estáticos auxiliares
    public static function getDocumentTypes()
    {
        return [
            self::TYPE_INVOICE => 'Factura',
            self::TYPE_CREDIT_NOTE => 'Nota de Crédito',
            self::TYPE_DEBIT_NOTE => 'Nota de Débito'
        ];
    }

    public static function getPaymentMethods()
    {
        return [
            self::PAYMENT_CASH => 'Dinheiro',
            self::PAYMENT_BANK_TRANSFER => 'Transferência Bancária',
            self::PAYMENT_CHECK => 'Cheque',
            self::PAYMENT_CREDIT_CARD => 'Cartão de Crédito',
            self::PAYMENT_OTHER => 'Outro'
        ];
    }
}
```

### BillingSetting Model (atualizado)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingSetting extends Model
{
    protected $fillable = [
        'invoice_prefix',
        'next_invoice_number',
        'quote_prefix',
        'next_quote_number',
        'credit_note_prefix',
        'next_credit_note_number',
        'debit_note_prefix',
        'next_debit_note_number',
        'default_payment_terms',
        'default_tax_rate',
        'company_name',
        'company_email',
        'company_phone',
        'company_address',
        'company_nuit'
    ];

    protected $casts = [
        'next_invoice_number' => 'integer',
        'next_quote_number' => 'integer',
        'next_credit_note_number' => 'integer',
        'next_debit_note_number' => 'integer',
        'default_payment_terms' => 'integer',
        'default_tax_rate' => 'decimal:2'
    ];

    public static function getSettings()
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'invoice_prefix' => 'FAT',
                'next_invoice_number' => 1,
                'quote_prefix' => 'COT',
                'next_quote_number' => 1,
                'credit_note_prefix' => 'NC',
                'next_credit_note_number' => 1,
                'debit_note_prefix' => 'ND',
                'next_debit_note_number' => 1,
                'default_payment_terms' => 30,
                'default_tax_rate' => 16
            ]
        );
    }

    public function getNextInvoiceNumber()
    {
        $number = $this->invoice_prefix . str_pad($this->next_invoice_number, 6, '0', STR_PAD_LEFT);
        $this->increment('next_invoice_number');
        return $number;
    }

    public function getNextCreditNoteNumber()
    {
        $number = $this->credit_note_prefix . str_pad($this->next_credit_note_number, 6, '0', STR_PAD_LEFT);
        $this->increment('next_credit_note_number');
        return $number;
    }

    public function getNextDebitNoteNumber()
    {
        $number = $this->debit_note_prefix . str_pad($this->next_debit_note_number, 6, '0', STR_PAD_LEFT);
        $this->increment('next_debit_note_number');
        return $number;
    }
}
```

## 3. Controllers

### CreditNoteController
```php
<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditNoteController extends Controller
{
    public function index(Request $request)
    {
        $creditNotes = Invoice::creditNotes()
            ->with(['client', 'relatedInvoice'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('credit-notes.index', compact('creditNotes'));
    }

    public function create(Request $request)
    {
        $invoiceId = $request->get('invoice_id');
        $invoice = null;
        
        if ($invoiceId) {
            $invoice = Invoice::findOrFail($invoiceId);
        }

        $clients = Client::orderBy('name')->get();

        return view('credit-notes.create', compact('invoice', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'related_invoice_id' => 'nullable|exists:invoices,id',
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'adjustment_reason' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Calcular totais
            $subtotal = 0;
            $taxAmount = 0;

            foreach ($validated['items'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemTax = $itemSubtotal * (($item['tax_rate'] ?? 0) / 100);
                
                $subtotal += $itemSubtotal;
                $taxAmount += $itemTax;
            }

            // Calcular desconto
            $discountAmount = $validated['discount_amount'] ?? 0;
            if ($validated['discount_percentage'] ?? 0 > 0) {
                $discountAmount = ($subtotal + $taxAmount) * ($validated['discount_percentage'] / 100);
            }

            $total = $subtotal + $taxAmount - $discountAmount;

            // Criar nota de crédito
            $creditNote = Invoice::create([
                'document_type' => Invoice::TYPE_CREDIT_NOTE,
                'client_id' => $validated['client_id'],
                'related_invoice_id' => $validated['related_invoice_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['invoice_date'], // Notas de crédito não têm vencimento
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_percentage' => $validated['discount_percentage'] ?? 0,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'status' => 'paid', // Notas de crédito são automaticamente processadas
                'adjustment_reason' => $validated['adjustment_reason'],
                'notes' => $validated['notes'],
                'payment_method' => Invoice::PAYMENT_OTHER
            ]);

            // Criar itens
            foreach ($validated['items'] as $item) {
                $creditNote->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'total_price' => $item['quantity'] * $item['unit_price']
                ]);
            }

            // Se está relacionada a uma fatura, atualizar o saldo
            if ($creditNote->related_invoice_id) {
                $relatedInvoice = Invoice::find($creditNote->related_invoice_id);
                if ($relatedInvoice) {
                    // Reduzir o valor pago da fatura original
                    $relatedInvoice->paid_amount = max(0, $relatedInvoice->paid_amount - $total);
                    if ($relatedInvoice->paid_amount < $relatedInvoice->total) {
                        $relatedInvoice->status = 'sent';
                    }
                    $relatedInvoice->save();
                }
            }

            DB::commit();

            return redirect()->route('credit-notes.show', $creditNote)
                ->with('success', 'Nota de crédito criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erro ao criar nota de crédito: ' . $e->getMessage());
        }
    }

    public function show(Invoice $creditNote)
    {
        if (!$creditNote->isCreditNote()) {
            abort(404);
        }

        $creditNote->load(['client', 'items', 'relatedInvoice']);

        return view('credit-notes.show', compact('creditNote'));
    }
}
```

### DebitNoteController
```php
<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebitNoteController extends Controller
{
    public function index(Request $request)
    {
        $debitNotes = Invoice::debitNotes()
            ->with(['client', 'relatedInvoice'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('debit-notes.index', compact('debitNotes'));
    }

    public function create(Request $request)
    {
        $invoiceId = $request->get('invoice_id');
        $invoice = null;
        
        if ($invoiceId) {
            $invoice = Invoice::findOrFail($invoiceId);
        }

        $clients = Client::orderBy('name')->get();

        return view('debit-notes.create', compact('invoice', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'related_invoice_id' => 'nullable|exists:invoices,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'adjustment_reason' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Calcular totais
            $subtotal = 0;
            $taxAmount = 0;

            foreach ($validated['items'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemTax = $itemSubtotal * (($item['tax_rate'] ?? 0) / 100);
                
                $subtotal += $itemSubtotal;
                $taxAmount += $itemTax;
            }

            $total = $subtotal + $taxAmount;

            // Criar nota de débito
            $debitNote = Invoice::create([
                'document_type' => Invoice::TYPE_DEBIT_NOTE,
                'client_id' => $validated['client_id'],
                'related_invoice_id' => $validated['related_invoice_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'status' => 'sent',
                'adjustment_reason' => $validated['adjustment_reason'],
                'notes' => $validated['notes'],
                'payment_method' => Invoice::PAYMENT_OTHER
            ]);

            // Criar itens
            foreach ($validated['items'] as $item) {
                $debitNote->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'total_price' => $item['quantity'] * $item['unit_price']
                ]);
            }

            DB::commit();

            return redirect()->route('debit-notes.show', $debitNote)
                ->with('success', 'Nota de débito criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erro ao criar nota de débito: ' . $e->getMessage());
        }
    }

    public function show(Invoice $debitNote)
    {
        if (!$debitNote->isDebitNote()) {
            abort(404);
        }

        $debitNote->load(['client', 'items', 'relatedInvoice']);

        return view('debit-notes.show', compact('debitNote'));
    }
}
```

### CashSaleController
```php
<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashSaleController extends Controller
{
    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('cash-sales.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'cash_received' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Calcular totais
            $subtotal = 0;
            $taxAmount = 0;

            foreach ($validated['items'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemTax = $itemSubtotal * (($item['tax_rate'] ?? 0) / 100);
                
                $subtotal += $itemSubtotal;
                $taxAmount += $itemTax;
            }

            // Calcular desconto
            $discountAmount = $validated['discount_amount'] ?? 0;
            if (($validated['discount_percentage'] ?? 0) > 0) {
                $discountAmount = ($subtotal + $taxAmount) * ($validated['discount_percentage'] / 100);
            }

            $total = $subtotal + $taxAmount - $discountAmount;

            // Verificar se o valor recebido é suficiente
            if ($validated['cash_received'] < $total) {
                throw new \Exception('Valor recebido é insuficiente');
            }

            $change = $validated['cash_received'] - $total;

            // Criar venda à dinheiro
            $cashSale = Invoice::create([
                'document_type' => Invoice::TYPE_INVOICE,
                'client_id' => $validated['client_id'],
                'invoice_date' => now(),
                'due_date' => now(),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_percentage' => $validated['discount_percentage'] ?? 0,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'is_cash_sale' => true,
                'payment_method' => Invoice::PAYMENT_CASH,
                'cash_received' => $validated['cash_received'],
                'change_given' => $change,
                'paid_amount' => $total,
                'status' => 'paid',
                'paid_at' => now(),
                'notes' => $validated['notes']
            ]);

            // Criar itens
            foreach ($validated['items'] as $item) {
                $cashSale->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'total_price' => $item['quantity'] * $item['unit_price']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'invoice_id' => $cashSale->id,
                'invoice_number' => $cashSale->invoice_number,
                'total' => $total,
                'change' => $change,
                'redirect_url' => route('invoices.show', $cashSale)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function quickSale()
    {
        // Vista para vendas rápidas (POS-like)
        return view('cash-sales.quick-sale');
    }
}
```

## 4. Views

### Botões de Ação Atualizados (invoices/show.blade.php)
```blade
<!-- Adicionar após os botões existentes -->
@if($invoice->isInvoice() && $invoice->status === 'paid')
    <!-- Criar Nota de Crédito -->
    <a href="{{ route('credit-notes.create', ['invoice_id' => $invoice->id]) }}"
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-orange-600 rounded-lg hover:bg-orange-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m0 0l6 6m-6-6v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h7"/>
        </svg>
        Criar Nota de Crédito
    </a>
@endif

@if($invoice->isInvoice())
    <!-- Criar Nota de Débito -->
    <a href="{{ route('debit-notes.create', ['invoice_id' => $invoice->id]) }}"
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-yellow-600 rounded-lg hover:bg-yellow-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Criar Nota de Débito
    </a>
@endif

### Vista para Vendas à Dinheiro (cash-sales/create.blade.php)
```blade
@extends('layouts.app')

@section('title', 'Nova Venda à Dinheiro')

@section('content')
<div class="container mx-auto px-4">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Nova Venda à Dinheiro</h1>
        <p class="mt-2 text-gray-600">Registre uma venda com pagamento imediato</p>
    </div>

    <form id="cashSaleForm" action="{{ route('cash-sales.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Coluna Principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Seleção de Cliente -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Cliente</h3>
                    
                    <select name="client_id" id="client_id" class="w-full rounded-lg border-gray-300" required>
                        <option value="">Selecione um cliente</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Itens -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Itens da Venda</h3>
                        <button type="button" onclick="addItem()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Adicionar Item
                        </button>
                    </div>

                    <div id="items-container">
                        <!-- Items serão adicionados aqui -->
                    </div>
                </div>

                <!-- Observações -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Observações</h3>
                    <textarea name="notes" rows="3" 
                              class="w-full rounded-lg border-gray-300"
                              placeholder="Observações opcionais..."></textarea>
                </div>
            </div>

            <!-- Coluna Lateral - Resumo -->
            <div class="space-y-6">
                <!-- Resumo Financeiro -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumo da Venda</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span id="subtotal" class="font-medium">0,00 MT</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">IVA:</span>
                            <span id="tax" class="font-medium">0,00 MT</span>
                        </div>

                        <!-- Desconto -->
                        <div class="border-t pt-3">
                            <div class="mb-2">
                                <label class="text-sm text-gray-600">Desconto:</label>
                                <div class="flex gap-2 mt-1">
                                    <input type="number" name="discount_percentage" 
                                           id="discount_percentage"
                                           min="0" max="100" step="0.01"
                                           class="w-20 rounded border-gray-300 text-sm"
                                           placeholder="%">
                                    <input type="number" name="discount_amount" 
                                           id="discount_amount"
                                           min="0" step="0.01"
                                           class="flex-1 rounded border-gray-300 text-sm"
                                           placeholder="Valor">
                                </div>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Desconto:</span>
                                <span id="discount_display" class="font-medium text-red-600">0,00 MT</span>
                            </div>
                        </div>

                        <div class="border-t pt-3 pb-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>TOTAL:</span>
                                <span id="total" class="text-blue-600">0,00 MT</span>
                            </div>
                        </div>

                        <!-- Pagamento -->
                        <div class="border-t pt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Valor Recebido:
                            </label>
                            <input type="number" name="cash_received" id="cash_received"
                                   min="0" step="0.01" required
                                   class="w-full rounded-lg border-gray-300 text-lg font-bold text-center"
                                   placeholder="0,00">
                        </div>

                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Troco:</span>
                                <span id="change" class="text-green-600">0,00 MT</span>
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="mt-6 space-y-3">
                        <button type="submit" id="submitBtn"
                                class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            Finalizar Venda
                        </button>
                        
                        <a href="{{ route('invoices.index') }}"
                           class="block w-full py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium text-center">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let itemIndex = 0;

function addItem() {
    const container = document.getElementById('items-container');
    const itemHtml = `
        <div class="item-row border rounded-lg p-4 mb-4" data-index="${itemIndex}">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                    <input type="text" name="items[${itemIndex}][description]" 
                           class="w-full rounded border-gray-300" required>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Qtd</label>
                    <input type="number" name="items[${itemIndex}][quantity]" 
                           class="w-full rounded border-gray-300 item-quantity" 
                           min="0.01" step="0.01" value="1" required>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preço Unit.</label>
                    <input type="number" name="items[${itemIndex}][unit_price]" 
                           class="w-full rounded border-gray-300 item-price" 
                           min="0" step="0.01" value="0" required>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">IVA %</label>
                    <input type="number" name="items[${itemIndex}][tax_rate]" 
                           class="w-full rounded border-gray-300 item-tax" 
                           min="0" max="100" step="0.01" value="16">
                </div>
                <div class="col-span-1 flex items-end">
                    <button type="button" onclick="removeItem(this)" 
                            class="w-full py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    itemIndex++;
    attachEventListeners();
}

function removeItem(button) {
    button.closest('.item-row').remove();
    calculateTotals();
}

function attachEventListeners() {
    // Quantidade, preço e taxa
    document.querySelectorAll('.item-quantity, .item-price, .item-tax').forEach(input => {
        input.removeEventListener('input', calculateTotals);
        input.addEventListener('input', calculateTotals);
    });

    // Desconto
    document.getElementById('discount_percentage').addEventListener('input', function() {
        if (this.value) {
            document.getElementById('discount_amount').value = '';
        }
        calculateTotals();
    });

    document.getElementById('discount_amount').addEventListener('input', function() {
        if (this.value) {
            document.getElementById('discount_percentage').value = '';
        }
        calculateTotals();
    });

    // Valor recebido
    document.getElementById('cash_received').addEventListener('input', calculateChange);
}

function calculateTotals() {
    let subtotal = 0;
    let taxAmount = 0;

    document.querySelectorAll('.item-row').forEach(row => {
        const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const taxRate = parseFloat(row.querySelector('.item-tax').value) || 0;

        const itemSubtotal = quantity * price;
        const itemTax = itemSubtotal * (taxRate / 100);

        subtotal += itemSubtotal;
        taxAmount += itemTax;
    });

    // Calcular desconto
    let discountAmount = 0;
    const discountPercentage = parseFloat(document.getElementById('discount_percentage').value) || 0;
    const discountValue = parseFloat(document.getElementById('discount_amount').value) || 0;

    if (discountPercentage > 0) {
        discountAmount = (subtotal + taxAmount) * (discountPercentage / 100);
    } else if (discountValue > 0) {
        discountAmount = discountValue;
    }

    const total = subtotal + taxAmount - discountAmount;

    // Atualizar display
    document.getElementById('subtotal').textContent = formatCurrency(subtotal);
    document.getElementById('tax').textContent = formatCurrency(taxAmount);
    document.getElementById('discount_display').textContent = formatCurrency(discountAmount);
    document.getElementById('total').textContent = formatCurrency(total);

    calculateChange();
}

function calculateChange() {
    const total = parseCurrency(document.getElementById('total').textContent);
    const received = parseFloat(document.getElementById('cash_received').value) || 0;
    const change = Math.max(0, received - total);

    document.getElementById('change').textContent = formatCurrency(change);

    // Habilitar/desabilitar botão de submit
    const submitBtn = document.getElementById('submitBtn');
    if (received >= total && total > 0) {
        submitBtn.disabled = false;
    } else {
        submitBtn.disabled = true;
    }
}

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-MZ', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value) + ' MT';
}

function parseCurrency(text) {
    return parseFloat(text.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
}

// Adicionar primeiro item ao carregar
addItem();

// Submit do formulário
document.getElementById('cashSaleForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Processando...';

    try {
        const response = await fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            // Mostrar sucesso
            alert(`Venda realizada!\nFatura: ${data.invoice_number}\nTroco: ${formatCurrency(data.change)}`);
            window.location.href = data.redirect_url;
        } else {
            throw new Error(data.message || 'Erro ao processar venda');
        }
    } catch (error) {
        alert('Erro: ' + error.message);
        submitBtn.disabled = false;
        submitBtn.textContent = 'Finalizar Venda';
    }
});
</script>
@endpush
@endsection
```

### Vista para Notas de Crédito (credit-notes/create.blade.php)
```blade
@extends('layouts.app')

@section('title', 'Nova Nota de Crédito')

@section('content')
<div class="container mx-auto px-4">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Nova Nota de Crédito</h1>
        <p class="mt-2 text-gray-600">Crie uma nota de crédito para ajustar valores de faturas</p>
    </div>

    <form action="{{ route('credit-notes.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <!-- Informações Básicas -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações Básicas</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                            <select name="client_id" class="w-full rounded-lg border-gray-300" required>
                                <option value="">Selecione um cliente</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" 
                                            {{ ($invoice && $invoice->client_id == $client->id) ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data *</label>
                            <input type="date" name="invoice_date" 
                                   value="{{ date('Y-m-d') }}"
                                   class="w-full rounded-lg border-gray-300" required>
                        </div>
                    </div>

                    @if($invoice)
                        <input type="hidden" name="related_invoice_id" value="{{ $invoice->id }}">
                        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <strong>Fatura Relacionada:</strong> {{ $invoice->invoice_number }}
                            </p>
                        </div>
                    @endif

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Motivo do Ajuste *
                        </label>
                        <textarea name="adjustment_reason" rows="3" 
                                  class="w-full rounded-lg border-gray-300" 
                                  placeholder="Descreva o motivo desta nota de crédito..."
                                  required></textarea>
                    </div>
                </div>

                <!-- Itens -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Itens a Creditar</h3>
                    
                    <div id="credit-items-container">
                        <!-- Items dinâmicos -->
                    </div>

                    <button type="button" onclick="addCreditItem()" 
                            class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Adicionar Item
                    </button>
                </div>

                <!-- Observações -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Observações</h3>
                    <textarea name="notes" rows="3" 
                              class="w-full rounded-lg border-gray-300"
                              placeholder="Observações adicionais..."></textarea>
                </div>
            </div>

            <!-- Sidebar com totais -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumo</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span id="credit-subtotal">0,00 MT</span>
                        </div>
                        <div class="flex justify-between">
                            <span>IVA:</span>
                            <span id="credit-tax">0,00 MT</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t font-bold">
                            <span>Total a Creditar:</span>
                            <span id="credit-total" class="text-red-600">0,00 MT</span>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full mt-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium">
                        Criar Nota de Crédito
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// JavaScript similar ao de vendas à dinheiro, adaptado para notas de crédito
</script>
@endsection
```

## 5. Rotas (web.php)

```php
// Adicionar estas rotas ao arquivo routes/web.php

// Vendas à Dinheiro
Route::prefix('cash-sales')->name('cash-sales.')->group(function () {
    Route::get('/create', [CashSaleController::class, 'create'])->name('create');
    Route::post('/', [CashSaleController::class, 'store'])->name('store');
    Route::get('/quick-sale', [CashSaleController::class, 'quickSale'])->name('quick-sale');
});

// Notas de Crédito
Route::resource('credit-notes', CreditNoteController::class);

// Notas de Débito
Route::resource('debit-notes', DebitNoteController::class);

// Adicionar à rota de faturas existente
Route::prefix('invoices')->name('invoices.')->group(function () {
    // ... rotas existentes ...
    
    // Relatórios por tipo de documento
    Route::get('/by-type/{type}', [InvoiceController::class, 'indexByType'])->name('by-type');
});
```

## 6. Atualização do InvoiceController

```php
// Adicionar este método ao InvoiceController existente

public function create()
{
    $clients = Client::orderBy('name')->get();
    $settings = BillingSetting::getSettings();
    
    // Verificar se é venda à dinheiro
    $isCashSale = request()->get('cash_sale', false);

    return view('invoices.create', compact('clients', 'settings', 'isCashSale'));
}

// Atualizar o método store para suportar descontos
public function store(Request $request)
{
    $validated = $request->validate([
        'client_id' => 'required|exists:clients,id',
        'quote_id' => 'nullable|exists:quotes,id',
        'invoice_date' => 'required|date',
        'payment_terms_days' => 'required|numeric|min:0|max:365',
        'payment_method' => 'required|in:cash,bank_transfer,check,credit_card,other',
        'items' => 'required|array|min:1',
        'items.*.description' => 'required|string|max:255',
        'items.*.quantity' => 'required|numeric|min:0.01',
        'items.*.unit_price' => 'required|numeric|min:0',
        'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
        'discount_percentage' => 'nullable|numeric|min:0|max:100',
        'discount_amount' => 'nullable|numeric|min:0',
        'notes' => 'nullable|string',
        'terms_conditions' => 'nullable|string',
        'is_cash_sale' => 'nullable|boolean',
        'cash_received' => 'nullable|numeric|min:0'
    ]);

    try {
        DB::beginTransaction();

        // Calcular totais
        $totals = $this->calculateInvoiceTotals($validated['items']);
        
        // Calcular desconto
        $discountAmount = $validated['discount_amount'] ?? 0;
        if (($validated['discount_percentage'] ?? 0) > 0) {
            $discountAmount = ($totals['subtotal'] + $totals['tax_amount']) * ($validated['discount_percentage'] / 100);
        }
        
        $total = $totals['subtotal'] + $totals['tax_amount'] - $discountAmount;

        // Calcular data de vencimento
        $paymentTermsDays = (int) $validated['payment_terms_days'];
        $dueDate = Carbon::parse($validated['invoice_date'])->addDays($paymentTermsDays);

        // Criar fatura
        $invoiceData = [
            'client_id' => $validated['client_id'],
            'quote_id' => $validated['quote_id'] ?? null,
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $dueDate,
            'payment_terms_days' => $paymentTermsDays,
            'payment_method' => $validated['payment_method'],
            'subtotal' => $totals['subtotal'],
            'tax_amount' => $totals['tax_amount'],
            'discount_percentage' => $validated['discount_percentage'] ?? 0,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'status' => 'draft',
            'notes' => $validated['notes'],
            'terms_conditions' => $validated['terms_conditions'],
            'document_type' => Invoice::TYPE_INVOICE
        ];

        // Se é venda à dinheiro
        if ($validated['is_cash_sale'] ?? false) {
            $cashReceived = $validated['cash_received'] ?? $total;
            
            if ($cashReceived < $total) {
                throw new \Exception('Valor recebido insuficiente para venda à dinheiro');
            }

            $invoiceData['is_cash_sale'] = true;
            $invoiceData['cash_received'] = $cashReceived;
            $invoiceData['change_given'] = $cashReceived - $total;
            $invoiceData['paid_amount'] = $total;
            $invoiceData['status'] = 'paid';
            $invoiceData['paid_at'] = now();
        }

        $invoice = Invoice::create($invoiceData);

        // Criar itens da fatura
        foreach ($validated['items'] as $itemData) {
            $this->createInvoiceItem($invoice, $itemData);
        }

        DB::commit();

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Fatura criada com sucesso!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()
            ->with('error', 'Erro ao criar fatura: ' . $e->getMessage());
    }
}
```

## 7. Dashboard Updates

```php
// Adicionar ao DashboardController para mostrar estatísticas

public function index()
{
    $stats = [
        // ... estatísticas existentes ...
        
        // Novas estatísticas
        'cash_sales_today' => Invoice::cashSales()
            ->whereDate('created_at', today())
            ->sum('total'),
            
        'credit_notes_month' => Invoice::creditNotes()
            ->whereMonth('created_at', now()->month)
            ->sum('total'),
            
        'debit_notes_month' => Invoice::debitNotes()
            ->whereMonth('created_at', now()->month)
            ->sum('total'),
            
        'discount_given_month' => Invoice::whereMonth('created_at', now()->month)
            ->sum('discount_amount')
    ];

    return view('dashboard', compact('stats'));
}
```

## Considerações Finais

Esta implementação adiciona:

1. **Desconto Comercial**: 
   - Percentual ou valor fixo
   - Aplicado antes do total final
   - Disponível em faturas e cotações

2. **Venda à Dinheiro**:
   - Interface simplificada para vendas rápidas
   - Cálculo automático de troco
   - Fatura automaticamente marcada como paga

3. **Nota de Crédito**:
   - Para devoluções ou ajustes negativos
   - Vinculada à fatura original
   - Atualiza saldo da fatura relacionada

4. **Nota de Débito**:
   - Para cobranças adicionais
   - Pode ser vinculada a uma fatura existente
   - Segue o fluxo normal de cobrança

As funcionalidades estão integradas ao sistema existente, mantendo a consistência e aproveitando a estrutura já criada.