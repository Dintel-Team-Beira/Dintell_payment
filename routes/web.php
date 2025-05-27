<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SuspensionPageController;
use App\Http\Controllers\ApiLogController;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::get('/', function () {
   if (auth()->check()) {
       return redirect()->route('dashboard');
   }else{
         return redirect()->route('login');
   }
})->name('home');
// Página pública de suspensão
Route::get('/suspended/{domain}', [SuspensionPageController::class, 'show'])
    ->name('suspension.page');

// Rotas de renovação pública (para links de email)
Route::get('/renew/{subscription}', [SubscriptionController::class, 'showRenewal'])
    ->name('subscription.renew');
Route::post('/renew/{subscription}', [SubscriptionController::class, 'processRenewal'])
    ->name('subscription.renew.process');

// Rotas autenticadas
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');

    // Gestão de Clientes
    Route::resource('clients', ClientController::class);
    Route::post('/clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])
        ->name('clients.toggle-status');

    // Gestão de Subscrições
    Route::resource('subscriptions', SubscriptionController::class);

    // Ações especais de subscrições
    Route::prefix('subscriptions/{subscription}')->group(function () {
        Route::post('/suspend', [SubscriptionController::class, 'suspend'])
            ->name('subscriptions.suspend');
        Route::post('/activate', [SubscriptionController::class, 'activate'])
            ->name('subscriptions.activate');
        Route::post('/cancel', [SubscriptionController::class, 'cancel'])
            ->name('subscriptions.cancel');
        Route::post('/renew', [SubscriptionController::class, 'renew'])
            ->name('subscriptions.renew');
        Route::post('/regenerate-key', [SubscriptionController::class, 'regenerateApiKey'])
            ->name('subscriptions.regenerate-key');
        Route::post('/toggle-manual', [SubscriptionController::class, 'toggleManualStatus'])
            ->name('subscriptions.toggle-manual');
    });

    // Gestão de Planos
    Route::resource('plans', SubscriptionPlanController::class);
    Route::post('/plans/{plan}/toggle', [SubscriptionPlanController::class, 'toggle'])
        ->name('plans.toggle');
    Route::patch('plans/{plan}/toggle-status', [SubscriptionPlanController::class, 'toggleStatus'])->name('plans.toggle-status');
    Route::post('plans/{plan}/duplicate', [SubscriptionPlanController::class, 'duplicate'])->name('plans.duplicate');

    // Logs da API
    Route::get('/api-logs', [ApiLogController::class, 'index'])->name('api-logs.index');
    Route::get('/api-logs/{apiLog}', [ApiLogController::class, 'show'])->name('api-logs.show');
    // Route::delete('/api-logs/cleanup', [ApiLogController::class, 'cleanup'])->name('api-logs.cleanup');
        // Excluir logs
        Route::delete('/api-logs/{apiLog}', [ApiLogController::class, 'destroy'])->name('api-logs.destroy');
        Route::post('/api-logs/bulk-delete', [ApiLogController::class, 'bulkDelete'])->name('api-logs.bulk-delete');
        Route::post('/api-logs/cleanup', [ApiLogController::class, 'cleanup'])->name('api-logs.cleanup');

        // Exportar e estatísticas
        Route::get('/api-logs-export', [ApiLogController::class, 'export'])->name('api-logs.export');
        Route::get('/api-logs-statistics', [ApiLogController::class, 'statistics'])->name('api-logs.statistics');

    // Logs de Email
    Route::get('/email-logs', [EmailController::class, 'logs'])->name('email-logs.index');
    Route::get('/email-logs/{emailLog}', [EmailController::class, 'show'])->name('email-logs.show');
    Route::post('/email-logs/{emailLog}/resend', [EmailController::class, 'resend'])->name('email-logs.resend');
    Route::post('/email-logs/bulk-resend', [EmailController::class, 'bulkResend'])->name('email-logs.bulk-resend');
    Route::post('/email-logs/cleanup', [EmailController::class, 'cleanup'])->name('email-logs.cleanup');
    Route::get('/email-logs/export', [EmailController::class, 'export'])->name('email-logs.export');
    Route::post('/email/test', [EmailController::class, 'test'])->name('email.test');
    Route::get('/export', [EmailController::class, 'export'])->name('export');
    Route::post('/bulk-resend', [EmailController::class, 'bulkResend'])->name('bulk-resend');
    Route::post('/cleanup', [EmailController::class, 'cleanup'])->name('cleanup');
    Route::get('/{emailLog}', [EmailController::class, 'show'])->name('show');
    Route::post('/{emailLog}/resend', [EmailController::class, 'resend'])->name('resend');

    // Relatórios
    Route::prefix('reports')->group(function () {
        Route::get('/revenue', [DashboardController::class, 'revenueReport'])->name('reports.revenue');
        Route::get('/clients', [DashboardController::class, 'clientsReport'])->name('reports.clients');
        Route::get('/usage', [DashboardController::class, 'usageReport'])->name('reports.usage');
        Route::get('/export/{type}', [DashboardController::class, 'exportReport'])->name('reports.export');
    });

    // Configurações do sistema
    // Route::prefix('settings')->group(function () {
    //     Route::get('/', [SettingsController::class, 'index'])->name('settings.index');
    //     Route::post('/', [SettingsController::class, 'update'])->name('settings.update');
    // });
});
