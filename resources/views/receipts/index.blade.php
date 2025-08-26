@extends('layouts.app')

@section('title', 'Recibos')
@section('subtitle', 'Gerencie os recibos de pagamento')

@section('header-actions')
<div class="flex space-x-3">
    <!-- Botão de Exportar -->
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m-8 9a9 9 0 110-18 9 9 0 010 18z"/>
            </svg>
            Exportar
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="open" @click.away="open = false" x-cloak
             class="absolute right-0 z-10 w-48 mt-2 bg-white border border-gray-200 divide-y divide-gray-100 rounded-lg shadow-lg">
            <div class="py-1">
                <a href="{{ route('receipts.export', ['format' => 'excel'] + request()->all()) }}"
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <svg class="w-4 h-4 mr-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                    </svg>
                    Excel (.xlsx)
                </a>
                <a href="{{ route('receipts.export', ['format' => 'pdf'] + request()->all()) }}"
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <svg class="w-4 h-4 mr-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5z"/>
                    </svg>
                    PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<!-- Estatísticas -->
<div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Total de Recibos</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_receipts'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-emerald-100">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Este Mês</h3>
                <p class="text-2xl font-bold text-emerald-600">{{ number_format($stats['total_amount_this_month'] ?? 0, 2) }} MT</p>
                <p class="text-xs text-gray-500">
                    {{ $stats['receipts_this_month'] ?? 0 }} recibos
                </p>
            </div>
        </div>
    </div>

    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Este Ano</h3>
                <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_amount_this_year'] ?? 0, 2) }} MT</p>
            </div>
        </div>
    </div>

    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-indigo-100 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Média Mensal</h3>
                <p class="text-2xl font-bold text-indigo-600">{{ number_format(($stats['total_amount_this_year'] ?? 0) / 12, 2) }} MT</p>
            </div>
        </div>
    </div>
</div>

<!-- Filtros e Pesquisa -->
<div class="mb-8 bg-white border border-gray-200 shadow-sm rounded-xl" x-data="{ showFilters: {{ request()->hasAny(['status', 'client_id', 'payment_method', 'date_from', 'date_to']) ? 'true' : 'false' }} }">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Pesquisar e Filtrar</h3>
        <button @click="showFilters = !showFilters"
                class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"/>
            </svg>
            <span x-text="showFilters ? 'Ocultar Filtros' : 'Mostrar Filtros'"></span>
        </button>
    </div>

    <div class="p-6">
        <!-- Barra de Pesquisa -->
        <div class="mb-6">
            <form method="GET" action="{{ route('receipts.index') }}" class="relative">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="client_id" value="{{ request('client_id') }}">
                <input type="hidden" name="payment_method" value="{{ request('payment_method') }}">
                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to" value="{{ request('date_to') }}">

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" placeholder="Pesquisar por número, cliente ou referência..."
                           class="w-full py-3 pl-10 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           value="{{ request('search') }}">

                    @if(request('search'))
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <a href="{{ route('receipts.index', request()->except('search')) }}"
                               class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>

        <!-- Filtros Avançados -->
        <div x-show="showFilters" x-collapse>
            <form method="GET" action="{{ route('receipts.index') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
                    <div>
                        <label for="status" class="block mb-2 text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>

                    <div>
                        <label for="client_id" class="block mb-2 text-sm font-medium text-gray-700">Cliente</label>
                        <select name="client_id" id="client_id" class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos os clientes</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="payment_method" class="block mb-2 text-sm font-medium text-gray-700">Método de Pagamento</label>
                        <select name="payment_method" id="payment_method" class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos os métodos</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Dinheiro</option>
                            <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Transferência Bancária</option>
                            <option value="mobile_money" {{ request('payment_method') == 'mobile_money' ? 'selected' : '' }}>Dinheiro Móvel</option>
                            <option value="other" {{ request('payment_method') == 'other' ? 'selected' : '' }}>Outro</option>
                        </select>
                    </div>

                    <div>
                        <label for="date_from" class="block mb-2 text-sm font-medium text-gray-700">Data de</label>
                        <input type="date" name="date_from" id="date_from"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               value="{{ request('date_from') }}">
                    </div>

                    <div>
                        <label for="date_to" class="block mb-2 text-sm font-medium text-gray-700">Data até</label>
                        <input type="date" name="date_to" id="date_to"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="flex items-center mt-4 space-x-2">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                        Filtrar
                    </button>
                    <a href="{{ route('receipts.index') }}" class="px-4 py-2 text-sm font-medium text-white transition-colors bg-gray-500 rounded-lg hover:bg-gray-600">
                        Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lista de Recibos -->
