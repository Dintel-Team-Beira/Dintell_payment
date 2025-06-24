@extends('layouts.app')

@section('title', 'Fatura # {{ $invoice->invoice_number }}')

@section('header-actions')
<div class="flex items-center gap-x-4">
    <a href="{{ route('invoices.index') }}"
       class="flex items-center px-3 py-2 text-sm font-semibold text-white bg-gray-600 rounded-md shadow-sm hover:bg-gray-500">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Voltar
    </a>
    <div class="flex items-center gap-x-2">
        @if($invoice->status !== 'paid')
            <a href="{{ route('invoices.edit', $invoice) }}"
               class="flex items-center px-3 py-2 text-sm font-semibold text-white bg-yellow-600 rounded-md shadow-sm hover:bg-yellow-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                Editar
            </a>
        @endif
        <a href="{{ route('invoices.download-pdf', $invoice) }}"
           class="flex items-center px-3 py-2 text-sm font-semibold text-white bg-teal-600 rounded-md shadow-sm hover:bg-teal-500">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Baixar PDF
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="container px-4 mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Fatura #{{ $invoice->invoice_number }}</h1>
        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
            {{ $invoice->status === 'draft' ? 'bg-gray-100 text-gray-800' :
               ($invoice->status === 'sent' ? 'bg-yellow-100 text-yellow-800' :
               ($invoice->status === 'paid' ? 'bg-green-100 text-green-800' :
               ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-gray-300 text-gray-900'))) }}">
            {{ $invoice->status === 'draft' ? 'Rascunho' :
               ($invoice->status === 'sent' ? 'Enviada' :
               ($invoice->status === 'paid' ? 'Paga' :
               ($invoice->status === 'overdue' ? 'Vencida' : 'Cancelada'))) }}
        </span>
    </div>

    <!-- Invoice Details -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Detalhes da Fatura</h3>
        </div>
        <div class="grid grid-cols-1 gap-6 p-6 sm:grid-cols-2">
            <div>
                <h4 class="text-sm font-medium text-gray-600">Cliente</h4>
                <p class="mt-1 text-sm text-gray-900">{{ $invoice->client->name }}</p>
                @if($invoice->client->email)
                    <p class="text-sm text-gray-500">{{ $invoice->client->email }}</p>
                @endif
                @if($invoice->client->phone)
                    <p class="text-sm text-gray-500">{{ $invoice->client->phone }}</p>
                @endif
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-600">Informações da Fatura</h4>
                <p class="mt-1 text-sm text-gray-900">Data: {{ $invoice->invoice_date->format('d/m/Y') }}</p>
                <p class="text-sm text-gray-900">Vencimento:
                    <span class="{{ $invoice->isOverdue() && $invoice->status !== 'paid' ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                        {{ $invoice->due_date->format('d/m/Y') }}
                    </span>
                </p>
                @if($invoice->quote)
                    <p class="text-sm text-gray-900">
                        Cotação:
                        <a href="{{ route('quotes.show', $invoice->quote) }}" class="text-blue-600 hover:text-blue-900">
                            #{{ $invoice->quote->quote_number }}
                        </a>
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Invoice Items -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Itens da Fatura</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Descrição</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Quantidade</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Preço Unitário</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">IVA (%)</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoice->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $item->description }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ number_format($item->unit_price, 2) }} MT</td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ number_format($item->tax_rate, 2) }}%</td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ number_format($item->quantity * $item->unit_price * (1 + $item->tax_rate / 100), 2) }} MT</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2-12H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum item encontrado</h3>
                            <p class="mt-1 text-sm text-gray-500">Esta fatura não possui itens registrados.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-gray-200">
            <div class="flex justify-end">
                <div class="w-full sm:w-1/3">
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium text-gray-900">
                                {{ number_format($invoice->items->sum(fn($item) => $item->quantity * $item->unit_price), 2) }} MT
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">IVA:</span>
                            <span class="font-medium text-gray-900">
                                {{ number_format($invoice->items->sum(fn($item) => $item->quantity * $item->unit_price * ($item->tax_rate / 100)), 2) }} MT
                            </span>
                        </div>
                        <div class="flex justify-between pt-2 text-lg font-semibold text-gray-900 border-t">
                            <span>Total:</span>
                            <span>{{ number_format($invoice->total, 2) }} MT</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes and Terms -->
    @if($invoice->notes || $invoice->terms_conditions)
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Observações e Termos</h3>
        </div>
        <div class="grid grid-cols-1 gap-6 p-6 sm:grid-cols-2">
            @if($invoice->notes)
            <div>
                <h4 class="text-sm font-medium text-gray-600">Observações</h4>
                <p class="mt-1 text-sm text-gray-900">{{ $invoice->notes }}</p>
            </div>
            @endif
            @if($invoice->terms_conditions)
            <div>
                <h4 class="text-sm font-medium text-gray-600">Termos e Condições</h4>
                <p class="mt-1 text-sm text-gray-900">{{ $invoice->terms_conditions }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection