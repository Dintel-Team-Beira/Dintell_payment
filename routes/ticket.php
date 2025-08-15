<?php
// Adicionar essas rotas ao seu arquivo routes/web.php

// Support API Routes - Adicionar dentro do grupo middleware(['auth'])
Route::middleware(['auth'])->group(function () {

    // ... suas outras rotas existentes ...

    // API Routes para Support Popup
    Route::prefix('api/support')->name('api.support.')->group(function () {

        // Tickets do usuário
        Route::get('/tickets/my', [SupportController::class, 'myTickets'])->name('tickets.my');

        // Criar novo ticket
        Route::post('/tickets', [SupportController::class, 'store'])->name('tickets.store');

        // Visualizar ticket específico
        Route::get('/tickets/{ticket}', [SupportController::class, 'show'])->name('tickets.show');

        // Adicionar resposta ao ticket
        Route::post('/tickets/{ticket}/reply', [SupportController::class, 'addReply'])->name('tickets.reply');

        // Fechar ticket
        Route::patch('/tickets/{ticket}/close', [SupportController::class, 'close'])->name('tickets.close');

        // Base de conhecimento
        Route::get('/knowledge-base/search', [SupportController::class, 'searchKnowledgeBase'])->name('kb.search');

        // Estatísticas do usuário
        Route::get('/stats', [SupportController::class, 'getUserStats'])->name('stats');
    });

    // Support Views (opcional - para páginas completas de suporte)
    Route::prefix('support')->name('support.')->group(function () {

        // Página principal de suporte
        Route::get('/', function () {
            return view('support.index');
        })->name('index');

        // Meus tickets (página completa)
        Route::get('/my-tickets', [SupportController::class, 'myTickets'])->name('my-tickets');

        // Visualizar ticket (página completa)
        Route::get('/tickets/{ticket}', [SupportController::class, 'show'])->name('tickets.show');

        // FAQ
        Route::get('/faq', function () {
            $supportController = new SupportController();
            $faq = $supportController->getFAQData();
            return view('support.faq', compact('faq'));
        })->name('faq');
    });
});
