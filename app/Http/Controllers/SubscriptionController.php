<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Notifications\SubscriptionActivatedNotification;
use App\Notifications\SubscriptionRenewedNotification;
use App\Notifications\SubscriptionExpiredNotification;
use App\Notifications\SubscriptionExpiringNotification;
use App\Notifications\SubscriptionCancelledNotification;
use App\Notifications\SubscriptionSuspendedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['client', 'plan']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('domain', 'like', "%{$search}%")
                    ->orWhere('subdomain', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Plan filter
        if ($request->filled('plan')) {
            $query->where('subscription_plan_id', $request->plan);
        }

        $subscriptions = $query->latest()->paginate(15)->withQueryString();
        $plans = SubscriptionPlan::active()->get();

        return view('subscriptions.index', compact('subscriptions', 'plans'));
    }


    // Controller simplificado usando o método do modelo
    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'plan', 'apiLogs' => function ($query) {
            $query->latest()->limit(50);
        }]);

        // Calcular estatísticas diretamente no controller (solução rápida)
        $stats = [
            'total_requests' => number_format($subscription->total_requests ?? 0),
            'days_active' => $subscription->starts_at
                ? max(1, (int) now()->diffInDays($subscription->starts_at))
                : 0,
            'payment_failures' => $subscription->payment_failures ?? 0,
            'monthly_requests' => number_format($subscription->monthly_requests ?? 0),
            'storage_used' => round($subscription->storage_used_gb ?? 0, 2),
            'bandwidth_used' => round($subscription->bandwidth_used_gb ?? 0, 2),
            'usage_percentage' => round($subscription->usage_percentage ?? 0, 1),
            'total_revenue' => $subscription->total_revenue
                ? 'MT ' . number_format($subscription->total_revenue, 2)
                : 'MT 0.00',
            'last_payment' => $subscription->last_payment_date
                ? $subscription->last_payment_date->format('d/m/Y')
                : 'Nunca',
            'next_payment' => $subscription->next_payment_due
                ? $subscription->next_payment_due->format('d/m/Y')
                : 'N/A',
            'days_until_expiry' => $subscription->days_until_expiry ?? 'N/A',
            'trial_days_left' => $subscription->trial_days_left ?? 0,
            'avg_daily_requests' => $subscription->starts_at && $subscription->total_requests > 0
                ? round($subscription->total_requests / max(1, now()->diffInDays($subscription->starts_at)), 1)
                : 0
        ];

        return view('subscriptions.show', compact('subscription', 'stats'));
    }

    // Método adicional para API ou AJAX
    public function getStats(Subscription $subscription)
    {
        return response()->json([
            'basic_stats' => [
                'days_active' => $subscription->starts_at
                    ? max(1, (int) now()->diffInDays($subscription->starts_at))
                    : 0,
                'total_requests' => $subscription->total_requests ?? 0,
                'is_active' => $subscription->isActive(),
                'can_access' => $subscription->canAccess()
            ]
        ]);
    }

    public function create()
    {
        $plans = SubscriptionPlan::active()->get();
        $clients = Client::where('status', 'active')
            ->withCount('subscriptions')
            ->get();
        return view('subscriptions.create', compact('plans', 'clients'));
    }

    public function store(Request $request)
    {
        // return $request->all();

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'client_id' => 'required|exists:clients,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'domain' => 'required|string|max:255|unique:subscriptions,domain',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'amount_paid' => 'required|numeric|min:0'
        ]);
        $validated['company_id'] = auth()->user()->company->id;
        $subscription = Subscription::create($validated + [
            'status' => 'active',
            'last_payment_date' => now()
        ]);
        $subscription->client->notify(new SubscriptionActivatedNotification($subscription));

        return redirect()->route('subscriptions.show', $subscription)
            ->with('success', 'Subscrição criada com sucesso!');
    }

    public function edit(Subscription $subscription)
    {
        $plans = SubscriptionPlan::active()->get();
        $clients = Client::where('status', 'active')
            ->withCount('subscriptions')
            ->get();
        return view('subscriptions.edit', compact('subscription', 'plans', 'clients'));
    }

    // Método update atualizado
    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'domain' => 'required|string|max:255|unique:subscriptions,domain,' . $subscription->id,
            'status' => 'required|in:active,inactive,suspended,cancelled',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'suspension_reason' => 'nullable|string|max:500',
            'amount_paid' => 'required|numeric|min:0'
        ]);
        $validated['company_id'] = auth()->user()->company->id;
        $oldStatus = $subscription->status;

        // Lógica para suspensão
        if ($validated['status'] === 'suspended' && $subscription->status !== 'suspended') {
            $validated['suspended_at'] = now();

            // Enviar notificação de suspensão
            $subscription->update($validated);
            $subscription->client->notify(new SubscriptionSuspendedNotification($subscription));
        } elseif ($validated['status'] !== 'suspended') {
            $validated['suspended_at'] = null;
            $validated['suspension_reason'] = null;
            $subscription->update($validated);

            // Se saiu de suspensa para ativa, enviar notificação de ativação
            if ($oldStatus === 'suspended' && $validated['status'] === 'active') {
                $subscription->client->notify(new SubscriptionActivatedNotification($subscription));
            }
        } else {
            $subscription->update($validated);
        }

        return redirect()->route('subscriptions.show', $subscription)
            ->with('success', 'Subscrição atualizada com sucesso!');
    }

    // Método de suspensão atualizado
    public function suspend(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'suspension_config' => 'nullable|array',
            'send_notification' => 'nullable|boolean'
        ]);

        $subscription->update([
            'status' => 'suspended',
            'suspended_at' => now(),
            'suspension_reason' => $validated['reason'],
            'suspension_page_config' => $validated['suspension_config'] ?? null
        ]);

        // Enviar notificação detalhada se solicitado
        if ($validated['send_notification'] ?? true) {
            try {
                $subscription->client->notify(new SubscriptionSuspendedNotification($subscription, true));
                $notificationMessage = ' Notificação enviada por email.';
            } catch (\Exception $e) {
                \Log::error('Erro ao enviar notificação de suspensão: ' . $e->getMessage());
                $notificationMessage = ' Erro ao enviar notificação por email.';
            }
        } else {
            $notificationMessage = '';
        }

        return redirect()->route('subscriptions.show', $subscription)
                        ->with('success', 'Subscrição suspensa com sucesso!' . $notificationMessage);
    }

    public function regenerateApiKey(Subscription $subscription)
    {
        $subscription->update([
            'api_key' => 'sk_' . Str::random(40)
        ]);

        return redirect()->route('subscriptions.show', $subscription)
            ->with('success', 'Chave API regenerada com sucesso!');
    }

    public function activate(Request $request, Subscription $subscription)
    {
        $subscription->activate();

        return redirect()->route('subscriptions.show', $subscription)
            ->with('success', 'Subscrição ativada com sucesso!');
    }

    /**
     * Cancela uma assinatura
     */
    // Método de cancelamento atualizado
    public function cancel(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'immediate' => 'nullable|boolean'
        ]);

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['reason'],
            'ends_at' => $validated['immediate'] ? now() : $subscription->ends_at
        ]);

        // Enviar notificação de cancelamento
        $subscription->client->notify(new SubscriptionCancelledNotification($subscription));

        return redirect()->route('subscriptions.show', $subscription)
            ->with('success', 'Subscrição cancelada com sucesso!');
    }


    // Método de renovação atualizado
    public function renew(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:255',
            'payment_reference' => 'nullable|string|max:255'
        ]);

        try {
            $subscription->renew(
                $validated['amount_paid'],
                $validated['payment_method'],
                $validated['payment_reference'] ?? null
            );

            // Enviar notificação de renovação
            $subscription->client->notify(new SubscriptionRenewedNotification($subscription, $validated['amount_paid']));

            return redirect()->route('subscriptions.show', $subscription)
                ->with('success', 'Subscrição renovada com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao renovar subscrição: ' . $e->getMessage()]);
        }
    }


    // Método para marcar como expirada
    public function markAsExpired(Subscription $subscription)
    {
        $subscription->update(['status' => 'expired']);

        // Enviar notificação de expiração
        $subscription->client->notify(new SubscriptionExpiredNotification($subscription));

        return redirect()->route('subscriptions.show', $subscription)
            ->with('success', 'Subscrição marcada como expirada!');
    }

    // Método para avisar sobre expiração próxima
    public function sendExpirationWarning(Subscription $subscription, $daysLeft = 7)
    {
        $subscription->client->notify(new SubscriptionExpiringNotification($subscription, $daysLeft));

        return response()->json(['message' => 'Aviso de expiração enviado!']);
    }

    /**
     * Método para debug da renovação (pode ser removido em produção)
     */
    public function debugRenewal(Subscription $subscription)
    {
        $debugInfo = $subscription->debugRenewal();

        return response()->json([
            'subscription' => [
                'id' => $subscription->id,
                'domain' => $subscription->domain,
                'current_status' => $subscription->status,
                'manual_status' => $subscription->manual_status
            ],
            'plan' => [
                'id' => $subscription->plan->id ?? null,
                'name' => $subscription->plan->name ?? null,
                'price' => $subscription->plan->price ?? null,
                'billing_cycle_days' => $subscription->plan->billing_cycle_days ?? null
            ],
            'debug' => $debugInfo,
            'methods_check' => [
                'isActive' => $subscription->isActive(),
                'isExpired' => $subscription->isExpired(),
                'canAccess' => $subscription->canAccess(),
                'days_until_expiry' => $subscription->days_until_expiry
            ]
        ]);
    }

    /**
     * Alterna o status manual da assinatura
     */
    public function toggleManualStatus(Request $request, Subscription $subscription)
    {
        $newStatus = $subscription->manual_status === 'enabled' ? 'disabled' : 'enabled';

        $subscription->update([
            'manual_status' => $newStatus
        ]);

        $statusMessage = $newStatus === 'enabled' ? 'habilitado' : 'desabilitado';

        return redirect()->route('subscriptions.show', $subscription)
            ->with('success', "Status manual da subscrição {$statusMessage} com sucesso!");
    }

    /**
     * Buscar subscrições (para API/AJAX)
     */
    public function search(Request $request)
    {
        $query = Subscription::with(['client', 'plan']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('domain', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $subscriptions = $query->limit(10)->get()->map(function ($subscription) {
            return [
                'id' => $subscription->id,
                'domain' => $subscription->domain,
                'client_name' => $subscription->client->name,
                'plan_name' => $subscription->plan->name,
                'status' => $subscription->status,
                'can_access' => $subscription->canAccess()
            ];
        });

        return response()->json($subscriptions);
    }



    // NEW METHODS YAM

    /**
     * Mostrar página de bloqueio
     */
    public function blocked(Request $request)
    {
        // Se não estiver autenticado, redireciona para login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $company = $user->company;

        // Se não tem company
        if (!$company) {
            return view('subscriptions.blocked', [
                'reason' => 'no_company',
                'title' => 'Empresa Não Encontrada',
                'message' => 'Você precisa estar associado a uma empresa para acessar o sistema.',
                'company' => null,
                'user' => $user,
            ]);
        }

        // Pegar o motivo da URL (passado pela middleware)
        $reason = $request->get('reason', $this->detectBlockReason($company));

        // Montar dados baseados no motivo
        $blockData = $this->getBlockData($reason, $company);

        return view('subscription.blocked', [
            'reason' => $reason,
            'title' => $blockData['title'],
            'message' => $blockData['message'],
            'actionText' => $blockData['actionText'],
            'actionRoute' => $blockData['actionRoute'],
            'company' => $company,
            'plan' => $company->plan,
            'user' => $user,
        ]);
    }

    /**
     * Detectar o motivo do bloqueio analisando a company
     */
    protected function detectBlockReason(Company $company): string
    {
        // Status administrativo
        if ($company->status === Company::STATUS_SUSPENDED) {
            return 'suspended';
        }

        if ($company->status === Company::STATUS_INACTIVE) {
            return 'inactive';
        }

        if ($company->status === Company::STATUS_PENDING) {
            return 'pending';
        }

        // Subscrição
        if ($company->subscription_status === Company::SUBSCRIPTION_STATUS_CANCELLED) {
            return 'cancelled';
        }

        if ($company->subscription_status === Company::SUBSCRIPTION_STATUS_EXPIRED) {
            return 'expired';
        }

        if ($company->subscription_status === Company::SUBSCRIPTION_STATUS_SUSPENDED) {
            return 'subscription_suspended';
        }

        // Trial expirado
        if ($company->subscription_type === Company::SUBSCRIPTION_TYPE_TRIAL) {
            if ($company->trial_ends_at && $company->trial_ends_at->isPast()) {
                return 'trial_expired';
            }
        }

        return 'unknown';
    }

    /**
     * Obter dados de bloqueio baseado no motivo
     */
    protected function getBlockData(string $reason, Company $company): array
    {
        return match($reason) {
            'suspended' => [
                'title' => 'Conta Suspensa',
                'message' => $company->suspension_reason ?? 'Sua conta foi suspensa administrativamente. Entre em contato com o suporte para mais informações.',
                'actionText' => 'Contatar Suporte',
                'actionRoute' => 'support.contact',
            ],
            
            'inactive' => [
                'title' => 'Conta Inativa',
                'message' => 'Sua conta está inativa. Entre em contato com o suporte para reativação.',
                'actionText' => 'Contatar Suporte',
                'actionRoute' => 'support.contact',
            ],
            
            'pending' => [
                'title' => 'Conta Pendente',
                'message' => 'Sua conta está em análise. Aguarde a aprovação ou entre em contato com o suporte.',
                'actionText' => 'Contatar Suporte',
                'actionRoute' => 'support.contact',
            ],
            
            'cancelled' => [
                'title' => 'Subscrição Cancelada',
                'message' => 'Sua subscrição foi cancelada. Escolha um novo plano para continuar usando o sistema.',
                'actionText' => 'Ver Planos',
                'actionRoute' => 'billing.plans',
            ],
            
            'expired' => [
                'title' => 'Subscrição Expirada',
                'message' => 'Sua subscrição expirou. Renove agora para continuar aproveitando todos os recursos do sistema.',
                'actionText' => 'Renovar Agora',
                'actionRoute' => 'billing.plans',
            ],
            
            'subscription_suspended' => [
                'title' => 'Subscrição Suspensa',
                'message' => 'Sua subscrição foi suspensa devido a pagamento pendente. Regularize para continuar.',
                'actionText' => 'Regularizar Pagamento',
                'actionRoute' => 'billing.payment',
            ],
            
            'trial_expired' => [
                'title' => 'Período de Teste Expirado',
                'message' => sprintf(
                    'Seu período de teste de %s dias expirou. Escolha um plano para continuar com acesso completo.',
                    $company->trial_ends_at ? now()->diffInDays($company->trial_ends_at) : 14
                ),
                'actionText' => 'Ver Planos',
                'actionRoute' => 'billing.plans',
            ],
            
            'no_company' => [
                'title' => 'Empresa Não Encontrada',
                'message' => 'Você precisa estar associado a uma empresa para acessar o sistema.',
                'actionText' => 'Contatar Suporte',
                'actionRoute' => 'support.contact',
            ],
            
            default => [
                'title' => 'Acesso Bloqueado',
                'message' => 'Sua conta está temporariamente bloqueada. Entre em contato com o suporte.',
                'actionText' => 'Contatar Suporte',
                'actionRoute' => 'support.contact',
            ],
        };
    }
}
