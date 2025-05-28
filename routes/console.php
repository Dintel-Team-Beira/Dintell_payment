<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\Subscription;

// ===== AGENDAMENTO PRINCIPAL =====

// Verificação diária às 09:00 (Maputo)
Schedule::command('subscriptions:check-expired')
    ->dailyAt('09:00')
    ->timezone('Africa/Maputo')
    ->withoutOverlapping(10)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/subscriptions-morning.log'))
    ->onSuccess(function () {
        \Log::info('✅ Verificação matinal de subscrições concluída às ' . now()->format('H:i:s'));
    })
    ->onFailure(function () {
        \Log::error('❌ FALHA na verificação matinal de subscrições às ' . now()->format('H:i:s'));
    });

// Verificação vespertina às 18:00 (só se houver subscrições expiradas)
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
    });

// Verificação de emergência a cada 4 horas durante o dia
Schedule::command('subscriptions:check-expired')
    ->cron('0 8,12,16,20 * * *') // 8h, 12h, 16h, 20h
    ->timezone('Africa/Maputo')
    ->withoutOverlapping(5)
    ->runInBackground()
    ->when(function () {
        // Só executar se houver subscrições críticas (expirando hoje)
        return Subscription::whereDate('ends_at', today())
                          ->where('status', 'active')
                          ->exists();
    });

// ===== LIMPEZA E MANUTENÇÃO =====

// Limpar logs grandes toda semana
Schedule::call(function () {
    $logFiles = [
        storage_path('logs/subscriptions-morning.log'),
        storage_path('logs/subscriptions-evening.log')
    ];

    foreach ($logFiles as $logFile) {
        if (file_exists($logFile) && filesize($logFile) > 5 * 1024 * 1024) { // 5MB
            // Manter apenas as últimas 1000 linhas
            $lines = file($logFile);
            $keepLines = array_slice($lines, -1000);
            file_put_contents($logFile, implode('', $keepLines));
            \Log::info("Log truncado: {$logFile}");
        }
    }
})->weekly()->sundays()->at('02:00');

// Reset de contadores mensais
Schedule::call(function () {
    Subscription::query()->update(['monthly_requests' => 0]);
    \Log::info('Contadores mensais resetados');
})->monthlyOn(1, '01:00');

// ===== RELATÓRIO DIÁRIO =====

// Relatório de estatísticas diárias
Schedule::call(function () {
    $stats = [
        'active' => Subscription::where('status', 'active')->count(),
        'expired' => Subscription::where('status', 'expired')->count(),
        'trials' => Subscription::where('status', 'trial')->count(),
        'suspended' => Subscription::where('status', 'suspended')->count(),
        'expiring_soon' => Subscription::expiringSoon(7)->count(),
        'expiring_today' => Subscription::whereDate('ends_at', today())->where('status', 'active')->count(),
    ];

    \Log::info('📊 Relatório diário de subscrições', [
        'date' => today()->format('Y-m-d'),
        'stats' => $stats
    ]);

    // Alerta se houver muitas expirações próximas
    if ($stats['expiring_soon'] > 10) {
        \Log::warning("⚠️ ATENÇÃO: {$stats['expiring_soon']} subscrições expirando nos próximos 7 dias!");
    }

    // Alerta se houver expirações hoje
    if ($stats['expiring_today'] > 0) {
        \Log::warning("🚨 CRÍTICO: {$stats['expiring_today']} subscrições expirando HOJE!");
    }
})->dailyAt('23:00');
