<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plan;
use App\Models\User;
use App\Models\Company;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

/**
 * Controller ADMIN para gerenciar subscrições dos usuários
 * Separado do SubscriptionController existente para SaaS
 */
class UserSubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Dashboard de subscrições (Admin)
     */
    public function index(Request $request)
    {
        // Query base para empresas
        $query = Company::with(['plan', 'users', 'payments' => function($q) {
            $q->latest()->take(1);
        }]);

        // Aplicar filtros
        $this->applyFilters($query, $request);

        $companies = $query->orderBy('created_at', 'desc')->paginate(20);

        // Buscar planos para filtro
        $plans = Plan::where('is_active', true)->orderBy('name')->get();

        // Calcular estatísticas
        $stats = $this->getSubscriptionStats();

        // Métricas adicionais
        $metrics = $this->getSubscriptionMetrics();

        return view('admin.user-subscriptions.index', compact(
            'companies',
            'plans',
            'stats',
            'metrics'
        ));
    }

    /**
     * Detalhes de uma empresa específica
     */
    public function show(Company $company)
    {
        $company->load(['plan', 'users', 'payments', 'invoices']);

        // Estatísticas de uso da empresa
        $usage = $this->getCompanyUsage($company);

        // Timeline da subscrição
        $timeline = $this->getSubscriptionTimeline($company);

        // Pagamentos recentes
        $recentPayments = $company->payments()
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Status e alertas
        $alerts = $this->getCompanyAlerts($company);

        return view('admin.user-subscriptions.show', compact(
            'company',
            'usage',
            'timeline',
            'recentPayments',
            'alerts'
        ));
    }

    /**
     * Editar subscrição da empresa
     */
    public function edit(Company $company)
    {
        $plans = Plan::where('is_active', true)->orderBy('price')->get();

        return view('admin.user-subscriptions.edit', compact('company', 'plans'));
    }

    /**
     * Atualizar subscrição
     */
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'subscription_status' => 'required|in:active,suspended,expired,cancelled,pending_payment',
            'subscription_expires_at' => 'nullable|date|after:today',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $oldData = [
                'plan_id' => $company->plan_id,
                'subscription_status' => $company->subscription_status,
                'subscription_expires_at' => $company->subscription_expires_at
            ];

            // Atualizar dados da empresa
            $company->update([
                'plan_id' => $request->plan_id,
                'subscription_status' => $request->subscription_status,
                'subscription_expires_at' => $request->subscription_expires_at,
                'admin_notes' => $request->admin_notes
            ]);

            DB::commit();

            return redirect()->route('admin.user-subscriptions.show', $company)
                ->with('success', 'Subscrição atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erro ao atualizar subscrição: ' . $e->getMessage());
        }
    }

    /**
     * Suspender empresa
     */
    public function suspend(Request $request, Company $company)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        if ($company->isSuspended()) {
            return back()->with('warning', 'Esta empresa já está suspensa.');
        }

        DB::beginTransaction();
        try {
            $company->update([
                'subscription_status' => 'suspended',
                'suspended_at' => now(),
                'suspension_reason' => $request->reason
            ]);

            DB::commit();

            return redirect()->route('admin.user-subscriptions.show', $company)
                ->with('success', 'Empresa suspensa com sucesso!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erro ao suspender empresa: ' . $e->getMessage());
        }
    }

    /**
     * Reativar empresa
     */
    public function reactivate(Company $company)
    {
        if (!$company->isSuspended()) {
            return back()->with('warning', 'Esta empresa não está suspensa.');
        }

        DB::beginTransaction();
        try {
            $company->update([
                'subscription_status' => 'active',
                'suspended_at' => null,
                'suspension_reason' => null
            ]);

            DB::commit();

            return redirect()->route('admin.user-subscriptions.show', $company)
                ->with('success', 'Empresa reativada com sucesso!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erro ao reativar empresa: ' . $e->getMessage());
        }
    }

    /**
     * Estender período de teste
     */
    public function extendTrial(Request $request, Company $company)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:90'
        ]);

        if (!$company->isTrial()) {
            return back()->with('error', 'Esta empresa não está em período de teste.');
        }

        DB::beginTransaction();
        try {
            $currentExpiration = $company->subscription_expires_at ?? now();
            $newExpirationDate = $currentExpiration->addDays($request->days);

            $company->update([
                'subscription_expires_at' => $newExpirationDate
            ]);

            DB::commit();

            return back()->with('success', "Período de teste estendido por {$request->days} dias!");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erro ao estender período de teste: ' . $e->getMessage());
        }
    }

    /**
     * Lista de pagamentos pendentes
     */
    public function payments(Request $request)
    {
        $query = Payment::with(['company', 'plan']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        // Estatísticas de pagamentos
        $paymentStats = $this->getPaymentStats();

        return view('admin.user-subscriptions.payments', compact('payments', 'paymentStats'));
    }

    /**
     * Aprovar pagamento
     */
    public function approvePayment(Payment $payment)
    {
        if ($payment->status !== 'submitted') {
            return back()->with('error', 'Este pagamento não pode ser aprovado.');
        }

        DB::beginTransaction();
        try {
            // Atualizar status do pagamento
            $payment->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id()
            ]);

            // Ativar/renovar subscrição da empresa
            $company = $payment->company;
            $this->activateCompanySubscription($company, $payment->plan, $payment->type);

            DB::commit();

            return back()->with('success', 'Pagamento aprovado e subscrição ativada!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erro ao aprovar pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Rejeitar pagamento
     */
    public function rejectPayment(Request $request, Payment $payment)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        if ($payment->status !== 'submitted') {
            return back()->with('error', 'Este pagamento não pode ser rejeitado.');
        }

        DB::beginTransaction();
        try {
            $payment->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejected_by' => auth()->id(),
                'rejection_reason' => $request->rejection_reason
            ]);

            DB::commit();

            return back()->with('success', 'Pagamento rejeitado!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erro ao rejeitar pagamento: ' . $e->getMessage());
        }
    }

    // ============ MÉTODOS AUXILIARES ============

    private function applyFilters($query, $request)
    {
        if ($request->filled('subscription_status')) {
            $query->where('subscription_status', $request->subscription_status);
        }

        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('expiring_soon')) {
            $query->expiringSoon(7);
        }

        if ($request->filled('expired')) {
            $query->expired();
        }

        if ($request->filled('trial')) {
            $query->trial();
        }
    }

    private function getSubscriptionStats()
    {
        return [
            'total_companies' => Company::count(),
            'active_subscriptions' => Company::where('subscription_status', 'active')->count(),
            'pending_payments' => Payment::where('status', 'submitted')->count(),
            'expiring_soon' => Company::expiringSoon(7)->count(),
            'expired' => Company::expired()->count(),
            'trial_companies' => Company::trial()->count(),
            'suspended_companies' => Company::suspended()->count(),
        ];
    }

    private function getSubscriptionMetrics()
    {
        $currentMonth = now()->startOfMonth();

        return [
            'new_subscriptions_this_month' => Company::where('created_at', '>=', $currentMonth)->count(),
            'revenue_this_month' => Payment::where('status', 'approved')
                ->where('approved_at', '>=', $currentMonth)
                ->sum('amount'),
            'average_revenue_per_user' => $this->calculateARPU(),
            'churn_rate' => $this->calculateChurnRate(),
        ];
    }

    private function getCompanyUsage($company)
    {
        $currentPeriod = now()->startOfMonth();
        $plan = $company->plan;

        if (!$plan) {
            return [
                'users' => ['current' => 0, 'max' => 0],
                'invoices' => ['current_month' => 0, 'max_monthly' => 0, 'total' => 0]
            ];
        }

        return [
            'users' => [
                'current' => $company->users()->count(),
                'max' => $plan->max_users ?? 0,
            ],
            'invoices' => [
                'current_month' => $company->invoices()
                    ->whereYear('created_at', $currentPeriod->year)
                    ->whereMonth('created_at', $currentPeriod->month)
                    ->count(),
                'max_monthly' => $plan->max_invoices_per_month ?? 0,
                'total' => $company->invoices()->count()
            ],
            'clients' => [
                'current' => $company->clients()->count(),
                'max' => $plan->max_clients ?? 0
            ]
        ];
    }

    private function getSubscriptionTimeline($company)
    {
        return [
            'created_at' => $company->created_at,
            'trial_started' => $company->created_at,
            'subscription_expires_at' => $company->subscription_expires_at,
            'suspended_at' => $company->suspended_at,
            'last_payment' => $company->payments()->where('status', 'approved')->latest()->first()?->approved_at
        ];
    }

    private function getCompanyAlerts($company)
    {
        $alerts = [];

        // Verificar se está próximo da expiração
        $daysLeft = $company->getDaysUntilExpiration();
        if ($daysLeft !== null && $daysLeft <= 7) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "Subscrição expira em {$daysLeft} dias",
                'action' => 'extend'
            ];
        }

        // Verificar se tem pagamentos pendentes
        $pendingPayments = $company->payments()->where('status', 'submitted')->count();
        if ($pendingPayments > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$pendingPayments} pagamento(s) aguardando aprovação",
                'action' => 'review_payments'
            ];
        }

        // Verificar uso de limites
        $usage = $this->getCompanyUsage($company);
        if ($usage['users']['max'] > 0 && $usage['users']['current'] >= $usage['users']['max']) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "Limite de usuários excedido",
                'action' => 'increase_limit'
            ];
        }

        return $alerts;
    }

    private function getPaymentStats()
    {
        return [
            'pending_review' => Payment::where('status', 'submitted')->count(),
            'approved_today' => Payment::where('status', 'approved')
                ->whereDate('approved_at', today())->count(),
            'total_revenue_month' => Payment::where('status', 'approved')
                ->whereMonth('approved_at', now()->month)
                ->whereYear('approved_at', now()->year)
                ->sum('amount'),
            'average_payment_time' => $this->calculateAveragePaymentTime()
        ];
    }

    private function activateCompanySubscription($company, $plan, $paymentType = 'renewal')
    {
        $expirationDate = $this->calculateExpirationDate($plan, $company, $paymentType);

        $company->update([
            'subscription_status' => 'active',
            'subscription_expires_at' => $expirationDate,
            'subscription_type' => 'paid'
        ]);
    }

    private function calculateExpirationDate($plan, $company, $paymentType)
    {
        $baseDate = now();

        // Se é upgrade e ainda tem tempo restante, manter a data atual
        if ($paymentType === 'upgrade' && $company->subscription_expires_at && $company->subscription_expires_at->isFuture()) {
            $baseDate = $company->subscription_expires_at;
        }

        return match($plan->billing_cycle) {
            'monthly' => $baseDate->addMonth(),
            'quarterly' => $baseDate->addMonths(3),
            'yearly' => $baseDate->addYear(),
            default => $baseDate->addMonth()
        };
    }

    private function calculateARPU()
    {
        $totalRevenue = Payment::where('status', 'approved')
            ->whereMonth('approved_at', now()->month)
            ->sum('amount');

        $activeCompanies = Company::where('subscription_status', 'active')->count();

        return $activeCompanies > 0 ? $totalRevenue / $activeCompanies : 0;
    }

    private function calculateChurnRate()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $companiesAtStart = Company::where('created_at', '<', $startOfMonth)->count();
        $cancelledThisMonth = Company::where('subscription_status', 'cancelled')
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->count();

        return $companiesAtStart > 0 ? ($cancelledThisMonth / $companiesAtStart) * 100 : 0;
    }

    private function calculateAveragePaymentTime()
    {
        $avgTime = Payment::where('status', 'approved')
            ->whereNotNull('submitted_at')
            ->whereNotNull('approved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, submitted_at, approved_at)) as avg_hours')
            ->value('avg_hours');

        return round($avgTime ?? 0, 1);
    }
}
