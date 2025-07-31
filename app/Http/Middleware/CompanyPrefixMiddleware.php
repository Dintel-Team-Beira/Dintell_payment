<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use App\Models\Company;
class CompanyPrefixMiddleware
{
   public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Se não tem usuário ou é super admin, não faz nada
        if (!$user || $user->is_super_admin) {
            return $next($request);
        }

        // Se tem empresa, pegar o slug
        if ($user->company_id) {
            $company = Company::find($user->company_id);

            if ($company && $company->slug) {
                $prefix = $company->slug;
                $currentPath = trim($request->path(), '/');

                // Se não tem o prefixo na URL e precisa ter, redirecionar
                if (!str_starts_with($currentPath, $prefix . '/') && $this->needsPrefix($currentPath)) {
                    if (empty($currentPath) || $currentPath === 'dashboard') {
                        return redirect("/{$prefix}/dashboard");
                    }
                    return redirect("/{$prefix}/{$currentPath}");
                }

                // Salvar o prefixo para usar nas rotas
                config(['app.company_prefix' => $prefix]);
                $request->attributes->set('company_prefix', $prefix);
                $request->attributes->set('company', $company);
            }
        }

        return $next($request);
    }

    private function needsPrefix($path)
    {
        $routes = ['dashboard', 'invoices', 'clients', 'products', 'services', 'quotes', 'subscriptions', 'settings'];

        foreach ($routes as $route) {
            if (str_starts_with($path, $route)) {
                return true;
            }
        }

        return empty($path) || $path === '/';
    }
}
