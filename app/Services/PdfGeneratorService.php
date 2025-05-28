<?php
namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PdfGeneratorService
{
    public static function generateInvoice($subscription, $data = [])
    {
        try {
            $html = view('pdfs.subscription-invoice', array_merge([
                'subscription' => $subscription,
                'client' => $subscription->client,
                'plan' => $subscription->plan,
                'company' => self::getCompanyInfo(),
                'invoice_number' => self::generateInvoiceNumber($subscription),
                'invoice_date' => now(),
                'due_date' => now(),
                'subtotal' => $subscription->plan->price / 1.16,
                'iva_rate' => 16,
                'iva_amount' => $subscription->plan->price - ($subscription->plan->price / 1.16),
                'total' => $subscription->plan->price
            ], $data))->render();

            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');

            $fileName = 'invoices/factura_' . $subscription->id . '_' . time() . '.pdf';
            Storage::put($fileName, $pdf->output());

            return $fileName;

        } catch (\Exception $e) {
            Log::error('Erro ao gerar PDF da fatura: ' . $e->getMessage());
            return null;
        }
    }

    public static function generateReceipt($subscription, $amount, $paymentData = [])
    {
        try {
            $html = view('pdfs.payment-receipt', array_merge([
                'subscription' => $subscription,
                'client' => $subscription->client,
                'plan' => $subscription->plan,
                'amount' => $amount,
                'company' => self::getCompanyInfo(),
                'receiptNumber' => self::generateReceiptNumber($subscription),
                'paymentDate' => now(),
                'subtotal' => $amount / 1.16,
                'iva_rate' => 16,
                'iva_amount' => $amount - ($amount / 1.16)
            ], $paymentData))->render();

            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');

            $fileName = 'receipts/recibo_' . $subscription->id . '_' . time() . '.pdf';
            Storage::put($fileName, $pdf->output());

            return $fileName;

        } catch (\Exception $e) {
            Log::error('Erro ao gerar PDF do recibo: ' . $e->getMessage());
            return null;
        }
    }

    public static function generateRenewalCertificate($subscription, $amount, $oldExpiryDate = null)
    {
        try {
            $html = view('pdfs.subscription-renewal', [
                'subscription' => $subscription,
                'client' => $subscription->client,
                'plan' => $subscription->plan,
                'amount' => $amount,
                'company' => self::getCompanyInfo(),
                'renewalDate' => now(),
                'oldExpiryDate' => $oldExpiryDate,
                'newExpiryDate' => $subscription->ends_at,
                'renewalNumber' => self::generateRenewalNumber($subscription),
                'subtotal' => $amount / 1.16,
                'iva_rate' => 16,
                'iva_amount' => $amount - ($amount / 1.16)
            ])->render();

            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');

            $fileName = 'renewals/renovacao_' . $subscription->id . '_' . time() . '.pdf';
            Storage::put($fileName, $pdf->output());

            return $fileName;

        } catch (\Exception $e) {
            Log::error('Erro ao gerar PDF da renovação: ' . $e->getMessage());
            return null;
        }
    }

    private static function generateInvoiceNumber($subscription)
    {
        return sprintf('%d/DINTELL%d',
            $subscription->id + 1000,
            now()->year
        );
    }

    private static function generateReceiptNumber($subscription)
    {
        return sprintf('REC-%d-%s',
            $subscription->id,
            now()->format('YmdHis')
        );
    }

    private static function generateRenewalNumber($subscription)
    {
        return sprintf('REN-%d-%s',
            $subscription->id,
            now()->format('YmdHis')
        );
    }

    private static function getCompanyInfo()
    {
        return [
            'name' => 'DINTELL, LDA',
            'nuit' => '401170839',
            'address_maputo' => 'Av. Maguiguana nº 137 R/C - Maputo',
            'address_beira' => 'Rua Correia de Brito nº 1697, 1º andar - Beira',
            'country' => 'Moçambique',
            'phone' => '866713342',
            'email' => 'comercial@dintell.co.mz',
            'website' => 'www.dintell.co.mz',
            'slogan' => 'beyond technology, intelligence.',
            'bank_name' => 'BCI',
            'bank_account' => '222 038 724 100 01',
            'bank_nib' => '0008 0000 2203 8724 101 13'
        ];
    }
}
