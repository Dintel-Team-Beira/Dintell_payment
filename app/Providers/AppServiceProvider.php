<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Barryvdh\DomPDF\Facade\Pdf;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registrar DomPDF
        if ($this->app->environment('local')) {
            $this->app->register(\Barryvdh\DomPDF\ServiceProvider::class);
        }
    }

    public function boot(): void
    {
        // Configurações adicionais do DomPDF
        if (class_exists('\Barryvdh\DomPDF\ServiceProvider')) {
            Pdf::setOption(['dpi' => 96, 'defaultFont' => 'sans-serif']);
        }
    }
}