<?php
// app/Http/Controllers/Api/SubscriptionVerificationController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\ApiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class SubscriptionVerificationController extends Controller
{
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string|max:255',
            'api_key' => 'sometimes|string|size:43|starts_with:sk_'
        ]);

        if ($validator->fails()) {
            return $this->logAndRespond($request, [
                'status' => 'error',
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
                'action' => 'block'
            ], 400);
        }

        $domain = $request->input('domain');
        $apiKey = $request->input('api_key');

        // Cache da verificação
        $cacheKey = "domain_verify_{$domain}";
        $subscription = Cache::remember($cacheKey, 60, function () use ($domain, $apiKey) {
            $query = Subscription::where('domain', $domain)
                                ->orWhere('subdomain', $domain);

            if ($apiKey) {
                $query->where('api_key', $apiKey);
            }

            return $query->with(['client', 'plan'])->first();
        });

        if (!$subscription) {
            return $this->logAndRespond($request, [
                'status' => 'not_found',
                'message' => 'Domínio não encontrado no sistema',
                'action' => 'allow', // Permite acesso se não está sendo gerenciado
                'managed' => false
            ], 404, null);
        }

        // Verificação completa de acesso
        if (!$subscription->canAccess()) {
            $reason = $this->getBlockReason($subscription);
            $suspensionConfig = $subscription->suspension_page_config ?? $this->getDefaultSuspensionConfig($reason);

            return $this->logAndRespond($request, [
                'status' => 'blocked',
                'message' => $this->getBlockMessage($reason),
                'action' => 'redirect',
                'redirect_url' => route('suspension.page', ['domain' => $domain, 'reason' => $reason]),
                'reason' => $reason,
                'suspension_config' => $suspensionConfig,
                'managed' => true
            ], 403, $subscription);
        }

        // Subscrição ativa - incrementar uso
        $subscription->incrementUsage(1, 0.001);

        return $this->logAndRespond($request, [
            'status' => 'active',
            'message' => 'Acesso permitido',
            'action' => 'allow',
            'managed' => true,
            'subscription' => [
                'id' => $subscription->id,
                'plan' => $subscription->plan->name,
                'client' => $subscription->client->name,
                'expires_at' => $subscription->ends_at?->toISOString(),
                'days_until_expiry' => $subscription->days_until_expiry,
                'trial_days_left' => $subscription->trial_days_left,
                'usage_percentage' => $subscription->usage_percentage,
                'is_trial' => $subscription->isTrial()
            ]
        ], 200, $subscription);
    }

    public function quickCheck(Request $request)
    {
        // Verificação rápida apenas por domínio (para middleware)
        $domain = $request->input('domain') ?? $request->getHost();

        $subscription = Cache::remember("quick_check_{$domain}", 300, function () use ($domain) {
            return Subscription::where('domain', $domain)
                             ->orWhere('subdomain', $domain)
                             ->select(['id', 'status', 'manual_status', 'ends_at', 'trial_ends_at'])
                             ->first();
        });

        if (!$subscription) {
            return response()->json(['access' => 'allow', 'managed' => false]);
        }

        $access = $subscription->canAccess() ? 'allow' : 'block';

        return response()->json([
            'access' => $access,
            'managed' => true,
            'subscription_id' => $subscription->id
        ]);
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
            return $subscription->status === 'trial' ? 'trial_expired' : 'expired';
        }

        return 'unknown';
    }

    private function getBlockMessage($reason)
    {
        $messages = [
            'manually_disabled' => 'Serviço desabilitado manualmente',
            'suspended' => 'Subscrição suspensa',
            'cancelled' => 'Subscrição cancelada',
            'expired' => 'Subscrição expirada',
            'trial_expired' => 'Período de teste expirado',
            'unknown' => 'Acesso negado'
        ];

        return $messages[$reason] ?? $messages['unknown'];
    }

    private function getDefaultSuspensionConfig($reason)
    {
        $configs = [
            'manually_disabled' => [
                'title' => 'Serviço Temporariamente Desabilitado',
                'message' => 'Este website foi temporariamente desabilitado pelo administrador.',
                'icon' => 'pause',
                'color' => 'orange'
            ],
            'suspended' => [
                'title' => 'Conta Suspensa',
                'message' => 'Este website foi suspenso devido a violação dos termos de serviço ou problemas de pagamento.',
                'icon' => 'warning',
                'color' => 'red'
            ],
            'cancelled' => [
                'title' => 'Serviço Cancelado',
                'message' => 'A subscrição deste website foi cancelada.',
                'icon' => 'x',
                'color' => 'red'
            ],
            'expired' => [
                'title' => 'Subscrição Expirada',
                'message' => 'A subscrição deste website expirou. Entre em contato para renovar.',
                'icon' => 'clock',
                'color' => 'yellow'
            ],
            'trial_expired' => [
                'title' => 'Período de Teste Expirado',
                'message' => 'O período de teste gratuito expirou. Faça upgrade para continuar.',
                'icon' => 'clock',
                'color' => 'blue'
            ]
        ];

        $defaultConfig = $configs[$reason] ?? $configs['suspended'];

        return array_merge($defaultConfig, [
            'support_email' => config('app.support_email', 'support@example.com'),
            'support_phone' => config('app.support_phone', '+258 XX XXX XXXX'),
            'company_name' => config('app.name', 'SubManager')
        ]);
    }

    private function logAndRespond(Request $request, array $responseData, int $statusCode, ?Subscription $subscription = null)
    {
        // Log detalhado da requisição
        ApiLog::create([
            'subscription_id' => $subscription?->id,
            'domain' => $request->input('domain'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'endpoint' => $request->path(),
            'request_data' => $request->all(),
            'response_data' => $responseData,
            'response_code' => $statusCode
        ]);

        return response()->json($responseData, $statusCode);
    }
}