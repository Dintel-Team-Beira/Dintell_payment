<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Verificar se a URL tem um slug de empresa
            $companySlug = $this->extractCompanySlug($request);

            if ($companySlug) {
                // Buscar empresa pelo slug
                $company = Company::where('slug', $companySlug)
                                 ->where('status', true)
                                 ->first();

                if (!$company) {
                    // Se empresa não existe, mostrar 404 amigável
                    abort(404, "Empresa '{$companySlug}' não encontrada");
                }

                // Verificar se o usuário atual tem acesso a esta empresa
                $user = auth()->user();
                if ($user && !($user->is_super_admin ?? false) && $user->company_id !== $company->id) {
                    // Redirecionar para a empresa correta do usuário
                    if ($user->company_id) {
                        $userCompany = Company::find($user->company_id);
                        if ($userCompany && $userCompany->slug) {
                            return redirect("/{$userCompany->slug}" . $request->getRequestUri());
                        }
                    }

                    abort(403, 'Acesso negado a esta empresa');
                }

                // Definir contexto da empresa
                $this->setCompanyContext($company, $request);
            } else {
                // Se não há slug, usar empresa do usuário logado
                $user = auth()->user();
                if ($user && $user->company_id) {
                    $company = Company::find($user->company_id);
                    if ($company) {
                        $this->setCompanyContext($company, $request);
                    }
                }
            }

            return $next($request);

        } catch (\Exception $e) {
            // Log do erro e continuar sem empresa no contexto
            Log::warning('Erro no TenantMiddleware: ' . $e->getMessage());
            return $next($request);
        }
    }

    /**
     * Extrair slug da empresa da URL
     */
    private function extractCompanySlug(Request $request)
    {
        $path = trim($request->path(), '/');

        // Se é apenas '/', não há slug
        if (empty($path) || $path === '/') {
            return null;
        }

        $segments = explode('/', $path);

        // Lista de prefixos que NÃO são slugs de empresa
        $systemPrefixes = [
            'admin', 'api', 'login', 'register', 'password', 'suspended',
            'renew', 'limpar-cache', 'dashboard', 'clients', 'subscriptions',
            'quotes', 'invoices', 'products', 'services', 'billing',
            'configuracoes', 'settings', 'profile'
        ];

        // Verificar se o primeiro segmento é um slug válido
        if (!empty($segments[0]) && !in_array($segments[0], $systemPrefixes)) {
            return $segments[0];
        }

        return null;
    }

    /**
     * Definir contexto da empresa
     */
    private function setCompanyContext($company, Request $request)
    {
        // Definir empresa atual
        Config::set('app.current_company', $company);
        $request->attributes->set('company', $company);

        // Compartilhar com views
        View::share('currentCompany', $company);

        // Definir configurações específicas da empresa
        Config::set('company.current', [
            'id' => $company->id,
            'name' => $company->name,
            'slug' => $company->slug,
            'email' => $company->email ?? '',
            'phone' => $company->phone ?? '',
            'address' => $company->address ?? '',
        ]);
    }
}
