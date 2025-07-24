@extends('layouts.app')

@section('title', 'Nota de Crédito ' . $creditNote->invoice_number)
@section('subtitle', 'Detalhes da nota de crédito para ' . $creditNote->client->name)

@section('header-actions')
<div class="flex space-x-3">
    <!-- Baixar PDF -->
    <a href="{{ route('credit-notes.download-pdf', $creditNote) }}"
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
        </svg>
        Baixar PDF
    </a>

    <!-- Enviar por Email -->
    <button onclick="showEmailModal()"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-purple-600 rounded-lg hover:bg-purple-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        Enviar por Email
    </button>

    <!-- Editar -->
    <a href="{{ route('credit-notes.edit', $creditNote) }}"
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Editar
    </a>

    <!-- Duplicar -->
    <form action="{{ route('credit-notes.duplicate', $creditNote) }}" method="POST" class="inline">
        @csrf
        <button type="submit"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700"
                onclick="return confirm('Deseja duplicar esta nota de crédito?')">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            Duplicar
        </button>
    </form>

    <!-- Voltar -->
    <a href="{{ route('credit-notes.index') }}"
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-gray-500 rounded-lg hover:bg-gray-600">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Voltar
    </a>
</div>
@endsection

@section('content')
<div class="mx-auto space-y-8 max-w-10xl">
    <!-- Alert de Nota de Crédito -->
    <div class="p-4 border border-orange-200 rounded-lg bg-orange-50">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-orange-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-orange-800">Nota de Crédito</h3>
                <div class="mt-2 text-sm text-orange-700">
                    <p>Este documento representa um crédito de <strong>{{ number_format($creditNote->total, 2, ',', '.') }} MT</strong> a favor do cliente.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Coluna Principal -->
        <div class="space-y-8 lg:col-span-2">
            <!-- Informações Básicas -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-orange-100 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Informações da Nota de Crédito</h3>
                            <p class="text-sm text-gray-600">{{ $creditNote->invoice_number }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cliente</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $creditNote->client->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Data</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $creditNote->invoice_date->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Processada
                                </span>
                            </dd>
                        </div>
                        @if($creditNote->relatedInvoice)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fatura Relacionada</dt>
                            <dd class="mt-1">
                                <a href="{{ route('invoices.show', $creditNote->relatedInvoice) }}"
                                   class="text-sm text-blue-600 hover:text-blue-800">
                                    {{ $creditNote->relatedInvoice->invoice_number }}
                                </a>
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Motivo do Ajuste -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-red-100 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Motivo do Ajuste</h3>
                            <p class="text-sm text-gray-600">Razão para a emissão desta nota de crédito</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm leading-relaxed text-gray-900">{{ $creditNote->adjustment_reason }}</p>
                </div>
            </div>

            <!-- Itens -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Itens Creditados</h3>
                            <p class="text-sm text-gray-600">{{ $creditNote->items->count() }} {{ Str::plural('item', $creditNote->items->count()) }}</p>
                        </div>
                    </div>
                </div>
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Descrição
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                    Qtde
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                                    Preço Unit.
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                    IVA %
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($creditNote->items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->description }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-center text-gray-900">
                                    {{ number_format($item->quantity, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">
                                    {{ number_format($item->unit_price, 2, ',', '.') }} MT
                                </td>
                                <td class="px-6 py-4 text-sm text-center text-gray-900">
                                    {{ number_format($item->tax_rate, 1, ',', '.') }}%
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-right text-gray-900">
                                    {{ number_format($item->quantity * $item->unit_price * (1 + $item->tax_rate/100), 2, ',', '.') }} MT
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Observações -->
            @if($creditNote->notes)
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Observações</h3>
                            <p class="text-sm text-gray-600">Informações adicionais</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm leading-relaxed text-gray-900">{{ $creditNote->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Resumo Financeiro -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-red-100 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Resumo Financeiro</h3>
                            <p class="text-sm text-gray-600">Valores da nota de crédito</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-700">Subtotal:</span>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($creditNote->subtotal, 2, ',', '.') }} MT</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-700">IVA:</span>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($creditNote->tax_amount, 2, ',', '.') }} MT</span>
                        </div>
                        @if($creditNote->discount_amount > 0)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-700">Desconto:</span>
                            <span class="text-sm font-bold text-orange-600">-{{ number_format($creditNote->discount_amount, 2, ',', '.') }} MT</span>
                        </div>
                        @endif
                        <div class="flex items-center justify-between px-4 py-3 border-l-4 border-red-500 rounded-lg bg-gradient-to-r from-red-50 to-red-100">
                            <span class="text-lg font-bold text-red-800">TOTAL A CREDITAR:</span>
                            <span class="text-xl font-bold text-red-800">{{ number_format($creditNote->total, 2, ',', '.') }} MT</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações do Cliente -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informações do Cliente</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nome</p>
                            <p class="text-sm text-gray-900">{{ $creditNote->client->name }}</p>
                        </div>
                        @if($creditNote->client->email)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="text-sm text-gray-900">{{ $creditNote->client->email }}</p>
                        </div>
                        @endif
                        @if($creditNote->client->phone)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Telefone</p>
                            <p class="text-sm text-gray-900">{{ $creditNote->client->phone }}</p>
                        </div>
                        @endif
                        @if($creditNote->client->nuit)
                        <div>
                            <p class="text-sm font-medium text-gray-500">NUIT</p>
                            <p class="text-sm text-gray-900">{{ $creditNote->client->nuit }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Email -->
<div id="emailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('credit-notes.send-email', $creditNote) }}" method="POST">
                @csrf
                <div class="px-6 pt-6 pb-4 bg-white">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="w-full ml-3">
                            <h3 class="text-lg font-medium text-gray-900">Enviar Nota de Crédito por Email</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email do Destinatário</label>
                                    <input type="email" name="email" id="email" value="{{ $creditNote->client->email }}"
                                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                </div>
                                <div>
                                    <label for="subject" class="block text-sm font-medium text-gray-700">Assunto</label>
                                    <input type="text" name="subject" id="subject"
                                           value="Nota de Crédito {{ $creditNote->invoice_number }} - {{ config('app.name') }}"
                                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                </div>
                                <div>
                                    <label for="message" class="block text-sm font-medium text-gray-700">Mensagem</label>
                                    <textarea name="message" id="message" rows="4"
                                              class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>Prezado(a) {{ $creditNote->client->name }},

Enviamos em anexo a Nota de Crédito {{ $creditNote->invoice_number }} no valor de {{ number_format($creditNote->total, 2, ',', '.') }} MT.

Atenciosamente,
{{ config('app.name') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between px-6 py-3 bg-gray-50">
                    <button type="button" onclick="hideEmailModal()"
                            class="inline-flex justify-center px-4 py-2 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="inline-flex justify-center px-4 py-2 text-base font-medium text-white bg-purple-600 border border-transparent rounded-md shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:text-sm">
                        Enviar Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showEmailModal() {
    document.getElementById('emailModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function hideEmailModal() {
    document.getElementById('emailModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// Fechar modal ao clicar fora
document.getElementById('emailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideEmailModal();
    }
});
</script>
@endpush
@endsection
