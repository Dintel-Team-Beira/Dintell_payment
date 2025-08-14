@extends('layouts.app')

@section('title', 'Facturas')
@section('subtitle', 'Gerencie suas facturas e recebimentos')

@section('header-actions')
<div class="flex space-x-3">
    <!-- Botão de Exportar -->
    <div class="relative" x-data="{ open: false }">
        {{-- <button @click="open = !open" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m-8 9a9 9 0 110-18 9 9 0 010 18z"/>
            </svg>
            Exportar
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button> --}}

        <div x-show="open" @click.away="open = false" x-cloak
             class="absolute right-0 z-10 w-48 mt-2 bg-white border border-gray-200 divide-y divide-gray-100 rounded-lg shadow-lg">
            <div class="py-1">
                <a href="{{ route('invoices.export', ['format' => 'excel'] + request()->all()) }}"
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <svg class="w-4 h-4 mr-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                    </svg>
                    Excel (.xlsx)
                </a>
                <a href="{{ route('invoices.export', ['format' => 'csv'] + request()->all()) }}"
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <svg class="w-4 h-4 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5z"/>
                    </svg>
                    CSV
                </a>
                <a href="{{ route('invoices.export', ['format' => 'pdf'] + request()->all()) }}"
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <svg class="w-4 h-4 mr-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5z"/>
                    </svg>
                    PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Botão Nova Fatura -->
    <a href="{{ route('invoices.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nova Fatura
    </a>
</div>
@endsection

@section('content')
<!-- Estatísticas -->
<div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-5">
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
                <h3 class="text-sm font-medium text-gray-900">Total de Facturas</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_invoices'] }}</p>
                @if(isset($stats['invoices_growth']))
                    <p class="text-xs text-{{ $stats['invoices_growth'] >= 0 ? 'green' : 'red' }}-600">
                        {{ $stats['invoices_growth'] >= 0 ? '+' : '' }}{{ number_format($stats['invoices_growth'], 1) }}% vs mês anterior
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Pendentes</h3>
                <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['total_pending'], 2) }} MT</p>
                <p class="text-xs text-gray-500">
                    {{ $stats['pending_count'] }} facturas em aberto
                </p>
            </div>
        </div>
    </div>

    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Vencidas</h3>
                <p class="text-2xl font-bold text-red-600">{{ number_format($stats['total_overdue'], 2) }} MT</p>
                <p class="text-xs text-gray-500">
                    {{ $stats['count_overdue'] }} facturas vencidas
                </p>
            </div>
        </div>
    </div>

    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-emerald-100">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Pagas Este Mês</h3>
                <p class="text-2xl font-bold text-emerald-600">{{ number_format($stats['total_paid_this_month'], 2) }} MT</p>
                <p class="text-xs text-gray-500">
                    {{ $stats['paid_count_this_month'] }} facturas pagas
                </p>
            </div>
        </div>
    </div>

    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Média de Recebimento</h3>
                <p class="text-2xl font-bold text-purple-600">{{ $stats['avg_payment_days'] }}</p>
                <p class="text-xs text-gray-500">
                    dias para pagamento
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Filtros e Pesquisa -->
<div class="mb-8 bg-white border border-gray-200 shadow-sm rounded-xl" x-data="{ showFilters: {{ request()->hasAny(['status', 'client_id', 'date_from', 'date_to']) ? 'true' : 'false' }} }">
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
            <form method="GET" action="{{ ('invoices.index') }}" class="relative">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="client_id" value="{{ request('client_id') }}">
                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to" value="{{ request('date_to') }}">

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" placeholder="Pesquisar por número, cliente ou descrição..."
                           class="w-full py-3 pl-10 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           value="{{ request('search') }}">

                    @if(request('search'))
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <a href="{{ route('invoices.index', request()->except('search')) }}"
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
            <form method="GET" action="{{ ('invoices.index') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
                    <div>
                        <label for="status" class="block mb-2 text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Rascunho</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Enviada</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paga</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Vencida</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
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

                    <div class="flex items-end space-x-2">
                        <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                            Filtrar
                        </button>
                        <a href="{{ route('invoices.index') }}" class="flex-1 px-4 py-2 text-sm font-medium text-center text-white transition-colors bg-gray-500 rounded-lg hover:bg-gray-600">
                            Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lista de Facturas -->
