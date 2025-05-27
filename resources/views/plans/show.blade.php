@extends('layouts.app')

@section('title', $plan->name . ' - Detalhes do Plano')

@section('header-actions')
<div class="flex items-center gap-x-3">
    <a href="{{ route('plans.index') }}"
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-all bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Voltar aos Planos
    </a>

    <a href="{{ route('plans.edit', $plan) }}"
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-all bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Editar Plano
    </a>

    <div class="relative">
        <button onclick="toggleDropdown()"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-all bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
            </svg>
            Mais Ações
        </button>

        <div id="actionsDropdown" class="absolute right-0 z-10 hidden w-48 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg">
            <div class="py-1">
                <button onclick="copyPlanUrl()" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                    </svg>
                    Copiar Link
                </button>
                <a href="#" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exportar Dados
                </a>
                @if($plan->is_active)
                <button onclick="togglePlanStatus(false)" class="flex items-center w-full px-4 py-2 text-sm text-amber-700 hover:bg-amber-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Desativar Plano
                </button>
                @else
                <button onclick="togglePlanStatus(true)" class="flex items-center w-full px-4 py-2 text-sm text-emerald-700 hover:bg-emerald-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V9a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2z"/>
                    </svg>
                    Ativar Plano
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="mx-auto max-w-8xl">
    <!-- Plan Header Card -->
    <div class="mb-8 overflow-hidden bg-white border border-gray-200 shadow-sm rounded-2xl">
        <div class="relative">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-5" style="background: linear-gradient(45deg, {{ $plan->color_theme  }}, transparent);"></div>

            <div class="relative p-5 px-8 py-10">
                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-6">
                        <!-- Plan Icon -->
                        <div class="flex items-center justify-center w-20 h-20 shadow-lg rounded-2xl"
                             style="background: linear-gradient(135deg, {{ $plan->color_theme ?? '#3B82F6' }}, {{ $plan->color_theme ?? '#3B82F6' }}CC);">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>

                        <!-- Plan Info -->
                        <div>
                            <div class="flex items-center mb-2 space-x-3">
                                <h1 class="text-3xl font-bold text-gray-900">{{ $plan->name }}</h1>
                                @if($plan->is_featured)
                                <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-yellow-800 bg-yellow-100 rounded-full">
                                    ⭐ Destaque
                                </span>
                                @endif
                            </div>

                            <div class="flex items-center mb-3 space-x-4">
                                <div class="text-4xl font-bold" style="color: {{ $plan->color_theme ?? '#3B82F6' }}">
                                    MT {{ number_format($plan->price, 2) }}
                                </div>
                                <div class="text-lg text-gray-600">
                                    / {{ $plan->billing_cycle }}
                                </div>
                                @if($plan->setup_fee > 0)
                                <div class="text-sm text-gray-500">
                                    + MT {{ number_format($plan->setup_fee, 2) }} setup
                                </div>
                                @endif
                            </div>

                            <div class="flex items-center space-x-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $plan->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                    <div class="w-2 h-2 rounded-full mr-2
                                        {{ $plan->is_active ? 'bg-emerald-500' : 'bg-gray-500' }}"></div>
                                    {{ $plan->is_active ? 'Ativo' : 'Inativo' }}
                                </span>

                                <span class="text-sm text-gray-500">
                                    Slug: <code class="px-2 py-1 text-xs bg-gray-100 rounded">{{ $plan->slug }}</code>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Plan Stats -->
                    <div class="text-right">
                        <div class="grid grid-cols-1 gap-3">
                            <div class="p-3 border border-gray-200 rounded-lg bg-white/50 backdrop-blur">
                                <div class="text-2xl font-bold text-gray-900">{{ $plan->subscriptions_count ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Assinantes</div>
                            </div>
                            <div class="p-3 border border-gray-200 rounded-lg bg-white/50 backdrop-blur">
                                <div class="text-2xl font-bold" style="color: {{ $plan->color_theme ?? '#3B82F6' }}">
                                    MT {{ number_format(($plan->subscriptions_count ?? 0) * $plan->price, 0) }}
                                </div>
                                <div class="text-sm text-gray-600">Receita/mês</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($plan->description)
                <div class="pt-6 mt-6 border-t border-gray-200">
                    <p class="leading-relaxed text-gray-700">{{ $plan->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Left Column - Plan Details -->
        <div class="space-y-8 lg:col-span-2">
            <!-- Pricing Details -->
            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                <div class="flex items-center mb-6">
                    <div class="flex items-center justify-center w-8 h-8 mr-3 rounded-lg bg-emerald-100">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900">Detalhes de Preço</h2>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="p-4 rounded-lg bg-gray-50">
                        <div class="mb-1 text-sm font-medium text-gray-700">Preço Base</div>
                        <div class="text-2xl font-bold text-gray-900">MT {{ number_format($plan->price, 2) }}</div>
                        <div class="text-sm text-gray-600">Por {{ $plan->billing_cycle }}</div>
                    </div>

                    @if($plan->setup_fee > 0)
                    <div class="p-4 rounded-lg bg-gray-50">
                        <div class="mb-1 text-sm font-medium text-gray-700">Taxa de Instalação</div>
                        <div class="text-2xl font-bold text-gray-900">MT {{ number_format($plan->setup_fee, 2) }}</div>
                        <div class="text-sm text-gray-600">Única vez</div>
                    </div>
                    @endif

                    <div class="p-4 rounded-lg bg-gray-50">
                        <div class="mb-1 text-sm font-medium text-gray-700">Ciclo de Cobrança</div>
                        <div class="text-lg font-semibold text-gray-900">{{ $plan->billing_cycle_days }} dias</div>
                        <div class="text-sm text-gray-600">{{ ucfirst($plan->billing_cycle) }}</div>
                    </div>

                    @if($plan->trial_days > 0)
                    <div class="p-4 rounded-lg bg-blue-50">
                        <div class="mb-1 text-sm font-medium text-blue-700">Período de Trial</div>
                        <div class="text-lg font-semibold text-blue-900">{{ $plan->trial_days }} dias</div>
                        <div class="text-sm text-blue-600">Gratuito</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Resource Limits -->
            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                <div class="flex items-center mb-6">
                    <div class="flex items-center justify-center w-8 h-8 mr-3 bg-purple-100 rounded-lg">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900">Limites de Recursos</h2>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div class="p-4 text-center rounded-lg bg-gray-50">
                        <div class="mb-1 text-3xl font-bold text-gray-900">{{ $plan->max_domains }}</div>
                        <div class="text-sm text-gray-600">Domínios</div>
                    </div>

                    <div class="p-4 text-center rounded-lg bg-gray-50">
                        <div class="mb-1 text-3xl font-bold text-gray-900">{{ $plan->max_storage_gb }}</div>
                        <div class="text-sm text-gray-600">GB Storage</div>
                    </div>

                    <div class="p-4 text-center rounded-lg bg-gray-50">
                        <div class="mb-1 text-3xl font-bold text-gray-900">{{ $plan->max_bandwidth_gb }}</div>
                        <div class="text-sm text-gray-600">GB Bandwidth</div>
                    </div>

                    <div class="p-4 text-center rounded-lg bg-gray-50">
                        <div class="mb-1 text-3xl font-bold text-gray-900">∞</div>
                        <div class="text-sm text-gray-600">Requests</div>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                <div class="flex items-center mb-6">
                    <div class="flex items-center justify-center w-8 h-8 mr-3 rounded-lg bg-amber-100">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900">Funcionalidades Incluídas</h2>
                </div>

                @php
                    $features = is_array($plan->features) ? $plan->features : ($plan->features ? json_decode($plan->features, true) : []);
                @endphp

                @if(!empty($features))
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        @foreach($features as $feature)
                        <div class="flex items-center p-3 rounded-lg bg-gray-50">
                            <div class="flex items-center justify-center w-5 h-5 mr-3 rounded-full"
                                 style="background-color: {{ $plan->color_theme ?? '#3B82F6' }}20;">
                                <svg class="w-3 h-3" style="color: {{ $plan->color_theme ?? '#3B82F6' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-gray-700">{{ $feature }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <p class="text-gray-500">Nenhuma funcionalidade específica cadastrada</p>
                        <a href="{{ route('plans.edit', $plan) }}" class="inline-block mt-2 text-sm text-blue-600 hover:text-blue-800">
                            Adicionar funcionalidades
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Ações Rápidas</h3>

                <div class="space-y-3">
                    <a href="{{ route('plans.edit', $plan) }}"
                       class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar Plano
                    </a>

                    <button onclick="copyPlanUrl()"
                            class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                        </svg>
                        Copiar Link
                    </button>

                    @if($plan->is_active)
                    <button onclick="togglePlanStatus(false)"
                            class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium transition-colors border rounded-lg text-amber-700 bg-amber-50 border-amber-200 hover:bg-amber-100">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Desativar Plano
                    </button>
                    @else
                    <button onclick="togglePlanStatus(true)"
                            class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium transition-colors border rounded-lg text-emerald-700 bg-emerald-50 border-emerald-200 hover:bg-emerald-100">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V9a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2z"/>
                        </svg>
                        Ativar Plano
                    </button>
                    @endif
                </div>
            </div>

            <!-- Plan Information -->
            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Informações do Plano</h3>

                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-700">ID do Plano</dt>
                        <dd class="mt-1 font-mono text-sm text-gray-900">{{ $plan->id }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-700">Slug</dt>
                        <dd class="mt-1 font-mono text-sm text-gray-900">{{ $plan->slug }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-700">Cor do Tema</dt>
                        <dd class="flex items-center mt-1 space-x-2">
                            <div class="w-4 h-4 border border-gray-300 rounded" style="background-color: {{ $plan->color_theme ?? '#3B82F6' }}"></div>
                            <span class="font-mono text-sm text-gray-900">{{ $plan->color_theme ?? '#3B82F6' }}</span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-700">Criado em</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $plan->created_at->format('d/m/Y H:i') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-700">Última atualização</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $plan->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Recent Subscriptions -->
            @if(isset($plan->subscriptions) && $plan->subscriptions->count() > 0)
            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Assinantes Recentes</h3>
                    <a href="{{ route('subscriptions.index', ['plan' => $plan->id]) }}" class="text-sm text-blue-600 hover:text-blue-800">
                        Ver todos
                    </a>
                </div>

                <div class="space-y-3">
                    @foreach($plan->subscriptions->take(5) as $subscription)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50">
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $subscription->client->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-600">{{ $subscription->domain }}</div>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            {{ $subscription->status === 'active' ? 'bg-emerald-100 text-emerald-800' :
                               ($subscription->status === 'suspended' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($subscription->status) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Usage Analytics -->
            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Estatísticas de Uso</h3>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total de Assinantes</span>
                        <span class="text-sm font-medium text-gray-900">{{ $plan->subscriptions_count ?? 0 }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Assinantes Ativos</span>
                        <span class="text-sm font-medium text-emerald-600">{{ $plan->active_subscriptions_count ?? 0 }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Receita Mensal</span>
                        <span class="text-sm font-medium text-gray-900">MT {{ number_format(($plan->active_subscriptions_count ?? 0) * $plan->price, 2) }}</span>
                    </div>

                    @if($plan->trial_days > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Em Trial</span>
                        <span class="text-sm font-medium text-blue-600">{{ $plan->trial_subscriptions_count ?? 0 }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div id="successMessage" class="fixed z-50 max-w-md p-4 border rounded-lg shadow-lg top-4 right-4 bg-emerald-100 border-emerald-400 text-emerald-700">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
</div>
@endif

@if(session('error'))
<div id="errorMessage" class="fixed z-50 max-w-md p-4 text-red-700 bg-red-100 border border-red-400 rounded-lg shadow-lg top-4 right-4">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
</div>
@endif

<!-- Copy Success Toast -->
<div id="copyToast" class="fixed z-50 max-w-md p-4 text-blue-700 transition-transform duration-300 transform translate-x-full bg-blue-100 border border-blue-400 rounded-lg shadow-lg top-4 right-4">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
        </svg>
        Link copiado com sucesso!
    </div>
</div>

<script>
// Dropdown toggle
function toggleDropdown() {
    const dropdown = document.getElementById('actionsDropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('actionsDropdown');
    const button = event.target.closest('button');

    if (!button || button.onclick !== toggleDropdown) {
        dropdown.classList.add('hidden');
    }
});

// Copy plan URL
function copyPlanUrl() {
    const url = window.location.href;

    navigator.clipboard.writeText(url).then(function() {
        showCopyToast();
    }).catch(function(err) {
        console.error('Erro ao copiar: ', err);
        fallbackCopyText(url);
    });
}

// Fallback copy method
function fallbackCopyText(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.left = "-999999px";
    textArea.style.top = "-999999px";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        document.execCommand('copy');
        showCopyToast();
    } catch (err) {
        console.error('Fallback: Erro ao copiar', err);
        alert('Erro ao copiar link. Tente novamente.');
    }

    document.body.removeChild(textArea);
}

// Show copy success toast
function showCopyToast() {
    const toast = document.getElementById('copyToast');
    toast.classList.remove('translate-x-full');
    toast.classList.add('translate-x-0');

    setTimeout(() => {
        toast.classList.remove('translate-x-0');
        toast.classList.add('translate-x-full');
    }, 3000);
}

// Toggle plan status
function togglePlanStatus(activate) {
    const action = activate ? 'ativar' : 'desativar';
    const confirmed = confirm(`Tem certeza que deseja ${action} este plano?`);

    if (confirmed) {
        // Create form to submit status change
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("plans.toggle", $plan) }}';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';

        const statusField = document.createElement('input');
        statusField.type = 'hidden';
        statusField.name = 'is_active';
        statusField.value = activate ? '1' : '0';

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        form.appendChild(statusField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Auto-hide flash messages
document.addEventListener('DOMContentLoaded', function() {
    const messages = ['successMessage', 'errorMessage'];

    messages.forEach(messageId => {
        const element = document.getElementById(messageId);
        if (element) {
            setTimeout(() => {
                element.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
                element.style.opacity = '0';
                element.style.transform = 'translateX(100%)';
                setTimeout(() => element.remove(), 500);
            }, 5000);

            // Add close button
            const closeBtn = document.createElement('button');
            closeBtn.innerHTML = '×';
            closeBtn.className = 'ml-auto text-lg font-bold opacity-70 hover:opacity-100 leading-none';
            closeBtn.onclick = () => element.remove();
            element.querySelector('div').appendChild(closeBtn);
        }
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // E key to edit
    if (e.key === 'e' || e.key === 'E') {
        if (!e.target.tagName.match(/INPUT|TEXTAREA|SELECT/)) {
            window.location.href = '{{ route("plans.edit", $plan) }}';
        }
    }

    // Escape to go back
    if (e.key === 'Escape') {
        window.location.href = '{{ route("plans.index") }}';
    }

    // Ctrl/Cmd + C to copy URL
    if ((e.ctrlKey || e.metaKey) && e.key === 'c' && !window.getSelection().toString()) {
        e.preventDefault();
        copyPlanUrl();
    }
});

// Smooth scroll to sections (if needed)
function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
}

// Enhanced interactions
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to cards
    const cards = document.querySelectorAll('.bg-white.rounded-2xl');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.1)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });

    // Animate statistics on scroll
    const stats = document.querySelectorAll('.text-3xl.font-bold');
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                entry.target.classList.add('animated');
                animateNumber(entry.target);
            }
        });
    }, observerOptions);

    stats.forEach(stat => observer.observe(stat));
});

// Animate numbers
function animateNumber(element) {
    const targetValue = element.textContent.replace(/[^0-9]/g, '');
    if (!targetValue) return;

    const target = parseInt(targetValue);
    const duration = 1000;
    const increment = target / (duration / 16);
    let current = 0;

    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }

        const formattedValue = element.textContent.replace(/[0-9,]/g, '') + Math.floor(current).toLocaleString();
        element.textContent = formattedValue;
    }, 16);
}

