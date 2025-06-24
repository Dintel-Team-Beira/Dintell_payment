@extends('layouts.app')

@section('title', 'Cotação ' . $quote->quote_number)
@section('subtitle', 'Detalhes da cotação para ' . $quote->client->name)

@section('header-actions')
<div class="flex space-x-3">
    <!-- Status da Cotação -->
    <div class="flex items-center" x-data="{ open: false }">
        <button @click="open = !open"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @php
                $statusLabels = [
                    'draft' => 'Rascunho',
                    'sent' => 'Enviada',
                    'accepted' => 'Aceita',
                    'rejected' => 'Rejeitada',
                    'expired' => 'Expirada'
                ];
            @endphp
            Alterar Status: {{ $statusLabels[$quote->status] }}
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

            @foreach(['draft' => 'Rascunho', 'sent' => 'Enviada', 'accepted' => 'Aceita', 'rejected' => 'Rejeitada', 'expired' => 'Expirada'] as $status => $label)
                @if($status !== $quote->status)
                    <form action="{{ route('quotes.update-status', $quote) }}" method="POST" class="block">
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

    <!-- Ações -->
    @if($quote->canConvertToInvoice())
        <form action="{{ route('quotes.convert-to-invoice', $quote) }}" method="POST" class="inline">
            @csrf
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-purple-600 rounded-lg hover:bg-purple-700"
                    onclick="return confirm('Deseja converter esta cotação em fatura?')">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Converter em Fatura
            </button>
        </form>
    @endif

    <a href="{{ route('quotes.download-pdf', $quote) }}"
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
        </svg>
        Baixar PDF
    </a>

    @if($quote->status !== 'accepted' || !$quote->converted_to_invoice_at)
        <a href="{{ route('quotes.edit', $quote) }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar
        </a>
    @endif

    <a href="{{ route('quotes.index') }}"
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
    @if($quote->isExpired() && $quote->status !== 'accepted')
        <div class="p-4 border border-red-200 rounded-lg bg-red-50">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Cotação Expirada</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>Esta cotação expirou em {{ $quote->valid_until->format('d/m/Y') }}. Entre em contato com o cliente para renovar.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($quote->converted_to_invoice_at && $quote->invoice)
        <div class="p-4 border border-green-200 rounded-lg bg-green-50">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Cotação Convertida</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>Esta cotação foi convertida em fatura em {{ $quote->converted_to_invoice_at->format('d/m/Y H:i') }}.</p>
                        <p class="mt-1">
                            <a href="{{ route('invoices.show', $quote->invoice) }}" class="font-medium underline">
                                Ver Fatura {{ $quote->invoice->invoice_number }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Informações da Cotação -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Informações da Cotação</h3>
                @php
                    $statusClasses = [
                        'draft' => 'bg-gray-100 text-gray-800',
                        'sent' => 'bg-yellow-100 text-yellow-800',
                        'accepted' => 'bg-green-100 text-green-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        'expired' => 'bg-gray-100 text-gray-800'
                    ];
                    $statusLabels = [
                        'draft' => 'Rascunho',
                        'sent' => 'Enviada',
                        'accepted' => 'Aceita',
                        'rejected' => 'Rejeitada',
                        'expired' => 'Expirada'
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses[$quote->status] }}">
                    {{ $statusLabels[$quote->status] }}
                </span>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Número da Cotação</h4>
                    <p class="text-lg font-semibold text-gray-900">{{ $quote->quote_number }}</p>
                </div>
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Data da Cotação</h4>
                    <p class="text-lg font-semibold text-gray-900">{{ $quote->quote_date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Válida até</h4>
                    <p class="text-lg font-semibold {{ $quote->isExpired() && $quote->status !== 'accepted' ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $quote->valid_until->format('d/m/Y') }}
                    </p>
                </div>
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Criada em</h4>
                    <p class="text-lg font-semibold text-gray-900">{{ $quote->created_at->format('d/m/Y H:i') }}</p>
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
                    <p class="text-lg font-semibold text-gray-900">{{ $quote->client->name }}</p>
                </div>
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Email</h4>
                    <p class="text-lg text-gray-900">{{ $quote->client->email }}</p>
                </div>
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Telefone</h4>
                    <p class="text-lg text-gray-900">{{ $quote->client->phone ?? 'Não informado' }}</p>
                </div>
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">NUIT</h4>
                    <p class="text-lg text-gray-900">{{ $quote->client->tax_number ?? 'Não informado' }}</p>
                </div>
                @if($quote->client->address)
                <div class="md:col-span-2">
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Endereço</h4>
                    <p class="text-lg text-gray-900 whitespace-pre-line">{{ $quote->client->address }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Itens da Cotação -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Itens da Cotação</h3>
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
                    @foreach($quote->items as $item)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $item->description }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm text-gray-900">{{ number_format($item->unit_price, 2) }} MT</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="text-sm text-gray-900">{{ number_format($item->tax_rate, 1) }}%</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm font-medium text-gray-900">{{ number_format($item->total, 2) }} MT</div>
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
                            <span class="text-gray-900">{{ number_format($quote->subtotal, 2) }} MT</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-700">IVA:</span>
                            <span class="text-gray-900">{{ number_format($quote->tax_amount, 2) }} MT</span>
                        </div>
                        <div class="pt-2 border-t border-gray-200">
                            <div class="flex justify-between">
                                <span class="text-lg font-bold text-gray-900">TOTAL:</span>
                                <span class="text-xl font-bold text-green-600">{{ number_format($quote->total, 2) }} MT</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Observações e Termos -->
    @if($quote->notes || $quote->terms_conditions)
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        @if($quote->notes)
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Observações</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700 whitespace-pre-line">{{ $quote->notes }}</p>
            </div>
        </div>
        @endif

        @if($quote->terms_conditions)
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Termos e Condições</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700 whitespace-pre-line">{{ $quote->terms_conditions }}</p>
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
                                        <p class="text-sm text-gray-500">Cotação criada</p>
                                    </div>
                                    <div class="text-sm text-right text-gray-500 whitespace-nowrap">
                                        {{ $quote->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                    @if($quote->updated_at != $quote->created_at)
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
                                        <p class="text-sm text-gray-500">Cotação atualizada</p>
                                    </div>
                                    <div class="text-sm text-right text-gray-500 whitespace-nowrap">
                                        {{ $quote->updated_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endif

                    @if($quote->converted_to_invoice_at)
                    <li>
                        <div class="relative">
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="flex items-center justify-center w-8 h-8 bg-green-500 rounded-full ring-8 ring-white">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                    <div>
                                        <p class="text-sm text-gray-500">
                                            Convertida em fatura
                                            @if($quote->invoice)
                                                <a href="{{ route('invoices.show', $quote->invoice) }}" class="font-medium text-blue-600 hover:text-blue-500">
                                                    {{ $quote->invoice->invoice_number }}
                                                </a>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-sm text-right text-gray-500 whitespace-nowrap">
                                        {{ $quote->converted_to_invoice_at->format('d/m/Y H:i') }}
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
@endsection