<?php

namespace App\Services;

use App\Helpers\DocumentTemplateHelper;
use App\Models\Receipt;
use App\Models\Invoice;
use App\Models\BillingSetting;
use App\Models\DocumentTemplate;
use Illuminate\Support\Facades\DB;
use Exception;

class ReceiptService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function generateReceiptForInvoice(Invoice $invoice, array $paymentData = [])
    {
        try {
            DB::beginTransaction();

            // Validar se a fatura precisa de recibo
            if ($invoice->status !== 'paid') {
                throw new Exception('Recibo só pode ser gerado para faturas pagas');
            }

            // Verificar se já existe recibo para este pagamento
            $existingReceipt = Receipt::where('invoice_id', $invoice->id)
                ->where('amount_paid', $paymentData['amount_paid'] ?? $invoice->paid_amount)
                ->active()
                ->first();

            if ($existingReceipt) {
                throw new Exception('Já existe um recibo para este pagamento');
            }

            // Dados do recibo
            $receiptData = [
                'invoice_id' => $invoice->id,
                'client_id' => $invoice->client_id,
                'amount_paid' => $paymentData['amount_paid'] ?? $invoice->paid_amount,
                'payment_method' => $paymentData['payment_method'] ?? $invoice->payment_method ?? Receipt::PAYMENT_OTHER,
                'payment_date' => $paymentData['payment_date'] ?? $invoice->paid_at ?? now(),
                'transaction_reference' => $paymentData['transaction_reference'] ?? null,
                'notes' => $paymentData['notes'] ?? "Recibo gerado automaticamente para fatura {$invoice->invoice_number}",
                'issued_by' => auth()->id(),
            ];

            // Criar o recibo
            $receipt = Receipt::create($receiptData);

            DB::commit();

            return $receipt;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Gerar recibo manual para pagamento parcial
     */
    public function generateManualReceipt(Invoice $invoice, array $paymentData)
    {
        try {
            DB::beginTransaction();

            // Validações
            $this->validatePaymentData($paymentData, $invoice);

            // Criar o recibo
            $receiptData = array_merge($paymentData, [
                'invoice_id' => $invoice->id,
                'client_id' => $invoice->client_id,
                'issued_by' => auth()->id(),
                'payment_date' => $paymentData['payment_date'] ?? now(),
            ]);

            $receipt = Receipt::create($receiptData);

            // Atualizar a fatura se necessário
            if ($paymentData['update_invoice'] ?? true) {
                $this->updateInvoicePaymentStatus($invoice, $paymentData['amount_paid']);
            }

            DB::commit();

            return $receipt;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cancelar um recibo
     */
    public function cancelReceipt(Receipt $receipt, $reason = null)
    {
        try {
            DB::beginTransaction();

            if ($receipt->isCancelled()) {
                throw new Exception('Recibo já está cancelado');
            }

            // Cancelar o recibo
            $receipt->cancel($reason);

            DB::commit();

            return $receipt;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Obter estatísticas de recibos
     */
    public function getReceiptStats($companyId = null)
    {
        $query = Receipt::query();

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return [
            'total_receipts' => $query->active()->count(),
            'total_amount_this_month' => $query->active()->thisMonth()->sum('amount_paid'),
            'total_amount_this_year' => $query->active()->thisYear()->sum('amount_paid'),
            'receipts_this_month' => $query->active()->thisMonth()->count(),
            'payment_methods' => $query->active()
                ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount_paid) as total'))
                ->groupBy('payment_method')
                ->get(),
        ];
    }

    /**
     * Validar dados de pagamento
     */
    private function validatePaymentData(array $paymentData, Invoice $invoice)
    {
        if (!isset($paymentData['amount_paid']) || $paymentData['amount_paid'] <= 0) {
            throw new Exception('Valor do pagamento deve ser maior que zero');
        }

        if ($paymentData['amount_paid'] > $invoice->remaining_amount) {
            throw new Exception('Valor do pagamento não pode ser maior que o valor restante da fatura');
        }

        if (!isset($paymentData['payment_method']) || 
            !in_array($paymentData['payment_method'], array_keys(Receipt::getPaymentMethods()))) {
            throw new Exception('Método de pagamento inválido');
        }
    }

    /**
     * Atualizar status de pagamento da fatura
     */
    private function updateInvoicePaymentStatus(Invoice $invoice, float $paymentAmount)
    {
        $newPaidAmount = $invoice->paid_amount + $paymentAmount;
        $newStatus = $newPaidAmount >= $invoice->total ? 'paid' : 'sent';

        $invoice->update([
            'paid_amount' => $newPaidAmount,
            'status' => $newStatus,
            'paid_at' => $newStatus === 'paid' ? now() : $invoice->paid_at,
        ]);
    }

    /**
     * Gerar PDF do recibo
     */
    public function generateReceiptPdf(Receipt $receipt)
    {
        // Carregar relacionamentos necessários
        $receipt->load(['invoice', 'client', 'company', 'issuedBy']);

        // Usar o helper de template ou gerar PDF simples
        $data = compact('receipt');
        
        // Se tiver sistema de templates
        if (class_exists('App\Helpers\DocumentTemplateHelper')) {
            $template = DocumentTemplate::where('company_id', $receipt->company_id)
                ->where('type', 'receipt')
                ->where('is_selected', true)
                ->first();
                
            return DocumentTemplateHelper::downloadPdfDocument($template, $data);
        }

        // Fallback: usar view simples
        $html = view('pdfs.receipt', $data)->render();
        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML($html);
        
        return $pdf->download("recibo-{$receipt->receipt_number}.pdf");
    }
}
