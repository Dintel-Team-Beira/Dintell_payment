<?php
// routes/api.php

use App\Http\Controllers\Api\SubscriptionVerificationController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

// API pública para verificação de domínios
Route::prefix('v1')->group(function () {

    // Verificação completa de subscrição
    Route::post('/subscription/verify', [SubscriptionVerificationController::class, 'verify'])
         ->name('api.subscription.verify');

    // Verificação rápida (para middleware)
    Route::post('/subscription/quick-check', [SubscriptionVerificationController::class, 'quickCheck'])
         ->name('api.subscription.quick-check');

    // Status de domínio específico
    Route::get('/domain/{domain}/status', [SubscriptionVerificationController::class, 'domainStatus'])
         ->name('api.domain.status');

    // Webhooks para pagamentos
    Route::post('/webhooks/payment', [WebhookController::class, 'handlePayment'])
         ->name('api.webhooks.payment');

    // Webhook para MPesa
    Route::post('/webhooks/mpesa', [WebhookController::class, 'handleMpesa'])
         ->name('api.webhooks.mpesa');
});

// Rotas protegidas com autenticação
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // Informações da própria subscrição
    Route::get('/my-subscription', [SubscriptionVerificationController::class, 'mySubscription'])
         ->name('api.my-subscription');

    // Atualizar uso
    Route::post('/usage/update', [SubscriptionVerificationController::class, 'updateUsage'])
         ->name('api.usage.update');

    // Histórico de requests
    Route::get('/usage/history', [SubscriptionVerificationController::class, 'usageHistory'])
         ->name('api.usage.history');


            // API routes for plans
    Route::get('api/plans/active', [PlansController::class, 'getActivePlans'])->name('api.plans.active');
    Route::get('api/plans/statistics', [PlansController::class, 'statistics'])->name('api.plans.statistics');
});