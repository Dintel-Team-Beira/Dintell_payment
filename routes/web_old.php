<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SuspensionPageController;
use App\Http\Controllers\ApiLogController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CashSaleController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\DebitNoteController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

// Página pública de suspensão (deve ficar ANTES das rotas autenticadas)
Route::get('/suspended/{domain}', [SuspensionPageController::class, 'show'])
    ->name('suspension.page');

// Rotas de renovação pública (para links de email)
Route::get('/renew/{subscription}', [SubscriptionController::class, 'showRenewal'])
    ->name('subscription.renew');
Route::post('/renew/{subscription}', [SubscriptionController::class, 'processRenewal'])
    ->name('subscription.renew.process');

// Rota Home corrigida
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Rotas autenticadas e verificadas
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

    // Ações especiais de subscrições
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
    Route::patch('plans/{plan}/toggle-status', [SubscriptionPlanController::class, 'toggleStatus'])
        ->name('plans.toggle-status');
    Route::post('plans/{plan}/duplicate', [SubscriptionPlanController::class, 'duplicate'])
        ->name('plans.duplicate');

    // Logs da API
    Route::prefix('api-logs')->name('api-logs.')->group(function () {
        Route::get('/', [ApiLogController::class, 'index'])->name('index');
        Route::get('/{apiLog}', [ApiLogController::class, 'show'])->name('show');
        Route::delete('/{apiLog}', [ApiLogController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [ApiLogController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/cleanup', [ApiLogController::class, 'cleanup'])->name('cleanup');
        Route::get('/export/data', [ApiLogController::class, 'export'])->name('export');
        Route::get('/statistics/data', [ApiLogController::class, 'statistics'])->name('statistics');
    });

    // Logs de Email (corrigido conflitos de nomes)
    Route::prefix('email-logs')->name('email-logs.')->group(function () {
        Route::get('/', [EmailController::class, 'logs'])->name('index');
        Route::get('/{emailLog}', [EmailController::class, 'show'])->name('show');
        Route::post('/{emailLog}/resend', [EmailController::class, 'resend'])->name('resend');
        Route::post('/bulk-resend', [EmailController::class, 'bulkResend'])->name('bulk-resend');
        Route::post('/cleanup', [EmailController::class, 'cleanup'])->name('cleanup');
        Route::get('/export/data', [EmailController::class, 'export'])->name('export');
        Route::post('/test-email', [EmailController::class, 'test'])->name('test');
    });

    // Relatórios
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/revenue', [DashboardController::class, 'revenueReport'])->name('revenue');
        Route::get('/clients', [DashboardController::class, 'clientsReport'])->name('clients');
        Route::get('/usage', [DashboardController::class, 'usageReport'])->name('usage');
        Route::get('/export/{type}', [DashboardController::class, 'exportReport'])->name('export');
    });
});

