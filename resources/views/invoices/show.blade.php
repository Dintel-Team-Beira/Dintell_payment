@extends('layouts.app')

@section('title', 'Fatura ' . $invoice->invoice_number)
@section('subtitle', 'Detalhes da fatura para ' . $invoice->client->name)

@section('header-actions')
<div class="flex space-x-3">
    <!-- Status da Fatura -->
    <div class="flex items-center" x-data="{ open: false }">
        <button @click="open = !open"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @php
                $statusLabels = [
                    'draft' => 'Rascunho',
                    'sent' => 'Enviada',
                    'paid' => 'Paga',
                    'overdue' => 'Vencida',
                    'cancelled' => 'Cancelada'
                ];
            @endphp
            Alterar Status: {{ $statusLabels[$invoice->status] }}
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="open" @click.away="open = false"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="absolute right-0 z-10 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">

            @foreach(['draft' => 'Rascunho', 'sent' => 'Enviada', 'paid' => 'Paga', 'overdue' => 'Vencida', 'cancelled' => 'Cancelada'] as $status => $label)
                @if($status !== $invoice->status)
                    <form action="{{ route('invoices.update-status', $invoice) }}" method="POST" class="block">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $status }}">
                        <button type="submit" class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                            {{ $label }}
                        </button>
                    </form>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Marcar como Paga -->
    @if($invoice->status !== 'paid')
        <button onclick="showPaymentModal()"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Marcar como Paga
        </button>
    @endif

    <!-- Baixar PDF -->
    <a href="{{ route('invoices.download-pdf', $invoice) }}"
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
        </svg>
        Baixar PDF
    </a>

    <!-- Enviar por Email -->
    @if($invoice->status === 'draft')
        <button onclick="showEmailModal()"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-purple-600 rounded-lg hover:bg-purple-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Enviar por Email
        </button>
    @endif

    <!-- Editar -->
    @if($invoice->status !== 'paid')
        <a href="{{ route('invoices.edit', $invoice) }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar
        </a>
    @endif

    <a href="{{ route('invoices.index') }}"
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-gray-500 rounded-lg hover:bg-gray-600">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Voltar
    </a>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <!-- Alertas -->
    @if($invoice->isOverdue() && $invoice->status !== 'paid')
        <div class="p-4 border border-red-200 rounded-lg bg-red-50">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Fatura Vencida</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>Esta fatura venceu em {{ $invoice->due_date->format('d/m/Y') }} ({{ $invoice->due_date->diffForHumans() }}). Entre em contato com o cliente para regularizar o pagamento.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($invoice->quote)
        <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Fatura Baseada em Cotação</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Esta fatura foi gerada a partir da cotação {{ $invoice->quote->quote_number }}.</p>
                        <p class="mt-1">
                            <a href="{{ route('quotes.show', $invoice->quote) }}" class="font-medium underline">
                                Ver Cotação Original
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($invoice->status === 'paid')
        <div class="p-4 border border-green-200 rounded-lg bg-green-50">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Fatura Paga</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>Esta fatura foi paga{{ $invoice->paid_at ? ' em ' . $invoice->paid_at->format('d/m/Y H:i') : '' }}.</p>
                        @if($invoice->paid_amount > 0)
                            <p>Valor pago: {{ number_format($invoice->paid_amount, 2) }} MT</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Informações da Fatura -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Informações da Fatura</h3>
                @php
                    $statusClasses = [
                        'draft' => 'bg-gray-100 text-gray-800',
                        'sent' => 'bg-yellow-100 text-yellow-800',
                        'paid' => 'bg-green-100 text-green-800',
                        'overdue' => 'bg-red-100 text-red-800',
                        'cancelled' => 'bg-gray-100 text-gray-800'
                    ];
                    $statusLabels = [
                        'draft' => 'Rascunho',
                        'sent' => 'Enviada',
                        'paid' => 'Paga',
                        'overdue' => 'Vencida',
                        'cancelled' => 'Cancelada'
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses[$invoice->status] }}">
                    {{ $statusLabels[$invoice->status] }}
                </span>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Número da Fatura</h4>
                    <p class="text-lg font-semibold text-gray-900">{{ $invoice->invoice_number }}</p>
                </div>
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Data da Fatura</h4>
                    <p class="text-lg font-semibold text-gray-900">{{ $invoice->invoice_date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Data de Vencimento</h4>
                    <p class="text-lg font-semibold {{ $invoice->isOverdue() && $invoice->status !== 'paid' ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $invoice->due_date->format('d/m/Y') }}
                    </p>
                    @if($invoice->isOverdue() && $invoice->status !== 'paid')
                        <p class="text-xs text-red-500">Venceu {{ $invoice->due_date->diffForHumans() }}</p>
                    @endif
                </div>
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Prazo de Pagamento</h4>
                    <p class="text-lg font-semibold text-gray-900">{{ $invoice->payment_terms_days }} dias</p>
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
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Nome</h4>
                    <p class="text-lg font-semibold text-gray-900">{{ $invoice->client->name }}</p>
                </div>
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Email</h4>
                    <p class="text-lg text-gray-900">{{ $invoice->client->email }}</p>
                </div>
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Telefone</h4>
                    <p class="text-lg text-gray-900">{{ $invoice->client->phone ?? 'Não informado' }}</p>
                </div>
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">NUIT</h4>
                    <p class="text-lg text-gray-900">{{ $invoice->client->nuit ?? 'Não informado' }}</p>
                </div>
                @if($invoice->client->address)
                <div class="md:col-span-2">
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Endereço</h4>
                    <p class="text-lg text-gray-900 whitespace-pre-line">{{ $invoice->client->address }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Itens da Fatura -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Itens da Fatura</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Descrição</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Qtd</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Preço Unit.</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">IVA (%)</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($invoice->items as $item)
                    @php
                        $itemSubtotal = $item->quantity * $item->unit_price;
                        $itemTax = $itemSubtotal * ($item->tax_rate / 100);
                        $itemTotal = $itemSubtotal + $itemTax;
                    @endphp
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $item->description }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="text-sm text-gray-900">{{ number_format($item->quantity, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm text-gray-900">{{ number_format($item->unit_price, 2) }} MT</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="text-sm text-gray-900">{{ number_format($item->tax_rate, 1) }}%</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm font-medium text-gray-900">{{ number_format($itemTotal, 2) }} MT</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totais -->
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex justify-end">
                <div class="w-80">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-700">Subtotal:</span>
                            <span class="text-gray-900">{{ number_format($invoice->subtotal, 2) }} MT</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-700">IVA:</span>
                            <span class="text-gray-900">{{ number_format($invoice->tax_amount, 2) }} MT</span>
                        </div>
                        @if($invoice->paid_amount > 0)
                        <div class="flex justify-between text-sm border-t pt-2">
                            <span class="font-medium text-gray-700">Valor Pago:</span>
                            <span class="text-green-600">{{ number_format($invoice->paid_amount, 2) }} MT</span>
                        </div>
                        @if($invoice->remaining_amount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-700">Valor Restante:</span>
                            <span class="text-red-600">{{ number_format($invoice->remaining_amount, 2) }} MT</span>
                        </div>
                        @endif
                        @endif
                        <div class="pt-2 border-t border-gray-200">
                            <div class="flex justify-between">
                                <span class="text-lg font-bold text-gray-900">TOTAL:</span>
                                <span class="text-xl font-bold text-blue-600">{{ number_format($invoice->total, 2) }} MT</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Observações e Termos -->
    @if($invoice->notes || $invoice->terms_conditions)
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        @if($invoice->notes)
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Observações</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700 whitespace-pre-line">{{ $invoice->notes }}</p>
            </div>
        </div>
        @endif

        @if($invoice->terms_conditions)
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Termos e Condições</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700 whitespace-pre-line">{{ $invoice->terms_conditions }}</p>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Histórico de Ações -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Histórico</h3>
        </div>
        <div class="p-6">
            <div class="flow-root">
                <ul class="-mb-8">
                    <li>
                        <div class="relative pb-8">
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="flex items-center justify-center w-8 h-8 bg-blue-500 rounded-full ring-8 ring-white">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Fatura criada</p>
                                        @if($invoice->quote)
                                            <p class="text-xs text-gray-400">Baseada na cotação {{ $invoice->quote->quote_number }}</p>
                                        @endif
                                    </div>
                                    <div class="text-sm text-right text-gray-500 whitespace-nowrap">
                                        {{ $invoice->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                    @if($invoice->updated_at != $invoice->created_at)
                    <li>
                        <div class="relative pb-8">
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="flex items-center justify-center w-8 h-8 bg-yellow-500 rounded-full ring-8 ring-white">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Fatura atualizada</p>
                                    </div>
                                    <div class="text-sm text-right text-gray-500 whitespace-nowrap">
                                        {{ $invoice->updated_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endif

                    @if($invoice->status === 'sent' && $invoice->sent_at)
                    <li>
                        <div class="relative pb-8">
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="flex items-center justify-center w-8 h-8 bg-purple-500 rounded-full ring-8 ring-white">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Fatura enviada para o cliente</p>
                                    </div>
                                    <div class="text-sm text-right text-gray-500 whitespace-nowrap">
                                        {{ $invoice->sent_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endif

                    @if($invoice->status === 'paid' && $invoice->paid_at)
                    <li>
                        <div class="relative">
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="flex items-center justify-center w-8 h-8 bg-green-500 rounded-full ring-8 ring-white">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                    <div>
                                        <p class="text-sm text-gray-500">
                                            Pagamento recebido
                                            @if($invoice->paid_amount > 0)
                                                <span class="font-medium text-green-600">({{ number_format($invoice->paid_amount, 2) }} MT)</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-sm text-right text-gray-500 whitespace-nowrap">
                                        {{ $invoice->paid_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Pagamento -->
<div id="paymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="paymentForm" action="{{ route('invoices.mark-as-paid', $invoice) }}" method="POST">
                @csrf
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-green-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Registrar Pagamento</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Confirme o recebimento do pagamento desta fatura.
                                </p>
                            </div>
                            <div class="mt-4">
                                <label for="payment_amount" class="block text-sm font-medium text-gray-700">Valor Pago</label>
                                <div class="mt-1">
                                    <input type="number" name="amount" id="payment_amount"
                                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                                           value="{{ number_format($invoice->remaining_amount, 2, '.', '') }}"
                                           max="{{ number_format($invoice->remaining_amount, 2, '.', '') }}"
                                           min="0" step="0.01">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Valor restante: {{ number_format($invoice->remaining_amount, 2) }} MT
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirmar Pagamento
                    </button>
                    <button type="button" onclick="closePaymentModal()"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Email -->
<div id="emailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="emailForm" action="{{ route('invoices.send-email', $invoice) }}" method="POST">
                @csrf
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-purple-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Enviar Fatura por Email</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Envie a fatura para o cliente por email.
                                </p>
                            </div>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="email_address" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" id="email_address"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                           value="{{ $invoice->client->email }}" required>
                                </div>
                                <div>
                                    <label for="email_subject" class="block text-sm font-medium text-gray-700">Assunto</label>
                                    <input type="text" name="subject" id="email_subject"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                           value="Fatura {{ $invoice->invoice_number }}" required>
                                </div>
                                <div>
                                    <label for="email_message" class="block text-sm font-medium text-gray-700">Mensagem (opcional)</label>
                                    <textarea name="message" id="email_message" rows="3"
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                              placeholder="Mensagem adicional para o cliente..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-purple-600 border border-transparent rounded-md shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Enviar Email
                    </button>
                    <button type="button" onclick="closeEmailModal()"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showPaymentModal() {
    document.getElementById('paymentModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function showEmailModal() {
    document.getElementById('emailModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeEmailModal() {
    document.getElementById('emailModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// Fechar modais ao clicar fora
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('bg-gray-500')) {
        closePaymentModal();
        closeEmailModal();
    }
});

// Fechar modais com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePaymentModal();
        closeEmailModal();
    }
});
</script>
@endpush
@endsection