<?php

// ===== SubscriptionRenewedNotification.php =====
namespace App\Notifications;

use App\Models\Subscription;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionRenewedNotification extends Notification
{
    use Queueable;

    protected $subscription;
    protected $amount;

    public function __construct(Subscription $subscription, $amount = null)
    {
        $this->subscription = $subscription;
        $this->amount = $amount;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
                    ->subject('🔄 Subscrição Renovada - ' . $this->subscription->domain)
                    ->greeting('Parabéns, ' . $notifiable->name . '!')
                    ->line('Sua subscrição foi renovada com sucesso.')
                    ->line('**Domínio:** ' . $this->subscription->domain)
                    ->line('**Plano:** ' . $this->subscription->plan->name);

        if ($this->amount) {
            $message->line('**Valor Pago:** MT ' . number_format($this->amount, 2));
        }

        $message->line('**Data da Renovação:** ' . now()->format('d/m/Y H:i'))
                ->line('**Nova Data de Vencimento:** ' . ($this->subscription->ends_at ? $this->subscription->ends_at->format('d/m/Y') : 'Sem vencimento'))
                ->action('Acessar Painel', route('subscriptions.show', $this->subscription->id))
                ->line('Obrigado por continuar conosco!')
                ->salutation('Atenciosamente, Equipe ' . config('app.name'));

        $this->logEmail($notifiable, 'renewed');

        return $message;
    }

    private function logEmail($notifiable, $type)
    {
        EmailLog::create([
            'subscription_id' => $this->subscription->id,
            'client_id' => $this->subscription->client_id,
            'to_email' => $notifiable->email,
            'subject' => '🔄 Subscrição Renovada - ' . $this->subscription->domain,
            'type' => $type,
            'content' => 'Notificação de renovação de subscrição' . ($this->amount ? ' - MT ' . number_format($this->amount, 2) : ''),
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }
}
