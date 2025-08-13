<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Invoice;

use App\Traits\LogsActivity;
class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['company']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('company', function($companyQuery) use ($search) {
                      $companyQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $companies = Company::where('status', 'active')->orderBy('name')->get();

        // Estatísticas
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'admins' => User::where('role', 'admin')->count(),
            'companies' => User::whereNotNull('company_id')->distinct('company_id')->count('company_id'),
        ];

        return view('admin.users.index', compact('users', 'companies', 'stats'));
    }

    public function create()
    {
        $companies = Company::where('status', 'active')->orderBy('name')->get();
        return view('admin.users.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,user',
            'company_id' => 'nullable|exists:companies,id',
            'is_active' => 'boolean',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userData = $request->except(['password', 'password_confirmation', 'avatar']);
        $userData['password'] = Hash::make($request->password);
        $userData['is_active'] = $request->boolean('is_active', true);

        // Upload avatar se fornecido
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $userData['avatar'] = $avatarPath;
        }

        $user = User::create($userData);

        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuário criado com sucesso!');
    }

   public function show(User $user)
{
    // Carregamento correto das relações
    $user->load(['company']);

    // Estatísticas do usuário baseadas na estrutura real
    $userStats = [
        // Se o usuário tem company_id, buscar dados da empresa
        'invoices_count' => $user->company_id ?
            \App\Models\Invoice::where('company_id', $user->company_id)->count() : 0,

        'quotes_count' => $user->company_id ?
            \App\Models\Quote::where('company_id', $user->company_id)->count() : 0,

        'total_revenue' => $user->company_id ?
            \App\Models\Invoice::where('company_id', $user->company_id)
                ->where('status', 'paid')
                ->sum('total') : 0,

        'last_login' => $user->last_login_at,

        // Estatísticas adicionais se for admin da empresa
        'clients_count' => $user->company_id ?
            \App\Models\Client::where('company_id', $user->company_id)->count() : 0,

        'products_count' => $user->company_id ?
            \App\Models\Product::where('company_id', $user->company_id)->count() : 0,

        'services_count' => $user->company_id ?
            \App\Models\Service::where('company_id', $user->company_id)->count() : 0,
    ];

    // Atividade recente do usuário (se for super admin)
    $recentActivity = [];
    if ($user->is_super_admin) {
        $recentActivity = \App\Models\AdminActivity::where('admin_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();
    }

    // Dados da empresa se o usuário pertencer a uma
    $companyStats = null;
    if ($user->company_id && $user->company) {
        $companyStats = [
            'status' => $user->company->status,
            'subscription_plan' => $user->company->subscription_plan,
            'trial_ends_at' => $user->company->trial_ends_at,
            'users_count' => $user->company->users()->count(),
            'max_users' => $user->company->max_users,
        ];
    }
     $this->logUserActivity('suspended', $user, 'Suspendeu usuário');
    return view('admin.users.show', compact('user', 'userStats', 'recentActivity', 'companyStats'));
}

    public function edit(User $user)
    {
        $companies = Company::where('status', 'active')->orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'companies'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,user',
            'company_id' => 'nullable|exists:companies,id',
            'is_active' => 'boolean',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userData = $request->except(['password', 'password_confirmation', 'avatar']);
        $userData['is_active'] = $request->boolean('is_active');

        // Atualizar senha apenas se fornecida
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Upload avatar se fornecido
        if ($request->hasFile('avatar')) {
            // Deletar avatar anterior se existir
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $userData['avatar'] = $avatarPath;
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        // Verificar se não é o próprio usuário logado
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                            ->with('error', 'Você não pode deletar sua própria conta!');
        }

        // Deletar avatar se existir
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuário deletado com sucesso!');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'ativado' : 'desativado';

        return response()->json([
            'success' => true,
            'message' => "Usuário {$status} com sucesso!",
            'is_active' => $user->is_active
        ]);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $userIds = $request->user_ids;

        // Remover o usuário atual da lista para evitar auto-modificação
        $userIds = array_filter($userIds, function($id) {
            return $id != auth()->id();
        });

        switch ($request->action) {
            case 'activate':
                User::whereIn('id', $userIds)->update(['is_active' => true]);
                $message = 'Usuários ativados com sucesso!';
                break;

            case 'deactivate':
                User::whereIn('id', $userIds)->update(['is_active' => false]);
                $message = 'Usuários desativados com sucesso!';
                break;

            case 'delete':
                User::whereIn('id', $userIds)->delete();
                $message = 'Usuários deletados com sucesso!';
                break;
        }

        return redirect()->route('admin.users.index')->with('success', $message);
    }

    public function export(Request $request)
    {
        $filename = 'usuarios_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($request) {
            $file = fopen('php://output', 'w');

            // Cabeçalhos CSV
            fputcsv($file, [
                'ID', 'Nome', 'Email', 'Função', 'Empresa', 'Telefone',
                'Status', 'Criado em', 'Último Login'
            ]);

            // Dados
            $query = User::with('company');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $query->chunk(100, function($users) use ($file) {
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->role,
                        $user->company?->name ?? 'N/A',
                        $user->phone ?? 'N/A',
                        $user->is_active ? 'Ativo' : 'Inativo',
                        $user->created_at->format('d/m/Y H:i'),
                        $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca'
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
