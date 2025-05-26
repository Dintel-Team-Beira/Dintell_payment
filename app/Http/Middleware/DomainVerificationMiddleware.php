<?php
// app/Http/Middleware/DomainVerificationMiddleware.php

namespace App\Http\Middleware;

use App\Models\Subscription;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DomainVerificationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Pegar o domínio atual
        $domain = $request->getHost();

        // Cache da verificação por 5 minutos
        $cacheKey = "domain_check_{$domain}";
        $subscription = Cache::remember($cacheKey, 300, function () use ($domain) {
            return Subscription::where('domain', $domain)
                             ->orWhere('subdomain', $domain)
                             ->with(['client', 'plan'])
                             ->first();
        });

        // Se não encontrou subscrição, permitir acesso (não é um domínio gerenciado)
        if (!$subscription) {
            return $next($request);
        }

        // Verificar se pode acessar
        if (!$subscription->canAccess()) {
            // Log da tentativa de acesso
            $subscription->apiLogs()->create([
                'domain' => $domain,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'endpoint' => $request->path(),
                'request_data' => $request->all(),
                'response_data' => ['status' => 'blocked', 'reason' => $this->getBlockReason($subscription)],
                'response_code' => 403
            ]);

            // Redirecionar para página de suspensão
            return redirect()->route('suspension.page', [
                'domain' => $domain,
                'reason' => $this->getBlockReason($subscription)
            ]);
        }

        // Incrementar uso
        $subscription->incrementUsage(1, 0.001); // 1 request, ~1KB bandwidth

        // Adicionar informações ao request
        $request->merge(['subscription' => $subscription]);

        return $next($request);
    }

    private function getBlockReason($subscription)
    {
        if ($subscription->manual_status === 'disabled') {
            return 'manually_disabled';
        }

        if ($subscription->status === 'suspended') {
            return 'suspended';
        }

        if ($subscription->status === 'cancelled') {
            return 'cancelled';
        }

        if ($subscription->isExpired()) {
            return 'expired';
        }

        return 'unknown';
    }
}