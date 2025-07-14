@extends('layouts.app')

@section('title', 'Editar Plano - ' . $plan->name)

@section('header-actions')
<div class="flex items-center gap-x-3">
    <a href="{{ route('plans.index') }}"
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-all bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Voltar aos Planos
    </a>

    <a href="{{ route('plans.show', $plan) }}"
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 transition-all border border-blue-200 rounded-lg bg-blue-50 hover:bg-blue-100">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        Visualizar
    </a>
</div>
@endsection

@section('content')
<div class="mx-auto max-w-8xl">
    <!-- Header com informações do plano -->
    <div class="mb-8 bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center justify-center w-12 h-12 rounded-lg"
                         style="background-color: {{ $plan->color_theme ?? '#3B82F6' }}20; color: {{ $plan->color_theme ?? '#3B82F6' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Editando: {{ $plan->name }}</h1>
                        <p class="text-gray-600">MT {{ number_format($plan->price, 2) }} / {{ $plan->billing_cycle }}</p>
                        <div class="flex items-center mt-2 space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $plan->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $plan->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                            @if($plan->is_featured)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                ⭐ Destaque
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="text-sm text-right text-gray-500">
                    <div>Criado: {{ $plan->created_at->format('d/m/Y') }}</div>
                    <div>Atualizado: {{ $plan->updated_at->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('plans.update', $plan) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Informações Básicas -->
        <div class="p-6 bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl">
            <div class="flex items-center mb-6">
                <div class="flex items-center justify-center w-8 h-8 mr-3 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Informações do Plano</h2>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Nome do Plano *</label>
                    <input type="text" name="name" value="{{ old('name', $plan->name) }}" required
                           class="block w-full transition-colors border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('name')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Slug *</label>
                    <input type="text" name="slug" value="{{ old('slug', $plan->slug) }}" required
                           class="block w-full transition-colors border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('slug')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-gray-500">URL amigável para o plano (sem espaços ou caracteres especiais)</p>
                </div>

                <div class="lg:col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Descrição</label>
                    <textarea name="description" rows="4"
                              class="block w-full p-2 transition-colors border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Descreva os benefícios e características deste plano...">{{ old('description', $plan->description) }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Preços e Cobrança -->
        <div class="p-6 bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl">
            <div class="flex items-center mb-6">
                <div class="flex items-center justify-center w-8 h-8 mr-3 rounded-lg bg-emerald-100">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Preços e Cobrança</h2>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Preço (MT) *</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">MT</span>
                        </div>
                        <input type="number" step="0.01" name="price" value="{{ old('price', $plan->price) }}" required
                               class="block w-full pl-12 transition-colors border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    @error('price')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Taxa de Instalação (MT)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">MT</span>
                        </div>
                        <input type="number" step="0.01" name="setup_fee" value="{{ old('setup_fee', $plan->setup_fee ?? 0) }}"
                               class="block w-full pl-12 transition-colors border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    @error('setup_fee')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Ciclo de Cobrança *</label>
                    <select name="billing_cycle" required
                            class="block w-full p-2 transition-colors border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="monthly" {{ old('billing_cycle', $plan->billing_cycle) === 'monthly' ? 'selected' : '' }}>Mensal</option>
                        <option value="quarterly" {{ old('billing_cycle', $plan->billing_cycle) === 'quarterly' ? 'selected' : '' }}>Trimestral</option>
                        <option value="yearly" {{ old('billing_cycle', $plan->billing_cycle) === 'yearly' ? 'selected' : '' }}>Anual</option>
                        <option value="lifetime" {{ old('billing_cycle', $plan->billing_cycle) === 'lifetime' ? 'selected' : '' }}>Vitalício</option>
                    </select>
                    @error('billing_cycle')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Dias do Ciclo *</label>
                    <input type="number" name="billing_cycle_days" value="{{ old('billing_cycle_days', $plan->billing_cycle_days) }}" required
                           class="block w-full transition-colors border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('billing_cycle_days')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-gray-500">Número de dias entre cada cobrança</p>
                </div>
            </div>
        </div>

        <!-- Limites e Recursos -->
        <div class="p-6 bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl">
            <div class="flex items-center mb-6">
                <div class="flex items-center justify-center w-8 h-8 mr-3 bg-purple-100 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Limites e Recursos</h2>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Máx. Domínios *</label>
                    <input type="number" name="max_domains" value="{{ old('max_domains', $plan->max_domains) }}" required min="1"
                           class="block w-full transition-colors border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('max_domains')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Storage (GB) *</label>
                    <input type="number" name="max_storage_gb" value="{{ old('max_storage_gb', $plan->max_storage_gb) }}" required min="1"
                           class="block w-full transition-colors border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('max_storage_gb')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Bandwidth (GB) *</label>
                    <input type="number" name="max_bandwidth_gb" value="{{ old('max_bandwidth_gb', $plan->max_bandwidth_gb) }}" required min="1"
                           class="block w-full transition-colors border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('max_bandwidth_gb')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Dias de Trial</label>
                    <input type="number" name="trial_days" value="{{ old('trial_days', $plan->trial_days ?? 0) }}" min="0"
                           class="block w-full transition-colors border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('trial_days')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-gray-500">0 = sem trial</p>
                </div>
            </div>
        </div>

        <!-- Funcionalidades -->
        <div class="p-6 bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl">
            <div class="flex items-center mb-6">
                <div class="flex items-center justify-center w-8 h-8 mr-3 rounded-lg bg-amber-100">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Funcionalidades Incluídas</h2>
            </div>

            <div id="features-container" class="space-y-3">
                @php
                    $features = old('features', is_array($plan->features) ? $plan->features : ($plan->features ? json_decode($plan->features, true) : []));
                @endphp

                @if(empty($features))
                    <div class="flex items-center space-x-3 feature-item">
                        <div class="flex-1">
                            <input type="text" name="features[]" placeholder="Ex: Suporte 24/7"
                                   class="block w-full transition-colors border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <button type="button" onclick="removeFeature(this)"
                                class="p-2 text-red-600 transition-colors rounded-lg hover:text-red-800 hover:bg-red-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                @else
                    @foreach($features as $feature)
                    <div class="flex items-center space-x-3 feature-item">
                        <div class="flex-1">
                            <input type="text" name="features[]" value="{{ $feature }}" placeholder="Ex: Suporte 24/7"
                                   class="block w-full transition-colors border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <button type="button" onclick="removeFeature(this)"
                                class="p-2 text-red-600 transition-colors rounded-lg hover:text-red-800 hover:bg-red-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                    @endforeach
                @endif
            </div>

            <button type="button" onclick="addFeature()"
                    class="inline-flex items-center px-3 py-2 mt-4 text-sm font-medium text-blue-700 transition-colors border border-blue-200 rounded-lg bg-blue-50 hover:bg-blue-100">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Adicionar Funcionalidade
            </button>
        </div>

        <!-- Configurações -->
        <div class="p-6 bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl">
            <div class="flex items-center mb-6">
                <div class="flex items-center justify-center w-8 h-8 mr-3 bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Configurações Avançadas</h2>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="space-y-4">
                        <div class="flex items-center p-4 rounded-lg bg-gray-50">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <div class="ml-3">
                                <label class="text-sm font-medium text-gray-900">Plano Ativo</label>
                                <p class="text-xs text-gray-600">Permite que clientes assinem este plano</p>
                            </div>
                        </div>

                        <div class="flex items-center p-4 rounded-lg bg-gray-50">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $plan->is_featured) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <div class="ml-3">
                                <label class="text-sm font-medium text-gray-900">Plano em Destaque</label>
                                <p class="text-xs text-gray-600">Destacar na página de preços</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Cor do Tema</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" name="color_theme" value="{{ old('color_theme', $plan->color_theme ?? '#3B82F6') }}"
                                   class="w-20 h-12 border border-gray-300 rounded-lg cursor-pointer">
                            <div class="flex-1">
                                <input type="text" value="{{ old('color_theme', $plan->color_theme ?? '#3B82F6') }}" readonly
                                       class="block w-full text-sm border-gray-300 rounded-lg bg-gray-50">
                                <p class="mt-1 text-xs text-gray-500">Cor usada nos elementos visuais do plano</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <div class="flex items-center space-x-4">
                <a href="{{ route('plans.index') }}"
                   class="px-6 py-3 text-sm font-medium text-gray-700 transition-all bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Cancelar
                </a>

                <a href="{{ route('plans.show', $plan) }}"
                   class="px-6 py-3 text-sm font-medium text-blue-700 transition-all border border-blue-200 rounded-lg bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Visualizar
                </a>
            </div>

            <button type="submit"
                    class="px-8 py-3 text-sm font-medium text-white transition-all bg-blue-600 border border-transparent rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Atualizar Plano
            </button>
        </div>
    </form>
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

@if($errors->any())
<div id="errorMessage" class="fixed z-50 max-w-md p-4 text-red-700 bg-red-100 border border-red-400 rounded-lg shadow-lg top-4 right-4">
    <div class="flex items-start">
        <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <div class="font-medium">Erro ao salvar:</div>
            <ul class="mt-1 text-sm list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<script>
// Features management
function addFeature() {
    const container = document.getElementById('features-container');
    const newFeature = document.createElement('div');
    newFeature.className = 'feature-item flex items-center space-x-3';
    newFeature.innerHTML = `
        <div class="flex-1">
            <input type="text" name="features[]" placeholder="Ex: Suporte 24/7"
                   class="block w-full transition-colors border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <button type="button" onclick="removeFeature(this)"
                class="p-2 text-red-600 transition-colors rounded-lg hover:text-red-800 hover:bg-red-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    `;
    container.appendChild(newFeature);

    // Focus on the new input
    const newInput = newFeature.querySelector('input');
    newInput.focus();
}

function removeFeature(button) {
    const container = document.getElementById('features-container');
    if (container.children.length > 1) {
        button.closest('.feature-item').remove();
    }
}

// Auto-generate slug from name
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.querySelector('input[name="name"]');
    const slugInput = document.querySelector('input[name="slug"]');
    const originalSlug = slugInput.value;

    nameInput.addEventListener('input', function() {
        // Only auto-generate if slug is empty or matches the original
        if (!slugInput.value || slugInput.value === originalSlug) {
            const slug = this.value.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
                .replace(/\s+/g, '-') // Replace spaces with hyphens
                .replace(/-+/g, '-') // Replace multiple hyphens with single
                .trim('-'); // Remove leading/trailing hyphens
            slugInput.value = slug;
        }
    });
});

