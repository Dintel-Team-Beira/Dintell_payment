<?php
// app/Notifications/PaymentReceivedNotification.php

namespace App\Notifications;

use App\Models\Subscription;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Storage;

class PaymentReceivedNotification extends Notification
{
    use Queueable;

    protected $subscription;
    protected $amount;
    protected $paymentMethod;
    protected $paymentReference;

    public function __construct(Subscription $subscription, $amount, $paymentMethod = null, $paymentReference = null)
    {
        $this->subscription = $subscription;
        $this->amount = $amount;
        $this->paymentMethod = $paymentMethod ?? 'Transferência Bancária';
        $this->paymentReference = $paymentReference;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('💰 Pagamento Confirmado - Recibo #' . $this->generateReceiptNumber())
            ->view('emails.payment-received', [
                'subscription' => $this->subscription,
                'client' => $notifiable,
                'plan' => $this->subscription->plan,
                'amount' => $this->amount,
                'paymentMethod' => $this->paymentMethod,
                'paymentReference' => $this->paymentReference,
                'receiptNumber' => $this->generateReceiptNumber(),
                'company' => $this->getCompanyInfo(),
                'paymentDate' => now(),
                'subtotal' => $this->amount / 1.16,
                'iva_amount' => $this->amount - ($this->amount / 1.16)
            ]);

        // Anexar recibo PDF
        $receiptPath = $this->generateReceiptPDF();
        if ($receiptPath && Storage::exists($receiptPath)) {
            $message->attach(Storage::path($receiptPath), [
                'as' => 'Recibo_' . $this->generateReceiptNumber() . '.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        $this->logEmail($notifiable, 'payment');

        return $message;
    }

    private function getCompanyInfo()
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
            'slogan' => 'beyond technology, intelligence.'
        ];
    }

    private function generateReceiptNumber()
    {
        return sprintf('REC-%d-%s',
            $this->subscription->id,
            now()->format('YmdHis')
        );
    }

    private function generateReceiptPDF()
    {
        try {
            $pdf = app('dompdf.wrapper');

            $html = view('pdfs.payment-receipt', [
                'subscription' => $this->subscription,
                'client' => $this->subscription->client,
                'plan' => $this->subscription->plan,
                'amount' => $this->amount,
                'paymentMethod' => $this->paymentMethod,
                'paymentReference' => $this->paymentReference,
                'receiptNumber' => $this->generateReceiptNumber(),
                'company' => $this->getCompanyInfo(),
                'paymentDate' => now(),
                'subtotal' => $this->amount / 1.16,
                'iva_rate' => 16,
                'iva_amount' => $this->amount - ($this->amount / 1.16)
            ])->render();

            $pdf->loadHTML($html);
            $pdf->setPaper('A4', 'portrait');

            $fileName = 'receipts/recibo_' . $this->subscription->id . '_' . time() . '.pdf';
            Storage::put($fileName, $pdf->output());

            return $fileName;

        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF do recibo: ' . $e->getMessage());
            return null;
        }
    }

    private function logEmail($notifiable, $type)
    {
        EmailLog::create([
            'subscription_id' => $this->subscription->id,
            'client_id' => $this->subscription->client_id,
            'to_email' => $notifiable->email,
            'subject' => '💰 Pagamento Confirmado - Recibo #' . $this->generateReceiptNumber(),
            'type' => $type,
            'content' => "Confirmação de pagamento no valor de MT " . number_format($this->amount, 2) . " via " . $this->paymentMethod,
            'status' => 'sent',
            'sent_at' => now(),
            'has_attachment' => true
        ]);
    }
}