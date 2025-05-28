<?php

namespace App\Notifications;

use App\Models\Subscription;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionExpiredNotification extends Notification
{
    use Queueable;

    protected $subscription;
    protected $gracePeriodDays;

    public function __construct(Subscription $subscription, $gracePeriodDays = 7)
    {
        $this->subscription = $subscription;
        $this->gracePeriodDays = $gracePeriodDays;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $gracePeriodEnd = now()->addDays($this->gracePeriodDays);

        $message = (new MailMessage)
                    ->subject('⚠️ Subscrição Expirada - ' . $this->subscription->domain)
                    ->greeting('Olá ' . $notifiable->name . ',')
                    ->line('Sua subscrição expirou e precisa ser renovada.')
                    ->line('**Domínio:** ' . $this->subscription->domain)
                    ->line('**Plano:** ' . $this->subscription->plan->name)
                    ->line('**Data de Expiração:** ' . $this->subscription->ends_at->format('d/m/Y H:i'))
                    ->line('**Valor para Renovação:** MT ' . number_format($this->subscription->plan->price, 2))
                    ->line('⏰ **Período de Carência:** Até ' . $gracePeriodEnd->format('d/m/Y') . ' (ainda funcionando)')
                    ->action('Renovar Agora', route('subscriptions.renew', $this->subscription->id))
                    ->line('Após o período de carência, seu website será suspenso.')
                    ->line('Entre em contato conosco se precisar de ajuda.')
                    ->salutation('Atenciosamente, Equipe ' . config('app.name'));

        $this->logEmail($notifiable, 'expired');

        return $message;
    }

    private function logEmail($notifiable, $type)
    {
        EmailLog::create([
            'subscription_id' => $this->subscription->id,
            'client_id' => $this->subscription->client_id,
            'to_email' => $notifiable->email,
            'subject' => '⚠️ Subscrição Expirada - ' . $this->subscription->domain,
            'type' => $type,
            'content' => 'Notificação de expiração de subscrição',
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }
}
