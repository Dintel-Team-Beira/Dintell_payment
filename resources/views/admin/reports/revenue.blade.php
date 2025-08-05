@extends('layouts.admin')

@section('title', 'Relatório de Uso do Sistema')

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
        <a href="{{ route('admin.reports.export', ['type' => 'usage', 'period' => $period, 'company_id' => $company_id, 'format' => 'csv']) }}"
           class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Exportar CSV
        </a>

        <button onclick="refreshData()" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Atualizar
        </button>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Métricas Principais -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total de Logins -->
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total de Logins</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['total_logins']) }}</p>
                        <p class="text-sm text-gray-500">acessos no período</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usuários Ativos -->
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Usuários Ativos</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['active_users']) }}</p>
                        <p class="text-sm text-gray-500">usuários únicos</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Faturas Criadas -->
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Faturas Criadas</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['invoices_created']) }}</p>
                        <p class="text-sm text-gray-500">documentos gerados</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cotações Criadas -->
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-orange-100 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Cotações Criadas</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['quotes_created']) }}</p>
                        <p class="text-sm text-gray-500">propostas geradas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance do Sistema -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Performance do Sistema</h3>
                <p class="text-sm text-gray-500">Métricas de desempenho</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-900">Tempo de Resposta Médio</div>
                        <div class="text-xs text-gray-500">Latência do servidor</div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-semibold text-green-600">{{ $systemPerformance['avg_response_time'] }}</div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-900">Taxa de Erro</div>
                        <div class="text-xs text-gray-500">Requisições com falha</div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-semibold text-yellow-600">{{ $systemPerformance['error_rate'] }}</div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-900">Uptime</div>
                        <div class="text-xs text-gray-500">Disponibilidade</div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-semibold text-green-600">{{ $systemPerformance['uptime'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Uso de Funcionalidades -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Uso de Funcionalidades</h3>
                <p class="text-sm text-gray-500">Recursos mais utilizados</p>
            </div>
            <div class="p-6">
                <canvas id="featureUsageChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Atividade por Empresa -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Uso por Empresa</h3>
                <p class="text-sm text-gray-500">Empresas mais ativas</p>
            </div>
            <div class="p-6 space-y-4">
                @foreach($usageByCompany->take(5) as $company)
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $company->name }}</div>
                        <div class="text-xs text-gray-500">{{ $company->users_count }} usuários ativos</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-semibold text-blue-600">{{ number_format($company->invoices_count) }}</div>
                        <div class="text-xs text-gray-500">faturas</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Atividade por Período -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Atividade por Período</h3>
                <p class="text-sm text-gray-500">Atividade do sistema ao longo do tempo</p>
            </div>
            <div class="p-6">
                <canvas id="activityChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Usuários Mais Ativos -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Usuários Mais Ativos</h3>
                <p class="text-sm text-gray-500">Top usuários por atividade</p>
            </div>
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Usuário</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Empresa</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Faturas</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Último Login</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($mostActiveUsers->take(10) as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8">
                                        <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full">
                                            <span class="text-sm font-medium text-blue-600">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->company->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ number_format($user->invoices_count) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca' }}
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
    // Gráfico de Uso de Funcionalidades
    const featureCtx = document.getElementById('featureUsageChart').getContext('2d');
    new Chart(featureCtx, {
        type: 'doughnut',
        data: {
            labels: ['Faturas', 'Cotações', 'Clientes', 'Relatórios'],
            datasets: [{
                data: [
                    {{ $featureUsage['invoices'] }},
                    {{ $featureUsage['quotes'] }},
                    {{ $featureUsage['clients'] }},
                    {{ $featureUsage['reports'] }}
                ],
                backgroundColor: [
                    '#3B82F6',
                    '#10B981',
                    '#F59E0B',
                    '#EF4444'
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

    // Gráfico de Atividade por Período
    const activityCtx = document.getElementById('activityChart').getContext('2d');
    new Chart(activityCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($activityByPeriod->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m'); })) !!},
            datasets: [{
                label: 'Atividade',
                data: {!! json_encode($activityByPeriod->pluck('activity_count')) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
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
});

function refreshData() {
    window.location.reload();
}
</script>
@endpush
@endsection
