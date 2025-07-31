<?php

return [
    'name' => env('COMPANY_NAME', 'DINTELL, LDA'),
    'nuit' => env('COMPANY_NUIT', '401170839'),
    'address' => env('COMPANY_ADDRESS', 'Av. Maguiguana nº 137 R/C'),
    'city' => env('COMPANY_CITY', 'Maputo'),
    'country' => env('COMPANY_COUNTRY', 'Moçambique'),
    'phone' => env('COMPANY_PHONE', '866713342'),
    'email' => env('COMPANY_EMAIL', 'comercial@dintell.co.mz'),
    'website' => env('COMPANY_WEBSITE', 'www.dintell.co.mz'),
    'logo_url' => env('COMPANY_LOGO_URL', ''),
    'tagline' => env('COMPANY_TAGLINE', 'beyond technology, intelligence.'),
    'slug' => env('COMPANY_SLUG', 'dintell'),

    // Informações bancárias
    'bank' => env('COMPANY_BANK', 'BCI'),
    'bank_account' => env('COMPANY_BANK_ACCOUNT', '222 038 724 100 01'),
    'bank_nib' => env('COMPANY_BANK_NIB', '0008 0000 2203 8724 101 13'),

    // Configurações de documentos
    'quote_prefix' => env('QUOTE_PREFIX', 'COT'),
    'invoice_prefix' => env('INVOICE_PREFIX', 'FAT'),
    'default_tax_rate' => env('DEFAULT_TAX_RATE', 16.00), // Atualizado para 16% conforme a fatura
    'default_quote_validity_days' => env('DEFAULT_QUOTE_VALIDITY_DAYS', 30),
    'default_invoice_due_days' => env('DEFAULT_INVOICE_DUE_DAYS', 30),
];
