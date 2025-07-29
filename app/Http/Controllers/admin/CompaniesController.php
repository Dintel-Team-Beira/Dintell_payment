<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CompaniesController extends Controller
{
    use LogsActivity;

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

        // Log da visualização
        $this->logAdminActivity('Visualizou lista de empresas', [
            'total_companies' => $stats['total'],
            'filters' => $request->only(['status', 'subscription_plan', 'search', 'trial_expiring'])
        ]);

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

        // Log da visualização
        $this->logAdminActivity('Visualizou detalhes da empresa', [
            'company_id' => $company->id,
            'company_name' => $company->name,
            'company_status' => $company->status
        ]);

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

        // Log da ação
        $this->logAdminActivity('Acessou formulário de criação de empresa');

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

            // Log da criação
            $this->logAdminActivity('Criou nova empresa', [
                'company_id' => $company->id,
                'company_name' => $company->name,
                'company_email' => $company->email,
                'subscription_plan' => $company->subscription_plan,
                'status' => $company->status
            ]);

            return redirect()
                ->route('admin.companies.show', $company)
                ->with('success', 'Empresa criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Remover logo se foi feito upload
            if (isset($validated['logo'])) {
                Storage::disk('public')->delete($validated['logo']);
            }

            // Log do erro
            $this->logAdminActivity('Erro ao criar empresa', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);

            return back()
                ->withErrors(['error' => 'Erro ao criar empresa: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function suspend(Request $request, Company $company)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $company->suspend($request->reason);

        // Log da suspensão
        $this->logAdminActivity('Suspendeu empresa', [
            'company_id' => $company->id,
            'company_name' => $company->name,
            'reason' => $request->reason
        ]);

        return back()->with('success', 'Empresa suspensa com sucesso!');
    }

    public function activate(Company $company)
    {
        $company->activate();

        // Log da ativação
        $this->logAdminActivity('Ativou empresa', [
            'company_id' => $company->id,
            'company_name' => $company->name
        ]);

        return back()->with('success', 'Empresa ativada com sucesso!');
    }

    public function extendTrial(Request $request, Company $company)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:90'
        ]);

        $oldTrialEnd = $company->trial_ends_at;
        $company->extendTrial($request->days);

        // Log da extensão
        $this->logAdminActivity('Estendeu trial da empresa', [
            'company_id' => $company->id,
            'company_name' => $company->name,
            'days_added' => $request->days,
            'old_trial_end' => $oldTrialEnd?->toISOString(),
            'new_trial_end' => $company->fresh()->trial_ends_at?->toISOString()
        ]);

        return back()->with('success', "Trial estendido por {$request->days} dias!");
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
        session(['impersonate_company_id' => $company->id]);

        // Log da impersonificação
        $this->logAdminActivity('Iniciou impersonificação', [
            'company_id' => $company->id,
            'company_name' => $company->name,
            'target_user_id' => $user->id,
            'target_user_name' => $user->name
        ]);

        // Login como usuário da empresa
        auth()->login($user);

        return redirect()->route('dashboard')
            ->with('info', "Você está logado como {$user->name} da empresa {$company->name}");
    }

    public function stopImpersonation()
    {
        $adminId = session('impersonate_admin');
        $companyId = session('impersonate_company_id');

        if ($adminId) {
            // Log do fim da impersonificação
            $this->logAdminActivity('Finalizou impersonificação', [
                'company_id' => $companyId,
                'returning_admin_id' => $adminId
            ]);

            session()->forget(['impersonate_admin', 'impersonate_company_id']);
            auth()->loginUsingId($adminId);

            return redirect()->route('admin.companies.index')
                ->with('success', 'Sessão de impersonificação encerrada.');
        }

        return redirect()->route('admin.dashboard');
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
}