<div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Facturas</h3>
            @if($invoices->total() > 0)
                <p class="text-sm text-gray-500">
                    Mostrando {{ $invoices->firstItem() }} a {{ $invoices->lastItem() }} de {{ $invoices->total() }} facturas
                </p>
            @endif
        </div>

        <!-- Seleção em massa -->
        @if($invoices->count() > 0)
        <div class="flex items-center space-x-4" x-data="{ selectedInvoices: [], selectAll: false }">
            <div class="flex items-center">
                <input type="checkbox" x-model="selectAll"
                       @change="selectAll ? selectedInvoices = {{ $invoices->pluck('id') }} : selectedInvoices = []"
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label class="ml-2 text-sm text-gray-700">Selecionar tudo</label>
            </div>

            <div x-show="selectedInvoices.length > 0" x-cloak class="flex items-center space-x-2">
                <span x-text="selectedInvoices.length + ' selecionadas'" class="text-sm text-gray-600"></span>

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                        Ações em massa
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                         class="absolute right-0 z-10 w-48 mt-2 bg-white border border-gray-200 divide-y divide-gray-100 rounded-lg shadow-lg">
                        <div class="py-1">
                            <button onclick="bulkUpdateStatus('sent')"
                                    class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Marcar como Enviada
                            </button>
                            <button onclick="bulkUpdateStatus('paid')"
                                    class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Marcar como Paga
                            </button>
                            <button onclick="bulkDownloadPDF()"
                                    class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Baixar PDFs
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if($invoices->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <input type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'invoice_number', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                            Número
                            @if(request('sort') === 'invoice_number')
                                <svg class="inline w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="{{ request('direction') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                </svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'client_name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                            Cliente
                        </a>
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'invoice_date', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                            Data
                        </a>
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'due_date', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                            Vencimento
                        </a>
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'total', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                            Total
                        </a>
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($invoices as $invoice)
                <tr class="transition-colors hover:bg-gray-50" x-data="{ selected: false }">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" x-model="selected"
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('invoices.show', $invoice) }}" class="text-sm font-medium text-blue-600 hover:text-blue-900">
                            {{ $invoice->invoice_number }}
                        </a>
                        @if($invoice->quote)
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                    </svg>
                                    Cotação {{ $invoice->quote->quote_number }}
                                </span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8">
                                <div class="flex items-center justify-center w-8 h-8 text-sm font-medium text-white bg-gray-500 rounded-full">
                                    {{ strtoupper(substr($invoice->client->name, 0, 2)) }}
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $invoice->client->name }}</div>
                                <div class="text-sm text-gray-500">{{ $invoice->client->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                        <div>{{ $invoice->invoice_date->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $invoice->invoice_date->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                        @if($invoice->isOverdue() && $invoice->status !== 'paid')
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium text-red-600">
                                    {{ $invoice->due_date->format('d/m/Y') }}
                                </span>
                            </div>
                            <div class="text-xs text-red-500">Venceu {{ $invoice->due_date->diffForHumans() }}</div>
                        @else
                            <div>{{ $invoice->due_date->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">
                                @if($invoice->due_date->isToday())
                                    Vence hoje
                                @elseif($invoice->due_date->isTomorrow())
                                    Vence amanhã
                                @elseif($invoice->due_date->diffInDays() <= 7)
                                    Vence em {{ abs(number_format($invoice->due_date->diffInDays())) }} dias
                                @else
                                    {{ $invoice->due_date->diffForHumans() }}
                                @endif
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
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
                            $statusIcons = [
                                'draft' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                                'sent' => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8',
                                'paid' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                'overdue' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                                'cancelled' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
                            ];
                        @endphp
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$invoice->status] }}">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusIcons[$invoice->status] }}"/>
                                </svg>
                                {{ $statusLabels[$invoice->status] }}
                            </span>
                        </div>

                        @if($invoice->status === 'sent' && $invoice->due_date->diffInDays() <= 3)
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Urgente
                                </span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-right text-gray-900 whitespace-nowrap">
                        <div class="text-lg font-bold">{{ number_format($invoice->total, 2) }} MT</div>
                        @if($invoice->paid_amount > 0)
                            <div class="text-xs text-gray-500">
                                Pago: {{ number_format($invoice->paid_amount, 2) }} MT
                            </div>
                            @if($invoice->remaining_amount > 0)
                                <div class="text-xs text-red-500">
                                    Restante: {{ number_format($invoice->remaining_amount, 2) }} MT
                                </div>
                            @endif
                        @endif
                        @if($invoice->tax_amount > 0)
                            <div class="text-xs text-gray-500">
                                IVA: {{ number_format($invoice->tax_amount, 2) }} MT
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center space-x-2">
                            <!-- Ver fatura -->
                            <a href="{{ route('invoices.show', $invoice) }}"
                               class="p-2 text-blue-600 transition-colors rounded-lg bg-blue-50 hover:bg-blue-100 hover:text-blue-700"
                               title="Ver fatura">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            <!-- Editar fatura -->
                            @if($invoice->status !== 'paid')
                                <a href="{{ route('invoices.edit', $invoice) }}"
                                   class="p-2 transition-colors rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 hover:text-amber-700"
                                   title="Editar fatura">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            @endif

                            <!-- Baixar PDF -->
                            <a href="{{ route('invoices.download-pdf', $invoice) }}"
                               class="p-2 text-green-600 transition-colors rounded-lg bg-green-50 hover:bg-green-100 hover:text-green-700"
                               title="Baixar PDF">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                </svg>
                            </a>

                            <!-- Marcar como paga -->
                            @if($invoice->status !== 'paid')
                                <button onclick="showPaymentModal({{ $invoice->id }})"
                                        class="p-2 text-purple-600 transition-colors rounded-lg bg-purple-50 hover:bg-purple-100 hover:text-purple-700"
                                        title="Marcar como paga">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                            @endif

                            <!-- Enviar por email -->
                            @if($invoice->status === 'draft')
                                <button onclick="showEmailModal({{ $invoice->id }})"
                                        class="p-2 text-indigo-600 transition-colors rounded-lg bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-700"
                                        title="Enviar por email">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            @endif

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
                                        <button onclick="duplicateInvoice({{ $invoice->id }})"
                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            Duplicar
                                        </button>

                                        @if($invoice->quote)
                                            <a href="{{ route('quotes.show', $invoice->quote) }}"
                                               class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Ver Cotação Original
                                            </a>
                                        @endif

                                        <button onclick="showInvoiceHistory({{ $invoice->id }})"
                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Ver Histórico
                                        </button>
                                    </div>

                                    @if($invoice->status === 'draft')
                                    <div class="py-1">
                                        <button onclick="deleteInvoice({{ $invoice->id }})"
                                                class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Eliminar
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

        {{ $invoices->withQueryString()->links() }}
    </div>

    @else
    <div class="px-6 py-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>

        @if(request()->hasAny(['search', 'status', 'client_id', 'date_from', 'date_to']))
            <h3 class="mt-4 text-lg font-medium text-gray-900">Nenhuma fatura encontrada</h3>
            <p class="mt-2 text-sm text-gray-500">Tente ajustar os filtros ou termo de pesquisa.</p>
            <div class="mt-6">
                <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Limpar filtros
                </a>
            </div>
        @else
            <h3 class="mt-4 text-lg font-medium text-gray-900">Nenhuma fatura criada</h3>
            <p class="mt-2 text-sm text-gray-500">Comece criando sua primeira fatura para gerir recebimentos!</p>
            <div class="mt-6">
                <a href="{{ route('invoices.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Criar Primera Fatura
                </a>
            </div>
        @endif
    </div>
    @endif
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

                        <div class="w-full mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
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
                                           class="block w-full p-2 mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                           value="{{ $invoice->client->email }}" required>
                                </div>
                                <div>
                                    <label for="email_subject" class="block text-sm font-medium text-gray-700">Assunto</label>
                                    <input type="text" name="subject" id="email_subject"
                                           class="block w-full p-2 mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                           value="Fatura {{ $invoice->invoice_number }}" required>
                                </div>
                                <div>
                                    <label for="email_message" class="block text-sm font-medium text-gray-700">Mensagem (opcional)</label>
                                    <textarea name="message" id="email_message" rows="3"
                                              class="block w-full p-5 mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
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

<!-- Modais e Scripts -->
@push('scripts')
<script>
// Função para mudança de itens por página
function changePerPage(perPage) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page'); // Reset para primeira página
    window.location.href = url.toString();
}

// Função para marcar fatura como paga
function markAsPaid(invoiceId) {
    if (confirm('Deseja marcar esta fatura como paga?')) {
        fetch(`/invoices/${invoiceId}/mark-as-paid`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Fatura marcada como paga!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Erro ao marcar fatura como paga: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Erro ao marcar fatura como paga', 'error');
            console.error('Error:', error);
        });
    }
}

// Função para enviar fatura por email
function sendInvoiceEmail(invoiceId) {
    if (confirm('Deseja enviar esta fatura por email para o cliente?')) {
        fetch(`/invoices/${invoiceId}/send-email`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Fatura enviada com sucesso!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Erro ao enviar fatura: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Erro ao enviar fatura', 'error');
            console.error('Error:', error);
        });
    }
}

