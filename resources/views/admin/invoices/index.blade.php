@extends('layouts.admin')

@section('title', 'Faturas do Sistema')

@section('content')
<div class="container mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Faturas do Sistema</h1>
                <p class="mt-2 text-gray-600">Visão global de todas as faturas das empresas</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.invoices.export') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exportar Dados
                </a>
                <button onclick="openReportModal()"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Gerar Relatório
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 mb-8 sm:grid-cols-2 lg:grid-cols-4">
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-md">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1 w-0 ml-5">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total de Faturas</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_invoices'] ?? 0) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-md">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1 w-0 ml-5">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Receita Total</dt>
                        <dd class="text-2xl font-bold text-gray-900">
                            {{ number_format($stats['total_revenue'] ?? 0, 2) }} MT
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-8 h-8 bg-red-100 rounded-md">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1 w-0 ml-5">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Faturas Vencidas</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['overdue_invoices'] ?? 0) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-8 h-8 bg-yellow-100 rounded-md">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1 w-0 ml-5">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Valor Médio</dt>
                        <dd class="text-2xl font-bold text-gray-900">
                            {{ number_format($stats['average_invoice_value'] ?? 0, 2) }} MT
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="p-6 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm">
        <form method="GET" action="{{ route('admin.invoices.index') }}" class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
            <div class="flex-1">
                <label for="search" class="block mb-1 text-sm font-medium text-gray-700">Pesquisar</label>
                <input type="text"
                       name="search"
                       id="search"
                       value="{{ request('search') }}"
                       placeholder="Número da fatura, empresa ou cliente..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="w-full md:w-48">
                <label for="company" class="block mb-1 text-sm font-medium text-gray-700">Empresa</label>
                <select name="company"
                        id="company"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todas as empresas</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-48">
                <label for="status" class="block mb-1 text-sm font-medium text-gray-700">Status</label>
                <select name="status"
                        id="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todos os status</option>
                    @foreach(\App\Models\AdminInvoice::STATUSES as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-48">
                <label for="date_from" class="block mb-1 text-sm font-medium text-gray-700">Data Inicial</label>
                <input type="date"
                       name="date_from"
                       id="date_from"
                       value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="w-full md:w-48">
                <label for="date_to" class="block mb-1 text-sm font-medium text-gray-700">Data Final</label>
                <input type="date"
                       name="date_to"
                       id="date_to"
                       value="{{ request('date_to') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="flex space-x-2">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filtrar
                </button>
                <a href="{{ route('admin.invoices.index') }}"
                   class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-gray-300 border border-transparent rounded-md hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Fatura
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Empresa
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Cliente
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Data / Vencimento
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Valor
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Status
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Ações</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-gray-50 {{ $invoice->is_overdue ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</div>
                                <div class="text-xs text-gray-500">{{ $invoice->currency }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8">
                                        <div class="flex items-center justify-center w-8 h-8 bg-gray-200 rounded-full">
                                            @if($invoice->company?->logo)
                                                <img class="w-8 h-8 rounded-full" src="{{ asset('storage/'.$invoice->company->logo) }}" alt="">
                                            @else
                                             <div class="flex items-center justify-center w-10 h-10 bg-gray-200 rounded-lg">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $invoice->company?->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $invoice->company?->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $invoice->client?->name }}</div>
                                <div class="text-xs text-gray-500">{{ $invoice->client?->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                <div>{{ $invoice->date}}</div>
                                <div class="text-xs {{ $invoice->is_overdue ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                                    Venc: {{ $invoice->due_date->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $invoice->formatted_total }}</div>
                                @if($invoice->paid_amount > 0)
                                    <div class="text-xs text-green-600">
                                        Pago: {{ number_format($invoice->paid_amount, 2) }} {{ $invoice->currency }}
                                    </div>
                                @endif
                                @if($invoice->remaining_amount > 0)
                                    <div class="text-xs text-gray-500">
                                        Restante: {{ number_format($invoice->remaining_amount, 2) }} {{ $invoice->currency }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($invoice->status === 'paid') bg-green-100 text-green-800
                                    @elseif($invoice->status === 'overdue') bg-red-100 text-red-800
                                    @elseif($invoice->status === 'sent') bg-blue-100 text-blue-800
                                    @elseif($invoice->status === 'draft') bg-gray-100 text-gray-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ $invoice->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.invoices.show', $invoice->id) }}"
                                       class="text-blue-600 hover:text-blue-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.invoices.pdf', $invoice->id) }}"
                                       class="text-green-600 hover:text-green-900" target="_blank">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </a>
                                    @if($invoice->is_overdue)
                                        <button onclick="sendReminder({{ $invoice->id }})"
                                                class="text-orange-600 hover:text-orange-900">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma fatura encontrada</h3>
                                    <p class="mt-1 text-sm text-gray-500">Ajuste os filtros para ver mais resultados.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($invoices->hasPages())
            <div class="px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    <!-- Summary Card -->
    @if($invoices->count() > 0)
        <div class="p-6 mt-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <h3 class="mb-4 text-lg font-medium text-gray-900">Resumo da Página</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $invoices->count() }}</div>
                    <div class="text-sm text-gray-500">Faturas Mostradas</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">
                        {{ number_format($invoices->where('status', 'paid')->sum('total_amount'), 2) }} MT
                    </div>
                    <div class="text-sm text-gray-500">Total Pago</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">
                        {{ $invoices->where('is_overdue', true)->count() }}
                    </div>
                    <div class="text-sm text-gray-500">Vencidas</div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Report Modal -->
<div id="reportModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="#"" method="POST" id="reportForm">
                @csrf
                <div class="px-6 pt-6 pb-4 bg-white">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Gerar Relatório de Faturas</h3>
                        <button type="button" onclick="closeReportModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="report_type" class="block text-sm font-medium text-gray-700">Tipo de Relatório</label>
                            <select name="report_type" id="report_type" required
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="summary">Resumo Geral</option>
                                <option value="detailed">Relatório Detalhado</option>
                                <option value="overdue">Faturas Vencidas</option>
                                <option value="revenue">Receita por Período</option>
                                <option value="company_comparison">Comparação por Empresa</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="report_date_from" class="block text-sm font-medium text-gray-700">Data Inicial</label>
                                <input type="date" name="date_from" id="report_date_from" required
                                       value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="report_date_to" class="block text-sm font-medium text-gray-700">Data Final</label>
                                <input type="date" name="date_to" id="report_date_to" required
                                       value="{{ now()->format('Y-m-d') }}"
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label for="report_format" class="block text-sm font-medium text-gray-700">Formato</label>
                            <select name="format" id="report_format" required
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="include_details" value="1"
                                       class="text-blue-600 border-gray-300 rounded shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Incluir detalhes dos itens</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between px-6 py-3 bg-gray-50">
                    <button type="button" onclick="closeReportModal()"
                            class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Gerar Relatório
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openReportModal() {
        document.getElementById('reportModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeReportModal() {
        document.getElementById('reportModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        document.getElementById('reportForm').reset();
    }

    function sendReminder(invoiceId) {
        if (confirm('Enviar lembrete de pagamento para esta fatura?')) {
            fetch(`/admin/invoices/${invoiceId}/send-reminder`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Lembrete enviado com sucesso!');
                } else {
                    alert('Erro ao enviar lembrete. Tente novamente.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erro ao enviar lembrete. Tente novamente.');
            });
        }
    }

    // Auto-submit form on date change
    document.getElementById('date_from').addEventListener('change', function() {
        if (this.value && document.getElementById('date_to').value) {
            document.querySelector('form').submit();
        }
    });

    document.getElementById('date_to').addEventListener('change', function() {
        if (this.value && document.getElementById('date_from').value) {
            document.querySelector('form').submit();
        }
    });
</script>
@endpush
@endsection
