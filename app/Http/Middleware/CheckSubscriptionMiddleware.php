<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Verifica se a empresa pode realizar a ação baseado no seu plano
     */
    public function handle(Request $request, Closure $next, ...$features)
    {
        $company = session('current_company');

        if (!$company) {
            return redirect()->route('login')
                ->with('error', 'Empresa não identificada.');
        }

        // Verificar se a empresa está ativa
        if (!in_array($company->status, ['active', 'trial'])) {
            return redirect()->route('billing.dashboard')
                ->with('error', 'Sua assinatura não está ativa. Entre em contato com o suporte.');
        }

        // Verificar trial expirado
        if ($company->status === 'trial' && $company->trial_ends_at && $company->trial_ends_at->isPast()) {
            return redirect()->route('billing.dashboard')
                ->with('warning', 'Seu período de trial expirou. Faça upgrade para continuar.');
        }

        // Verificar features específicas se fornecidas
        foreach ($features as $feature) {
            if (!$this->checkFeatureAccess($company, $feature)) {
                return redirect()->route('billing.dashboard')
                    ->with('warning', "Este recurso não está disponível no seu plano atual. Faça upgrade para acessá-lo.");
            }
        }

        return $next($request);
    }

    private function checkFeatureAccess($company, $feature): bool
    {
        $featureMap = [
            'api_access' => $company->api_access_enabled,
            'custom_domain' => $company->custom_domain_enabled,
            'advanced_reports' => $company->hasFeature('advanced_reports'),
            'multi_currency' => $company->hasFeature('multi_currency'),
            'custom_branding' => $company->hasFeature('custom_branding'),
            'priority_support' => $company->hasFeature('priority_support'),
        ];

        return $featureMap[$feature] ?? true;
    }
}
