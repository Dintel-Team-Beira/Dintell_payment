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
            $user = auth()->user();

            // Super admins não precisam de contexto de empresa
            if ($user && $user->is_super_admin) {
                return $next($request);
            }

            // Verificar se a URL tem um slug de empresa
            $companySlug = $this->extractCompanySlug($request);

            if ($companySlug) {
                // Buscar empresa pelo slug
                $company = Company::where('slug', $companySlug)
                                 ->where('status', 'active')
                                 ->first();

                if (!$company) {
                    abort(404, "Empresa '{$companySlug}' não encontrada");
                }

                // Verificar se o usuário tem acesso a esta empresa
                if ($user && $user->company_id !== $company->id) {
                    abort(403, 'Acesso negado a esta empresa');
                }

                // Definir contexto da empresa
                $this->setCompanyContext($company, $request);

                // ⭐ IMPORTANTE: Remover o slug da URL para o Route Model Binding funcionar
                $this->adjustRequestPath($request, $companySlug);

            } else {
                // Se não há slug na URL e usuário está logado, redirecionar com slug
                if ($user && $user->company_id) {
                    $company = Company::find($user->company_id);
                    if ($company && $company->slug) {
                        // Redirecionar para incluir o slug da empresa
                        $currentPath = $request->path();

                        // Não redirecionar se já estiver em rotas especiais
                        if (!$this->isSpecialRoute($currentPath)) {
                            $newUrl = url("/{$company->slug}/{$currentPath}");
                            return redirect($newUrl);
                        }
                    }
                }
            }

            return $next($request);

        } catch (\Exception $e) {
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
            'renew', 'limpar-cache', 'logout', 'forgot-password',
            'reset-password', 'verify-email', 'confirm-password',
            'email'
        ];

        // Verificar se o primeiro segmento é um slug válido
        if (!empty($segments[0]) && !in_array($segments[0], $systemPrefixes)) {
            return $segments[0];
        }

        return null;
    }

    /**
     * Ajustar o path do request removendo o slug da empresa
     */
    private function adjustRequestPath(Request $request, string $companySlug)
    {
        $currentPath = $request->path();

        // Remove o slug da empresa do início do path
        if (str_starts_with($currentPath, $companySlug . '/')) {
            $newPath = substr($currentPath, strlen($companySlug) + 1);

            // Atualizar o path interno do Laravel
            $request->server->set('REQUEST_URI', '/' . $newPath . ($request->getQueryString() ? '?' . $request->getQueryString() : ''));
            $request->server->set('PATH_INFO', '/' . $newPath);

            // Para debug
            Log::info("TenantMiddleware: Path ajustado de '{$currentPath}' para '{$newPath}'");
        }
    }

    /**
     * Verificar se é uma rota especial que não precisa de slug
     */
    private function isSpecialRoute($path)
    {
        $specialRoutes = [
            'dashboard', 'profile', 'logout', 'verify-email',
            'confirm-password', 'email/verification-notification'
        ];

        foreach ($specialRoutes as $route) {
            if (str_starts_with($path, $route)) {
                return true;
            }
        }

        return false;
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
