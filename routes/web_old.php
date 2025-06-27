<?php

use App\Http\Controllers\QuoteController;

// Rotas para Cotações
Route::prefix('quotes')->name('quotes.')->group(function () {
    // Rotas básicas do resource
    Route::get('/', [QuoteController::class, 'index'])->name('index');
    Route::get('/create', [QuoteController::class, 'create'])->name('create');
    Route::post('/', [QuoteController::class, 'store'])->name('store');
    Route::get('/{quote}', [QuoteController::class, 'show'])->name('show');
    Route::get('/{quote}/edit', [QuoteController::class, 'edit'])->name('edit');
    Route::put('/{quote}', [QuoteController::class, 'update'])->name('update');
    Route::delete('/{quote}', [QuoteController::class, 'destroy'])->name('destroy');

    // Rotas adicionais para funcionalidades específicas
    Route::post('/{quote}/duplicate', [QuoteController::class, 'duplicate'])->name('duplicate');
    Route::post('/{quote}/send-email', [QuoteController::class, 'sendEmail'])->name('send-email');
    Route::post('/{quote}/update-status', [QuoteController::class, 'updateStatus'])->name('update-status');
    Route::post('/{quote}/convert-to-invoice', [QuoteController::class, 'convertToInvoice'])->name('convert-to-invoice');
    Route::get('/{quote}/download-pdf', [QuoteController::class, 'downloadPDF'])->name('download-pdf');

    // Rotas para ações em massa
    Route::post('/bulk-update-status', [QuoteController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
    Route::get('/bulk-download-pdf', [QuoteController::class, 'bulkDownloadPDF'])->name('bulk-download-pdf');

    // Rotas para exportação
    Route::get('/export/data', [QuoteController::class, 'export'])->name('export');

    // Rota para dados do dashboard
    Route::get('/dashboard/data', [QuoteController::class, 'getDashboardData'])->name('dashboard.data');
});

// Ou se preferires usar resource com rotas adicionais:
/*
Route::resource('quotes', QuoteController::class);

Route::prefix('quotes')->name('quotes.')->group(function () {
    Route::post('{quote}/duplicate', [QuoteController::class, 'duplicate'])->name('duplicate');
    Route::post('{quote}/send-email', [QuoteController::class, 'sendEmail'])->name('send-email');
    Route::post('{quote}/update-status', [QuoteController::class, 'updateStatus'])->name('update-status');
    Route::post('{quote}/convert-to-invoice', [QuoteController::class, 'convertToInvoice'])->name('convert-to-invoice');
    Route::get('{quote}/download-pdf', [QuoteController::class, 'downloadPDF'])->name('download-pdf');
    Route::post('bulk-update-status', [QuoteController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
    Route::get('bulk-download-pdf', [QuoteController::class, 'bulkDownloadPDF'])->name('bulk-download-pdf');
    Route::get('export/data', [QuoteController::class, 'export'])->name('export');
    Route::get('dashboard/data', [QuoteController::class, 'getDashboardData'])->name('dashboard.data');
});
*/