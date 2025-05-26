<?php
// app/Console/Commands/CheckExpiredSubscriptions.php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\SubscriptionExpiringNotification;
use App\Notifications\SubscriptionExpiredNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-expired {--dry-run : Simular sem fazer alterações}';
    protected $description = 'Verifica e processa subscrições expiradas automaticamente';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🧪 MODO SIMULAÇÃO - Nenhuma alteração será feita');
        }

        $this->info('🔍 Verificando subscrições expiradas...');

        // 1. Processar subscrições que expiraram hoje
        $expiredToday = Subscription::where('ends_at', '<=', now()->endOfDay())
                                  ->where('ends_at', '>=', now()->startOfDay())
                                  ->where('status', 'active')
                                  ->with('client')
                                  ->get();

        foreach ($expiredToday as $subscription) {
            if (!$dryRun) {
                $subscription->update([
                    'status' => 'expired',
                    'suspended_at' => now()
                ]);

                if ($subscription->email_notifications) {
                    $subscription->client->notify(new SubscriptionExpiredNotification($subscription));
                }
            }

            $this->line("❌ Expirada: {$subscription->domain} (Cliente: {$subscription->client->name})");
        }

        // 2. Processar trials que expiraram
        $expiredTrials = Subscription::where('trial_ends_at', '<=', now())
                                   ->where('status', 'trial')
                                   ->with('client')
                                   ->get();

        foreach ($expiredTrials as $subscription) {
            if (!$dryRun) {
                $subscription->update([
                    'status' => 'expired',
                    'suspended_at' => now()
                ]);

                if ($subscription->email_notifications) {
                    $subscription->client->notify(new TrialExpiredNotification($subscription));
                }
            }

            $this->line("❌ Trial expirado: {$subscription->domain} (Cliente: {$subscription->client->name})");
        }

        // 3. Avisos de expiração (7, 3, 1 dias)
        $warningDays = [7, 3, 1];

        foreach ($warningDays as $days) {
            $expiringSoon = Subscription::where('ends_at', '=', now()->addDays($days)->startOfDay())
                                      ->where('status', 'active')
                                      ->where(function($q) use ($days) {
                                          $q->whereNull('last_warning_sent')
                                            ->orWhere('last_warning_sent', '<', now()->subDays($days + 1));
                                      })
                                      ->with('client')
                                      ->get();

            foreach ($expiringSoon as $subscription) {
                if (!$dryRun) {
                    if ($subscription->email_notifications) {
                        $subscription->client->notify(new SubscriptionExpiringNotification($subscription));
                    }

                    $subscription->update(['last_warning_sent' => now()]);
                }

                $this->line("⚠️  Aviso {$days}d: {$subscription->domain} (Cliente: {$subscription->client->name})");
            }
        }

        // 4. Auto-renovar subscrições elegíveis
        $autoRenewable = Subscription::where('ends_at', '<=', now()->addDays(1))
                                   ->where('status', 'active')
                                   ->where('auto_renew', true)
                                   ->where('payment_failures', '<', 3)
                                   ->with(['client', 'plan'])
                                   ->get();

        foreach ($autoRenewable as $subscription) {
            if (!$dryRun) {
                // Aqui você integraria com o gateway de pagamento
                $paymentSuccess = $this->processAutoRenewal($subscription);

                if ($paymentSuccess) {
                    $subscription->renew($subscription->plan->price, 'auto_renewal');
                    $this->line("✅ Auto-renovada: {$subscription->domain}");
                } else {
                    $subscription->increment('payment_failures');
                    $this->line("❌ Falha na renovação: {$subscription->domain}");
                }
            } else {
                $this->line("🔄 Para auto-renovar: {$subscription->domain}");
            }
        }

        $this->info("✅ Processamento concluído!");
        $this->info("📊 Expiradas hoje: {$expiredToday->count()}");
        $this->info("📊 Trials expirados: {$expiredTrials->count()}");
        $this->info("📊 Para auto-renovar: {$autoRenewable->count()}");
    }

    private function processAutoRenewal($subscription)
    {
        // Simulação de processamento de pagamento
        // Aqui você integraria com MPesa, Visa, etc.

        try {
            // Exemplo de integração fictícia
            // $payment = PaymentGateway::charge([
            //     'amount' => $subscription->plan->price,
            //     'customer_id' => $subscription->client->id,
            //     'description' => "Renovação automática - {$subscription->domain}"
            // ]);

            // return $payment->isSuccessful();

            // Por enquanto, simular sucesso em 80% dos casos
            return rand(1, 100) <= 80;

        } catch (\Exception $e) {
            $this->error("Erro na renovação de {$subscription->domain}: {$e->getMessage()}");
            return false;
        }
    }
}