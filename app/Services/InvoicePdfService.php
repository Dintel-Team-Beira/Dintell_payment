<?php
// Serviço para geração de PDF
// app/Services/InvoicePdfService.php
namespace App\Services;

use App\Models\Invoice;
use App\Models\Quote;
use App\Models\BillingSetting;
use App\Models\DocumentTemplate;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfService
{
    public function generateInvoicePdf(Invoice $invoice)
    {
        $invoice->load('client', 'items');
        $settings = BillingSetting::getSettings();
        $template = $this->getTemplate('invoice');

        $html = $this->renderTemplate($template, [
            'invoice' => $invoice,
            'settings' => $settings,
            'type' => 'Fatura'
        ]);

        return Pdf::loadHTML($html)
            ->setPaper('a4')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isRemoteEnabled' => true
            ]);
    }

    public function generateQuotePdf(Quote $quote)
    {
        $quote->load('client', 'items');
        $settings = BillingSetting::getSettings();
        $template = $this->getTemplate('quote');

        $html = $this->renderTemplate($template, [
            'document' => $quote,
            'settings' => $settings,
            'type' => 'Cotação'
        ]);

        return Pdf::loadHTML($html)
            ->setPaper('a4')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isRemoteEnabled' => true
            ]);
    }

    private function getTemplate(string $type)
    {
        $template = DocumentTemplate::where('type', $type)
            ->where('is_default', true)
            ->first();

        if (!$template) {
            return $this->getDefaultTemplate($type);
        }

        return $template->html_template;
    }

    private function renderTemplate(string $template, array $data)
    {
        // Substituir variáveis no template
        $html = $template;

        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $html = $this->replaceObjectVars($html, $key, $value);
            } else {
                $html = str_replace("{{" . $key . "}}", $value, $html);
            }
        }

        return $html;
    }

    private function replaceObjectVars(string $html, string $prefix, $object)
    {
        $reflection = new \ReflectionClass($object);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);
            $html = str_replace("{{" . $prefix . "." . $property->getName() . "}}", $value, $html);
        }

        // Substituir atributos usando métodos getter
        if (method_exists($object, 'toArray')) {
            $attributes = $object->toArray();
            foreach ($attributes as $key => $value) {
                $html = str_replace("{{" . $prefix . "." . $key . "}}", $value, $html);
            }
        }

        return $html;
    }

    private function getDefaultTemplate(string $type)
    {
        $title = $type === 'invoice' ? 'FATURA' : 'COTAÇÃO';

        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>' . $title . '</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; color: #333; }
                .header { border-bottom: 2px solid #007bff; padding-bottom: 20px; margin-bottom: 30px; }
                .company-info { float: left; width: 50%; }
                .document-info { float: right; width: 45%; text-align: right; }
                .client-info { clear: both; margin: 30px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; }
                .items-table { width: 100%; border-collapse: collapse; margin: 30px 0; }
                .items-table th, .items-table td { border: 1px solid #dee2e6; padding: 10px; text-align: left; }
                .items-table th { background: #007bff; color: white; }
                .totals { float: right; width: 300px; margin-top: 20px; }
                .totals table { width: 100%; }
                .totals td { padding: 8px; border-bottom: 1px solid #dee2e6; }
                .total-final { font-weight: bold; font-size: 1.2em; background: #f8f9fa; }
                .footer { clear: both; margin-top: 50px; padding-top: 20px; border-top: 1px solid #dee2e6; font-size: 0.9em; color: #666; }
                .clearfix::after { content: ""; display: table; clear: both; }
            </style>
        </head>
        <body>
            <div class="clearfix header">
                <div class="company-info">
                    <h1>{{settings.company_name}}</h1>
                    <p>{{settings.company_address}}</p>
                    <p>NUIT: {{settings.tax_number}}</p>
                </div>
                <div class="document-info">
                    <h2>' . $title . '</h2>
                    <p><strong>Número:</strong> {{document.invoice_number}}{{document.quote_number}}</p>
                    <p><strong>Data:</strong> {{document.invoice_date}}{{document.quote_date}}</p>
                    <p><strong>Vencimento:</strong> {{document.due_date}}{{document.valid_until}}</p>
                </div>
            </div>

            <div class="client-info">
                <h3>Cliente:</h3>
                <p><strong>{{document.client.name}}</strong></p>
                <p>{{document.client.address}}</p>
                <p>Email: {{document.client.email}}</p>
                <p>Telefone: {{document.client.phone}}</p>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th>Qtd</th>
                        <th>Preço Unit.</th>
                        <th>IVA (%)</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    {{items_rows}}
                </tbody>
            </table>

            <div class="totals">
                <table>
                    <tr>
                        <td>Subtotal:</td>
                        <td>{{document.subtotal}} MT</td>
                    </tr>
                    <tr>
                        <td>IVA:</td>
                        <td>{{document.tax_amount}} MT</td>
                    </tr>
                    <tr class="total-final">
                        <td>TOTAL:</td>
                        <td>{{document.total}} MT</td>
                    </tr>
                </table>
            </div>

            <div class="footer">
                <p><strong>Observações:</strong></p>
                <p>{{document.notes}}</p>

                <p><strong>Termos e Condições:</strong></p>
                <p>{{document.terms_conditions}}</p>
            </div>
        </body>
        </html>';
    }
}