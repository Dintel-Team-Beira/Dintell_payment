<?php

use App\Http\Controllers\Admin\AdminHelpController;
use App\Http\Controllers\Admin\AdminMonitoringController;
use App\Http\Controllers\Admin\AdminSupportController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CompaniesController as AdminCompaniesController;
use App\Http\Controllers\admin\CompanySubscription;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InvoicesController as AdminInvoicesController;
use App\Http\Controllers\Admin\LogsController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\UsersController as AdminUsersController;
use App\Http\Controllers\ApiLogController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebitNoteController;
use App\Http\Controllers\EmailController;
// Controllers Admin (SaaS)
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ReceiptController;
// Middlewares diretos
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SuspensionPageController;
use App\Http\Controllers\TemplatePreviewController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\TenantMiddleware;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

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

    // Rotas para AdminActivity
    Route::prefix('activities')->name('activities.')->group(function () {
        Route::get('/', [AdminActivitiesController::class, 'index'])->name('index');
        Route::get('/dashboard', [AdminActivitiesController::class, 'dashboard'])->name('dashboard');
        Route::get('/export', [AdminActivitiesController::class, 'export'])->name('export');
        Route::get('/{activity}', [AdminActivitiesController::class, 'show'])->name('show');
        Route::delete('/clear', [AdminActivitiesController::class, 'clear'])->name('clear');
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
            Route::post('/{company}/toggle-status', [AdminCompaniesController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/bulk-action', [AdminCompaniesController::class, 'bulkAction'])->name('bulk-action');
            Route::get('/export/data', [AdminCompaniesController::class, 'export'])->name('export');

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

        // Demais rotas administrativas...
        // (mantidas as mesmas do seu código original)
        // Gestão de Usuários
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminUsersController::class, 'index'])->name('index');
            Route::get('/create', [AdminUsersController::class, 'create'])->name('create');
            Route::post('/', [AdminUsersController::class, 'store'])->name('store');
            Route::get('/{user}', [AdminUsersController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [AdminUsersController::class, 'edit'])->name('edit');
            Route::put('/{user}', [AdminUsersController::class, 'update'])->name('update');
            Route::delete('/{user}', [AdminUsersController::class, 'destroy'])->name('destroy');

            // Ações especiais
            Route::post('/{user}/toggle-status', [AdminUsersController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/bulk-action', [AdminUsersController::class, 'bulkAction'])->name('bulk-action');
            Route::get('/export/data', [AdminUsersController::class, 'export'])->name('export');
        });

        // Gestão de Empresas
        Route::prefix('companies')->name('companies.')->group(function () {
            Route::get('/', [AdminCompaniesController::class, 'index'])->name('index');
            Route::get('/create', [AdminCompaniesController::class, 'create'])->name('create');
            Route::post('/', [AdminCompaniesController::class, 'store'])->name('store');
            Route::get('/{company}', [AdminCompaniesController::class, 'show'])->name('show');
            Route::get('/{company}/edit', [AdminCompaniesController::class, 'edit'])->name('edit');
            Route::put('/{company}', [AdminCompaniesController::class, 'update'])->name('update');
            Route::delete('/{company}', [AdminCompaniesController::class, 'destroy'])->name('destroy');
            // Rota para iniciar a impersonificação
            Route::post('{company}/impersonate', [AdminCompaniesController::class, 'impersonate'])
                ->name('impersonate');

            // Rota para parar a impersonificação
            Route::get('/stop-impersonation', [AdminCompaniesController::class, 'stopImpersonation'])
                ->name('stop-impersonation');

            // Ações especiais
            Route::post('/{company}/toggle-status', [AdminCompaniesController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/bulk-action', [AdminCompaniesController::class, 'bulkAction'])->name('bulk-action');
            Route::get('/export/data', [AdminCompaniesController::class, 'export'])->name('export');
        });

        // Faturas do Sistema (Visão Global)
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [AdminInvoicesController::class, 'index'])->name('index');
            Route::get('/{invoice}', [AdminInvoicesController::class, 'show'])->name('show');
            Route::get('/analytics/dashboard', [AdminInvoicesController::class, 'analytics'])->name('analytics');

            // Ações em massa
            Route::post('/bulk-action', [AdminInvoicesController::class, 'bulkAction'])->name('bulk-action');
            Route::post('/{invoice}/mark-as-paid', [AdminInvoicesController::class, 'markAsPaid'])->name('mark-as-paid');
            Route::patch('/{invoice}/status', [AdminInvoicesController::class, 'updateStatus'])->name('update-status');

            // Exportação
            Route::get('/export/data', [AdminInvoicesController::class, 'export'])->name('export');

            // Rotas de PDF
            Route::get('/{invoice}/download-pdf', [InvoiceController::class, 'downloadPdf'])->name('pdf');
            Route::get('/{invoice}/view-pdf', [InvoiceController::class, 'viewPdf'])->name('view-pdf');
            Route::get('/{invoice}/print', [InvoiceController::class, 'print'])->name('print');
        });

        // Relatórios Administrativos
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/revenue', [ReportsController::class, 'revenue'])->name('revenue');
            Route::get('/clients', [ReportsController::class, 'clients'])->name('clients');
            Route::get('/usage', [ReportsController::class, 'usage'])->name('usage');
            Route::get('/export/{type}', [ReportsController::class, 'export'])->name('export');
        });

        // Configurações do Sistema
        Route::prefix('settings')->name('settings.')->group(function () {

            // Configurações - Dashboard principal
            Route::get('/settings', [SettingsController::class, 'index'])->name('index');
            // Configurações do Sistema
            // Configurações de Segurança
            Route::get('/security', [SettingsController::class, 'security'])->name('security');
            Route::put('/security', [SettingsController::class, 'updateSecurity'])->name('security.update');
            Route::post('/security/report', [SettingsController::class, 'securityReport'])->name('security.report');
            Route::get('/system', [AdminSettingsController::class, 'system'])->name('system');
            Route::post('/system', [AdminSettingsController::class, 'updateSystem'])->name('system.update');

            // Configurações de Faturação
            Route::get('/billing', [AdminSettingsController::class, 'billing'])->name('billing');
            Route::post('/billing', [AdminSettingsController::class, 'updateBilling'])->name('billing.update');

            // Configurações de Email
            Route::get('/email', [AdminSettingsController::class, 'email'])->name('email');
            Route::post('/email', [AdminSettingsController::class, 'updateEmail'])->name('email.update');
            Route::post('/email/test', [AdminSettingsController::class, 'testEmail'])->name('email.test');

            // Configurações de Backup
            Route::get('/backups', [AdminSettingsController::class, 'backups'])->name('backups');
            Route::post('/backups', [AdminSettingsController::class, 'updateBackups'])->name('backups.update');
            Route::post('/backups/create', [AdminSettingsController::class, 'createBackup'])->name('backups.create');
            Route::get('/backups/{filename}/download', [AdminSettingsController::class, 'downloadBackup'])->name('backups.download');
            Route::delete('/backups/{filename}', [AdminSettingsController::class, 'deleteBackup'])->name('backups.delete');
            Route::post('/backups/{filename}/restore', [AdminSettingsController::class, 'restoreBackup'])->name('backups.restore');

            // Ações do Sistema
            Route::post('/cache/clear', [AdminSettingsController::class, 'clearCache'])->name('cache.clear');
            Route::post('/system/optimize', [AdminSettingsController::class, 'optimizeSystem'])->name('system.optimize');
            Route::post('/maintenance/toggle', [AdminSettingsController::class, 'maintenanceMode'])->name('maintenance.toggle');

            // Informações do Sistema
            Route::get('/database/info', [AdminSettingsController::class, 'getDatabaseInfo'])->name('database.info');
            Route::get('/system/info', [AdminSettingsController::class, 'getSystemInfo'])->name('system.info');

            // Importar/Exportar Configurações
            Route::get('/export', [AdminSettingsController::class, 'exportSettings'])->name('export');
            Route::post('/import', [AdminSettingsController::class, 'importSettings'])->name('import');
        });

        // Logs do Sistema
        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/', [LogsController::class, 'index'])->name('index');
            Route::get('/{log}', [LogsController::class, 'show'])->name('show');
            Route::delete('/{log}', [LogsController::class, 'destroy'])->name('destroy');
            Route::post('/clear', [LogsController::class, 'clear'])->name('clear');
            Route::get('/download/{log}', [LogsController::class, 'download'])->name('download');
        });

        // Monitoramento
        Route::prefix('monitoring')->name('monitoring.')->group(function () {
            Route::get('/performance', [AdminMonitoringController::class, 'performance'])->name('performance');
            Route::get('/health', [AdminMonitoringController::class, 'health'])->name('health');
            Route::get('/metrics', [AdminMonitoringController::class, 'metrics'])->name('metrics');
        });

        // Suporte
        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/tickets', [AdminSupportController::class, 'tickets'])->name('tickets');
            Route::get('/tickets/{ticket}', [AdminSupportController::class, 'showTicket'])->name('tickets.show');
            Route::post('/tickets/{ticket}/reply', [AdminSupportController::class, 'replyTicket'])->name('tickets.reply');
            Route::patch('/tickets/{ticket}/status', [AdminSupportController::class, 'updateTicketStatus'])->name('tickets.status');
        });

        // Documentação e Ajuda
        Route::prefix('help')->name('help.')->group(function () {
            Route::get('/documentation', [AdminHelpController::class, 'documentation'])->name('documentation');
            Route::get('/api-docs', [AdminHelpController::class, 'apiDocs'])->name('api-docs');
            Route::get('/changelog', [AdminHelpController::class, 'changelog'])->name('changelog');
        });

        // Resouce routes subscription
        Route::resource('subscriptions', CompanySubscription::class)->except(['edit', 'update']);
        // Rotas de ações específicas
        Route::prefix('subscriptions')->name('subscriptions.')->group(function () {

            // Cancelamento
            Route::post('{subscription}/cancel', [CompanySubscription::class, 'cancel'])
                ->name('cancel');

            // Suspensão
            Route::post('{subscription}/suspend', [CompanySubscription::class, 'suspend'])
                ->name('suspend');

            // Reativação
            Route::post('{subscription}/reactivate', [CompanySubscription::class, 'reactivate'])
                ->name('reactivate');

            // Renovação manual
            Route::post('{subscription}/renew', [CompanySubscription::class, 'renew'])
                ->name('renew');

            // Toggle auto-renew (AJAX)
            Route::post('{subscription}/toggle-auto-renew', [CompanySubscription::class, 'toggleAutoRenew'])
                ->name('toggle-auto-renew');

            // Processar expirações (chamado por cron/command)
            Route::post('process-expirations', [CompanySubscription::class, 'processExpirations'])
                ->name('process-expirations');
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

        // Se é usuário de empresa, redireciona para dashboard da empresa com slug
        if ($user->company_id) {
            $company = \App\Models\Company::find($user->company_id);
            if ($company && $company->slug) {
                return redirect('dintell/dashboard');
            }
        }

        return redirect()->route('login');
    }

    // Se não está logado, vai para login
    return redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| ROTAS COM DA EMPRESA
|-------------------
-------------------------------------------------------
*/

Route::middleware(['auth', 'subscription.check'])->prefix('dintell')->group(function () {
    // Dashboard da Empresa
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
        // Listar faturas
        Route::get('/', [InvoiceController::class, 'index'])->name('index');

        // Criar nova fatura
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');

        // Visualizar fatura específica - USANDO ID NUMÉRICO
        Route::get('/{invoice}', [InvoiceController::class, 'show'])
            ->name('show')
            ->where('invoice', '[0-9]+');

        // Editar fatura
        Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])
            ->name('edit')
            ->where('invoice', '[0-9]+');

        // Atualizar fatura
        Route::put('/{invoice}', [InvoiceController::class, 'update'])
            ->name('update')
            ->where('invoice', '[0-9]+');

        // Excluir fatura
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])
            ->name('destroy')
            ->where('invoice', '[0-9]+');

        // Ações específicas de faturas
        Route::post('/{invoice}/mark-as-paid', [InvoiceController::class, 'markAsPaid'])
            ->name('mark-as-paid')
            ->where('invoice', '[0-9]+');

        Route::patch('/{invoice}/status', [InvoiceController::class, 'updateStatus'])
            ->name('update-status')
            ->where('invoice', '[0-9]+');

        Route::get('/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])
            ->name('download-pdf')
            ->where('invoice', '[0-9]+');

        Route::post('/{invoice}/send-email', [InvoiceController::class, 'sendByEmail'])
            ->name('send-email')
            ->where('invoice', '[0-9]+');

        Route::post('/{invoice}/duplicate', [InvoiceController::class, 'duplicate'])
            ->name('duplicate');

        // Ações em massa
        Route::post('/bulk-update-status', [InvoiceController::class, 'bulkUpdateStatus'])
            ->name('bulk-update-status');

        Route::get('/bulk-download-pdf', [InvoiceController::class, 'bulkDownloadPdf'])
            ->name('bulk-download-pdf');

        // Exportação
        Route::get('/export/data', [InvoiceController::class, 'export'])
            ->name('export');

        // Relatórios por tipo de documento
        Route::get('/by-type/{type}', [InvoiceController::class, 'indexByType'])
            ->name('by-type');

        // Para recibos
        Route::post('/{invoice}/generate-receipt', [InvoiceController::class, 'generateReceipt'])->name('generate-receipt');
        Route::get('/{invoice}/receipts', [InvoiceController::class, 'receipts'])->name('receipts');
    });

    Route::prefix('receipts')->name('receipts.')->group(function () {
        Route::get('/', [ReceiptController::class, 'index'])->name('index');
        Route::get('/{receipt}', [ReceiptController::class, 'show'])->name('show');
        Route::post('/{receipt}/cancel', [ReceiptController::class, 'cancel'])->name('cancel');
        Route::get('/{receipt}/download-pdf', [ReceiptController::class, 'downloadPdf'])->name('download-pdf');
        Route::post('/{receipt}/duplicate', [ReceiptController::class, 'duplicate'])->name('duplicate');
        Route::get('/export', [ReceiptController::class, 'export'])->name('export');
        Route::get('/api/stats', [ReceiptController::class, 'stats'])->name('api.stats');
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

    // Notas de Crédito
    // Notas de Crédito
    Route::resource('credit-notes', CreditNoteController::class)->names([
        'index' => 'credit-notes.index',
        'create' => 'credit-notes.create',
        'store' => 'credit-notes.store',
        'show' => 'credit-notes.show',
        'edit' => 'credit-notes.edit',
        'update' => 'credit-notes.update',
        'destroy' => 'credit-notes.destroy',
    ]);

    Route::group(['prefix' => 'credit-notes', 'as' => 'credit-notes.'], function () {
        Route::get('/{creditNote}/pdf', [CreditNoteController::class, 'downloadPdf'])->name('download-pdf');
        Route::post('/{creditNote}/send-email', [CreditNoteController::class, 'sendByEmail'])->name('send-email');
        Route::post('/{creditNote}/duplicate', [CreditNoteController::class, 'duplicate'])->name('duplicate');
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

    // API Routes para AJAX
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/clients/{client}/quotes', [InvoiceController::class, 'getClientQuotes'])->name('clients.quotes');
        Route::get('/quotes/{quote}/items', [InvoiceController::class, 'getQuoteItems'])->name('quotes.items');
        Route::get('/invoices/stats', [InvoiceController::class, 'getStats'])->name('invoices.stats');
        Route::get('/products/active', [ProductController::class, 'activeProducts']);
        Route::get('/services/active', [ServiceController::class, 'activeServices']);
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
    // Logs da API (se disponível)
    Route::prefix('api-logs')->name('api-logs.')->group(function () {
        Route::get('/', [ApiLogController::class, 'index'])->name('index');
        Route::get('/{apiLog}', [ApiLogController::class, 'show'])->name('show');
        Route::delete('/{apiLog}', [ApiLogController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [ApiLogController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/cleanup', [ApiLogController::class, 'cleanup'])->name('cleanup');
        Route::get('/export/data', [ApiLogController::class, 'export'])->name('export');
        Route::get('/statistics/data', [ApiLogController::class, 'statistics'])->name('statistics');
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

    // Relatórios (da empresa)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/revenue', [DashboardController::class, 'revenueReport'])->name('revenue');
        Route::get('/clients', [DashboardController::class, 'clientsReport'])->name('clients');
        Route::get('/usage', [DashboardController::class, 'usageReport'])->name('usage');
        Route::get('/export/{type}', [DashboardController::class, 'exportReport'])->name('export');
    });

    // Template preview
    // Route::get('template-preview/{type}',[TemplatePreviewController::class,'show'])->name('template-preview');
    Route::get('template-preview/list/{type}', [TemplatePreviewController::class, 'list'])->name('template-preview.list');
    Route::get('template-preview/preview/{templateId}', [TemplatePreviewController::class, 'show'])->name('template-preview');
    Route::get('template-preview/download/{templateId}', [TemplatePreviewController::class, 'download'])->name('template-preview.download');
    Route::post('template-preview/select/{idTemplate}', [TemplatePreviewController::class, 'selectTemplate'])
        ->name('template-preview.select');
});

/*
|--------------------------------------------------------------------------
| ROTAS SEM SLUG (fallback para usuários sem empresa)
|--------------------------------------------------------------------------
*/

// Rotas apenas autenticadas (sem verificação de email e sem slug)
Route::middleware(['auth', TenantMiddleware::class])->group(function () {

    // Profile routes (sem slug, pois é pessoal)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/subscription/blocked', [SubscriptionController::class, 'blocked'])->name('subscription.blocked');

    // Página que avisa que precisa de empresa
    Route::get('/company/required', function () {
        return view('required');
    })->name('company.required');

    // Dashboard (redireciona automaticamente para versão com slug)
    // Route::get('/dashboard', function () {
    //     $user = auth()->user();
    //     if ($user && $user->company_id && !$user->is_super_admin) {
    //         $company = \App\Models\Company::find($user->company_id);
    //         if ($company && $company->slug) {
    //             return redirect("/{$company->slug}/dashboard");
    //         }
    //     }
    //     return redirect()->route('login');
    // });

});

/*
|--------------------------------------------------------------------------
| ROTAS DE AUTENTICAÇÃO (Breeze/Fortify)
|--------------------------------------------------------------------------
*/

// Incluir rotas de autenticação do sistema
require __DIR__.'/auth.php';
require __DIR__.'/support.php';
require __DIR__.'/admin_settings.php';
require __DIR__.'/admin_plans.php';
require __DIR__.'/Admin_monitoring.php';

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
