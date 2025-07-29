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
use App\Http\Controllers\ProfileController;

// Controllers Admin (SaaS)
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CompaniesController as AdminCompaniesController;
use App\Http\Controllers\Admin\UsersController as AdminUsersController;
use App\Http\Controllers\Admin\InvoicesController as AdminInvoicesController;
use App\Http\Controllers\Admin\ReportsController as AdminReportsController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;

// Middlewares
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\TenantMiddleware;
use App\Http\Middleware\CheckSubscriptionMiddleware;
use App\Http\Middleware\CheckFeatureMiddleware;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| ROTAS PÚBLICAS (sem mudanças)
|--------------------------------------------------------------------------
*/

Route::get('/suspended/{domain}', [SuspensionPageController::class, 'show'])
    ->name('suspension.page');

Route::get('/renew/{subscription}', [SubscriptionController::class, 'showRenewal'])
    ->name('subscription.renew');
Route::post('/renew/{subscription}', [SubscriptionController::class, 'processRenewal'])
    ->name('subscription.renew.process');

/*
|--------------------------------------------------------------------------
| ROTAS ADMINISTRATIVAS (sem mudanças)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::post('logout', [AdminAuthController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');

    Route::middleware(['auth', AdminMiddleware::class])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Gestão de Empresas
        Route::prefix('companies')->name('companies.')->group(function () {
            Route::get('/', [AdminCompaniesController::class, 'index'])->name('index');
            Route::get('/create', [AdminCompaniesController::class, 'create'])->name('create');
            Route::post('/', [AdminCompaniesController::class, 'store'])->name('store');
            Route::get('/{company}', [AdminCompaniesController::class, 'show'])->name('show');
            Route::get('/{company}/edit', [AdminCompaniesController::class, 'edit'])->name('edit');
            Route::put('/{company}', [AdminCompaniesController::class, 'update'])->name('update');
            Route::delete('/{company}', [AdminCompaniesController::class, 'destroy'])->name('destroy');

            Route::post('/{company}/suspend', [AdminCompaniesController::class, 'suspend'])->name('suspend');
            Route::post('/{company}/activate', [AdminCompaniesController::class, 'activate'])->name('activate');
            Route::post('/{company}/extend-trial', [AdminCompaniesController::class, 'extendTrial'])->name('extend-trial');
            Route::get('/{company}/impersonate', [AdminCompaniesController::class, 'impersonate'])->name('impersonate');
            Route::get('/{company}/analytics', [AdminCompaniesController::class, 'analytics'])->name('analytics');
            Route::get('/export', [AdminCompaniesController::class, 'export'])->name('export');
        });

        Route::get('/stop-impersonation', [AdminCompaniesController::class, 'stopImpersonation'])->name('stop-impersonation');

        // Outras rotas admin... (manter todas como estavam)
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminUsersController::class, 'index'])->name('index');
            Route::get('/create', [AdminUsersController::class, 'create'])->name('create');
            Route::post('/', [AdminUsersController::class, 'store'])->name('store');
            Route::get('/{user}', [AdminUsersController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [AdminUsersController::class, 'edit'])->name('edit');
            Route::put('/{user}', [AdminUsersController::class, 'update'])->name('update');
            Route::delete('/{user}', [AdminUsersController::class, 'destroy'])->name('destroy');
        });
    });
});

/*
|--------------------------------------------------------------------------
| ROTA HOME
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();

    if ($user->is_super_admin ?? false) {
        return redirect()->route('admin.dashboard');
    }

    // SIMPLES: sempre ir para dashboard sem slug
    return redirect('/dashboard');
})->name('home');
/*
|--------------------------------------------------------------------------
| ROTAS COM SLUG DA EMPRESA
|--------------------------------------------------------------------------
*/

