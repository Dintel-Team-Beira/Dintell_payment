<?php

namespace App\Providers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

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

        // Redefine a lógica de redirecionamento para usuários autenticados (middleware 'guest')
        RedirectIfAuthenticated::redirectUsing(function ($request) {

            // 1. O usuário já está autenticado
            $user = Auth::user();

            // 2. Lógica de redirecionamento
            if ($user) {

                // Redirecionamento para Master Admin
                if ($user->is_admin) {
                    return route('admin.dashboard'); // Não precisa de tenant
                }

                // Redirecionamento para Tenant User
                if ($user->company && $user->company->slug) {
                    // Monta a rota dinamicamente
                    return route('dashboard', ['tenant' => $user->company->slug]);
                }
            }

            // Fallback (se a lógica de tenant falhar)
            return '/';
        });
        
        // Route::model('invoice', Invoice::class);
    }
}
