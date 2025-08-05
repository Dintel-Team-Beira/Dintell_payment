@extends('layouts.admin')

@section('title', 'Relatório de Clientes')

@section('header-actions')
<div class="flex items-center gap-x-4">
    <!-- Filtros -->
    <form method="GET" class="flex items-center gap-x-3">
        <select name="period" class="px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
            <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Hoje</option>
            <option value="yesterday" {{ $period == 'yesterday' ? 'selected' : '' }}>Ontem</option>
            <option value="this_week" {{ $period == 'this_week' ? 'selected' : '' }}>Esta Semana</option>
            <option value="last_week" {{ $period == 'last_week' ? 'selected' : '' }}>Semana Passada</option>
            <option value="this_month" {{ $period == 'this_month' ? 'selected' : '' }}>Este Mês</option>
            <option value="last_month" {{ $period == 'last_month' ? 'selected' : '' }}>Mês Passado</option>
            <option value="this_quarter" {{ $period == 'this_quarter' ? 'selected' : '' }}>Este Trimestre</option>
            <option value="this_year" {{ $period == 'this_year' ? 'selected' : '' }}>Este Ano</option>
            <option value="last_year" {{ $period == 'last_year' ? 'selected' : '' }}>Ano Passado</option>
        </select>

        <select name="company_id" class="px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
            <option value="">Todas as Empresas</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ $company_id == $company->id ? 'selected' : '' }}>
                    {{ $company->name }}
                </option>
            @endforeach
        </select>
    </form>

    <!-- Botões de Exportação -->
    <div class="flex items-center gap-x-2">
        <a href="{{ route('admin.reports.export', ['type' => 'clients', 'period' => $period, 'company_id' => $company_id, 'format' => 'csv']) }}"
           class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Exportar CSV
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Métricas Principais -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total de Clientes -->
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total de Clientes</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['total_clients']) }}</p>
                        <p class="text-sm text-gray-500">clientes cadastrados</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clientes Ativos -->
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Clientes Ativos</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['active_clients']) }}</p>
                        <div class="flex items-center mt-1">
                            <span class="text-sm text-green-600">{{ number_format(($metrics['active_clients'] / max($metrics['total_clients'], 1)) * 100, 1) }}%</span>
                            <span class="ml-1 text-sm text-gray-500">do total</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Novos Clientes -->
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Novos Clientes</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['new_clients']) }}</p>
                        <p class="text-sm text-gray-500">no período selecionado</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Taxa de Retenção -->
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Taxa de Retenção</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($retentionRate, 1) }}%</p>
                        <p class="text-sm text-gray-500">clientes recorrentes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Novos Clientes por Período -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Novos Clientes por Período</h3>
                <p class="text-sm text-gray-500">Evolução do cadastro de clientes</p>
            </div>
            <div class="p-6">
                <canvas id="newClientsChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Clientes por Empresa -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Clientes por Empresa</h3>
                <p class="text-sm text-gray-500">Distribuição de clientes por empresa</p>
            </div>
            <div class="p-6">
                <canvas id="clientsByCompanyChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabelas -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Top Clientes por Faturas -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Top Clientes por Número de Faturas</h3>
                <p class="text-sm text-gray-500">Clientes mais ativos no período</p>
            </div>
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Cliente</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Empresa</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Faturas</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($topClientsByInvoices->take(10) as $client)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $client->name }}</div>
                                <div class="text-sm text-gray-500">{{ $client->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $client->company->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ number_format($client->invoices_count) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Clientes Inativos -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Clientes Inativos</h3>
                <p class="text-sm text-gray-500">Clientes que precisam de atenção</p>
            </div>
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Cliente</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Empresa</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Inativo desde</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($inactiveClients->take(10) as $client)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $client->name }}</div>
                                <div class="text-sm text-gray-500">{{ $client->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $client->company->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $client->updated_at->format('d/m/Y') }}</div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Novos Clientes por Período
    const newClientsCtx = document.getElementById('newClientsChart').getContext('2d');
    new Chart(newClientsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($newClientsByPeriod->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m'); })) !!},
            datasets: [{
                label: 'Novos Clientes',
                data: {!! json_encode($newClientsByPeriod->pluck('count')) !!},
                backgroundColor: 'rgba(147, 51, 234, 0.8)',
                borderColor: 'rgb(147, 51, 234)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Gráfico de Clientes por Empresa
    const clientsByCompanyCtx = document.getElementById('clientsByCompanyChart').getContext('2d');
    new Chart(clientsByCompanyCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($clientsByCompany->pluck('company.name')) !!},
            datasets: [{
                data: {!! json_encode($clientsByCompany->pluck('client_count')) !!},
                backgroundColor: [
                    '#3B82F6',
                    '#10B981',
                    '#F59E0B',
                    '#EF4444',
                    '#8B5CF6',
                    '#06B6D4',
                    '#F97316',
                    '#84CC16'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush
