<?php
// app/Http/Controllers/SuspensionPageController.php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\ApiLog;
use Illuminate\Http\Request;

class SuspensionPageController extends Controller
{
    public function show(Request $request, $domain)
    {
        $reason = $request->get('reason', 'unknown');

        $subscription = Subscription::where('domain', $domain)
                                  ->orWhere('subdomain', $domain)
                                  ->with(['client', 'plan'])
                                  ->first();

        // Log da visualização da página de suspensão
        if ($subscription) {
            ApiLog::create([
                'subscription_id' => $subscription->id,
                'domain' => $domain,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'endpoint' => 'suspension_page',
                'request_data' => ['reason' => $reason],
                'response_data' => ['action' => 'suspension_page_viewed'],
                'response_code' => 200
            ]);
        }

        $config = $subscription?->suspension_page_config ?? $this->getDefaultConfig($reason);

        // Personalizar configuração baseada no motivo
        $config = $this->customizeConfigByReason($config, $reason, $subscription);

        return view('suspension.page', compact('domain', 'config', 'subscription', 'reason'));
    }

    private function getDefaultConfig($reason)
    {
        $configs = [
            'manually_disabled' => [
                'title' => 'Serviço Temporariamente Desabilitado',
                'message' => 'Este website foi temporariamente desabilitado para manutenção.',
                'icon' => 'tools',
                'color_primary' => '#F59E0B',
                'color_secondary' => '#FEF3C7',
                'show_contact' => true,
                'show_details' => false
            ],
            'suspended' => [
                'title' => 'Conta Suspensa',
                'message' => 'Este website foi suspenso devido a violação dos termos de serviço.',
                'icon' => 'warning',
                'color_primary' => '#DC2626',
                'color_secondary' => '#FEE2E2',
                'show_contact' => true,
                'show_details' => true
            ],
            'expired' => [
                'title' => 'Subscrição Expirada',
                'message' => 'A subscrição deste website expirou.',
                'icon' => 'clock',
                'color_primary' => '#D97706',
                'color_secondary' => '#FED7AA',
                'show_contact' => true,
                'show_details' => true,
                'show_renewal' => true
            ],
            'trial_expired' => [
                'title' => 'Período de Teste Expirado',
                'message' => 'O período de teste gratuito expirou.',
                'icon' => 'star',
                'color_primary' => '#3B82F6',
                'color_secondary' => '#DBEAFE',
                'show_contact' => true,
                'show_details' => true,
                'show_upgrade' => true
            ],
            'cancelled' => [
                'title' => 'Serviço Cancelado',
                'message' => 'A subscrição deste website foi cancelada.',
                'icon' => 'x-circle',
                'color_primary' => '#6B7280',
                'color_secondary' => '#F3F4F6',
                'show_contact' => true,
                'show_details' => false
            ]
        ];

        $defaultConfig = $configs[$reason] ?? $configs['suspended'];

        return array_merge($defaultConfig, [
            'support_email' => config('app.support_email', 'support@submanager.com'),
            'support_phone' => config('app.support_phone', '+258 84 123 4567'),
            'support_whatsapp' => config('app.support_whatsapp', '+258841234567'),
            'company_name' => config('app.name', 'SubManager'),
            'company_url' => config('app.url'),
            'show_powered_by' => true
        ]);
    }

    private function customizeConfigByReason($config, $reason, $subscription)
    {
        if (!$subscription) {
            return $config;
        }

        switch ($reason) {
            case 'expired':
                $config['renewal_url'] = route('subscription.renew', $subscription->id);
                $config['renewal_amount'] = $subscription->plan->price;
                break;

            case 'trial_expired':
                $config['upgrade_plans'] = $this->getAvailablePlans();
                break;

            case 'suspended':
                if ($subscription->suspension_reason) {
                    $config['suspension_details'] = $subscription->suspension_reason;
                }
                break;
        }

        return $config;
    }

    private function getAvailablePlans()
    {
        return \App\Models\SubscriptionPlan::active()
                                         ->orderBy('price')
                                         ->get(['id', 'name', 'price', 'features']);
    }
}