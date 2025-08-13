@extends('layouts.admin')

@section('title', 'Monitoramento de Performance')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
     <div class="mx-5 bg-white rounded-md shadow">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Monitoramento de Performance</h1>
                    <p class="mt-1 text-sm text-gray-600">Acompanhe métricas de performance e uso do sistema em tempo real</p>
                </div>
                <div class="flex space-x-3">
                    <div class="flex items-center space-x-2">
                        <label class="text-sm text-gray-600">Auto-refresh:</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="autoRefresh" class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    <select id="timeframe" class="px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1h">Última hora</option>
                        <option value="24h" selected>Últimas 24h</option>
                        <option value="7d">Últimos 7 dias</option>
                        <option value="30d">Últimos 30 dias</option>
                    </select>
                    <button onclick="refreshMetrics()"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Atualizar Métricas
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-8">
        <!-- Métricas Principais -->
        <div class="grid grid-cols-1 gap-5 mb-8 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Tempo de Resposta -->
            <div class="overflow-hidden transition-shadow bg-white rounded-lg shadow hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Tempo de Resposta</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900" id="responseTime">{{ $metrics['response_time']['current'] }}ms</div>
                                    <div class="ml-2 flex items-baseline text-sm font-semibold {{ $metrics['response_time']['status'] == 'good' ? 'text-green-600' : 'text-red-600' }}">
                                        <svg class="self-center flex-shrink-0 h-5 w-5 {{ $metrics['response_time']['status'] == 'good' ? 'text-green-500' : 'text-red-500' }}" fill="currentColor" viewBox="0 0 20 20">
                                            @if($metrics['response_time']['status'] == 'good')
                                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                            @else
                                                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            @endif
                                        </svg>
                                    </div>
                                </dd>
                                <dd class="mt-1 text-xs text-gray-500">Média 24h: {{ $metrics['response_time']['average_24h'] }}ms</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="h-2 bg-gray-200 rounded-full">
                            <div class="h-2 transition-all duration-500 bg-blue-600 rounded-full" style="width: 75%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Uso de Memória -->
            <div class="overflow-hidden transition-shadow bg-white rounded-lg shadow hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Uso de Memória</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900" id="memoryUsage">{{ $metrics['memory_usage']['current'] }}</div>
                                    <div class="flex items-baseline ml-2 text-sm font-semibold text-gray-600">
                                        / {{ $metrics['memory_usage']['limit'] }}
                                    </div>
                                </dd>
                                <dd class="mt-1 text-xs text-gray-500">Pico 24h: {{ $metrics['memory_usage']['peak_24h'] }}</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="h-2 bg-gray-200 rounded-full">
                            <div class="h-2 transition-all duration-500 bg-green-600 rounded-full" style="width: 65%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Uso de CPU -->
            <div class="overflow-hidden transition-shadow bg-white rounded-lg shadow hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Uso de CPU</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900" id="cpuUsage">{{ $metrics['cpu_usage']['current'] }}%</div>
                                </dd>
                                <dd class="mt-1 text-xs text-gray-500">Média 24h: {{ $metrics['cpu_usage']['average_24h'] }}%</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="h-2 bg-gray-200 rounded-full">
                            <div class="h-2 transition-all duration-500 bg-yellow-600 rounded-full" style="width: {{ $metrics['cpu_usage']['current'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Uso de Disco -->
            <div class="overflow-hidden transition-shadow bg-white rounded-lg shadow hover:shadow-md">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Uso de Disco</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900" id="diskUsage">{{ $metrics['disk_usage']['percentage'] }}%</div>
                                </dd>
                                <dd class="mt-1 text-xs text-gray-500">{{ $metrics['disk_usage']['used'] }} / {{ $metrics['disk_usage']['total'] }}</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="h-2 bg-gray-200 rounded-full">
                            <div class="h-2 transition-all duration-500 bg-purple-600 rounded-full" style="width: {{ $metrics['disk_usage']['percentage'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-2">
            <!-- Gráfico de Tempo de Resposta -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="flex items-center text-lg font-medium text-gray-900">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Tempo de Resposta (24h)
                    </h3>
                </div>
                <div class="p-6">
                    <div class="relative h-80">
                        <canvas id="responseTimeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Recursos do Sistema -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="flex items-center text-lg font-medium text-gray-900">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Recursos do Sistema
                    </h3>
                </div>
                <div class="p-6">
                    <div class="relative h-80">
                        <canvas id="resourcesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações Detalhadas -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Informações do Sistema -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="flex items-center text-lg font-medium text-gray-900">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Sistema
                    </h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">PHP</dt>
                            <dd class="font-medium text-gray-900">{{ $systemInfo['php_version'] }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Laravel</dt>
                            <dd class="font-medium text-gray-900">{{ $systemInfo['laravel_version'] }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Servidor</dt>
                            <dd class="font-medium text-gray-900">{{ $systemInfo['server'] }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">OS</dt>
                            <dd class="font-medium text-gray-900">{{ $systemInfo['os'] }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Uptime</dt>
                            <dd class="font-medium text-gray-900">{{ $systemInfo['uptime'] }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Environment</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $systemInfo['environment'] === 'production' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($systemInfo['environment']) }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Base de Dados -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="flex items-center text-lg font-medium text-gray-900">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                        </svg>
                        Base de Dados
                    </h3>
                </div>
                <div class="px-6 py-4">
                    @if($databaseStats['status'] === 'connected')
                        <dl class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-500">Driver</dt>
                                <dd class="font-medium text-gray-900">{{ ucfirst($databaseStats['driver']) }}</dd>
                            </div>
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-500">Base</dt>
                                <dd class="font-medium text-gray-900">{{ $databaseStats['database_name'] }}</dd>
                            </div>
                            @isset($databaseStats['table_count'])
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-500">Tabelas</dt>
                                <dd class="font-medium text-gray-900">{{ $databaseStats['table_count'] }}</dd>
                            </div>
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-500">Tamanho</dt>
                                <dd class="font-medium text-gray-900">{{ $databaseStats['total_size'] }}</dd>
                            </div>
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-500">Conexões</dt>
                                <dd class="font-medium text-gray-900">{{ $databaseStats['active_connections'] }}/{{ $databaseStats['max_connections'] }}</dd>
                            </div>
                            @endisset
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-500">Status</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 mr-1 bg-green-400 rounded-full"></span>
                                        Conectado
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    @else
                        <div class="py-4 text-center">
                            <svg class="w-8 h-8 mx-auto mb-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-2.734-.833-3.464 0L2.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <p class="text-sm text-red-600">Erro na conexão</p>
                            <p class="text-xs text-gray-500">{{ $databaseStats['message'] ?? 'Erro desconhecido' }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cache e Performance -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="flex items-center text-lg font-medium text-gray-900">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Cache & Performance
                    </h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Hit Rate</dt>
                            <dd class="font-medium text-gray-900">{{ $metrics['cache']['hit_rate'] }}%</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Tamanho</dt>
                            <dd class="font-medium text-gray-900">{{ $metrics['cache']['size'] }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Chaves</dt>
                            <dd class="font-medium text-gray-900">{{ number_format($metrics['cache']['keys']) }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Load Average</dt>
                            <dd class="font-medium text-gray-900">{{ $systemInfo['load_average'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Auto-refresh functionality
let autoRefreshInterval;
const autoRefreshCheckbox = document.getElementById('autoRefresh');

autoRefreshCheckbox.addEventListener('change', function() {
    if (this.checked) {
        autoRefreshInterval = setInterval(refreshMetrics, 30000); // 30 seconds
    } else {
        clearInterval(autoRefreshInterval);
    }
});

// Refresh metrics
function refreshMetrics() {
    const timeframe = document.getElementById('timeframe').value;
    const button = event?.target || document.querySelector('button[onclick="refreshMetrics()"]');
    const originalText = button.innerHTML;

    button.innerHTML = '<div class="inline-block w-4 h-4 mr-2 border-2 border-current rounded-full animate-spin border-t-transparent"></div>Carregando...';
    button.disabled = true;

    fetch(`{{ route('admin.monitoring.metrics') }}?timeframe=${timeframe}`)
        .then(response => response.json())
        .then(data => {
            updateMetrics(data);
            updateCharts(data);
            showNotification('Métricas atualizadas com sucesso!', 'success');
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao atualizar métricas', 'error');
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
}

// Update metrics display
function updateMetrics(data) {
    // Update response time
    if (data.response_times && data.response_times.length > 0) {
        const latest = data.response_times[data.response_times.length - 1];
        document.getElementById('responseTime').textContent = latest + 'ms';
    }

    // Update CPU usage
    if (data.cpu_usage && data.cpu_usage.length > 0) {
        const latest = data.cpu_usage[data.cpu_usage.length - 1];
        document.getElementById('cpuUsage').textContent = latest + '%';
    }

    // Update memory usage
    if (data.memory_usage && data.memory_usage.length > 0) {
        const latest = data.memory_usage[data.memory_usage.length - 1];
        document.getElementById('memoryUsage').textContent = latest + 'MB';
    }

    // Update active users
    if (data.active_users && data.active_users.length > 0) {
        const latest = data.active_users[data.active_users.length - 1];
        if (document.getElementById('activeUsers')) {
            document.getElementById('activeUsers').textContent = latest;
        }
    }
}

// Initialize charts
let responseTimeChart;
let resourcesChart;

function initCharts() {
    // Response Time Chart
    const responseTimeCtx = document.getElementById('responseTimeChart').getContext('2d');
    responseTimeChart = new Chart(responseTimeCtx, {
        type: 'line',
        data: {
            labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
            datasets: [{
                label: 'Tempo de Resposta (ms)',
                data: [120, 135, 125, 140, 130, 125],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + 'ms';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Resources Chart
    const resourcesCtx = document.getElementById('resourcesChart').getContext('2d');
    resourcesChart = new Chart(resourcesCtx, {
        type: 'doughnut',
        data: {
            labels: ['CPU', 'Memória', 'Disco'],
            datasets: [{
                data: [{{ $metrics['cpu_usage']['current'] }}, 65, {{ $metrics['disk_usage']['percentage'] }}],
                backgroundColor: [
                    '#fbbf24',
                    '#10b981',
                    '#8b5cf6'
                ],
                borderWidth: 0
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
}

// Update charts with new data
function updateCharts(data) {
    // Update response time chart
    if (data.response_times && responseTimeChart) {
        const labels = data.response_times.map((_, index) => {
            const date = new Date();
            date.setHours(date.getHours() - (data.response_times.length - 1 - index));
            return date.getHours().toString().padStart(2, '0') + ':00';
        });

        responseTimeChart.data.labels = labels;
        responseTimeChart.data.datasets[0].data = data.response_times;
        responseTimeChart.update();
    }

    // Update resources chart
    if (resourcesChart) {
        const cpuUsage = data.cpu_usage ? data.cpu_usage[data.cpu_usage.length - 1] : {{ $metrics['cpu_usage']['current'] }};
        const memoryUsage = data.memory_usage ? data.memory_usage[data.memory_usage.length - 1] : 65;
        const diskUsage = {{ $metrics['disk_usage']['percentage'] }};

        resourcesChart.data.datasets[0].data = [cpuUsage, memoryUsage, diskUsage];
        resourcesChart.update();
    }
}

// Show notification
function showNotification(message, type) {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'
    }`;

    const icon = type === 'success'
        ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
        : '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>';

    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0 mr-3">
                ${icon}
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-3">
                <button class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none" onclick="this.closest('.notification').remove()">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 3000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initCharts();

    // Timeframe change handler
    document.getElementById('timeframe').addEventListener('change', function() {
        refreshMetrics();
    });
});
</script>
@endpush

@endsection
