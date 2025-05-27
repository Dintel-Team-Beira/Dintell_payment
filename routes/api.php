<?php
// routes/api.php

use App\Http\Controllers\Api\SubscriptionVerificationController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Route;

// API pública para verificação de domínios
Route::prefix('v1')->group(function () {
    // 🔍 Verificação rápida (sem autenticação)
    Route::get('/check/{domain}', [SubscriptionVerificationController::class, 'quickCheck'])
        ->name('check')
        ->middleware('throttle:100,1'); // 100 requests por minuto

    // 📊 Status detalhado (com API key opcional)
    Route::get('/status/{domain}', [SubscriptionVerificationController::class, 'detailedStatus'])
        ->name('status')
        ->middleware('throttle:60,1');

    // 📈 Analytics avançado (requer API key)
    Route::get('/analytics/{domain}', [SubscriptionVerificationController::class, 'analytics'])
        ->name('analytics')
        ->middleware('throttle:30,1');

    // 🚨 Webhook registration
    Route::post('/webhook', [SubscriptionVerificationController::class, 'webhook'])
        ->name('webhook')
        ->middleware('throttle:10,1');

    // 🏥 Health check
    Route::get('/health', [SubscriptionVerificationController::class, 'health'])
        ->name('health');

    // 📊 Batch verification (múltiplos domínios)
    Route::post('/batch-check', [SubscriptionVerificationController::class, 'batchCheck'])
        ->name('batch')
        ->middleware('throttle:10,1');
});

// Rotas protegidas com autenticação
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // Informações da própria subscrição
    // Route::get('/my-subscription', [SubscriptionVerificationController::class, 'mySubscription'])
    //      ->name('api.my-subscription');

    // Atualizar uso
    // Route::post('/usage/update', [SubscriptionVerificationController::class, 'updateUsage'])
    //      ->name('api.usage.update');

    // Histórico de requests
    // Route::get('/usage/history', [SubscriptionVerificationController::class, 'usageHistory'])
    //      ->name('api.usage.history');


    // API routes for plans
    // Route::get('api/plans/active', [PlansController::class, 'getActivePlans'])->name('api.plans.active');
    // Route::get('api/plans/statistics', [PlansController::class, 'statistics'])->name('api.plans.statistics');
    Route::get('/api/email-stats', [EmailController::class, 'apiStats'])->name('api.email.stats');
});