// Grupo de rotas com slug - usando NOMES ORIGINAIS das rotas
Route::prefix('{company_slug}')->middleware(['auth', 'verified', TenantMiddleware::class])->group(function () {

    // Dashboard da Empresa
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.with-slug');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.full-with-slug');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics.with-slug');

    // Gestão de Clientes - MANTER NOMES ORIGINAIS
    Route::resource('clients', ClientController::class);
    Route::post('/clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])
        ->name('clients.toggle-status.with-slug');

    // Gestão de Subscrições - MANTER NOMES ORIGINAIS
    Route::middleware([CheckSubscriptionMiddleware::class])->group(function () {
        Route::resource('subscriptions', SubscriptionController::class);

        Route::prefix('subscriptions/{subscription}')->group(function () {
            Route::post('/suspend', [SubscriptionController::class, 'suspend'])
                ->name('subscriptions.suspend.with-slug');
            Route::post('/activate', [SubscriptionController::class, 'activate'])
                ->name('subscriptions.activate.with-slug');
            Route::post('/cancel', [SubscriptionController::class, 'cancel'])
                ->name('subscriptions.cancel.with-slug');
            Route::post('/renew', [SubscriptionController::class, 'renew'])
                ->name('subscriptions.renew.with-slug');
            Route::post('/regenerate-key', [SubscriptionController::class, 'regenerateApiKey'])
                ->name('subscriptions.regenerate-key.with-slug');
            Route::post('/toggle-manual', [SubscriptionController::class, 'toggleManualStatus'])
                ->name('subscriptions.toggle-manual.with-slug');
        });
    });

    // Gestão de Planos - MANTER NOMES ORIGINAIS
    Route::resource('plans', SubscriptionPlanController::class);
    Route::post('/plans/{plan}/toggle', [SubscriptionPlanController::class, 'toggle'])
        ->name('plans.toggle.with-slug');
    Route::patch('plans/{plan}/toggle-status', [SubscriptionPlanController::class, 'toggleStatus'])
        ->name('plans.toggle-status.with-slug');
    Route::post('plans/{plan}/duplicate', [SubscriptionPlanController::class, 'duplicate'])
        ->name('plans.duplicate.with-slug');

    // Logs da API - MANTER NOMES ORIGINAIS
    Route::middleware([CheckFeatureMiddleware::class . ':api_access'])->group(function () {
        Route::prefix('api-logs')->name('api-logs.')->group(function () {
            Route::get('/', [ApiLogController::class, 'index'])->name('index');
            Route::get('/{apiLog}', [ApiLogController::class, 'show'])->name('show');
            Route::delete('/{apiLog}', [ApiLogController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-delete', [ApiLogController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('/cleanup', [ApiLogController::class, 'cleanup'])->name('cleanup');
            Route::get('/export/data', [ApiLogController::class, 'export'])->name('export');
            Route::get('/statistics/data', [ApiLogController::class, 'statistics'])->name('statistics');
        });
    });

    // Todas as outras rotas mantendo nomes originais...
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

    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/store', [InvoiceController::class, 'store'])->name('store');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
        Route::post('/{invoice}/mark-as-paid', [InvoiceController::class, 'markAsPaid'])->name('mark-as-paid');
        Route::patch('/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('update-status');
        Route::get('/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('download-pdf');
        Route::post('/{invoice}/send-email', [InvoiceController::class, 'sendByEmail'])->name('send-email');
        Route::post('/{invoice}/duplicate', [InvoiceController::class, 'duplicate'])->name('duplicate');
        Route::post('/bulk-update-status', [InvoiceController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
        Route::get('/bulk-download-pdf', [InvoiceController::class, 'bulkDownloadPdf'])->name('bulk-download-pdf');
        Route::get('/export/data', [InvoiceController::class, 'export'])->name('export');
        Route::get('/by-type/{type}', [InvoiceController::class, 'indexByType'])->name('by-type');
    });

    // Continuar com todas as outras rotas mantendo os nomes originais...
    Route::prefix('produtos')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('servicos')->name('services.')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('index');
        Route::get('/create', [ServiceController::class, 'create'])->name('create');
        Route::post('/', [ServiceController::class, 'store'])->name('store');
        Route::get('/{service}', [ServiceController::class, 'show'])->name('show');
        Route::get('/{service}/edit', [ServiceController::class, 'edit'])->name('edit');
        Route::put('/{service}', [ServiceController::class, 'update'])->name('update');
        Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('destroy');
    });

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

    // API Routes
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/clients/{client}/quotes', [InvoiceController::class, 'getClientQuotes'])->name('clients.quotes');
        Route::get('/quotes/{quote}/items', [InvoiceController::class, 'getQuoteItems'])->name('quotes.items');
        Route::get('/invoices/stats', [InvoiceController::class, 'getStats'])->name('invoices.stats');
        Route::get('/products/active', [ProductController::class, 'activeProducts']);
        Route::get('/services/active', [ServiceController::class, 'activeServices']);
    });
});

