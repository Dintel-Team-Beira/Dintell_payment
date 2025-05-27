<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
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
            $query->where(function($q) use ($search) {
                $q->where('domain', 'like', "%{$search}%")
                  ->orWhere('subdomain', 'like', "%{$search}%")
                  ->orWhereHas('client', function($clientQuery) use ($search) {
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
        $subscription->load(['user', 'plan', 'apiLogs' => function($query) {
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
        return view('subscriptions.create', compact('plans','clients'));
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

        $subscription = Subscription::create($validated + [
            'status' => 'active',
            'last_payment_date' => now()
        ]);

        return redirect()->route('subscriptions.show', $subscription)
                        ->with('success', 'Subscrição criada com sucesso!');
    }

    public function edit(Subscription $subscription)
    {
        $plans = SubscriptionPlan::active()->get();
        $clients = Client::where('status', 'active')
        ->withCount('subscriptions')
        ->get();
        return view('subscriptions.edit', compact('subscription', 'plans','clients'));
    }

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

        if ($validated['status'] === 'suspended' && $subscription->status !== 'suspended') {
            $validated['suspended_at'] = now();
        } elseif ($validated['status'] !== 'suspended') {
            $validated['suspended_at'] = null;
            $validated['suspension_reason'] = null;
        }

        $subscription->update($validated);

        return redirect()->route('subscriptions.show', $subscription)
                        ->with('success', 'Subscrição atualizada com sucesso!');
    }

    public function suspend(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'suspension_config' => 'nullable|array'
        ]);

        $subscription->update([
            'status' => 'suspended',
            'suspended_at' => now(),
            'suspension_reason' => $validated['reason'],
            'suspension_page_config' => $validated['suspension_config'] ?? null
        ]);

        return redirect()->route('subscriptions.show', $subscription)
                        ->with('success', 'Subscrição suspensa com sucesso!');
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

        return redirect()->route('subscriptions.show', $subscription)
                        ->with('success', 'Subscrição cancelada com sucesso!');
    }

    public function renew(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:255',
            'payment_reference' => 'nullable|string|max:255'
        ]);

        // Debug antes da renovação
        \Log::info('Before renewal', [
            'subscription_id' => $subscription->id,
            'current_ends_at' => $subscription->ends_at,
            'current_status' => $subscription->status,
            'debug_info' => $subscription->debugRenewal()
        ]);

        try {
            // Processar renovação
            $subscription->renew(
                $validated['amount_paid'],
                $validated['payment_method'],
                $validated['payment_reference'] ?? null
            );

            // Recarregar dados do banco
            $subscription->refresh();

            // Debug após renovação
            \Log::info('After renewal', [
                'subscription_id' => $subscription->id,
                'new_ends_at' => $subscription->ends_at,
                'new_status' => $subscription->status,
                'is_active' => $subscription->isActive(),
                'can_access' => $subscription->canAccess()
            ]);

            return redirect()->route('subscriptions.show', $subscription)
                            ->with('success', 'Subscrição renovada com sucesso! Nova data de expiração: ' .
                                   $subscription->ends_at->format('d/m/Y H:i'));

        } catch (\Exception $e) {
            \Log::error('Renewal failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Erro ao renovar subscrição: ' . $e->getMessage()]);
        }
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
            $query->where(function($q) use ($search) {
                $q->where('domain', 'like', "%{$search}%")
                  ->orWhereHas('client', function($clientQuery) use ($search) {
                      $clientQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $subscriptions = $query->limit(10)->get()->map(function($subscription) {
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
}