<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use App\Models\Company;

class DynamicPrefixMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $prefix = $this->determineDynamicPrefix($request);

        if ($prefix) {
            // Definir o prefixo no contexto global
            Config::set('app.dynamic_prefix', $prefix);
            $request->attributes->set('dynamic_prefix', $prefix);

            // Aplicar lógica específica baseada no prefixo
            $this->applyPrefixLogic($prefix, $request);
        }

        return $next($request);
    }

    /**
     * Determinar o prefixo dinâmico baseado em múltiplos fatores
     */
    private function determineDynamicPrefix(Request $request): ?string
    {
        // PRIORIDADE 1: Prefixo na URL
        $urlPrefix = $this->extractPrefixFromUrl($request);
        if ($urlPrefix) {
            return $urlPrefix;
        }

        // PRIORIDADE 2: Subdomínio
        $subdomainPrefix = $this->extractPrefixFromSubdomain($request);
        if ($subdomainPrefix) {
            return $subdomainPrefix;
        }

        // PRIORIDADE 3: Usuário logado
        $userPrefix = $this->extractPrefixFromUser($request);
        if ($userPrefix) {
            return $userPrefix;
        }

        // PRIORIDADE 4: Configuração/Environment
        $envPrefix = $this->extractPrefixFromEnvironment();
        if ($envPrefix) {
            return $envPrefix;
        }

        // PRIORIDADE 5: Default/Session
        return $this->extractPrefixFromSession($request);
    }

    /**
     * Extrair prefixo da URL
     */
    private function extractPrefixFromUrl(Request $request): ?string
    {
        $path = trim($request->path(), '/');
        $segments = explode('/', $path);

        // Lista de prefixos válidos (pode vir do banco de dados)
        $validPrefixes = $this->getValidPrefixes();

        if (!empty($segments[0]) && in_array($segments[0], $validPrefixes)) {
            return $segments[0];
        }

        return null;
    }

    /**
     * Extrair prefixo do subdomínio
     */
    private function extractPrefixFromSubdomain(Request $request): ?string
    {
        $host = $request->getHost();
        $mainDomain = config('app.main_domain', 'meuapp.com');

        // Ex: empresa1.meuapp.com -> empresa1
        if (str_contains($host, $mainDomain)) {
            $subdomain = str_replace('.' . $mainDomain, '', $host);

            if ($subdomain !== $host && $this->isValidPrefix($subdomain)) {
                return $subdomain;
            }
        }

        return null;
    }

    /**
     * Extrair prefixo do usuário logado
     */
    private function extractPrefixFromUser(Request $request): ?string
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        // Super admin - sem prefixo ou prefixo especial
        if ($user->is_super_admin) {
            return config('app.admin_prefix', 'admin');
        }

        // Usuário com empresa
        if ($user->company_id) {
            $company = Cache::remember(
                "company_{$user->company_id}",
                300,
                fn() => Company::find($user->company_id)
            );

            return $company?->slug;
        }

        // Por tipo de usuário
        return match($user->user_type ?? 'user') {
            'admin' => 'admin',
            'manager' => 'manager',
            'client' => 'client',
            default => null
        };
    }

    /**
     * Extrair prefixo do ambiente/configuração
     */
    private function extractPrefixFromEnvironment(): ?string
    {
        // Baseado no ambiente
        $env = app()->environment();
        if ($env !== 'production') {
            return config('app.dev_prefix', $env);
        }

        // Baseado na configuração
        return config('app.default_prefix');
    }

    /**
     * Extrair prefixo da sessão
     */
    private function extractPrefixFromSession(Request $request): ?string
    {
        return session('dynamic_prefix');
    }

    /**
     * Aplicar lógica específica baseada no prefixo
     */
    private function applyPrefixLogic(string $prefix, Request $request): void
    {
        // Definir configurações específicas do prefixo
        switch ($prefix) {
            case 'admin':
                Config::set('app.admin_mode', true);
                Config::set('view.theme', 'admin');
                break;

            case 'api':
                Config::set('app.api_mode', true);
                break;

            default:
                // Lógica para prefixos de empresa
                if ($this->isCompanyPrefix($prefix)) {
                    $this->setCompanyContext($prefix, $request);
                }
                break;
        }

        // Log do prefixo para debug
        if (config('app.debug')) {
            \Log::info("Dynamic prefix applied: {$prefix}", [
                'url' => $request->fullUrl(),
                'user_id' => auth()->id()
            ]);
        }
    }

    /**
     * Definir contexto da empresa
     */
    private function setCompanyContext(string $companySlug, Request $request): void
    {
        $company = Cache::remember(
            "company_by_slug_{$companySlug}",
            300,
            fn() => Company::where('slug', $companySlug)->where('status', 'active')->first()
        );

        if ($company) {
            Config::set('app.current_company', $company);
            $request->attributes->set('company', $company);

            // Definir tema/configurações específicas da empresa
            if ($company->theme) {
                Config::set('view.theme', $company->theme);
            }
        }
    }

    /**
     * Verificar se é um prefixo válido
     */
    private function isValidPrefix(string $prefix): bool
    {
        return in_array($prefix, $this->getValidPrefixes());
    }

    /**
     * Verificar se é um prefixo de empresa
     */
    private function isCompanyPrefix(string $prefix): bool
    {
        $systemPrefixes = ['admin', 'api', 'auth', 'login', 'register'];
        return !in_array($prefix, $systemPrefixes);
    }

    /**
     * Obter lista de prefixos válidos
     */
    private function getValidPrefixes(): array
    {
        return Cache::remember('valid_prefixes', 3600, function () {
            $systemPrefixes = [
                'admin', 'api', 'auth', 'login', 'register', 'manager', 'client'
            ];

            $companyPrefixes = Company::where('status', 'active')
                ->whereNotNull('slug')
                ->pluck('slug')
                ->toArray();

            return array_merge($systemPrefixes, $companyPrefixes);
        });
    }
}