// Função para eliminar fatura
function deleteInvoice(invoiceId) {
    if (confirm('Tem certeza que deseja eliminar esta fatura? Esta ação não pode ser desfeita.')) {
        fetch(`/invoices/${invoiceId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Fatura eliminada com sucesso!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Erro ao eliminar fatura: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Erro ao eliminar fatura', 'error');
            console.error('Error:', error);
        });
    }
}

// Função para duplicar fatura
function duplicateInvoice(invoiceId) {
    if (confirm('Deseja criar uma cópia desta fatura?')) {
        // Mostrar loading
        showNotification('Duplicando fatura...', 'info');

        fetch(`/invoices/${invoiceId}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
        })
        .then(response => {
            console.log('Response status:', response.status);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);

            if (data.success) {
                showNotification('Fatura duplicada com sucesso!', 'success');
                if (data.redirect_url) {
                    setTimeout(() => window.location.href = data.redirect_url, 1500);
                } else {
                    setTimeout(() => location.reload(), 1500);
                }
            } else {
                showNotification('Erro ao duplicar fatura: ' + (data.message || 'Erro desconhecido'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao duplicar fatura: ' + error.message, 'error');
        });
    }
}

// Função para mostrar histórico da fatura
function showInvoiceHistory(invoiceId) {
    // Por enquanto, redirecionar para a página de visualização
    // Mais tarde pode implementar um modal com histórico
    window.location.href = `/invoices/${invoiceId}`;
}

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

    // Animação de entrada
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // Remover após 5 segundos
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 5000);
}

