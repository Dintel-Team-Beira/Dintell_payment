@extends('layouts.admin')
@section('title', 'Relatórios Administrativos')

@section('content')
<div class="container px-6 py-8 mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Relatórios Administrativos</h1>
                <p class="mt-2 text-gray-600">Análises e insights do sistema de faturação</p>
            </div>
            <div class="flex space-x-3">
                <div class="relative">
                    <select id="periodSelector" onchange="updateDashboard()"
                            class="py-2 pl-3 pr-10 text-sm bg-white border border-gray-300 rounded-md appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="today">Hoje</option>
                        <option value="week">Esta
                        <option value="today">Hoje</option>
                        <option value="week">Esta Semana</option>
                        <option value="month" selected>Este Mês</option>
                        <option value="quarter">Este Trimestre</option>
                        <option value="year">Este Ano</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 pointer-events-none">
                        <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                        </svg>
                    </div>
                </div>
                <button onclick="openCustomReportModal()"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Relatório Personalizado
                </button>
            </div>
        </div>
    </div>

    <!-- Main Stats Cards -->
    <div class="grid grid-cols-1 gap-5 mb-8 sm:grid-cols-2 lg:grid-cols-4">
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
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-bold text-gray-900" id="totalRevenue">
                                {{ number_format($stats['total_revenue'] ?? 0, 2) }} MT
                            </div>
                            <div class="flex items-baseline ml-2 text-sm font-semibold" id="revenueGrowth">
                                @if(($stats['revenue_growth'] ?? 0) >= 0)
                                    <span class="text-green-600">+{{ number_format($stats['revenue_growth'] ?? 0, 1) }}%</span>
                                    <svg class="w-3 h-3 ml-1 text-green-600" fill="currentColor" viewBox="0 0 12 12">
                                        <path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM3 8l4-4 4 4H3z"/>
                                    </svg>
                                @else
                                    <span class="text-red-600">{{ number_format($stats['revenue_growth'] ?? 0, 1) }}%</span>
                                    <svg class="w-3 h-3 ml-1 text-red-600" fill="currentColor" viewBox="0 0 12 12">
                                        <path d="M8.293 6.707a1 1 0 001.414-1.414l-1.414 1.414zM9 4L5 8l-4-4h8z"/>
                                    </svg>
                                @endif
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

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
                        <dt class="text-sm font-medium text-gray-500 truncate">Faturas Criadas</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-bold text-gray-900" id="totalInvoices">
                                {{ number_format($stats['invoices_created'] ?? 0) }}
                            </div>
                            <div class="flex items-baseline ml-2 text-sm font-semibold" id="invoicesGrowth">
                                @if(($stats['invoices_growth'] ?? 0) >= 0)
                                    <span class="text-green-600">+{{ number_format($stats['invoices_growth'] ?? 0, 1) }}%</span>
                                    <svg class="w-3 h-3 ml-1 text-green-600" fill="currentColor" viewBox="0 0 12 12">
                                        <path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM3 8l4-4 4 4H3z"/>
                                    </svg>
                                @else
                                    <span class="text-red-600">{{ number_format($stats['invoices_growth'] ?? 0, 1) }}%</span>
                                    <svg class="w-3 h-3 ml-1 text-red-600" fill="currentColor" viewBox="0 0 12 12">
                                        <path d="M8.293 6.707a1 1 0 001.414-1.414l-1.414 1.414zM9 4L5 8l-4-4h8z"/>
                                    </svg>
                                @endif
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-8 h-8 bg-purple-100 rounded-md">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1 w-0 ml-5">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Empresas Ativas</dt>
                        <dd class="text-2xl font-bold text-gray-900" id="activeCompanies">
                            {{ number_format($stats['active_companies'] ?? 0) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-8 h-8 bg-yellow-100 rounded-md">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1 w-0 ml-5">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Faturas Vencidas</dt>
                        <dd class="text-2xl font-bold text-gray-900" id="overdueInvoices">
                            {{ number_format($stats['overdue_invoices'] ?? 0) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Type Cards -->
    <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-3">
        <!-- Revenue Report -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-gray-900">Relatório de Receita</h3>
                        <p class="text-sm text-gray-500">Análise financeira detalhada</p>
                    </div>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Receita este mês:</span>
                    <span class="font-medium">{{ number_format($revenueStats['current_month'] ?? 0, 2) }} MT</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Mês anterior:</span>
                    <span class="font-medium">{{ number_format($revenueStats['previous_month'] ?? 0, 2) }} MT</span>
                </div>
                <div class="pt-3">
                    <a href="{{ route('admin.reports.revenue') }}"
                       class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-green-700 bg-green-100 border border-transparent rounded-md hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Ver Relatório Completo
                        <svg class="ml-2 -mr-0.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Clients Report -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-gray-900">Relatório de Clientes</h3>
                        <p class="text-sm text-gray-500">Análise da base de clientes</p>
                    </div>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Total de clientes:</span>
                    <span class="font-medium">{{ number_format($clientStats['total'] ?? 0) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Novos este mês:</span>
                    <span class="font-medium">{{ number_format($clientStats['new_this_month'] ?? 0) }}</span>
                </div>
                <div class="pt-3">
                    <a href="{{ route('admin.reports.clients') }}"
                       class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-blue-700 bg-blue-100 border border-transparent rounded-md hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Ver Relatório Completo
                        <svg class="ml-2 -mr-0.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Usage Report -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-gray-900">Uso do Sistema</h3>
                        <p class="text-sm text-gray-500">Métricas de utilização</p>
                    </div>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Usuários ativos:</span>
                    <span class="font-medium">{{ number_format($usageStats['active_users'] ?? 0) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Logins hoje:</span>
                    <span class="font-medium">{{ number_format($usageStats['daily_logins'] ?? 0) }}</span>
                </div>
                <div class="pt-3">
                    <a href="{{ route('admin.reports.usage') }}"
                       class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-purple-700 bg-purple-100 border border-transparent rounded-md hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Ver Relatório Completo
                        <svg class="ml-2 -mr-0.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-2">
        <!-- Revenue Chart -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Receita Mensal</h3>
                <div class="flex space-x-2">
                    <button onclick="toggleChart('revenue', 'bar')"
                            class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Barras
                    </button>
                    <button onclick="toggleChart('revenue', 'line')"
                            class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Linha
                    </button>
                </div>
            </div>
            <div id="revenueChart" style="height: 300px;"></div>
        </div>

        <!-- Invoices Chart -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Status das Faturas</h3>
                <button onclick="refreshChart('invoices')"
                        class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                    Atualizar
                </button>
            </div>
            <div id="invoicesChart" style="height: 300px;"></div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Relatórios Recentes</h3>
                <a href="{{ route('admin.reports.history') }}"
                   class="text-sm text-blue-600 hover:text-blue-500">Ver todos</a>
            </div>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($recentReports ?? [] as $report)
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $report->name }}</div>
                            <div class="text-sm text-gray-500">
                                {{ $report->type_name }} • Gerado por {{ $report->generatedBy->name }} • {{ $report->generated_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($report->status === 'completed') bg-green-100 text-green-800
                            @elseif($report->status === 'processing') bg-yellow-100 text-yellow-800
                            @elseif($report->status === 'failed') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $report->status_name }}
                        </span>
                        @if($report->status === 'completed' && $report->file_path)
                            <a href="{{ asset('storage/' . $report->file_path) }}"
                               class="text-blue-600 hover:text-blue-500" download>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum relatório gerado</h3>
                    <p class="mt-1 text-sm text-gray-500">Gere seu primeiro relatório personalizado.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Custom Report Modal -->
<div id="customReportModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.reports.generate') }}" method="POST" id="customReportForm">
                @csrf
                <div class="px-6 pt-6 pb-4 bg-white">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Relatório Personalizado</h3>
                        <button type="button" onclick="closeCustomReportModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="custom_report_type" class="block text-sm font-medium text-gray-700">Tipo de Relatório</label>
                            <select name="type" id="custom_report_type" required
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach(\App\Models\AdminReport::TYPES as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="custom_date_from" class="block text-sm font-medium text-gray-700">Data Inicial</label>
                                <input type="date" name="date_from" id="custom_date_from" required
                                       value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>


