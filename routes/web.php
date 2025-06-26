<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SuspensionPageController;
use App\Http\Controllers\ApiLogController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingsController;
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


Route::middleware(['auth'])->group(function () {

    // Dashboard de Faturação
    Route::get('/billing/dashboard', [App\Http\Controllers\BillingController::class, 'dashboard'])->name('billing.dashboard');
    Route::get('/billing/reports', [App\Http\Controllers\BillingController::class, 'reports'])->name('billing.reports');

    // Cotações
    Route::get('/quotes/index', [App\Http\Controllers\QuoteController::class, 'index'])->name('quotes.index');
    Route::prefix('quotes')->name('quotes.')->group(function () {
        Route::get('/create', [App\Http\Controllers\QuoteController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\QuoteController::class, 'store'])->name('store');
        Route::get('/{quote}', [App\Http\Controllers\QuoteController::class, 'show'])->name('show');
        Route::get('/{quote}/edit', [App\Http\Controllers\QuoteController::class, 'edit'])->name('edit');
        Route::put('/{quote}', [App\Http\Controllers\QuoteController::class, 'update'])->name('update');
        Route::delete('/{quote}', [App\Http\Controllers\QuoteController::class, 'destroy'])->name('destroy');
        Route::post('/{quote}/convert-to-invoice', [App\Http\Controllers\QuoteController::class, 'convertToInvoice'])->name('convert-to-invoice');
        Route::patch('/{quote}/status', [App\Http\Controllers\QuoteController::class, 'updateStatus'])->name('update-status');
        Route::get('/{quote}/pdf', [App\Http\Controllers\QuoteController::class, 'downloadPdf'])->name('download-pdf');
    });

    // Faturas
    Route::get('/invoices/index', [App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices.index');
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/create', [App\Http\Controllers\InvoiceController::class, 'create'])->name('create');
        Route::post('/store', [App\Http\Controllers\InvoiceController::class, 'store'])->name('store');
        Route::get('/{invoice}', [App\Http\Controllers\InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/edit', [App\Http\Controllers\InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{invoice}', [App\Http\Controllers\InvoiceController::class, 'update'])->name('update');
        Route::delete('/{invoice}', [App\Http\Controllers\InvoiceController::class, 'destroy'])->name('destroy');
        Route::post('/{invoice}/mark-as-paid', [App\Http\Controllers\InvoiceController::class, 'markAsPaid'])->name('mark-as-paid');
        Route::patch('/{invoice}/status', [App\Http\Controllers\InvoiceController::class, 'updateStatus'])->name('update-status');
        Route::get('/{invoice}/pdf', [App\Http\Controllers\InvoiceController::class, 'downloadPdf'])->name('download-pdf');
        Route::post('/{invoice}/send-email', [App\Http\Controllers\InvoiceController::class, 'sendByEmail'])->name('send-email');
    });

    // Configurações de Faturação
    Route::prefix('billing/settings')->name('billing.settings.')->group(function () {
        Route::get('/', [App\Http\Controllers\BillingController::class, 'index'])->name('index');
        Route::put('/', [App\Http\Controllers\BillingController::class, 'update'])->name('update');
    });
});


 // Configurações
 Route::prefix('configuracoes')->name('settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::get('/empresa', [SettingsController::class, 'company'])->name('company');
    Route::post('/empresa', [SettingsController::class, 'updateCompany'])->name('company.update');
    Route::get('/faturamento', [SettingsController::class, 'billing'])->name('billing');
    Route::post('/faturamento', [SettingsController::class, 'updateBilling'])->name('billing.update');
    Route::get('/notificacoes', [SettingsController::class, 'notifications'])->name('notifications');
    Route::post('/notificacoes', [SettingsController::class, 'updateNotifications'])->name('notifications.update');
    Route::get('/impostos', [SettingsController::class, 'taxes'])->name('taxes');
    Route::post('/impostos', [SettingsController::class, 'updateTaxes'])->name('taxes.update');
    Route::get('/backup', [SettingsController::class, 'backup'])->name('backup');
    Route::post('/reset', [SettingsController::class, 'reset'])->name('reset');
});

// Produtos
Route::get('/produtos/index',[ProductController::class, 'index']);
Route::resource('produtos/create', ProductController::class)->names([
    'index' => 'products.index',
    'create' => 'products.create',
    'store' => 'products.store',
    'show' => 'products.show',
    'edit' => 'products.edit',
    'update' => 'products.update',
    'destroy' => 'products.destroy'
]);

// Rotas específicas de produtos
Route::prefix('produtos')->name('products.')->group(function () {
    Route::get('{product}/duplicate', [ProductController::class, 'duplicate'])->name('duplicate');
    Route::post('{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('export', [ProductController::class, 'export'])->name('export');
    Route::post('import', [ProductController::class, 'import'])->name('import');
    Route::get('low-stock', [ProductController::class, 'lowStock'])->name('low-stock');
});

// Serviços
Route::get('servicos/dintell',[ServiceController::class, 'index'])->name('novo');
Route::get('servicos/dintell',[ServiceController::class, 'index'])->name('servicos');
Route::resource('servicos', ServiceController::class)->names([
    'index' => 'services.index',
    'create' => 'services.create',
    'store' => 'services.store',
    'show' => 'services.show',
    'edit' => 'services.edit',
    'update' => 'services.update',
    'destroy' => 'services.destroy'
]);

// Rotas específicas de serviços
Route::prefix('servicos')->name('services.')->group(function () {
    Route::get('{service}/duplicate', [ServiceController::class, 'duplicate'])->name('duplicate');
    Route::post('{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('export', [ServiceController::class, 'export'])->name('export');
    Route::post('import', [ServiceController::class, 'import'])->name('import');
});