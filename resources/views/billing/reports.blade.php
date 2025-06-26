@extends('layouts.app')

@section('title', 'Relatórios de Faturamento')

@section('content')
<div class="py-4 container-fluid">
    <!-- Header -->
    <div class="mb-4 row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-0 text-gray-800 h3">Relatórios de Faturamento</h1>
                    <p class="text-muted">
                        Período: {{ $startDate->format('d/m/Y') }} até {{ $endDate->format('d/m/Y') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('billing.dashboard') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
                    </a>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="mb-4 row">
        <div class="col-12">
            <div class="shadow card">
                <div class="card-body">
                    <form method="GET" action="{{ route('billing.reports') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="period" class="form-label">Período:</label>
                            <select name="period" id="period" class="form-select">
                                <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>
                                    Esta Semana
                                </option>
                                <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>
                                    Este Mês
                                </option>
                                <option value="quarterly" {{ $period === 'quarterly' ? 'selected' : '' }}>
                                    Este Trimestre
                                </option>
                                <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>
                                    Este Ano
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo Executivo -->
    <div class="mb-4 row">
        <div class="col-12">
            <div class="shadow card">
                <div class="text-white card-header bg-primary">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar"></i> Resumo Executivo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <div class="p-3 text-center rounded bg-light">
                                <h3 class="text-success">
                                    {{ number_format($invoiceStats['paid_amount'], 2, ',', '.') }} MT
                                </h3>
                                <p class="mb-0 text-muted">Total Recebido</p>
                            </div>
                        </div>
                        <div class="mb-3 col-md-4">
                            <div class="p-3 text-center rounded bg-light">
                                <h3 class="text-warning">
                                    {{ number_format($invoiceStats['pending_amount'], 2, ',', '.') }} MT
                                </h3>
                                <p class="mb-0 text-muted">Valor Pendente</p>
                            </div>
                        </div>
                        <div class="mb-3 col-md-4">
                            <div class="p-3 text-center rounded bg-light">
                                <h3 class="text-danger">
                                    {{ number_format($invoiceStats['overdue_amount'], 2, ',', '.') }} MT
                                </h3>
                                <p class="mb-0 text-muted">Valor Vencido</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Relatório de Faturas -->
        <div class="mb-4 col-lg-8">
            <div class="shadow card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-invoice"></i> Relatório de Faturas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Métrica</th>
                                    <th class="text-center">Quantidade</th>
                                    <th class="text-right">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Total de Faturas</strong></td>
                                    <td class="text-center">{{ number_format($invoiceStats['total_count']) }}</td>
                                    <td class="text-right">
                                        <strong>{{ number_format($invoiceStats['total_amount'], 2, ',', '.') }} MT</strong>
                                    </td>
                                </tr>
                                <tr class="table-success">
                                    <td>Faturas Pagas</td>
                                    <td class="text-center">{{ number_format($invoiceStats['paid_count']) }}</td>
                                    <td class="text-right">
                                        {{ number_format($invoiceStats['paid_amount'], 2, ',', '.') }} MT
                                    </td>
                                </tr>
                                <tr class="table-warning">
                                    <td>Faturas Pendentes</td>
                                    <td class="text-center">{{ number_format($invoiceStats['pending_count']) }}</td>
                                    <td class="text-right">
                                        {{ number_format($invoiceStats['pending_amount'], 2, ',', '.') }} MT
                                    </td>
                                </tr>
                                <tr class="table-danger">
                                    <td>Faturas Vencidas</td>
                                    <td class="text-center">{{ number_format($invoiceStats['overdue_count']) }}</td>
                                    <td class="text-right">
                                        {{ number_format($invoiceStats['overdue_amount'], 2, ',', '.') }} MT
                                    </td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>Ticket Médio</strong></td>
                                    <td class="text-center">-</td>
                                    <td class="text-right">
                                        <strong>{{ number_format($invoiceStats['average_value'], 2, ',', '.') }} MT</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Gráfico de Pizza - Status das Faturas -->
                    <div class="mt-4">
                        <h6>Distribuição por Status</h6>
                        <canvas id="invoiceStatusChart" style="max-height: 300px;"></canvas>
                    </div>

                    <!-- Faturamento por Mês -->
                    @if($invoiceStats['by_month']->count() > 0)
                        <div class="mt-4">
                            <h6>Faturamento por Mês</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Mês/Ano</th>
                                            <th class="text-center">Quantidade</th>
                                            <th class="text-right">Valor Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoiceStats['by_month'] as $monthData)
                                            <tr>
                                                <td>{{ str_pad($monthData->month, 2, '0', STR_PAD_LEFT) }}/{{ $monthData->year }}</td>
                                                <td class="text-center">{{ number_format($monthData->count) }}</td>
                                                <td class="text-right">{{ number_format($monthData->total, 2, ',', '.') }} MT</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Relatório de Orçamentos -->
        <div class="mb-4 col-lg-4">
            <div class="shadow card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-file-alt"></i> Relatório de Orçamentos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total de Orçamentos:</span>
                            <strong>{{ number_format($quoteStats['total_count']) }}</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Valor Total:</span>
                            <strong>{{ number_format($quoteStats['total_amount'], 2, ',', '.') }} MT</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between text-success">
                            <span>Aceitos:</span>
                            <strong>{{ number_format($quoteStats['accepted_count']) }}</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between text-success">
                            <span>Valor Aceito:</span>
                            <strong>{{ number_format($quoteStats['accepted_amount'], 2, ',', '.') }} MT</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between text-primary">
                            <span>Convertidos:</span>
                            <strong>{{ number_format($quoteStats['converted_count']) }}</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Ticket Médio:</span>
                            <strong>{{ number_format($quoteStats['average_value'], 2, ',', '.') }} MT</strong>
                        </div>
                    </div>

                    <hr>

                    <div class="text-center">
                        <div class="mb-2">
                            <span class="text-muted">Taxa de Conversão</span>
                        </div>
                        <div class="mb-2 progress" style="height: 20px;">
                            <div class="progress-bar bg-success"
                                 style="width: {{ $quoteStats['conversion_rate'] }}%"
                                 role="progressbar">
                                {{ $quoteStats['conversion_rate'] }}%
                            </div>
                        </div>
                        <small class="text-muted">
                            {{ $quoteStats['converted_count'] }} de {{ $quoteStats['total_count'] }} orçamentos convertidos
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Relatório de Clientes -->
    <div class="row">
        <div class="mb-4 col-lg-8">
            <div class="shadow card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-users"></i> Top 10 Clientes por Faturamento
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th class="text-right">Valor Faturado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clientStats['top_clients'] as $index => $client)
                                    <tr>
                                        <td>
                                            @if($index === 0)
                                                <i class="fas fa-crown text-warning"></i>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="text-white avatar bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    {{ strtoupper(substr($client->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold">{{ $client->name }}</div>
                                                    <small class="text-muted">{{ $client->email ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <strong class="text-success">
                                                {{ number_format($client->invoices_sum_total ?? 0, 2, ',', '.') }} MT
                                            </strong>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-4 text-center text-muted">
                                            Nenhum cliente com faturamento no período
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas de Clientes -->
        <div class="mb-4 col-lg-4">
            <div class="shadow card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-chart-pie"></i> Estatísticas de Clientes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span>Clientes Ativos</span>
                            <span class="badge badge-primary badge-pill">
                                {{ number_format($clientStats['total_active_clients']) }}
                            </span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span>Novos Clientes</span>
                            <span class="badge badge-success badge-pill">
                                {{ number_format($clientStats['new_clients']) }}
                            </span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success"
                                 style="width: {{ $clientStats['total_active_clients'] > 0 ? ($clientStats['new_clients'] / $clientStats['total_active_clients']) * 100 : 0 }}%">
                            </div>
                        </div>
                        <small class="text-muted">
                            {{ $clientStats['total_active_clients'] > 0 ? round(($clientStats['new_clients'] / $clientStats['total_active_clients']) * 100, 1) : 0 }}%
                            do total de clientes ativos
                        </small>
                    </div>

                    <div class="mb-4">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span>Com Faturas Vencidas</span>
                            <span class="badge badge-danger badge-pill">
                                {{ number_format($clientStats['clients_with_overdue']) }}
                            </span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger"
                                 style="width: {{ $clientStats['total_active_clients'] > 0 ? ($clientStats['clients_with_overdue'] / $clientStats['total_active_clients']) * 100 : 0 }}%">
                            </div>
                        </div>
                        <small class="text-muted">
                            {{ $clientStats['total_active_clients'] > 0 ? round(($clientStats['clients_with_overdue'] / $clientStats['total_active_clients']) * 100, 1) : 0 }}%
                            dos clientes ativos
                        </small>
                    </div>

                    <hr>

                    <div class="text-center">
                        <h5 class="text-primary">{{ number_format($clientStats['total_active_clients']) }}</h5>
                        <p class="mb-0 text-muted">Total de Clientes Ativos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Insights e Recomendações -->
    <div class="row">
        <div class="col-12">
            <div class="shadow card">
                <div class="text-white card-header bg-info">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb"></i> Insights e Recomendações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success">
                                <i class="fas fa-thumbs-up"></i> Pontos Positivos
                            </h6>
                            <ul class="list-unstyled">
                                @if($invoiceStats['paid_count'] > 0)
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        {{ $invoiceStats['paid_count'] }} faturas foram pagas no período
                                    </li>
                                @endif
                                @if($quoteStats['conversion_rate'] > 50)
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Boa taxa de conversão de orçamentos: {{ $quoteStats['conversion_rate'] }}%
                                    </li>
                                @endif
                                @if($clientStats['new_clients'] > 0)
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        {{ $clientStats['new_clients'] }} novos clientes adquiridos
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-warning">
                                <i class="fas fa-exclamation-triangle"></i> Pontos de Atenção
                            </h6>
                            <ul class="list-unstyled">
                                @if($invoiceStats['overdue_count'] > 0)
                                    <li class="mb-2">
                                        <i class="fas fa-exclamation text-warning me-2"></i>
                                        {{ $invoiceStats['overdue_count'] }} faturas estão vencidas
                                    </li>
                                @endif
                                @if($quoteStats['conversion_rate'] < 30)
                                    <li class="mb-2">
                                        <i class="fas fa-exclamation text-warning me-2"></i>
                                        Taxa de conversão baixa: {{ $quoteStats['conversion_rate'] }}%
                                    </li>
                                @endif
                                @if($clientStats['clients_with_overdue'] > 0)
                                    <li class="mb-2">
                                        <i class="fas fa-exclamation text-warning me-2"></i>
                                        {{ $clientStats['clients_with_overdue'] }} clientes com faturas vencidas
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary">
                                <i class="fas fa-rocket"></i> Recomendações
                            </h6>
                            <div class="row">
                                @if($invoiceStats['overdue_count'] > 0)
                                    <div class="mb-3 col-md-4">
                                        <div class="card border-danger">
                                            <div class="card-body">
                                                <h6 class="card-title text-danger">Cobranças</h6>
                                                <p class="card-text small">
                                                    Implemente um sistema de lembrete automático para faturas vencidas
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if($quoteStats['conversion_rate'] < 50)
                                    <div class="mb-3 col-md-4">
                                        <div class="card border-warning">
                                            <div class="card-body">
                                                <h6 class="card-title text-warning">Orçamentos</h6>
                                                <p class="card-text small">
                                                    Revise sua estratégia de orçamentos para melhorar a conversão
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="mb-3 col-md-4">
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <h6 class="card-title text-success">Crescimento</h6>
                                            <p class="card-text small">
                                                Foque nos top clientes para aumentar o ticket médio
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Pizza - Status das Faturas
    const ctx = document.getElementById('invoiceStatusChart').getContext('2d');

    const invoiceStatusData = {
        labels: ['Pagas', 'Pendentes', 'Vencidas'],
        datasets: [{
            data: [
                {{ $invoiceStats['paid_count'] }},
                {{ $invoiceStats['pending_count'] }},
                {{ $invoiceStats['overdue_count'] }}
            ],
            backgroundColor: [
                '#28a745',
                '#17a2b8',
                '#dc3545'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    };

    new Chart(ctx, {
        type: 'doughnut',
        data: invoiceStatusData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
@media print {
    .btn, .card-header .btn {
        display: none !important;
    }

    .card {
        border: 1px solid #dee2e6 !important;
        page-break-inside: avoid;
    }

    .card-header {
        background-color: #f8f9fa !important;
        color: #495057 !important;
    }

    .bg-primary {
        background-color: #007bff !important;
        color: white !important;
    }

    .bg-info {
        background-color: #17a2b8 !important;
        color: white !important;
    }

    .text-primary {
        color: #007bff !important;
    }

    .text-success {
        color: #28a745 !important;
    }

    .text-warning {
        color: #ffc107 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }
}

.avatar {
    font-size: 14px;
    font-weight: bold;
}

.progress {
    background-color: #e9ecef;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.badge-pill {
    font-size: 0.875em;
}
</style>
@endpush
@endsection