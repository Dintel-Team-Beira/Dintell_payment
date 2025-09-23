<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class SubscriptionControllerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return $next($request);
        }

        // Verificar se a empresa tem plano ativo
        if (!$company->hasActivePlan()) {
            return $this->handleExpiredSubscription($request, $company);
        }

        // Verificar limites
        $limits = $this->checkLimits($company);

        if ($limits['blocked']) {
            return $this->handleLimitExceeded($request, $company, $limits);
        }

        // Adicionar dados de limite às views
        View::share('subscription_status', [
            'company' => $company,
            'plan' => $company->plan,
            'limits' => $limits,
            'warnings' => $this->getWarnings($company, $limits)
        ]);

        return $next($request);
    }

    private function checkLimits($company)
    {
        $plan = $company->plan;
        $currentPeriod = now()->startOfMonth();

        // Contar usuários
        $currentUsers = $company->users()->count();
        $maxUsers = $plan->max_users;

        // Contar faturas do mês
        $currentInvoices = $company->invoices()
            ->whereYear('created_at', $currentPeriod->year)
            ->whereMonth('created_at', $currentPeriod->month)
            ->count();
        $maxInvoices = $plan->max_invoices_per_month;

        // Verificar se está bloqueado
        $blocked = false;
        $blockReasons = [];

        if ($currentUsers > $maxUsers) {
            $blocked = true;
            $blockReasons[] = 'Limite de usuários excedido';
        }

        if ($currentInvoices >= $maxInvoices) {
            $blocked = true;
            $blockReasons[] = 'Limite de faturas mensais atingido';
        }

        // Verificar expiração
        if ($company->subscription_expires_at && $company->subscription_expires_at->isPast()) {
            $blocked = true;
            $blockReasons[] = 'Subscrição expirada';
        }

        return [
            'blocked' => $blocked,
            'reasons' => $blockReasons,
            'users' => [
                'current' => $currentUsers,
                'max' => $maxUsers,
                'percentage' => min(100, ($currentUsers / $maxUsers) * 100)
            ],
            'invoices' => [
                'current' => $currentInvoices,
                'max' => $maxInvoices,
                'percentage' => min(100, ($currentInvoices / $maxInvoices) * 100)
            ],
            'subscription' => [
                'expires_at' => $company->subscription_expires_at,
                'days_left' => $company->subscription_expires_at ?
                    $company->subscription_expires_at->diffInDays(now()) : null,
                'is_trial' => $company->subscription_type === 'trial'
            ]
        ];
    }

    private function getWarnings($company, $limits)
    {
        $warnings = [];

        // Aviso de limite de usuários (80% do limite)
        if ($limits['users']['percentage'] >= 80) {
            $warnings[] = [
                'type' => 'users',
                'message' => 'Você está próximo do limite de usuários. Considere fazer upgrade do seu plano.',
                'percentage' => $limits['users']['percentage']
            ];
        }

        // Aviso de limite de faturas (90% do limite)
        if ($limits['invoices']['percentage'] >= 90) {
            $warnings[] = [
                'type' => 'invoices',
                'message' => 'Você está próximo do limite de faturas mensais.',
                'percentage' => $limits['invoices']['percentage']
            ];
        }

        // Aviso de expiração (7 dias)
        if ($limits['subscription']['days_left'] && $limits['subscription']['days_left'] <= 7) {
            $warnings[] = [
                'type' => 'expiration',
                'message' => 'Sua subscrição expira em ' . $limits['subscription']['days_left'] . ' dias.',
                'days_left' => $limits['subscription']['days_left']
            ];
        }

        return $warnings;
    }

    private function handleExpiredSubscription($request, $company)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'error' => 'Subscription expired',
                'message' => 'Sua subscrição expirou. Renove para continuar usando o sistema.',
                'redirect' => route('subscription.renewal')
            ], 402);
        }

        return redirect()->route('subscription.renewal')
            ->with('error', 'Sua subscrição expirou. Renove para continuar usando o sistema.');
    }

    private function handleLimitExceeded($request, $company, $limits)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'error' => 'Limit exceeded',
                'message' => 'Você atingiu os limites do seu plano: ' . implode(', ', $limits['reasons']),
                'limits' => $limits,
                'redirect' => route('subscription.upgrade')
            ], 402);
        }

        return redirect()->route('subscription.upgrade')
            ->with('error', 'Você atingiu os limites do seu plano: ' . implode(', ', $limits['reasons']))
            ->with('limits', $limits);
    }
}
