<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar se o usuário está autenticado
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        // Verificar se o usuário é admin do SaaS
        if (!$user->is_super_admin) {
            // Se não é admin, redireciona para o sistema normal
            if ($user->company_id) {
                return redirect()->route('billing.dashboard')
                    ->with('error', 'Acesso negado. Você não tem permissões administrativas.');
            }

            // Se não tem empresa associada, deslogar
            Auth::logout();
            return redirect()->route('admin.login')
                ->with('error', 'Acesso negado. Você não tem permissões administrativas.');
        }

        // Registrar atividade do admin
        $user->update(['last_activity_at' => now()]);

        return $next($request);
    }
}
