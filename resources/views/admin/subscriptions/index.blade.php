@extends('admin.layouts.app')

@section('title', 'Gestão de Subscrições')

@section('content')
<div class="container px-4 py-8 mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Gestão de Subscrições</h1>
                <p class="mt-2 text-gray-600">Gerencie todas as subscrições das empresas</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Pagamentos
                </a>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-5">
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-lg">
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Empresas</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_companies'] }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-lg">
                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Subscrições Ativas</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_subscriptions'] }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-8 h-8 bg-yellow-100 rounded-lg">
                        <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pagamentos Pendentes</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_payments'] }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-8 h-8 bg-orange-100 rounded-lg">
                        <svg class="w-4 h-4 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Expirando em 7 dias</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['expiring_soon'] }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-8 h-8 bg-red-100 rounded-lg">
                        <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Expiradas</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['expired'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="mb-6 bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-5">
                <div>
                    <label for="search" class="block mb-1 text-sm font-medium text-gray-700">Buscar</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           placeholder="Nome ou email da empresa..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="subscription_status" class="block mb-1 text-sm font-medium text-gray-700">Status</label>
                    <select name="subscription_status" id="subscription_status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos os status</option>
                        <option value="active" {{ request('subscription_status') === 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="suspended" {{ request('subscription_status') === 'suspended' ? 'selected' : '' }}>Suspenso</option>
                        <option value="expired" {{ request('subscription_status') === 'expired' ? 'selected' : '' }}>Expirado</option>
                        <option value="cancelled" {{ request('subscription_status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>

                <div>
                    <label for="plan_id" class="block mb-1 text-sm font-medium text-gray-700">Plano</label>
                    <select name="plan_id" id="plan_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos os planos</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Filtros Rápidos</label>
                    <div class="flex space-x-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="expiring_soon" value="1" {{ request('expiring_soon') ? 'checked' : '' }}
                                   class="text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-1 text-xs">Expirando</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="expired" value="1" {{ request('expired') ? 'checked' : '' }}
                                   class="text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="ml-1 text-xs">Expirado</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 font-medium text-white transition-colors bg-blue-600 rounded-md hover:bg-blue-700">
                        Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de Empresas -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Lista de Empresas</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Empresa</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Plano</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Usuários</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Faturas</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Expira em</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($companies as $company)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10">
                                        <div class="flex items-center justify-center w-10 h-10 bg-gray-300 rounded-full">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ strtoupper(substr($company->name, 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $company->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $company->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $company->plan->name ?? 'Sem plano' }}</div>
                                <div class="text-sm text-gray-500">
                                    @if($company->plan)
                                        {{ number_format($company->plan->price, 2) }} MT
                                        @if($company->subscription_type === 'trial')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 ml-1">
                                                TESTE
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @switch($company->subscription_status)
                                        @case('active') bg-green-100 text-green-800 @break
                                        @case('suspended') bg-red-100 text-red-800 @break
                                        @case('expired') bg-gray-100 text-gray-800 @break
                                        @default bg-yellow-100 text-yellow-800
                                    @endswitch">
                                    @switch($company->subscription_status)
                                        @case('active') Ativo @break
                                        @case('suspended') Suspenso @break
                                        @case('expired') Expirado @break
                                        @case('cancelled') Cancelado @break
                                        @default Pendente
                                    @endswitch
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                {{ $company->users_count }} / {{ $company->plan->max_users ?? 0 }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                {{ $company->invoices_count }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                @if($company->subscription_expires_at)
                                    @php
                                        $daysLeft = $company->subscription_expires_at->diffInDays(now(), false);
                                    @endphp
                                    <span class="{{ $daysLeft <= 7 ? 'text-red-600 font-medium' : '' }}">
                                        @if($daysLeft == 0)
                                            Hoje
                                        @elseif($daysLeft < 0)
                                            Expirou há {{ abs($daysLeft) }} dias
                                        @else
                                            {{ $daysLeft }} dias
                                        @endif
                                    </span>
                                    <div class="text-xs text-gray-500">
                                        {{ $company->subscription_expires_at->format('d/m/Y') }}
                                    </div>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.subscriptions.show', $company) }}"
                                       class="text-indigo-600 hover:text-indigo-900">Ver</a>

                                    @if($company->subscription_status === 'active')
                                        <button onclick="openSuspendModal({{ $company->id }})"
                                                class="text-red-600 hover:text-red-900">Suspender</button>
                                    @elseif($company->subscription_status === 'suspended')
                                        <form action="{{ route('admin.subscriptions.reactivate', $company) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900"
                                                    onclick="return confirm('Deseja reativar esta empresa?')">Reativar</button>
                                        </form>
                                    @endif

                                    @if($company->subscription_type === 'trial')
                                        <button onclick="openExtendTrialModal({{ $company->id }})"
                                                class="text-blue-600 hover:text-blue-900">Estender</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <p class="mb-2 text-lg font-medium">Nenhuma empresa encontrada</p>
                                <p class="text-sm">Ajuste os filtros para ver resultados.</p>
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
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.314 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Suspender Empresa</h3>
                            <p class="text-sm text-gray-500">Esta ação bloqueará o acesso da empresa ao sistema.</p>
                        </div>
                    </div>

                    <div>
                        <label for="suspendReason" class="block mb-2 text-sm font-medium text-gray-700">Motivo da Suspensão</label>
                        <textarea name="reason" id="suspendReason" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                  placeholder="Descreva o motivo da suspensão..."></textarea>
                    </div>
                </div>

                <div class="flex justify-between px-6 py-3 bg-gray-50">
                    <button type="button" onclick="closeSuspendModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700">
                        Suspender Empresa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Extensão de Teste -->
<div id="extendTrialModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeExtendTrialModal()"></div>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="extendTrialForm" method="POST">
                @csrf
                <div class="px-6 pt-6 pb-4 bg-white">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Estender Período de Teste</h3>
                            <p class="text-sm text-gray-500">Adicione mais dias ao período de teste gratuito.</p>
                        </div>
                    </div>

                    <div>
                        <label for="extendDays" class="block mb-2 text-sm font-medium text-gray-700">Dias para Adicionar</label>
                        <input type="number" name="days" id="extendDays" min="1" max="90" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Ex: 30">
                        <p class="mt-1 text-xs text-gray-500">Máximo de 90 dias</p>
                    </div>
                </div>

                <div class="flex justify-between px-6 py-3 bg-gray-50">
                    <button type="button" onclick="closeExtendTrialModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                        Estender Teste
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openSuspendModal(companyId) {
    document.getElementById('suspendForm').action = `/admin/subscriptions/${companyId}/suspend`;
    document.getElementById('suspendModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeSuspendModal() {
    document.getElementById('suspendModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('suspendReason').value = '';
}

function openExtendTrialModal(companyId) {
    document.getElementById('extendTrialForm').action = `/admin/subscriptions/${companyId}/extend-trial`;
    document.getElementById('extendTrialModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeExtendTrialModal() {
    document.getElementById('extendTrialModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('extendDays').value = '';
}

// Fechar modais com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSuspendModal();
        closeExtendTrialModal();
    }
});
</script>
@endsection
