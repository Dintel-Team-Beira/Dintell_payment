<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
        $user->load(['company', 'invoices', 'quotes']);

        // Estatísticas do usuário
        $userStats = [
            'invoices_count' => $user->invoices()->count(),
            'quotes_count' => $user->quotes()->count(),
            'total_revenue' => $user->invoices()->where('status', 'paid')->sum('total_amount'),
            'last_login' => $user->last_login_at,
        ];

        return view('admin.users.show', compact('user', 'userStats'));
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
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
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
        if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
            \Storage::disk('public')->delete($user->avatar);
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
