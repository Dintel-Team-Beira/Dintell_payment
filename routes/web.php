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

// Middlewares diretos
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\TenantMiddleware;
use App\Http\Middleware\CheckSubscriptionMiddleware;
use App\Http\Middleware\CheckFeatureMiddleware;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| ROTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

// Página pública de suspensão (deve ficar ANTES das rotas autenticadas)
Route::get('/suspended/{domain}', [SuspensionPageController::class, 'show'])
    ->name('suspension.page');

// Rotas de renovação pública (para links de email)
Route::get('/renew/{subscription}', [SubscriptionController::class, 'showRenewal'])
    ->name('subscription.renew');
Route::post('/renew/{subscription}', [SubscriptionController::class, 'processRenewal'])
    ->name('subscription.renew.process');

/*
|--------------------------------------------------------------------------
| ROTAS ADMINISTRATIVAS (SaaS)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Rotas de login/logout para admins
    Route::middleware('guest')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::post('logout', [AdminAuthController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');

    // Rotas protegidas do admin (apenas super admins) - MIDDLEWARE DIRETO
    Route::middleware(['auth', AdminMiddleware::class])->group(function () {

        // Dashboard Admin
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Gestão de Empresas (Tenants)
        Route::prefix('companies')->name('companies.')->group(function () {
            Route::get('/', [AdminCompaniesController::class, 'index'])->name('index');
            Route::get('/create', [AdminCompaniesController::class, 'create'])->name('create');
            Route::post('/', [AdminCompaniesController::class, 'store'])->name('store');
            Route::get('/{company}', [AdminCompaniesController::class, 'show'])->name('show');
            Route::get('/{company}/edit', [AdminCompaniesController::class, 'edit'])->name('edit');
            Route::put('/{company}', [AdminCompaniesController::class, 'update'])->name('update');
            Route::delete('/{company}', [AdminCompaniesController::class, 'destroy'])->name('destroy');

            // Ações especiais
            Route::post('/{company}/suspend', [AdminCompaniesController::class, 'suspend'])->name('suspend');
            Route::post('/{company}/activate', [AdminCompaniesController::class, 'activate'])->name('activate');
            Route::post('/{company}/extend-trial', [AdminCompaniesController::class, 'extendTrial'])->name('extend-trial');
            Route::get('/{company}/impersonate', [AdminCompaniesController::class, 'impersonate'])->name('impersonate');
            Route::get('/{company}/analytics', [AdminCompaniesController::class, 'analytics'])->name('analytics');

            // Exportação
            Route::get('/export', [AdminCompaniesController::class, 'export'])->name('export');
        });

        // Parar impersonificação
        Route::get('/stop-impersonation', [AdminCompaniesController::class, 'stopImpersonation'])->name('stop-impersonation');

        // Gestão de Usuários Globais
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminUsersController::class, 'index'])->name('index');
            Route::get('/create', [AdminUsersController::class, 'create'])->name('create');
            Route::post('/', [AdminUsersController::class, 'store'])->name('store');
            Route::get('/{user}', [AdminUsersController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [AdminUsersController::class, 'edit'])->name('edit');
            Route::put('/{user}', [AdminUsersController::class, 'update'])->name('update');
            Route::delete('/{user}', [AdminUsersController::class, 'destroy'])->name('destroy');

            // Ações especiais
            Route::post('/{user}/reset-password', [AdminUsersController::class, 'resetPassword'])->name('reset-password');
            Route::post('/{user}/toggle-status', [AdminUsersController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Faturas Globais (todas as empresas)
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [AdminInvoicesController::class, 'index'])->name('index');
            Route::get('/{invoice}', [AdminInvoicesController::class, 'show'])->name('show');
            Route::get('/export', [AdminInvoicesController::class, 'export'])->name('export');
        });

        // Relatórios Administrativos
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/revenue', [AdminReportsController::class, 'revenue'])->name('revenue');
            Route::get('/clients', [AdminReportsController::class, 'clients'])->name('clients');
            Route::get('/usage', [AdminReportsController::class, 'usage'])->name('usage');
            Route::get('/export/{type}', [AdminReportsController::class, 'export'])->name('export');

            // APIs para gráficos
            Route::get('/api/revenue', [AdminDashboardController::class, 'revenueReport'])->name('api.revenue');
            Route::get('/api/clients', [AdminDashboardController::class, 'clientsReport'])->name('api.clients');
            Route::get('/api/usage', [AdminDashboardController::class, 'usageReport'])->name('api.usage');
        });

        // Configurações do Sistema
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/system', [AdminSettingsController::class, 'system'])->name('system');
            Route::post('/system', [AdminSettingsController::class, 'updateSystem'])->name('system.update');

            Route::get('/billing', [AdminSettingsController::class, 'billing'])->name('billing');
            Route::post('/billing', [AdminSettingsController::class, 'updateBilling'])->name('billing.update');

            Route::get('/email', [AdminSettingsController::class, 'email'])->name('email');
            Route::post('/email', [AdminSettingsController::class, 'updateEmail'])->name('email.update');

            Route::get('/backups', [AdminSettingsController::class, 'backups'])->name('backups');
            Route::post('/backups/create', [AdminSettingsController::class, 'createBackup'])->name('backups.create');
        });

        // Monitoramento
        Route::prefix('monitoring')->name('monitoring.')->group(function () {
            Route::get('/performance', [AdminSettingsController::class, 'performance'])->name('performance');
            Route::get('/health', [AdminSettingsController::class, 'health'])->name('health');
        });

        // Logs
        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/', [AdminSettingsController::class, 'logs'])->name('index');
            Route::get('/download', [AdminSettingsController::class, 'downloadLogs'])->name('download');
        });

        // Suporte
        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/tickets', [AdminSettingsController::class, 'tickets'])->name('tickets');
        });

        // Documentação
        Route::prefix('help')->name('help.')->group(function () {
            Route::get('/documentation', [AdminSettingsController::class, 'documentation'])->name('documentation');
        });
    });
});

/*
|--------------------------------------------------------------------------
| ROTA HOME / REDIRECIONAMENTO PRINCIPAL
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        // Se é super admin, redireciona para admin
        if ($user->is_super_admin) {
            return redirect()->route('admin.dashboard');
        }

        // Se é usuário de empresa, redireciona para dashboard da empresa
        return redirect()->route('dashboard');
    }

    // Se não está logado, vai para login
    return redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| ROTAS DO SISTEMA PRINCIPAL (EMPRESAS/TENANTS)
|--------------------------------------------------------------------------
*/

