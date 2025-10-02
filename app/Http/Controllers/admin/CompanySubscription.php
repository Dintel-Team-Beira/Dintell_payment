<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanySubscription as ModelsCompanySubscription;
use App\Models\Plan;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class CompanySubscription extends Controller
{
     public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    /**
     * Lista todas as subscrições
     */
    public function index(Request $request)
    {
        $query = ModelsCompanySubscription::with(['company', 'plan'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('company', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('expiring')) {
            $days = (int) $request->expiring;
            $query->where('ends_at', '<=', now()->addDays($days))
                  ->where('ends_at', '>', now())
                  ->whereIn('status', ['active', 'trialing']);
        }

        $subscriptions = $query->paginate(15);

        // Estatísticas
        $stats = [
            'total' => ModelsCompanySubscription::count(),
            'active' => ModelsCompanySubscription::active()->count(),
            'trialing' => ModelsCompanySubscription::trialing()->count(),
            'suspended' => ModelsCompanySubscription::suspended()->count(),
            'expired' => ModelsCompanySubscription::expired()->count(),
            'revenue_month' => ModelsCompanySubscription::active()
                ->sum('amount'),
            'expiring_7days' => ModelsCompanySubscription::where('ends_at', '<=', now()->addDays(7))
                ->where('ends_at', '>', now())
                ->whereIn('status', ['active', 'trialing'])
                ->count(),
        ];

        $plans = Plan::where('is_active', true)->get();

        return view('admin.subscriptions.index', compact('subscriptions', 'stats', 'plans'));
    }

    /**
     * Mostra detalhes de uma subscrição
     */
    public function show(ModelsCompanySubscription $subscription)
    {
        $subscription->load(['company', 'plan', 'canceledByUser', 'suspendedByUser', 'reactivatedByUser']);

        // Histórico de mudanças
        $history = ModelsCompanySubscription::where('company_id', $subscription->company_id)
            ->where('id', '!=', $subscription->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.subscriptions.show', compact('subscription', 'history'));
    }

    /**
     * Formulário para criar nova subscrição
     */
    public function create(Request $request)
    {
        $companies = Company::orderBy('name')->get();
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        
        $selectedCompany = $request->company_id 
            ? Company::find($request->company_id) 
            : null;

        return view('admin.subscriptions.create', compact('companies', 'plans', 'selectedCompany'));
    }

    /**
     * Criar nova subscrição
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,quarterly,yearly',
            'starts_at' => 'nullable|date',
            'auto_renew' => 'boolean',
            'coupon_code' => 'nullable|string|max:50',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $company = Company::findOrFail($validated['company_id']);
        $plan = Plan::findOrFail($validated['plan_id']);

        // Verificar se já tem subscrição ativa
        $activeSubscription = $company->subscriptions()
            ->whereIn('status', ['active', 'trialing'])
            ->first();

        if ($activeSubscription) {
            return back()->withErrors([
                'company_id' => 'Esta empresa já possui uma subscrição ativa.'
            ])->withInput();
        }

        try {
            $subscription = $this->subscriptionService->createSubscription(
                company: $company,
                plan: $plan,
                options: [
                    'billing_cycle' => $validated['billing_cycle'],
                    'starts_at' => $validated['starts_at'] ?? now(),
                    'auto_renew' => $validated['auto_renew'] ?? true,
                    'coupon_code' => $validated['coupon_code'] ?? null,
                    'discount_amount' => $validated['discount_amount'] ?? null,
                    'discount_percentage' => $validated['discount_percentage'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]
            );

            return redirect()
                ->route('admin.subscriptions.show', $subscription)
                ->with('success', 'Subscrição criada com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao criar subscrição: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Cancelar subscrição
     */
    public function cancel(Request $request, ModelsCompanySubscription $subscription)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
            'immediate' => 'boolean',
        ]);

        try {
            $this->subscriptionService->cancelSubscription(
                subscription: $subscription,
                reason: $validated['reason'] ?? null,
                immediate: $validated['immediate'] ?? false
            );

            return back()->with('success', 'Subscrição cancelada com sucesso!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao cancelar: ' . $e->getMessage()]);
        }
    }

    /**
     * Suspender subscrição
     */
    public function suspend(Request $request, ModelsCompanySubscription $subscription)
    {
        $validated = $request->validate([
            'reason' => 'required|in:payment_failed,terms_violation,fraud_suspected,excessive_usage,manual_admin,chargeback,abuse_detected',
            'message' => 'nullable|string|max:500',
            'details' => 'nullable|string|max:1000',
            'can_appeal' => 'boolean',
        ]);

        try {
            $this->subscriptionService->suspendSubscription(
                subscription: $subscription,
                reason: $validated['reason'],
                message: $validated['message'] ?? null,
                details: $validated['details'] ?? null,
                canAppeal: $validated['can_appeal'] ?? true
            );

            return back()->with('success', 'Subscrição suspensa com sucesso!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao suspender: ' . $e->getMessage()]);
        }
    }

    /**
     * Reativar subscrição
     */
    public function reactivate(Request $request, ModelsCompanySubscription $subscription)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $this->subscriptionService->reactivateSubscription(
                subscription: $subscription,
                notes: $validated['notes'] ?? null
            );

            return back()->with('success', 'Subscrição reativada com sucesso!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao reativar: ' . $e->getMessage()]);
        }
    }

    /**
     * Renovar subscrição manualmente
     */
    public function renew(ModelsCompanySubscription $subscription)
    {
        try {
            $this->subscriptionService->renewSubscription($subscription);

            return back()->with('success', 'Subscrição renovada com sucesso!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao renovar: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle auto-renew
     */
    public function toggleAutoRenew(ModelsCompanySubscription $subscription)
    {
        try {
            if ($subscription->auto_renew) {
                $subscription->disableAutoRenew();
                $message = 'Renovação automática desativada';
            } else {
                $subscription->enableAutoRenew();
                $message = 'Renovação automática ativada';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'auto_renew' => $subscription->auto_renew,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar renovação automática',
            ], 500);
        }
    }

    /**
     * Processar expirações (chamado por cron/command)
     */
    public function processExpirations()
    {
        try {
            $results = $this->subscriptionService->processExpiringSubscriptions();

            return response()->json([
                'success' => true,
                'results' => $results,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
