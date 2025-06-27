@extends('layouts.app')

@section('title', 'Cotações')
@section('subtitle', 'Gerencie suas cotações e propostas comerciais')

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
                <a href="{{ route('quotes.export', ['format' => 'excel'] + request()->all()) }}"
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <svg class="w-4 h-4 mr-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                    </svg>
                    Excel (.xlsx)
                </a>
                <a href="{{ route('quotes.export', ['format' => 'csv'] + request()->all()) }}"
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <svg class="w-4 h-4 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5z"/>
                    </svg>
                    CSV
                </a>
                <a href="{{ route('quotes.export', ['format' => 'pdf'] + request()->all()) }}"
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <svg class="w-4 h-4 mr-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5z"/>
                    </svg>
                    PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Botão Nova Cotação -->
    <a href="{{ route('quotes.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nova Cotação
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
                <h3 class="text-sm font-medium text-gray-900">Total de Cotações</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_quotes'] }}</p>
                @if(isset($stats['quotes_growth']))
                    <p class="text-xs text-{{ $stats['quotes_growth'] >= 0 ? 'green' : 'red' }}-600">
                        {{ $stats['quotes_growth'] >= 0 ? '+' : '' }}{{ number_format($stats['quotes_growth'], 1) }}% vs mês anterior
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Valor Total</h3>
                <p class="text-2xl font-bold text-green-600">{{ number_format($stats['total_amount'], 2) }} MT</p>
                @if(isset($stats['amount_growth']))
                    <p class="text-xs text-{{ $stats['amount_growth'] >= 0 ? 'green' : 'red' }}-600">
                        {{ $stats['amount_growth'] >= 0 ? '+' : '' }}{{ number_format($stats['amount_growth'], 1) }}% vs mês anterior
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
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_count'] }}</p>
                <p class="text-xs text-gray-500">
                    {{ number_format($stats['pending_amount'] ?? 0, 2) }} MT em análise
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
                <h3 class="text-sm font-medium text-gray-900">Aceitas</h3>
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['accepted_count'] }}</p>
                <p class="text-xs text-gray-500">
                    {{ number_format($stats['accepted_amount'] ?? 0, 2) }} MT fechados
                </p>
            </div>
        </div>
    </div>

    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Taxa de Conversão</h3>
                <p class="text-2xl font-bold text-purple-600">{{ $stats['conversion_rate'] }}%</p>
                <p class="text-xs text-gray-500">
                    Meta: {{ $stats['conversion_target'] ?? 75 }}%
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
            <form method="GET" action="{{ route('quotes.index') }}" class="relative">
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
                            <a href="{{ route('quotes.index', request()->except('search')) }}"
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
            <form method="GET" action="{{ route('quotes.index') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
                    <div>
                        <label for="status" class="block mb-2 text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Rascunho</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Enviada</option>
                            <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Aceita</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeitada</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expirada</option>
                        </select>
                    </div>

                    <div>
                        <label for="client_id" class="block mb-2 text-sm font-medium text-gray-700">Cliente</label>
                        <select name="client_id" id="client_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                        <a href="{{ route('quotes.index') }}" class="flex-1 px-4 py-2 text-sm font-medium text-center text-white transition-colors bg-gray-500 rounded-lg hover:bg-gray-600">
                            Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lista de Cotações -->
