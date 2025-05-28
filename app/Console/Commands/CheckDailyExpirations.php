<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\SubscriptionExpiredNotification;
use App\Notifications\SubscriptionExpiringNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckDailyExpirations extends Command
{
    protected $signature = 'subscriptions:check-daily
                            {--dry-run : Simular sem fazer alterações}
                            {--force : Forçar execução mesmo que já tenha rodado hoje}';

    protected $description = 'Verifica diariamente subscrições expiradas e próximas ao vencimento';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if ($dryRun) {
            $this->warn('🧪 MODO SIMULAÇÃO - Nenhuma alteração será feita');
        }

        $this->info('🔍 Verificando subscrições expiradas hoje...');
        $this->newLine();

        // Contadores
        $stats = [
            'expired_today' => 0,
            'expired_trials' => 0,
            'warnings_7d' => 0,
            'warnings_3d' => 0,
            'warnings_1d' => 0,
            'errors' => 0
        ];

        // 1. PROCESSAR SUBSCRIÇÕES QUE EXPIRARAM HOJE
        $this->processExpiredSubscriptions($dryRun, $stats);

        // 2. PROCESSAR TRIALS EXPIRADOS
        $this->processExpiredTrials($dryRun, $stats);

        // 3. ENVIAR AVISOS DE EXPIRAÇÃO (7, 3, 1 dias)
        $this->processExpirationWarnings($dryRun, $stats);

        // 4. MOSTRAR ESTATÍSTICAS
        $this->showDailyStats($stats, $dryRun);

        // 5. SALVAR LOG DA EXECUÇÃO
        if (!$dryRun) {
            $this->logDailyExecution($stats);
        }

        return Command::SUCCESS;
    }

    /**
     * Processar subscrições regulares que expiraram hoje
     */
    private function processExpiredSubscriptions($dryRun, &$stats)
    {
        $this->info('📅 Verificando subscrições regulares expiradas...');

        // Buscar subscrições que expiraram hoje ou antes de hoje
        $expiredSubscriptions = Subscription::where('ends_at', '<=', now()->endOfDay())
            ->where('status', 'active') // Só as que ainda estão ativas
            ->with(['client', 'plan'])
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            $this->line("🔍 Processando: {$subscription->domain} (Cliente: {$subscription->client->name})");

            if (!$dryRun) {
                try {
                    // 1. Atualizar status para expirado
                    $subscription->update([
                        'status' => 'expired',
                        'suspended_at' => now(),
                        'suspension_reason' => 'Expiração automática - vencimento em ' . $subscription->ends_at->format('d/m/Y')
                    ]);

                    // 2. Enviar notificação de expiração
                    $subscription->client->notify(new SubscriptionExpiredNotification($subscription));

                    $this->line("   ✅ Status alterado para 'expired'");
                    $this->line("   ✉️  Email enviado para: {$subscription->client->email}");

                    $stats['expired_today']++;

                } catch (\Exception $e) {
                    $this->error("   ❌ Erro ao processar {$subscription->domain}: {$e->getMessage()}");
                    $stats['errors']++;
                }
            } else {
                $this->line("   🧪 [SIMULAÇÃO] Seria marcada como expirada");
                $stats['expired_today']++;
            }

            $this->newLine();
        }

        if ($expiredSubscriptions->isEmpty()) {
            $this->line("   ✅ Nenhuma subscrição regular expirou hoje");
        }
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

        foreach ($expiredTrials as $subscription) {
            $this->line("🔍 Processando trial: {$subscription->domain} (Cliente: {$subscription->client->name})");

            if (!$dryRun) {
                try {
                    // 1. Atualizar status
                    $subscription->update([
                        'status' => 'expired',
                        'suspended_at' => now(),
                        'suspension_reason' => 'Trial expirado - vencimento em ' . $subscription->trial_ends_at->format('d/m/Y')
                    ]);

                    // 2. Enviar notificação
                    $subscription->client->notify(new SubscriptionExpiredNotification($subscription));

                    $this->line("   ✅ Trial expirado processado");
                    $this->line("   ✉️  Email enviado para: {$subscription->client->email}");

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

        if ($expiredTrials->isEmpty()) {
            $this->line("   ✅ Nenhum trial expirou hoje");
        }

        $this->newLine();
    }

    /**
     * Processar avisos de expiração (7, 3, 1 dias antes)
     */
    private function processExpirationWarnings($dryRun, &$stats)
    {
        $this->info('⚠️  Verificando avisos de expiração...');

        $warningDays = [7, 3, 1];

        foreach ($warningDays as $days) {
            $this->line("🔍 Verificando subscrições que expiram em {$days} dias...");

            $targetDate = now()->addDays($days)->toDateString();

            $expiringSoon = Subscription::whereDate('ends_at', $targetDate)
                ->where('status', 'active')
                ->with(['client', 'plan'])
                ->get();

            foreach ($expiringSoon as $subscription) {
                // Verificar se já enviou aviso para este período hoje
                $warningKey = "warning_{$days}d_sent";
                $lastWarningSent = $subscription->getMetaData($warningKey);

                $shouldSendWarning = !$lastWarningSent ||
                                   Carbon::parse($lastWarningSent)->format('Y-m-d') !== now()->format('Y-m-d');

                if ($shouldSendWarning) {
                    $this->line("🔍 Enviando aviso {$days}d: {$subscription->domain} (Cliente: {$subscription->client->name})");

                    if (!$dryRun) {
                        try {
                            // Enviar aviso de expiração
                            $subscription->client->notify(new SubscriptionExpiringNotification($subscription));

                            // Marcar que enviou o aviso hoje
                            $subscription->setMetaData($warningKey, now()->toDateTimeString());

                            // Atualizar campo last_warning_sent
                            $subscription->update(['last_warning_sent' => now()]);

                            $this->line("   ✉️  Aviso {$days}d enviado para: {$subscription->client->email}");

                            $stats["warnings_{$days}d"]++;

                        } catch (\Exception $e) {
                            $this->error("   ❌ Erro ao enviar aviso para {$subscription->client->email}: {$e->getMessage()}");
                            $stats['errors']++;
                        }
                    } else {
                        $this->line("   🧪 [SIMULAÇÃO] Aviso {$days}d seria enviado");
                        $stats["warnings_{$days}d"]++;
                    }
                } else {
                    $this->line("   ⏭️  Aviso {$days}d já foi enviado hoje para: {$subscription->domain}");
                }
            }

            if ($expiringSoon->isEmpty()) {
                $this->line("   ✅ Nenhuma subscrição expira em {$days} dias");
            }
        }

        $this->newLine();
    }

    /**
     * Mostrar estatísticas da execução
     */
    private function showDailyStats($stats, $dryRun)
    {
        $this->info('📊 ESTATÍSTICAS DA EXECUÇÃO DIÁRIA:');
        $this->table(
            ['Categoria', 'Quantidade'],
            [
                ['Subscrições expiradas hoje', $stats['expired_today']],
                ['Trials expirados', $stats['expired_trials']],
                ['Avisos 7 dias enviados', $stats['warnings_7d']],
                ['Avisos 3 dias enviados', $stats['warnings_3d']],
                ['Avisos 1 dia enviados', $stats['warnings_1d']],
                ['Erros encontrados', $stats['errors']],
            ]
        );

        $total_actions = $stats['expired_today'] + $stats['expired_trials'] +
                        $stats['warnings_7d'] + $stats['warnings_3d'] + $stats['warnings_1d'];

        if ($dryRun) {
            $this->warn("⚠️  SIMULAÇÃO: {$total_actions} ações seriam executadas");
        } else {
            $this->info("✅ CONCLUÍDO: {$total_actions} ações executadas com sucesso!");
        }
    }

    /**
     * Salvar log da execução diária
     */
    private function logDailyExecution($stats)
    {
        try {
            \Log::info('Verificação diária de subscrições executada', [
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'stats' => $stats,
                'command' => 'subscriptions:check-daily'
            ]);

            // Opcional: Salvar em tabela de logs se você tiver
            /*
            \DB::table('command_logs')->insert([
                'command_name' => 'subscriptions:check-daily',
                'executed_at' => now(),
                'stats' => json_encode($stats),
                'status' => $stats['errors'] > 0 ? 'completed_with_errors' : 'success'
            ]);
            */

        } catch (\Exception $e) {
            $this->error("❌ Erro ao salvar log: {$e->getMessage()}");
        }
    }
}