// Print functionality
function printPlan() {
    window.print();
}

// Export functionality placeholder
function exportPlanData() {
    const planData = {
        id: {{ $plan->id }},
        name: '{{ addslashes($plan->name) }}',
        slug: '{{ $plan->slug }}',
        price: {{ $plan->price }},
        billing_cycle: '{{ $plan->billing_cycle }}',
        billing_cycle_days: {{ $plan->billing_cycle_days }},
        setup_fee: {{ $plan->setup_fee ?? 0 }},
        trial_days: {{ $plan->trial_days ?? 0 }},
        max_domains: {{ $plan->max_domains }},
        max_storage_gb: {{ $plan->max_storage_gb }},
        max_bandwidth_gb: {{ $plan->max_bandwidth_gb }},
        features: @json($plan->features ?? []),
        is_active: {{ $plan->is_active ? 'true' : 'false' }},
        is_featured: {{ $plan->is_featured ? 'true' : 'false' }},
        color_theme: '{{ $plan->color_theme ?? '#3B82F6' }}',
        created_at: '{{ $plan->created_at }}',
        updated_at: '{{ $plan->updated_at }}'
    };

    const dataStr = JSON.stringify(planData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});

    const link = document.createElement('a');
    link.href = URL.createObjectURL(dataBlob);
    link.download = 'plan_{{ $plan->slug }}_{{ now()->format("Y-m-d") }}.json';
    link.click();
}
</script>

<style>
/* Custom animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

/* Smooth transitions */
.transition-all {
    transition: all 0.3s ease-in-out;
}

/* Card hover effects */
.hover-lift {
    transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Status indicators */
.status-active {
    animation: pulse 2s infinite;
}

/* Custom scrollbar */
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }

    body {
        background: white !important;
    }

    .bg-white {
        background: white !important;
    }

    .shadow-sm,
    .shadow-lg {
        box-shadow: none !important;
    }

    .border {
        border: 1px solid #d1d5db !important;
    }

    .rounded-2xl {
        border-radius: 8px !important;
    }
}

/* Mobile responsive adjustments */
@media (max-width: 1024px) {
    .lg\:col-span-2 {
        margin-bottom: 2rem;
    }
}

@media (max-width: 640px) {
    .space-y-8 > * + * {
        margin-top: 1.5rem;
    }

    .px-8 {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .py-10 {
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
    }

    .text-3xl {
        font-size: 1.5rem;
    }

    .text-4xl {
        font-size: 2rem;
    }

    .w-20.h-20 {
        width: 3rem;
        height: 3rem;
    }

    .grid-cols-4 {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Focus states for accessibility */
button:focus,
a:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Loading states */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    margin: auto;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endsection