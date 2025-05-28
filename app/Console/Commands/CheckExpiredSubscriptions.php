<?php

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
            'payment_failures' => 0,
            'errors' => 0
        ];

        // ===== 1. VERIFICAR E PROCESSAR SUBSCRIÇÕES QUE EXPIRARAM HOJE =====
        $this->processExpiredSubscriptionsToday($dryRun, $stats);

        // ===== 2. PROCESSAR TRIALS QUE EXPIRARAM =====
        $this->processExpiredTrials($dryRun, $stats);

        // ===== 3. AVISOS DE EXPIRAÇÃO (7, 3, 1 dias) =====
        $this->processExpirationWarnings($dryRun, $stats, $warningDays);

        // ===== 4. AUTO-RENOVAR SUBSCRIÇÕES ELEGÍVEIS =====
        $this->processAutoRenewals($dryRun, $stats);

        // ===== 5. MOSTRAR ESTATÍSTICAS FINAIS =====
        $this->showStats($stats, $dryRun);

        // ===== 6. SALVAR LOG DA EXECUÇÃO =====
        if (!$dryRun) {
            $this->saveExecutionLog($stats);
        }

        return Command::SUCCESS;
    }

    /**
     * Verificar e processar subscrições que expiraram hoje
     */
    private function processExpiredSubscriptionsToday($dryRun, &$stats)
    {
        $this->info('📅 Verificando subscrições que expiraram hoje ou antes...');

        // Buscar subscrições onde ends_at <= hoje E ainda estão com status 'active'
        $expiredToday = Subscription::where('ends_at', '<=', now()->endOfDay())
            ->where('status', 'active')
            ->with(['client', 'plan'])
            ->get();

        if ($expiredToday->isEmpty()) {
            $this->line("   ✅ Nenhuma subscrição regular expirou hoje");
            $this->newLine();
            return;
        }

        foreach ($expiredToday as $subscription) {
            $expiredDate = $subscription->ends_at->format('d/m/Y H:i');
            $this->line("🔍 Processando: {$subscription->domain} (Expirou: {$expiredDate})");
            $this->line("   Cliente: {$subscription->client->name} ({$subscription->client->email})");

            if (!$dryRun) {
                try {
                    // 1. ALTERAR STATUS PARA EXPIRED
                    $subscription->update([
                        'status' => 'expired',
                        'suspended_at' => now(),
                        'suspension_reason' => 'Expiração automática - vencimento em ' . $expiredDate
                    ]);

                    $this->line("   ✅ Status alterado: active → expired");

                    // 2. ENVIAR EMAIL DE EXPIRAÇÃO
                    $subscription->client->notify(new SubscriptionExpiredNotification($subscription));
                    $this->line("   ✉️  Email de expiração enviado para: {$subscription->client->email}");

                    $stats['expired_today']++;

                } catch (\Exception $e) {
                    $this->error("   ❌ Erro ao processar {$subscription->domain}: {$e->getMessage()}");
                    $stats['errors']++;
                }
            } else {
                $this->line("   🧪 [SIMULAÇÃO] Status seria alterado para 'expired'");
                $this->line("   🧪 [SIMULAÇÃO] Email seria enviado para: {$subscription->client->email}");
                $stats['expired_today']++;
            }

            $this->newLine();
        }

        $message = $dryRun ?
            "🧪 [SIMULAÇÃO] {$stats['expired_today']} subscrições seriam marcadas como expiradas" :
            "✅ {$stats['expired_today']} subscrições processadas e marcadas como expiradas";

        $this->info($message);
        $this->newLine();
    }

    /**
     * Processar trials que expiraram
     */
    private function processExpiredTrials($dryRun, &$stats)
    {
        $this->info('🎯 Verificando trials expirados...');

        $expiredTrials = Subscription::where('trial_ends_at', '<=', now()->endOfDay())
            ->where('status', 'trial')
            ->with(['client', 'plan'])
            ->get();

        if ($expiredTrials->isEmpty()) {
            $this->line("   ✅ Nenhum trial expirou hoje");
            $this->newLine();
            return;
        }

        foreach ($expiredTrials as $subscription) {
            $trialExpiredDate = $subscription->trial_ends_at->format('d/m/Y H:i');
            $this->line("🔍 Processando trial: {$subscription->domain} (Trial expirou: {$trialExpiredDate})");

            if (!$dryRun) {
                try {
                    // Alterar status do trial para expired
                    $subscription->update([
                        'status' => 'expired',
                        'suspended_at' => now(),
                        'suspension_reason' => 'Trial expirado - vencimento em ' . $trialExpiredDate
                    ]);

                    // Enviar notificação de trial expirado
                    $subscription->client->notify(new SubscriptionExpiredNotification($subscription));
                    $this->line("   ✉️  Notificação de trial enviada para: {$subscription->client->email}");

                    $stats['expired_trials']++;

                } catch (\Exception $e) {
                    $this->error("   ❌ Erro ao processar trial {$subscription->domain}: {$e->getMessage()}");
                    $stats['errors']++;
                }
            } else {
                $this->line("   🧪 [SIMULAÇÃO] Trial seria marcado como expirado");
                $stats['expired_trials']++;
            }
        }

        $this->newLine();
    }

    /**
     * Processar avisos de expiração
     */
    private function processExpirationWarnings($dryRun, &$stats, $warningDays)
    {
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

                // Verificar se precisa enviar aviso
                $shouldSend = !$lastWarning ||
                             Carbon::parse($lastWarning)->format('Y-m-d') !== now()->format('Y-m-d');

                if ($shouldSend) {
                    if (!$dryRun) {
                        try {
                            $subscription->client->notify(new SubscriptionExpiringNotification($subscription));

                            // Marcar que enviou aviso hoje
                            $subscription->setMetaData($lastWarningKey, now()->toDateTimeString());

                            // Atualizar campo last_warning_sent
                            $subscription->update(['last_warning_sent' => now()]);

                            $this->line("✉️  Aviso {$days}d enviado para: {$subscription->client->email}");
                            $stats['warnings_sent']++;
                        } catch (\Exception $e) {
                            $this->error("❌ Erro ao enviar aviso para {$subscription->client->email}: {$e->getMessage()}");
                            $stats['errors']++;
                        }
                    } else {
                        $this->line("🧪 Aviso {$days}d seria enviado para: {$subscription->client->email}");
                        $stats['warnings_sent']++;
                    }

                    $this->line("⚠️  Aviso {$days}d: {$subscription->domain} (Cliente: {$subscription->client->name})");
                } else {
                    $this->line("⏭️  Aviso {$days}d já enviado hoje: {$subscription->domain}");
                }
            }
        }

        $this->newLine();
    }

    /**
     * Processar auto-renovações
     */
    private function processAutoRenewals($dryRun, &$stats)
    {
        $this->info('🔄 Verificando subscrições para auto-renovação...');

        $autoRenewable = Subscription::where('ends_at', '<=', now()->addDays(1))
            ->where('status', 'active')
            ->where('auto_renew', true)
            ->where('payment_failures', '<', 3)
            ->with(['client', 'plan'])
            ->get();

        if ($autoRenewable->isEmpty()) {
            $this->line("   ✅ Nenhuma subscrição elegível para auto-renovação");
            $this->newLine();
            return;
        }

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
                $stats['auto_renewed']++;
            }
        }

        $this->newLine();
    }

    private function processAutoRenewal($subscription)
    {
        // Simulação de processamento de pagamento
        try {
            // Verificar se o cliente tem método de pagamento válido
            if (!$subscription->client->hasValidPaymentMethod()) {
                $this->line("❌ Cliente {$subscription->client->name} não tem método de pagamento válido");
                return false;
            }

            // TODO: Integrar com gateway de pagamento real (MPesa, Visa, etc.)

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
                ['Subscrições expiradas processadas', $stats['expired_today']],
                ['Trials expirados processados', $stats['expired_trials']],
                ['Avisos de expiração enviados', $stats['warnings_sent']],
                ['Auto-renovações bem-sucedidas', $stats['auto_renewed']],
                ['Falhas de pagamento', $stats['payment_failures']],
                ['Erros encontrados', $stats['errors']],
            ]
        );

        $totalActions = $stats['expired_today'] + $stats['expired_trials'] + $stats['warnings_sent'] + $stats['auto_renewed'];

        if ($dryRun) {
            $this->warn("⚠️  SIMULAÇÃO: {$totalActions} ações seriam executadas");
            $this->warn('Execute sem --dry-run para aplicar as alterações.');
        } else {
            $this->info("✅ PROCESSAMENTO CONCLUÍDO: {$totalActions} ações executadas!");

            if ($stats['errors'] > 0) {
                $this->warn("⚠️  {$stats['errors']} erros encontrados. Verifique os logs.");
            }
        }
    }

    private function saveExecutionLog($stats)
    {
        try {
            // Salvar log detalhado da execução
            \Log::info('Subscrições verificadas automaticamente', [
                'executed_at' => now()->toDateTimeString(),
                'command' => 'subscriptions:check-expired',
                'stats' => $stats,
                'summary' => [
                    'total_actions' => $stats['expired_today'] + $stats['expired_trials'] + $stats['warnings_sent'] + $stats['auto_renewed'],
                    'success_rate' => $stats['errors'] > 0 ? 'with_errors' : 'success',
                    'execution_time' => now()->toTimeString()
                ]
            ]);

            $this->line("📝 Log da execução salvo com sucesso");

        } catch (\Exception $e) {
            $this->error("❌ Erro ao salvar log: {$e->getMessage()}");
        }
    }
}