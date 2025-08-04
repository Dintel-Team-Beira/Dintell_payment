<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvoicesController extends Controller
{
   /**
     * Display a listing of all system invoices
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['company:id,name,email,logo', 'client:id,name,email']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('company', function ($companyQuery) use ($search) {
                      $companyQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('client', function ($clientQuery) use ($search) {
                      $clientQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Company filter
        if ($request->filled('company')) {
            $query->where('company_id', $request->company);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filters
        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        // Amount range filters
        if ($request->filled('amount_min')) {
            $query->where('total', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('total', '<=', $request->amount_max);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(15);

        // Add computed attributes
        $invoices->getCollection()->transform(function ($invoice) {
            $invoice->is_overdue = $invoice->due_date < now() && !in_array($invoice->status, ['paid', 'cancelled']);
            $invoice->remaining_amount = $invoice->total - ($invoice->paid_amount ?? 0);
            $invoice->formatted_total = number_format($invoice->total, 2) . ' ' . 'MT';
            return $invoice;
        });

        // Data for filters
        $companies = Company::where('status', 'active')->orderBy('name')->get();

        // General statistics
        $stats = $this->getInvoiceStats();

        return view('admin.invoices.index', compact('invoices', 'companies', 'stats'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['user', 'company', 'client', 'items', 'payments']);

        return view('admin.invoices.show', compact('invoice'));
    }

    public function analytics(Request $request)
    {
        $period = $request->get('period', '30'); // dias
        $startDate = Carbon::now()->subDays($period);

        // Dados para gráficos
        $revenueData = $this->getRevenueAnalytics($startDate);
        $statusData = $this->getStatusAnalytics($startDate);
        $companyData = $this->getCompanyAnalytics($startDate);
        $monthlyData = $this->getMonthlyAnalytics();

        // Top empresas por receita
        $topCompanies = Invoice::with('company')
            ->where('status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('company_id, SUM(total) as total_revenue, COUNT(*) as invoice_count')
            ->groupBy('company_id')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        // Top usuários por vendas
        $topUsers = Invoice::with('user')
            ->where('status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('user_id, SUM(total) as total_revenue, COUNT(*) as invoice_count')
            ->groupBy('user_id')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        return view('admin.invoices.analytics', compact(
            'revenueData', 'statusData', 'companyData', 'monthlyData',
            'topCompanies', 'topUsers', 'period'
        ));
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:paid,cancelled,overdue,delete',
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:invoices,id'
        ]);

        $invoiceIds = $request->invoice_ids;

        switch ($request->action) {
            case 'paid':
                Invoice::whereIn('id', $invoiceIds)
                       ->where('status', '!=', 'paid')
                       ->update([
                           'status' => 'paid',
                           'paid_at' => now()
                       ]);
                $message = 'Faturas marcadas como pagas!';
                break;

            case 'cancelled':
                Invoice::whereIn('id', $invoiceIds)
                       ->where('status', '!=', 'paid')
                       ->update(['status' => 'cancelled']);
                $message = 'Faturas canceladas!';
                break;

            case 'overdue':
                Invoice::whereIn('id', $invoiceIds)
                       ->where('status', '!=', 'paid')
                       ->update(['status' => 'overdue']);
                $message = 'Faturas marcadas como vencidas!';
                break;

            case 'delete':
                Invoice::whereIn('id', $invoiceIds)
                       ->where('status', '!=', 'paid')
                       ->delete();
                $message = 'Faturas excluídas!';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    public function export(Request $request)
    {
        $query = Invoice::with(['company:id,name', 'client:id,name']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('company', function ($companyQuery) use ($search) {
                      $companyQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('client', function ($clientQuery) use ($search) {
                      $clientQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('company')) {
            $query->where('company_id', $request->company);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        $filename = 'invoices_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($query) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'Número da Fatura',
                'Empresa',
                'Cliente',
                'Usuário',
                'Data da Fatura',
                'Data de Vencimento',
                'Status',
                'Subtotal',
                'Imposto',
                'Total',
                'Data de Pagamento',
                'Criado em'
            ]);

            // CSV Data
            $query->chunk(100, function ($invoices) use ($file) {
                foreach ($invoices as $invoice) {
                    fputcsv($file, [
                        $invoice->invoice_number ?? 'N/A',
                        $invoice->company?->name ?? 'N/A',
                        $invoice->client?->name ?? 'N/A',
                        $invoice->user?->name ?? 'N/A',
                        $invoice->invoice_date->format('d/m/Y'),
                        $invoice->due_date->format('d/m/Y'),
                        ucfirst($invoice->status),
                        number_format($invoice->subtotal, 2, ',', '.'),
                        number_format($invoice->tax_amount, 2, ',', '.'),
                        number_format($invoice->total, 2, ',', '.'), // CORRIGIDO: era total_amount
                        $invoice->paid_at ? $invoice->paid_at->format('d/m/Y') : 'N/A',
                        $invoice->created_at->format('d/m/Y H:i')
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getInvoiceStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            'total' => Invoice::count(),
            'pending' => Invoice::where('status', 'pending')->count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'overdue' => Invoice::where('status', 'overdue')->count(),
            'cancelled' => Invoice::where('status', 'cancelled')->count(),
            'today_revenue' => Invoice::where('status', 'paid')->whereDate('paid_at', $today)->sum('total'), // CORRIGIDO
            'month_revenue' => Invoice::where('status', 'paid')->where('paid_at', '>=', $thisMonth)->sum('total'), // CORRIGIDO
            'last_month_revenue' => Invoice::where('status', 'paid')
                                          ->whereBetween('paid_at', [$lastMonth, $thisMonth])
                                          ->sum('total'), // CORRIGIDO
            'avg_invoice_value' => Invoice::where('status', 'paid')->avg('total'), // CORRIGIDO
        ];
    }

    private function getRevenueAnalytics($startDate)
    {
        return Invoice::where('status', 'paid')
                     ->where('paid_at', '>=', $startDate)
                     ->selectRaw('DATE(paid_at) as date, SUM(total) as revenue') // CORRIGIDO
                     ->groupBy('date')
                     ->orderBy('date')
                     ->get();
    }

    private function getStatusAnalytics($startDate)
    {
        return Invoice::where('created_at', '>=', $startDate)
                     ->selectRaw('status, COUNT(*) as count, SUM(total) as total') // CORRIGIDO
                     ->groupBy('status')
                     ->get();
    }

    private function getCompanyAnalytics($startDate)
    {
        return Invoice::with('company')
                     ->where('created_at', '>=', $startDate)
                     ->selectRaw('company_id, COUNT(*) as invoice_count, SUM(total) as total_revenue') // CORRIGIDO
                     ->groupBy('company_id')
                     ->orderBy('total_revenue', 'desc')
                     ->get();
    }

    private function getMonthlyAnalytics()
    {
        return Invoice::selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                COUNT(*) as count,
                SUM(total) as revenue, -- CORRIGIDO
                SUM(CASE WHEN status = "paid" THEN total ELSE 0 END) as paid_revenue -- CORRIGIDO
            ')
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }
}
