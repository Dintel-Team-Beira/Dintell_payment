<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SettingsController;



Route::prefix('admin')->name('admin.')->group(function () {

    // Configurações - Dashboard principal
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

    // Configurações do Sistema
    Route::get('/settings/system', [SettingsController::class, 'system'])->name('settings.system');
    Route::put('/settings/system', [SettingsController::class, 'updateSystem'])->name('settings.system.update');
    Route::post('/settings/system/clear-cache', [SettingsController::class, 'clearCache'])->name('settings.system.clear-cache');
    Route::post('/settings/system/optimize', [SettingsController::class, 'optimizeSystem'])->name('settings.system.optimize');
    Route::get('/settings/system/info', [SettingsController::class, 'systemInfo'])->name('settings.system.info');

    // Configurações de Faturação
    Route::get('/settings/billing', [SettingsController::class, 'billing'])->name('settings.billing');
    Route::put('/settings/billing', [SettingsController::class, 'updateBilling'])->name('settings.billing.update');

    // Configurações de Email
    Route::get('/settings/email', [SettingsController::class, 'email'])->name('settings.email');
    Route::put('/settings/email', [SettingsController::class, 'updateEmail'])->name('settings.email.update');
    Route::post('/settings/email/test', [SettingsController::class, 'testEmail'])->name('settings.email.test');

    // Configurações de Backup
    Route::get('/settings/backups', [SettingsController::class, 'backups'])->name('settings.backups');
    Route::put('/settings/backups', [SettingsController::class, 'updateBackups'])->name('settings.backups.update');
    Route::post('/settings/backups/create', [SettingsController::class, 'createBackup'])->name('settings.backups.create');
    Route::get('/settings/backups/list', [SettingsController::class, 'listBackups'])->name('settings.backups.list');
    Route::get('/settings/backups/{id}/download', [SettingsController::class, 'downloadBackup'])->name('settings.backups.download');
    Route::post('/settings/backups/{id}/restore', [SettingsController::class, 'restoreBackup'])->name('settings.backups.restore');
    Route::delete('/settings/backups/{id}', [SettingsController::class, 'deleteBackup'])->name('settings.backups.delete');

    // Configurações de Segurança
    Route::get('/settings/security', [SettingsController::class, 'security'])->name('settings.security');
    Route::put('/settings/security', [SettingsController::class, 'updateSecurity'])->name('settings.security.update');
    Route::post('/settings/security/report', [SettingsController::class, 'securityReport'])->name('settings.security.report');

    // Exportar/Importar Configurações
    Route::get('/settings/export', [SettingsController::class, 'exportSettings'])->name('settings.export');
    Route::post('/settings/import', [SettingsController::class, 'importSettings'])->name('settings.import');

    // Logs do Sistema
    // Route::get('/logs', [SettingsController::class, 'logs'])->name('logs.index');
    // Route::get('/logs/{file}', [SettingsController::class, 'viewLog'])->name('logs.show');
    // Route::delete('/logs/{file}', [SettingsController::class, 'deleteLog'])->name('logs.delete');
    // Route::post('/logs/clear-all', [SettingsController::class, 'clearAllLogs'])->name('logs.clear-all');

    // Dentro do grupo admin existente
   // ADICIONE ESTA SEÇÃO COMPLETA:
    Route::prefix('settings')->name('settings.')->group(function () {
        // Configurações gerais
        Route::get('/', [SettingsController::class, 'index'])->name('index');

        // Configurações de email
        Route::get('/email', [SettingsController::class, 'emailSettings'])->name('email');
        Route::prefix('email')->name('email.')->group(function () {
            Route::get('/', [SettingsController::class, 'emailSettings'])->name('index');
            Route::post('/update', [SettingsController::class, 'updateEmailSettings'])->name('update');
            Route::get('/preview', [SettingsController::class, 'emailPreview'])->name('preview');
            Route::post('/test', [SettingsController::class, 'sendTestEmail'])->name('test');
        });
    });
});
