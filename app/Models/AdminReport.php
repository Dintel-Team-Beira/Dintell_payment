<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'parameters',
        'generated_by',
        'file_path',
        'status',
        'generated_at'
    ];

    protected $casts = [
        'parameters' => 'array',
        'generated_at' => 'datetime'
    ];

    const TYPES = [
        'revenue' => 'Relatório de Receita',
        'clients' => 'Relatório de Clientes',
        'usage' => 'Relatório de Uso do Sistema',
        'invoices' => 'Relatório de Faturas',
        'companies' => 'Relatório de Empresas',
        'performance' => 'Relatório de Performance'
    ];

    const STATUSES = [
        'pending' => 'Pendente',
        'processing' => 'Processando',
        'completed' => 'Concluído',
        'failed' => 'Falhou'
    ];

    // Relationships
    public function generatedBy()
    {
        return $this->belongsTo(AdminUser::class, 'generated_by');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Static methods para geração de relatórios
    public static function generateRevenueReport($startDate, $endDate, $companyId = null)
    {
        $query = AdminInvoice::query()
            ->where('status', 'paid')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $results = $query->with(['company', 'client'])
            ->selectRaw('
                DATE_FORMAT(date, "%Y-%m") as period,
                company_id,
                COUNT(*) as total_invoices,
                SUM(total_amount) as total_revenue,
                AVG(total_amount) as average_invoice,
                SUM(tax_amount) as total_tax
            ')
            ->groupBy(['period', 'company_id'])
            ->orderBy('period')
            ->get();

        $summary = [
            'total_revenue' => $results->sum('total_revenue'),
            'total_invoices' => $results->sum('total_invoices'),
            'average_invoice' => $results->avg('average_invoice'),
            'total_tax' => $results->sum('total_tax'),
            'period' => $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y')
        ];

        return [
            'summary' => $summary,
            'detailed' => $results,
            'monthly_breakdown' => self::getMonthlyBreakdown($startDate, $endDate, $companyId)
        ];
    }

    public static function generateClientsReport($startDate, $endDate)
    {
        $clientsData = DB::table('clients')
            ->leftJoin('invoices', 'clients.id', '=', 'invoices.client_id')
            ->leftJoin('companies', 'clients.company_id', '=', 'companies.id')
            ->select([
                'clients.id',
                'clients.name',
                'clients.email',
                'clients.created_at',
                'companies.name as company_name',
                DB::raw('COUNT(invoices.id) as total_invoices'),
                DB::raw('SUM(CASE WHEN invoices.status = "paid" THEN invoices.total_amount ELSE 0 END) as total_paid'),
                DB::raw('SUM(CASE WHEN invoices.status = "overdue" THEN invoices.total_amount ELSE 0 END) as total_overdue'),
                DB::raw('MAX(invoices.date) as last_invoice_date')
            ])
            ->whereBetween('clients.created_at', [$startDate, $endDate])
            ->groupBy(['clients.id', 'clients.name', 'clients.email', 'clients.created_at', 'companies.name'])
            ->orderBy('total_paid', 'desc')
            ->get();

        $summary = [
            'total_clients' => $clientsData->count(),
            'active_clients' => $clientsData->where('total_invoices', '>', 0)->count(),
            'total_revenue' => $clientsData->sum('total_paid'),
            'average_per_client' => $clientsData->count() > 0 ? $clientsData->sum('total_paid') / $clientsData->count() : 0,
            'top_clients' => $clientsData->take(10)
        ];

        return [
            'summary' => $summary,
            'detailed' => $clientsData
        ];
    }

    public static function generateUsageReport($startDate, $endDate)
    {
        // Stats das empresas
        $companiesStats = Company::select([
                'id', 'name', 'created_at', 'status',
                DB::raw('(SELECT COUNT(*) FROM users WHERE users.company_id = companies.id) as users_count'),
                DB::raw('(SELECT COUNT(*) FROM invoices WHERE invoices.company_id = companies.id AND invoices.date BETWEEN "' . $startDate . '" AND "' . $endDate . '") as invoices_count'),
                DB::raw('(SELECT SUM(total_amount) FROM invoices WHERE invoices.company_id = companies.id AND invoices.status = "paid" AND invoices.date BETWEEN "' . $startDate . '" AND "' . $endDate . '") as revenue')
            ])
            ->withCount(['invoices', 'clients'])
            ->get();

        // Stats dos usuários
        $usersStats = DB::table('users')
            ->leftJoin('companies', 'users.company_id', '=', 'companies.id')
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.last_login_at',
                'companies.name as company_name',
                DB::raw('(SELECT COUNT(*) FROM invoices WHERE invoices.created_by = users.id AND invoices.date BETWEEN "' . $startDate . '" AND "' . $endDate . '") as invoices_created')
            ])
            ->whereBetween('users.created_at', [$startDate, $endDate])
            ->orderBy('users.last_login_at', 'desc')
            ->get();

        $summary = [
            'total_companies' => $companiesStats->count(),
            'active_companies' => $companiesStats->where('status', 'active')->count(),
            'total_users' => $usersStats->count(),
            'active_users' => $usersStats->whereNotNull('last_login_at')->count(),
            'total_invoices' => $companiesStats->sum('invoices_count'),
            'total_revenue' => $companiesStats->sum('revenue')
        ];

        return [
            'summary' => $summary,
            'companies' => $companiesStats,
            'users' => $usersStats
        ];
    }

    public static function generatePerformanceReport($period = 'month')
    {
        $startDate = match($period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth()
        };

        $endDate = now();

        // Métricas de performance
        $metrics = [
            'invoices_created' => AdminInvoice::whereBetween('created_at', [$startDate, $endDate])->count(),
            'invoices_paid' => AdminInvoice::where('status', 'paid')->whereBetween('payment_date', [$startDate, $endDate])->count(),
            'revenue_generated' => AdminInvoice::where('status', 'paid')->whereBetween('payment_date', [$startDate, $endDate])->sum('total_amount'),
            'new_companies' => Company::whereBetween('created_at', [$startDate, $endDate])->count(),
            'new_clients' => Client::whereBetween('created_at', [$startDate, $endDate])->count(),
            'overdue_invoices' => AdminInvoice::overdue()->count(),
            'average_payment_time' => self::getAveragePaymentTime($startDate, $endDate)
        ];

        // Growth rates (comparação com período anterior)
        $previousPeriod = self::getPreviousPeriodDates($startDate, $endDate);
        $previousMetrics = [
            'invoices_created' => AdminInvoice::whereBetween('created_at', $previousPeriod)->count(),
            'revenue_generated' => AdminInvoice::where('status', 'paid')->whereBetween('payment_date', $previousPeriod)->sum('total_amount'),
        ];

        $growthRates = [
            'invoices_growth' => self::calculateGrowthRate($previousMetrics['invoices_created'], $metrics['invoices_created']),
            'revenue_growth' => self::calculateGrowthRate($previousMetrics['revenue_generated'], $metrics['revenue_generated'])
        ];

        return [
            'period' => $period,
            'date_range' => $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'),
            'metrics' => $metrics,
            'growth_rates' => $growthRates,
            'daily_breakdown' => self::getDailyBreakdown($startDate, $endDate)
        ];
    }

    private static function getMonthlyBreakdown($startDate, $endDate, $companyId = null)
    {
        $query = AdminInvoice::where('status', 'paid')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->selectRaw('
                DATE_FORMAT(date, "%Y-%m") as month,
                COUNT(*) as invoices_count,
                SUM(total_amount) as revenue
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->month => [
                    'invoices' => $item->invoices_count,
                    'revenue' => $item->revenue
                ]];
            });
    }

    private static function getAveragePaymentTime($startDate, $endDate)
    {
        return AdminInvoice::where('status', 'paid')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->whereNotNull('payment_date')
            ->selectRaw('AVG(DATEDIFF(payment_date, date)) as avg_days')
            ->first()
            ->avg_days ?? 0;
    }

    private static function getPreviousPeriodDates($startDate, $endDate)
    {
        $periodLength = $startDate->diffInDays($endDate);
        $previousEnd = $startDate->copy()->subDay();
        $previousStart = $previousEnd->copy()->subDays($periodLength);

        return [$previousStart, $previousEnd];
    }

    private static function calculateGrowthRate($previous, $current)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }

    private static function getDailyBreakdown($startDate, $endDate)
    {
        return AdminInvoice::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                DATE(created_at) as date,
                COUNT(*) as invoices_created,
                SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as revenue
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    // Accessors
    public function getTypeNameAttribute()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
