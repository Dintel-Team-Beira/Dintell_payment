@extends('layouts.admin')

@section('title', 'Detalhes da Empresa - ' . $company->name)

@section('header-actions')
<div class="flex items-center gap-x-4">
    <a href="{{ route('admin.companies.index') }}"
       class="flex items-center px-3 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Voltar
    </a>

    <div class="flex items-center space-x-2">
        @if($company->status === 'suspended')
        <form action="{{ route('admin.companies.activate', $company) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit"
                    class="flex items-center px-3 py-2 text-sm font-semibold text-green-700 bg-green-100 border border-green-200 rounded-md hover:bg-green-200"
                    onclick="return confirm('Tem certeza que deseja ativar esta empresa?')">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Ativar
            </button>
        </form>
        @else
        <button type="button"
                onclick="openSuspendModal()"
                class="flex items-center px-3 py-2 text-sm font-semibold text-red-700 bg-red-100 border border-red-200 rounded-md hover:bg-red-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364"/>
            </svg>
            Suspender
        </button>
        @endif

        @if($company->is_trial)
        <button type="button"
                onclick="openExtendTrialModal()"
                class="flex items-center px-3 py-2 text-sm font-semibold text-blue-700 bg-blue-100 border border-blue-200 rounded-md hover:bg-blue-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Estender Trial
        </button>
        @endif

        <a href="{{ route('admin.companies.edit', $company) }}"
           class="flex items-center px-3 py-2 text-sm font-semibold text-blue-700 bg-blue-100 border border-blue-200 rounded-md hover:bg-blue-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar
        </a>

        @if($company->users()->where('role', 'admin')->exists())
        <form action="{{ route('admin.companies.impersonate', $company) }}" method="POST" class="inline">
            @csrf
            <button type="submit"
                    class="flex items-center px-3 py-2 text-sm font-semibold text-purple-700 bg-purple-100 border border-purple-200 rounded-md hover:bg-purple-200"
                    onclick="return confirm('Tem certeza que deseja acessar como esta empresa?')">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Impersonar
            </button>
        </form>
        @endif
    </div>
