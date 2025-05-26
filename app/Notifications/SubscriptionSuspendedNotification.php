<?php
// app/Notifications/SubscriptionSuspendedNotification.php

namespace App\Notifications;

use App\Models\Subscription;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionSuspendedNotification extends Notification
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
        $message = (new MailMessage)
                    ->subject('⚠️ Subscrição Suspensa - ' . $this->subscription->domain)
                    ->greeting('Olá ' . $notifiable->name . ',')
                    ->line('Sua subscrição foi suspensa.')
                    ->line('**Domínio:** ' . $this->subscription->domain)
                    ->line('**Motivo:** ' . ($this->subscription->suspension_reason ?? 'Não especificado'))
                    ->line('**Data da Suspensão:** ' . $this->subscription->suspended_at->format('d/m/Y H:i'))
                    ->action('Ver Detalhes', route('suspension.page', ['domain' => $this->subscription->domain]))
                    ->line('Para reativar sua subscrição, entre em contato conosco.')
                    ->salutation('Atenciosamente, Equipe ' . config('app.name'));

        // Log do email
        $this->logEmail($notifiable, 'suspended');

        return $message;
    }

    private function logEmail($notifiable, $type)
    {
        EmailLog::create([
            'subscription_id' => $this->subscription->id,
            'client_id' => $this->subscription->client_id,
            'to_email' => $notifiable->email,
            'subject' => '⚠️ Subscrição Suspensa - ' . $this->subscription->domain,
            'type' => $type,
            'content' => 'Notificação de suspensão de subscrição',
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }
}