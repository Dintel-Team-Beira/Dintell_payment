<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PlansController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Plan::query();

        // Filtros
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('billing_cycle')) {
            $query->forBillingCycle($request->billing_cycle);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $plans = $query->ordered()->paginate(10)->withQueryString();

        $stats = [
            'total_plans' => Plan::count(),
            'active_plans' => Plan::active()->count(),
            'popular_plans' => Plan::popular()->count(),
            'free_plans' => Plan::where('price', 0)->count(),
        ];

        return view('admin.plans.index', compact('plans', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $plan = new Plan();

        // Definir valores padrão
        $plan->currency = 'MZN';
        $plan->billing_cycle = 'monthly';
        $plan->is_active = true;
        $plan->has_trial = false;
        $plan->trial_days = 0;
        $plan->color = '#3B82F6';
        $plan->features = [];
        $plan->limitations = [];

        $availableFeatures = $this->getAvailableFeatures();
        $billingCycles = $this->getBillingCycles();
        $currencies = $this->getCurrencies();
        $colors = $this->getColors();
        $icons = $this->getIcons();

        return view('admin.plans.create', compact('plan', 'availableFeatures', 'billingCycles', 'currencies', 'colors', 'icons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validatePlan($request);

        // Gerar slug se não fornecido
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Garantir slug único
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Plan::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $plan = Plan::create($validated);

        return redirect()->route('admin.plans.show', $plan)
                        ->with('success', 'Plano criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Plan $plan)
    {
        $plan->load(['companies', 'subscriptions']);

        $stats = [
            'active_subscriptions' => $plan->subscriptions()->where('status', 'active')->count(),
            'total_revenue' => $plan->subscriptions()->sum('amount'),
            'companies_count' => $plan->companies()->count(),
            'average_usage' => $this->calculateAverageUsage($plan),
        ];

        return view('admin.plans.show', compact('plan', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Plan $plan)
    {
        $availableFeatures = $this->getAvailableFeatures();
        $billingCycles = $this->getBillingCycles();
        $currencies = $this->getCurrencies();
        $colors = $this->getColors();
        $icons = $this->getIcons();

        return view('admin.plans.edit', compact('plan', 'availableFeatures', 'billingCycles', 'currencies', 'colors', 'icons'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plan $plan)
    {
        $validated = $this->validatePlan($request, $plan->id);

        // Verificar se o slug mudou e garantir unicidade
        if ($validated['slug'] !== $plan->slug) {
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Plan::where('slug', $validated['slug'])->where('id', '!=', $plan->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $plan->update($validated);

        return redirect()->route('admin.plans.show', $plan)
                        ->with('success', 'Plano atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plan $plan)
    {
        // Verificar se o plano tem subscrições ativas
        $activeSubscriptions = $plan->subscriptions()->where('status', 'active')->count();

        if ($activeSubscriptions > 0) {
            return redirect()->route('admin.plans.index')
                            ->with('error', 'Não é possível excluir um plano com subscrições ativas.');
        }

        $plan->delete();

        return redirect()->route('admin.plans.index')
                        ->with('success', 'Plano excluído com sucesso!');
    }

    /**
     * Toggle plan status
     */
    public function toggleStatus(Plan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);

        $status = $plan->is_active ? 'ativado' : 'desativado';

        return response()->json([
            'success' => true,
            'message' => "Plano {$status} com sucesso!",
            'is_active' => $plan->is_active
        ]);
    }

    /**
     * Toggle popular status
     */
    public function togglePopular(Plan $plan)
    {
        // Se estamos marcando como popular, desmarcar outros
        if (!$plan->is_popular) {
            Plan::where('is_popular', true)->update(['is_popular' => false]);
        }

        $plan->update(['is_popular' => !$plan->is_popular]);

        $status = $plan->is_popular ? 'marcado como popular' : 'desmarcado como popular';

        return response()->json([
            'success' => true,
            'message' => "Plano {$status}!",
            'is_popular' => $plan->is_popular
        ]);
    }

    /**
     * Duplicate a plan
     */
    public function duplicate(Plan $plan)
    {
        $newPlan = $plan->replicate();
        $newPlan->name = $plan->name . ' (Cópia)';
        $newPlan->slug = Str::slug($newPlan->name);
        $newPlan->is_popular = false;
        $newPlan->is_active = false;

        // Garantir slug único
        $originalSlug = $newPlan->slug;
        $counter = 1;
        while (Plan::where('slug', $newPlan->slug)->exists()) {
            $newPlan->slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $newPlan->save();

        return redirect()->route('admin.plans.edit', $newPlan)
                        ->with('success', 'Plano duplicado com sucesso! Edite conforme necessário.');
    }

    /**
     * Reorder plans
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'plans' => 'required|array',
            'plans.*.id' => 'required|exists:plans,id',
            'plans.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->plans as $planData) {
            Plan::where('id', $planData['id'])->update(['sort_order' => $planData['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'Ordem dos planos atualizada!']);
    }

    /**
     * Validation helper
     */
    private function validatePlan(Request $request, $planId = null)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                $planId ? Rule::unique('plans')->ignore($planId) : Rule::unique('plans')
            ],
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0|max:999999.99',
            'currency' => 'required|string|size:3',
            'billing_cycle' => 'required|in:monthly,quarterly,yearly',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'max_users' => 'nullable|integer|min:1',
            'max_companies' => 'nullable|integer|min:1',
            'max_invoices_per_month' => 'nullable|integer|min:1',
            'max_clients' => 'nullable|integer|min:1',
            'max_products' => 'nullable|integer|min:1',
            'max_storage_mb' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'limitations' => 'nullable|array',
            'trial_days' => 'nullable|integer|min:0|max:365',
            'has_trial' => 'boolean',
            'stripe_price_id' => 'nullable|string|max:255',
            'color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
            'icon' => 'nullable|string|max:50'
        ]);
    }

    /**
     * Helper methods
     */
    private function getAvailableFeatures()
    {
        return [
            'Faturação básica',
            'Gestão de clientes',
            'Gestão de produtos',
            'Relatórios básicos',
            'Relatórios avançados',
            'Múltiplas empresas',
            'Backup automático',
            'API access',
            'Customizações avançadas',
            'Integração personalizada',
            'Suporte por email',
            'Suporte prioritário',
            'Suporte 24/7',
            'Gerente de conta dedicado',
            'Templates personalizados',
            'Exportação de dados',
            'Notificações automáticas',
            'Pagamentos online',
            'Controle de estoque',
            'Dashboard avançado'
        ];
    }

    private function getBillingCycles()
    {
        return [
            'monthly' => 'Mensal',
            'quarterly' => 'Trimestral',
            'yearly' => 'Anual'
        ];
    }

    private function getCurrencies()
    {
        return [
            'MZN' => 'Metical Moçambicano (MZN)',
            'USD' => 'Dólar Americano (USD)',
            'EUR' => 'Euro (EUR)',
            'ZAR' => 'Rand Sul-Africano (ZAR)'
        ];
    }

    private function getColors()
    {
        return [
            '#6B7280' => 'Cinza',
            '#3B82F6' => 'Azul',
            '#10B981' => 'Verde',
            '#F59E0B' => 'Amarelo',
            '#EF4444' => 'Vermelho',
            '#8B5CF6' => 'Roxo',
            '#F97316' => 'Laranja',
            '#06B6D4' => 'Ciano',
            '#84CC16' => 'Lima',
            '#EC4899' => 'Rosa'
        ];
    }

    private function getIcons()
    {
        return [
            'rocket' => 'Foguete',
            'star' => 'Estrela',
            'building' => 'Edifício',
            'crown' => 'Coroa',
            'shield' => 'Escudo',
            'diamond' => 'Diamante',
            'trophy' => 'Troféu',
            'heart' => 'Coração',
            'lightning' => 'Raio',
            'fire' => 'Fogo'
        ];
    }

    private function calculateAverageUsage(Plan $plan)
    {
        // Implementar cálculo de uso médio
        return [
            'users' => 0,
            'invoices' => 0,
            'storage' => 0
        ];
    }
}