// Ações em massa
function bulkUpdateStatus(status) {
    const selectedInvoices = Array.from(document.querySelectorAll('input[type="checkbox"]:checked'))
        .map(cb => cb.closest('tr'))
        .filter(row => row.querySelector('a[href*="/invoices/"]'))
        .map(row => {
            const link = row.querySelector('a[href*="/invoices/"]').href;
            return link.split('/').pop();
        });

    if (selectedInvoices.length === 0) {
        showNotification('Selecione pelo menos uma fatura', 'warning');
        return;
    }

    const statusLabels = {
        'sent': 'enviadas',
        'paid': 'pagas',
        'cancelled': 'canceladas'
    };

    if (confirm(`Deseja marcar ${selectedInvoices.length} facturas como ${statusLabels[status]}?`)) {
        fetch('/invoices/bulk-update-status', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                invoice_ids: selectedInvoices,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(`${selectedInvoices.length} facturas atualizadas com sucesso!`, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Erro ao atualizar facturas: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Erro ao atualizar facturas', 'error');
            console.error('Error:', error);
        });
    }
}

function bulkDownloadPDF() {
    const selectedInvoices = Array.from(document.querySelectorAll('input[type="checkbox"]:checked'))
        .map(cb => cb.closest('tr'))
        .filter(row => row.querySelector('a[href*="/invoices/"]'))
        .map(row => {
            const link = row.querySelector('a[href*="/invoices/"]').href;
            return link.split('/').pop();
        });

    if (selectedInvoices.length === 0) {
        showNotification('Selecione pelo menos uma fatura', 'warning');
        return;
    }

    window.open(`/invoices/bulk-download-pdf?invoice_ids=${selectedInvoices.join(',')}`);
}
</script>
@endpush

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
