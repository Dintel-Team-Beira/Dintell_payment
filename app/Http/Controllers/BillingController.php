<?php

// Controller: BillingController.php
// app/Http/Controllers/BillingController.php
namespace App\Http\Controllers;

use App\Models\BillingSetting;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BillingController extends Controller
{

    public function index()
    {
        $settings = BillingSetting::getSettings();

        return view('settings.index', compact('settings'));
    }

    public function dashboard()
    {
        // Estatísticas gerais
        $stats = [
            'total_invoices' => Invoice::count(),
            'total_quotes' => Quote::count(),
            'total_pending' => Invoice::whereIn('status', ['sent', 'overdue'])->sum('total'),
            'total_overdue' => Invoice::where('status', 'overdue')->sum('total'),
            'count_overdue' => Invoice::where('status', 'overdue')->count(),
            'total_paid_this_month' => Invoice::where('status', 'paid')
                ->whereMonth('paid_at', Carbon::now()->month)
                ->whereYear('paid_at', Carbon::now()->year)
                ->sum('total'),
            'paid_count' => Invoice::where('status', 'paid')->count(),
            'sent_count' => Invoice::where('status', 'sent')->count(),
            'draft_count' => Invoice::where('status', 'draft')->count(),
        ];

        // Faturas vencidas (máximo 5)
        $overdueInvoices = Invoice::with('client')
            ->where('status', 'overdue')
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        // Faturas recentes (máximo 5)
        $recentInvoices = Invoice::with('client')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Dados para gráficos (últimos 6 meses)
        $chartData = $this->getChartData();

        return view('billing.dashboard', compact(
            'stats',
            'overdueInvoices',
            'recentInvoices',
            'chartData'
        ));
    }

    private function getChartData()
    {
        $months = [];
        $paid = [];
        $pending = [];

        // Últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M/Y');

            $months[] = $monthName;

            // Total pago no mês
            $paidAmount = Invoice::where('status', 'paid')
                ->whereMonth('paid_at', $date->month)
                ->whereYear('paid_at', $date->year)
                ->sum('total');

            // Total pendente no final do mês
            $pendingAmount = Invoice::whereIn('status', ['sent', 'overdue'])
                ->whereMonth('invoice_date', $date->month)
                ->whereYear('invoice_date', $date->year)
                ->sum('total');

            $paid[] = floatval($paidAmount);
            $pending[] = floatval($pendingAmount);
        }

        return [
            'months' => $months,
            'paid' => $paid,
            'pending' => $pending
        ];
    }

    public function reports(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $startDate = $this->getStartDate($period);
        $endDate = Carbon::now();

        $invoiceStats = $this->getInvoiceStats($startDate, $endDate);
        $quoteStats = $this->getQuoteStats($startDate, $endDate);
        $clientStats = $this->getClientStats($startDate, $endDate);

        return view('billing.reports', compact(
            'period',
            'startDate',
            'endDate',
            'invoiceStats',
            'quoteStats',
            'clientStats'
        ));
    }

    private function getStartDate($period)
    {
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

    private function getInvoiceStats($startDate, $endDate)
    {
        $invoices = Invoice::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total_count' => $invoices->count(),
            'total_amount' => $invoices->sum('total'),
            'paid_count' => Invoice::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'paid')->count(),
            'paid_amount' => Invoice::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'paid')->sum('total'),
            'pending_count' => Invoice::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'sent')->count(),
            'pending_amount' => Invoice::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'sent')->sum('total'),
            'overdue_count' => Invoice::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'overdue')->count(),
            'overdue_amount' => Invoice::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'overdue')->sum('total'),
            'average_value' => Invoice::whereBetween('created_at', [$startDate, $endDate])
                ->avg('total'),
            'by_month' => Invoice::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count, SUM(total) as total')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get()
        ];
    }

    private function getQuoteStats($startDate, $endDate)
    {
        $quotes = Quote::whereBetween('created_at', [$startDate, $endDate]);

        $totalQuotes = $quotes->count();
        $convertedQuotes = Quote::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('converted_to_invoice_at')
            ->count();

        return [
            'total_count' => $totalQuotes,
            'total_amount' => $quotes->sum('total'),
            'accepted_count' => Quote::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'accepted')->count(),
            'accepted_amount' => Quote::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'accepted')->sum('total'),
            'converted_count' => $convertedQuotes,
            'conversion_rate' => $totalQuotes > 0 ? round(($convertedQuotes / $totalQuotes) * 100, 2) : 0,
            'average_value' => Quote::whereBetween('created_at', [$startDate, $endDate])
                ->avg('total')
        ];
    }

    private function getClientStats($startDate, $endDate)
    {
        // Top 10 clientes por valor faturado
        $topClients = Client::withSum(['invoices' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'paid');
            }], 'total')
            ->orderBy('invoices_sum_total', 'desc')
            ->limit(10)
            ->get();

        // Novos clientes no período
        $newClients = Client::whereBetween('created_at', [$startDate, $endDate])->count();

        // Clientes com faturas vencidas
        $clientsWithOverdue = Client::whereHas('invoices', function($query) {
                $query->where('status', 'overdue');
            })->count();

        return [
            'top_clients' => $topClients,
            'new_clients' => $newClients,
            'clients_with_overdue' => $clientsWithOverdue,
            'total_active_clients' => Client::whereHas('invoices', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })->count()
        ];
    }
}