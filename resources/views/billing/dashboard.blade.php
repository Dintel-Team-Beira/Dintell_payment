@extends('layouts.app')

@section('title', 'Dashboard de Facturação')
@section('subtitle', 'Simplifique a sua Gestão de Facturação')

@section('header-actions')
<div class="flex items-center gap-x-4">
    <!-- Period Filter -->
    <form method="GET" class="flex items-center gap-x-2">
        <select name="period" class="rounded-md border-0 py-1.5 pl-3 pr-8 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm" onchange="this.form.submit()">
            <option value="monthly" {{ request('period') === 'monthly' ? 'selected' : '' }}>Este Mês</option>
            <option value="quarterly" {{ request('period') === 'quarterly' ? 'selected' : '' }}>Este Trimestre</option>
            <option value="yearly" {{ request('period') === 'yearly' ? 'selected' : '' }}>Este Ano</option>
            <option value="custom" {{ request('period') === 'custom' ? 'selected' : '' }}>Personalizado</option>
        </select>

        @if(request('period') === 'custom')
        <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
        <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
        <button type="submit" class="px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-500">
            Aplicar
        </button>
        @endif
    </form>

    <a href="{{ route('quotes.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nova Cotação
    </a>

    <!-- Export Button -->
    <a href="{{ route('billing.export', request()->all()) }}" class="flex items-center px-3 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Exportar
    </a>

    <!-- Quick Action Button -->
    <a href="{{ route('invoices.create') }}" class="flex px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
        </svg>
        Nova Factura
    </a>
