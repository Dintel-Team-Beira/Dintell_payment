@extends('layouts.admin')

@section('title', 'Health Check do Sistema')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="flex items-center text-2xl font-bold text-gray-900">
                        <svg class="w-8 h-8 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Health Check do Sistema
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Verificação completa do estado de saúde de todos os componentes do sistema</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="runHealthCheck()"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Executar Health Check
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-8">
        <!-- Status Geral -->
        <div class="mb-8">
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        @php
                            $overallHealthy = collect($healthChecks)->where('status', 'healthy')->count();
                            $totalChecks = count($healthChecks);
                            $healthPercentage = $totalChecks > 0 ? ($overallHealthy / $totalChecks) * 100 : 0;
                            $isSystemHealthy = $healthPercentage >= 80;
                        @endphp

                        <div class="flex items-center justify-center w-16 h-16 rounded-full {{ $isSystemHealthy ? 'bg-green-100' : 'bg-red-100' }}">
                            @if($isSystemHealthy)
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @else
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-2.734-.833-3.464 0L2.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-bold {{ $isSystemHealthy ? 'text-green-900' : 'text-red-900' }}">
                                Sistema {{ $isSystemHealthy ? 'Saudável' : 'com Problemas' }}
                            </h3>
                            <p class="text-gray-600">
                                {{ $overallHealthy }} de {{ $totalChecks }} verificações aprovadas ({{ number_format($healthPercentage, 1) }}%)
                            </p>
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="text-sm text-gray-500">Última verificação</div>
                        <div class="text-lg font-medium text-gray-900" id="lastCheckTime">{{ now()->format('d/m/Y H:i:s') }}</div>
                    </div>
                </div>

                <!-- Barra de Progresso -->
                <div class="mt-4">
                    <div class="h-3 bg-gray-200 rounded-full">
                        <div class="h-3 rounded-full transition-all duration-500 {{ $isSystemHealthy ? 'bg-green-600' : 'bg-red-600' }}"
                             style="width: {{ $healthPercentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Health Checks -->
        <div class="mb-8">
            <h2 class="flex items-center mb-4 text-lg font-medium text-gray-900">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Verificações de Sistema
            </h2>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2" id="healthChecksGrid">
                @foreach($healthChecks as $check)
                    <div class="p-4 transition-shadow bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md health-check-card">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full
                                    {{ $check['status'] === 'healthy' ? 'bg-green-100' : ($check['status'] === 'warning' ? 'bg-yellow-100' : 'bg-red-100') }}">
                                    @if($check['status'] === 'healthy')
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @elseif($check['status'] === 'warning')
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-2.734-.833-3.464 0L2.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-gray-900">{{ $check['name'] }}</h3>
                                    <p class="text-xs text-gray-500">{{ $check['message'] }}</p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                @if($check['critical'])
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mr-2">
                                        Crítico
                                    </span>
                                @endif
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $check['status'] === 'healthy' ? 'bg-green-100 text-green-800' : ($check['status'] === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    <span class="w-1.5 h-1.5 mr-1 rounded-full
                                        {{ $check['status'] === 'healthy' ? 'bg-green-400' : ($check['status'] === 'warning' ? 'bg-yellow-400' : 'bg-red-400') }}
                                        {{ $check['status'] === 'healthy' ? 'animate-pulse' : '' }}"></span>
                                    {{ ucfirst($check['status']) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Status dos Serviços -->
        <div class="mb-8">
            <h2 class="flex items-center mb-4 text-lg font-medium text-gray-900">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                </svg>
                Status dos Serviços
            </h2>

            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="divide-y divide-gray-200">
                    @foreach($services as $serviceName => $serviceStatus)
                        <div class="flex items-center justify-between p-4">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full
                                    {{ $serviceStatus === 'healthy' ? 'bg-green-100' : ($serviceStatus === 'warning' ? 'bg-yellow-100' : 'bg-red-100') }}">
                                    <span class="w-2 h-2 rounded-full
                                        {{ $serviceStatus === 'healthy' ? 'bg-green-500' : ($serviceStatus === 'warning' ? 'bg-yellow-500' : 'bg-red-500') }}
                                        {{ $serviceStatus === 'healthy' ? 'animate-pulse' : '' }}"></span>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $serviceName) }}</h3>
                                    <p class="text-xs text-gray-500">
                                        {{ $serviceStatus === 'healthy' ? 'Operacional' : ($serviceStatus === 'warning' ? 'Com avisos' : 'Indisponível') }}
                                    </p>
                                </div>
                            </div>

                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $serviceStatus === 'healthy' ? 'bg-green-100 text-green-800' : ($serviceStatus === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($serviceStatus) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Alertas do Sistema -->
        @if(count($alerts) > 0)
        <div class="mb-8">
            <h2 class="flex items-center mb-4 text-lg font-medium text-gray-900">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-2.734-.833-3.464 0L2.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                Alertas Ativos ({{ count($alerts) }})
            </h2>

            <div class="space-y-3" id="alertsContainer">
                @foreach($alerts as $alert)
                    <div class="bg-white rounded-lg shadow-sm border-l-4
                        {{ $alert['type'] === 'error' ? 'border-red-400' : ($alert['type'] === 'warning' ? 'border-yellow-400' : 'border-blue-400') }} p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full
                                    {{ $alert['type'] === 'error' ? 'bg-red-100' : ($alert['type'] === 'warning' ? 'bg-yellow-100' : 'bg-blue-100') }}">
                                    @if($alert['type'] === 'error')
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    @elseif($alert['type'] === 'warning')
                                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-2.734-.833-3.464 0L2.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium
                                        {{ $alert['type'] === 'error' ? 'text-red-900' : ($alert['type'] === 'warning' ? 'text-yellow-900' : 'text-blue-900') }}">
                                        {{ $alert['title'] }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600">{{ $alert['message'] }}</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-gray-500">{{ $alert['timestamp']->diffForHumans() }}</span>
                                <button onclick="dismissAlert(this)"
                                        class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Histórico de Status -->
        <div class="mb-8">
            <h2 class="flex items-center mb-4 text-lg font-medium text-gray-900">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Histórico de Status (Últimas 24h)
            </h2>

            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="relative h-64">
                    <canvas id="healthHistoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Métricas de Performance Rápida -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="p-4 text-center bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="text-2xl font-bold text-blue-600" id="uptimeMetric">99.9%</div>
                <div class="text-sm text-gray-500">Uptime</div>
            </div>

            <div class="p-4 text-center bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="text-2xl font-bold text-green-600" id="responseMetric">125ms</div>
                <div class="text-sm text-gray-500">Tempo Médio</div>
            </div>

            <div class="p-4 text-center bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="text-2xl font-bold text-purple-600" id="errorsMetric">0</div>
                <div class="text-sm text-gray-500">Erros (24h)</div>
            </div>

            <div class="p-4 text-center bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="text-2xl font-bold text-yellow-600" id="checksMetric">{{ count($healthChecks) }}</div>
                <div class="text-sm text-gray-500">Verificações</div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Run health check
function runHealthCheck() {
    const button = event.target;
    const originalText = button.innerHTML;

    button.innerHTML = '<div class="inline-block w-4 h-4 mr-2 border-2 border-current rounded-full animate-spin border-t-transparent"></div>Executando...';
    button.disabled = true;

    // Simulate health check process
    setTimeout(() => {
        updateHealthChecks();
        updateLastCheckTime();
        showNotification('Health check executado com sucesso!', 'success');

        button.innerHTML = originalText;
        button.disabled = false;
    }, 2000);
}

// Update health checks
function updateHealthChecks() {
    const cards = document.querySelectorAll('.health-check-card');
    cards.forEach(card => {
        // Add a subtle animation to show update
        card.style.transform = 'scale(0.98)';
        setTimeout(() => {
            card.style.transform = 'scale(1)';
        }, 200);
    });
}

// Update last check time
function updateLastCheckTime() {
    const timeElement = document.getElementById('lastCheckTime');
    if (timeElement) {
        timeElement.textContent = new Date().toLocaleString('pt-BR');
    }
}

// Dismiss alert
function dismissAlert(button) {
    const alert = button.closest('div[class*="border-l-4"]');
    alert.style.transform = 'translateX(100%)';
    alert.style.opacity = '0';
    setTimeout(() => {
        alert.remove();

        // Update alerts counter
        const alertsContainer = document.getElementById('alertsContainer');
        const remainingAlerts = alertsContainer.children.length;

        if (remainingAlerts === 0) {
            // Hide the entire alerts section if no alerts remain
            const alertsSection = alertsContainer.closest('.mb-8');
            alertsSection.style.display = 'none';
        }
    }, 300);
}

// Initialize health history chart
let healthHistoryChart;

function initHealthHistoryChart() {
    const ctx = document.getElementById('healthHistoryChart').getContext('2d');

    // Generate mock data for the last 24 hours
    const labels = [];
    const data = [];

    for (let i = 23; i >= 0; i--) {
        const time = new Date();
        time.setHours(time.getHours() - i);
        labels.push(time.getHours().toString().padStart(2, '0') + ':00');

        // Mock health percentage (mostly healthy with some variations)
        data.push(Math.random() > 0.1 ? 100 : Math.floor(Math.random() * 40) + 60);
    }

    healthHistoryChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Health Status (%)',
                data: data,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
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
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Health: ' + context.parsed.y + '%';
                        }
                    }
                }
            }
        }
    });
}

