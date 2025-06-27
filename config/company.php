
<?php
// config/company.php

return [
    'name' => env('COMPANY_NAME', 'SUA EMPRESA, LDA'),
    'nuit' => env('COMPANY_NUIT', '123456789'),
    'address' => env('COMPANY_ADDRESS', 'Av. Principal nº 123, R/C'),
    'city' => env('COMPANY_CITY', 'Maputo'),
    'country' => env('COMPANY_COUNTRY', 'Moçambique'),
    'phone' => env('COMPANY_PHONE', '+258 84 123 4567'),
    'email' => env('COMPANY_EMAIL', 'geral@empresa.co.mz'),
    'website' => env('COMPANY_WEBSITE', 'www.empresa.co.mz'),
    'logo_url' => env('COMPANY_LOGO_URL', ''),
    'tagline' => env('COMPANY_TAGLINE', 'beyond technology, intelligence.'),

    // Informações bancárias
    'bank' => env('COMPANY_BANK', 'BCI'),
    'bank_account' => env('COMPANY_BANK_ACCOUNT', '222 038 724 100 01'),
    'bank_nib' => env('COMPANY_BANK_NIB', '0008 0000 2203 8724 101 13'),

    // Configurações de documentos
    'quote_prefix' => env('QUOTE_PREFIX', 'COT'),
    'invoice_prefix' => env('INVOICE_PREFIX', 'FAT'),
    'default_tax_rate' => env('DEFAULT_TAX_RATE', 17.00),
    'default_quote_validity_days' => env('DEFAULT_QUOTE_VALIDITY_DAYS', 30),
    'default_invoice_due_days' => env('DEFAULT_INVOICE_DUE_DAYS', 30),
];
