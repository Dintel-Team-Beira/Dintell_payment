<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CompaniesController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::with(['creator', 'users'])
            ->withCount(['users', 'invoices', 'clients']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('subscription_plan')) {
            $query->where('subscription_plan', $request->subscription_plan);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('trial_expiring')) {
            $query->where('status', 'trial')
                  ->where('trial_ends_at', '<=', now()->addDays(7));
        }

        $companies = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Estatísticas gerais
        $stats = [
            'total' => Company::count(),
            'active' => Company::where('status', 'active')->count(),
            'trial' => Company::where('status', 'trial')->count(),
            'suspended' => Company::where('status', 'suspended')->count(),
            'trial_expiring' => Company::where('status', 'trial')
                ->where('trial_ends_at', '<=', now()->addDays(7))
                ->count(),
            'monthly_revenue' => Company::where('status', 'active')
                ->sum('monthly_fee'),
        ];

        return view('admin.companies.index', compact('companies', 'stats'));
    }

    public function show(Company $company)
    {
        $company->load(['creator', 'users', 'invoices' => function ($query) {
            $query->latest()->limit(10);
        }]);

        // Estatísticas da empresa
        $stats = [
            'total_invoices' => $company->invoices()->count(),
            'paid_invoices' => $company->invoices()->where('status', 'paid')->count(),
            'pending_invoices' => $company->invoices()->where('status', 'pending')->count(),
            'total_revenue' => $company->invoices()->where('status', 'paid')->sum('total_amount'),
            'monthly_revenue' => $company->invoices()
                ->where('status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->sum('total_amount'),
            'avg_invoice_value' => $company->invoices()->where('status', 'paid')->avg('total_amount'),
        ];

        // Atividade mensal (últimos 12 meses)
        $monthlyActivity = DB::table('invoices')
            ->where('company_id', $company->id)
            ->where('created_at', '>=', now()->subMonths(12))
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as invoices_count'),
                DB::raw('SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as revenue')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('admin.companies.show', compact('company', 'stats', 'monthlyActivity'));
    }

    public function create()
    {
        $subscriptionPlans = [
            'basic' => [
                'name' => 'Básico',
                'price' => 500,
                'max_users' => 1,
                'max_invoices_per_month' => 50,
                'max_clients' => 100,
                'features' => ['Faturação básica', 'Relatórios simples']
            ],
            'premium' => [
                'name' => 'Premium',
                'price' => 1500,
                'max_users' => 5,
                'max_invoices_per_month' => 200,
                'max_clients' => 500,
                'features' => ['Faturação avançada', 'API access', 'Relatórios avançados', 'Suporte prioritário']
            ],
            'enterprise' => [
                'name' => 'Empresarial',
                'price' => 3000,
                'max_users' => 999,
                'max_invoices_per_month' => 999999,
                'max_clients' => 999999,
                'features' => ['Ilimitado', 'Domínio personalizado', 'Integração avançada', 'Suporte dedicado']
            ]
        ];

        return view('admin.companies.create', compact('subscriptionPlans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:companies',
            'email' => 'required|email|unique:companies',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'subscription_plan' => 'required|in:basic,premium,enterprise',
            'status' => 'required|in:active,trial,suspended',
            'trial_days' => 'nullable|integer|min:1|max:90',
            'custom_domain_enabled' => 'boolean',
            'api_access_enabled' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            // Upload do logo se fornecido
            if ($request->hasFile('logo')) {
                $validated['logo'] = $request->file('logo')->store('companies/logos', 'public');
            }

            // Configurações baseadas no plano
            $planConfig = $this->getPlanConfiguration($validated['subscription_plan']);
            $validated = array_merge($validated, $planConfig);

            // Configurar trial se necessário
            if ($validated['status'] === 'trial') {
                $trialDays = $validated['trial_days'] ?? 30;
                $validated['trial_ends_at'] = now()->addDays($trialDays);
            }

            $validated['created_by'] = auth()->id();

            $company = Company::create($validated);

            DB::commit();

            return redirect()
                ->route('admin.companies.show', $company)
                ->with('success', 'Empresa criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Remover logo se foi feito upload
            if (isset($validated['logo'])) {
                Storage::disk('public')->delete($validated['logo']);
            }

            return back()
                ->withErrors(['error' => 'Erro ao criar empresa: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit(Company $company)
    {
        $subscriptionPlans = [
            'basic' => [
                'name' => 'Básico',
                'price' => 500,
                'max_users' => 1,
                'max_invoices_per_month' => 50,
                'max_clients' => 100,
            ],
            'premium' => [
                'name' => 'Premium',
                'price' => 1500,
                'max_users' => 5,
                'max_invoices_per_month' => 200,
                'max_clients' => 500,
            ],
            'enterprise' => [
                'name' => 'Empresarial',
                'price' => 3000,
                'max_users' => 999,
                'max_invoices_per_month' => 999999,
                'max_clients' => 999999,
            ]
        ];

        return view('admin.companies.edit', compact('company', 'subscriptionPlans'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('companies')->ignore($company->id)],
            'email' => ['required', 'email', Rule::unique('companies')->ignore($company->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'subscription_plan' => 'required|in:basic,premium,enterprise',
            'status' => 'required|in:active,trial,suspended,inactive',
            'custom_domain_enabled' => 'boolean',
            'api_access_enabled' => 'boolean',
            'domain' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $oldLogo = $company->logo;

            // Upload do novo logo se fornecido
            if ($request->hasFile('logo')) {
                $validated['logo'] = $request->file('logo')->store('companies/logos', 'public');

                // Remover logo antigo
                if ($oldLogo) {
                    Storage::disk('public')->delete($oldLogo);
                }
            }

            // Atualizar configurações do plano se mudou
            if ($company->subscription_plan !== $validated['subscription_plan']) {
                $planConfig = $this->getPlanConfiguration($validated['subscription_plan']);
                $validated = array_merge($validated, $planConfig);
            }

            $company->update($validated);
            $company->updateUsageStats();

            DB::commit();

            return redirect()
                ->route('admin.companies.show', $company)
                ->with('success', 'Empresa atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Erro ao atualizar empresa: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Company $company)
    {
        DB::beginTransaction();

        try {
            // Verificar se a empresa pode ser deletada
            if ($company->invoices()->count() > 0) {
                return back()->withErrors(['error' => 'Não é possível deletar uma empresa com faturas.']);
            }

            // Remover logo se existir
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }

            // Soft delete
            $company->delete();

            DB::commit();

            return redirect()
                ->route('admin.companies.index')
                ->with('success', 'Empresa removida com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Erro ao remover empresa: ' . $e->getMessage()]);
        }
    }

    public function suspend(Request $request, Company $company)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $company->suspend($request->reason);

        return back()->with('success', 'Empresa suspensa com sucesso!');
    }

    public function activate(Company $company)
    {
        $company->activate();

        return back()->with('success', 'Empresa ativada com sucesso!');
    }

    public function extendTrial(Request $request, Company $company)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:90'
        ]);

        $company->extendTrial($request->days);

        return back()->with('success', "Trial extendido por {$request->days} dias!");
    }

    public function impersonate(Company $company)
    {
        // Pegar o primeiro admin da empresa
        $user = $company->users()->where('role', 'admin')->first();

        if (!$user) {
            return back()->withErrors(['error' => 'Empresa não possui administrador.']);
        }

        // Salvar admin atual na sessão
        session(['impersonate_admin' => auth()->id()]);

        // Login como usuário da empresa
        auth()->login($user);

        return redirect()->route('billing.dashboard')
            ->with('info', "Você está logado como {$user->name} da empresa {$company->name}");
    }

    public function stopImpersonation()
    {
        $adminId = session('impersonate_admin');

        if ($adminId) {
            session()->forget('impersonate_admin');
            auth()->loginUsingId($adminId);

            return redirect()->route('admin.companies.index')
                ->with('success', 'Sessão de impersonificação encerrada.');
        }

        return redirect()->route('admin.dashboard');
    }

    public function analytics(Company $company)
    {
        // Dados para gráficos e analytics avançados
        $analytics = [
            'revenue_trend' => $this->getRevenueTrend($company),
            'invoice_status_distribution' => $this->getInvoiceStatusDistribution($company),
            'client_growth' => $this->getClientGrowth($company),
            'usage_metrics' => $company->usage_percentage,
        ];

        return view('admin.companies.analytics', compact('company', 'analytics'));
    }

    private function getPlanConfiguration(string $plan): array
    {
        $configs = [
            'basic' => [
                'monthly_fee' => 500.00,
                'max_users' => 1,
                'max_invoices_per_month' => 50,
                'max_clients' => 100,
                'custom_domain_enabled' => false,
                'api_access_enabled' => false,
                'feature_flags' => [
                    'advanced_reports' => false,
                    'multi_currency' => false,
                    'api_access' => false,
                    'custom_branding' => false,
                ]
            ],
            'premium' => [
                'monthly_fee' => 1500.00,
                'max_users' => 5,
                'max_invoices_per_month' => 200,
                'max_clients' => 500,
                'custom_domain_enabled' => false,
                'api_access_enabled' => true,
                'feature_flags' => [
                    'advanced_reports' => true,
                    'multi_currency' => true,
                    'api_access' => true,
                    'custom_branding' => false,
                ]
            ],
            'enterprise' => [
                'monthly_fee' => 3000.00,
                'max_users' => 999,
                'max_invoices_per_month' => 999999,
                'max_clients' => 999999,
                'custom_domain_enabled' => true,
                'api_access_enabled' => true,
                'feature_flags' => [
                    'advanced_reports' => true,
                    'multi_currency' => true,
                    'api_access' => true,
                    'custom_branding' => true,
                    'priority_support' => true,
                ]
            ]
        ];

        return $configs[$plan] ?? $configs['basic'];
    }

    private function getRevenueTrend(Company $company): array
    {
        return DB::table('invoices')
            ->where('company_id', $company->id)
            ->where('status', 'paid')
            ->where('created_at', '>=', now()->subMonths(12))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month')
            ->toArray();
    }

    private function getInvoiceStatusDistribution(Company $company): array
    {
        return $company->invoices()
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    private function getClientGrowth(Company $company): array
    {
        return DB::table('clients')
            ->where('company_id', $company->id)
            ->where('created_at', '>=', now()->subMonths(12))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as new_clients')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('new_clients', 'month')
            ->toArray();
    }
}
