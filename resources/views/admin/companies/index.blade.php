@extends('layouts.admin')

@section('title', 'Gestão de Empresas')

@section('header-actions')
<div class="flex items-center gap-x-4">
    <a href="{{ route('admin.companies.create') }}"
       class="flex items-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Nova Empresa
    </a>

    <button type="button"
            class="flex items-center px-3 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50"
            onclick="exportData()">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Exportar
    </button>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Estatísticas -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total de Empresas</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Empresas Ativas</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['active']) }}</p>
                        <p class="text-xs text-gray-500">{{ round(($stats['active'] / max($stats['total'], 1)) * 100, 1) }}% do total</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Em Trial</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['trial']) }}</p>
                        @if($stats['trial_expiring'] > 0)
                        <p class="text-xs text-orange-600">{{ $stats['trial_expiring'] }} expirando em 7 dias</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Receita Mensal</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['monthly_revenue'], 2, ',', '.') }} MT</p>
                        <p class="text-xs text-gray-500">De empresas ativas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros e Busca -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.companies.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-5">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Buscar</label>
                    <input type="text"
                           name="search"
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="Nome, email ou slug..."
                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Todos</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>Trial</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspenso</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>

                <div>
                    <label for="subscription_plan" class="block text-sm font-medium text-gray-700">Plano</label>
                    <select name="subscription_plan" id="subscription_plan" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Todos</option>
                        <option value="basic" {{ request('subscription_plan') === 'basic' ? 'selected' : '' }}>Básico</option>
                        <option value="premium" {{ request('subscription_plan') === 'premium' ? 'selected' : '' }}>Premium</option>
                        <option value="enterprise" {{ request('subscription_plan') === 'enterprise' ? 'selected' : '' }}>Empresarial</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Filtros Especiais</label>
                    <div class="flex items-center mt-1">
                        <input type="checkbox"
                               name="trial_expiring"
                               id="trial_expiring"
                               value="1"
                               {{ request('trial_expiring') ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="trial_expiring" class="ml-2 text-sm text-gray-700">Trial expirando</label>
                    </div>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Empresas -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Empresas Cadastradas</h3>
            <p class="mt-1 text-sm text-gray-500">{{ $companies->total() }} empresas encontradas</p>
        </div>

        <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Empresa
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Status & Plano
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Usuários
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Faturas
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Receita Mensal
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Criado em
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Ações</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($companies as $company)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($company->logo)
                                <img class="w-10 h-10 rounded-lg" src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }}">
                                @else
                                <div class="flex items-center justify-center w-10 h-10 bg-gray-200 rounded-lg">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                @endif
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('admin.companies.show', $company) }}" class="hover:text-blue-600">
                                            {{ $company->name }}
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $company->email }}</div>
                                    <div class="text-xs text-gray-400">{{ $company->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col space-y-1">
                                <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full
                                    {{ $company->status === 'active' ? 'text-green-800 bg-green-100' : '' }}
                                    {{ $company->status === 'trial' ? 'text-yellow-800 bg-yellow-100' : '' }}
                                    {{ $company->status === 'suspended' ? 'text-red-800 bg-red-100' : '' }}
                                    {{ $company->status === 'inactive' ? 'text-gray-800 bg-gray-100' : '' }}">
                                    {{ ucfirst($company->status) }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ ucfirst($company->subscription_plan ?? 'N/A') }}
                                </span>
                                @if($company->is_trial && $company->trial_days_left <= 7)
                                <span class="text-xs text-orange-600">
                                    {{ $company->trial_days_left }} dias restantes
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            {{ $company->users_count }}/{{ $company->max_users }}
                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(100, ($company->users_count / max($company->max_users, 1)) * 100) }}%"></div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            {{ number_format($company->invoices_count) }}
                            <div class="text-xs text-gray-500">
                                {{ $company->current_month_invoices }}/{{ $company->max_invoices_per_month }} este mês
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                            {{ number_format($company->monthly_fee ?? 0, 2, ',', '.') }} MT
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $company->created_at->format('d/m/Y') }}
                            <div class="text-xs text-gray-400">
                                por {{ $company->creator->name ?? 'Sistema' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.companies.show', $company) }}"
                                   class="text-blue-600 hover:text-blue-900"
                                   title="Ver detalhes">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                <a href="{{ route('admin.companies.edit', $company) }}"
                                   class="text-gray-600 hover:text-gray-900"
                                   title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>

                                @if($company->status === 'suspended')
                                <form method="POST" action="{{ route('admin.companies.activate', $company) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="text-green-600 hover:text-green-900"
                                            title="Ativar"
                                            onclick="return confirm('Deseja ativar esta empresa?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                </form>
                                @else
                                <button type="button"
                                        class="text-orange-600 hover:text-orange-900"
                                        title="Suspender"
                                        onclick="suspendCompany({{ $company->id }})">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                    </svg>
                                </button>
                                @endif

                                <a href="{{ route('admin.companies.impersonate', $company) }}"
                                   class="text-purple-600 hover:text-purple-900"
                                   title="Acessar como empresa"
                                   onclick="return confirm('Deseja acessar o sistema como esta empresa?')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma empresa encontrada</h3>
                                <p class="mt-1 text-sm text-gray-500">Comece criando uma nova empresa.</p>
                                <div class="mt-6">
                                    <a href="{{ route('admin.companies.create') }}"
                                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Nova Empresa
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($companies->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $companies->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal de Suspensão -->
<div id="suspendModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeSuspendModal()"></div>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="suspendForm" method="POST">
                @csrf
                <div class="px-6 pt-6 pb-4 bg-white">
                    <div class="flex items-start">
                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Suspender Empresa</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Esta ação irá suspender a empresa e impedir o acesso ao sistema. Informe o motivo da suspensão:</p>
                                <textarea name="reason"
                                          rows="3"
                                          required
                                          class="block w-full mt-3 border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                          placeholder="Motivo da suspensão..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Suspender
                    </button>
                    <button type="button"
                            onclick="closeSuspendModal()"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function suspendCompany(companyId) {
    const modal = document.getElementById('suspendModal');
    const form = document.getElementById('suspendForm');

    form.action = `/admin/companies/${companyId}/suspend`;
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeSuspendModal() {
    const modal = document.getElementById('suspendModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function exportData() {
    // Implementar exportação
    const params = new URLSearchParams(window.location.search);
    window.location.href = '{{ route("admin.companies.export") }}?' + params.toString();
}

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSuspendModal();
    }
});
</script>
@endpush