</div>
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <!-- Key Performance Indicators -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Receita Total -->
        <div class="relative p-6 overflow-hidden text-white shadow-sm bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl">
            <div class="absolute top-0 right-0 w-24 h-24 -mt-4 -mr-4 bg-white rounded-full bg-opacity-10"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-100">Receita Total</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['total_invoices'], 2) }} MT</p>
                        <div class="flex items-center mt-2">
                            @if(isset($stats['invoices_growth']))
                            <p class="text-xs text-{{ $stats['invoices_growth'] >= 0 ? 'white' : 'white' }}-600">
                                {{ $stats['invoices_growth'] >= 0 ? '+' : '' }}{{ number_format($stats['invoices_growth'], 1) }}% vs mês anterior
                            </p>
                            @endif
                        </div>
                    </div>
                    <div class="p-3 bg-white rounded-lg bg-opacity-20">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Facturas Pendentes -->
        <div class="relative p-6 overflow-hidden text-white shadow-sm bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl">
            <div class="absolute top-0 right-0 w-24 h-24 -mt-4 -mr-4 bg-white rounded-full bg-opacity-10"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-amber-100">Valor Pendente</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['total_pending'], 2) }} MT</p>
                        <p class="mt-2 text-sm text-amber-200">{{ $stats['count_pending'] }} facturas</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg bg-opacity-20">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cotações -->
        <div class="relative p-6 overflow-hidden text-white shadow-sm bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl">
            <div class="absolute top-0 right-0 w-24 h-24 -mt-4 -mr-4 bg-white rounded-full bg-opacity-10"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-100">Cotações Ativas</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['quotes_total_value'], 2) }} MT</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-sm text-purple-200">{{ $stats['quotes_count'] }} cotações</span>
                            <span class="text-sm text-purple-200">{{ $stats['conversion_rate'] }}% conversão</span>
                        </div>
                    </div>
                    <div class="p-3 bg-white rounded-lg bg-opacity-20">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Taxa de Conversão -->
        <div class="relative p-6 overflow-hidden text-white shadow-sm bg-gradient-to-br from-green-500 to-green-600 rounded-xl">
            <div class="absolute top-0 right-0 w-24 h-24 -mt-4 -mr-4 bg-white rounded-full bg-opacity-10"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-100">Recebido Este Mês</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['total_paid_this_month'], 2) }} MT</p>
                        <p class="mt-2 text-sm text-green-200">{{ $stats['paid_count_this_month'] }} facturas pagas</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg bg-opacity-20">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Revenue Chart -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Evolução da Receita</h3>
                <p class="text-sm text-gray-500">Receita dos últimos 6 meses</p>
            </div>
            <div class="p-6">
                <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Distribuição por Status</h3>
                <p class="text-sm text-gray-500">Facturas e cotações por status</p>
            </div>
            <div class="p-6">
                <canvas id="statusChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions Enhanced -->
    {{-- <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Ações Rápidas</h3>
            <p class="text-sm text-gray-500">Acesse rapidamente as principais funcionalidades</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('invoices.create') }}"
    class="flex items-center p-4 transition-all duration-200 border border-blue-200 rounded-lg group hover:border-blue-300 hover:shadow-md">
    <div class="flex-shrink-0">
        <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-lg group-hover:bg-blue-200">
            <svg class="w-5 h-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
        </div>
    </div>
    <div class="ml-4">
        <p class="text-sm font-semibold text-gray-900">Nova Fatura</p>
        <p class="text-xs text-gray-500">Criar fatura para cliente</p>
    </div>
    </a>

    <a href="{{ route('quotes.create') }}" class="flex items-center p-4 transition-all duration-200 border border-purple-200 rounded-lg group hover:border-purple-300 hover:shadow-md">
        <div class="flex-shrink-0">
            <div class="flex items-center justify-center w-10 h-10 bg-purple-100 rounded-lg group-hover:bg-purple-200">
                <svg class="w-5 h-5 text-purple-600" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                </svg>
            </div>
        </div>
        <div class="ml-4">
            <p class="text-sm font-semibold text-gray-900">Nova Cotação</p>
            <p class="text-xs text-gray-500">Criar proposta de preço</p>
        </div>
    </a>

    <a href="{{ route('clients.create') }}" class="flex items-center p-4 transition-all duration-200 border border-green-200 rounded-lg group hover:border-green-300 hover:shadow-md">
        <div class="flex-shrink-0">
            <div class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-lg group-hover:bg-green-200">
                <svg class="w-5 h-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M8 8a3 3 0 100-6 3 3 0 000 6zM12.735 14c.618 0 1.093-.561.872-1.139a6.002 6.002 0 00-11.215 0c-.22.578.254 1.139.872 1.139h9.47z" />
                </svg>
            </div>
        </div>
        <div class="ml-4">
            <p class="text-sm font-semibold text-gray-900">Novo Cliente</p>
            <p class="text-xs text-gray-500">Cadastrar cliente</p>
        </div>
    </a>

    <a href="/billing/reports" class="flex items-center p-4 transition-all duration-200 border border-gray-200 rounded-lg group hover:border-gray-300 hover:shadow-md">
        <div class="flex-shrink-0">
            <div class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-lg group-hover:bg-gray-200">
                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                </svg>
            </div>
        </div>
        <div class="ml-4">
            <p class="text-sm font-semibold text-gray-900">Relatórios</p>
            <p class="text-xs text-gray-500">Ver análises detalhadas</p>
        </div>
    </a>
</div>
</div>
</div> --}}

