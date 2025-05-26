<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with(['user', 'plan'])
                                   ->latest()
                                   ->paginate(15);

                                   $plans = SubscriptionPlan::active()->get();
        return view('subscriptions.index', compact('subscriptions','plans'));
    }

    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'plan', 'apiLogs' => function($query) {
            $query->latest()->limit(50);
        }]);

        // Calcular estatísticas
        $stats = [
            'total_requests' => $subscription->total_requests,
            'days_active' => $subscription->starts_at ? now()->diffInDays($subscription->starts_at) : 0,
            'payment_failures' => $subscription->payment_failures,
            // Adicione outras estatísticas conforme necessário
        ];

        return view('subscriptions.show', compact('subscription', 'stats'));
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
        return view('subscriptions.edit', compact('subscription', 'plans'));
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

    /**
     * Renova uma assinatura
     */
    public function renew(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:255'
        ]);

        $subscription->renew($validated['amount_paid'], $validated['payment_method']);

        return redirect()->route('subscriptions.show', $subscription)
                        ->with('success', 'Subscrição renovada com sucesso!');
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
}