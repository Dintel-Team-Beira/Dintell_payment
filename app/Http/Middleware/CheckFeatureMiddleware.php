<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckFeatureMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Verifica se a empresa tem acesso a uma feature específica
     */
    public function handle(Request $request, Closure $next, string $feature)
    {
        $user = auth()->user();
        $company = session('current_company');

        // Super admins têm acesso a tudo
        if ($user && $user->is_super_admin) {
            return $next($request);
        }

        if (!$company) {
            return redirect()->route('login');
        }

        // Verificar se a empresa tem a feature
        if (!$this->hasFeature($company, $feature)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Feature não disponível no seu plano.',
                    'feature' => $feature,
                    'current_plan' => $company->subscription_plan,
                    'upgrade_url' => route('billing.upgrade')
                ], 403);
            }

            return redirect()->back()
                ->with('warning', $this->getFeatureMessage($feature));
        }

        return $next($request);
    }

    private function hasFeature($company, string $feature): bool
    {
        // Mapear features para verificações
        switch ($feature) {
            case 'api':
                return $company->api_access_enabled;

            case 'custom_domain':
                return $company->custom_domain_enabled;

            case 'advanced_reports':
                return $company->hasFeature('advanced_reports');

            case 'multi_currency':
                return $company->hasFeature('multi_currency');

            case 'custom_branding':
                return $company->hasFeature('custom_branding');

            case 'priority_support':
                return $company->hasFeature('priority_support');

            case 'unlimited_invoices':
                return $company->max_invoices_per_month >= 999999;

            case 'unlimited_clients':
                return $company->max_clients >= 999999;

            case 'team_collaboration':
                return $company->max_users > 1;

            default:
                // Features customizadas no array feature_flags
                return $company->hasFeature($feature);
        }
    }

    private function getFeatureMessage(string $feature): string
    {
        $messages = [
            'api' => 'Acesso à API não está disponível no seu plano atual.',
            'custom_domain' => 'Domínio personalizado não está disponível no seu plano atual.',
            'advanced_reports' => 'Relatórios avançados não estão disponíveis no seu plano atual.',
            'multi_currency' => 'Suporte a múltiplas moedas não está disponível no seu plano atual.',
            'custom_branding' => 'Personalização da marca não está disponível no seu plano atual.',
            'priority_support' => 'Suporte prioritário não está disponível no seu plano atual.',
            'unlimited_invoices' => 'Você atingiu o limite de faturas do seu plano.',
            'unlimited_clients' => 'Você atingiu o limite de clientes do seu plano.',
            'team_collaboration' => 'Colaboração em equipe não está disponível no seu plano atual.',
        ];

        return $messages[$feature] ?? "Esta funcionalidade não está disponível no seu plano atual.";
    }
}
