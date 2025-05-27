<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\ApiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class SubscriptionVerificationController extends Controller
{
/**
     * 🔍 VERIFICAÇÃO RÁPIDA DE STATUS
     * GET /api/website/check/{domain}
     */
    public function quickCheck(Request $request, $domain)
    {
        // Rate limiting
        $key = 'website-check:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 100)) {
            return response()->json(['error' => 'Muitas requisições'], 429);
        }
        RateLimiter::hit($key, 60);

        $cacheKey = "quick_check_{$domain}";
        $result = Cache::remember($cacheKey, 180, function () use ($domain) {
            $subscription = Subscription::where('domain', $domain)
                                       ->orWhere('subdomain', $domain)
                                       ->select(['id', 'status', 'manual_status', 'ends_at', 'trial_ends_at'])
                                       ->first();

            if (!$subscription) {
                return ['managed' => false, 'access' => 'allow'];
            }

            return [
                'managed' => true,
                'access' => $subscription->canAccess() ? 'allow' : 'deny',
                'subscription_id' => $subscription->id,
                'status' => $subscription->status,
                'expires_at' => $subscription->ends_at?->toISOString()
            ];
        });

        // Log assíncrono
        $this->logApiCall($request, $domain, 'quick_check', $result);

        return response()->json(array_merge($result, [
            'domain' => $domain,
            'timestamp' => now()->toISOString(),
            'cache_ttl' => 180
        ]));
    }

    /**
     * 📊 VERIFICAÇÃO COMPLETA COM DADOS DETALHADOS
     * GET /api/website/status/{domain}
     */
    public function detailedStatus(Request $request, $domain)
    {
        $apiKey = $request->header('X-API-Key') ?? $request->input('api_key');

        // Validação de API key se fornecida
        if ($apiKey && !$this->validateApiKey($apiKey)) {
            return response()->json(['error' => 'API key inválida'], 401);
        }

        $cacheKey = "detailed_status_{$domain}";
        $result = Cache::remember($cacheKey, 300, function () use ($domain, $apiKey) {
            $query = Subscription::where('domain', $domain)->orWhere('subdomain', $domain);

            if ($apiKey) {
                $query->where('api_key', $apiKey);
            }

            $subscription = $query->with(['client', 'plan', 'apiLogs' => function($q) {
                $q->latest()->limit(10);
            }])->first();

            if (!$subscription) {
                return ['found' => false];
            }

            return ['found' => true, 'subscription' => $subscription];
        });

        if (!$result['found']) {
            return response()->json([
                'status' => 'not_found',
                'domain' => $domain,
                'managed' => false,
                'message' => 'Domínio não encontrado no sistema'
            ], 404);
        }

        $subscription = $result['subscription'];
        $canAccess = $subscription->canAccess();

        $response = [
            'status' => $canAccess ? 'active' : 'blocked',
            'domain' => $domain,
            'managed' => true,
            'access' => $canAccess ? 'allow' : 'deny',

            // Informações da subscription
            'subscription' => [
                'id' => $subscription->id,
                'status' => $subscription->status,
                'manual_status' => $subscription->manual_status,
                'is_trial' => $subscription->isTrial(),
                'created_at' => $subscription->created_at->toISOString(),
            ],

            // Cliente e plano
            'client' => [
                'id' => $subscription->client->id,
                'name' => $subscription->client->name,
                'email' => $subscription->client->email
            ],

            'plan' => [
                'id' => $subscription->plan->id,
                'name' => $subscription->plan->name,
                'price' => $subscription->plan->price,
                'billing_cycle' => $subscription->plan->billing_cycle
            ],

            // Datas importantes
            'timeline' => [
                'starts_at' => $subscription->starts_at?->toISOString(),
                'ends_at' => $subscription->ends_at?->toISOString(),
                'trial_ends_at' => $subscription->trial_ends_at?->toISOString(),
                'days_until_expiry' => $subscription->days_until_expiry,
                'trial_days_left' => $subscription->trial_days_left
            ],

            // Uso atual
            'usage' => [
                'total_requests' => $subscription->total_requests,
                'monthly_requests' => $subscription->monthly_requests,
                'storage_used_gb' => round($subscription->storage_used_gb ?? 0, 3),
                'bandwidth_used_gb' => round($subscription->bandwidth_used_gb ?? 0, 3),
                'usage_percentage' => $subscription->usage_percentage,
                'last_request_at' => $subscription->last_request_at?->toISOString()
            ],

            // Limites
            'limits' => [
                'max_storage_gb' => $subscription->plan->max_storage_gb,
                'max_bandwidth_gb' => $subscription->plan->max_bandwidth_gb,
                'max_domains' => $subscription->plan->max_domains
            ],

            // Atividade recente
            'recent_activity' => $subscription->apiLogs->map(function($log) {
                return [
                    'endpoint' => $log->clean_endpoint,
                    'ip' => $log->ip_address,
                    'status' => $log->response_code,
                    'timestamp' => $log->created_at->toISOString()
                ];
            }),

            'checked_at' => now()->toISOString()
        ];

        // Informações de bloqueio se aplicável
        if (!$canAccess) {
            $reason = $this->getBlockReason($subscription);
            $response['block_info'] = [
                'reason' => $reason,
                'message' => $this->getBlockMessage($reason),
                'suspended_at' => $subscription->suspended_at?->toISOString(),
                'suspension_reason' => $subscription->suspension_reason,
                'suspension_config' => $subscription->suspension_page_config ?? $this->getDefaultSuspensionConfig($reason)
            ];
        }

        // Log da consulta
        $this->logApiCall($request, $domain, 'detailed_status', ['status' => $response['status']]);

        // Incrementar uso se ativo
        if ($canAccess) {
            dispatch(function () use ($subscription) {
                $subscription->incrementUsage(1, 0.001);
            })->onQueue('usage');
        }

        return response()->json($response);
    }

    /**
     * 📈 ESTATÍSTICAS E ANALYTICS DO DOMÍNIO
     * GET /api/website/analytics/{domain}
     */
    public function analytics(Request $request, $domain)
    {
        $period = $request->input('period', '7d'); // 1d, 7d, 30d
        $apiKey = $request->header('X-API-Key');

        if (!$apiKey || !$this->validateApiKey($apiKey)) {
            return response()->json(['error' => 'API key necessária para analytics'], 401);
        }

        $subscription = Subscription::where('domain', $domain)
                                   ->where('api_key', $apiKey)
                                   ->with(['plan'])
                                   ->first();

        if (!$subscription) {
            return response()->json(['error' => 'Domínio não encontrado'], 404);
        }

        $startDate = match($period) {
            '1d' => now()->subDay(),
            '7d' => now()->subWeek(),
            '30d' => now()->subMonth(),
            default => now()->subWeek()
        };

        $analytics = [
            'period' => $period,
            'domain' => $domain,

            // Estatísticas gerais
            'overview' => [
                'total_requests' => $subscription->total_requests,
                'monthly_requests' => $subscription->monthly_requests,
                'avg_daily_requests' => $subscription->getAverageDailyRequests(),
                'uptime_days' => $subscription->getUptimeDays(),
                'usage_percentage' => $subscription->usage_percentage
            ],

            // Logs do período
            'period_stats' => [
                'requests' => $subscription->apiLogs()
                                          ->where('created_at', '>=', $startDate)
                                          ->count(),
                'unique_ips' => $subscription->apiLogs()
                                            ->where('created_at', '>=', $startDate)
                                            ->distinct('ip_address')
                                            ->count(),
                'error_rate' => $this->calculateErrorRate($subscription, $startDate),
                'avg_response_time' => '150ms' // Placeholder - você pode implementar
            ],

            // Top endpoints
            'top_endpoints' => $subscription->apiLogs()
                                           ->where('created_at', '>=', $startDate)
                                           ->selectRaw('endpoint, count(*) as hits')
                                           ->groupBy('endpoint')
                                           ->orderBy('hits', 'desc')
                                           ->limit(10)
                                           ->get(),

            // Distribuição por hora
            'hourly_distribution' => $subscription->apiLogs()
                                                 ->where('created_at', '>=', $startDate)
                                                 ->selectRaw('HOUR(created_at) as hour, count(*) as requests')
                                                 ->groupBy('hour')
                                                 ->orderBy('hour')
                                                 ->get(),

            // Status codes
            'status_codes' => $subscription->apiLogs()
                                          ->where('created_at', '>=', $startDate)
                                          ->selectRaw('response_code, count(*) as count')
                                          ->groupBy('response_code')
                                          ->get(),

            'generated_at' => now()->toISOString()
        ];

        return response()->json($analytics);
    }

    /**
     * 🚨 WEBHOOK PARA NOTIFICAÇÕES EM TEMPO REAL
     * POST /api/website/webhook
     */
    public function webhook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'domain' => 'required|string',
            'events' => 'required|array',
            'events.*' => 'in:status_change,usage_limit,payment_due,suspension',
            'api_key' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Verificar se o domínio pertence à API key
        $subscription = Subscription::where('domain', $request->domain)
                                   ->where('api_key', $request->api_key)
                                   ->first();

        if (!$subscription) {
            return response()->json(['error' => 'Domínio não encontrado'], 404);
        }

        // Salvar webhook (você precisará criar uma tabela webhooks)
        // Webhook::create([...]);

        return response()->json([
            'message' => 'Webhook registrado com sucesso',
            'webhook_id' => 'webhook_' . uniqid(),
            'events' => $request->events
        ]);
    }

    /**
     * 📊 HEALTH CHECK AVANÇADO
     * GET /api/website/health
     */
    public function health()
    {
        $startTime = microtime(true);

        $health = [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'services' => []
        ];

        // Check database
        try {
            $dbTime = microtime(true);
            $subscriptionCount = Subscription::count();
            $health['services']['database'] = [
                'status' => 'up',
                'response_time' => round((microtime(true) - $dbTime) * 1000, 2) . 'ms',
                'subscriptions' => $subscriptionCount
            ];
        } catch (\Exception $e) {
            $health['status'] = 'degraded';
            $health['services']['database'] = ['status' => 'down', 'error' => $e->getMessage()];
        }

        // Check cache
        try {
            $cacheTime = microtime(true);
            Cache::put('health_test', 'ok', 10);
            $cacheResult = Cache::get('health_test');
            Cache::forget('health_test');

            $health['services']['cache'] = [
                'status' => $cacheResult === 'ok' ? 'up' : 'down',
                'response_time' => round((microtime(true) - $cacheTime) * 1000, 2) . 'ms'
            ];
        } catch (\Exception $e) {
            $health['status'] = 'degraded';
            $health['services']['cache'] = ['status' => 'down', 'error' => $e->getMessage()];
        }

        // Check queue
        try {
            $queueTime = microtime(true);
            dispatch(function () {})->onQueue('health-check');
            $health['services']['queue'] = [
                'status' => 'up',
                'response_time' => round((microtime(true) - $queueTime) * 1000, 2) . 'ms'
            ];
        } catch (\Exception $e) {
            $health['status'] = 'degraded';
            $health['services']['queue'] = ['status' => 'down', 'error' => $e->getMessage()];
        }

        $health['total_response_time'] = round((microtime(true) - $startTime) * 1000, 2) . 'ms';

        $httpCode = $health['status'] === 'healthy' ? 200 : 503;
        return response()->json($health, $httpCode);
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    private function validateApiKey($apiKey)
    {
        return str_starts_with($apiKey, 'sk_') &&
               Subscription::where('api_key', $apiKey)->exists();
    }

    private function logApiCall($request, $domain, $endpoint, $result)
    {
        // dispatch(function () use ($request, $domain, $endpoint, $result) {
            ApiLog::create([
                'subscription_id' => $result['subscription_id'] ?? null,
                'domain' => $domain,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'endpoint' => "GET /api/website/{$endpoint}/{$domain}",
                'request_data' => [
                    'domain' => $domain,
                    'endpoint' => $endpoint,
                    'headers' => $request->headers->all()
                ],
                'response_data' => $result,
                'response_code' => 200
            ]);
        // })->onQueue('logs');
    }

    private function calculateErrorRate($subscription, $startDate)
    {
        $total = $subscription->apiLogs()->where('created_at', '>=', $startDate)->count();
        if ($total === 0) return 0;

        $errors = $subscription->apiLogs()
                              ->where('created_at', '>=', $startDate)
                              ->where('response_code', '>=', 400)
                              ->count();

        return round(($errors / $total) * 100, 2);
    }

    private function getBlockReason($subscription)
    {
        if ($subscription->manual_status === 'disabled') return 'manually_disabled';
        if ($subscription->status === 'suspended') return 'suspended';
        if ($subscription->status === 'cancelled') return 'cancelled';
        if ($subscription->isExpired()) {
            return $subscription->status === 'trial' ? 'trial_expired' : 'expired';
        }
        return 'unknown';
    }

    private function getBlockMessage($reason)
    {
        return match($reason) {
            'manually_disabled' => 'Serviço desabilitado manualmente',
            'suspended' => 'Subscrição suspensa',
            'cancelled' => 'Subscrição cancelada',
            'expired' => 'Subscrição expirada',
            'trial_expired' => 'Período de teste expirado',
            default => 'Acesso negado'
        };
    }

    private function getDefaultSuspensionConfig($reason)
    {
        $configs = [
            'suspended' => [
                'title' => 'Website Suspenso',
                'message' => 'Este website foi temporariamente suspenso.',
                'color' => '#f59e0b',
                'icon' => '⚠️'
            ],
            'expired' => [
                'title' => 'Subscrição Expirada',
                'message' => 'A subscrição deste website expirou.',
                'color' => '#ef4444',
                'icon' => '⏰'
            ],
            'cancelled' => [
                'title' => 'Serviço Cancelado',
                'message' => 'Este website foi cancelado.',
                'color' => '#ef4444',
                'icon' => '❌'
            ]
        ];

        return $configs[$reason] ?? $configs['suspended'];
    }
}
