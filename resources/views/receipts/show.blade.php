@extends('layouts.app')

@section('title', 'Recibo ' . $receipt->receipt_number)
@section('subtitle', 'Detalhes do recibo de pagamento')

@section('header-actions')
<div class="flex space-x-3">
    <!-- Baixar PDF -->
    <a href="{{ route('receipts.download-pdf', $receipt) }}" 
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
        </svg>
        Baixar PDF
    </a>

    {{-- @if($receipt->status === 'active')
    <button onclick="showCancelModal()" 
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        Cancelar Recibo
    </button>
    @endif --}}

    <!-- Menu de Ações -->
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" 
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
            </svg>
            Mais Ações
        </button>

        <div x-show="open" @click.away="open = false" x-cloak
             class="absolute right-0 z-10 w-48 mt-2 bg-white border border-gray-200 divide-y divide-gray-100 rounded-lg shadow-lg">
            <div class="py-1">
                {{-- <button onclick="duplicateReceipt({{ $receipt->id }})"
                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Duplicar Recibo
                </button> --}}
                
                @if($receipt->invoice)
                <a href="{{ route('invoices.show', $receipt->invoice) }}"
                   class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Ver Fatura Original
                </a>
                @endif

                <a href="{{ route('receipts.index') }}"
                   class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Todos os Recibos
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Cabeçalho do Recibo -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Recibo {{ $receipt->receipt_number }}</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Emitido em {{ $receipt->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div class="text-right">
                    @php
                        $statusClasses = [
                            'active' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800'
                        ];
                        $statusLabels = [
                            'active' => 'Ativo',
                            'cancelled' => 'Cancelado'
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full {{ $statusClasses[$receipt->status] }}">
                        {{ $statusLabels[$receipt->status] }}
                    </span>
                </div>
            </div>
        </div>

        <div class="px-6 py-6">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Informações do Cliente -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900">Cliente</h3>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-12 h-12 text-lg font-bold text-white bg-gray-500 rounded-full">
                                    {{ strtoupper(substr($receipt->client->name, 0, 2)) }}
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">{{ $receipt->client->name }}</h4>
                                @if($receipt->client->email)
                                    <p class="text-sm text-gray-600">{{ $receipt->client->email }}</p>
                                @endif
                                @if($receipt->client->phone)
                                    <p class="text-sm text-gray-600">{{ $receipt->client->phone }}</p>
                                @endif
                                @if($receipt->client->address)
                                    <p class="text-sm text-gray-600">{{ $receipt->client->address }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Valor Recebido -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900">Valor Recebido</h3>
                    <div class="p-6 text-center bg-green-50 border border-green-200 rounded-lg">
                        <div class="text-3xl font-bold text-green-600">
                            {{ number_format($receipt->amount_paid, 2, ',', '.') }} MT
                        </div>
                        <p class="mt-2 text-sm text-green-700">
                            Pago em {{ $receipt->payment_date->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalhes do Pagamento -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Detalhes do Pagamento</h2>
        </div>
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Data do Pagamento</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $receipt->payment_date->format('d/m/Y H:i') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Método de Pagamento</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $receipt->payment_method_label }}</p>
                    </div>
                    
                    @if($receipt->transaction_reference)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Referência da Transação</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $receipt->transaction_reference }}</p>
                    </div>
                    @endif
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Data de Emissão</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $receipt->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Emitido por</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $receipt->issuedBy->name ?? 'Sistema Automático' }}</p>
                    </div>
                    
                    @if($receipt->invoice)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fatura Relacionada</label>
                        <p class="mt-1">
                            <a href="{{ route('invoices.show', $receipt->invoice) }}" 
                               class="text-sm text-blue-600 hover:text-blue-800">
                                {{ $receipt->invoice->invoice_number }}
                            </a>
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo da Fatura (se existir) -->
    @if($receipt->invoice)
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Resumo da Fatura</h2>
        </div>
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="text-sm font-medium text-gray-700">Total da Fatura</div>
                    <div class="mt-1 text-lg font-bold text-gray-900">
                        {{ number_format($receipt->invoice->total, 2, ',', '.') }} MT
                    </div>
                </div>
                <div class="p-4 bg-blue-50 rounded-lg">
                    <div class="text-sm font-medium text-blue-700">Total Pago</div>
                    <div class="mt-1 text-lg font-bold text-blue-900">
                        {{ number_format($receipt->invoice->paid_amount, 2, ',', '.') }} MT
                    </div>
                </div>
                <div class="p-4 bg-yellow-50 rounded-lg">
                    <div class="text-sm font-medium text-yellow-700">Saldo Restante</div>
                    <div class="mt-1 text-lg font-bold text-yellow-900">
                        {{ number_format($receipt->invoice->remaining_amount, 2, ',', '.') }} MT
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Observações -->
    @if($receipt->notes)
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Observações</h2>
        </div>
        <div class="px-6 py-6">
            <p class="text-sm text-gray-700">{{ $receipt->notes }}</p>
        </div>
    </div>
    @endif
</div>

<!-- Modal de Cancelamento -->
@if($receipt->status === 'active')
<div id="cancelModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeCancelModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="cancelForm" action="{{ route('receipts.cancel', $receipt) }}" method="POST">
                @csrf
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Cancelar Recibo</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Tem certeza que deseja cancelar este recibo? Esta ação irá reverter o pagamento na fatura relacionada.
                                </p>
                            </div>
                            <div class="mt-4">
                                <label for="cancel_reason" class="block text-sm font-medium text-gray-700">Motivo do Cancelamento *</label>
                                <div class="mt-1">
                                    <textarea name="reason" id="cancel_reason" rows="3" required
                                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                              placeholder="Descreva o motivo do cancelamento..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirmar Cancelamento
                    </button>
                    <button type="button" onclick="closeCancelModal()"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
// Modal de cancelamento
function showCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    
    setTimeout(() => {
        document.getElementById('cancel_reason').focus();
    }, 100);
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('cancel_reason').value = '';
}

// Duplicar recibo
function duplicateReceipt(receiptId) {
    if (confirm('Deseja criar uma cópia deste recibo?')) {
        showNotification('Duplicando recibo...', 'info');

        fetch(`/receipts/${receiptId}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Recibo duplicado com sucesso!', 'success');
                if (data.receipt && data.receipt.show_url) {
                    setTimeout(() => window.location.href = data.receipt.show_url, 1500);
                }
            } else {
                showNotification('Erro ao duplicar recibo: ' + (data.message || 'Erro desconhecido'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao duplicar recibo', 'error');
        });
    }
}

// Função para mostrar notificações
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;

    const colors = {
        'success': 'bg-green-500 text-white',
        'error': 'bg-red-500 text-white',
        'info': 'bg-blue-500 text-white',
        'warning': 'bg-yellow-500 text-white'
    };

    notification.className += ` ${colors[type]}`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 5000);
}

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        @if($receipt->status === 'active')
        if (!document.getElementById('cancelModal').classList.contains('hidden')) {
            closeCancelModal();
        }
        @endif
    }
});
</script>
@endpush
@endsection