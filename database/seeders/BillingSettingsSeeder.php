<?php

// Seeder para configurações de faturação
// database/seeders/BillingSettingsSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BillingSetting;
use App\Models\DocumentTemplate;

class BillingSettingsSeeder extends Seeder
{
    public function run()
    {
        // Criar configurações padrão
        BillingSetting::updateOrCreate(
            ['id' => 1],
            [
                'company_name' => 'Sua Empresa Lda',
                'company_address' => 'Rua Principal, 123' . PHP_EOL . 'Maputo, Moçambique',
                'tax_number' => '400000000',
                'invoice_prefix' => 'FAT',
                'quote_prefix' => 'COT',
                'next_invoice_number' => 1,
                'next_quote_number' => 1,
                'default_tax_rate' => 17.00
            ]
        );

        // Template padrão para faturas
        DocumentTemplate::updateOrCreate(
            ['type' => 'invoice', 'is_default' => true],
            [
                'name' => 'Template Padrão - Fatura',
                'type' => 'invoice',
                'html_template' => $this->getInvoiceTemplate(),
                'css_styles' => json_encode($this->getDefaultStyles()),
                'is_default' => true
            ]
        );

        // Template padrão para cotações
        DocumentTemplate::updateOrCreate(
            ['type' => 'quote', 'is_default' => true],
            [
                'name' => 'Template Padrão - Cotação',
                'type' => 'quote',
                'html_template' => $this->getQuoteTemplate(),
                'css_styles' => json_encode($this->getDefaultStyles()),
                'is_default' => true
            ]
        );
    }

