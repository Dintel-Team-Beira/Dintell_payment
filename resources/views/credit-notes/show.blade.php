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
<div class="max-w-4xl mx-auto space-y-8">
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
                    <p>Este documento representa um crédito ao cliente.</p>
                    @if($creditNote->relatedInvoice)
                        <p class="mt-1">
                            Relacionada à fatura:
                            <a href="{{ route('invoices.show', $creditNote->relatedInvoice) }}"
                               class="font-medium underline">
                                {{ $creditNote->relatedInvoice->invoice_number }}
                            </a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Informações da Nota de Crédito -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Informações da Nota de Crédito</h3>
                <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-green-800 bg-green-100 rounded-full">
                    Processada
                </span>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Número</h4>
                    <p class="text-lg font-semibold text-gray-900">{{ $creditNote->invoice_number }}</p>
                </div>
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Data</h4>
                    <p class="text-lg font-semibold text-gray-900">{{ $creditNote->invoice_date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Fatura Relacionada</h4>
                    @if($creditNote->relatedInvoice)
                        <a href="{{ route('invoices.show', $creditNote->relatedInvoice) }}"
                           class="text-lg font-semibold text-blue-600 hover:text-blue-800">
                            {{ $creditNote->relatedInvoice->invoice_number }}
                        </a>
                    @else
                        <p class="text-lg text-gray-500">-</p>
                    @endif
                </div>
            </div>

            <div class="mt-6">
                <h4 class="mb-2 text-sm font-medium text-gray-500">Motivo do Ajuste</h4>
                <div class="p-4 rounded-lg bg-gray-50">
                    <p class="text-gray-700">{{ $creditNote->adjustment_reason }}</p>
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
                    <p class="text-lg font-semibold text-gray-900">{{ $creditNote->client->name }}</p>
                </div>
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Email</h4>
                    <p class="text-lg text-gray-900">{{ $creditNote->client->email }}</p>
                </div>
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">Telefone</h4>
                    <p class="text-lg text-gray-900">{{ $creditNote->client->phone ?? 'Não informado' }}</p>
                </div>
                <div>
                    <h4 class="mb-1 text-sm font-medium text-gray-500">NUIT</h4>
                    <p class="text-lg text-gray-900">{{ $creditNote->client->nuit ?? 'Não informado' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Itens da Nota de Crédito -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Itens Creditados</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Descrição
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                            Qtd
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                            Preço Unit.
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                            IVA (%)
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                            Total
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($creditNote->items as $item)
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
                            <span class="text-gray-900">{{ number_format($creditNote->subtotal, 2) }} MT</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-700">IVA:</span>
                            <span class="text-gray-900">{{ number_format($creditNote->tax_amount, 2) }} MT</span>
                        </div>
                        @if($creditNote->discount_amount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-700">Desconto:</span>
                            <span class="text-gray-900">-{{ number_format($creditNote->discount_amount, 2) }} MT</span>
                        </div>
                        @endif
                        <div class="pt-2 border-t border-gray-200">
                            <div class="flex justify-between">
                                <span class="text-lg font-bold text-gray-900">TOTAL CREDITADO:</span>
                                <span class="text-xl font-bold text-red-600">-{{ number_format($creditNote->total, 2) }} MT</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Observações -->
    @if($creditNote->notes)
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Observações</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-700 whitespace-pre-line">{{ $creditNote->notes }}</p>
        </div>
    </div>
    @endif
</div>
@endsection
