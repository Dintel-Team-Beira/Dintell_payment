<?php

namespace App\Models;

use App\Scopes\CompanyScope;
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
        'company_nuit',
        'company_id', // Adicionar para multi-tenancy

        //NEW FIELDS
        'receipt_prefix',
        'receipt_next_number',
        'receipt_number_length',

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
        $company = auth()->user()->company;
        return self::firstOrCreate(
            ['company_id' => $company ? $company->id : null],
            [
                'company_name' => $company->name,
                'company_address' => $company->address,
                'company_phone' => $company->phone,
                'company_email' => $company->email,
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

    /**
     * Gerar próximo número de recibo
     */
    public function getNextReceiptNumber()
    {
        $company = auth()->user()->company;
        $prefix = $this->receipt_prefix ?? strtoupper(strtok($this->company_name, ' ')) . '-REC';//$this->company_name.'-'.'REC';
        $nextNumber = $this->receipt_next_number ?? 1;
        $length = $this->receipt_number_length ?? 6;

        $receiptNumber = $prefix . str_pad($nextNumber, $length, '0', STR_PAD_LEFT);

        // Incrementar para próximo uso
        $this->increment('receipt_next_number');

        return $receiptNumber;
    }

    /**
     * Resetar numeração de recibos
     */
    public function resetReceiptNumbers($startNumber = 1)
    {
        $this->update(['receipt_next_number' => $startNumber]);
    }
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }
}
