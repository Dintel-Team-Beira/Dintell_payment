@extends('layouts.admin')

@section('title', 'Dashboard Administrativo')

@section('header-actions')
<div class="flex items-center gap-x-4">
    <div class="flex items-center px-3 py-1 text-sm text-green-800 bg-green-100 rounded-full">
        <div class="w-2 h-2 mr-2 bg-green-400 rounded-full animate-pulse"></div>
        Sistema Online
    </div>

    <select class="px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            onchange="updatePeriod(this.value)">
        <option value="7days">Últimos 7 dias</option>
        <option value="30days">Últimos 30 dias</option>
        <option value="12months" selected>Últimos 12 meses</option>
    </select>
</div>
@endsection

@section('content')
<div class="space-y-8">
    <!-- KPIs Principais -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total de Empresas -->
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total de Empresas</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['companies']['total']) }}</p>
                        <div class="flex items-center mt-1">
                            @if($stats['companies']['growth_rate'] >= 0)
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                            </svg>
                            <span class="text-sm text-green-600">+{{ number_format($stats['companies']['growth_rate'], 1) }}%</span>
                            @else
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                            </svg>
                            <span class="text-sm text-red-600">{{ number_format($stats['companies']['growth_rate'], 1) }}%</span>
                            @endif
                            <span class="ml-1 text-sm text-gray-500">vs mês anterior</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receita Mensal -->
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Receita Mensal (MRR)</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['revenue']['monthly'], 2, ',', '.') }} MT</p>
                        <div class="flex items-center mt-1">
                            @if($stats['revenue']['mrr_growth'] >= 0)
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                            </svg>
                            <span class="text-sm text-green-600">+{{ number_format($stats['revenue']['mrr_growth'], 1) }}%</span>
                            @else
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                            </svg>
                            <span class="text-sm text-red-600">{{ number_format($stats['revenue']['mrr_growth'], 1) }}%</span>
                            @endif
                            <span class="ml-1 text-sm text-gray-500">crescimento</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empresas Ativas -->
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
                        <p class="text-sm font-medium text-gray-500">Empresas Ativas</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['companies']['active']) }}</p>
                        <p class="text-sm text-gray-500">{{ number_format($stats['metrics']['activation_rate'], 1) }}% taxa de ativação</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trials -->
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Em Trial</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['companies']['trial']) }}</p>
                        @if($stats['companies']['trial_expiring_week'] > 0)
                        <p class="text-sm text-orange-600">{{ $stats['companies']['trial_expiring_week'] }} expirando esta semana</p>
                        @else
                        <p class="text-sm text-gray-500">{{ number_format($stats['metrics']['trial_to_active_rate'], 1) }}% conversão</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas Secundárias -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Métricas de Negócio</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">ARR (Receita Anual)</span>
                        <span class="font-semibold">{{ number_format($stats['revenue']['annual'], 2, ',', '.') }} MT</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">ARPU (Receita por Usuário)</span>
                        <span class="font-semibold">{{ number_format($stats['revenue']['average_per_company'], 2, ',', '.') }} MT</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Taxa de Churn</span>
                        <span class="font-semibold {{ $stats['metrics']['churn_rate'] > 5 ? 'text-red-600' : 'text-green-600' }}">
                            {{ number_format($stats['metrics']['churn_rate'], 1) }}%
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total de Usuários</span>
                        <span class="font-semibold">{{ number_format($stats['users']['total']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Faturas do Sistema</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total de Faturas</span>
                        <span class="font-semibold">{{ number_format($stats['invoices']['total']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Faturas Pagas</span>
                        <span class="font-semibold text-green-600">{{ number_format($stats['invoices']['paid']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Faturas Pendentes</span>
                        <span class="font-semibold text-orange-600">{{ number_format($stats['invoices']['pending']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Este Mês</span>
                        <span class="font-semibold">
                            {{ number_format($stats['invoices']['this_month']) }}
                            @if($stats['invoices']['growth_rate'] != 0)
                            <span class="text-xs {{ $stats['invoices']['growth_rate'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ({{ $stats['invoices']['growth_rate'] >= 0 ? '+' : '' }}{{ number_format($stats['invoices']['growth_rate'], 1) }}%)
                            </span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Distribuição por Planos</h3>
                <div class="space-y-3">
                    @foreach($chartData['plan_distribution'] as $plan => $data)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-3
                                {{ $plan === 'basic' ? 'bg-blue-500' : '' }}
                                {{ $plan === 'premium' ? 'bg-green-500' : '' }}
                                {{ $plan === 'enterprise' ? 'bg-purple-500' : '' }}">
                            </div>
                            <span class="text-sm text-gray-600 capitalize">{{ $plan ?? 'Não definido' }}</span>
                        </div>
                        <span class="font-semibold">{{ $data->count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Gráfico de Receita -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Evolução da Receita</h3>
                <p class="text-sm text-gray-600">MRR (Monthly Recurring Revenue) nos últimos 12 meses</p>
            </div>
            <div class="p-6">
                <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Gráfico de Crescimento -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Crescimento de Empresas</h3>
                <p class="text-sm text-gray-600">Novas empresas cadastradas por mês</p>
            </div>
            <div class="p-6">
                <canvas id="growthChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabelas de Dados -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Empresas Recentes -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Empresas Recentes</h3>
                    <a href="{{ route('admin.companies.index') }}"
                       class="text-sm text-blue-600 hover:text-blue-800">
                        Ver todas
                    </a>
                </div>
            </div>
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Empresa</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Criado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentCompanies as $company)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($company->logo)
                                    <img class="w-8 h-8 rounded-lg" src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }}">
                                    @else
                                    <div class="flex items-center justify-center w-8 h-8 bg-gray-200 rounded-lg">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    @endif
                                    <div class="ml-3">
                                        <a href="{{ route('admin.companies.show', $company) }}"
                                           class="text-sm font-medium text-gray-900 hover:text-blue-600">
                                            {{ $company->name }}
                                        </a>
                                        <p class="text-xs text-gray-500">{{ $company->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full
                                    {{ $company->status === 'active' ? 'text-green-800 bg-green-100' : '' }}
                                    {{ $company->status === 'trial' ? 'text-yellow-800 bg-yellow-100' : '' }}
                                    {{ $company->status === 'suspended' ? 'text-red-800 bg-red-100' : '' }}">
                                    {{ ucfirst($company->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $company->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                Nenhuma empresa recente
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Trials Expirando -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Trials Expirando</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        {{ count($trialExpiring) }} empresas
                    </span>
                </div>
            </div>
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Empresa</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Expira em</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($trialExpiring as $company)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($company->logo)
                                    <img class="w-8 h-8 rounded-lg" src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }}">
                                    @else
                                    <div class="flex items-center justify-center w-8 h-8 bg-gray-200 rounded-lg">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    @endif
                                    <div class="ml-3">
                                        <a href="{{ route('admin.companies.show', $company) }}"
                                           class="text-sm font-medium text-gray-900 hover:text-blue-600">
                                            {{ $company->name }}
                                        </a>
                                        <p class="text-xs text-gray-500">{{ $company->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium
                                    {{ $company->trial_days_left <= 1 ? 'text-red-600' : '' }}
                                    {{ $company->trial_days_left <= 3 && $company->trial_days_left > 1 ? 'text-orange-600' : '' }}
                                    {{ $company->trial_days_left > 3 ? 'text-gray-900' : '' }}">
                                    {{ $company->trial_days_left }} dias
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $company->trial_ends_at->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <button onclick="extendTrial({{ $company->id }})"
                                            class="text-blue-600 hover:text-blue-900"
                                            title="Estender trial">
                                        Estender
                                    </button>
                                    <a href="{{ route('admin.companies.show', $company) }}"
                                       class="text-gray-600 hover:text-gray-900"
                                       title="Ver detalhes">
                                        Ver
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-8 h-8 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p>Nenhum trial expirando</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Empresas por Receita -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Top Empresas por Receita</h3>
            <p class="text-sm text-gray-600">Empresas que mais geram receita no sistema</p>
        </div>
        <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Empresa</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Plano</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Receita Total</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Faturas</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topCompaniesByRevenue as $index => $company)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($company->logo)
                                <img class="w-10 h-10 rounded-lg" src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }}">
                                @else
                                <div class="flex items-center justify-center w-10 h-10 bg-gray-200 rounded-lg">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                @endif
                                <div class="ml-4">
                                    <a href="{{ route('admin.companies.show', $company) }}"
                                       class="text-sm font-medium text-gray-900 hover:text-blue-600">
                                        {{ $company->name }}
                                    </a>
                                    <p class="text-xs text-gray-500">{{ $company->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full
                                {{ $company->subscription_plan === 'basic' ? 'text-blue-800 bg-blue-100' : '' }}
                                {{ $company->subscription_plan === 'premium' ? 'text-green-800 bg-green-100' : '' }}
                                {{ $company->subscription_plan === 'enterprise' ? 'text-purple-800 bg-purple-100' : '' }}">
                                {{ ucfirst($company->subscription_plan ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                            {{ number_format($company->total_revenue, 2, ',', '.') }} MT
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ number_format($company->total_invoices) }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.companies.show', $company) }}"
                                   class="text-blue-600 hover:text-blue-900">
                                    Ver
                                </a>
                                <a href="{{ route('admin.companies.analytics', $company) }}"
                                   class="text-green-600 hover:text-green-900">
                                    Analytics
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Nenhuma empresa com receita registrada
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Atividade Recente -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Atividade Recente</h3>
            <p class="text-sm text-gray-600">Últimas ações no sistema</p>
        </div>
        <div class="flow-root p-6">
            <ul role="list" class="-mb-8">
                @forelse($recentActivity as $index => $activity)
                <li>
                    <div class="relative pb-8">
                        @if(!$loop->last)
                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                        @endif
                        <div class="relative flex space-x-3">
                            <div>
                                <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white
                                    {{ $activity['color'] === 'green' ? 'bg-green-500' : '' }}
                                    {{ $activity['color'] === 'blue' ? 'bg-blue-500' : '' }}
                                    {{ $activity['color'] === 'yellow' ? 'bg-yellow-500' : '' }}
                                    {{ $activity['color'] === 'red' ? 'bg-red-500' : '' }}">
                                    @if($activity['icon'] === 'building')
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    @elseif($activity['icon'] === 'refresh')
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    @elseif($activity['icon'] === 'clock')
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    @endif
                                </span>
                            </div>
                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                <div>
                                    <p class="text-sm text-gray-500">
                                        <strong>{{ $activity['title'] }}</strong> - {{ $activity['description'] }}
                                    </p>
                                    <p class="text-xs text-gray-400">por {{ $activity['user'] }}</p>
                                </div>
                                <div class="text-sm text-right text-gray-500 whitespace-nowrap">
                                    <time datetime="{{ $activity['time']->toISOString() }}">
                                        {{ $activity['time']->diffForHumans() }}
                                    </time>
                                    @if(isset($activity['link']))
                                    <div class="mt-1">
                                        <a href="{{ $activity['link'] }}" class="text-xs text-blue-600 hover:text-blue-900">
                                            Ver detalhes
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @empty
                <li class="py-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p>Nenhuma atividade recente</p>
                </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<!-- Modal para Estender Trial -->
<div id="extendTrialModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeExtendTrialModal()"></div>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="extendTrialForm" method="POST">
                @csrf
                <div class="px-6 pt-6 pb-4 bg-white">
                    <div class="flex items-start">
                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-blue-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Estender Trial</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Quantos dias deseja adicionar ao trial desta empresa?</p>
                                <input type="number"
                                       name="days"
                                       min="1"
                                       max="90"
                                       value="30"
                                       required
                                       class="block w-full mt-3 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Estender
                    </button>
                    <button type="button"
                            onclick="closeExtendTrialModal()"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuração dos gráficos
    const chartData = @json($chartData);

    // Gráfico de Receita
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: chartData.monthly_revenue.map(item => {
                const [year, month] = item.month.split('-');
                return new Date(year, month - 1).toLocaleDateString('pt-BR', { month: 'short', year: '2-digit' });
            }),
            datasets: [{
                label: 'Novas Empresas',
                data: chartData.company_growth.map(item => item.new_companies),
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgb(59, 130, 246)',
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
});

// Funções para modais
function extendTrial(companyId) {
    const modal = document.getElementById('extendTrialModal');
    const form = document.getElementById('extendTrialForm');

    form.action = `/admin/companies/${companyId}/extend-trial`;
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeExtendTrialModal() {
    const modal = document.getElementById('extendTrialModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function updatePeriod(period) {
    // Implementar atualização de período dos gráficos
    window.location.href = `{{ route('admin.dashboard') }}?period=${period}`;
}

// Fechar modais com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeExtendTrialModal();
    }
});

// Auto-refresh da página a cada 5 minutos
setTimeout(function() {
    window.location.reload();
}, 300000);
</script>
@endpush 