<div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Recibos</h3>
            @if($receipts->total() > 0)
                <p class="text-sm text-gray-500">
                    Mostrando {{ $receipts->firstItem() }} a {{ $receipts->lastItem() }} de {{ $receipts->total() }} recibos
                </p>
            @endif
        </div>
    </div>

    @if($receipts->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'receipt_number', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                            Número do Recibo
                            @if(request('sort') === 'receipt_number')
                                <svg class="inline w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="{{ request('direction') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                </svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Fatura</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Cliente</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'payment_date', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                            Data de Pagamento
                        </a>
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Método</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'amount_paid', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                            Valor
                        </a>
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($receipts as $receipt)
                <tr class="transition-colors hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('receipts.show', $receipt) }}" class="text-sm font-medium text-blue-600 hover:text-blue-900">
                            {{ $receipt->receipt_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($receipt->invoice)
                            <a href="{{ route('invoices.show', $receipt->invoice) }}" class="text-sm text-blue-600 hover:text-blue-900">
                                {{ $receipt->invoice->invoice_number }}
                            </a>
                        @else
                            <span class="text-sm text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8">
                                <div class="flex items-center justify-center w-8 h-8 text-sm font-medium text-white bg-gray-500 rounded-full">
                                    {{ strtoupper(substr($receipt->client->name, 0, 2)) }}
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $receipt->client->name }}</div>
                                @if($receipt->client->email)
                                    <div class="text-sm text-gray-500">{{ $receipt->client->email }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                        <div>{{ $receipt->payment_date->format('d/m/Y H:i') }}</div>
                        <div class="text-xs text-gray-500">{{ $receipt->payment_date->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                        {{ $receipt->payment_method_label }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-right text-gray-900 whitespace-nowrap">
                        <div class="text-lg font-bold">{{ number_format($receipt->amount_paid, 2, ',', '.') }} MT</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
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
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$receipt->status] }}">
                            {{ $statusLabels[$receipt->status] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center space-x-2">
                            <!-- Ver recibo -->
                            <a href="{{ route('receipts.show', $receipt) }}"
                               class="p-2 text-blue-600 transition-colors rounded-lg bg-blue-50 hover:bg-blue-100 hover:text-blue-700"
                               title="Ver recibo">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            <!-- Baixar PDF -->
                            <a href="{{ route('receipts.download-pdf', $receipt) }}"
                               class="p-2 text-green-600 transition-colors rounded-lg bg-green-50 hover:bg-green-100 hover:text-green-700"
                               title="Baixar PDF">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                </svg>
                            </a>

                            <!-- Menu de ações -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                        class="p-2 text-gray-400 transition-colors rounded-lg bg-gray-50 hover:bg-gray-100 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                </button>

                                <div x-show="open" @click.away="open = false" x-cloak
                                     class="absolute right-0 z-10 w-48 mt-2 bg-white border border-gray-200 divide-y divide-gray-100 rounded-lg shadow-lg">
                                    <div class="py-1">
                                        <button onclick="duplicateReceipt({{ $receipt->id }})"
                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            Duplicar
                                        </button>

                                        @if($receipt->invoice)
                                            <a href="{{ route('invoices.show', $receipt->invoice) }}"
                                               class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Ver Fatura
                                            </a>
                                        @endif
                                    </div>

                                    @if($receipt->status === 'active')
                                    <div class="py-1">
                                        <button onclick="showCancelModal({{ $receipt->id }})"
                                                class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Cancelar Recibo
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200">
        <div class="flex items-center text-sm text-gray-700">
            <span>Mostrar</span>
            <select onchange="changePerPage(this.value)" class="mx-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
            </select>
            <span>registos por página</span>
        </div>

        {{ $receipts->withQueryString()->links() }}
    </div>

    @else
    <div class="px-6 py-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>

        @if(request()->hasAny(['search', 'status', 'client_id', 'payment_method', 'date_from', 'date_to']))
            <h3 class="mt-4 text-lg font-medium text-gray-900">Nenhum recibo encontrado</h3>
            <p class="mt-2 text-sm text-gray-500">Tente ajustar os filtros ou termo de pesquisa.</p>
            <div class="mt-6">
                <a href="{{ route('receipts.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Limpar filtros
                </a>
            </div>
        @else
            <h3 class="mt-4 text-lg font-medium text-gray-900">Nenhum recibo encontrado</h3>
            <p class="mt-2 text-sm text-gray-500">Recibos são gerados automaticamente quando faturas são marcadas como pagas.</p>
            <div class="mt-6">
                <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Ver Faturas
                </a>
            </div>
        @endif
    </div>
    @endif
</div>

<!-- Modal de Cancelamento de Recibo -->
<div id="cancelModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeCancelModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="cancelForm">
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
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Cancelar Recibo
                    </button>
                    <button type="button" onclick="closeCancelModal()"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar Operação
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentReceiptId = null;

// Função para mudança de itens por página
function changePerPage(perPage) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

// Função para duplicar recibo
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
                } else {
                    setTimeout(() => location.reload(), 1500);
                }
            } else {
                showNotification('Erro ao duplicar recibo: ' + (data.message || 'Erro desconhecido'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao duplicar recibo: ' + error.message, 'error');
        });
    }
}

// Função para mostrar modal de cancelamento
function showCancelModal(receiptId) {
    currentReceiptId = receiptId;
    document.getElementById('cancelModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    
    setTimeout(() => {
        document.getElementById('cancel_reason').focus();
    }, 100);
}

// Função para fechar modal de cancelamento
function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('cancelForm').reset();
    currentReceiptId = null;
}

// Submissão do formulário de cancelamento
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('cancelForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!currentReceiptId) {
            showNotification('Erro: recibo não identificado', 'error');
            return;
        }
        
        const reason = document.getElementById('cancel_reason').value.trim();
        
        if (!reason) {
            showNotification('Por favor, informe o motivo do cancelamento', 'warning');
            return;
        }
        
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        submitButton.textContent = 'Processando...';
        submitButton.disabled = true;
        
        fetch(`/receipts/${currentReceiptId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                closeCancelModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Erro ao cancelar recibo: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao cancelar recibo. Tente novamente.', 'error');
        })
        .finally(() => {
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        });
    });
});

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

// Fechar modais com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!document.getElementById('cancelModal').classList.contains('hidden')) {
            closeCancelModal();
        }
    }
});
</script>
@endpush
@endsection