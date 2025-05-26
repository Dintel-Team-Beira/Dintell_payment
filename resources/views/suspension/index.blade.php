@extends('layouts.app')

@section('title', 'Subscrições')
@section('subtitle', 'Gerencie todas as subscrições do sistema')

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <form method="GET" class="flex items-center space-x-4">
                <div class="flex-1 max-w-md">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Buscar por domínio ou cliente..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos os status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativo</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspenso</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Filtrar
                </button>
            </form>
        </div>

        <a href="{{ route('subscriptions.create') }}"
           class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nova Subscrição
        </a>
    </div>

    <!-- Subscriptions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Cliente & Domínio
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Plano
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Expiração
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Receita
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($subscriptions as $subscription)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-3
                                {{ $subscription->isActive() ? 'bg-green-400' : 'bg-red-400' }}">
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $subscription->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $subscription->domain }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $subscription->plan->name }}</div>
                        <div class="text-sm text-gray-500">{{ $subscription->plan->formatted_price }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' :
                               ($subscription->status === 'suspended' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($subscription->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($subscription->ends_at)
                            <div>{{ $subscription->ends_at->format('d/m/Y') }}</div>
                            @if($subscription->days_until_expiry !== null)
                                <div class="text-xs {{ $subscription->days_until_expiry <= 7 ? 'text-red-600' : 'text-gray-400' }}">
                                    {{ $subscription->days_until_expiry > 0 ? $subscription->days_until_expiry . ' dias' : 'Expirado' }}
                                </div>
                            @endif
                        @else
                            <span class="text-gray-400">Sem expiração</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        MT {{ number_format($subscription->amount_paid, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('subscriptions.show', $subscription) }}"
                               class="text-blue-600 hover:text-blue-900">Ver</a>
                            <a href="{{ route('subscriptions.edit', $subscription) }}"
                               class="text-indigo-600 hover:text-indigo-900">Editar</a>

                            @if($subscription->status !== 'suspended')
                            <button onclick="openSuspendModal({{ $subscription->id }})"
                                    class="text-yellow-600 hover:text-yellow-900">Suspender</button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
</script>
@endsection