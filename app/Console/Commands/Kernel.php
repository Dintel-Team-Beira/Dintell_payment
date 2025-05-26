<?php
// app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\CheckExpiredSubscriptions::class,
        Commands\SendExpirationNotifications::class,
        Commands\CleanOldLogs::class,
        Commands\ProcessAutoRenewals::class,
        Commands\GenerateReports::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Verificar subscrições expiradas (a cada hora)
        $schedule->command('subscriptions:check-expired')
                 ->hourly()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Enviar notificações de expiração (diariamente às 09:00)
        $schedule->command('subscriptions:send-notifications')
                 ->dailyAt('09:00')
                 ->timezone('Africa/Maputo');

        // Limpeza de logs antigos (semanalmente)
        $schedule->command('logs:clean --days=90')
                 ->weekly()
                 ->sundays()
                 ->at('02:00');

        // Processar renovações automáticas (diariamente às 06:00)
        $schedule->command('subscriptions:process-renewals')
                 ->dailyAt('06:00')
                 ->timezone('Africa/Maputo');

        // Gerar relatórios mensais (primeiro dia do mês)
        $schedule->command('reports:generate monthly')
                 ->monthlyOn(1, '08:00')
                 ->timezone('Africa/Maputo');

        // Backup do banco de dados (diariamente)
        $schedule->command('backup:run --only-db')
                 ->dailyAt('03:00')
                 ->timezone('Africa/Maputo');

        // Limpar cache (diariamente)
        $schedule->command('cache:clear')
                 ->dailyAt('04:00');

        // Otimizar aplicação (semanalmente)
        $schedule->command('optimize')
                 ->weekly()
                 ->sundays()
                 ->at('01:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}