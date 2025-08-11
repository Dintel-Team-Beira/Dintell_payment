<?php

use App\Http\Controllers\Admin\AdminMonitoringController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/performance', [AdminMonitoringController::class, 'performance'])->name('performance');
        Route::get('/health', [AdminMonitoringController::class, 'health'])->name('health');
        Route::get('/metrics', [AdminMonitoringController::class, 'metrics'])->name('metrics');
    });
});
