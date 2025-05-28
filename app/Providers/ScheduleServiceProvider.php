<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schedule;
use App\Models\Subscription;

class ScheduleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // ===== VERIFICAÇÃO AUTOMÁTICA DE SUBSCRIÇÕES EXPIRADAS =====

        // EXECUÇÃO PRINCIPAL: Todos os dias às 09:00 (Maputo)
        Schedule::command('subscriptions:check-expired')
                ->dailyAt('09:00')
                ->timezone('Africa/Maputo')
                ->withoutOverlapping(10) // Timeout de 10 minutos
                ->runInBackground()
                ->appendOutputTo(storage_path('logs/subscriptions-morning.log'))
                ->onSuccess(function () {
                    \Log::info('✅ Verificação matinal concluída', [
                        'time' => now()->format('H:i:s'),
                        'date' => now()->format('Y-m-d')
                    ]);
                })
                ->onFailure(function () {
                    \Log::error('❌ FALHA na verificação matinal', [
                        'time' => now()->format('H:i:s'),
                        'date' => now()->format('Y-m-d')
                    ]);
                });

        // EXECUÇÃO VESPERTINA: Todos os dias às 18:00 (Maputo)
        Schedule::command('subscriptions:check-expired')
                ->dailyAt('18:00')
                ->timezone('Africa/Maputo')
                ->withoutOverlapping(10)
                ->runInBackground()
                ->appendOutputTo(storage_path('logs/subscriptions-evening.log'))
                ->when(function () {
                    // Só executar se houver subscrições que expiraram hoje
                    return Subscription::whereDate('ends_at', today())
                                      ->where('status', 'active')
                                      ->exists();
                })
                ->description('Verificação vespertina de subscrições expiradas');

        // VERIFICAÇÃO DE EMERGÊNCIA: A cada 4 horas durante o dia útil
        Schedule::command('subscriptions:check-expired')
                ->cron('0 8,12,16,20 * * *') // 8h, 12h, 16h, 20h
                ->timezone('Africa/Maputo')
                ->withoutOverlapping(5)
                ->runInBackground()
                ->when(function () {
                    // Só executar se houver subscrições críticas (expirando hoje)
                    $criticalCount = Subscription::whereDate('ends_at', today())
                                                ->where('status', 'active')
                                                ->count();

                    if ($criticalCount > 0) {
                        \Log::info("🚨 Verificação de emergência: {$criticalCount} subscrições críticas encontradas");
                        return true;
                    }

                    return false;
                })
                ->description('Verificação de emergência para subscrições críticas');

        // ===== RELATÓRIOS E ESTATÍSTICAS =====

        // Relatório diário de estatísticas
        Schedule::call(function () {
            $this->generateDailyReport();
        })->dailyAt('23:00')
          ->description('Relatório diário de subscrições');

        // ===== LIMPEZA E MANUTENÇÃO =====

        // Limpar logs grandes toda semana
        Schedule::call(function () {
            $this->cleanupLogs();
        })->weekly()->sundays()->at('02:00')
          ->description('Limpeza de logs antigos');

        // Reset de contadores mensais
        Schedule::call(function () {
            $this->resetMonthlyCounters();
        })->monthlyOn(1, '01:00')
          ->description('Reset de contadores mensais');
    }

    /**
     * Gerar relatório diário
     */
    private function generateDailyReport()
    {
        try {
            $stats = [
                'date' => today()->format('Y-m-d'),
                'active' => Subscription::where('status', 'active')->count(),
                'expired' => Subscription::where('status', 'expired')->count(),
                'trials' => Subscription::where('status', 'trial')->count(),
                'suspended' => Subscription::where('status', 'suspended')->count(),
                'cancelled' => Subscription::where('status', 'cancelled')->count(),
                'expiring_soon_7d' => Subscription::expiringSoon(7)->count(),
                'expiring_soon_3d' => Subscription::expiringSoon(3)->count(),
                'expiring_today' => Subscription::whereDate('ends_at', today())->where('status', 'active')->count(),
                'expired_today' => Subscription::whereDate('ends_at', today())->where('status', 'expired')->count(),
            ];

            \Log::info('📊 Relatório diário de subscrições', $stats);

            // Alertas automáticos
            if ($stats['expiring_soon_7d'] > 10) {
                \Log::warning("⚠️ ATENÇÃO: {$stats['expiring_soon_7d']} subscrições expirando nos próximos 7 dias!");
            }

            if ($stats['expiring_today'] > 0) {
                \Log::warning("🚨 CRÍTICO: {$stats['expiring_today']} subscrições expirando HOJE!");
            }

            if ($stats['expired_today'] > 0) {
                \Log::info("✅ PROCESSADO: {$stats['expired_today']} subscrições expiradas hoje foram processadas");
            }

        } catch (\Exception $e) {
            \Log::error('Erro ao gerar relatório diário: ' . $e->getMessage());
        }
    }

    /**
     * Limpar logs antigos
     */
    private function cleanupLogs()
    {
        try {
            $logFiles = [
                storage_path('logs/subscriptions-morning.log'),
                storage_path('logs/subscriptions-evening.log')
            ];

            foreach ($logFiles as $logFile) {
                if (file_exists($logFile) && filesize($logFile) > 5 * 1024 * 1024) { // 5MB
                    // Manter apenas as últimas 1000 linhas
                    $lines = file($logFile);
                    if (count($lines) > 1000) {
                        $keepLines = array_slice($lines, -1000);
                        file_put_contents($logFile, implode('', $keepLines));
                        \Log::info("📝 Log truncado: " . basename($logFile));
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Erro na limpeza de logs: ' . $e->getMessage());
        }
    }

    /**
     * Reset contadores mensais
     */
    private function resetMonthlyCounters()
    {
        try {
            $affected = Subscription::query()->update(['monthly_requests' => 0]);
            \Log::info("🔄 Contadores mensais resetados: {$affected} subscrições atualizadas");
        } catch (\Exception $e) {
            \Log::error('Erro ao resetar contadores mensais: ' . $e->getMessage());
        }
    }

    public function register()
    {
        //
    }
}