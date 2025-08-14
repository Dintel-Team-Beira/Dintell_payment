<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\User;
use Illuminate\Http\Request;

class AdminActivitiesController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminActivity::with(['admin'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhereHas('admin', function($adminQuery) use ($search) {
                      $adminQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $activities = $query->paginate(20)->withQueryString();

        // Dados para filtros
        $admins = User::where('is_super_admin', true)
            ->orWhere('role', 'admin')
            ->orderBy('name')
            ->get();

        $categories = [
            AdminActivity::CATEGORY_USER_MANAGEMENT => 'Gestão de Usuários',
            AdminActivity::CATEGORY_COMPANY_MANAGEMENT => 'Gestão de Empresas',
            AdminActivity::CATEGORY_INVOICE_MANAGEMENT => 'Gestão de Faturas',
            AdminActivity::CATEGORY_SYSTEM_CONFIG => 'Configuração do Sistema',
            AdminActivity::CATEGORY_SECURITY => 'Segurança',
            AdminActivity::CATEGORY_DATA_EXPORT => 'Exportação de Dados',
        ];

        $severities = [
            AdminActivity::SEVERITY_LOW => 'Baixa',
            AdminActivity::SEVERITY_MEDIUM => 'Média',
            AdminActivity::SEVERITY_HIGH => 'Alta',
            AdminActivity::SEVERITY_CRITICAL => 'Crítica',
        ];

        // Estatísticas
        $stats = [
            'today' => AdminActivity::whereDate('created_at', today())->count(),
            'this_week' => AdminActivity::where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => AdminActivity::whereMonth('created_at', now()->month)->count(),
            'critical' => AdminActivity::where('severity', AdminActivity::SEVERITY_CRITICAL)
                         ->where('created_at', '>=', now()->subDays(7))
                         ->count(),
        ];

        return view('admin.activities.index', compact(
            'activities', 'admins', 'categories', 'severities', 'stats'
        ));
    }

    public function show(AdminActivity $activity)
    {
        $activity->load(['admin']);

        return view('admin.activities.show', compact('activity'));
    }

    public function export(Request $request)
    {
        $query = AdminActivity::with(['admin'])
            ->orderBy('created_at', 'desc');

        // Aplicar mesmos filtros da listagem
        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $activities = $query->limit(5000)->get(); // Limitar exportação

        $filename = 'admin_activities_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');

            // Cabeçalho do CSV
            fputcsv($file, [
                'Data/Hora',
                'Administrador',
                'Email',
                'Ação',
                'Descrição',
                'Categoria',
                'Severidade',
                'IP',
                'URL',
                'Método'
            ]);

            // Dados
            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->formatted_created_at,
                    $activity->admin->name ?? 'Usuário Deletado',
                    $activity->admin->email ?? '',
                    $activity->action,
                    $activity->description,
                    $activity->category_label,
                    $activity->severity_label,
                    $activity->ip_address,
                    $activity->url,
                    $activity->method
                ]);
            }

            fclose($file);
        };

        // Log da exportação
        AdminActivity::log(
            'admin_activities_exported',
            'Exportou log de atividades administrativas',
            [
                'total_records' => $activities->count(),
                'filters' => $request->all(),
                'format' => 'csv'
            ],
            AdminActivity::SEVERITY_MEDIUM,
            AdminActivity::CATEGORY_DATA_EXPORT
        );

        return response()->stream($callback, 200, $headers);
    }

    public function clear(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $cutoffDate = now()->subDays($request->days);
        $deletedCount = AdminActivity::where('created_at', '<', $cutoffDate)->delete();

        // Log da limpeza
        AdminActivity::logSystemAction(
            'admin_activities_cleared',
            "Limpou {$deletedCount} registros de atividades administrativas",
            [
                'deleted_count' => $deletedCount,
                'cutoff_days' => $request->days,
                'cutoff_date' => $cutoffDate->toDateString(),
            ]
        );

        return back()->with('success', "Foram removidos {$deletedCount} registros de atividades.");
    }

    public function dashboard()
    {
        // Atividades recentes
        $recentActivities = AdminActivity::with(['admin'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Estatísticas por severidade (últimos 30 dias)
        $severityStats = AdminActivity::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('severity, COUNT(*) as count')
            ->groupBy('severity')
            ->pluck('count', 'severity')
            ->toArray();

        // Atividades por categoria (últimos 30 dias)
        $categoryStats = AdminActivity::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('category, COUNT(*) as count')
            ->whereNotNull('category')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        // Atividade por dia (últimos 7 dias)
        $dailyActivity = AdminActivity::where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Top admins por atividade (últimos 30 dias)
        $topAdmins = AdminActivity::with(['admin'])
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('admin_id, COUNT(*) as activity_count')
            ->groupBy('admin_id')
            ->orderBy('activity_count', 'desc')
            ->limit(5)
            ->get();

        // Atividades críticas recentes
        $criticalActivities = AdminActivity::with(['admin'])
            ->where('severity', AdminActivity::SEVERITY_CRITICAL)
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.activities.dashboard', compact(
            'recentActivities',
            'severityStats',
            'categoryStats',
            'dailyActivity',
            'topAdmins',
            'criticalActivities'
        ));
    }
}

// No CompaniesController
// $this->logCompanyActivity('created', $company, 'Criou nova empresa');

// No UsersController
// $this->logUserActivity('suspended', $user, 'Suspendeu usuário');

// Ações de segurança
// $this->logSecurityActivity('Tentativa de acesso não autorizado');
