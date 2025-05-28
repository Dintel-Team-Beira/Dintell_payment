<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Notifications\SubscriptionActivatedNotification;
use App\Notifications\PaymentReceivedNotification;
use App\Notifications\SubscriptionRenewedNotification;

class TestEmailNotifications extends Command
{
    protected $signature = 'test:email-notifications
                            {subscription_id : ID da subscrição para teste}
                            {--type=all : Tipo de notificação (activated, payment, renewed, all)}';

    protected $description = 'Testar envio de notificações por email com anexos';

    public function handle()
    {
        $subscriptionId = $this->argument('subscription_id');
        $type = $this->option('type');

        $subscription = Subscription::with(['client', 'plan'])->find($subscriptionId);

        if (!$subscription) {
            $this->error('Subscrição não encontrada!');
            return;
        }

        $this->info("Testando notificações para: {$subscription->domain}");
        $this->info("Cliente: {$subscription->client->name} ({$subscription->client->email})");

        try {
            if ($type === 'activated' || $type === 'all') {
                $this->info('📧 Enviando notificação de ativação...');
                $subscription->client->notify(new SubscriptionActivatedNotification($subscription));
                $this->info('✅ Notificação de ativação enviada!');
            }

            if ($type === 'payment' || $type === 'all') {
                $this->info('📧 Enviando notificação de pagamento...');
                $subscription->client->notify(new PaymentReceivedNotification($subscription, $subscription->plan->price, 'Transferência Bancária', 'TEST-' . time()));
                $this->info('✅ Notificação de pagamento enviada!');
            }

            if ($type === 'renewed' || $type === 'all') {
                $this->info('📧 Enviando notificação de renovação...');
                $subscription->client->notify(new SubscriptionRenewedNotification($subscription, $subscription->plan->price, now()->subDays(30)));
                $this->info('✅ Notificação de renovação enviada!');
            }

            $this->info('🎉 Todas as notificações foram enviadas com sucesso!');

        } catch (\Exception $e) {
            $this->error('❌ Erro ao enviar notificações: ' . $e->getMessage());
        }
    }
}