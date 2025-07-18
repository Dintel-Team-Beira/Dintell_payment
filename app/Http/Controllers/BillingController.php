<?php

namespace App\Http\Controllers;

use App\Models\BillingSetting;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index()
    {
        $settings = BillingSetting::getSettings();
        return view('settings.index', compact('settings'));
    }

    public function dashboard(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $startDate = $this->getStartDate($period, $request->get('start_date'));
        $endDate = $this->getEndDate($period, $request->get('end_date'));

        // Estatísticas principais melhoradas
        $stats = $this->getEnhancedStats($startDate, $endDate);

        // Faturas vencidas (máximo 10)
        $overdueInvoices = Invoice::with('client')
            ->where('status', 'overdue')
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get();

        // Cotações recentes (máximo 10)
        $recentQuotes = Quote::with('client')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Top clientes por valor faturado
        $topClients = $this->getTopClients($startDate, $endDate);

        // Dados para gráficos melhorados
        $chartData = $this->getEnhancedChartData();
        $statss = $this->getInvoiceStats();
        // $stats = $this->getInvoiceStats();

        return view('billing.dashboard', compact(
            'stats',
            'statss',
            'overdueInvoices',
            'recentQuotes',
            'topClients',
            'chartData',
            'period',
            'startDate',
            'endDate'
        ));
    }

    private function getInvoiceStats()
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        $today = Carbon::today();

        $stats = [
            // Total de faturas
            'total_invoices' => Invoice::count(),

            // Faturas pendentes (status = 'draft' ou due_date ainda não passou)
            'total_pending' => Invoice::whereIn('status', ['draft', 'sent'])
                ->where('due_date', '>=', $today)
                ->sum('total'),
            'pending_count' => Invoice::whereIn('status', ['draft', 'sent'])
                ->where('due_date', '>=', $today)
                ->count(),

            // Faturas vencidas (due_date passou e não foram pagas)
            'total_overdue' => Invoice::whereIn('status', ['draft', 'sent'])
                ->where('due_date', '<', $today)
                ->sum('total'),
            'count_overdue' => Invoice::whereIn('status', ['draft', 'sent'])
                ->where('due_date', '<', $today)
                ->count(),

            // Faturas pagas este mês
            'total_paid_this_month' => Invoice::where('status', 'paid')
                ->whereMonth('updated_at', $currentMonth->month) // ou use paid_at se existir
                ->whereYear('updated_at', $currentMonth->year)
                ->sum('total'),
            'paid_count_this_month' => Invoice::where('status', 'paid')
                ->whereMonth('updated_at', $currentMonth->month)
                ->whereYear('updated_at', $currentMonth->year)
                ->count(),
        ];

        // Calcular média de dias para pagamento
        $paidInvoices = Invoice::where('status', 'paid')
            ->whereNotNull('updated_at') // ou paid_at se tiver essa coluna
            ->get();

        $totalDays = 0;
        $count = 0;

        foreach ($paidInvoices as $invoice) {
            if ($invoice->updated_at && $invoice->invoice_date) {
                // Se tiver paid_at, use: $invoice->paid_at
                $days = Carbon::parse($invoice->invoice_date)->diffInDays($invoice->updated_at);
                $totalDays += $days;
                $count++;
            }
        }

        $stats['avg_payment_days'] = $count > 0 ? round($totalDays / $count) : 0;

        // Calcular crescimento comparado ao mês anterior
        $lastMonthStats = [
            'total_invoices' => Invoice::whereMonth('created_at', $lastMonth->month)
                                 ->whereYear('created_at', $lastMonth->year)
                                 ->count(),
            'total_amount' => Invoice::whereMonth('created_at', $lastMonth->month)
                                 ->whereYear('created_at', $lastMonth->year)
                                 ->sum('total')
        ];

        $currentMonthStats = [
            'total_invoices' => Invoice::whereMonth('created_at', $currentMonth->month)
                                 ->whereYear('created_at', $currentMonth->year)
                                 ->count(),
            'total_amount' => Invoice::whereMonth('created_at', $currentMonth->month)
                                 ->whereYear('created_at', $currentMonth->year)
                                 ->sum('total')
        ];

        // Calcular percentual de crescimento
        if ($lastMonthStats['total_invoices'] > 0) {
            $stats['invoices_growth'] = (($currentMonthStats['total_invoices'] - $lastMonthStats['total_invoices']) / $lastMonthStats['total_invoices']) * 100;
        } else {
            $stats['invoices_growth'] = $currentMonthStats['total_invoices'] > 0 ? 100 : 0;
        }

        if ($lastMonthStats['total_amount'] > 0) {
            $stats['amount_growth'] = (($currentMonthStats['total_amount'] - $lastMonthStats['total_amount']) / $lastMonthStats['total_amount']) * 100;
        } else {
            $stats['amount_growth'] = $currentMonthStats['total_amount'] > 0 ? 100 : 0;
        }

        return $stats;
    }

    private function getEnhancedStats($startDate, $endDate)
    {
        // Período anterior para comparação
        $periodDays = $startDate->diffInDays($endDate);
        $previousStartDate = $startDate->copy()->subDays($periodDays);
        $previousEndDate = $startDate->copy()->subDay();

        // Stats do período atual
        $currentRevenue = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('total');

        // Stats do período anterior
        $previousRevenue = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$previousStartDate, $previousEndDate])
            ->sum('total');

        // Cálculo do crescimento
        $revenueGrowth = $previousRevenue > 0
            ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100
            : 0;

        // Dados das cotações
        $quotesStats = $this->getQuotesStats($startDate, $endDate);

        return [
            // Receita
            'total_revenue' => $currentRevenue,
            'revenue_growth' => $revenueGrowth,

            // Faturas
            'total_invoices' => Invoice::count(),
            'total_pending' => Invoice::whereIn('status', ['sent', 'overdue'])->sum('total'),
            'count_pending' => Invoice::whereIn('status', ['sent', 'overdue'])->count(),
            'total_overdue' => Invoice::where('status', 'overdue')->sum('total'),
            'count_overdue' => Invoice::where('status', 'overdue')->count(),

            // Pagamentos este mês
            'total_paid_this_month' => Invoice::where('status', 'paid')
                ->whereMonth('paid_at', Carbon::now()->month)
                ->whereYear('paid_at', Carbon::now()->year)
                ->sum('total'),
            'paid_count_this_month' => Invoice::where('status', 'paid')
                ->whereMonth('paid_at', Carbon::now()->month)
                ->whereYear('paid_at', Carbon::now()->year)
                ->count(),

            // Contadores por status
            'paid_count' => Invoice::where('status', 'paid')->count(),
            'sent_count' => Invoice::where('status', 'sent')->count(),
            'draft_count' => Invoice::where('status', 'draft')->count(),

            // Cotações
            'quotes_count' => $quotesStats['total_count'],
            'quotes_total_value' => $quotesStats['total_value'],
            'quotes_pending_count' => $quotesStats['pending_count'],
            'quotes_accepted_count' => $quotesStats['accepted_count'],
            'conversion_rate' => $quotesStats['conversion_rate'],

            // Clientes
            'total_clients' => Client::count(),
            'active_clients' => Client::whereHas('invoices', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })->count(),
        ];
    }

    private function getQuotesStats($startDate, $endDate)
    {
        $totalQuotes = Quote::whereBetween('created_at', [$startDate, $endDate])->count();
        $convertedQuotes = Quote::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('converted_to_invoice_at')
            ->count();

        return [
            'total_count' => Quote::whereIn('status', ['sent', 'accepted'])->count(),
            'total_value' => Quote::whereIn('status', ['sent', 'accepted'])->sum('total'),
            'pending_count' => Quote::where('status', 'sent')->count(),
            'accepted_count' => Quote::where('status', 'accepted')->count(),
            'rejected_count' => Quote::where('status', 'rejected')->count(),
            'expired_count' => Quote::where('status', 'expired')->count(),
            'converted_count' => $convertedQuotes,
            'conversion_rate' => $totalQuotes > 0 ? round(($convertedQuotes / $totalQuotes) * 100, 1) : 0,
            'average_value' => Quote::avg('total') ?? 0,
        ];
    }

    private function getTopClients($startDate, $endDate, $limit = 5)
    {
        return Client::select('clients.*')
            ->withSum(['invoices as total_invoiced' => function($query) use ($startDate, $endDate) {
                $query->where('status', 'paid')
                      ->whereBetween('paid_at', [$startDate, $endDate]);
            }], 'total')
            ->withCount(['invoices as invoices_count' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->having('total_invoiced', '>', 0)
            ->orderBy('total_invoiced', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getEnhancedChartData()
    {
        $months = [];
        $revenue = [];
        $pending = [];
        $quotes = [];

        // Últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M/Y');

            $months[] = $monthName;

            // Receita do mês
            $monthRevenue = Invoice::where('status', 'paid')
                ->whereMonth('paid_at', $date->month)
                ->whereYear('paid_at', $date->year)
                ->sum('total');

            // Pendente do mês
            $monthPending = Invoice::whereIn('status', ['sent', 'overdue'])
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total');

            // Cotações do mês
            $monthQuotes = Quote::whereIn('status', ['sent', 'accepted'])
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total');

            $revenue[] = floatval($monthRevenue);
            $pending[] = floatval($monthPending);
            $quotes[] = floatval($monthQuotes);
        }

        return [
            'months' => $months,
            'revenue' => $revenue,
            'pending' => $pending,
            'quotes' => $quotes,
        ];
    }

    private function getStartDate($period, $customStartDate = null)
    {
        if ($period === 'custom' && $customStartDate) {
            return Carbon::parse($customStartDate)->startOfDay();
        }

        switch ($period) {
            case 'weekly':
                return Carbon::now()->startOfWeek();
            case 'monthly':
                return Carbon::now()->startOfMonth();
            case 'quarterly':
                return Carbon::now()->startOfQuarter();
            case 'yearly':
                return Carbon::now()->startOfYear();
            default:
                return Carbon::now()->startOfMonth();
        }
    }

    private function getEndDate($period, $customEndDate = null)
    {
        if ($period === 'custom' && $customEndDate) {
            return Carbon::parse($customEndDate)->endOfDay();
        }

        return Carbon::now()->endOfDay();
    }

    public function reports(Request $request)
    {

        // return "hhe";
        $period = $request->get('period', 'monthly');
        $startDate = $this->getStartDate($period, $request->get('start_date'));
        $endDate = $this->getEndDate($period, $request->get('end_date'));

        $invoiceStats = $this->getDetailedInvoiceStats($startDate, $endDate);
        $quoteStats = $this->getDetailedQuoteStats($startDate, $endDate);
        $clientStats = $this->getDetailedClientStats($startDate, $endDate);
        $performanceMetrics = $this->getPerformanceMetrics($startDate, $endDate);

        return view('billing.reports', compact(
            'period',
            'startDate',
            'endDate',
            'invoiceStats',
            'quoteStats',
            'clientStats',
            'performanceMetrics'
        ));
    }

    private function getDetailedInvoiceStats($startDate, $endDate)
    {
        $baseQuery = Invoice::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total_count' => $baseQuery->count(),
            'total_amount' => $baseQuery->sum('total'),
            'paid_count' => Invoice::whereBetween('paid_at', [$startDate, $endDate])
                ->where('status', 'paid')->count(),
            'paid_amount' => Invoice::whereBetween('paid_at', [$startDate, $endDate])
                ->where('status', 'paid')->sum('total'),
            'pending_count' => $baseQuery->where('status', 'sent')->count(),
            'pending_amount' => $baseQuery->where('status', 'sent')->sum('total'),
            'overdue_count' => $baseQuery->where('status', 'overdue')->count(),
            'overdue_amount' => $baseQuery->where('status', 'overdue')->sum('total'),
            'draft_count' => $baseQuery->where('status', 'draft')->count(),
            'cancelled_count' => $baseQuery->where('status', 'cancelled')->count(),
            'average_value' => $baseQuery->avg('total') ?? 0,
            'average_payment_time' => $this->getAveragePaymentTime($startDate, $endDate),
            'by_month' => $baseQuery
                ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count, SUM(total) as total')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get(),
        ];
    }

    private function getDetailedQuoteStats($startDate, $endDate)
    {
        $baseQuery = Quote::whereBetween('created_at', [$startDate, $endDate]);
        $totalQuotes = $baseQuery->count();
        $convertedQuotes = $baseQuery->whereNotNull('converted_to_invoice_at')->count();

        return [
            'total_count' => $totalQuotes,
            'total_amount' => $baseQuery->sum('total'),
            'sent_count' => $baseQuery->where('status', 'sent')->count(),
            'accepted_count' => $baseQuery->where('status', 'accepted')->count(),
            'rejected_count' => $baseQuery->where('status', 'rejected')->count(),
            'expired_count' => $baseQuery->where('status', 'expired')->count(),
            'draft_count' => $baseQuery->where('status', 'draft')->count(),
            'converted_count' => $convertedQuotes,
            'conversion_rate' => $totalQuotes > 0 ? round(($convertedQuotes / $totalQuotes) * 100, 2) : 0,
            'average_value' => $baseQuery->avg('total') ?? 0,
            'average_response_time' => $this->getAverageQuoteResponseTime($startDate, $endDate),
            'by_month' => $baseQuery
                ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count, SUM(total) as total')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get(),
        ];
    }

    private function getDetailedClientStats($startDate, $endDate)
    {
        $topClientsByRevenue = Client::withSum(['invoices as revenue' => function($query) use ($startDate, $endDate) {
                $query->where('status', 'paid')
                      ->whereBetween('paid_at', [$startDate, $endDate]);
            }], 'total')
            ->having('revenue', '>', 0)
            ->orderBy('revenue', 'desc')
            ->limit(10)
            ->get();

        $topClientsByInvoices = Client::withCount(['invoices as invoice_count' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->having('invoice_count', '>', 0)
            ->orderBy('invoice_count', 'desc')
            ->limit(10)
            ->get();

        return [
            'new_clients' => Client::whereBetween('created_at', [$startDate, $endDate])->count(),
            'active_clients' => Client::whereHas('invoices', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })->count(),
            'clients_with_overdue' => Client::whereHas('invoices', function($query) {
                $query->where('status', 'overdue');
            })->count(),
            'top_clients_by_revenue' => $topClientsByRevenue,
            'top_clients_by_invoices' => $topClientsByInvoices,
            'average_client_value' => $topClientsByRevenue->avg('revenue') ?? 0,
        ];
    }

    private function getPerformanceMetrics($startDate, $endDate)
    {
        $totalInvoices = Invoice::whereBetween('created_at', [$startDate, $endDate])->count();
        $paidInvoices = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])->count();

        return [
            'collection_rate' => $totalInvoices > 0 ? round(($paidInvoices / $totalInvoices) * 100, 2) : 0,
            'average_invoice_value' => Invoice::whereBetween('created_at', [$startDate, $endDate])->avg('total') ?? 0,
            'overdue_rate' => $totalInvoices > 0 ? round((Invoice::where('status', 'overdue')->count() / $totalInvoices) * 100, 2) : 0,
            'revenue_per_client' => $this->getRevenuePerClient($startDate, $endDate),
        ];
    }

    private function getAveragePaymentTime($startDate, $endDate)
    {
        $avgDays = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->whereNotNull('due_date')
            ->selectRaw('AVG(DATEDIFF(paid_at, created_at)) as avg_days')
            ->value('avg_days');

        return round($avgDays ?? 0, 1);
    }

    private function getAverageQuoteResponseTime($startDate, $endDate)
    {
        $avgDays = Quote::whereIn('status', ['accepted', 'rejected'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('responded_at')
            ->selectRaw('AVG(DATEDIFF(responded_at, created_at)) as avg_days')
            ->value('avg_days');

        return round($avgDays ?? 0, 1);
    }

    private function getRevenuePerClient($startDate, $endDate)
    {
        $totalRevenue = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('total');

        $activeClients = Client::whereHas('invoices', function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })->count();

        return $activeClients > 0 ? round($totalRevenue / $activeClients, 2) : 0;
    }

  public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:excel,pdf',
            'period' => 'nullable|in:daily,weekly,monthly,quarterly,yearly,custom',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $format = $request->get('format');
        $period = $request->get('period', 'monthly');
        $startDate = $this->getStartDate($period, $request->get('start_date'));
        $endDate = $this->getEndDate($period, $request->get('end_date'));

        try {
            $data = $this->getExportData($startDate, $endDate);

            if ($format === 'excel') {
                return $this->exportToExcel($data, $startDate, $endDate);
            } else {
                return $this->exportToPdf($data, $startDate, $endDate);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar exportação: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDashboardStats(Request $request)
{
    $period = $request->get('period', 'monthly');
    $startDate = $this->getStartDate($period, $request->get('start_date'));
    $endDate = $this->getEndDate($period, $request->get('end_date'));

    $stats = $this->getEnhancedStats($startDate, $endDate);

    return response()->json($stats);
}

public function getChartData(Request $request)
{
    $chartData = $this->getEnhancedChartData();

    return response()->json($chartData);
}


}
