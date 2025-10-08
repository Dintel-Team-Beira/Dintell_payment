<?php

namespace App\Http\Controllers;

use App\Helpers\DocumentTemplateHelper;
use App\Models\Company;
use App\Models\DocumentTemplate;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\CreditNote;
use App\Models\DebitNote;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TemplatePreviewController extends Controller
{
    /**
     * Listar templates por tipo
     */
    public function list(string $tenant,$type)
    {
        $companyId = auth()->user()->company_id;

        $templates = DocumentTemplate::where('type', $type)
            ->where('company_id', $companyId)
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get(['id', 'name', 'is_default', 'is_selected', 'created_at', 'updated_at']);
        // retornar os defaults para todas empresas se não houver nenhum personalizado
        if($templates->isEmpty()){
            $templates = DocumentTemplate::where('type', $type)
            ->where('is_default', true)
            ->get(['id', 'name', 'is_default', 'is_selected', 'created_at', 'updated_at']);
        }
        return response()->json($templates);
    }

    /**
     * 🆕 Visualizar template como HTML (otimizado)
     */
    public function show(string $tenant, $templateId)
    {
        // dd($tenant);
        $company = Company::where('slug', $tenant)->firstOrFail();
        // dd($company);
        $template = DocumentTemplate::findOrFail($templateId);
        $data = $this->getTemplateData($template, $company);

        return DocumentTemplateHelper::previewInBrowser($template, $data);
    }

    /**
     * 🆕 Download do template como PDF (otimizado)
     */
    public function download(string $tenant, $templateId)
    {
        $company = Company::where('slug', $tenant)->firstOrFail();
        $template = DocumentTemplate::findOrFail($templateId);
        $data = $this->getTemplateData($template, $company);

        //  USAR O HELPER PARA PDF com nome personalizado
        $options = [
            'filename' => $this->generateFileName($template, $data),
            'enable_cache' => true,
             'for_pdf' => true, 
            'enable_logging' => false,
            'paper_size' => 'A4',
            'orientation' => 'portrait'
        ];

        return DocumentTemplateHelper::downloadPdfDocument($template, $data, $options);
    }

    /**
     * 🆕 Método unificado para buscar dados do template
     */
    /*
    private function getTemplateData(DocumentTemplate $template): array
    {
        $company = $template->company;
        $data = ['company' => $company];

        switch ($template->type) {
            case 'invoice':
                $invoice = Invoice::with(['client', 'items'])
                    ->where('company_id', $company->id)
                    ->first();
                
                if (!$invoice) {
                    $invoice = $this->createSampleInvoice($company);
                }
                $data['invoice'] = $invoice;
                break;

            case 'quote':
                $quote = Quote::with(['client', 'items'])
                    ->where('company_id', $company->id)
                    ->first();
                
                if (!$quote) {
                    $quote = $this->createSampleQuote($company);
                }
                $data['quote'] = $quote;
                break;

            case 'credit_note':
            case 'credit': // Manter compatibilidade
                $creditNote = $this->getCreditNote($company->id);
                if (!$creditNote) {
                    $creditNote = $this->createSampleCreditNote($company);
                }
                $data['creditNote'] = $creditNote;
                break;

            case 'debit_note':
            case 'debit': // Manter compatibilidade
                $debitNote = $this->getDebitNote($company->id);
                if (!$debitNote) {
                    $debitNote = $this->createSampleDebitNote($company);
                }
                $data['debitNote'] = $debitNote;
                break;
            case 'receipt':
                $receipt = $this->getReceipt($company->id);
                $data['receipt'] = $receipt;
                break;
            default:
                // Para tipos não mapeados, retornar apenas company
                break;
        }

        return $data;
    }
    */
private function getTemplateData(DocumentTemplate $template, Company $company): array
    {
        // $company = $comp;
        $data = ['company' => $company];
        // dd($company);
        switch ($template->type) {
            case 'invoice':
                $invoice = Invoice::with(['client', 'items'])
                    ->where('company_id', $company->id)
                    ->first();
                
                if (!$invoice) {
                    $invoice = $this->createSampleInvoice($company);
                }
                $data['invoice'] = $invoice;
                break;

            case 'quote':
                $quote = Quote::with(['client', 'items'])
                    ->where('company_id', $company->id)
                    ->first();
                
                if (!$quote) {
                    $quote = $this->createSampleQuote($company);
                }
                $data['quote'] = $quote;
                break;

            case 'credit_note':
            case 'credit': // Manter compatibilidade
                $creditNote = $this->getCreditNote($company->id);
                if (!$creditNote) {
                    $creditNote = $this->createSampleCreditNote($company);
                }
                $data['creditNote'] = $creditNote;
                break;

            case 'debit_note':
            case 'debit': // Manter compatibilidade
                $debitNote = $this->getDebitNote($company->id);
                if (!$debitNote) {
                    $debitNote = $this->createSampleDebitNote($company);
                }
                $data['debitNote'] = $debitNote;
                break;
            case 'receipt':
                $receipt = $this->getReceipt($company->id);
                $data['receipt'] = $receipt;
                break;
            default:
                // Para tipos não mapeados, retornar apenas company
                break;
        }

        return $data;
    }
    /**
     * 🆕 Buscar Credit Note (flexível para diferentes models)
     */
    private function getCreditNote($companyId)
    {
        // Opção 2: Se usa Invoice com document_type
        return Invoice::with(['client', 'items'])
            ->where('company_id', $companyId)
            ->where('document_type', Invoice::TYPE_CREDIT_NOTE)
            ->first();
    }

    /**
     * 🆕 Buscar Debit Note (flexível para diferentes models)
     */
    private function getDebitNote($companyId)
    {
        // Opção 2: Se usa Invoice com document_type
        return Invoice::with(['client', 'items'])
            ->where('company_id', $companyId)
            ->where('document_type', Invoice::TYPE_DEBIT_NOTE)
            ->first();
    }

    /**
     * Buscar Receipt 
     */
    private function getReceipt($companyId)
    {
        return Receipt::where('company_id', $companyId)
            ->first();
    }
    /**
     * 🆕 Gerar nome do arquivo baseado no template e dados
     */
    private function generateFileName(DocumentTemplate $template, array $data): string
    {
        $prefix = ucfirst($template->type);
        $companyName = $data['company']->name ?? 'Template';
        $timestamp = now()->format('Y-m-d');

        // Sanitizar nome da empresa
        $safeName = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $companyName);
        
        return "{$prefix}_{$safeName}_{$timestamp}";
    }

    /**
     * 🆕 API para obter apenas o HTML (útil para modais/AJAX)
     */
    public function getHtml($templateId)
    {
        $template = DocumentTemplate::findOrFail($templateId);
        $data = $this->getTemplateData($template);

        $html = DocumentTemplateHelper::renderHtmlForBrowser($template, $data);
        
        return response()->json([
            'success' => true,
            'html' => $html,
            'template_name' => $template->name
        ]);
    }

    /**
     * 🆕 Preview com opções (útil para diferentes formatos)
     */
    public function preview($templateId, Request $request)
    {
        $template = DocumentTemplate::findOrFail($templateId);
        $data = $this->getTemplateData($template);
        
        $format = $request->get('format', 'html'); // html ou pdf

        switch ($format) {
            case 'pdf':
                return DocumentTemplateHelper::downloadPdfDocument($template, $data, [
                    'filename' => $this->generateFileName($template, $data)
                ]);
                
            case 'html':
            default:
                return DocumentTemplateHelper::previewInBrowser($template, $data);
        }
    }

    /**
     * Selecionar template (mantido como estava)
     */
    public function selectTemplate(Request $request, $idTemplate)
    {
        $template = DocumentTemplate::findOrFail($idTemplate);
        $companyId = auth()->user()->company_id;

        // Desmarcar todos os outros templates como selecionados
        DocumentTemplate::where('company_id', $companyId)
            ->where('type', $template->type)
            ->where('is_selected', true)
            ->update(['is_selected' => false]);

        // Marcar o template atual como selecionado
        $template->is_selected = true;
        $template->save();

        return response()->json(['message' => 'Template selecionado com sucesso.']);
    }

    /**
     * 🆕 Validar template antes do uso
     */
    public function validator($templateId)
    {
        try {
            $template = DocumentTemplate::findOrFail($templateId);
            $data = $this->getTemplateData($template);

            // Tentar renderizar para verificar erros
            $html = DocumentTemplateHelper::renderHtmlForBrowser($template, $data);

            return response()->json([
                'success' => true,
                'message' => 'Template válido',
                'template_name' => $template->name
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro no template: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 400);
        }
    }

    // 🔧 MÉTODOS DE SAMPLE DATA (você pode manter os existentes)
    
    /**
     * Criar invoice de exemplo (implementar conforme sua necessidade)
     */
    private function createSampleInvoice($company)
    {
        // Implementar criação de dados de exemplo
        // ou retornar um objeto mock
        return (object)[
            'invoice_number' => 'INV-SAMPLE-001',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => 1000.00,
            'tax_amount' => 170.00,
            'total' => 1170.00,
            'client' => (object)[
                'name' => 'Cliente de Exemplo',
                'email' => 'cliente@exemplo.com',
                'phone' => '+258 84 123 4567',
                'nuit' => '123456789',
                'address' => 'Maputo, Moçambique'
            ],
            'items' => [
                (object)[
                    'description' => 'Produto/Serviço de Exemplo',
                    'quantity' => 1,
                    'unit_price' => 1000.00,
                    'tax_rate' => 17.00
                ]
            ]
        ];
    }

    /**
     * Criar quote de exemplo
     */
    private function createSampleQuote($company)
    {
        return (object)[
            'quote_number' => 'COT-SAMPLE-001',
            'quote_date' => now(),
            'valid_until' => now()->addDays(30),
            'subtotal' => 1000.00,
            'tax_amount' => 170.00,
            'total' => 1170.00,
            'client' => (object)[
                'name' => 'Cliente de Exemplo',
                'email' => 'cliente@exemplo.com',
                'phone' => '+258 84 123 4567',
                'nuit' => '123456789',
                'address' => 'Maputo, Moçambique'
            ],
            'items' => [
                (object)[
                    'description' => 'Produto/Serviço de Exemplo',
                    'quantity' => 1,
                    'unit_price' => 1000.00,
                    'tax_rate' => 17.00
                ]
            ]
        ];
    }

    /**
     * Criar credit note de exemplo
     */
    private function createSampleCreditNote($company)
    {
        return (object)[
            'credit_note_number' => 'CN-SAMPLE-001',
            'invoice_date' => now(),
            'subtotal' => 1000.00,
            'tax_amount' => 170.00,
            'total' => 1170.00,
            'adjustment_reason' => 'Devolução de mercadoria',
            'client' => (object)[
                'name' => 'Cliente de Exemplo',
                'email' => 'cliente@exemplo.com',
                'phone' => '+258 84 123 4567',
                'nuit' => '123456789',
                'address' => 'Maputo, Moçambique'
            ],
            'items' => [
                (object)[
                    'description' => 'Devolução - Produto/Serviço',
                    'quantity' => 1,
                    'unit_price' => 1000.00,
                    'tax_rate' => 17.00
                ]
            ]
        ];
    }

    /**
     * Criar debit note de exemplo
     */
    private function createSampleDebitNote($company)
    {
        return (object)[
            'invoice_number' => 'DN-SAMPLE-001', // Para nota de débito
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => 500.00,
            'tax_amount' => 85.00,
            'total' => 585.00,
            'adjustment_reason' => 'Cobrança adicional por serviços extras',
            'client' => (object)[
                'name' => 'Cliente de Exemplo',
                'email' => 'cliente@exemplo.com',
                'phone' => '+258 84 123 4567',
                'nuit' => '123456789',
                'address' => 'Maputo, Moçambique'
            ],
            'relatedInvoice' => (object)[
                'invoice_number' => 'INV-2024-001',
                'invoice_date' => now()->subDays(10),
                'total' => 2000.00
            ],
            'items' => [
                (object)[
                    'description' => 'Cobrança adicional',
                    'quantity' => 1,
                    'unit_price' => 500.00,
                    'tax_rate' => 17.00
                ]
            ]
        ];
    }
}