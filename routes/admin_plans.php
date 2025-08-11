<?php

use App\Http\Controllers\Admin\PlansController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // Rotas para planos
    Route::resource('plans', PlansController::class);

    // Rotas adicionais para planos
    Route::post('/plans/{plan}/toggle-status', [PlansController::class, 'toggleStatus'])->name('plans.toggle-status');
    Route::post('/plans/{plan}/toggle-popular', [PlansController::class, 'togglePopular'])->name('plans.toggle-popular');
    Route::post('/plans/{plan}/duplicate', [PlansController::class, 'duplicate'])->name('plans.duplicate');
    Route::post('/plans/reorder', [PlansController::class, 'reorder'])->name('plans.reorder');

});
