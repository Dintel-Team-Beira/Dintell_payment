<?php
namespace App\Notifications;

use App\Models\Subscription;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionCancelledNotification extends Notification
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
                    ->subject('❌ Subscrição Cancelada - ' . $this->subscription->domain)
                    ->greeting('Olá ' . $notifiable->name . ',')
                    ->line('Sua subscrição foi cancelada conforme solicitado.')
                    ->line('**Domínio:** ' . $this->subscription->domain)
                    ->line('**Data do Cancelamento:** ' . $this->subscription->cancelled_at->format('d/m/Y H:i'));

        if ($this->subscription->cancellation_reason) {
            $message->line('**Motivo:** ' . $this->subscription->cancellation_reason);
        }

        $message->line('**Funcionará até:** ' . $this->subscription->ends_at->format('d/m/Y H:i'))
                ->action('Ver Detalhes', route('subscriptions.show', $this->subscription->id))
                ->line('Sentiremos sua falta! Esperamos vê-lo novamente.')
                ->salutation('Atenciosamente, Equipe ' . config('app.name'));

        $this->logEmail($notifiable, 'cancelled');

        return $message;
    }

    private function logEmail($notifiable, $type)
    {
        EmailLog::create([
            'subscription_id' => $this->subscription->id,
            'client_id' => $this->subscription->client_id,
            'to_email' => $notifiable->email,
            'subject' => '❌ Subscrição Cancelada - ' . $this->subscription->domain,
            'type' => $type,
            'content' => 'Notificação de cancelamento de subscrição',
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }
}