@extends('layouts.app')

@section('title', 'Subscrições')

@section('header-actions')
<div class="flex items-center gap-x-4">
    <!-- Search and Filters -->
    <form method="GET" class="flex items-center gap-x-2">
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar domínio ou cliente..."
                   class="block w-full rounded-md border-0 py-1.5 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
        </div>

        {{-- <select name="status" class="rounded-md border-0 py-1.5 pl-3 pr-8 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
            <option value="">Todos os status</option>
            @foreach($statusOptions as $value => $label)
                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select> --}}

        <select name="plan" class="rounded-md border-0 py-1.5 pl-3 pr-8 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
            <option value="">Todos os planos</option>
            @foreach($plans as $plan)
                <option value="{{ $plan->id }}" {{ request('plan') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
            @endforeach
        </select>

        <button type="submit" class="rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
            Filtrar
        </button>

        @if(request()->hasAny(['search', 'status', 'plan']))
        <a href="{{ route('subscriptions.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            Limpar
        </a>
        @endif
    </form>

    <!-- Add Subscription Button -->
    <a href="{{ route('subscriptions.create') }}"
       class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
        </svg>
        Nova Subscrição
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Subscrições</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Subscription::count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Ativas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Subscription::where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Suspensas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Subscription::where('status', 'suspended')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Receita Mensal</p>
                    <p class="text-2xl font-bold text-gray-900">MT {{ number_format(\App\Models\Subscription::where('last_payment_date', '>=', now()->startOfMonth())->sum('amount_paid'), 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Lista de Subscrições</h3>
            <p class="mt-1 text-sm text-gray-500">Gerencie todas as subscrições do sistema</p>
        </div>

        <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cliente & Domínio
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Plano
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Expiração
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Receita
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Ações</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subscriptions as $subscription)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-3
                                    {{ $subscription->canAccess() ? 'bg-green-400' : 'bg-red-400' }}">
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $subscription->client->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $subscription->domain }}</div>
                                    @if($subscription->subdomain)
                                        <div class="text-xs text-gray-400">{{ $subscription->subdomain }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $subscription->plan->name }}</div>
                            <div class="text-sm text-gray-500">MT {{ number_format($subscription->plan->price, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' :
                                       ($subscription->status === 'suspended' ? 'bg-yellow-100 text-yellow-800' :
                                        ($subscription->status === 'trial' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>

                                @if($subscription->manual_status === 'disabled')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Manual: OFF
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($subscription->ends_at)
                                <div>{{ $subscription->ends_at->format('d/m/Y') }}</div>
                                @if($subscription->days_until_expiry !== null)
                                    <div class="text-xs {{ $subscription->days_until_expiry <= 7 ? 'text-red-600' : 'text-gray-400' }}">
                                        @if($subscription->days_until_expiry > 0)
                                            {{ (int)$subscription->days_until_expiry }} dias
                                        @else
                                            Expirado
                                        @endif
                                    </div>
                                @endif
                            @elseif($subscription->trial_ends_at)
                                <div class="text-blue-600">Trial até {{ $subscription->trial_ends_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-blue-500">{{ (int)$subscription->trial_days_left }} dias</div>
                            @else
                                <span class="text-gray-400">Sem expiração</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            MT {{ number_format($subscription->total_revenue, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('subscriptions.show', $subscription) }}"
                                   class="text-blue-600 hover:text-blue-900">Ver</a>
                                <a href="{{ route('subscriptions.edit', $subscription) }}"
                                   class="text-indigo-600 hover:text-indigo-900">Editar</a>

                                @if($subscription->status === 'active')
                                    <button onclick="openSuspendModal({{ $subscription->id }})"
                                            class="text-yellow-600 hover:text-yellow-900">Suspender</button>
                                @elseif($subscription->status === 'suspended')
                                    <form method="POST" action="{{ route('subscriptions.activate', $subscription) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900">Ativar</button>
                                    </form>
                                @endif

                                <button onclick="toggleManual({{ $subscription->id }})"
                                        class="text-purple-600 hover:text-purple-900">
                                    {{ $subscription->manual_status === 'enabled' ? 'Desabilitar' : 'Habilitar' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <p class="text-lg font-medium">Nenhuma subscrição encontrada</p>
                            <p class="mt-1">Comece criando sua primeira subscrição.</p>
                            <a href="{{ route('subscriptions.create') }}"
                               class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Nova Subscrição
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subscriptions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $subscriptions->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Suspend Modal -->
<div id="suspendModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Suspender Subscrição</h3>
            <form id="suspendForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Motivo da Suspensão</label>
                    <textarea name="reason" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Descreva o motivo da suspensão..."></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <button type="button" onclick="closeSuspendModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-md hover:bg-yellow-700">
                        Suspender
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openSuspendModal(subscriptionId) {
    document.getElementById('suspendForm').action = `/subscriptions/${subscriptionId}/suspend`;
    document.getElementById('suspendModal').classList.remove('hidden');
}

function closeSuspendModal() {
    document.getElementById('suspendModal').classList.add('hidden');
}

function toggleManual(subscriptionId) {
    if (confirm('Alterar status manual desta subscrição?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/subscriptions/${subscriptionId}/toggle-manual`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection