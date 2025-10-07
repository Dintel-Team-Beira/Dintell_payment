<?php

namespace App\Http\Controllers;

use App\Helpers\DocumentTemplateHelper;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\BillingSetting;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebitNoteController extends Controller
{
    public function index(Request $request)
    {
        $debitNotes = Invoice::where('document_type', Invoice::TYPE_DEBIT_NOTE)
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

        // Itens pré-definidos mais comuns para notas de débito
        $commonDebitItems = $this->getCommonDebitItems();

        return view('debit-notes.create', compact('invoice', 'clients', 'commonDebitItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'related_invoice_id' => 'nullable|exists:invoices,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'debit_type' => 'required|string',
            'debit_reason' => 'required|string',
            'base_amount' => 'required|numeric|min:0',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'fixed_amount' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string'
        ]);
        $validated['company_id'] = auth()->user()->company->id;
        try {
            DB::beginTransaction();

            $settings = BillingSetting::getSettings();

            // Calcular valores baseado no tipo de débito
            $calculatedValues = $this->calculateDebitValues($validated);

            // Criar nota de débito
            $debitNote = Invoice::create([
                'invoice_number' => $settings->getNextDebitNoteNumber(),
                'document_type' => Invoice::TYPE_DEBIT_NOTE,
                'client_id' => $validated['client_id'],
                'related_invoice_id' => $validated['related_invoice_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $calculatedValues['subtotal'],
                'tax_amount' => $calculatedValues['tax_amount'],
                'total' => $calculatedValues['total'],
                'status' => 'sent',
                'adjustment_reason' => $this->getDebitReasonText($validated['debit_type'], $validated['debit_reason']),
                'notes' => $validated['notes'],
                'payment_method' => Invoice::PAYMENT_OTHER ?? 'other'
            ]);

            // Criar item único baseado no tipo de débito
            $debitNote->items()->create([
                'description' => $this->getDebitItemDescription($validated['debit_type'], $validated['debit_reason']),
                'quantity' => 1,
                'unit_price' => $calculatedValues['subtotal'],
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'total_price' => $calculatedValues['subtotal']
            ]);

            // Se está relacionada a uma fatura, atualizar o valor devido
            if ($debitNote->related_invoice_id) {
                $relatedInvoice = Invoice::find($debitNote->related_invoice_id);
                if ($relatedInvoice) {
                    // Adicionar o valor do débito ao total da fatura original
                    $relatedInvoice->total += $calculatedValues['total'];
                    if ($relatedInvoice->status === 'paid') {
                        $relatedInvoice->status = 'sent'; // Voltar para enviada pois há valor adicional
                    }
                    $relatedInvoice->save();
                }
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

    public function show(string $tenant, Invoice $debitNote)
    {
        if (!$debitNote->isDebitNote()) {
            abort(404);
        }

        $debitNote->load(['client', 'items', 'relatedInvoice']);

        return view('debit-notes.show', compact('debitNote'));
    }

    public function downloadPdf(string $tenant,Invoice $debitNote)
    {
        if (!$debitNote->isDebitNote()) {
            abort(404);
        }

        $debitNote->load(['client', 'items', 'relatedInvoice']);
        $settings = BillingSetting::getSettings();
        $company = auth()->user()->company;
        $pdf = app('dompdf.wrapper');

        $template = DocumentTemplate::where('company_id', $company->id)->where('type','debit')->where('is_selected',true)->first();

        return DocumentTemplateHelper::downloadPdfDocument($template, compact('debitNote', 'settings', 'company'));
        /*
        $pdf->loadView('pdfs.debit-notes', compact('debitNote', 'settings', 'company'));

        $filename = 'nota-debito-' . $debitNote->invoice_number . '.pdf';

        return $pdf->download($filename);
        */
    }

    // Métodos auxiliares privados

    private function getCommonDebitItems()
    {
        return [
            'late_payment' => [
                'name' => 'Juros por Atraso no Pagamento',
                'description' => 'Cobrança de juros devido ao atraso no pagamento da fatura',
                'default_percentage' => 2.0,
                'tax_rate' => 16,
                'reasons' => [
                    'monthly_interest' => 'Juros mensais por atraso',
                    'daily_interest' => 'Juros diários por atraso',
                    'compound_interest' => 'Juros compostos por atraso prolongado'
                ]
            ],
            'penalty_fee' => [
                'name' => 'Taxa de Multa',
                'description' => 'Multa aplicada conforme contrato',
                'default_percentage' => 10.0,
                'tax_rate' => 16,
                'reasons' => [
                    'contract_breach' => 'Quebra de contrato',
                    'late_payment_penalty' => 'Multa por pagamento em atraso',
                    'service_interruption' => 'Multa por interrupção de serviço'
                ]
            ],
            'additional_services' => [
                'name' => 'Serviços Adicionais',
                'description' => 'Cobrança por serviços extras não incluídos na fatura original',
                'default_fixed_amount' => 500.00,
                'tax_rate' => 16,
                'reasons' => [
                    'extra_work' => 'Trabalho adicional solicitado',
                    'urgent_delivery' => 'Entrega urgente',
                    'weekend_service' => 'Atendimento em final de semana',
                    'technical_support' => 'Suporte técnico adicional'
                ]
            ],
            'material_adjustment' => [
                'name' => 'Ajuste de Material',
                'description' => 'Diferença de preço ou quantidade de materiais',
                'default_percentage' => 0,
                'tax_rate' => 16,
                'reasons' => [
                    'price_increase' => 'Aumento no preço dos materiais',
                    'quantity_adjustment' => 'Ajuste na quantidade de materiais',
                    'specification_change' => 'Mudança de especificação'
                ]
            ],
            'administrative_fee' => [
                'name' => 'Taxa Administrativa',
                'description' => 'Cobrança de taxa administrativa',
                'default_fixed_amount' => 100.00,
                'tax_rate' => 16,
                'reasons' => [
                    'processing_fee' => 'Taxa de processamento',
                    'document_reissue' => 'Reemissão de documentos',
                    'certificate_fee' => 'Taxa de certificação'
                ]
            ],
            'correction_adjustment' => [
                'name' => 'Correção de Valor',
                'description' => 'Correção de erro na fatura original',
                'default_percentage' => 0,
                'tax_rate' => 16,
                'reasons' => [
                    'calculation_error' => 'Erro de cálculo na fatura original',
                    'tax_correction' => 'Correção de impostos',
                    'discount_reversal' => 'Reversão de desconto aplicado incorretamente'
                ]
            ]
        ];
    }

    private function calculateDebitValues($validated)
    {
        $baseAmount = $validated['base_amount'];
        $subtotal = 0;

        if (!empty($validated['percentage']) && $validated['percentage'] > 0) {
            // Cálculo baseado em percentual
            $subtotal = $baseAmount * ($validated['percentage'] / 100);
        } elseif (!empty($validated['fixed_amount']) && $validated['fixed_amount'] > 0) {
            // Valor fixo
            $subtotal = $validated['fixed_amount'];
        } else {
            // Usar o valor base
            $subtotal = $baseAmount;
        }

        $taxRate = $validated['tax_rate'] ?? 0;
        $taxAmount = $subtotal * ($taxRate / 100);
        $total = $subtotal + $taxAmount;

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total
        ];
    }

    private function getDebitReasonText($debitType, $debitReason)
    {
        $commonItems = $this->getCommonDebitItems();

        if (isset($commonItems[$debitType]['reasons'][$debitReason])) {
            return $commonItems[$debitType]['reasons'][$debitReason];
        }

        return $debitReason;
    }

    private function getDebitItemDescription($debitType, $debitReason)
    {
        $commonItems = $this->getCommonDebitItems();

        if (isset($commonItems[$debitType])) {
            $itemName = $commonItems[$debitType]['name'];
            $reasonText = $this->getDebitReasonText($debitType, $debitReason);

            return "{$itemName} - {$reasonText}";
        }

        return "Cobrança adicional - {$debitReason}";
    }

    // API para obter detalhes de um tipo de débito (AJAX)
    public function getDebitTypeDetails(Request $request)
    {
        $debitType = $request->get('type');
        $commonItems = $this->getCommonDebitItems();

        if (isset($commonItems[$debitType])) {
            return response()->json($commonItems[$debitType]);
        }

        return response()->json(['error' => 'Tipo de débito não encontrado'], 404);
    }

    // API para calcular valores em tempo real (AJAX)
    public function calculatePreview(Request $request)
    {
        $validated = $request->validate([
            'base_amount' => 'required|numeric|min:0',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'fixed_amount' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100'
        ]);

        $calculatedValues = $this->calculateDebitValues($validated);

        return response()->json($calculatedValues);
    }
}
