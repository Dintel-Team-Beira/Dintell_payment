<?php
// routes/api.php

use App\Http\Controllers\Api\BillingApiController;
use App\Http\Controllers\Api\SubscriptionVerificationController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SupportController;
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


Route::middleware(['auth:sanctum'])->prefix('v1/billing')->group(function () {

    // Dashboard
    Route::get('/dashboard', [BillingApiController::class, 'dashboard']);

    // Faturas
    Route::get('/invoices', [BillingApiController::class, 'invoices']);
    Route::get('/invoices/{invoice}', [BillingApiController::class, 'invoice']);
    Route::post('/invoices/{invoice}/mark-as-paid', [BillingApiController::class, 'markInvoiceAsPaid']);

    // Cotações
    Route::get('/quotes', [BillingApiController::class, 'quotes']);
    Route::get('/quotes/{quote}', [BillingApiController::class, 'quote']);
    Route::post('/quotes/{quote}/convert-to-invoice', [BillingApiController::class, 'convertQuoteToInvoice']);
});



  // API Routes para AJAX
  Route::prefix('api')->name('api.')->group(function () {
    Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');
    Route::get('/invoices/stats', [InvoiceController::class, 'stats'])->name('invoices.stats');
    Route::get('/quotes/stats', [QuoteController::class, 'stats'])->name('quotes.stats');
    Route::get('/dashboard/chart-data', [BillingController::class, 'getChartDataApi'])->name('dashboard.chart-data');
    Route::get('/dashboard/stats', [BillingController::class, 'getStats'])->name('dashboard.stats');

    // Ações em lote
    Route::post('/bulk-delete', [BillingController::class, 'bulkDelete'])->name('bulk-delete');
    Route::post('/bulk-send', [BillingController::class, 'bulkSend'])->name('bulk-send');
    Route::post('/bulk-archive', [BillingController::class, 'bulkArchive'])->name('bulk-archive');
});







