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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                $settings = BillingSetting::getSettings();
                $invoice->invoice_number = $settings->getNextInvoiceNumber();

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
