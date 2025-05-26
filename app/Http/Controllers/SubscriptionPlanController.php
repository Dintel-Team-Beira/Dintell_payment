<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')
            // ->orderBy('is_featured', 'desc')
            ->orderBy('price')
            ->get();

        return view('plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('plans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,quarterly,yearly,lifetime',
            'billing_cycle_days' => 'required|integer|min:1',
            'max_domains' => 'required|integer|min:1',
            'max_storage_gb' => 'required|integer|min:1',
            'max_bandwidth_gb' => 'required|integer|min:1',
            'trial_days' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'color_theme' => 'nullable|string|max:7'
        ]);

        // Filter out empty features
        if (isset($validated['features'])) {
            $validated['features'] = array_filter($validated['features'], function($feature) {
                return !empty(trim($feature));
            });
        }

        // Set default values
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);
        $validated['setup_fee'] = $validated['setup_fee'] ?? 0;
        $validated['trial_days'] = $validated['trial_days'] ?? 0;

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        SubscriptionPlan::create($validated);

        return redirect()->route('plans.index')
            ->with('success', 'Plano criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(SubscriptionPlan $plan)
    {
        $plan->load('subscriptions');

        return view('plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubscriptionPlan $plan)
    {
        return view('plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subscription_plans', 'slug')->ignore($plan->id)
            ],
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,quarterly,yearly,lifetime',
            'billing_cycle_days' => 'required|integer|min:1',
            'max_domains' => 'required|integer|min:1',
            'max_storage_gb' => 'required|integer|min:1',
            'max_bandwidth_gb' => 'required|integer|min:1',
            'trial_days' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'color_theme' => 'nullable|string|max:7'
        ]);

        // Filter out empty features
        if (isset($validated['features'])) {
            $validated['features'] = array_filter($validated['features'], function($feature) {
                return !empty(trim($feature));
            });
        }

        // Set boolean values
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['setup_fee'] = $validated['setup_fee'] ?? 0;
        $validated['trial_days'] = $validated['trial_days'] ?? 0;

        $plan->update($validated);

        return redirect()->route('plans.index')
            ->with('success', 'Plano atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubscriptionPlan $plan)
    {
        // Check if plan has active subscriptions
        if ($plan->subscriptions()->exists()) {
            return redirect()->route('plans.index')
                ->with('error', 'Não é possível excluir um plano que possui subscrições ativas.');
        }

        $plan->delete();

        return redirect()->route('plans.index')
            ->with('success', 'Plano excluído com sucesso!');
    }

    /**
     * Toggle the active status of a plan.
     */
    public function toggle(SubscriptionPlan $plan)
    {
        $plan->update([
            'is_active' => !$plan->is_active
        ]);

        $status = $plan->is_active ? 'ativado' : 'desativado';

        return redirect()->route('plans.index')
            ->with('success', "Plano {$status} com sucesso!");
    }

    /**
     * Duplicate a plan.
     */
    public function duplicate(SubscriptionPlan $plan)
    {
        $newPlan = $plan->replicate();
        $newPlan->name = $plan->name . ' (Cópia)';
        $newPlan->slug = $plan->slug . '-copy-' . time();
        $newPlan->is_active = false;
        $newPlan->is_featured = false;
        $newPlan->save();

        return redirect()->route('plans.edit', $newPlan)
            ->with('success', 'Plano duplicado com sucesso! Edite as informações necessárias.');
    }

    /**
     * Get active plans for API or AJAX requests.
     */
    public function getActivePlans()
    {
        $plans = SubscriptionPlan::active()
            ->select('id', 'name', 'slug', 'price', 'billing_cycle', 'features', 'trial_days')
            ->orderBy('price')
            ->get();

        return response()->json($plans);
    }

    /**
     * Get plan statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_plans' => SubscriptionPlan::count(),
            'active_plans' => SubscriptionPlan::active()->count(),
            'featured_plans' => SubscriptionPlan::where('is_featured', true)->count(),
            'total_subscriptions' => SubscriptionPlan::withSum('subscriptions', 'id')->sum('subscriptions_sum_id') ?? 0,
            'average_price' => SubscriptionPlan::active()->avg('price'),
            'price_range' => [
                'min' => SubscriptionPlan::active()->min('price'),
                'max' => SubscriptionPlan::active()->max('price')
            ]
        ];

        return response()->json($stats);
    }
    
}