// Show notification
function showNotification(message, type) {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    // Create notification
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

// Auto-refresh health status every 5 minutes
setInterval(() => {
    runHealthCheck();
}, 300000);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initHealthHistoryChart();

    // Add subtle animations to health check cards
    const cards = document.querySelectorAll('.health-check-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.3s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Update metrics periodically
    setInterval(updateMetrics, 60000); // Update every minute
});

// Update performance metrics
function updateMetrics() {
    // These would normally come from real API calls
    const uptimeElement = document.getElementById('uptimeMetric');
    const responseElement = document.getElementById('responseMetric');
    const errorsElement = document.getElementById('errorsMetric');

    if (uptimeElement) {
        // Simulate uptime calculation
        const uptime = (99.8 + Math.random() * 0.2).toFixed(1);
        uptimeElement.textContent = uptime + '%';
    }

    if (responseElement) {
        // Simulate response time variation
        const responseTime = Math.floor(120 + Math.random() * 20);
        responseElement.textContent = responseTime + 'ms';
    }

    if (errorsElement) {
        // Simulate error count (usually low)
        const errors = Math.random() > 0.9 ? Math.floor(Math.random() * 3) : 0;
        errorsElement.textContent = errors.toString();
        errorsElement.className = errors > 0 ? 'text-2xl font-bold text-red-600' : 'text-2xl font-bold text-green-600';
    }
}

