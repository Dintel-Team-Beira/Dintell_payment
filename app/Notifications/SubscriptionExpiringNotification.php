<?php

// ===== NOTIFICAÇÃO DE EXPIRAÇÃO DETALHADA =====

namespace App\Notifications;

use App\Models\Subscription;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Storage;

class SubscriptionExpiringNotification extends Notification
{
    use Queueable;

    protected $subscription;
    protected $attachPDF;

    public function __construct(Subscription $subscription, $attachPDF = true)
    {
        $this->subscription = $subscription;
        $this->attachPDF = $attachPDF;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $daysLeft = $this->subscription->days_until_expiry;
        $subject = "⏰ Subscrição expira em {$daysLeft} dias - " . $this->subscription->domain;

        $message = (new MailMessage)
            ->subject($subject)
            ->view('emails.subscription-expiring', [
                'subscription' => $this->subscription,
                'client' => $notifiable,
                'plan' => $this->subscription->plan,
                'daysLeft' => $daysLeft,
                'company' => $this->getCompanyInfo()
            ]);

        // Anexar aviso em PDF se solicitado
        if ($this->attachPDF) {
            $pdfPath = $this->generateExpirationNoticePDF($notifiable, $daysLeft);
            if ($pdfPath && Storage::exists($pdfPath)) {
                $message->attach(Storage::path($pdfPath), [
                    'as' => 'Aviso_Expiracao_' . $this->subscription->domain . '.pdf',
                    'mime' => 'application/pdf',
                ]);
            }
        }

        $this->logEmail($notifiable, 'expiring', $subject);

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

    private function generateExpirationNoticePDF($client, $daysLeft)
    {
        try {
            $pdf = app('dompdf.wrapper');

            $html = view('pdfs.subscription-expiration-notice', [
                'subscription' => $this->subscription,
                'client' => $client,
                'plan' => $this->subscription->plan,
                'company' => $this->getCompanyInfo(),
                'notice_number' => $this->generateNoticeNumber(),
                'notice_date' => now(),
                'expiration_date' => $this->subscription->ends_at,
                'days_left' => $daysLeft,
                'renewal_amount' => $this->subscription->plan->price,
                'subtotal' => $this->subscription->plan->price / 1.16,
                'iva_rate' => 16,
                'iva_amount' => $this->subscription->plan->price - ($this->subscription->plan->price / 1.16),
                'total' => $this->subscription->plan->price
            ])->render();

            $pdf->loadHTML($html);
            $pdf->setPaper('A4', 'portrait');

            $fileName = 'notices/aviso_expiracao_' . $this->subscription->id . '_' . time() . '.pdf';
            Storage::put($fileName, $pdf->output());

            return $fileName;

        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF do aviso de expiração: ' . $e->getMessage());
            return null;
        }
    }

    private function generateNoticeNumber()
    {
        return sprintf('AV%d/DINTELL%d',
            $this->subscription->id + 2000,
            now()->year
        );
    }

    private function logEmail($notifiable, $type, $subject)
    {
        EmailLog::create([
            'subscription_id' => $this->subscription->id,
            'client_id' => $this->subscription->client_id,
            'to_email' => $notifiable->email,
            'subject' => $subject,
            'type' => $type,
            'content' => 'Aviso de expiração de subscrição com anexo em PDF',
            'status' => 'sent',
            'sent_at' => now(),
            'has_attachment' => $this->attachPDF
        ]);
    }
}