<!-- Tables Section -->
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <!-- Overdue Invoices -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Facturas Vencidas</h3>
                <p class="text-sm text-gray-500">{{ $stats['count_overdue'] }} facturas pendentes</p>
            </div>
            <a href="{{ route('invoices.index', ['status' => 'overdue']) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                Ver todas
            </a>
        </div>
        <div class="overflow-hidden">
            @if($overdueInvoices->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Cliente</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Valor</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Dias</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($overdueInvoices as $invoice)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8">
                                        <div class="flex items-center justify-center w-8 h-8 bg-red-100 rounded-full">
                                            <span class="text-xs font-medium text-red-600">{{ substr($invoice->client->name, 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $invoice->client->name }}</p>
                                        <p class="text-xs text-gray-500">#{{ $invoice->invoice_number }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                {{ number_format($invoice->total, 2) }} MT
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{-- <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$quote->status] }}"> --}}
                                {{-- {{ $statusLabels[$quote->status] }} --}}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-medium text-gray-900">Nenhuma cotação encontrada</h3>
                <p class="mt-2 text-sm text-gray-500">
                    <a href="{{ route('quotes.create') }}" class="text-purple-600 hover:text-purple-800">Criar primeira cotação</a>
                </p>
            </div>
            @endif
        </div>
    </div>
    <!-- Top Clients -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Top Clientes</h3>
                <p class="text-sm text-gray-500">Clientes com maior volume de negócios</p>
            </div>
            <a href="{{ route('clients.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                Ver todos
            </a>
        </div>
        <div class="p-6">
            @if($topClients->count() > 0)
            <div class="space-y-4">
                @foreach($topClients as $index => $client)
                <div class="flex items-center justify-between p-4 rounded-lg bg-gray-50">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full">
                                <span class="text-sm font-medium text-blue-600">#{{ $index + 1 }}</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-semibold text-gray-900">{{ $client->name }}</p>
                            <p class="text-xs text-gray-500">{{ $client->invoices_count }} facturas</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">{{ number_format($client->total_invoiced, 2) }} MT</p>
                        <p class="text-xs text-gray-500">Total facturado</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-8 text-center">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-gray-100 rounded-full">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 8a3 3 0 100-6 3 3 0 000 6zM12.735 14c.618 0 1.093-.561.872-1.139a6.002 6.002 0 00-11.215 0c-.22.578.254 1.139.872 1.139h9.47z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-medium text-gray-900">Nenhum cliente encontrado</h3>
                <p class="mt-2 text-sm text-gray-500">
                    <a href="{{ route('clients.create') }}" class="text-blue-600 hover:text-blue-800">Cadastrar primeiro cliente</a>
                </p>
            </div>
            @endif
        </div>
    </div>

</div>


</div>

<!-- Charts JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line'
        , data: {
            labels: {
                !!json_encode($chartData['months']) !!
            }
            , datasets: [{
                label: 'Receita'
                , data: {
                    !!json_encode($chartData['revenue']) !!
                }
                , borderColor: 'rgb(59, 130, 246)'
                , backgroundColor: 'rgba(59, 130, 246, 0.1)'
                , tension: 0.4
                , fill: true
            }, {
                label: 'Pendente'
                , data: {
                    !!json_encode($chartData['pending']) !!
                }
                , borderColor: 'rgb(245, 158, 11)'
                , backgroundColor: 'rgba(245, 158, 11, 0.1)'
                , tension: 0.4
                , fill: true
            }]
        }
        , options: {
            responsive: true
            , maintainAspectRatio: false
            , scales: {
                y: {
                    beginAtZero: true
                    , ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' MT';
                        }
                    }
                }
            }
            , plugins: {
                legend: {
                    display: true
                    , position: 'top'
                }
            }
        }
    });

    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut'
        , data: {
            labels: ['Pagas', 'Pendentes', 'Vencidas', 'Cotações Ativas']
            , datasets: [{
                data: [{
                        {
                            $stats['paid_count']
                        }
                    }
                    , {
                        {
                            $stats['sent_count']
                        }
                    }
                    , {
                        {
                            $stats['count_overdue']
                        }
                    }
                    , {
                        {
                            $stats['quotes_count']
                        }
                    }
                ]
                , backgroundColor: [
                    'rgb(34, 197, 94)'
                    , 'rgb(245, 158, 11)'
                    , 'rgb(239, 68, 68)'
                    , 'rgb(147, 51, 234)'
                ]
                , borderWidth: 2
                , borderColor: 'white'
            }]
        }
        , options: {
            responsive: true
            , maintainAspectRatio: false
            , plugins: {
                legend: {
                    display: true
                    , position: 'bottom'
                }
            }
        }
    });

</script>

