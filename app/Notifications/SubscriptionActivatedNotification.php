<?php
// app/Notifications/SubscriptionActivatedNotification.php

namespace App\Notifications;

use App\Models\Subscription;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionActivatedNotification extends Notification
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
                    ->subject('✅ Subscrição Ativada - ' . $this->subscription->domain)
                    ->greeting('Ótimas notícias, ' . $notifiable->name . '!')
                    ->line('Sua subscrição foi ativada com sucesso.')
                    ->line('**Domínio:** ' . $this->subscription->domain)
                    ->line('**Plano:** ' . $this->subscription->plan->name)
                    ->line('**Próximo Vencimento:** ' . ($this->subscription->ends_at ? $this->subscription->ends_at->format('d/m/Y') : 'Sem vencimento'))
                    ->action('Acessar Website', 'https://' . $this->subscription->domain)
                    ->line('Seu website já está funcionando normalmente.')
                    ->salutation('Atenciosamente, Equipe ' . config('app.name'));

        $this->logEmail($notifiable, 'activated');

        return $message;
    }

    private function logEmail($notifiable, $type)
    {
        EmailLog::create([
            'subscription_id' => $this->subscription->id,
            'client_id' => $this->subscription->client_id,
            'to_email' => $notifiable->email,
            'subject' => '✅ Subscrição Ativada - ' . $this->subscription->domain,
            'type' => $type,
            'content' => 'Notificação de ativação de subscrição',
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }
}