// Rotas autenticadas e verificadas (para empresas) - MIDDLEWARES DIRETOS
Route::middleware(['auth', 'verified', TenantMiddleware::class])->group(function () {

    // Dashboard da Empresa
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');

    // Gestão de Clientes
    Route::resource('clients', ClientController::class);
    Route::post('/clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])
        ->name('clients.toggle-status');

    // Gestão de Subscrições (com verificação de limites)
    Route::middleware([CheckSubscriptionMiddleware::class])->group(function () {
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
    });

    // Gestão de Planos
    Route::resource('plans', SubscriptionPlanController::class);
    Route::post('/plans/{plan}/toggle', [SubscriptionPlanController::class, 'toggle'])
        ->name('plans.toggle');
    Route::patch('plans/{plan}/toggle-status', [SubscriptionPlanController::class, 'toggleStatus'])
        ->name('plans.toggle-status');
    Route::post('plans/{plan}/duplicate', [SubscriptionPlanController::class, 'duplicate'])
        ->name('plans.duplicate');

    // Logs da API (apenas para planos que têm API)
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

    // Logs de Email
    Route::prefix('email-logs')->name('email-logs.')->group(function () {
        Route::get('/', [EmailController::class, 'logs'])->name('index');
        Route::get('/{emailLog}', [EmailController::class, 'show'])->name('show');
        Route::post('/{emailLog}/resend', [EmailController::class, 'resend'])->name('resend');
        Route::post('/bulk-resend', [EmailController::class, 'bulkResend'])->name('bulk-resend');
        Route::post('/cleanup', [EmailController::class, 'cleanup'])->name('cleanup');
        Route::get('/export/data', [EmailController::class, 'export'])->name('export');
        Route::post('/test-email', [EmailController::class, 'test'])->name('test');
    });

    // Relatórios (da empresa) - Relatórios avançados apenas para Premium+
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/revenue', [DashboardController::class, 'revenueReport'])->name('revenue');
        Route::get('/clients', [DashboardController::class, 'clientsReport'])->name('clients');
        Route::get('/usage', [DashboardController::class, 'usageReport'])->name('usage');
        Route::get('/export/{type}', [DashboardController::class, 'exportReport'])->name('export');

        // Relatórios avançados apenas para Premium+
        Route::middleware([CheckFeatureMiddleware::class . ':advanced_reports'])->group(function () {
            Route::get('/advanced', [DashboardController::class, 'advancedReports'])->name('advanced');
            Route::get('/analytics', [DashboardController::class, 'analyticsReports'])->name('analytics');
        });
    });
});

