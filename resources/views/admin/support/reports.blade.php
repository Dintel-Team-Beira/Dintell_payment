{{-- resources/views/admin/support/reports.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Relatórios de Suporte')

@section('content')
<div class="px-4">
    <!-- Header -->
    <div class="mb-8">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold leading-tight text-gray-900">Relatórios de Suporte</h1>
                <p class="mt-2 text-gray-600">Análise detalhada e métricas do sistema de suporte</p>
            </div>
            <div class="flex items-center mt-4 space-x-3 sm:mt-0">
                <!-- Period Selector -->
                <div class="flex items-center space-x-2">
                    <label for="period" class="text-sm font-medium text-gray-700">Período:</label>
                    <select id="period" onchange="updatePeriod(this.value)"
                            class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="7" {{ $period == 7 ? 'selected' : '' }}>Últimos 7 dias</option>
                        <option value="30" {{ $period == 30 ? 'selected' : '' }}>Últimos 30 dias</option>
                        <option value="90" {{ $period == 90 ? 'selected' : '' }}>Últimos 90 dias</option>
                        <option value="365" {{ $period == 365 ? 'selected' : '' }}>Último ano</option>
                    </select>
                </div>
                <button onclick="exportReport()"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exportar PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Tickets Criados -->
        <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-8 h-8 bg-green-500 rounded-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 w-0 ml-5">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Tickets Criados</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ $data['tickets_created'] ?? 0 }}</div>
                                <div class="flex items-baseline ml-2 text-sm font-semibold text-green-600">
                                    <svg class="self-center flex-shrink-0 w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    +12%
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tickets Resolvidos -->
        <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-8 h-8 bg-blue-500 rounded-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 w-0 ml-5">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Tickets Resolvidos</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ $data['tickets_resolved'] ?? 0 }}</div>
                                <div class="flex items-baseline ml-2 text-sm font-semibold text-green-600">
                                    <svg class="self-center flex-shrink-0 w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    +8%
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tempo Médio de Resposta -->
        <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-8 h-8 bg-yellow-500 rounded-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 w-0 ml-5">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Tempo Médio Resposta</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ $data['avg_response_time'] ?? 'N/A' }}</div>
                                <div class="flex items-baseline ml-2 text-sm font-semibold text-red-600">
                                    <svg class="self-center flex-shrink-0 w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 13.586V6a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    +5min
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Taxa de Satisfação -->
        <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-8 h-8 bg-purple-500 rounded-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 w-0 ml-5">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Taxa de Satisfação</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ $data['satisfaction_rate'] ?? 'N/A' }}</div>
                                <div class="flex items-baseline ml-2 text-sm font-semibold text-green-600">
                                    <svg class="self-center flex-shrink-0 w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    +3%
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 gap-8 mb-8 lg:grid-cols-2">
        <!-- Tickets por Categoria -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Tickets por Categoria</h3>
            </div>
            <div class="p-6">
                <canvas id="categoryChart" width="400" height="200"></canvas>
                <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
                    @foreach($data['tickets_by_category'] ?? [] as $category => $count)
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-2 category-color-{{ $loop->index }}"></div>
                        <span class="text-gray-600">{{ ucfirst($category) }}: </span>
                        <span class="ml-1 font-semibold">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Tickets por Prioridade -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Tickets por Prioridade</h3>
            </div>
            <div class="p-6">
                <canvas id="priorityChart" width="400" height="200"></canvas>
                <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
                    @foreach($data['tickets_by_priority'] ?? [] as $priority => $count)
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-2 priority-color-{{ $priority }}"></div>
                        <span class="text-gray-600">{{ ucfirst($priority) }}: </span>
                        <span class="ml-1 font-semibold">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Performance dos Agentes -->
    <div class="mb-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Performance dos Agentes</h3>
                <button onclick="exportAgentReport()"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exportar
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Agente</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Total Tickets</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Resolvidos</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Taxa Resolução</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Satisfação Média</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Performance</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($data['agent_performance'] ?? [] as $agent)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10">
                                    <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full">
                                        <span class="text-sm font-medium text-blue-800">{{ substr($agent['name'], 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $agent['name'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $agent['total_tickets'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $agent['resolved_tickets'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $resolutionRate = $agent['total_tickets'] > 0 ? ($agent['resolved_tickets'] / $agent['total_tickets'] * 100) : 0;
                            @endphp
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <div class="w-full h-2 bg-gray-200 rounded-full">
                                        <div class="bg-{{ $resolutionRate >= 80 ? 'green' : ($resolutionRate >= 60 ? 'yellow' : 'red') }}-600 h-2 rounded-full"
                                             style="width: {{ $resolutionRate }}%"></div>
                                    </div>
                                </div>
                                <div class="ml-2 text-sm font-medium text-gray-900">{{ number_format($resolutionRate, 1) }}%</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($agent['avg_satisfaction'])
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $agent['avg_satisfaction'] ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                    <span class="ml-1 text-sm text-gray-600">({{ number_format($agent['avg_satisfaction'], 1) }})</span>
                                </div>
                            @else
                                <span class="text-sm text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $performance = 'Baixa';
                                $performanceColor = 'red';
                                if ($resolutionRate >= 80 && ($agent['avg_satisfaction'] ?? 0) >= 4) {
                                    $performance = 'Excelente';
                                    $performanceColor = 'green';
                                } elseif ($resolutionRate >= 70 && ($agent['avg_satisfaction'] ?? 0) >= 3.5) {
                                    $performance = 'Boa';
                                    $performanceColor = 'blue';
                                } elseif ($resolutionRate >= 60) {
                                    $performance = 'Média';
                                    $performanceColor = 'yellow';
                                }
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $performanceColor }}-100 text-{{ $performanceColor }}-800">
                                {{ $performance }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Nenhum agente com atividade no período</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tempo de Resposta Timeline -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Tempo de Resposta - Últimos {{ $period }} dias</h3>
        </div>
        <div class="p-6">
            <canvas id="responseTimeChart" width="800" height="300"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});

function initializeCharts() {
    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryData = @json(array_values($data['tickets_by_category'] ?? []));
    const categoryLabels = @json(array_map('ucfirst', array_keys($data['tickets_by_category'] ?? [])));

    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryData,
                backgroundColor: [
                    '#3B82F6', // blue
                    '#10B981', // green
                    '#F59E0B', // yellow
                    '#EF4444', // red
                    '#8B5CF6', // purple
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Priority Chart
    const priorityCtx = document.getElementById('priorityChart').getContext('2d');
    const priorityData = @json(array_values($data['tickets_by_priority'] ?? []));
    const priorityLabels = @json(array_map('ucfirst', array_keys($data['tickets_by_priority'] ?? [])));

    new Chart(priorityCtx, {
        type: 'bar',
        data: {
            labels: priorityLabels,
            datasets: [{
                data: priorityData,
                backgroundColor: [
                    '#6B7280', // low - gray
                    '#3B82F6', // medium - blue
                    '#F59E0B', // high - orange
                    '#EF4444', // urgent - red
                ],
                borderWidth: 1,
                borderRadius: 4
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

    // Response Time Chart
    const responseTimeCtx = document.getElementById('responseTimeChart').getContext('2d');

    // Sample data - replace with actual data from controller
    const responseTimeData = generateSampleResponseTimeData({{ $period }});

    new Chart(responseTimeCtx, {
        type: 'line',
        data: {
            labels: responseTimeData.labels,
            datasets: [{
                label: 'Tempo Médio (horas)',
                data: responseTimeData.data,
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + 'h';
                        }
                    }
                }
            }
        }
    });
}

function generateSampleResponseTimeData(days) {
    const labels = [];
    const data = [];

    for (let i = days - 1; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        labels.push(date.toLocaleDateString('pt-BR', { month: 'short', day: 'numeric' }));

        // Generate sample response time data (2-8 hours)
        data.push(Math.random() * 6 + 2);
    }

    return { labels, data };
}

function updatePeriod(period) {
    window.location.href = `{{ route('admin.support.reports') }}?period=${period}`;
}

function exportReport() {
    // Create export URL with current period
    const period = document.getElementById('period').value;
    window.open(`{{ route('admin.support.reports') }}?period=${period}&export=pdf`, '_blank');
}

function exportAgentReport() {
    // Export agent performance data
    const period = document.getElementById('period').value;
    window.open(`{{ route('admin.support.reports') }}?period=${period}&export=agents`, '_blank');
}
</script>
@endpush

@push('styles')
<style>
/* Custom colors for categories and priorities */
.category-color-0 { background-color: #3B82F6; }
.category-color-1 { background-color: #10B981; }
.category-color-2 { background-color: #F59E0B; }
.category-color-3 { background-color: #EF4444; }
.category-color-4 { background-color: #8B5CF6; }

.priority-color-low { background-color: #6B7280; }
.priority-color-medium { background-color: #3B82F6; }
.priority-color-high { background-color: #F59E0B; }
.priority-color-urgent { background-color: #EF4444; }

/* Chart containers */
canvas {
    max-height: 300px;
}

/* Performance indicators */
.bg-green-100 { background-color: #dcfce7; }
.text-green-800 { color: #166534; }
.bg-blue-100 { background-color: #dbeafe; }
.text-blue-800 { color: #1e40af; }
.bg-yellow-100 { background-color: #fef3c7; }
.text-yellow-800 { color: #92400e; }
.bg-red-100 { background-color: #fecaca; }
.text-red-800 { color: #991b1b; }

/* Progress bars */
.bg-green-600 { background-color: #059669; }
.bg-yellow-600 { background-color: #d97706; }
.bg-red-600 { background-color: #dc2626; }

/* Cards hover effects */
.bg-white:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .grid-cols-1.sm\\:grid-cols-2.lg\\:grid-cols-4 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
    }

    canvas {
        max-height: 250px;
    }
}

/* Animation for cards */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.bg-white {
    animation: slideInUp 0.6s ease-out;
}

/* Performance badges */
.performance-excellent {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.performance-good {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
}

.performance-average {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.performance-low {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

/* Chart legends */
.chart-legend {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1rem;
    margin-top: 1rem;
}

.legend-item {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
    color: #6b7280;
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 8px;
}

/* Export buttons */
button:hover {
    transform: translateY(-1px);
}

/* Table enhancements */
tbody tr:hover {
    background-color: #f9fafb;
    transform: scale(1.01);
    transition: all 0.2s ease;
}

/* Loading states */
.loading-spinner {
    width: 20px;
    height: 20px;
    border: 2px solid #e5e7eb;
    border-top: 2px solid #3b82f6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush
@endsection
