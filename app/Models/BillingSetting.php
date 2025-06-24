<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_address',
        'tax_number',
        'invoice_prefix',
        'quote_prefix',
        'next_invoice_number',
        'next_quote_number',
        'default_tax_rate'
    ];

    protected $casts = [
        'default_tax_rate' => 'decimal:2'
    ];

    public static function getSettings()
    {
        return self::first() ?? self::create([
            'company_name' => 'Sua Empresa',
            'company_address' => 'Endereço da empresa',
            'invoice_prefix' => 'FAT',
            'quote_prefix' => 'COT',
            'next_invoice_number' => 1,
            'next_quote_number' => 1,
            'default_tax_rate' => 17.00
        ]);
    }

    public function getNextInvoiceNumber()
    {
        $number = $this->next_invoice_number;
        $this->increment('next_invoice_number');
        return $this->invoice_prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function getNextQuoteNumber()
    {
        $number = $this->next_quote_number;
        $this->increment('next_quote_number');
        return $this->quote_prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}