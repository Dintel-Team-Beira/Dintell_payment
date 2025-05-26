<?php
// app/Notifications/SubscriptionExpiringNotification.php

namespace App\Notifications;

use App\Models\Subscription;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionExpiringNotification extends Notification
{
    use Queueable;

    protected $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $daysLeft = $this->subscription->days_until_expiry;

        $message = (new MailMessage)
                    ->subject("⏰ Subscrição expira em {$daysLeft} dias - " . $this->subscription->domain)
                    ->greeting('Olá ' . $notifiable->name . ',')
                    ->line("Sua subscrição expira em **{$daysLeft} dias**.")
                    ->line('**Domínio:** ' . $this->subscription->domain)
                    ->line('**Plano:** ' . $this->subscription->plan->name)
                    ->line('**Data de Expiração:** ' . $this->subscription->ends_at->format('d/m/Y'))
                    ->line('**Valor para Renovação:** MT ' . number_format($this->subscription->plan->price, 2))
                    ->action('Renovar Agora', route('subscription.renew', $this->subscription->id))
                    ->line('Renove sua subscrição para evitar a interrupção do serviço.')
                    ->salutation('Atenciosamente, Equipe ' . config('app.name'));

        $this->logEmail($notifiable, 'expiring');

        return $message;
    }

    private function logEmail($notifiable, $type)
    {
        EmailLog::create([
            'subscription_id' => $this->subscription->id,
            'client_id' => $this->subscription->client_id,
            'to_email' => $notifiable->email,
            'subject' => "⏰ Subscrição expira em {$this->subscription->days_until_expiry} dias",
            'type' => $type,
            'content' => 'Aviso de expiração de subscrição',
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }
}