<script>
    // dashboard-enhancements.js
    // Adicionar este arquivo ao final do template Blade ou como arquivo JS separado

    document.addEventListener('DOMContentLoaded', function() {
        // Auto-refresh do dashboard a cada 5 minutos
        setInterval(refreshDashboard, 300000);

        // Inicializar tooltips e outros componentes
        initializeComponents();

        // Event listeners para filtros
        setupFilters();

        // Configurar notificações
        setupNotifications();
    });

    function refreshDashboard() {
        const currentPeriod = document.querySelector('select[name="period"]').value;
        const startDate = document.querySelector('input[name="start_date"]') ? .value;
        const endDate = document.querySelector('input[name="end_date"]') ? .value;

        fetch(`/api/dashboard-stats?period=${currentPeriod}&start_date=${startDate}&end_date=${endDate}`)
            .then(response => response.json())
            .then(data => updateDashboardStats(data))
            .catch(error => console.error('Erro ao atualizar dashboard:', error));
    }

    function updateDashboardStats(stats) {
        // Atualizar valores nos cards
        document.querySelector('[data-stat="total_revenue"]').textContent =
            new Intl.NumberFormat('pt-MZ', {
                style: 'currency'
                , currency: 'MZN'
            }).format(stats.total_revenue);

        document.querySelector('[data-stat="total_pending"]').textContent =
            new Intl.NumberFormat('pt-MZ', {
                style: 'currency'
                , currency: 'MZN'
            }).format(stats.total_pending);

        document.querySelector('[data-stat="quotes_total_value"]').textContent =
            new Intl.NumberFormat('pt-MZ', {
                style: 'currency'
                , currency: 'MZN'
            }).format(stats.quotes_total_value);

        document.querySelector('[data-stat="total_paid_this_month"]').textContent =
            new Intl.NumberFormat('pt-MZ', {
                style: 'currency'
                , currency: 'MZN'
            }).format(stats.total_paid_this_month);

        // Atualizar indicadores de crescimento
        const growthElement = document.querySelector('[data-growth="revenue"]');
        if (growthElement) {
            growthElement.textContent = `${stats.revenue_growth > 0 ? '+' : ''}${stats.revenue_growth.toFixed(1)}%`;
            growthElement.className = stats.revenue_growth >= 0 ? 'text-green-300' : 'text-red-300';
        }
    }

    function initializeComponents() {
        // Adicionar animações aos cards
        const cards = document.querySelectorAll('.bg-gradient-to-br');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('animate-fade-in-up');
        });

        // Adicionar hover effects
        const actionButtons = document.querySelectorAll('.group');
        actionButtons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.transition = 'transform 0.2s ease';
            });

            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    }

    function setupFilters() {
        const periodSelect = document.querySelector('select[name="period"]');
        const customFields = document.querySelector('.custom-date-fields');

        if (periodSelect) {
            periodSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    if (customFields) {
                        customFields.style.display = 'flex';
                    }
                } else {
                    if (customFields) {
                        customFields.style.display = 'none';
                    }
                    // Auto-submit form for other periods
                    this.form.submit();
                }
            });
        }

        // Real-time search para tabelas
        const searchInputs = document.querySelectorAll('input[type="search"]');
        searchInputs.forEach(input => {
            let debounceTimer;
            input.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    filterTable(this.value, this.closest('.table-container'));
                }, 300);
            });
        });
    }

    function filterTable(searchTerm, container) {
        if (!container) return;

        const rows = container.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const matches = text.includes(searchTerm.toLowerCase());
            row.style.display = matches ? '' : 'none';
        });
    }

    function setupNotifications() {
        // Verificar facturas vencidas e mostrar notificações
        const overdueCount = parseInt(document.querySelector('[data-stat="count_overdue"]') ? .textContent || '0');

        if (overdueCount > 0) {
            showNotification(
                `Você tem ${overdueCount} factura${overdueCount > 1 ? 's' : ''} vencida${overdueCount > 1 ? 's' : ''}`
                , 'warning'
                , 5000
            );
        }

        // Verificar cotações expiradas
        checkExpiredQuotes();
    }

    function checkExpiredQuotes() {
        fetch('/api/expired-quotes')
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    showNotification(
                        `${data.count} cotação${data.count > 1 ? 'ões' : ''} ${data.count > 1 ? 'expiraram' : 'expirou'} recentemente`
                        , 'info'
                        , 4000
                    );
                }
            })
            .catch(error => console.error('Erro ao verificar cotações:', error));
    }

    function showNotification(message, type = 'info', duration = 3000) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transition-all duration-300 ${getNotificationClass(type)}`;
        notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                ${getNotificationIcon(type)}
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="flex-shrink-0 ml-4">
                <button class="inline-flex text-gray-400 hover:text-gray-600" onclick="this.parentElement.parentElement.parentElement.remove()">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    `;

        document.body.appendChild(notification);

        // Auto-remove notification
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, duration);
    }

    function getNotificationClass(type) {
        const classes = {
            'info': 'bg-blue-50 border border-blue-200 text-blue-800'
            , 'success': 'bg-green-50 border border-green-200 text-green-800'
            , 'warning': 'bg-yellow-50 border border-yellow-200 text-yellow-800'
            , 'error': 'bg-red-50 border border-red-200 text-red-800'
        };
        return classes[type] || classes.info;
    }

    function getNotificationIcon(type) {
        const icons = {
            'info': `<svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                 </svg>`
            , 'success': `<svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                       <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>`
            , 'warning': `<svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                       <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>`
            , 'error': `<svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                     <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                  </svg>`
        };
        return icons[type] || icons.info;
    }

    // Função para exportar dados
    function exportDashboard(format = 'excel') {
        const form = document.createElement('form');
        form.method = 'GET';
        form.action = '/billing/export';

        // Adicionar parâmetros atuais
        const currentParams = new URLSearchParams(window.location.search);
        currentParams.append('format', format);

        currentParams.forEach((value, key) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

    // Função para atualizar gráficos dinamicamente
    function updateCharts() {
        fetch('/api/chart-data')
            .then(response => response.json())
            .then(data => {
                // Atualizar gráfico de receita
                if (window.revenueChart) {
                    window.revenueChart.data.labels = data.months;
                    window.revenueChart.data.datasets[0].data = data.revenue;
                    window.revenueChart.data.datasets[1].data = data.pending;
                    window.revenueChart.update();
                }

                // Atualizar gráfico de status
                if (window.statusChart) {
                    // Buscar novos dados de status
                    fetch('/api/dashboard-stats')
                        .then(response => response.json())
                        .then(stats => {
                            window.statusChart.data.datasets[0].data = [
                                stats.paid_count
                                , stats.sent_count
                                , stats.count_overdue
                                , stats.quotes_count
                            ];
                            window.statusChart.update();
                        });
                }
            });
    }

    // CSS adicional para animações (adicionar ao final do template)
    const additionalCSS = `
<style>
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out forwards;
}

.table-container {
    overflow-x: auto;
}

.notification-enter {
    transform: translateX(100%);
}

.notification-enter-active {
    transform: translateX(0);
    transition: transform 0.3s ease;
}

.hover-scale:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}

.gradient-border {
    position: relative;
    border-radius: 0.75rem;
}

.gradient-border::before {
    content: '';
    position: absolute;
    inset: 0;
    padding: 1px;
    background: linear-gradient(45deg, #3b82f6, #8b5cf6, #06b6d4);
    border-radius: inherit;
    mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    mask-composite: xor;
}

.status-indicator {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 6px;
}

.status-indicator.paid {
    background-color: #10b981;
}

.status-indicator.pending {
    background-color: #f59e0b;
}

.status-indicator.overdue {
    background-color: #ef4444;
}

.status-indicator.draft {
    background-color: #6b7280;
}
</style>
`;

    // Adicionar CSS ao documento
    document.head.insertAdjacentHTML('beforeend', additionalCSS);

</script>
@endsection
