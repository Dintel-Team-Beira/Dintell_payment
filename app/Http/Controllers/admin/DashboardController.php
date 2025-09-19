<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Comment
    public function index()
    {
        // Estatísticas gerais
        $stats = $this->getGeneralStats();

        // Dados para gráficos
        $chartData = $this->getChartData();

        // Empresas recentes
        $recentCompanies = Company::with('creator')
            ->latest()
            ->limit(5)
            ->get();

        // Empresas com trial expirando
        $trialExpiring = Company::where('status', 'trial')
            ->where('trial_ends_at', '<=', now()->addDays(7))
            ->with('creator')
            ->orderBy('trial_ends_at')
            ->limit(10)
            ->get();

        // Atividade recente
        $recentActivity = $this->getRecentActivity();

        // Top empresas por receita
        $topCompaniesByRevenue = Company::where('status', 'active')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'chartData',
            'recentCompanies',
            'trialExpiring',
            'recentActivity',
            'topCompaniesByRevenue'
        ));
    }

    private function getGeneralStats(): array
    {
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        // Empresas
        $totalCompanies = Company::count();
        $activeCompanies = Company::where('status', 'active')->count();
        $trialCompanies = Company::where('status', 'trial')->count();
        $suspendedCompanies = Company::where('status', 'suspended')->count();

        $newCompaniesThisMonth = Company::where('created_at', '>=', $currentMonth)->count();
        $newCompaniesLastMonth = Company::where('created_at', '>=', $lastMonth)
            ->where('created_at', '<', $currentMonth)
            ->count();

        // Receita
        $monthlyRevenue = Company::where('status', 'active')->sum('monthly_fee');
        $annualRevenue = $monthlyRevenue * 12;

        // Crescimento MRR (Monthly Recurring Revenue)
        $previousMonthRevenue = Company::where('created_at', '<', $currentMonth)
            ->where('status', 'active')
            ->sum('monthly_fee');

        $mrrGrowth = $previousMonthRevenue > 0
            ? (($monthlyRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100
            : 0;

        // Usuários
        $totalUsers = User::count();
        $activeUsers = User::where('last_activity_at', '>=', now()->subDays(30))->count();

        // Faturas do sistema (todas as empresas)
        $totalInvoices = Invoice::count();
        $paidInvoices = Invoice::where('status', 'paid')->count();
        $pendingInvoices = Invoice::where('status', 'pending')->count();

        $thisMonthInvoices = Invoice::where('created_at', '>=', $currentMonth)->count();
        $lastMonthInvoices = Invoice::where('created_at', '>=', $lastMonth)
            ->where('created_at', '<', $currentMonth)
            ->count();

        // Taxa de conversão trial para pago
        $trialToActiveRate = $trialCompanies > 0
            ? ($activeCompanies / ($activeCompanies + $trialCompanies)) * 100
            : 0;

        // Churn rate (empresas que cancelaram no último mês)
        $canceledThisMonth = Company::onlyTrashed()
            ->where('deleted_at', '>=', $currentMonth)
            ->count();

        $churnRate = $totalCompanies > 0 ? ($canceledThisMonth / $totalCompanies) * 100 : 0;

        return [
            'companies' => [
                'total' => $totalCompanies,
                'active' => $activeCompanies,
                'trial' => $trialCompanies,
                'suspended' => $suspendedCompanies,
                'new_this_month' => $newCompaniesThisMonth,
                'growth_rate' => $newCompaniesLastMonth > 0
                    ? (($newCompaniesThisMonth - $newCompaniesLastMonth) / $newCompaniesLastMonth) * 100
                    : 0,
                'trial_expiring_week' => Company::where('status', 'trial')
                    ->where('trial_ends_at', '<=', now()->addDays(7))
                    ->count(),
            ],
            'revenue' => [
                'monthly' => $monthlyRevenue,
                'annual' => $annualRevenue,
                'mrr_growth' => $mrrGrowth,
                'average_per_company' => $activeCompanies > 0 ? $monthlyRevenue / $activeCompanies : 0,
            ],
            'users' => [
                'total' => $totalUsers,
                'active' => $activeUsers,
                'average_per_company' => $totalCompanies > 0 ? $totalUsers / $totalCompanies : 0,
            ],
            'invoices' => [
                'total' => $totalInvoices,
                'paid' => $paidInvoices,
                'pending' => $pendingInvoices,
                'this_month' => $thisMonthInvoices,
                'growth_rate' => $lastMonthInvoices > 0
                    ? (($thisMonthInvoices - $lastMonthInvoices) / $lastMonthInvoices) * 100
                    : 0,
            ],
            'metrics' => [
                'trial_to_active_rate' => $trialToActiveRate,
                'churn_rate' => $churnRate,
                'activation_rate' => $totalCompanies > 0 ? ($activeCompanies / $totalCompanies) * 100 : 0,
            ]
        ];
    }

    private function getChartData(): array
    {
        // Receita mensal nos últimos 12 meses
        $monthlyRevenue = Company::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(monthly_fee) as revenue'),
                DB::raw('COUNT(*) as companies')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->where('status', 'active')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Preencher meses faltantes
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $months->put($month, [
                'month' => $month,
                'revenue' => $monthlyRevenue->get($month)->revenue ?? 0,
                'companies' => $monthlyRevenue->get($month)->companies ?? 0,
            ]);
        }

        // Crescimento de empresas nos últimos 12 meses
        $companyGrowth = Company::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as new_companies')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $companyGrowthData = collect();
        $cumulativeCount = Company::where('created_at', '<', now()->subMonths(12))->count();

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $newCompanies = $companyGrowthData->get($month)->new_companies ?? 0;
            $cumulativeCount += $newCompanies;

            $companyGrowthData->put($month, [
                'month' => $month,
                'new_companies' => $newCompanies,
                'total_companies' => $cumulativeCount,
            ]);
        }

        // Distribuição por planos
        $planDistribution = Company::select('subscription_plan', DB::raw('COUNT(*) as count'))
            ->groupBy('subscription_plan')
            ->get()
            ->keyBy('subscription_plan');

        // Status das empresas
        $statusDistribution = Company::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return [
            'monthly_revenue' => $months->values(),
            'company_growth' => $companyGrowthData->values(),
            'plan_distribution' => $planDistribution,
            'status_distribution' => $statusDistribution,
        ];
    }

    private function getRecentActivity(): array
    {
        $activities = collect();

        // Empresas criadas recentemente
        $recentCompanies = Company::with('creator')
            ->where('created_at', '>=', now()->subDays(7))
            ->latest()
            ->get();

        foreach ($recentCompanies as $company) {
            $activities->push([
                'type' => 'company_created',
                'title' => 'Nova empresa criada',
                'description' => "{$company->name} foi criada",
                'user' => $company->creator->name ?? 'Sistema',
                'time' => $company->created_at,
                'icon' => 'building',
                'color' => 'green',
                'link' => route('admin.companies.show', $company),
            ]);
        }

        // Empresas que mudaram de status
        $statusChanges = Company::with('creator')
            ->where('updated_at', '>=', now()->subDays(7))
            ->where('updated_at', '!=', DB::raw('created_at'))
            ->latest('updated_at')
            ->limit(10)
            ->get();

        foreach ($statusChanges as $company) {
            $activities->push([
                'type' => 'company_updated',
                'title' => 'Status da empresa alterado',
                'description' => "{$company->name} agora está {$company->status}",
                'user' => $company->creator->name ?? 'Sistema',
                'time' => $company->updated_at,
                'icon' => 'refresh',
                'color' => 'blue',
                'link' => route('admin.companies.show', $company),
            ]);
        }

        // Trials expirando
        $expiringTrials = Company::where('status', 'trial')
            ->where('trial_ends_at', '<=', now()->addDays(3))
            ->latest('trial_ends_at')
            ->limit(5)
            ->get();

        foreach ($expiringTrials as $company) {
            $activities->push([
                'type' => 'trial_expiring',
                'title' => 'Trial expirando',
                'description' => "Trial de {$company->name} expira em {$company->trial_days_left} dias",
                'user' => 'Sistema',
                'time' => $company->trial_ends_at,
                'icon' => 'clock',
                'color' => 'yellow',
                'link' => route('admin.companies.show', $company),
            ]);
        }

        return $activities->sortByDesc('time')->take(20)->values()->all();
    }

    public function revenueReport(Request $request)
    {
        $period = $request->get('period', '12months');

        switch ($period) {
            case '7days':
                $data = $this->getRevenueDataByDays(7);
                break;
            case '30days':
                $data = $this->getRevenueDataByDays(30);
                break;
            case '6months':
                $data = $this->getRevenueDataByMonths(6);
                break;
            case '12months':
            default:
                $data = $this->getRevenueDataByMonths(12);
                break;
        }

        return response()->json($data);
    }

    public function clientsReport(Request $request)
    {
        $period = $request->get('period', '12months');

        $data = Company::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as new_companies'),
                DB::raw('SUM(COUNT(*)) OVER (ORDER BY DATE_FORMAT(created_at, "%Y-%m")) as total_companies')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($data);
    }

    public function usageReport(Request $request)
    {
        $companies = Company::with(['invoices' => function ($query) {
                $query->where('created_at', '>=', now()->subMonth());
            }])
            ->where('status', 'active')
            ->get();

        $usageData = $companies->map(function ($company) {
            $usage = $company->usage_percentage;
            return [
                'company' => $company->name,
                'users_usage' => $usage['users'],
                'invoices_usage' => $usage['invoices'],
                'clients_usage' => $usage['clients'],
                'plan' => $company->subscription_plan,
            ];
        });

        return response()->json($usageData);
    }

    public function exportReport(Request $request, $type)
    {
        // Implementar exportação de relatórios
        // CSV, Excel, PDF, etc.

        switch ($type) {
            case 'companies':
                return $this->exportCompanies($request);
            case 'revenue':
                return $this->exportRevenue($request);
            case 'usage':
                return $this->exportUsage($request);
            default:
                abort(404);
        }
    }

    private function getRevenueDataByDays($days): array
    {
        $data = collect();

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = Company::where('status', 'active')
                ->where('created_at', '<=', $date->endOfDay())
                ->sum('monthly_fee');

            $data->push([
                'date' => $date->format('Y-m-d'),
                'revenue' => $revenue,
                'label' => $date->format('d/m'),
            ]);
        }

        return $data->toArray();
    }

    private function getRevenueDataByMonths($months): array
    {
        $data = collect();

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = Company::where('status', 'active')
                ->where('created_at', '<=', $month->endOfMonth())
                ->sum('monthly_fee');

            $data->push([
                'month' => $month->format('Y-m'),
                'revenue' => $revenue,
                'label' => $month->format('M/Y'),
            ]);
        }

        return $data->toArray();
    }

    private function exportCompanies(Request $request)
    {
        // Implementar exportação de empresas
        return response()->json(['message' => 'Export not implemented yet']);
    }

    private function exportRevenue(Request $request)
    {
        // Implementar exportação de receita
        return response()->json(['message' => 'Export not implemented yet']);
    }

    private function exportUsage(Request $request)
    {
        // Implementar exportação de uso
        return response()->json(['message' => 'Export not implemented yet']);
    }
}
