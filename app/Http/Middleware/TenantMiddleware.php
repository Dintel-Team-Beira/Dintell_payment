<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;

class TenantMiddleware
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
            return redirect()->route('login');
        }

        $user = Auth::user();


             // Verificar se a URL tem um slug de empresa
        $companySlug = $this->extractCompanySlug($request);

        if ($companySlug) {
            // Buscar empresa pelo slug
            $company = Company::where('slug', $companySlug)
                             ->where('status', true)
                             ->first();

            if (!$company) {
                abort(404, 'Empresa não encontrada');
            }

            // Verificar se o usuário atual tem acesso a esta empresa
            $user = auth()->user();
            if ($user && !$user->is_super_admin && $user->company_id !== $company->id) {
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

        // Se é super admin tentando acessar área de tenant, permitir
        if ($user->is_super_admin && !session('impersonate_company_id')) {
            return redirect()->route('admin.dashboard')
                ->with('info', 'Como administrador, acesse através do painel administrativo.');
        }

        // Identificar empresa (tenant)
        $company = $this->identifyTenant($request, $user);

        if (!$company) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Empresa não encontrada ou inativa.');
        }

        // Verificar se a empresa está ativa
        if (!$this->isCompanyActive($company)) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', $this->getCompanyStatusMessage($company));
        }

        // Verificar limites da empresa
        if (!$this->checkCompanyLimits($company, $request)) {
            return redirect()->route('billing.dashboard')
                ->with('warning', 'Você atingiu os limites do seu plano atual.');
        }

        // Definir empresa atual na sessão
        session(['current_company' => $company]);

        // Definir configuração global da empresa
        config(['tenant.current' => $company]);

        // Atualizar última atividade
        $company->update(['last_activity_at' => now()]);
        $user->update(['last_activity_at' => now()]);

        return $next($request);
    }



    /**
     * Extrair slug da empresa da URL
     */
    private function extractCompanySlug(Request $request)
    {
        $path = trim($request->path(), '/');
        $segments = explode('/', $path);

        // Verificar se o primeiro segmento é um slug válido
        if (!empty($segments[0]) && !in_array($segments[0], [
            'admin', 'api', 'login', 'register', 'password', 'suspended', 'renew'
        ])) {
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
            'email' => $company->email,
            'phone' => $company->phone,
            'address' => $company->address,
        ]);
    }
    /**
     * Identificar a empresa baseado na requisição
     */
    private function identifyTenant(Request $request, $user): ?Company
    {
        // 1. Verificar se há impersonificação
        if (session('impersonate_company_id')) {
            return Company::find(session('impersonate_company_id'));
        }

        // 2. Verificar por subdomínio
        $company = $this->identifyBySubdomain($request);
        if ($company) {
            return $company;
        }

        // 3. Verificar por domínio personalizado
        $company = $this->identifyByCustomDomain($request);
        if ($company) {
            return $company;
        }

        // 4. Usar empresa do usuário
        if ($user->company_id) {
            return Company::find($user->company_id);
        }

        return null;
    }

    /**
     * Identificar empresa por subdomínio
     */
    private function identifyBySubdomain(Request $request): ?Company
    {
        $host = $request->getHost();
        $parts = explode('.', $host);

        if (count($parts) >= 3) {
            $subdomain = $parts[0];

            // Ignorar subdomínios reservados
            $reserved = ['www', 'admin', 'api', 'mail', 'ftp'];
            if (!in_array($subdomain, $reserved)) {
                return Company::where('slug', $subdomain)
                    ->whereIn('status', ['active', 'trial'])
                    ->first();
            }
        }

        return null;
    }

    /**
     * Identificar empresa por domínio personalizado
     */
    private function identifyByCustomDomain(Request $request): ?Company
    {
        $host = $request->getHost();

        return Company::where('domain', $host)
            ->where('custom_domain_enabled', true)
            ->whereIn('status', ['active', 'trial'])
            ->first();
    }

    /**
     * Verificar se a empresa está ativa
     */
    private function isCompanyActive(Company $company): bool
    {
        // Verificar status
        if ($company->status === 'suspended') {
            return false;
        }

        if ($company->status === 'inactive') {
            return false;
        }

        // Verificar se trial expirou
        if ($company->status === 'trial' && $company->trial_ends_at && $company->trial_ends_at->isPast()) {
            // Marcar como inativo se trial expirou
            $company->update(['status' => 'inactive']);
            return false;
        }

        return true;
    }

    /**
     * Verificar limites da empresa
     */
    private function checkCompanyLimits(Company $company, Request $request): bool
    {
        $route = $request->route()->getName();

        // Verificar limite de faturas para rotas de criação
        if (str_contains($route, 'invoices.create') || str_contains($route, 'invoices.store')) {
            if (!$company->canCreateInvoice()) {
                return false;
            }
        }

        // Verificar limite de clientes para rotas de criação
        if (str_contains($route, 'clients.create') || str_contains($route, 'clients.store')) {
            if (!$company->canCreateClient()) {
                return false;
            }
        }

        // Verificar acesso à API se necessário
        if (str_contains($route, 'api.') && !$company->api_access_enabled) {
            return false;
        }

        return true;
    }

    /**
     * Obter mensagem de status da empresa
     */
    private function getCompanyStatusMessage(Company $company): string
    {
        switch ($company->status) {
            case 'suspended':
                $reason = $company->metadata['suspension_reason'] ?? 'Motivo não especificado';
                return "Sua conta foi suspensa. Motivo: {$reason}. Entre em contato com o suporte.";

            case 'inactive':
                return 'Sua conta está inativa. Entre em contato com o suporte para reativá-la.';

            case 'trial':
                if ($company->trial_ends_at && $company->trial_ends_at->isPast()) {
                    return 'Seu período de trial expirou. Faça upgrade para continuar usando o sistema.';
                }
                break;
        }

        return 'Sua conta não está disponível no momento.';
    }
}