// Rotas apenas autenticadas (sem verificação de email)
Route::middleware(['auth'])->group(function () {

    // Dashboard de Faturação
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/dashboard', [BillingController::class, 'dashboard'])->name('dashboard');
        Route::get('/reports', [BillingController::class, 'reports'])->name('reports');
        Route::get('/export', [BillingController::class, 'export'])->name('export');

        // Configurações de faturação
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [BillingController::class, 'index'])->name('index');
            Route::post('/update', [BillingController::class, 'updateSettings'])->name('update');
        });
    });

    // Cotações
    Route::prefix('quotes')->name('quotes.')->group(function () {
        Route::get('/', [QuoteController::class, 'index'])->name('index');
        Route::get('/create', [QuoteController::class, 'create'])->name('create');
        Route::post('/', [QuoteController::class, 'store'])->name('store');
        Route::get('/{quote}', [QuoteController::class, 'show'])->name('show');
        Route::get('/{quote}/edit', [QuoteController::class, 'edit'])->name('edit');
        Route::put('/{quote}', [QuoteController::class, 'update'])->name('update');
        Route::delete('/{quote}', [QuoteController::class, 'destroy'])->name('destroy');
        Route::post('/{quote}/convert-to-invoice', [QuoteController::class, 'convertToInvoice'])->name('convert-to-invoice');
        Route::patch('/{quote}/status', [QuoteController::class, 'updateStatus'])->name('update-status');
        Route::get('/{quote}/pdf', [QuoteController::class, 'downloadPdf'])->name('download-pdf');
        Route::post('/{quote}/send-email', [QuoteController::class, 'sendEmail'])->name('send-email');
        Route::post('/{quote}/duplicate', [QuoteController::class, 'duplicate'])->name('duplicate');
        Route::get('/export/data', [QuoteController::class, 'export'])->name('export');
        Route::get('/statistics/data', [QuoteController::class, 'getStatistics'])->name('statistics');
    });

    // Faturas
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/store', [InvoiceController::class, 'store'])->name('store');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');

        // Ações específicas de faturas
        Route::post('/{invoice}/mark-as-paid', [InvoiceController::class, 'markAsPaid'])->name('mark-as-paid');
        Route::patch('/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('update-status');
        Route::get('/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('download-pdf');
        Route::post('/{invoice}/send-email', [InvoiceController::class, 'sendByEmail'])->name('send-email');
        Route::post('/{invoice}/duplicate', [InvoiceController::class, 'duplicate'])->name('duplicate');

        // Ações em massa
        Route::post('/bulk-update-status', [InvoiceController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
        Route::get('/bulk-download-pdf', [InvoiceController::class, 'bulkDownloadPdf'])->name('bulk-download-pdf');

        // Exportação e relatórios
        Route::get('/export/data', [InvoiceController::class, 'export'])->name('export');
        Route::get('/by-type/{type}', [InvoiceController::class, 'indexByType'])->name('by-type');
    });

    // API Routes para AJAX
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/clients/{client}/quotes', [InvoiceController::class, 'getClientQuotes'])->name('clients.quotes');
        Route::get('/quotes/{quote}/items', [InvoiceController::class, 'getQuoteItems'])->name('quotes.items');
        Route::get('/invoices/stats', [InvoiceController::class, 'getStats'])->name('invoices.stats');
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
    Route::prefix('produtos')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');

        // Ações específicas
        Route::get('/{product}/duplicate', [ProductController::class, 'duplicate'])->name('duplicate');
        Route::post('/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/export/data', [ProductController::class, 'export'])->name('export');
        Route::post('/import/data', [ProductController::class, 'import'])->name('import');
        Route::get('/low-stock/list', [ProductController::class, 'lowStock'])->name('low-stock');
    });

    // Serviços
    Route::prefix('servicos')->name('services.')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('index');
        Route::get('/create', [ServiceController::class, 'create'])->name('create');
        Route::post('/', [ServiceController::class, 'store'])->name('store');
        Route::get('/{service}', [ServiceController::class, 'show'])->name('show');
        Route::get('/{service}/edit', [ServiceController::class, 'edit'])->name('edit');
        Route::put('/{service}', [ServiceController::class, 'update'])->name('update');
        Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('destroy');

        // Ações específicas
        Route::get('/{service}/duplicate', [ServiceController::class, 'duplicate'])->name('duplicate');
        Route::post('/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/export/data', [ServiceController::class, 'export'])->name('export');
        Route::post('/import/data', [ServiceController::class, 'import'])->name('import');
    });

    // Vendas à Dinheiro
    Route::prefix('cash-sales')->name('cash-sales.')->group(function () {
        Route::get('/create', [CashSaleController::class, 'create'])->name('create');
        Route::post('/', [CashSaleController::class, 'store'])->name('store');
        Route::get('/quick-sale', [CashSaleController::class, 'quickSale'])->name('quick-sale');
    });

    // Notas de Crédito
    Route::resource('credit-notes', CreditNoteController::class)->names([
        'index' => 'credit-notes.index',
        'create' => 'credit-notes.create',
        'store' => 'credit-notes.store',
        'show' => 'credit-notes.show',
        'edit' => 'credit-notes.edit',
        'update' => 'credit-notes.update',
        'destroy' => 'credit-notes.destroy'
    ]);
 // Ações específicas
 Route::group(['prefix' => 'credit-notes', 'as' => 'credit-notes.'], function () {

     Route::get('/{creditNote}/pdf', [CreditNoteController::class, 'downloadPdf'])->name('download-pdf');
     Route::post('/{creditNote}/send-email', [CreditNoteController::class, 'sendByEmail'])->name('send-email');
     Route::post('/{creditNote}/duplicate', [CreditNoteController::class, 'duplicate'])->name('duplicate');
    });
    // Notas de Débito
    // Route::resource('debit-notes', DebitNoteController::class)->names([
    //     'index' => 'debit-notes.index',
    //     'create' => 'debit-notes.create',
    //     'store' => 'debit-notes.store',
    //     'show' => 'debit-notes.show',
    //     'edit' => 'debit-notes.edit',
    //     'update' => 'debit-notes.update',
    //     'destroy' => 'debit-notes.destroy'
    // ]);
});


 // Notas de Débito
    Route::prefix('debit-notes')->name('debit-notes.')->group(function () {
        Route::get('/', [DebitNoteController::class, 'index'])->name('index');
        Route::get('/create', [DebitNoteController::class, 'create'])->name('create');
        Route::post('/', [DebitNoteController::class, 'store'])->name('store');
        Route::get('/{debitNote}', [DebitNoteController::class, 'show'])->name('show');
        Route::get('/{debitNote}/edit', [DebitNoteController::class, 'edit'])->name('edit');
        Route::put('/{debitNote}', [DebitNoteController::class, 'update'])->name('update');
        Route::delete('/{debitNote}', [DebitNoteController::class, 'destroy'])->name('destroy');

        // Ações específicas - TODAS as rotas necessárias
        Route::get('/{debitNote}/pdf', [DebitNoteController::class, 'downloadPdf'])->name('download-pdf');
        Route::post('/{debitNote}/send-email', [DebitNoteController::class, 'sendByEmail'])->name('send-email');
        Route::post('/{debitNote}/duplicate', [DebitNoteController::class, 'duplicate'])->name('duplicate');
        Route::post('/{debitNote}/mark-as-paid', [DebitNoteController::class, 'markAsPaid'])->name('mark-as-paid');
        Route::patch('/{debitNote}/status', [DebitNoteController::class, 'updateStatus'])->name('update-status');

        // API para AJAX
        Route::get('/api/debit-type-details', [DebitNoteController::class, 'getDebitTypeDetails'])->name('api.debit-type-details');
        Route::post('/api/calculate-preview', [DebitNoteController::class, 'calculatePreview'])->name('api.calculate-preview');

        // Exportação
        Route::get('/export/data', [DebitNoteController::class, 'export'])->name('export');
    });

// Utilitários
Route::get('/limpar-cache', function () {
    Artisan::call('optimize:clear');
    return 'Cache limpo com sucesso!';
});

require __DIR__ . '/auth.php';
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
