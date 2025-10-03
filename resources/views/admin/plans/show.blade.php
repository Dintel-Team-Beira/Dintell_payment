@extends('layouts.admin')

@section('title', 'Detalhes do Plano - ' . $plan->name)

@section('content')
<div class="container mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-9 9a1 1 0 001.414 1.414L9 5.414V17a1 1 0 102 0V5.414l7.293 7.293a1 1 0 001.414-1.414l-9-9z"/>
                                </svg>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <a href="{{ route('admin.plans.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Planos</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $plan->name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-16 h-16 text-white rounded-xl" style="background-color: {{ $plan->color ?? '#3B82F6' }};">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($plan->icon == 'star')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                @elseif($plan->icon == 'rocket')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                @elseif($plan->icon == 'building')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                @endif
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $plan->name }}</h1>
                        @if($plan->description)
                            <p class="mt-2 text-gray-600">{{ $plan->description }}</p>
                        @endif
                        <div class="flex items-center mt-3 space-x-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $plan->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                            @if($plan->is_popular)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Popular
                                </span>
                            @endif
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($plan->billing_cycle) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex space-x-3">
                <a href="{{ route('admin.plans.edit', $plan) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>

                <button type="button" onclick="toggleStatus({{ $plan->id }})" class="inline-flex items-center px-4 py-2 text-sm font-medium {{ $plan->is_active ? 'text-red-700 bg-red-50 border-red-200 hover:bg-red-100 focus:ring-red-500' : 'text-green-700 bg-green-50 border-green-200 hover:bg-green-100 focus:ring-green-500' }} border rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($plan->is_active)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-9-4V8a3 3 0 013-3h4a3 3 0 013 3v2M7 21h10a2 2 0 002-2v-5a2 2 0 00-2-2H7a2 2 0 00-2 2v5a2 2 0 002 2z"/>
                        @endif
                    </svg>
                    {{ $plan->is_active ? 'Desativar' : 'Ativar' }}
                </button>

                <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
        <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-md">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 w-0 ml-5">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Subscrições Ativas</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['active_subscriptions']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-8 h-8 bg-yellow-100 rounded-md">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 w-0 ml-5">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Receita Total</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_revenue'], 2) }} MT</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-md">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 w-0 ml-5">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Empresas</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['companies_count']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-8 h-8 bg-purple-100 rounded-md">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 w-0 ml-5">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Uso Médio</dt>
                            {{-- <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['average_usage']) }}%</dd> --}}
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Plan Details -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Pricing Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informações de Preços</h3>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Preço</label>
                            <div class="mt-1 text-3xl font-bold text-gray-900">
                                {{ number_format($plan->price, 2) }}
                                <span class="text-sm font-normal text-gray-500">{{ $plan->currency ?? 'MZN' }}</span>
                            </div>
                            <p class="text-sm text-gray-500">Por {{ $plan->billing_cycle }}</p>
                        </div>

                        @if($plan->setup_fee && $plan->setup_fee > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Taxa de Configuração</label>
                            <div class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ number_format($plan->setup_fee, 2) }}
                                <span class="text-sm font-normal text-gray-500">{{ $plan->currency ?? 'MZN' }}</span>
                            </div>
                            <p class="text-sm text-gray-500">Pagamento único</p>
                        </div>
                        @endif
                    </div>

                    @if($plan->has_trial && $plan->trial_days > 0)
                    <div class="p-4 mt-6 border border-blue-200 rounded-lg bg-blue-50">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span class="text-sm font-medium text-blue-900">
                                Período de Teste: {{ $plan->trial_days }} {{ $plan->trial_days == 1 ? 'dia' : 'dias' }} grátis
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Features -->
            @if($plan->features && count($plan->features) > 0)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Funcionalidades</h3>
                </div>
                <div class="px-6 py-6">
                    <ul class="space-y-3">
                        @foreach($plan->features as $feature)
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="ml-3 text-sm text-gray-700">{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Limitations -->
            @if($plan->limitations && count($plan->limitations) > 0)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Limitações</h3>
                </div>
                <div class="px-6 py-6">
                    <ul class="space-y-3">
                        @foreach($plan->limitations as $limitation)
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <span class="ml-3 text-sm text-gray-700">{{ $limitation }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Ações Rápidas</h3>
                </div>
                <div class="px-6 py-6 space-y-3">
                    <button type="button" onclick="togglePopular({{ $plan->id }})" class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-medium {{ $plan->is_popular ? 'text-yellow-700 bg-yellow-50 border-yellow-200 hover:bg-yellow-100' : 'text-gray-700 bg-gray-50 border-gray-200 hover:bg-gray-100' }} border rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        {{ $plan->is_popular ? 'Remover "Popular"' : 'Marcar como Popular' }}
                    </button>

                    <form action="{{ route('admin.plans.duplicate', $plan) }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Duplicar Plano
                        </button>
                    </form>
                </div>
            </div>

            <!-- Plan Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informações do Plano</h3>
                </div>
                <div class="px-6 py-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Slug</label>
                        <code class="block px-2 py-1 mt-1 text-sm text-gray-900 bg-gray-100 rounded">{{ $plan->slug }}</code>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Criado em</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $plan->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Última atualização</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $plan->updated_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ordem de exibição</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $plan->sort_order ?? 'Não definida' }}</p>
                    </div>
                </div>
            </div>

            <!-- Delete Plan -->
            @if($stats['active_subscriptions'] == 0)
            <div class="bg-white border-l-4 border-red-400 rounded-lg shadow">
                <div class="px-6 py-4">
                    <h3 class="text-lg font-medium text-red-900">Zona de Perigo</h3>
                    <p class="mt-2 text-sm text-red-700">
                        Esta ação não pode ser desfeita. Isso irá excluir permanentemente o plano.
                    </p>
                    <div class="mt-4">
                        <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este plano? Esta ação não pode ser desfeita.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Excluir Plano
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white border-l-4 border-yellow-400 rounded-lg shadow">
                <div class="px-6 py-4">
                    <h3 class="text-lg font-medium text-yellow-900">Aviso</h3>
                    <p class="mt-2 text-sm text-yellow-700">
                        Este plano não pode ser excluído pois possui {{ $stats['active_subscriptions'] }} subscrição(ões) ativa(s).
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Recent Subscriptions -->
    @if($plan->subscriptions && $plan->subscriptions->count() > 0)
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Subscrições Recentes</h3>
                    <span class="text-sm text-gray-500">{{ $plan->subscriptions->count() }} total</span>
                </div>
            </div>
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Empresa</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Valor</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Data de Início</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Próximo Pagamento</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($plan->subscriptions->take(10) as $subscription)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8">
                                        <div class="flex items-center justify-center w-8 h-8 bg-gray-200 rounded-full">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ substr($subscription->company->name ?? 'N/A', 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $subscription->company->name ?? 'Empresa não encontrada' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $subscription->company->email ?? 'Email não disponível' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($subscription->status == 'active') bg-green-100 text-green-800
                                    @elseif($subscription->status == 'expired') bg-red-100 text-red-800
                                    @elseif($subscription->status == 'cancelled') bg-gray-100 text-gray-800
                                    @elseif($subscription->status == 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                {{ number_format($subscription->amount, 2) }} MT
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $subscription->starts_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                @if($subscription->next_billing_date)
                                    {{ $subscription->next_billing_date->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($plan->subscriptions->count() > 10)
                <div class="px-6 py-3 text-center bg-gray-50">
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-500">
                        Ver todas as {{ $plan->subscriptions->count() }} subscrições
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function toggleStatus(planId) {
    fetch(`/admin/plans/${planId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification('Erro ao alterar status do plano', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao alterar status do plano', 'error');
    });
}

function togglePopular(planId) {
    fetch(`/admin/plans/${planId}/toggle-popular`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification('Erro ao alterar popularidade do plano', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao alterar popularidade do plano', 'error');
    });
}

function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300`;

    const colors = {
        success: 'bg-green-50 border border-green-200 text-green-800',
        error: 'bg-red-50 border border-red-200 text-red-800',
        info: 'bg-blue-50 border border-blue-200 text-blue-800',
        warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800'
    };

    const icons = {
        success: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
        error: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
        info: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
        warning: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>'
    };

    notification.className += ` ${colors[type]}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0 mr-3">
                ${icons[type]}
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

    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}
</script>
@endpush

@push('styles')
<style>
.notification {
    transform: translateX(100%);
}

.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Hover effects for cards */
.hover\:bg-gray-50:hover {
    background-color: #f9fafb;
}

/* Focus styles */
.focus\:ring-2:focus {
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

/* Custom scrollbar for tables */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Status badge animations */
.inline-flex.px-2 {
    transition: all 0.2s ease-in-out;
}

.inline-flex.px-2:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Card hover effects */
.bg-white.shadow.rounded-lg {
    transition: all 0.2s ease-in-out;
}

.bg-white.shadow.rounded-lg:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Button hover effects */
button:hover:not(:disabled),
a:hover {
    transform: translateY(-1px);
}

button:disabled {
    transform: none;
    opacity: 0.6;
    cursor: not-allowed;
}

/* Responsive improvements */
@media (max-width: 640px) {
    .grid.grid-cols-1.gap-6.sm\\:grid-cols-2 {
        grid-template-columns: 1fr;
    }

    .flex.space-x-3 {
        flex-direction: column;
        gap: 0.75rem;
    }

    .flex.space-x-3 > * + * {
        margin-left: 0;
    }

    .px-6 {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}
</style>
@endpush

@endsection
