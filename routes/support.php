<?php

// Adicionar ao arquivo routes/web.php

use App\Http\Controllers\SupportController;
use App\Http\Controllers\Admin\AdminSupportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Support Routes - Public/User
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('support')->name('support.')->group(function () {

    // Visualização de tickets do usuário
    Route::get('/my-tickets', [SupportController::class, 'myTickets'])->name('my-tickets');
    Route::get('/tickets/{ticket}', [SupportController::class, 'show'])->name('tickets.show');

    // Criação e gestão de tickets
    Route::post('/tickets', [SupportController::class, 'store'])->name('tickets.store');
    Route::post('/tickets/{ticket}/reply', [SupportController::class, 'addReply'])->name('tickets.reply');
    Route::patch('/tickets/{ticket}/close', [SupportController::class, 'close'])->name('tickets.close');
    Route::patch('/tickets/{ticket}/reopen', [SupportController::class, 'reopen'])->name('tickets.reopen');
    Route::post('/tickets/{ticket}/rate', [SupportController::class, 'rateSatisfaction'])->name('tickets.rate');

    // Downloads
    Route::get('/tickets/{ticket}/attachments/{index}', [SupportController::class, 'downloadAttachment'])
         ->name('tickets.attachments.download');

    // Base de conhecimento e FAQ
    Route::get('/knowledge-base/search', [SupportController::class, 'searchKnowledgeBase'])->name('kb.search');

    // Estatísticas do usuário
    Route::get('/stats', [SupportController::class, 'getUserStats'])->name('stats');
});

/*
|--------------------------------------------------------------------------
| Support API Routes - Para o popup
|--------------------------------------------------------------------------
*/



/*
|--------------------------------------------------------------------------
| Admin Support Routes - Já existentes, mas aprimoradas
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('admin/support')->name('admin.support.')->group(function () {

    // Dashboard de suporte
    Route::get('/', [AdminSupportController::class, 'index'])->name('index');

    // Gestão de tickets
    Route::get('/tickets', [AdminSupportController::class, 'tickets'])->name('tickets');
    Route::get('/tickets/{ticket}', [AdminSupportController::class, 'showTicket'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [AdminSupportController::class, 'replyTicket'])->name('tickets.reply');
    Route::patch('/tickets/{ticket}/status', [AdminSupportController::class, 'updateTicketStatus'])->name('tickets.status');
    Route::patch('/tickets/{ticket}/assign', [AdminSupportController::class, 'assignTicket'])->name('tickets.assign');

    // Relatórios
    Route::get('/reports', [AdminSupportController::class, 'reports'])->name('reports');

    // Configurações do sistema de suporte (futuro)
    Route::get('/settings', [AdminSupportController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminSupportController::class, 'updateSettings'])->name('settings.update');
});



// Support Routes - User
Route::middleware(['auth'])->prefix('support')->name('support.')->group(function () {
    Route::get('/my-tickets', [SupportController::class, 'myTickets'])->name('my-tickets');
    Route::get('/tickets/{ticket}', [SupportController::class, 'show'])->name('tickets.show');
    Route::post('/tickets', [SupportController::class, 'store'])->name('tickets.store');
    Route::post('/tickets/{ticket}/reply', [SupportController::class, 'addReply'])->name('tickets.reply');
    Route::patch('/tickets/{ticket}/close', [SupportController::class, 'close'])->name('tickets.close');
    Route::patch('/tickets/{ticket}/reopen', [SupportController::class, 'reopen'])->name('tickets.reopen');
    Route::post('/tickets/{ticket}/rate', [SupportController::class, 'rateSatisfaction'])->name('tickets.rate');
    Route::get('/tickets/{ticket}/attachments/{index}', [SupportController::class, 'downloadAttachment'])->name('tickets.attachments.download');
    Route::get('/knowledge-base/search', [SupportController::class, 'searchKnowledgeBase'])->name('kb.search');
    Route::get('/stats', [SupportController::class, 'getUserStats'])->name('stats');
});

// Support API Routes - Para o popup
Route::middleware(['auth'])->prefix('api/support')->name('api.support.')->group(function () {
    Route::get('/tickets/my', [SupportController::class, 'myTickets'])->name('tickets.my');
    Route::post('/tickets', [SupportController::class, 'store'])->name('tickets.store');
    Route::get('/knowledge-base/search', [SupportController::class, 'searchKnowledgeBase'])->name('kb.search');
    Route::get('/stats', [SupportController::class, 'getUserStats'])->name('stats');
    Route::post('/tickets/{ticket}/quick-reply', [SupportController::class, 'addReply'])->name('tickets.quick-reply');
    Route::patch('/tickets/{ticket}/quick-close', [SupportController::class, 'close'])->name('tickets.quick-close');
});

// Admin Support Routes
Route::middleware(['auth'])->prefix('admin/support')->name('admin.support.')->group(function () {
    Route::get('/', [AdminSupportController::class, 'index'])->name('index');
    Route::get('/tickets', [AdminSupportController::class, 'tickets'])->name('tickets');
    Route::get('/tickets/{ticket}', [AdminSupportController::class, 'showTicket'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [AdminSupportController::class, 'replyTicket'])->name('tickets.reply');
    Route::patch('/tickets/{ticket}/status', [AdminSupportController::class, 'updateTicketStatus'])->name('tickets.status');
    Route::patch('/tickets/{ticket}/assign', [AdminSupportController::class, 'assignTicket'])->name('tickets.assign');
    Route::get('/reports', [AdminSupportController::class, 'reports'])->name('reports');
});

