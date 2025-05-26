@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Visão geral do sistema de subscrições')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Clients -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total de Clientes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_clients']) }}</p>
                    <p class="text-xs text-green-600">+{{ $stats['active_clients'] }} ativos</p>
                </div>
            </div>
        </div>

        <!-- Active Subscriptions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Subscrições Ativas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_subscriptions']) }}</p>
                    <p class="text-xs text-gray-500">de {{ $stats['total_subscriptions'] }} total</p>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Receita Mensal</p>
                    <p class="text-2xl font-bold text-gray-900">MT {{ number_format($stats['monthly_revenue'], 2) }}</p>
                    <p class="text-xs text-green-600">Total: MT {{ number_format($stats['total_revenue'], 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Expiring Soon -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Expirando em Breve</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['expiring_soon'] }}</p>
                    <p class="text-xs text-red-600">{{ $stats['expired_subscriptions'] }} já expiradas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['active_subscriptions'] }}</div>
            <div class="text-sm text-gray-600">Ativas</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['trial_subscriptions'] }}</div>
            <div class="text-sm text-gray-600">Em Trial</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['suspended_subscriptions'] }}</div>
            <div class="text-sm text-gray-600">Suspensas</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ $stats['expired_subscriptions'] }}</div>
            <div class="text-sm text-gray-600">Expiradas</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Receita dos Últimos {{ $period }} Dias</h3>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Subscriptions Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Novas Subscrições</h3>
            <div class="h-64">
                <canvas id="subscriptionsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ações Rápidas</h3>
            <div class="space-y-3">
                <a href="{{ route('subscriptions.create') }}"
                   class="flex items-center p-3 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nova Subscrição
                </a>

                <a href="{{ route('clients.create') }}"
                   class="flex items-center p-3 text-sm font-medium text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                    Novo Cliente
                </a>

                <a href="{{ route('plans.create') }}"
                   class="flex items-center p-3 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Novo Plano
                </a>

                <a href="{{ route('api-logs.index') }}"
                   class="flex items-center p-3 text-sm font-medium text-purple-600 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Ver Logs da API
                </a>
            </div>
        </div>

        <!-- Top Clients -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Top Clientes</h3>
                <a href="{{ route('clients.index') }}" class="text-sm text-blue-600 hover:text-blue-700">Ver todos</a>
            </div>

            <div class="space-y-4">
                @forelse($topClients as $client)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-medium text-sm">
                            {{ substr($client->name, 0, 2) }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $client->name }}</p>
                            <p class="text-xs text-gray-500">{{ $client->subscriptions_count }} subscrições</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">MT {{ number_format($client->revenue, 2) }}</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Nenhum cliente encontrado</p>
                @endforelse
            </div>
        </div>

        <!-- Expiring Subscriptions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Expirando em Breve</h3>
                <a href="{{ route('subscriptions.index', ['status' => 'expiring']) }}" class="text-sm text-blue-600 hover:text-blue-700">Ver todas</a>
            </div>

            <div class="space-y-4">
                @forelse($expiring as $subscription)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $subscription->domain }}</p>
                        <p class="text-xs text-gray-500">{{ $subscription->client->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-red-600">{{ $subscription->days_until_expiry }}d</p>
                        <p class="text-xs text-gray-500">{{ $subscription->ends_at->format('d/m') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Nenhuma subscrição expirando</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Atividade Recente</h3>

        <div class="space-y-4">
            @forelse($recentActivity as $activity)
            <div class="flex items-start space-x-3">
                <div class="w-2 h-2 rounded-full mt-2
                    {{ $activity['type'] === 'new_subscription' ? 'bg-green-500' :
                       ($activity['type'] === 'payment' ? 'bg-blue-500' : 'bg-red-500') }}">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-900">{{ $activity['message'] }}</p>
                    <p class="text-xs text-gray-500">{{ $activity['date']->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">Nenhuma atividade recente</p>
            @endforelse
        </div>
    </div>

    <!-- API Usage -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Uso da API (Últimos {{ $period }} dias)</h3>
        <div class="h-32">
            <canvas id="apiChart"></canvas>
        </div>
    </div>

    <!-- Plan Distribution -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribuição por Planos</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($planDistribution as $plan)
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">{{ $plan->count }}</div>
                <div class="text-sm text-gray-600">{{ $plan->name }}</div>
                <div class="text-xs text-gray-500">MT {{ number_format($plan->price, 2) }}/mês</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Charts Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($revenueChart->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m'); })) !!},
        datasets: [{
            label: 'Receita (MT)',
            data: {!! json_encode($revenueChart->pluck('revenue')) !!},
            borderColor: 'rgb(147, 51, 234)',
            backgroundColor: 'rgba(147, 51, 234, 0.1)',
            tension: 0.1,
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
                        return 'MT ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Subscriptions Chart
const subsCtx = document.getElementById('subscriptionsChart').getContext('2d');
const subsChart = new Chart(subsCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($subscriptionsChart->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m'); })) !!},
        datasets: [{
            label: 'Novas Subscrições',
            data: {!! json_encode($subscriptionsChart->pluck('count')) !!},
            backgroundColor: 'rgba(59, 130, 246, 0.5)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
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

// API Usage Chart
const apiCtx = document.getElementById('apiChart').getContext('2d');
const apiChart = new Chart(apiCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($apiUsage->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m'); })) !!},
        datasets: [{
            label: 'Requests',
            data: {!! json_encode($apiUsage->pluck('requests')) !!},
            borderColor: 'rgb(16, 185, 129)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Auto refresh dashboard every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);
</script>
@endsection