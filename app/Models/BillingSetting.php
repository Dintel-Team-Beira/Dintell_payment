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
                'company_name' => 'Minha Empresa',
                'company_address' => 'Endereço da Empresa',
                'company_phone' => '123456789',
                'company_email' => '',
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
     protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }
}