// Rotas apenas autenticadas (sem verificação de email) - MIDDLEWARES DIRETOS
Route::middleware(['auth', TenantMiddleware::class])->group(function () {

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

    // Cotações (com verificação de limites)
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

    // Faturas (com verificação de limites)
    Route::middleware([CheckSubscriptionMiddleware::class])->group(function () {
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
    });

    // API Routes para AJAX (verificar se tem acesso à API)
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/clients/{client}/quotes', [InvoiceController::class, 'getClientQuotes'])->name('clients.quotes');
        Route::get('/quotes/{quote}/items', [InvoiceController::class, 'getQuoteItems'])->name('quotes.items');
        Route::get('/invoices/stats', [InvoiceController::class, 'getStats'])->name('invoices.stats');
        Route::get('/products/active', [ProductController::class, 'activeProducts']);
        Route::get('/services/active', [ServiceController::class, 'activeServices']);

        // API externa (apenas para planos com acesso)
        Route::middleware([CheckFeatureMiddleware::class . ':api_access'])->group(function () {
            Route::prefix('external')->name('external.')->group(function () {
                Route::get('/invoices', [InvoiceController::class, 'apiIndex'])->name('invoices.index');
                Route::post('/invoices', [InvoiceController::class, 'apiStore'])->name('invoices.store');
                Route::get('/clients', [ClientController::class, 'apiIndex'])->name('clients.index');
            });
        });
    });

    // Configurações da Empresa
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

    // Produtos (com verificação de limites)
    Route::middleware([CheckSubscriptionMiddleware::class])->group(function () {
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
    });

    // Serviços (com verificação de limites)
    Route::middleware([CheckSubscriptionMiddleware::class])->group(function () {
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
    });

    // Vendas à Dinheiro
    Route::prefix('cash-sales')->name('cash-sales.')->group(function () {
        Route::get('/create', [CashSaleController::class, 'create'])->name('create');
        Route::post('/', [CashSaleController::class, 'store'])->name('store');
        Route::get('/quick-sale', [CashSaleController::class, 'quickSale'])->name('quick-sale');
    });

    // Notas de Crédito (com verificação de limites)
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

        // Ações específicas para Notas de Crédito
        Route::group(['prefix' => 'credit-notes', 'as' => 'credit-notes.'], function () {
            Route::get('/{creditNote}/pdf', [CreditNoteController::class, 'downloadPdf'])->name('download-pdf');
            Route::post('/{creditNote}/send-email', [CreditNoteController::class, 'sendByEmail'])->name('send-email');
            Route::post('/{creditNote}/duplicate', [CreditNoteController::class, 'duplicate'])->name('duplicate');
        });
    });

    // Notas de Débito (com verificação de limites)
    Route::middleware([CheckSubscriptionMiddleware::class])->group(function () {
        Route::prefix('debit-notes')->name('debit-notes.')->group(function () {
            Route::get('/', [DebitNoteController::class, 'index'])->name('index');
            Route::get('/create', [DebitNoteController::class, 'create'])->name('create');
            Route::post('/', [DebitNoteController::class, 'store'])->name('store');
            Route::get('/{debitNote}', [DebitNoteController::class, 'show'])->name('show');
            Route::get('/{debitNote}/edit', [DebitNoteController::class, 'edit'])->name('edit');
            Route::put('/{debitNote}', [DebitNoteController::class, 'update'])->name('update');
            Route::delete('/{debitNote}', [DebitNoteController::class, 'destroy'])->name('destroy');

            // Ações específicas
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
| ROTAS DE AUTENTICAÇÃO (Breeze/Fortify)
|--------------------------------------------------------------------------
*/

// Incluir rotas de autenticação do sistema
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| UTILITÁRIOS
|--------------------------------------------------------------------------
*/

// Limpar cache
Route::get('/limpar-cache', function () {
    Artisan::call('optimize:clear');
    return 'Cache limpo com sucesso!';
});
