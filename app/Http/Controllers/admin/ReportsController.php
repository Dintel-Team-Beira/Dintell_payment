<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function revenue(Request $request)
    {
        $period = $request->get('period', 'this_month');
        $company_id = $request->get('company_id');

        // Definir datas baseado no período
        $dateRange = $this->getDateRange($period);

        // Query base para receita
        $revenueQuery = Invoice::where('status', 'paid')
                              ->whereBetween('paid_at', [$dateRange['start'], $dateRange['end']]);

        if ($company_id) {
            $revenueQuery->where('company_id', $company_id);
        }

        // Métricas principais - CORRIGIDO: usar 'total' em vez de 'total_amount'
        $metrics = [
            'total_revenue' => $revenueQuery->sum('total'),
            'total_invoices' => $revenueQuery->count(),
            'avg_invoice_value' => $revenueQuery->avg('total'),
            'total_tax' => $revenueQuery->sum('tax_amount'),
        ];

        // Receita por período (diário/mensal dependendo do range)
        $revenueByPeriod = $this->getRevenueByPeriod($dateRange, $company_id);

        // Receita por empresa
        $revenueByCompany = $this->getRevenueByCompany($dateRange);

        // Receita por status de fatura
        $revenueByStatus = $this->getRevenueByStatus($dateRange, $company_id);

        // Top clientes por receita
        $topClients = $this->getTopClientsByRevenue($dateRange, $company_id);

        // Comparação com período anterior
        $previousPeriod = $this->getPreviousDateRange($period);
        $previousRevenue = Invoice::where('status', 'paid')
                                 ->whereBetween('paid_at', [$previousPeriod['start'], $previousPeriod['end']])
                                 ->when($company_id, function($q) use ($company_id) {
                                     return $q->where('company_id', $company_id);
                                 })
                                 ->sum('total'); // CORRIGIDO

        $revenueGrowth = $previousRevenue > 0
                        ? (($metrics['total_revenue'] - $previousRevenue) / $previousRevenue) * 100
                        : 0;

        // Dados para filtros
        $companies = Company::where('status', 'active')->orderBy('name')->get();

        return view('admin.reports.revenue', compact(
            'metrics', 'revenueByPeriod', 'revenueByCompany', 'revenueByStatus',
            'topClients', 'revenueGrowth', 'period', 'companies', 'company_id'
        ));
    }

    public function clients(Request $request)
    {
        $period = $request->get('period', 'this_month');
        $company_id = $request->get('company_id');

        $dateRange = $this->getDateRange($period);

        // Query base para clientes
        $clientsQuery = Client::query();

        if ($company_id) {
            $clientsQuery->where('company_id', $company_id);
        }

        // Métricas de clientes
        $metrics = [
            'total_clients' => $clientsQuery->count(),
            'active_clients' => $clientsQuery->where('is_active', true)->count(),
            'new_clients' => $clientsQuery->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count(),
            'clients_with_invoices' => $clientsQuery->whereHas('invoices')->count(),
        ];

        // Clientes por empresa
        $clientsByCompany = Client::with('company')
                                  ->select('company_id', DB::raw('COUNT(*) as client_count'))
                                  ->groupBy('company_id')
                                  ->orderBy('client_count', 'desc')
                                  ->get();

        // Top clientes por número de faturas
        $topClientsByInvoices = Client::withCount(['invoices' => function($query) use ($dateRange) {
                                        $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                                    }])
                                    ->when($company_id, function($q) use ($company_id) {
                                        return $q->where('company_id', $company_id);
                                    })
                                    ->orderBy('invoices_count', 'desc')
                                    ->limit(10)
                                    ->get();

        // Novos clientes por período
        $newClientsByPeriod = $this->getNewClientsByPeriod($dateRange, $company_id);

        // Clientes inativos
        $inactiveClients = Client::where('is_active', false)
                                ->when($company_id, function($q) use ($company_id) {
                                    return $q->where('company_id', $company_id);
                                })
                                ->with('company')
                                ->orderBy('updated_at', 'desc')
                                ->limit(20)
                                ->get();

        // Taxa de retenção (clientes que fizeram pelo menos 2 faturas)
        $retentionRate = $this->getClientRetentionRate($dateRange, $company_id);

        $companies = Company::where('status', 'active')->orderBy('name')->get();

        return view('admin.reports.clients', compact(
            'metrics', 'clientsByCompany', 'topClientsByInvoices', 'newClientsByPeriod',
            'inactiveClients', 'retentionRate', 'period', 'companies', 'company_id'
        ));
    }

    public function usage(Request $request)
    {
        $period = $request->get('period', 'this_month');
        $company_id = $request->get('company_id');

        $dateRange = $this->getDateRange($period);

        // Métricas de uso do sistema
        $metrics = [
            'total_logins' => $this->getTotalLogins($dateRange, $company_id),
            'active_users' => $this->getActiveUsers($dateRange, $company_id),
            'invoices_created' => $this->getInvoicesCreated($dateRange, $company_id),
            'quotes_created' => $this->getQuotesCreated($dateRange, $company_id),
        ];

        // Uso por empresa
        $usageByCompany = $this->getUsageByCompany($dateRange);

        // Usuários mais ativos
        $mostActiveUsers = $this->getMostActiveUsers($dateRange, $company_id);

        // Atividade por período
        $activityByPeriod = $this->getActivityByPeriod($dateRange, $company_id);

        // Funcionalidades mais usadas
        $featureUsage = $this->getFeatureUsage($dateRange, $company_id);

        // Performance do sistema
        $systemPerformance = [
            'avg_response_time' => $this->getAverageResponseTime($dateRange),
            'error_rate' => $this->getErrorRate($dateRange),
            'uptime' => $this->getSystemUptime(),
        ];

        $companies = Company::where('status', 'active')->orderBy('name')->get();

        return view('admin.reports.usage', compact(
            'metrics', 'usageByCompany', 'mostActiveUsers', 'activityByPeriod',
            'featureUsage', 'systemPerformance', 'period', 'companies', 'company_id'
        ));
    }

    public function export(Request $request, $type)
    {
        $period = $request->get('period', 'this_month');
        $company_id = $request->get('company_id');
        $format = $request->get('format', 'csv');

        $dateRange = $this->getDateRange($period);

        $filename = "relatorio_{$type}_" . now()->format('Y-m-d_H-i-s') . ".{$format}";

        switch ($type) {
            case 'revenue':
                return $this->exportRevenueReport($dateRange, $company_id, $filename, $format);
            case 'clients':
                return $this->exportClientsReport($dateRange, $company_id, $filename, $format);
            case 'usage':
                return $this->exportUsageReport($dateRange, $company_id, $filename, $format);
            default:
                return redirect()->back()->with('error', 'Tipo de relatório inválido!');
        }
    }

    // Métodos auxiliares para cálculos de data
    private function getDateRange($period)
    {
        switch ($period) {
            case 'today':
                return [
                    'start' => Carbon::today(),
                    'end' => Carbon::today()->endOfDay()
                ];
            case 'yesterday':
                return [
                    'start' => Carbon::yesterday(),
                    'end' => Carbon::yesterday()->endOfDay()
                ];
            case 'this_week':
                return [
                    'start' => Carbon::now()->startOfWeek(),
                    'end' => Carbon::now()->endOfWeek()
                ];
            case 'last_week':
                return [
                    'start' => Carbon::now()->subWeek()->startOfWeek(),
                    'end' => Carbon::now()->subWeek()->endOfWeek()
                ];
            case 'this_month':
                return [
                    'start' => Carbon::now()->startOfMonth(),
                    'end' => Carbon::now()->endOfMonth()
                ];
            case 'last_month':
                return [
                    'start' => Carbon::now()->subMonth()->startOfMonth(),
                    'end' => Carbon::now()->subMonth()->endOfMonth()
                ];
            case 'this_quarter':
                return [
                    'start' => Carbon::now()->startOfQuarter(),
                    'end' => Carbon::now()->endOfQuarter()
                ];
            case 'this_year':
                return [
                    'start' => Carbon::now()->startOfYear(),
                    'end' => Carbon::now()->endOfYear()
                ];
            case 'last_year':
                return [
                    'start' => Carbon::now()->subYear()->startOfYear(),
                    'end' => Carbon::now()->subYear()->endOfYear()
                ];
            default:
                return [
                    'start' => Carbon::now()->startOfMonth(),
                    'end' => Carbon::now()->endOfMonth()
                ];
        }
    }

    private function getPreviousDateRange($period)
    {
        switch ($period) {
            case 'today':
                return [
                    'start' => Carbon::yesterday(),
                    'end' => Carbon::yesterday()->endOfDay()
                ];
            case 'this_week':
                return [
                    'start' => Carbon::now()->subWeek()->startOfWeek(),
                    'end' => Carbon::now()->subWeek()->endOfWeek()
                ];
            case 'this_month':
                return [
                    'start' => Carbon::now()->subMonth()->startOfMonth(),
                    'end' => Carbon::now()->subMonth()->endOfMonth()
                ];
            case 'this_quarter':
                return [
                    'start' => Carbon::now()->subQuarter()->startOfQuarter(),
                    'end' => Carbon::now()->subQuarter()->endOfQuarter()
                ];
            case 'this_year':
                return [
                    'start' => Carbon::now()->subYear()->startOfYear(),
                    'end' => Carbon::now()->subYear()->endOfYear()
                ];
            default:
                return [
                    'start' => Carbon::now()->subMonth()->startOfMonth(),
                    'end' => Carbon::now()->subMonth()->endOfMonth()
                ];
        }
    }

    // Métodos para dados de receita - CORRIGIDOS
    private function getRevenueByPeriod($dateRange, $company_id = null)
    {
        $query = Invoice::where('status', 'paid')
                       ->whereBetween('paid_at', [$dateRange['start'], $dateRange['end']]);

        if ($company_id) {
            $query->where('company_id', $company_id);
        }

        return $query->selectRaw('DATE(paid_at) as date, SUM(total) as revenue') // CORRIGIDO
                     ->groupBy('date')
                     ->orderBy('date')
                     ->get();
    }

    private function getRevenueByCompany($dateRange)
    {
        return Invoice::with('company')
                     ->where('status', 'paid')
                     ->whereBetween('paid_at', [$dateRange['start'], $dateRange['end']])
                     ->selectRaw('company_id, SUM(total) as revenue, COUNT(*) as invoice_count') // CORRIGIDO
                     ->groupBy('company_id')
                     ->orderBy('revenue', 'desc')
                     ->get();
    }

    private function getRevenueByStatus($dateRange, $company_id = null)
    {
        $query = Invoice::whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);

        if ($company_id) {
            $query->where('company_id', $company_id);
        }

        return $query->selectRaw('status, COUNT(*) as count, SUM(total) as total') // CORRIGIDO
                     ->groupBy('status')
                     ->get();
    }

    private function getTopClientsByRevenue($dateRange, $company_id = null)
    {
        $query = Invoice::with('client')
                       ->where('status', 'paid')
                       ->whereBetween('paid_at', [$dateRange['start'], $dateRange['end']]);

        if ($company_id) {
            $query->where('company_id', $company_id);
        }

        return $query->selectRaw('client_id, SUM(total) as revenue, COUNT(*) as invoice_count') // CORRIGIDO
                     ->groupBy('client_id')
                     ->orderBy('revenue', 'desc')
                     ->limit(10)
                     ->get();
    }

    // Métodos para dados de clientes
    private function getNewClientsByPeriod($dateRange, $company_id = null)
    {
        $query = Client::whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);

        if ($company_id) {
            $query->where('company_id', $company_id);
        }

        return $query->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                     ->groupBy('date')
                     ->orderBy('date')
                     ->get();
    }

    private function getClientRetentionRate($dateRange, $company_id = null)
    {
        // Clientes que fizeram mais de uma fatura no período
        $query = Client::whereHas('invoices', function($q) use ($dateRange) {
                          $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                      }, '>=', 2);

        if ($company_id) {
            $query->where('company_id', $company_id);
        }

        $repeatClients = $query->count();

        $totalClientsQuery = Client::whereHas('invoices', function($q) use ($dateRange) {
                                   $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                               });

        if ($company_id) {
            $totalClientsQuery->where('company_id', $company_id);
        }

        $totalClients = $totalClientsQuery->count();

        return $totalClients > 0 ? ($repeatClients / $totalClients) * 100 : 0;
    }

    // Métodos para dados de uso
    private function getTotalLogins($dateRange, $company_id = null)
    {
        // Este método precisaria de uma tabela de logs de login
        // Por agora, retornamos um valor simulado
        return User::when($company_id, function($q) use ($company_id) {
                     return $q->where('company_id', $company_id);
                 })
                 ->where('last_login_at', '>=', $dateRange['start'])
                 ->count() * 15; // Simulando múltiplos logins por usuário
    }

    private function getActiveUsers($dateRange, $company_id = null)
    {
        return User::when($company_id, function($q) use ($company_id) {
                     return $q->where('company_id', $company_id);
                 })
                 ->where('last_login_at', '>=', $dateRange['start'])
                 ->count();
    }

    private function getInvoicesCreated($dateRange, $company_id = null)
    {
        return Invoice::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                     ->when($company_id, function($q) use ($company_id) {
                         return $q->where('company_id', $company_id);
                     })
                     ->count();
    }

    private function getQuotesCreated($dateRange, $company_id = null)
    {
        // Assumindo que existe um model Quote
        if (class_exists('App\Models\Quote')) {
            return \App\Models\Quote::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                                   ->when($company_id, function($q) use ($company_id) {
                                       return $q->where('company_id', $company_id);
                                   })
                                   ->count();
        }
        return 0;
    }

    private function getUsageByCompany($dateRange)
    {
        return Company::withCount([
                         'invoices' => function($q) use ($dateRange) {
                             $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                         },
                         'users' => function($q) use ($dateRange) {
                             $q->where('last_login_at', '>=', $dateRange['start']);
                         }
                     ])
                     ->where('status', 'active')
                     ->orderBy('invoices_count', 'desc')
                     ->get();
    }

    private function getMostActiveUsers($dateRange, $company_id = null)
    {
        return User::withCount([
                      'invoices' => function($q) use ($dateRange) {
                          $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                      }
                  ])
                  ->when($company_id, function($q) use ($company_id) {
                      return $q->where('company_id', $company_id);
                  })
                  ->where('last_login_at', '>=', $dateRange['start'])
                  ->orderBy('invoices_count', 'desc')
                  ->limit(10)
                  ->get();
    }

    private function getActivityByPeriod($dateRange, $company_id = null)
    {
        return Invoice::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                     ->when($company_id, function($q) use ($company_id) {
                         return $q->where('company_id', $company_id);
                     })
                     ->selectRaw('DATE(created_at) as date, COUNT(*) as activity_count')
                     ->groupBy('date')
                     ->orderBy('date')
                     ->get();
    }

    private function getFeatureUsage($dateRange, $company_id = null)
    {
        // Simulando uso de funcionalidades
        return [
            'invoices' => $this->getInvoicesCreated($dateRange, $company_id),
            'quotes' => $this->getQuotesCreated($dateRange, $company_id),
            'clients' => Client::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                              ->when($company_id, function($q) use ($company_id) {
                                  return $q->where('company_id', $company_id);
                              })
                              ->count(),
            'reports' => rand(50, 200), // Simulado
        ];
    }

    private function getAverageResponseTime($dateRange)
    {
        // Simulado - em um sistema real, isso viria de logs de performance
        return rand(150, 300) . 'ms';
    }

    private function getErrorRate($dateRange)
    {
        // Simulado - em um sistema real, isso viria de logs de erro
        return rand(0, 3) . '%';
    }

    private function getSystemUptime()
    {
        // Simulado
        return '99.9%';
    }

    // Métodos de exportação - CORRIGIDOS
    private function exportRevenueReport($dateRange, $company_id, $filename, $format)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($dateRange, $company_id) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Data', 'Número Fatura', 'Empresa', 'Cliente', 'Subtotal', 'IVA', 'Total'
            ]);

            Invoice::with(['company', 'client'])
                   ->where('status', 'paid')
                   ->whereBetween('paid_at', [$dateRange['start'], $dateRange['end']])
                   ->when($company_id, function($q) use ($company_id) {
                       return $q->where('company_id', $company_id);
                   })
                   ->chunk(100, function($invoices) use ($file) {
                       foreach ($invoices as $invoice) {
                           fputcsv($file, [
                               $invoice->paid_at->format('d/m/Y'),
                               $invoice->invoice_number,
                               $invoice->company?->name ?? 'N/A',
                               $invoice->client?->name ?? 'N/A',
                               number_format($invoice->subtotal, 2, ',', '.'),
                               number_format($invoice->tax_amount, 2, ',', '.'),
                               number_format($invoice->total, 2, ',', '.') // CORRIGIDO
                           ]);
                       }
                   });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportClientsReport($dateRange, $company_id, $filename, $format)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($dateRange, $company_id) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Nome', 'Email', 'Telefone', 'Empresa', 'Total Faturas', 'Receita Total', 'Criado em'
            ]);

            Client::with(['company'])
                  ->withCount('invoices')
                  ->withSum('invoices', 'total') // CORRIGIDO
                  ->when($company_id, function($q) use ($company_id) {
                      return $q->where('company_id', $company_id);
                  })
                  ->chunk(100, function($clients) use ($file) {
                      foreach ($clients as $client) {
                          fputcsv($file, [
                              $client->name,
                              $client->email,
                              $client->phone ?? 'N/A',
                              $client->company?->name ?? 'N/A',
                              $client->invoices_count,
                              number_format($client->invoices_sum_total ?? 0, 2, ',', '.'), // CORRIGIDO
                              $client->created_at->format('d/m/Y')
                          ]);
                      }
                  });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportUsageReport($dateRange, $company_id, $filename, $format)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($dateRange, $company_id) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Usuário', 'Email', 'Empresa', 'Total Faturas', 'Último Login'
            ]);

            User::with(['company'])
                ->withCount([
                    'invoices' => function($q) use ($dateRange) {
                        $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                    }
                ])
                ->when($company_id, function($q) use ($company_id) {
                    return $q->where('company_id', $company_id);
                })
                ->chunk(100, function($users) use ($file) {
                    foreach ($users as $user) {
                        fputcsv($file, [
                            $user->name,
                            $user->email,
                            $user->company?->name ?? 'N/A',
                            $user->invoices_count,
                            $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca'
                        ]);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
