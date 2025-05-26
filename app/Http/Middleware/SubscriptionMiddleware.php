<?php
// app/Http/Middleware/SubscriptionMiddleware.php

namespace App\Http\Middleware;

use App\Models\Subscription;
use Closure;
use Illuminate\Http\Request;

class SubscriptionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key') ?? $request->input('api_key');
        $domain = $request->header('X-Domain') ?? $request->input('domain');

        if (!$apiKey || !$domain) {
            return response()->json([
                'error' => 'API Key e Domain são obrigatórios',
                'code' => 'MISSING_CREDENTIALS'
            ], 401);
        }

        $subscription = Subscription::where('api_key', $apiKey)
                                  ->where('domain', $domain)
                                  ->first();

        if (!$subscription) {
            return response()->json([
                'error' => 'Subscrição não encontrada',
                'code' => 'SUBSCRIPTION_NOT_FOUND'
            ], 404);
        }

        if (!$subscription->isActive()) {
            return response()->json([
                'error' => 'Subscrição inativa',
                'code' => 'SUBSCRIPTION_INACTIVE',
                'suspension_page_url' => route('suspension.page', ['domain' => $domain])
            ], 403);
        }

        // Adicionar informações da subscrição ao request
        $request->merge(['subscription' => $subscription]);

        return $next($request);
    }
}