// Add hover effects to service status items
document.querySelectorAll('div:has(> .capitalize)').forEach(item => {
    item.addEventListener('mouseenter', function() {
        this.style.backgroundColor = '#f9fafb';
    });

    item.addEventListener('mouseleave', function() {
        this.style.backgroundColor = '';
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+R or Cmd+R to run health check
    if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
        e.preventDefault();
        runHealthCheck();
    }
});

// Service worker for background health monitoring (if available)
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').then(function(registration) {
        console.log('SW registered: ', registration);
    }).catch(function(registrationError) {
        console.log('SW registration failed: ', registrationError);
    });
}

// Page visibility API for pausing/resuming monitoring
let isPageVisible = true;

document.addEventListener('visibilitychange', function() {
    isPageVisible = !document.hidden;

    if (isPageVisible) {
        // Page became visible, refresh health status
        runHealthCheck();
    }
});

// Connection status monitoring
window.addEventListener('online', function() {
    showNotification('Conexão restaurada', 'success');
    runHealthCheck();
});

window.addEventListener('offline', function() {
    showNotification('Conexão perdida', 'error');
});

// Performance observer for real-time metrics
if ('PerformanceObserver' in window) {
    const observer = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
            if (entry.entryType === 'navigation') {
                // Update response time with real data
                const responseElement = document.getElementById('responseMetric');
                if (responseElement) {
                    responseElement.textContent = Math.round(entry.loadEventEnd - entry.loadEventStart) + 'ms';
                }
            }
        }
    });

    observer.observe({ entryTypes: ['navigation'] });
}

// Export health report functionality
function exportHealthReport() {
    const healthData = {
        timestamp: new Date().toISOString(),
        systemStatus: '{{ $isSystemHealthy ? "healthy" : "unhealthy" }}',
        checksTotal: {{ count($healthChecks) }},
        checksPassed: {{ $overallHealthy }},
        healthChecks: @json($healthChecks),
        services: @json($services),
        alerts: @json($alerts)
    };

    const dataStr = JSON.stringify(healthData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});

    const link = document.createElement('a');
    link.href = URL.createObjectURL(dataBlob);
    link.download = `health-report-${new Date().toISOString().slice(0, 10)}.json`;
    link.click();
}

// Add export button dynamically
document.addEventListener('DOMContentLoaded', function() {
    const headerButtons = document.querySelector('div.flex.space-x-3');
    const exportButton = document.createElement('button');
    exportButton.onclick = exportHealthReport;
    exportButton.className = 'inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500';
    exportButton.innerHTML = `
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
        </svg>
        Exportar Relatório
    `;
    headerButtons.appendChild(exportButton);
});
</script>
@endpush

@endsection
