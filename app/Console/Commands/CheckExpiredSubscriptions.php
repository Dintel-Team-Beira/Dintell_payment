<?php
// app/Console/Commands/CheckExpiredSubscriptions.php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\SubscriptionExpiringNotification;
use App\Notifications\SubscriptionExpiredNotification;
use App\Notifications\SubscriptionRenewedNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-expired
                            {--dry-run : Simular sem fazer alterações}
                            {--force : Forçar verificação mesmo que já tenha sido executada hoje}
                            {--days=7,3,1 : Dias antes da expiração para avisar}';

    protected $description = 'Verifica e processa subscrições expiradas automaticamente';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $warningDays = array_map('intval', explode(',', $this->option('days')));

        if ($dryRun) {
            $this->warn('🧪 MODO SIMULAÇÃO - Nenhuma alteração será feita');
        }

        $this->info('🔍 Verificando subscrições expiradas...');
        $this->newLine();

        // Contadores para estatísticas
        $stats = [
            'expired_today' => 0,
            'expired_trials' => 0,
            'warnings_sent' => 0,
            'auto_renewed' => 0,
            'payment_failures' => 0
        ];

        // 1. Processar subscrições que expiraram hoje
        $this->info('📅 Verificando subscrições que expiraram hoje...');
        $expiredToday = Subscription::where('ends_at', '<=', now())
                                  ->where('status', 'active')
                                  ->with(['client', 'plan'])
                                  ->get();

        foreach ($expiredToday as $subscription) {
            if (!$dryRun) {
                $subscription->update([
                    'status' => 'expired',
                    'suspended_at' => now()
                ]);

                // Enviar notificação de expiração
                try {
                    $subscription->client->notify(new SubscriptionExpiredNotification($subscription));
                    $this->line("✉️  Notificação enviada para: {$subscription->client->email}");
                } catch (\Exception $e) {
                    $this->error("❌ Erro ao enviar email para {$subscription->client->email}: {$e->getMessage()}");
                }
            }

            $this->line("❌ Expirada: {$subscription->domain} (Cliente: {$subscription->client->name})");
            $stats['expired_today']++;
        }

        $this->newLine();

        // 2. Processar trials que expiraram
        $this->info('🎯 Verificando trials expirados...');
        $expiredTrials = Subscription::where('trial_ends_at', '<=', now())
                                   ->where('status', 'trial')
                                   ->with(['client', 'plan'])
                                   ->get();

        foreach ($expiredTrials as $subscription) {
            if (!$dryRun) {
                $subscription->update([
                    'status' => 'expired',
                    'suspended_at' => now()
                ]);

                // Enviar notificação de trial expirado
                try {
                    $subscription->client->notify(new SubscriptionExpiredNotification($subscription));
                    $this->line("✉️  Notificação de trial enviada para: {$subscription->client->email}");
                } catch (\Exception $e) {
                    $this->error("❌ Erro ao enviar email para {$subscription->client->email}: {$e->getMessage()}");
                }
            }

            $this->line("❌ Trial expirado: {$subscription->domain} (Cliente: {$subscription->client->name})");
            $stats['expired_trials']++;
        }

        $this->newLine();

        // 3. Avisos de expiração (7, 3, 1 dias)
        $this->info('⚠️  Verificando subscrições próximas ao vencimento...');

        foreach ($warningDays as $days) {
            $this->line("🔍 Verificando subscrições que expiram em {$days} dias...");

            $targetDate = now()->addDays($days);
            $expiringSoon = Subscription::whereDate('ends_at', $targetDate->toDateString())
                                      ->where('status', 'active')
                                      ->with(['client', 'plan'])
                                      ->get();

            foreach ($expiringSoon as $subscription) {
                // Verificar se já enviou aviso para este período
                $lastWarningKey = "last_warning_{$days}d";
                $lastWarning = $subscription->getMetaData($lastWarningKey);

                if (!$lastWarning || $lastWarning < now()->subDay()) {
                    if (!$dryRun) {
                        try {
                            $subscription->client->notify(new SubscriptionExpiringNotification($subscription, $days));

                            // Marcar que enviou aviso
                            $subscription->setMetaData($lastWarningKey, now());

                            $this->line("✉️  Aviso {$days}d enviado para: {$subscription->client->email}");
                            $stats['warnings_sent']++;
                        } catch (\Exception $e) {
                            $this->error("❌ Erro ao enviar aviso para {$subscription->client->email}: {$e->getMessage()}");
                        }
                    }

                    $this->line("⚠️  Aviso {$days}d: {$subscription->domain} (Cliente: {$subscription->client->name})");
                }
            }
        }

        $this->newLine();

        // 4. Auto-renovar subscrições elegíveis
        $this->info('🔄 Verificando subscrições para auto-renovação...');
        $autoRenewable = Subscription::where('ends_at', '<=', now()->addDays(1))
                                   ->where('status', 'active')
                                   ->where('auto_renew', true)
                                   ->where('payment_failures', '<', 3)
                                   ->with(['client', 'plan'])
                                   ->get();

        foreach ($autoRenewable as $subscription) {
            if (!$dryRun) {
                $paymentSuccess = $this->processAutoRenewal($subscription);

                if ($paymentSuccess) {
                    try {
                        $subscription->renew($subscription->plan->price, 'auto_renewal');

                        // Enviar notificação de renovação
                        $subscription->client->notify(new SubscriptionRenewedNotification($subscription, $subscription->plan->price));

                        $this->line("✅ Auto-renovada: {$subscription->domain} - MT " . number_format($subscription->plan->price, 2));
                        $stats['auto_renewed']++;
                    } catch (\Exception $e) {
                        $this->error("❌ Erro na renovação de {$subscription->domain}: {$e->getMessage()}");
                        $stats['payment_failures']++;
                    }
                } else {
                    $subscription->increment('payment_failures');
                    $this->line("❌ Falha na renovação: {$subscription->domain} (Tentativa {$subscription->payment_failures}/3)");
                    $stats['payment_failures']++;
                }
            } else {
                $this->line("🔄 Para auto-renovar: {$subscription->domain} - MT " . number_format($subscription->plan->price, 2));
            }
        }

        $this->newLine();

        // 5. Mostrar estatísticas finais
        $this->showStats($stats, $dryRun);

        // 6. Salvar log da execução
        if (!$dryRun) {
            $this->saveExecutionLog($stats);
        }

        return Command::SUCCESS;
    }

    private function processAutoRenewal($subscription)
    {
        // Simulação de processamento de pagamento
        // Aqui você integraria com MPesa, Visa, etc.

        try {
            // Verificar se o cliente tem método de pagamento válido
            if (!$subscription->client->hasValidPaymentMethod()) {
                $this->line("❌ Cliente {$subscription->client->name} não tem método de pagamento válido");
                return false;
            }

            // TODO: Integrar com gateway de pagamento real
            // Exemplo de integração:
            /*
            $paymentGateway = new PaymentGateway();
            $payment = $paymentGateway->charge([
                'amount' => $subscription->plan->price,
                'customer_id' => $subscription->client->id,
                'description' => "Renovação automática - {$subscription->domain}",
                'reference' => "auto_renew_" . $subscription->id . "_" . time()
            ]);

            if ($payment->isSuccessful()) {
                // Salvar informações do pagamento
                $subscription->payments()->create([
                    'amount' => $subscription->plan->price,
                    'payment_method' => 'auto_renewal',
                    'payment_reference' => $payment->getReference(),
                    'status' => 'completed'
                ]);

                return true;
            }

            return false;
            */

            // Por enquanto, simular sucesso em 85% dos casos
            $success = rand(1, 100) <= 85;

            if ($success) {
                // Simular delay de processamento
                usleep(500000); // 0.5 segundos
            }

            return $success;

        } catch (\Exception $e) {
            $this->error("Erro na renovação de {$subscription->domain}: {$e->getMessage()}");
            return false;
        }
    }

    private function showStats($stats, $dryRun)
    {
        $this->info('📊 ESTATÍSTICAS DA EXECUÇÃO:');
        $this->table(
            ['Categoria', 'Quantidade'],
            [
                ['Subscrições expiradas hoje', $stats['expired_today']],
                ['Trials expirados', $stats['expired_trials']],
                ['Avisos de expiração enviados', $stats['warnings_sent']],
                ['Auto-renovações bem-sucedidas', $stats['auto_renewed']],
                ['Falhas de pagamento', $stats['payment_failures']],
            ]
        );

        if ($dryRun) {
            $this->warn('⚠️  Lembre-se: Esta foi uma simulação. Execute sem --dry-run para aplicar as alterações.');
        } else {
            $this->info('✅ Processamento concluído com sucesso!');
        }
    }

    private function saveExecutionLog($stats)
    {
        try {
            // Salvar log da execução (você pode criar uma tabela para isso)
            \Log::info('Subscrições verificadas automaticamente', [
                'executed_at' => now(),
                'stats' => $stats,
                'command' => 'subscriptions:check-expired'
            ]);
        } catch (\Exception $e) {
            $this->error("Erro ao salvar log: {$e->getMessage()}");
        }
    }
}