    private function getInvoiceTemplate()
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>FATURA</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; color: #333; }
                .header { border-bottom: 3px solid #2563eb; padding-bottom: 20px; margin-bottom: 30px; }
                .company-info { float: left; width: 50%; }
                .company-info h1 { color: #2563eb; margin: 0 0 10px 0; font-size: 24px; }
                .document-info { float: right; width: 45%; text-align: right; }
                .document-info h2 { color: #2563eb; margin: 0 0 15px 0; font-size: 28px; }
                .client-info { clear: both; margin: 30px 0; padding: 20px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #2563eb; }
                .client-info h3 { margin: 0 0 15px 0; color: #2563eb; }
                .items-table { width: 100%; border-collapse: collapse; margin: 30px 0; }
                .items-table th { background: #2563eb; color: white; padding: 12px; text-align: left; }
                .items-table td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
                .items-table .number { text-align: right; }
                .totals { float: right; width: 350px; margin-top: 20px; }
                .totals table { width: 100%; }
                .totals td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; }
                .totals .label { font-weight: 600; }
                .totals .amount { text-align: right; font-weight: 600; }
                .total-final { background: #f3f4f6; border-top: 2px solid #2563eb; }
                .total-final td { font-size: 18px; font-weight: bold; color: #2563eb; padding: 12px; }
                .footer { clear: both; margin-top: 60px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
                .footer h4 { color: #2563eb; margin-bottom: 10px; }
                .clearfix::after { content: ""; display: table; clear: both; }
                .status-paid { background: #10b981; color: white; padding: 5px 10px; border-radius: 4px; font-size: 12px; }
                .status-overdue { background: #ef4444; color: white; padding: 5px 10px; border-radius: 4px; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="clearfix header">
                <div class="company-info">
                    <h1>{{settings.company_name}}</h1>
                    <p>{{settings.company_address}}</p>
                    <p><strong>NUIT:</strong> {{settings.tax_number}}</p>
                </div>
                <div class="document-info">
                    <h2>FATURA</h2>
                    <p><strong>Número:</strong> {{invoice.invoice_number}}</p>
                    <p><strong>Data:</strong> {{invoice.invoice_date}}</p>
                    <p><strong>Vencimento:</strong> {{invoice.due_date}}</p>
                    {{invoice_status}}
                </div>
            </div>

            <div class="client-info">
                <h3>Faturar a:</h3>
                <p><strong>{{invoice.client.name}}</strong></p>
                <p>{{invoice.client.address}}</p>
                <p><strong>Email:</strong> {{invoice.client.email}}</p>
                <p><strong>Telefone:</strong> {{invoice.client.phone}}</p>
                {{client_tax_number}}
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th class="number">Qtd</th>
                        <th class="number">Preço Unit.</th>
                        <th class="number">IVA (%)</th>
                        <th class="number">Total</th>
                    </tr>
                </thead>
                <tbody>
                    {{invoice_items}}
                </tbody>
            </table>

            <div class="totals">
                <table>
                    <tr>
                        <td class="label">Subtotal:</td>
                        <td class="amount">{{invoice.subtotal}} MT</td>
                    </tr>
                    <tr>
                        <td class="label">IVA:</td>
                        <td class="amount">{{invoice.tax_amount}} MT</td>
                    </tr>
                    <tr class="total-final">
                        <td class="label">TOTAL:</td>
                        <td class="amount">{{invoice.total}} MT</td>
                    </tr>
                </table>
            </div>

            <div class="footer">
                {{invoice_notes}}
                {{invoice_terms}}

                <div style="margin-top: 40px; text-align: center; color: #6b7280; font-size: 12px;">
                    <p>Esta fatura foi gerada eletronicamente e é válida sem assinatura.</p>
                    <p>Para questões sobre esta fatura, contacte-nos através dos dados acima.</p>
                </div>
            </div>
        </body>
        </html>';
    }

    private function getQuoteTemplate()
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>COTAÇÃO</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; color: #333; }
                .header { border-bottom: 3px solid #059669; padding-bottom: 20px; margin-bottom: 30px; }
                .company-info { float: left; width: 50%; }
                .company-info h1 { color: #059669; margin: 0 0 10px 0; font-size: 24px; }
                .document-info { float: right; width: 45%; text-align: right; }
                .document-info h2 { color: #059669; margin: 0 0 15px 0; font-size: 28px; }
                .client-info { clear: both; margin: 30px 0; padding: 20px; background: #f0fdf4; border-radius: 8px; border-left: 4px solid #059669; }
                .client-info h3 { margin: 0 0 15px 0; color: #059669; }
                .items-table { width: 100%; border-collapse: collapse; margin: 30px 0; }
                .items-table th { background: #059669; color: white; padding: 12px; text-align: left; }
                .items-table td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
                .items-table .number { text-align: right; }
                .totals { float: right; width: 350px; margin-top: 20px; }
                .totals table { width: 100%; }
                .totals td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; }
                .totals .label { font-weight: 600; }
                .totals .amount { text-align: right; font-weight: 600; }
                .total-final { background: #f3f4f6; border-top: 2px solid #059669; }
                .total-final td { font-size: 18px; font-weight: bold; color: #059669; padding: 12px; }
                .footer { clear: both; margin-top: 60px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
                .footer h4 { color: #059669; margin-bottom: 10px; }
                .clearfix::after { content: ""; display: table; clear: both; }
                .validity-notice { background: #fef3c7; padding: 15px; border-radius: 8px; border-left: 4px solid #f59e0b; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="clearfix header">
                <div class="company-info">
                    <h1>{{settings.company_name}}</h1>
                    <p>{{settings.company_address}}</p>
                    <p><strong>NUIT:</strong> {{settings.tax_number}}</p>
                </div>
                <div class="document-info">
                    <h2>COTAÇÃO</h2>
                    <p><strong>Número:</strong> {{quote.quote_number}}</p>
                    <p><strong>Data:</strong> {{quote.quote_date}}</p>
                    <p><strong>Válida até:</strong> {{quote.valid_until}}</p>
                </div>
            </div>

            <div class="client-info">
                <h3>Cotação para:</h3>
                <p><strong>{{quote.client.name}}</strong></p>
                <p>{{quote.client.address}}</p>
                <p><strong>Email:</strong> {{quote.client.email}}</p>
                <p><strong>Telefone:</strong> {{quote.client.phone}}</p>
            </div>

            <div class="validity-notice">
                <strong>⏰ Validade:</strong> Esta cotação é válida até {{quote.valid_until}}. Após esta data, os preços podem ser revistos.
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th class="number">Qtd</th>
                        <th class="number">Preço Unit.</th>
                        <th class="number">IVA (%)</th>
                        <th class="number">Total</th>
                    </tr>
                </thead>
                <tbody>
                    {{quote_items}}
                </tbody>
            </table>

            <div class="totals">
                <table>
                    <tr>
                        <td class="label">Subtotal:</td>
                        <td class="amount">{{quote.subtotal}} MT</td>
                    </tr>
                    <tr>
                        <td class="label">IVA:</td>
                        <td class="amount">{{quote.tax_amount}} MT</td>
                    </tr>
                    <tr class="total-final">
                        <td class="label">TOTAL:</td>
                        <td class="amount">{{quote.total}} MT</td>
                    </tr>
                </table>
            </div>

            <div class="footer">
                {{quote_notes}}
                {{quote_terms}}

                <div style="margin-top: 40px; text-align: center; color: #6b7280; font-size: 12px;">
                    <p>Esta cotação foi gerada eletronicamente.</p>
                    <p>Para aceitar esta cotação ou esclarecer dúvidas, contacte-nos através dos dados acima.</p>
                </div>
            </div>
        </body>
        </html>';
    }

    private function getDefaultStyles()
    {
        return [
            'primary_color' => '#2563eb',
            'success_color' => '#059669',
            'warning_color' => '#f59e0b',
            'danger_color' => '#ef4444',
            'font_family' => 'Arial, sans-serif',
            'font_size' => '14px'
        ];
    }
}