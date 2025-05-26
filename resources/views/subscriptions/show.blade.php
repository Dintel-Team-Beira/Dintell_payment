@extends('layouts.app')

@section('title', 'Subscrição: ' . $subscription->domain)

@section('header-actions')
<div class="flex items-center gap-x-3">
    <!-- Status Actions -->
    @if($subscription->status === 'active')
        <form method="POST" action="{{ route('subscriptions.suspend', $subscription) }}" class="inline">
            @csrf
            <button type="button" onclick="openSuspendModal()"
                    class="rounded-md bg-yellow-600 px-3 py-2 text-sm font-semibold text-white hover:bg-yellow-500">
                Suspender
            </button>
        </form>
    @elseif($subscription->status === 'suspended')
        <form method="POST" action="{{ route('subscriptions.activate', $subscription) }}" class="inline">
            @csrf
            <button type="submit"
                    class="rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-500">
                Ativar
            </button>
        </form>
    @endif

    <!-- Manual Toggle -->
    <form method="POST" action="{{ route('subscriptions.toggle-manual', $subscription) }}" class="inline">
        @csrf
        <button type="submit"
                class="rounded-md bg-{{ $subscription->manual_status === 'enabled' ? 'gray' : 'blue' }}-600 px-3 py-2 text-sm font-semibold text-white hover:bg-{{ $subscription->manual_status === 'enabled' ? 'gray' : 'blue' }}-500">
            {{ $subscription->manual_status === 'enabled' ? 'Desabilitar' : 'Habilitar' }}
        </button>
    </form>

    <a href="{{ route('subscriptions.edit', $subscription) }}"
       class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
        Editar
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Main Info Card -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full mr-3 {{ $subscription->canAccess() ? 'bg-green-400' : 'bg-red-400' }}"></div>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">{{ $subscription->domain }}</h1>
                        <p class="text-sm text-gray-500">{{ $subscription->client->name }} • {{ $subscription->plan->name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                        {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' :
                           ($subscription->status === 'suspended' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($subscription->status) }}
                    </span>
                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded
                        {{ $subscription->manual_status === 'enabled' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $subscription->manual_status === 'enabled' ? 'Manual: ON' : 'Manual: OFF' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="px-6 py-4">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Chave API</dt>
                    <dd class="mt-1 flex items-center">
                        <code class="text-sm text-gray-900 bg-gray-100 px-2 py-1 rounded">{{ substr($subscription->api_key, 0, 20) }}...</code>
                        <form method="POST" action="{{ route('subscriptions.regenerate-key', $subscription) }}" class="inline ml-2">
                            @csrf
                            <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm">Regenerar</button>
                        </form>
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Expira em</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($subscription->ends_at)
                            {{ $subscription->ends_at->format('d/m/Y H:i') }}
                            @if($subscription->days_until_expiry !== null)
                                <span class="text-{{ $subscription->days_until_expiry <= 7 ? 'red' : 'gray' }}-500">
                                    ({{ $subscription->days_until_expiry > 0 ? $subscription->days_until_expiry . ' dias' : 'Expirado' }})
                                </span>
                            @endif
                        @else
                            <span class="text-gray-400">Sem expiração</span>
                        @endif
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Receita Total</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">MT {{ number_format($subscription->total_revenue, 2) }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l-1-3m1 3l-1-3m-16.5-3h9.75"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Requests</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_requests']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Dias Ativo</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['days_active'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 010 0L21.75 9M21.75 9H15M21.75 9v6.75"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Uso (%)</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($subscription->usage_percentage, 1) }}%</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Falhas Pagamento</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['payment_failures'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <!-- Renew Subscription -->
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6">
            {{-- <h3 class="text-lg font-semibold mb-4 --}}
<h3 class="text-lg font-semibold mb-4">Renovar Subscrição</h3>
        <form method="POST" action="{{ route('subscriptions.renew', $subscription) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Valor</label>
                <input type="number" step="0.01" name="amount" value="{{ $subscription->plan->price }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Método de Pagamento</label>
                <select name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="mpesa">MPesa</option>
                    <option value="visa">Visa</option>
                    <option value="bank_transfer">Transferência</option>
                    <option value="cash">Dinheiro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Referência</label>
                <input type="text" name="payment_reference"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                Processar Renovação
            </button>
        </form>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-4">Ações Rápidas</h3>
        <div class="space-y-3">
            <a href="https://{{ $subscription->domain }}" target="_blank"
               class="flex items-center justify-between p-3 text-sm bg-blue-50 rounded-lg hover:bg-blue-100">
                <span>Visitar Website</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>

            <a href="{{ route('suspension.page', ['domain' => $subscription->domain]) }}" target="_blank"
               class="flex items-center justify-between p-3 text-sm bg-yellow-50 rounded-lg hover:bg-yellow-100">
                <span>Ver Página Suspensão</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </a>

            <button onclick="testApiConnection()"
                    class="w-full flex items-center justify-between p-3 text-sm bg-green-50 rounded-lg hover:bg-green-100">
                <span>Testar API</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Recent API Logs -->
@if($subscription->apiLogs->count() > 0)
<div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-base font-semibold leading-6 text-gray-900">Logs da API Recentes</h3>
    </div>
    <div class="overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data/Hora</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Endpoint</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($subscription->apiLogs->take(10) as $log)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $log->created_at->format('d/m H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->ip_address }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->endpoint }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            {{ $log->response_code < 300 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $log->response_code }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
</div>
<!-- Suspend Modal -->
<div id="suspendModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Suspender Subscrição</h3>
        <form method="POST" action="{{ route('subscriptions.suspend', $subscription) }}">
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
<script>
function openSuspendModal() {
    document.getElementById('suspendModal').classList.remove('hidden');
}

function closeSuspendModal() {
    document.getElementById('suspendModal').classList.add('hidden');
}

async function testApiConnection() {
    try {
        const response = await fetch('/api/v1/subscription/verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                domain: '{{ $subscription->domain }}',
                api_key: '{{ $subscription->api_key }}'
            })
        });

        const data = await response.json();
        alert('Teste da API: ' + data.status + ' - ' + data.message);
    } catch (error) {
        alert('Erro no teste da API: ' + error.message);
    }
}
</script>
@endsection