<div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Cotações</h3>
            @if($quotes->total() > 0)
                <p class="text-sm text-gray-500">
                    Mostrando {{ $quotes->firstItem() }} a {{ $quotes->lastItem() }} de {{ $quotes->total() }} cotações
                </p>
            @endif
        </div>

        <!-- Seleção em massa -->
        @if($quotes->count() > 0)
        <div class="flex items-center space-x-4" x-data="{ selectedQuotes: [], selectAll: false }">
            <div class="flex items-center">
                <input type="checkbox" x-model="selectAll"
                       @change="selectAll ? selectedQuotes = {{ $quotes->pluck('id') }} : selectedQuotes = []"
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label class="ml-2 text-sm text-gray-700">Selecionar tudo</label>
            </div>

            <div x-show="selectedQuotes.length > 0" x-cloak class="flex items-center space-x-2">
                <span x-text="selectedQuotes.length + ' selecionadas'" class="text-sm text-gray-600"></span>

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
                            <button onclick="bulkUpdateStatus('accepted')"
                                    class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Marcar como Aceita
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

    @if($quotes->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <input type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'quote_number', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                            Número
                            @if(request('sort') === 'quote_number')
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
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'quote_date', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                            Data
                        </a>
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'valid_until', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                            Válida até
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
                @foreach($quotes as $quote)
                <tr class="transition-colors hover:bg-gray-50" x-data="{ selected: false }">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" x-model="selected"
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('quotes.show', $quote) }}" class="text-sm font-medium text-blue-600 hover:text-blue-900">
                            {{ $quote->quote_number }}
                        </a>
                        @if($quote->notes)
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                    </svg>
                                    Anotações
                                </span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8">
                                <div class="flex items-center justify-center w-8 h-8 text-sm font-medium text-white bg-gray-500 rounded-full">
                                    {{ strtoupper(substr($quote->client->name, 0, 2)) }}
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $quote->client->name }}</div>
                                <div class="text-sm text-gray-500">{{ $quote->client->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                        <div>{{ $quote->quote_date->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $quote->quote_date->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                        @if($quote->isExpired() && $quote->status !== 'accepted')
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium text-red-600">
                                    {{ $quote->valid_until->format('d/m/Y') }}
                                </span>
                            </div>
                            <div class="text-xs text-red-500">Expirou {{ $quote->valid_until->diffForHumans() }}</div>
                        @else
                            <div>{{ $quote->valid_until->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">
                                @if($quote->valid_until->isToday())
                                    Expira hoje
                                @elseif($quote->valid_until->isTomorrow())
                                    Expira amanhã
                                @elseif($quote->valid_until->diffInDays() <= 7)
                                    Expira em {{ $quote->valid_until->diffInDays() }} dias
                                @else
                                    {{ $quote->valid_until->diffForHumans() }}
                                @endif
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
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
                            $statusIcons = [
                                'draft' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                                'sent' => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8',
                                'accepted' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                'rejected' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                                'expired' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
                            ];
                        @endphp
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$quote->status] }}">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusIcons[$quote->status] }}"/>
                                </svg>
                                {{ $statusLabels[$quote->status] }}
                            </span>
                        </div>

                        @if($quote->status === 'sent' && $quote->valid_until->diffInDays() <= 3)
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
                        <div class="text-lg font-bold">{{ number_format($quote->total, 2) }} MT</div>
                        @if($quote->discount_amount > 0)
                            <div class="text-xs text-gray-500">
                                Desconto: {{ number_format($quote->discount_amount, 2) }} MT
                            </div>
                        @endif
                        @if($quote->tax_amount > 0)
                            <div class="text-xs text-gray-500">
                                IVA: {{ number_format($quote->tax_amount, 2) }} MT
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center space-x-2">
                            <!-- Ver cotação -->
                            <a href="{{ route('quotes.show', $quote) }}"
                               class="p-2 text-blue-600 transition-colors rounded-lg bg-blue-50 hover:bg-blue-100 hover:text-blue-700"
                               title="Ver cotação">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            <!-- Editar cotação -->
                            @if($quote->status !== 'accepted' || !$quote->converted_to_invoice_at)
                                <a href="{{ route('quotes.edit', $quote) }}"
                                   class="p-2 transition-colors rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 hover:text-amber-700"
                                   title="Editar cotação">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            @endif

                            <!-- Baixar PDF -->
                            <a href="{{ route('quotes.download-pdf', $quote) }}"
                               class="p-2 text-green-600 transition-colors rounded-lg bg-green-50 hover:bg-green-100 hover:text-green-700"
                               title="Baixar PDF">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                </svg>
                            </a>

                            <!-- Enviar por email -->
                            @if($quote->status === 'draft')
                                <button onclick="sendQuoteEmail({{ $quote->id }})"
                                        class="p-2 text-purple-600 transition-colors rounded-lg bg-purple-50 hover:bg-purple-100 hover:text-purple-700"
                                        title="Enviar por email">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            @endif

                            <!-- Converter em fatura -->
                            @if($quote->canConvertToInvoice())
                                <form action="{{ route('quotes.convert-to-invoice', $quote) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="p-2 text-indigo-600 transition-colors rounded-lg bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-700"
                                            title="Converter em fatura"
                                            onclick="return confirm('Deseja converter esta cotação em fatura?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </button>
                                </form>
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
                                        <button onclick="duplicateQuote({{ $quote->id }})"
                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            Duplicar
                                        </button>

                                        @if($quote->status !== 'accepted')
                                            <button onclick="updateQuoteStatus({{ $quote->id }}, 'rejected')"
                                                    class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Marcar como Rejeitada
                                            </button>
                                        @endif

                                        <button onclick="showQuoteHistory({{ $quote->id }})"
                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Ver Histórico
                                        </button>
                                    </div>

                                    @if($quote->status === 'draft')
                                    <div class="py-1">
                                        <button onclick="deleteQuote({{ $quote->id }})"
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

        {{ $quotes->withQueryString()->links() }}
    </div>

    @else
    <div class="px-6 py-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>

        @if(request()->hasAny(['search', 'status', 'client_id', 'date_from', 'date_to']))
            <h3 class="mt-4 text-lg font-medium text-gray-900">Nenhuma cotação encontrada</h3>
            <p class="mt-2 text-sm text-gray-500">Tente ajustar os filtros ou termo de pesquisa.</p>
            <div class="mt-6">
                <a href="{{ route('quotes.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Limpar filtros
                </a>
            </div>
        @else
            <h3 class="mt-4 text-lg font-medium text-gray-900">Nenhuma cotação criada</h3>
            <p class="mt-2 text-sm text-gray-500">Comece criando sua primeira cotação para gerir propostas comerciais!</p>
            <div class="mt-6">
                <a href="{{ route('quotes.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Criar Primera Cotação
                </a>
            </div>
        @endif
    </div>
    @endif
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

// Função para enviar cotação por email
function sendQuoteEmail(quoteId) {
    if (confirm('Deseja enviar esta cotação por email para o cliente?')) {
        fetch(`/quotes/${quoteId}/send-email`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Cotação enviada com sucesso!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Erro ao enviar cotação: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Erro ao enviar cotação', 'error');
            console.error('Error:', error);
        });
    }
}

// Função para atualizar status da cotação
function updateQuoteStatus(quoteId, status) {
    const statusLabels = {
        'sent': 'enviada',
        'accepted': 'aceita',
        'rejected': 'rejeitada'
    };

    if (confirm(`Deseja marcar esta cotação como ${statusLabels[status]}?`)) {
        fetch(`/quotes/${quoteId}/update-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(`Cotação marcada como ${statusLabels[status]}!`, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Erro ao atualizar status: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Erro ao atualizar status', 'error');
            console.error('Error:', error);
        });
    }
}

// Função para eliminar cotação
function deleteQuote(quoteId) {
    if (confirm('Tem certeza que deseja eliminar esta cotação? Esta ação não pode ser desfeita.')) {
        fetch(`/quotes/${quoteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Cotação eliminada com sucesso!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Erro ao eliminar cotação: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Erro ao eliminar cotação', 'error');
            console.error('Error:', error);
        });
    }
}

// Função para duplicar cotação
function duplicateQuote(quoteId) {
    if (confirm('Deseja criar uma cópia desta cotação?')) {
        fetch(`/quotes/${quoteId}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Cotação duplicada com sucesso!', 'success');
                if (data.redirect_url) {
                    setTimeout(() => window.location.href = data.redirect_url, 1500);
                } else {
                    setTimeout(() => location.reload(), 1500);
                }
            } else {
                showNotification('Erro ao duplicar cotação: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Erro ao duplicar cotação', 'error');
            console.error('Error:', error);
        });
    }
}

// Função para mostrar histórico da cotação
function showQuoteHistory(quoteId) {
    // Por enquanto, redirecionar para a página de visualização
    // Mais tarde pode implementar um modal com histórico
    window.location.href = `/quotes/${quoteId}`;
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
    const selectedQuotes = Array.from(document.querySelectorAll('input[type="checkbox"]:checked'))
        .map(cb => cb.closest('tr'))
        .filter(row => row.querySelector('a[href*="/quotes/"]'))
        .map(row => {
            const link = row.querySelector('a[href*="/quotes/"]').href;
            return link.split('/').pop();
        });

    if (selectedQuotes.length === 0) {
        showNotification('Selecione pelo menos uma cotação', 'warning');
        return;
    }

    const statusLabels = {
        'sent': 'enviadas',
        'accepted': 'aceitas',
        'rejected': 'rejeitadas'
    };

    if (confirm(`Deseja marcar ${selectedQuotes.length} cotações como ${statusLabels[status]}?`)) {
        fetch('/quotes/bulk-update-status', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                quote_ids: selectedQuotes,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(`${selectedQuotes.length} cotações atualizadas com sucesso!`, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Erro ao atualizar cotações: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Erro ao atualizar cotações', 'error');
            console.error('Error:', error);
        });
    }
}

function bulkDownloadPDF() {
    const selectedQuotes = Array.from(document.querySelectorAll('input[type="checkbox"]:checked'))
        .map(cb => cb.closest('tr'))
        .filter(row => row.querySelector('a[href*="/quotes/"]'))
        .map(row => {
            const link = row.querySelector('a[href*="/quotes/"]').href;
            return link.split('/').pop();
        });

    if (selectedQuotes.length === 0) {
        showNotification('Selecione pelo menos uma cotação', 'warning');
        return;
    }

    window.open(`/quotes/bulk-download-pdf?quote_ids=${selectedQuotes.join(',')}`);
}
</script>
@endpush
@endsection