/*
|--------------------------------------------------------------------------
| ROTAS LEGADAS (SEM SLUG) - MANTER TODAS EXATAMENTE COMO ESTAVAM
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', TenantMiddleware::class])->group(function () {

    // Dashboard da Empresa
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');

    // Gestão de Clientes
    Route::resource('clients', ClientController::class);
    Route::post('/clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])
        ->name('clients.toggle-status');

    // Gestão de Subscrições
    Route::middleware([CheckSubscriptionMiddleware::class])->group(function () {
        Route::resource('subscriptions', SubscriptionController::class);

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
    });

    // Gestão de Planos
    Route::resource('plans', SubscriptionPlanController::class);
    Route::post('/plans/{plan}/toggle', [SubscriptionPlanController::class, 'toggle'])
        ->name('plans.toggle');
    Route::patch('plans/{plan}/toggle-status', [SubscriptionPlanController::class, 'toggleStatus'])
        ->name('plans.toggle-status');
    Route::post('plans/{plan}/duplicate', [SubscriptionPlanController::class, 'duplicate'])
        ->name('plans.duplicate');

    // TODAS AS OUTRAS ROTAS EXATAMENTE COMO ESTAVAM NO SEU ARQUIVO ORIGINAL
    // (copiar todas as rotas do seu arquivo original aqui)

    // Logs da API
    Route::middleware([CheckFeatureMiddleware::class . ':api_access'])->group(function () {
        Route::prefix('api-logs')->name('api-logs.')->group(function () {
            Route::get('/', [ApiLogController::class, 'index'])->name('index');
            Route::get('/{apiLog}', [ApiLogController::class, 'show'])->name('show');
            Route::delete('/{apiLog}', [ApiLogController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-delete', [ApiLogController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('/cleanup', [ApiLogController::class, 'cleanup'])->name('cleanup');
            Route::get('/export/data', [ApiLogController::class, 'export'])->name('export');
            Route::get('/statistics/data', [ApiLogController::class, 'statistics'])->name('statistics');
        });
    });

    // Email Logs
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

        Route::middleware([CheckFeatureMiddleware::class . ':advanced_reports'])->group(function () {
            Route::get('/advanced', [DashboardController::class, 'advancedReports'])->name('advanced');
            Route::get('/analytics', [DashboardController::class, 'analyticsReports'])->name('analytics');
        });
    });

    // Dashboard de Faturação
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/dashboard', [BillingController::class, 'dashboard'])->name('dashboard');
        Route::get('/reports', [BillingController::class, 'reports'])->name('reports');
        Route::get('/export', [BillingController::class, 'export'])->name('export');

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [BillingController::class, 'index'])->name('index');
            Route::post('/update', [BillingController::class, 'updateSettings'])->name('update');
        });
    });

    // Cotações
    Route::middleware([CheckSubscriptionMiddleware::class])->group(function () {
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
    });

    // Faturas
    Route::middleware([CheckSubscriptionMiddleware::class])->group(function () {
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('index');
            Route::get('/create', [InvoiceController::class, 'create'])->name('create');
            Route::post('/store', [InvoiceController::class, 'store'])->name('store');
            Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
            Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
            Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
            Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');

            Route::post('/{invoice}/mark-as-paid', [InvoiceController::class, 'markAsPaid'])->name('mark-as-paid');
            Route::patch('/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('update-status');
            Route::get('/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('download-pdf');
            Route::post('/{invoice}/send-email', [InvoiceController::class, 'sendByEmail'])->name('send-email');
            Route::post('/{invoice}/duplicate', [InvoiceController::class, 'duplicate'])->name('duplicate');

            Route::post('/bulk-update-status', [InvoiceController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
            Route::get('/bulk-download-pdf', [InvoiceController::class, 'bulkDownloadPdf'])->name('bulk-download-pdf');

            Route::get('/export/data', [InvoiceController::class, 'export'])->name('export');
            Route::get('/by-type/{type}', [InvoiceController::class, 'indexByType'])->name('by-type');
        });
    });

    // API Routes para AJAX
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/clients/{client}/quotes', [InvoiceController::class, 'getClientQuotes'])->name('clients.quotes');
        Route::get('/quotes/{quote}/items', [InvoiceController::class, 'getQuoteItems'])->name('quotes.items');
        Route::get('/invoices/stats', [InvoiceController::class, 'getStats'])->name('invoices.stats');
        Route::get('/products/active', [ProductController::class, 'activeProducts']);
        Route::get('/services/active', [ServiceController::class, 'activeServices']);

        Route::middleware([CheckFeatureMiddleware::class . ':api_access'])->group(function () {
            Route::prefix('external')->name('external.')->group(function () {
                Route::get('/invoices', [InvoiceController::class, 'apiIndex'])->name('invoices.index');
                Route::post('/invoices', [InvoiceController::class, 'apiStore'])->name('invoices.store');
                Route::get('/clients', [ClientController::class, 'apiIndex'])->name('clients.index');
            });
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
    Route::middleware([CheckSubscriptionMiddleware::class])->group(function () {
        Route::prefix('produtos')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{product}', [ProductController::class, 'show'])->name('show');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');

            Route::get('/{product}/duplicate', [ProductController::class, 'duplicate'])->name('duplicate');
            Route::post('/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/export/data', [ProductController::class, 'export'])->name('export');
            Route::post('/import/data', [ProductController::class, 'import'])->name('import');
            Route::get('/low-stock/list', [ProductController::class, 'lowStock'])->name('low-stock');
        });
    });

    // Serviços
    Route::middleware([CheckSubscriptionMiddleware::class])->group(function () {
        Route::prefix('servicos')->name('services.')->group(function () {
            Route::get('/', [ServiceController::class, 'index'])->name('index');
            Route::get('/create', [ServiceController::class, 'create'])->name('create');
            Route::post('/', [ServiceController::class, 'store'])->name('store');
            Route::get('/{service}', [ServiceController::class, 'show'])->name('show');
            Route::get('/{service}/edit', [ServiceController::class, 'edit'])->name('edit');
            Route::put('/{service}', [ServiceController::class, 'update'])->name('update');
            Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('destroy');

            Route::get('/{service}/duplicate', [ServiceController::class, 'duplicate'])->name('duplicate');
            Route::post('/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/export/data', [ServiceController::class, 'export'])->name('export');
            Route::post('/import/data', [ServiceController::class, 'import'])->name('import');
        });
    });

    // Vendas à Dinheiro
    Route::prefix('cash-sales')->name('cash-sales.')->group(function () {
        Route::get('/create', [CashSaleController::class, 'create'])->name('create');
        Route::post('/', [CashSaleController::class, 'store'])->name('store');
        Route::get('/quick-sale', [CashSaleController::class, 'quickSale'])->name('quick-sale');
    });

    // Notas de Crédito
    Route::middleware([CheckSubscriptionMiddleware::class])->group(function () {
        Route::resource('credit-notes', CreditNoteController::class)->names([
            'index' => 'credit-notes.index',
            'create' => 'credit-notes.create',
            'store' => 'credit-notes.store',
            'show' => 'credit-notes.show',
            'edit' => 'credit-notes.edit',
            'update' => 'credit-notes.update',
            'destroy' => 'credit-notes.destroy'
        ]);

        Route::group(['prefix' => 'credit-notes', 'as' => 'credit-notes.'], function () {
            Route::get('/{creditNote}/pdf', [CreditNoteController::class, 'downloadPdf'])->name('download-pdf');
            Route::post('/{creditNote}/send-email', [CreditNoteController::class, 'sendByEmail'])->name('send-email');
            Route::post('/{creditNote}/duplicate', [CreditNoteController::class, 'duplicate'])->name('duplicate');
        });
    });

    // Notas de Débito
    Route::middleware([CheckSubscriptionMiddleware::class])->group(function () {
        Route::prefix('debit-notes')->name('debit-notes.')->group(function () {
            Route::get('/', [DebitNoteController::class, 'index'])->name('index');
            Route::get('/create', [DebitNoteController::class, 'create'])->name('create');
            Route::post('/', [DebitNoteController::class, 'store'])->name('store');
            Route::get('/{debitNote}', [DebitNoteController::class, 'show'])->name('show');
            Route::get('/{debitNote}/edit', [DebitNoteController::class, 'edit'])->name('edit');
            Route::put('/{debitNote}', [DebitNoteController::class, 'update'])->name('update');
            Route::delete('/{debitNote}', [DebitNoteController::class, 'destroy'])->name('destroy');

            Route::get('/{debitNote}/pdf', [DebitNoteController::class, 'downloadPdf'])->name('download-pdf');
            Route::post('/{debitNote}/send-email', [DebitNoteController::class, 'sendByEmail'])->name('send-email');
            Route::post('/{debitNote}/duplicate', [DebitNoteController::class, 'duplicate'])->name('duplicate');
            Route::post('/{debitNote}/mark-as-paid', [DebitNoteController::class, 'markAsPaid'])->name('mark-as-paid');
            Route::patch('/{debitNote}/status', [DebitNoteController::class, 'updateStatus'])->name('update-status');

            Route::get('/api/debit-type-details', [DebitNoteController::class, 'getDebitTypeDetails'])->name('api.debit-type-details');
            Route::post('/api/calculate-preview', [DebitNoteController::class, 'calculatePreview'])->name('api.calculate-preview');

            Route::get('/export/data', [DebitNoteController::class, 'export'])->name('export');
        });
    });

    // Profile routes
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| ROTAS DE AUTENTICAÇÃO (sem mudanças)
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| UTILITÁRIOS (sem mudanças)
|--------------------------------------------------------------------------
*/

Route::get('/limpar-cache', function () {
    Artisan::call('optimize:clear');
    return 'Cache limpo com sucesso!';
});

// 2. Helper atualizado para funcionar com ambos os sistemas
// app/Helpers/CompanyHelper.php