</div>
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <!-- Header da Empresa -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center">
                    @if($company->logo)
                    <img src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }}" class="w-16 h-16 mr-4 rounded-lg">
                    @else
                    <div class="flex items-center justify-center w-16 h-16 mr-4 bg-gray-100 rounded-lg">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    @endif

                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $company->name }}</h1>
                        <div class="flex items-center mt-2 space-x-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $company->status === 'active' ? 'bg-green-100 text-green-800' :
                                   ($company->status === 'trial' ? 'bg-blue-100 text-blue-800' :
                                   ($company->status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($company->status) }}
                            </span>

                            @if($company->subscription_plan)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ ucfirst($company->subscription_plan) }}
                            </span>
                            @endif

                            @if($company->is_trial && $company->trial_ends_at)
                            <span class="text-sm text-gray-500">
                                Trial expira em {{ $company->trial_days_left }} dias
                            </span>
                            @endif
                        </div>
                        <div class="flex items-center mt-2 space-x-4 text-sm text-gray-500">
                            <span>{{ $company->email }}</span>
                            @if($company->phone)
                            <span>{{ $company->phone }}</span>
                            @endif
                            <span>Criado em {{ $company->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    @if($company->subdomain_url)
                    <a href="{{ $company->subdomain_url }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800">
                        {{ $company->subdomain_url }}
                        <svg class="inline w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Usuários</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900">{{ $company->users->count() }}</p>
                            <p class="ml-2 text-sm text-gray-500">/ {{ $company->max_users }}</p>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                            <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(100, ($company->users->count() / max(1, $company->max_users)) * 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total de Faturas</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_invoices'] }}</p>
                            <p class="ml-2 text-sm text-gray-500">/ {{ $company->max_invoices_per_month }} por mês</p>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                            <div class="bg-green-600 h-1.5 rounded-full" style="width: {{ min(100, ($stats['total_invoices'] / max(1, $company->max_invoices_per_month)) * 100) }}%"></div>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mr-2">
                                {{ $stats['paid_invoices'] }} Pagas
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ $stats['pending_invoices'] }} Pendentes
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Clientes</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900">{{ $company->total_clients ?? 0 }}</p>
                            <p class="ml-2 text-sm text-gray-500">/ {{ $company->max_clients }}</p>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                            <div class="bg-purple-600 h-1.5 rounded-full" style="width: {{ min(100, (($company->total_clients ?? 0) / max(1, $company->max_clients)) * 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Receita</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_revenue'], 2) }} MT</p>
                        <div class="mt-1 text-sm text-gray-500">
                            <span>Mensal: {{ number_format($stats['monthly_revenue'], 2) }} MT</span>
                            <span class="ml-2">• Média: {{ number_format($stats['avg_invoice_value'] ?? 0, 2) }} MT</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Informações Detalhadas -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Detalhes da Empresa -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Detalhes da Empresa</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nome</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Telefone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->phone ?: 'Não informado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">NUIT</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->tax_number ?: 'Não informado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cidade</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->city ?: 'Não informado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Slug</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->slug }}</dd>
                        </div>
                        @if($company->address)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Endereço</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->address }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Configurações -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Configurações</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Plano de Subscrição</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ ucfirst($company->subscription_plan) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Taxa de IVA Padrão</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->default_tax_rate }}%</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Moeda</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->currency }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Domínio Personalizado</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $company->custom_domain_enabled ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $company->custom_domain_enabled ? 'Habilitado' : 'Desabilitado' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Acesso à API</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $company->api_access_enabled ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $company->api_access_enabled ? 'Habilitado' : 'Desabilitado' }}
                                </span>
                            </dd>
                        </div>
                        @if($company->last_activity_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Última Atividade</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->last_activity_at->diffForHumans() }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Usuários da Empresa -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Usuários</h3>
                        <span class="text-sm text-gray-500">{{ $company->users->count() }} usuário(s)</span>
                    </div>
                </div>
                <div class="p-6">
                    @if($company->users->count() > 0)
                    <div class="space-y-4">
                        @foreach($company->users as $user)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center">
                                @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full">
                                @else
                                <div class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-full">
                                    <span class="text-sm font-medium text-gray-600">{{ substr($user->name, 0, 2) }}</span>
                                </div>
                                @endif
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="py-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        <p>Nenhum usuário cadastrado</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Atividade Recente -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Atividade Mensal</h3>
                </div>
                <div class="p-6">
                    @if($monthlyActivity->count() > 0)
                    <div class="space-y-4">
                        @foreach($monthlyActivity->take(6) as $activity)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::createFromDate($activity->year, $activity->month, 1)->format('M Y') }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $activity->invoices_count }} faturas</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ number_format($activity->revenue, 2) }} MT</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="py-8 text-center text-gray-500">
                        <p>Nenhuma atividade registrada</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Informações de Cobrança -->
            @if($company->next_payment_due)
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Cobrança</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Próximo Pagamento</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->next_payment_due->format('d/m/Y') }}</dd>
                        </div>
                        @if($company->monthly_fee)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Taxa Mensal</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($company->monthly_fee, 2) }} MT</dd>
                        </div>
                        @endif
                        @if($company->last_payment_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Último Pagamento</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->last_payment_at->format('d/m/Y') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            @endif

            <!-- Ações Rápidas -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Ações Rápidas</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.users.index', ['company_id' => $company->id]) }}"
                       class="flex items-center w-full px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        Ver Usuários
                    </a>

                    <button type="button"
                            onclick="sendNotification()"
                            class="flex items-center w-full px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-6H4v6z"/>
                        </svg>
                        Enviar Notificação
                    </button>

                    <button type="button"
                            onclick="resetPassword()"
                            class="flex items-center w-full px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Reset Senhas
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Suspensão -->
<div id="suspendModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.companies.suspend', $company) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="px-6 pt-6 pb-4 bg-white">
                    <div class="flex items-center mb-4">
                        <div class="p-2 mr-3 bg-red-100 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Suspender Empresa</h3>
                    </div>

                    <p class="mb-4 text-sm text-gray-600">
                        Tem certeza que deseja suspender a empresa <strong>{{ $company->name }}</strong>?
                        Esta ação irá bloquear o acesso de todos os usuários da empresa.
                    </p>

                    <div>
                        <label for="reason" class="block mb-2 text-sm font-medium text-gray-700">
                            Motivo da Suspensão *
                        </label>
                        <textarea name="reason"
                                  id="reason"
                                  rows="3"
                                  required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                  placeholder="Descreva o motivo da suspensão..."></textarea>
                    </div>
                </div>

                <div class="flex justify-between px-6 py-3 bg-gray-50">
                    <button type="button"
                            onclick="closeSuspendModal()"
                            class="inline-flex justify-center px-4 py-2 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="inline-flex justify-center px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm">
                        Suspender
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Extensão de Trial -->
<div id="extendTrialModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.companies.extend-trial', $company) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="px-6 pt-6 pb-4 bg-white">
                    <div class="flex items-center mb-4">
                        <div class="p-2 mr-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Estender Trial</h3>
                    </div>

                    <p class="mb-4 text-sm text-gray-600">
                        Empresa <strong>{{ $company->name }}</strong> está no período de trial.
                        @if($company->trial_ends_at)
                        Trial atual expira em {{ $company->trial_days_left }} dias ({{ $company->trial_ends_at->format('d/m/Y') }}).
                        @endif
                    </p>

                    <div>
                        <label for="days" class="block mb-2 text-sm font-medium text-gray-700">
                            Dias para Estender *
                        </label>
                        <input type="number"
                               name="days"
                               id="days"
                               min="1"
                               max="90"
                               value="30"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Máximo: 90 dias</p>
                    </div>
                </div>

                <div class="flex justify-between px-6 py-3 bg-gray-50">
                    <button type="button"
                            onclick="closeExtendTrialModal()"
                            class="inline-flex justify-center px-4 py-2 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="inline-flex justify-center px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                        Estender Trial
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openSuspendModal() {
    document.getElementById('suspendModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeSuspendModal() {
    document.getElementById('suspendModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function openExtendTrialModal() {
    document.getElementById('extendTrialModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeExtendTrialModal() {
    document.getElementById('extendTrialModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function sendNotification() {
    // Implementar envio de notificação
    alert('Funcionalidade de notificação será implementada');
}

function resetPassword() {
    if (confirm('Tem certeza que deseja resetar as senhas de todos os usuários desta empresa?')) {
        // Implementar reset de senhas
        alert('Funcionalidade de reset de senhas será implementada');
    }
}

// Fechar modais com Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSuspendModal();
        closeExtendTrialModal();
    }
});
</script>
@endpush

@push('styles')
<style>
.transition-colors {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.modal-overlay {
    backdrop-filter: blur(4px);
}
</style>
@endpush
@endsection
