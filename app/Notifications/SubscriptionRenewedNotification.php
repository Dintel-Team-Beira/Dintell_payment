<?php

namespace App\Notifications;

use App\Models\Subscription;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Storage;

class SubscriptionRenewedNotification extends Notification
{
    use Queueable;

    protected $subscription;
    protected $amount;
    protected $oldExpiryDate;

    public function __construct(Subscription $subscription, $amount = null, $oldExpiryDate = null)
    {
        $this->subscription = $subscription;
        $this->amount = $amount ?? $subscription->plan->price;
        $this->oldExpiryDate = $oldExpiryDate;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('🔄 Subscrição Renovada com Sucesso - ' . $this->subscription->domain)
            ->view('emails.subscription-renewed', [
                'subscription' => $this->subscription,
                'client' => $notifiable,
                'plan' => $this->subscription->plan,
                'amount' => $this->amount,
                'oldExpiryDate' => $this->oldExpiryDate,
                'company' => $this->getCompanyInfo(),
                'renewalDate' => now(),
                'nextBillingDate' => $this->subscription->ends_at,
                'daysExtended' => $this->subscription->plan->billing_cycle_days,
                'serviceStatus' => 'Ativo',
                'subtotal' => $this->amount / 1.16,
                'iva_amount' => $this->amount - ($this->amount / 1.16)
            ]);

        // Anexar comprovativo de renovação
        $renewalPath = $this->generateRenewalPDF();
        if ($renewalPath && Storage::exists($renewalPath)) {
            $message->attach(Storage::path($renewalPath), [
                'as' => 'Comprovativo_Renovacao_' . $this->subscription->domain . '.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        $this->logEmail($notifiable, 'renewed');

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

    private function generateRenewalPDF()
    {
        try {
            $pdf = app('dompdf.wrapper');

            $html = view('pdfs.subscription-renewal', [
                'subscription' => $this->subscription,
                'client' => $this->subscription->client,
                'plan' => $this->subscription->plan,
                'amount' => $this->amount,
                'company' => $this->getCompanyInfo(),
                'renewalDate' => now(),
                'oldExpiryDate' => $this->oldExpiryDate,
                'newExpiryDate' => $this->subscription->ends_at,
                'renewalNumber' => $this->generateRenewalNumber(),
                'subtotal' => $this->amount / 1.16,
                'iva_rate' => 16,
                'iva_amount' => $this->amount - ($this->amount / 1.16)
            ])->render();

            $pdf->loadHTML($html);
            $pdf->setPaper('A4', 'portrait');

            $fileName = 'renewals/renovacao_' . $this->subscription->id . '_' . time() . '.pdf';
            Storage::put($fileName, $pdf->output());

            return $fileName;

        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF da renovação: ' . $e->getMessage());
            return null;
        }
    }

    private function generateRenewalNumber()
    {
        return sprintf('REN-%d-%s',
            $this->subscription->id,
            now()->format('YmdHis')
        );
    }

    private function logEmail($notifiable, $type)
    {
        EmailLog::create([
            'subscription_id' => $this->subscription->id,
            'client_id' => $this->subscription->client_id,
            'to_email' => $notifiable->email,
            'subject' => '🔄 Subscrição Renovada com Sucesso - ' . $this->subscription->domain,
            'type' => $type,
            'content' => 'Notificação detalhada de renovação de subscrição com comprovativo - MT ' . number_format($this->amount, 2),
            'status' => 'sent',
            'sent_at' => now(),
            'has_attachment' => true
        ]);
    }
}