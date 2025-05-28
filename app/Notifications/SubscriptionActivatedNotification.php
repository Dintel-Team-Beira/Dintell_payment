<?php

// ===== NOTIFICAÇÃO DE ATIVAÇÃO DETALHADA =====

namespace App\Notifications;

use App\Models\Subscription;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Storage;

class SubscriptionActivatedNotification extends Notification
{
    use Queueable;

    protected $subscription;
    protected $attachInvoice;

    public function __construct(Subscription $subscription, $attachInvoice = true)
    {
        $this->subscription = $subscription;
        $this->attachInvoice = $attachInvoice;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('✅ Subscrição Ativada com Sucesso - ' . $this->subscription->domain)
            ->view('emails.subscription-activated', [
                'subscription' => $this->subscription,
                'client' => $notifiable,
                'plan' => $this->subscription->plan,
                'company' => $this->getCompanyInfo()
            ]);

        // Anexar comprovativo se solicitado
        if ($this->attachInvoice) {
            $invoicePath = $this->generateInvoicePDF();
            if ($invoicePath && Storage::exists($invoicePath)) {
                $message->attach(Storage::path($invoicePath), [
                    'as' => 'Comprovativo_Ativacao_' . $this->subscription->domain . '.pdf',
                    'mime' => 'application/pdf',
                ]);
            }
        }

        $this->logEmail($notifiable, 'activated');

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
            'slogan' => 'beyond technology, intelligence.',
            'bank_name' => 'BCI',
            'bank_account' => '222 038 724 100 01',
            'bank_nib' => '0008 0000 2203 8724 101 13'
        ];
    }

    private function generateInvoicePDF()
    {
        try {
            // Usar uma biblioteca como DomPDF ou TCPDF
            $pdf = app('dompdf.wrapper');

            $html = view('pdfs.subscription-invoice', [
                'subscription' => $this->subscription,
                'client' => $this->subscription->client,
                'plan' => $this->subscription->plan,
                'company' => $this->getCompanyInfo(),
                'invoice_number' => $this->generateInvoiceNumber(),
                'invoice_date' => now(),
                'due_date' => now(),
                'subtotal' => $this->subscription->plan->price / 1.16,
                'iva_rate' => 16,
                'iva_amount' => $this->subscription->plan->price - ($this->subscription->plan->price / 1.16),
                'total' => $this->subscription->plan->price
            ])->render();

            $pdf->loadHTML($html);
            $pdf->setPaper('A4', 'portrait');

            $fileName = 'invoices/comprovativo_' . $this->subscription->id . '_' . time() . '.pdf';
            Storage::put($fileName, $pdf->output());

            return $fileName;

        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF da fatura: ' . $e->getMessage());
            return null;
        }
    }

    private function generateInvoiceNumber()
    {
        return sprintf('%d/DINTELL%d',
            $this->subscription->id + 1000,
            now()->year
        );
    }

    private function logEmail($notifiable, $type)
    {
        EmailLog::create([
            'subscription_id' => $this->subscription->id,
            'client_id' => $this->subscription->client_id,
            'to_email' => $notifiable->email,
            'subject' => '✅ Subscrição Ativada com Sucesso - ' . $this->subscription->domain,
            'type' => $type,
            'content' => 'Notificação detalhada de ativação de subscrição com comprovativo',
            'status' => 'sent',
            'sent_at' => now(),
            'has_attachment' => $this->attachInvoice
        ]);
    }
}