// Color theme preview
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.querySelector('input[name="color_theme"]');
    const colorText = document.querySelector('input[type="text"][readonly]');
    const headerIcon = document.querySelector('.w-12.h-12');

    colorInput.addEventListener('change', function() {
        colorText.value = this.value;
        if (headerIcon) {
            headerIcon.style.backgroundColor = this.value + '20';
            headerIcon.style.color = this.value;
        }
    });
});

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    form.addEventListener('submit', function(e) {
        // Prevent double submission
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="inline w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Atualizando...
        `;

        // Re-enable after 10 seconds in case of network issues
        setTimeout(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }, 10000);
    });
});

// Billing cycle auto-calculation
document.addEventListener('DOMContentLoaded', function() {
    const billingCycleSelect = document.querySelector('select[name="billing_cycle"]');
    const billingCycleDaysInput = document.querySelector('input[name="billing_cycle_days"]');

    billingCycleSelect.addEventListener('change', function() {
        const cycleMappings = {
            'monthly': 30,
            'quarterly': 90,
            'yearly': 365,
            'lifetime': 36500 // ~100 years
        };

        if (cycleMappings[this.value]) {
            billingCycleDaysInput.value = cycleMappings[this.value];
        }
    });
});

// Auto-hide flash messages
document.addEventListener('DOMContentLoaded', function() {
    const messages = ['successMessage', 'errorMessage'];

    messages.forEach(messageId => {
        const element = document.getElementById(messageId);
        if (element) {
            // Auto-hide after 5 seconds
            setTimeout(() => {
                element.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
                element.style.opacity = '0';
                element.style.transform = 'translateX(100%)';
                setTimeout(() => element.remove(), 500);
            }, 5000);

            // Add close button
            const closeBtn = document.createElement('button');
            closeBtn.innerHTML = '×';
            closeBtn.className = 'ml-2 text-lg font-bold opacity-70 hover:opacity-100';
            closeBtn.onclick = () => element.remove();
            element.querySelector('div').appendChild(closeBtn);
        }
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + S to save
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        document.querySelector('form').submit();
    }

    // Escape to cancel
    if (e.key === 'Escape') {
        window.location.href = '{{ route('plans.index') }}';
    }

    // Ctrl/Cmd + Enter to add feature
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        addFeature();
    }
});

// Enhanced UX features
document.addEventListener('DOMContentLoaded', function() {
    // Add tooltips to important fields
    const tooltips = {
        'input[name="slug"]': 'URL amigável sem espaços ou caracteres especiais',
        'input[name="billing_cycle_days"]': 'Número de dias entre cada cobrança recorrente',
        'input[name="trial_days"]': 'Período gratuito antes da primeira cobrança',
        'input[name="max_domains"]': 'Quantidade máxima de domínios permitidos',
        'input[name="setup_fee"]': 'Taxa única cobrada na ativação (opcional)'
    };

    Object.entries(tooltips).forEach(([selector, text]) => {
        const element = document.querySelector(selector);
        if (element) {
            element.title = text;
        }
    });

    // Highlight changed fields
    const inputs = document.querySelectorAll('input, textarea, select');
    const originalValues = new Map();

    // Store original values
    inputs.forEach(input => {
        originalValues.set(input, input.value);
    });

    // Track changes
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value !== originalValues.get(this)) {
                this.classList.add('border-amber-300', 'bg-amber-50');
            } else {
                this.classList.remove('border-amber-300', 'bg-amber-50');
            }
        });
    });
});

// Price formatting
document.addEventListener('DOMContentLoaded', function() {
    const priceInputs = document.querySelectorAll('input[name="price"], input[name="setup_fee"]');

    priceInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value) {
                const value = parseFloat(this.value);
                if (!isNaN(value)) {
                    this.value = value.toFixed(2);
                }
            }
        });
    });
});
</script>

<style>
/* Enhanced styling */
.feature-item {
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Form validation styles */
.invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 1px #ef4444 !important;
}

.valid {
    border-color: #10b981 !important;
    box-shadow: 0 0 0 1px #10b981 !important;
}

/* Loading state */
.loading {
    opacity: 0.7;
    pointer-events: none;
}

/* Smooth transitions */
input, textarea, select {
    transition: all 0.2s ease-in-out;
}

input:focus, textarea:focus, select:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

/* Button hover effects */
button:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Print styles */
@media print {
    .no-print, button, .fixed {
        display: none !important;
    }

    .bg-white {
        background: white !important;
    }

    .shadow-sm {
        box-shadow: none !important;
    }
}

/* Mobile responsive adjustments */
@media (max-width: 640px) {
    .lg\:grid-cols-2 {
        grid-template-columns: 1fr;
    }

    .lg\:grid-cols-4 {
        grid-template-columns: repeat(2, 1fr);
    }

    .space-x-3 > * + * {
        margin-left: 0.5rem;
    }

    .px-6 {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}
</style>
@endsection
