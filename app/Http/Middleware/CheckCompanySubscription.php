<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanySubscription
{
    /**
     * Rotas que devem ser excluídas da verificação
     */
    protected array $excludedRoutes = [
        'logout',
        'billing.*',
        'billing.plans',
        'billing.upgrade',
        'billing.payment',
        'support.*',
        'profile.*', // Para poder atualizar dados
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se não estiver autenticado, deixa passar (outro middleware cuida)
        if (!auth()->check()) {
            return $next($request);
        }

         // Verificar se a rota atual está excluída
        if ($this->shouldExclude($request)) {
            return $next($request);
        }

        $user = auth()->user();
        $company = $user->company;

        // Se não tem company, bloqueia (não deveria acontecer)
        if (!$company) {
            return redirect()->route('company.required')
                ->with('error', 'Você precisa estar associado a uma empresa.');
        }
        // Verify both status: 
        // if($company->status === \App\Models\Company::STATUS_ACTIVE && $company->subscription_status === \App\Models\Company::SUBSCRIPTION_ACTIVE)
        // {
        //     return $next($request);
        // }
        // ===================================
        // 1º PRIORIDADE: STATUS ADMINISTRATIVO
        // ===================================
        $subscription = $company->subscriptions()->latest()->first();
        // Admin suspendeu manualmente
        if ($company->status === \App\Models\Company::STATUS_SUSPENDED) {
            if($subscription->isSuspended())
            {
                return $this->blockAccess(
                'Subscrição Suspensa',
                'Sua subscrição foi suspensa. Regularize o pagamento para continuar.',
                'subscription_suspended',
                $company
            );   
            }
            return $this->blockAccess(
                'Conta Suspensa',
                $company->suspension_reason ?? 'Sua conta foi suspensa. Entre em contato com o suporte.',
                'suspended',
                $company
            );
        }

        // Admin desativou
        if ($company->status === \App\Models\Company::STATUS_INACTIVE) {
            return $this->blockAccess(
                'Conta Inativa',
                'Sua conta está inativa. Entre em contato com o suporte para reativação.',
                'inactive',
                $company
            );
        }

        // Status pendente (aguardando aprovação?)
        if ($company->status === \App\Models\Company::STATUS_PENDING) {
            return $this->blockAccess(
                'Conta Pendente',
                'Sua conta está em análise. Aguarde a aprovação.',
                'pending',
                $company
            );
        }

        // ===================================
        // 2º PRIORIDADE: SUBSCRIÇÃO
        // ===================================


        // Subscrição cancelada
        // dd($subscription->isCanceled());
        // if ($company->subscription_status === \App\Models\Company::SUBSCRIPTION_STATUS_CANCELLED) {
        if ($subscription->isCanceled()) {
            return $this->blockAccess(
                'Subscrição Cancelada',
                'Sua subscrição foi cancelada. Faça um upgrade para continuar usando o sistema.',
                'cancelled',
                $company
            );
        }

        // Subscrição expirada
        // if ($company->subscription_status === \App\Models\Company::SUBSCRIPTION_STATUS_EXPIRED) {
        if($subscription->isExpired()){
            return $this->blockAccess(
                'Subscrição Expirada',
                'Sua subscrição expirou. Renove agora para continuar usando o sistema.',
                'expired',
                $company
            );
        }

        // Subscrição suspensa (provavelmente por falta de pagamento)
        // if ($company->subscription_status === \App\Models\Company::SUBSCRIPTION_STATUS_SUSPENDED) {
        if($subscription->isSuspended()){
            
            return $this->blockAccess(
                'Subscrição Suspensa',
                'Sua subscrição foi suspensa. Regularize o pagamento para continuar.',
                'subscription_suspended',
                $company
            );
        }

        // ===================================
        // 3º PRIORIDADE: TRIAL
        // ===================================

        // Trial expirado
        if ($company->subscription_type === \App\Models\Company::SUBSCRIPTION_TYPE_TRIAL) {
            if ($company->trial_ends_at && $company->trial_ends_at->isPast()) {
                return $this->blockAccess(
                    'Período de Teste Expirado',
                    'Seu período de teste expirou. Faça upgrade para um plano pago para continuar.',
                    'trial_expired',
                    $company
                );
            }
        }

        // ===================================
        // 4º PRIORIDADE: LIMITES DE USO
        // ===================================

        // dd($company->plan_id == $company->plan->id);
        // Verificar limites apenas se tiver plano
        if ($company->plan_id && $company->plan) {
            
            // Limite de usuários excedido
            $userUsage = $company->getUserUsageFeatured();
            // dd($userUsage);
            if ($userUsage['exceeded']) {
                // Bloqueia criação de novos usuários, mas permite uso do sistema
                // Você pode ajustar se quiser bloquear totalmente
                session()->flash('warning', 'Você atingiu o limite de usuários do seu plano.');
            }

            // Limite de faturas mensais excedido
            $invoiceUsage = $company->getInvoiceUsage();
            // $invoiceUsage = $company->getInvoiceUsageFeatured();
            // dd($invoiceUsage);
            if ($invoiceUsage['exceeded']) {
                // Bloqueia criação de novas faturas
                session()->flash('warning', 'Você atingiu o limite de faturas mensais do seu plano.');
            }

            $clientUsage = $company->getClientUsage();
            if ($clientUsage['exceeded']) {
                // Bloqueia criação de novos clientes
                session()->flash('warning', 'Você atingiu o limite de clientes do seu plano.');
            }
        }

        // ===================================
        // 5º PRIORIDADE: AVISOS (não bloqueia)
        // ===================================

        // Pagamento pendente (avisa mas não bloqueia)
        if ($company->subscription_status === \App\Models\Company::SUBSCRIPTION_STATUS_PENDING_PAYMENT) {
            session()->flash('warning', 'Você tem um pagamento pendente. Regularize para evitar suspensão.');
        }

        // Trial expirando em breve
        if ($company->subscription_type === \App\Models\Company::SUBSCRIPTION_TYPE_TRIAL) {
            if ($company->trial_ends_at) {
                $daysLeft = $company->trial_ends_at->diffInDays(now());
                if ($daysLeft <= 7 && $daysLeft > 0) {
                    session()->flash('trial_expiring', [
                        'days' => $daysLeft,
                        'message' => "Seu período de teste expira em {$daysLeft} dias."
                    ]);
                }
            }
        }

        // Avisos de uso
        $warnings = $company->getUsageWarnings();
        if (!empty($warnings)) {
            session()->flash('usage_warnings', $warnings);
        }

            // dd($company->getClientUsage());
        return $next($request);
    }
      /**
     * Verificar se deve excluir a rota da verificação
     */
    protected function shouldExclude(Request $request): bool
    {
        foreach ($this->excludedRoutes as $route) {
            if ($request->routeIs($route)) {
                return true;
            }
        }

        return false;
    }

     /**
     * Bloquear acesso e redirecionar
     */
    protected function blockAccess(
        string $title,
        string $message,
        string $reason,
        $company
    ): Response {
        // Se for requisição AJAX, retorna JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'blocked' => true,
                'reason' => $reason,
                'title' => $title,
                'message' => $message,
                'redirect' => route('subscription.blocked')
            ], 403);
        }

        // Redirecionar para página de bloqueio com dados
        return redirect()->route('subscription.blocked')
            ->with([
                'block_reason' => $reason,
                'block_title' => $title,
                'block_message' => $message,
                'company' => $company,
            ]);
    }
}