// Rotas para produtos
Route::prefix('products')->group(function () {
    Route::get('/active/{user_id}', [QuoteController::class, 'getActiveProducts']);
    Route::get('/categories', [ProductController::class, 'getCategories']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/{product}', [ProductController::class, 'show']);
    Route::post('/bulk-delete', [ProductController::class, 'bulkDelete']);
    Route::post('/bulk-deactivate', [ProductController::class, 'bulkDeactivate']);
    Route::post('/{product}/toggle-status', [ProductController::class, 'toggleStatus']);
    Route::post('/{id}/toggle-status-id', [ProductController::class, 'toggleStatusById']);
});

// Rotas para serviços
Route::prefix('services')->group(function () {
    Route::get('/active/{user_id}', [QuoteController::class, 'getActiveServices']);
    Route::get('/categories', [ServiceController::class, 'getCategories']);
    Route::get('/complexity-levels', [ServiceController::class, 'getComplexityLevels']);
    Route::get('/templates', [ServiceController::class, 'getTemplates']);
    Route::get('/search', [ServiceController::class, 'search']);
    Route::get('/{service}', [ServiceController::class, 'show']);
    Route::post('/bulk-delete', [ServiceController::class, 'bulkDelete']);
    Route::post('/bulk-deactivate', [ServiceController::class, 'bulkDeactivate']);
    Route::post('/{service}/toggle-status', [ServiceController::class, 'toggleStatus']);
});

// Route::get('/products/active', [ProductController::class, 'getActiveProducts']);
// Route::get('/services/active', [ServiceController::class, 'getActiveServices']);
// Rotas para cotações
Route::prefix('quotes')->group(function () {
    Route::get('/statistics', [QuoteController::class, 'getStatistics']);
    Route::post('/{quote}/send-email', [QuoteController::class, 'sendEmail']);
    Route::post('/{quote}/convert-to-invoice', [QuoteController::class, 'convertToInvoice']);
    Route::post('/{quote}/update-status', [QuoteController::class, 'updateStatus']);
    Route::get('/{quote}/pdf', [QuoteController::class, 'downloadPdf']);
    Route::post('/{quote}/duplicate', [QuoteController::class, 'duplicate']);
});

// Rotas para clientes (se necessário)
Route::prefix('clients')->group(function () {
    Route::get('/active', function () {
        return response()->json(\App\Models\Client::where('is_active', true)->orderBy('name')->get(['id', 'name', 'email', 'phone']));
    });
    Route::get('/search', function (\Illuminate\Http\Request $request) {
        $query = \App\Models\Client::where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return response()->json($query->orderBy('name')->get(['id', 'name', 'email', 'phone']));
    });
});




  // API de Faturas
  Route::prefix('invoices')->group(function () {
    Route::get('/', [InvoiceController::class, 'apiIndex']);
    Route::post('/', [InvoiceController::class, 'apiStore']);
    Route::get('/{invoice}', [InvoiceController::class, 'apiShow']);
    Route::put('/{invoice}', [InvoiceController::class, 'apiUpdate']);
    Route::delete('/{invoice}', [InvoiceController::class, 'apiDestroy']);

    // Ações específicas via API
    Route::post('/{invoice}/mark-paid', [InvoiceController::class, 'apiMarkAsPaid']);
    Route::patch('/{invoice}/status', [InvoiceController::class, 'apiUpdateStatus']);
    Route::post('/{invoice}/send-email', [InvoiceController::class, 'apiSendEmail']);

    // Estatísticas
    Route::get('/stats/dashboard', [InvoiceController::class, 'apiDashboardStats']);
    Route::get('/stats/monthly', [InvoiceController::class, 'apiMonthlyStats']);
});

// API de Clientes para Faturas
Route::get('/clients/{client}/quotes', [InvoiceController::class, 'getClientQuotes']);
Route::get('/quotes/{quote}/items', [InvoiceController::class, 'getQuoteItems']);



Route::get('/dashboard-stats', [BillingController::class, 'getDashboardStats'])->name('api.dashboard.stats');
Route::get('/chart-data', [BillingController::class, 'getChartData'])->name('api.chart.data');
Route::get('/top-clients', [BillingController::class, 'getTopClients'])->name('api.top.clients');
Route::get('/expired-quotes', [BillingController::class, 'getExpiredQuotes'])->name('api.expired-quotes');
Route::get('/quote-stats', [BillingController::class, 'getQuoteStats'])->name('api.quote-stats');
Route::post('/mark-expired-quotes', [BillingController::class, 'markExpiredQuotes'])->name('api.mark-expired-quotes');

// Rota para serviços ativos (para usar em cotações/faturas)

Route::get('services/search', [ServiceController::class, 'search'])->name('api.services.search');
Route::get('services/stats', [ServiceController::class, 'stats'])->name('api.services.stats');
Route::get('services/{service}/calculate-price', [ServiceController::class, 'calculatePrice'])->name('api.services.calculate-price');
Route::get('services/templates', [ServiceController::class, 'getTemplates'])->name('api.services.templates');
Route::post('services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('api.services.toggle-status');

Route::get('/services/active', function() {
    return App\Models\Service::active()
        ->select('id', 'name', 'code', 'hourly_rate', 'fixed_price', 'estimated_hours', 'complexity_level', 'description')
        ->orderBy('name')
        ->get()
        ->map(function ($service) {
            return [
                'id' => $service->id,
                'type' => 'service',
                'name' => $service->name,
                'code' => $service->code,
                'hourly_rate' => $service->hourly_rate,
                'fixed_price' => $service->fixed_price,
                'estimated_cost' => $service->estimated_cost,
                'description' => $service->description,
                'estimated_hours' => $service->estimated_hours,
                'complexity_level' => $service->complexity_level
            ];
        });
})->name('api.services.active');

// Se você quiser uma rota para o index que funcione tanto com /servicos quanto /servicos/dintell:
Route::get('servicos/{any?}', [ServiceController::class, 'index'])
    ->where('any', '.*')
    ->name('services.index.fallback');


Route::prefix('support')->name('api.support.')->group(function () {

    // API para o popup de suporte
    Route::get('/tickets/my', [SupportController::class, 'myTickets'])->name('tickets.my');
    Route::get('/tickets/{ticketId}/{idUser}', [SupportController::class, 'show'])->name('tickets.show');
    Route::post('/tickets', [SupportController::class, 'store'])->name('tickets.store');
    Route::get('/knowledge-base/search', [SupportController::class, 'searchKnowledgeBase'])->name('kb.search');
    Route::get('/stats', [SupportController::class, 'getUserStats'])->name('stats');

    // Quick actions
    Route::post('/tickets/{ticket}/reply', [SupportController::class, 'addReply'])->name('tickets.quick-reply');
    Route::patch('/tickets/{ticket}/quick-close', [SupportController::class, 'close'])->name('tickets.quick-close');
});


// No arquivo routes/api.php (ou web.php se você quiser manter como web routes)

Route::middleware(['auth:sanctum'])->prefix('api/support')->name('api.support.')->group(function () {
    // API para o popup de suporte
    Route::get('/tickets/my', [SupportController::class, 'myTickets'])->name('tickets.my');
    Route::post('/tickets', [SupportController::class, 'store'])->name('tickets.store');
    Route::get('/knowledge-base/search', [SupportController::class, 'searchKnowledgeBase'])->name('kb.search');
    Route::get('/stats', [SupportController::class, 'getUserStats'])->name('stats');

    // Quick actions
    Route::post('/tickets/{ticket}/quick-reply', [SupportController::class, 'addReply'])->name('tickets.quick-reply');
    Route::patch('/tickets/{ticket}/quick-close', [SupportController::class, 'close'])->name('tickets.quick-close');
});

// OU se você quiser manter como web routes (recomendado para este caso):
// No arquivo routes/web.php

// Adicionar no final do arquivo
Route::middleware(['web', 'auth'])->prefix('api/support')->group(function () {
    Route::get('/tickets', [App\Http\Controllers\SupportController::class, 'index']);
    Route::get('/tickets/{ticket}', [App\Http\Controllers\SupportController::class, 'show']);
    Route::post('/tickets', [App\Http\Controllers\SupportController::class, 'store']);
    Route::post('/tickets/{ticket}/messages', [App\Http\Controllers\SupportController::class, 'sendMessage']);
    Route::patch('/tickets/{ticket}/close', [App\Http\Controllers\SupportController::class, 'close']);
    Route::post('/tickets/{ticket}/rate', [App\Http\Controllers\SupportController::class, 'rate']);
});
