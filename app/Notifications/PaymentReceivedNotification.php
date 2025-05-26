<?php
// app/Notifications/PaymentReceivedNotification.php

namespace App\Notifications;

use App\Models\Subscription;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentReceivedNotification extends Notification
{
    use Queueable;

    protected $subscription;
    protected $amount;

    public function __construct(Subscription $subscription, $amount)
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
                    ->subject('💰 Pagamento Confirmado - ' . $this->subscription->domain)
                    ->greeting('Obrigado, ' . $notifiable->name . '!')
                    ->line('Confirmamos o recebimento do seu pagamento.')
                    ->line('**Valor Pago:** MT ' . number_format($this->amount, 2))
                    ->line('**Domínio:** ' . $this->subscription->domain)
                    ->line('**Plano:** ' . $this->subscription->plan->name)
                    ->line('**Data do Pagamento:** ' . now()->format('d/m/Y H:i'))
                    ->line('**Próximo Vencimento:** ' . ($this->subscription->ends_at ? $this->subscription->ends_at->format('d/m/Y') : 'Sem vencimento'))
                    ->action('Ver Fatura', route('subscription.invoice', $this->subscription->id))
                    ->line('Obrigado por escolher nossos serviços!')
                    ->salutation('Atenciosamente, Equipe ' . config('app.name'));

        $this->logEmail($notifiable, 'payment');

        return $message;
    }

    private function logEmail($notifiable, $type)
    {
        EmailLog::create([
            'subscription_id' => $this->subscription->id,
            'client_id' => $this->subscription->client_id,
            'to_email' => $notifiable->email,
            'subject' => '💰 Pagamento Confirmado - ' . $this->subscription->domain,
            'type' => $type,
            'content' => "Confirmação de pagamento no valor de MT " . number_format($this->amount, 2),
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }
}