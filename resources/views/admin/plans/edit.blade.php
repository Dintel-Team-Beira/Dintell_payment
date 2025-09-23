@extends('layouts.admin')

@section('title', 'Editar Plano - ' . $plan->name)

@section('content')
<div class="container px-6 py-8 mx-auto">
    <!-- Header -->
    <div class="mb-8">
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
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <a href="{{ route('admin.plans.show', $plan) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">{{ $plan->name }}</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Editar</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Editar Plano</h1>
                <p class="mt-2 text-gray-600">Atualize as informações do plano de subscrição</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.plans.show', $plan) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Visualizar
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.plans.update', $plan) }}" method="POST" id="planForm" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Main Content -->
            <div class="space-y-8 lg:col-span-2">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Informações Básicas</h3>
                                <p class="text-sm text-gray-600">Dados principais do plano</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-700">Nome do Plano *</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $plan->name) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-300 @enderror"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="slug" class="block mb-2 text-sm font-medium text-gray-700">Slug</label>
                                <input type="text" name="slug" id="slug" value="{{ old('slug', $plan->slug) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-300 @enderror">
                                <p class="mt-1 text-xs text-gray-500">Deixe em branco para gerar automaticamente</p>
                                @error('slug')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block mb-2 text-sm font-medium text-gray-700">Descrição</label>
                            <textarea name="description" id="description" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-300 @enderror"
                                      placeholder="Breve descrição do plano...">{{ old('description', $plan->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div>
                                <label for="icon" class="block mb-2 text-sm font-medium text-gray-700">Ícone</label>
                                <select name="icon" id="icon" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @foreach($icons ?? ['star', 'rocket', 'building', 'shield', 'crown', 'diamond'] as $iconName)
                                        <option value="{{ $iconName }}" {{ old('icon', $plan->icon) == $iconName ? 'selected' : '' }}>
                                            {{ ucfirst($iconName) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="color" class="block mb-2 text-sm font-medium text-gray-700">Cor do Tema</label>
                                <div class="flex items-center space-x-2">
                                    <input type="color" name="color" id="color" value="{{ old('color', $plan->color ?? '#3B82F6') }}"
                                           class="w-12 h-12 border border-gray-300 rounded-lg cursor-pointer">
                                    <input type="text" value="{{ old('color', $plan->color ?? '#3B82F6') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           readonly id="colorText">
                                </div>
                            </div>

                            <div>
                                <label for="sort_order" class="block mb-2 text-sm font-medium text-gray-700">Ordem de Exibição</label>
                                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $plan->sort_order ?? 0) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Information -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Informações de Preços</h3>
                                <p class="text-sm text-gray-600">Configure os valores e ciclo de cobrança</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div>
                                <label for="price" class="block mb-2 text-sm font-medium text-gray-700">Preço *</label>
                                <div class="relative">
                                    <input type="number" name="price" id="price" step="0.01" min="0"
                                           value="{{ old('price', $plan->price) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('price') border-red-300 @enderror"
                                           required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-sm text-gray-500">MT</span>
                                    </div>
                                </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="setup_fee" class="block mb-2 text-sm font-medium text-gray-700">Taxa de Configuração</label>
                                <div class="relative">
                                    <input type="number" name="setup_fee" id="setup_fee" step="0.01" min="0"
                                           value="{{ old('setup_fee', $plan->setup_fee ?? 0) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-sm text-gray-500">MT</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="billing_cycle" class="block mb-2 text-sm font-medium text-gray-700">Ciclo de Cobrança *</label>
                                <select name="billing_cycle" id="billing_cycle"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('billing_cycle') border-red-300 @enderror"
                                        required>
                                    @foreach($billingCycles ?? ['monthly' => 'Mensal', 'quarterly' => 'Trimestral', 'annually' => 'Anual', 'lifetime' => 'Vitalício'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('billing_cycle', $plan->billing_cycle) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('billing_cycle')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Trial Configuration -->
                        <div class="pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Período de Teste</h4>
                                    <p class="text-sm text-gray-500">Permitir que usuários testem o plano gratuitamente</p>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="has_trial" id="has_trial" value="1"
                                           {{ old('has_trial', $plan->has_trial) ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                </div>
                            </div>

                            <div id="trialDaysContainer" class="{{ old('has_trial', $plan->has_trial) ? '' : 'hidden' }}">
                                <label for="trial_days" class="block mb-2 text-sm font-medium text-gray-700">Dias de Teste</label>
                                <input type="number" name="trial_days" id="trial_days" min="1" max="365"
                                       value="{{ old('trial_days', $plan->trial_days ?? 7) }}"
                                       class="w-32 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-2 mr-3 bg-purple-100 rounded-lg">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Funcionalidades</h3>
                                    <p class="text-sm text-gray-600">Recursos incluídos no plano</p>
                                </div>
                            </div>
                            <button type="button" id="addFeatureBtn"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-purple-700 border border-purple-200 rounded-md bg-purple-50 hover:bg-purple-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Adicionar
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div id="featuresContainer" class="space-y-3">
                            @if(old('features', $plan->features ?? []))
                                @foreach(old('features', $plan->features ?? []) as $index => $feature)
                                <div class="flex items-center space-x-3 feature-item">
                                    <input type="text" name="features[]" value="{{ $feature }}"
                                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                           placeholder="Digite uma funcionalidade...">
                                    <button type="button" class="p-2 text-red-600 remove-feature hover:text-red-800 focus:outline-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                                @endforeach
                            @else
                                <div class="flex items-center space-x-3 feature-item">
                                    <input type="text" name="features[]" value=""
                                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                           placeholder="Digite uma funcionalidade...">
                                    <button type="button" class="p-2 text-red-600 remove-feature hover:text-red-800 focus:outline-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Limitations -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-2 mr-3 bg-red-100 rounded-lg">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Limitações</h3>
                                    <p class="text-sm text-gray-600">Restrições do plano (opcional)</p>
                                </div>
                            </div>
                            <button type="button" id="addLimitationBtn"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-700 border border-red-200 rounded-md bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Adicionar
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div id="limitationsContainer" class="space-y-3">
                            @if(old('limitations', $plan->limitations ?? []))
                                @foreach(old('limitations', $plan->limitations ?? []) as $index => $limitation)
                                <div class="flex items-center space-x-3 limitation-item">
                                    <input type="text" name="limitations[]" value="{{ $limitation }}"
                                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                           placeholder="Digite uma limitação...">
                                    <button type="button" class="p-2 text-red-600 remove-limitation hover:text-red-800 focus:outline-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                                @endforeach
                            @else
                                <div class="py-8 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                    </svg>
                                    <p>Nenhuma limitação adicionada</p>
                                    <p class="text-sm">Clique em "Adicionar" para incluir limitações</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Plan Status -->
                <div class="sticky bg-white rounded-lg shadow top-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Status do Plano</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Plano Ativo</label>
                                <p class="text-sm text-gray-500">Disponível para subscrição</p>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Plano Popular</label>
                                <p class="text-sm text-gray-500">Destacar como recomendado</p>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_popular" id="is_popular" value="1"
                                       {{ old('is_popular', $plan->is_popular) ? 'checked' : '' }}
                                       class="w-4 h-4 text-yellow-600 bg-gray-100 border-gray-300 rounded focus:ring-yellow-500 focus:ring-2">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit"
                                    class="inline-flex items-center justify-center w-full px-6 py-3 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Atualizar Plano
                            </button>

                            <a href="{{ route('admin.plans.show', $plan) }}"
                               class="inline-flex items-center justify-center w-full px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancelar
                            </a>
                        </div>

                        <div class="pt-6 mt-6 border-t border-gray-200">
                            <div class="flex items-center justify-center text-xs text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Campos marcados com * são obrigatórios
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Plan Preview -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Pré-visualização</h3>
                    </div>
                    <div class="p-6">
                        <div id="planPreview" class="p-6 text-center border-2 border-gray-200 border-dashed rounded-lg">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 text-white rounded-lg"
                                 style="background-color: {{ $plan->color ?? '#3B82F6' }};" id="previewIcon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                            </div>
                            <h4 id="previewName" class="mb-2 text-lg font-semibold text-gray-900">{{ $plan->name }}</h4>
                            <p id="previewDescription" class="mb-4 text-sm text-gray-600">{{ $plan->description }}</p>
                            <div class="text-2xl font-bold text-gray-900">
                                <span id="previewPrice">{{ number_format($plan->price, 2) }}</span> MT
                            </div>
                            <p class="text-sm text-gray-500">Por <span id="previewBilling">{{ $plan->billing_cycle }}</span></p>

                            <div class="mt-4 space-y-1">
                                <span id="previewActive" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $plan->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                                <span id="previewPopular" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 {{ $plan->is_popular ? '' : 'hidden' }}">
                                    Popular
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    nameInput.addEventListener('input', function() {
        if (!slugInput.dataset.manual) {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-|-$/g, '');
            slugInput.value = slug;
        }
        updatePreview();
    });

    slugInput.addEventListener('input', function() {
        this.dataset.manual = 'true';
    });

    // Color picker sync
    const colorInput = document.getElementById('color');
    const colorText = document.getElementById('colorText');

    colorInput.addEventListener('input', function() {
        colorText.value = this.value;
        updatePreview();
    });

    // Trial period toggle
    const hasTrialCheckbox = document.getElementById('has_trial');
    const trialDaysContainer = document.getElementById('trialDaysContainer');

    hasTrialCheckbox.addEventListener('change', function() {
        if (this.checked) {
            trialDaysContainer.classList.remove('hidden');
        } else {
            trialDaysContainer.classList.add('hidden');
        }
    });

    // Features management
    const featuresContainer = document.getElementById('featuresContainer');
    const addFeatureBtn = document.getElementById('addFeatureBtn');

    addFeatureBtn.addEventListener('click', function() {
        addFeatureField();
    });

    function addFeatureField(value = '') {
        const featureItem = document.createElement('div');
        featureItem.className = 'feature-item flex items-center space-x-3';
        featureItem.innerHTML = `
            <input type="text" name="features[]" value="${value}"
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="Digite uma funcionalidade...">
            <button type="button" class="p-2 text-red-600 remove-feature hover:text-red-800 focus:outline-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        `;

        featuresContainer.appendChild(featureItem);
        attachFeatureListeners(featureItem);
    }

    function attachFeatureListeners(featureItem) {
        const removeBtn = featureItem.querySelector('.remove-feature');
        removeBtn.addEventListener('click', function() {
            featureItem.remove();
            checkEmptyFeatures();
        });
    }

    function checkEmptyFeatures() {
        const featureItems = featuresContainer.querySelectorAll('.feature-item');
        if (featureItems.length === 0) {
            const emptyState = document.createElement('div');
            emptyState.className = 'text-center py-8 text-gray-500';
            emptyState.innerHTML = `
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>Nenhuma funcionalidade adicionada</p>
                <p class="text-sm">Clique em "Adicionar" para incluir funcionalidades</p>
            `;
            featuresContainer.appendChild(emptyState);
        }
    }

    // Limitations management
    const limitationsContainer = document.getElementById('limitationsContainer');
    const addLimitationBtn = document.getElementById('addLimitationBtn');

    addLimitationBtn.addEventListener('click', function() {
        addLimitationField();
    });

    function addLimitationField(value = '') {
        // Remove empty state if exists
        const emptyState = limitationsContainer.querySelector('.text-center');
        if (emptyState) {
            emptyState.remove();
        }

        const limitationItem = document.createElement('div');
        limitationItem.className = 'limitation-item flex items-center space-x-3';
        limitationItem.innerHTML = `
            <input type="text" name="limitations[]" value="${value}"
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                   placeholder="Digite uma limitação...">
            <button type="button" class="p-2 text-red-600 remove-limitation hover:text-red-800 focus:outline-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        `;

        limitationsContainer.appendChild(limitationItem);
        attachLimitationListeners(limitationItem);
    }

    function attachLimitationListeners(limitationItem) {
        const removeBtn = limitationItem.querySelector('.remove-limitation');
        removeBtn.addEventListener('click', function() {
            limitationItem.remove();
            checkEmptyLimitations();
        });
    }

    function checkEmptyLimitations() {
        const limitationItems = limitationsContainer.querySelectorAll('.limitation-item');
        if (limitationItems.length === 0) {
            const emptyState = document.createElement('div');
            emptyState.className = 'text-center py-8 text-gray-500';
            emptyState.innerHTML = `
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                </svg>
                <p>Nenhuma limitação adicionada</p>
                <p class="text-sm">Clique em "Adicionar" para incluir limitações</p>
            `;
            limitationsContainer.appendChild(emptyState);
        }
    }

    // Attach listeners to existing items
    document.querySelectorAll('.feature-item').forEach(attachFeatureListeners);
    document.querySelectorAll('.limitation-item').forEach(attachLimitationListeners);

    // Preview updates
    function updatePreview() {
        const name = document.getElementById('name').value || 'Nome do Plano';
        const description = document.getElementById('description').value || 'Descrição do plano';
        const price = document.getElementById('price').value || '0.00';
        const billingCycle = document.getElementById('billing_cycle').value || 'monthly';
        const color = document.getElementById('color').value || '#3B82F6';
        const isActive = document.getElementById('is_active').checked;
        const isPopular = document.getElementById('is_popular').checked;

        // Update preview elements
        document.getElementById('previewName').textContent = name;
        document.getElementById('previewDescription').textContent = description;
        document.getElementById('previewPrice').textContent = parseFloat(price).toFixed(2);
        document.getElementById('previewBilling').textContent = billingCycle;
        document.getElementById('previewIcon').style.backgroundColor = color;

        // Update status badges
        const activeSpan = document.getElementById('previewActive');
        activeSpan.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
        activeSpan.textContent = isActive ? 'Ativo' : 'Inativo';

        const popularSpan = document.getElementById('previewPopular');
        if (isPopular) {
            popularSpan.classList.remove('hidden');
        } else {
            popularSpan.classList.add('hidden');
        }
    }

    // Add event listeners for preview updates
    ['name', 'description', 'price', 'billing_cycle', 'color', 'is_active', 'is_popular'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('input', updatePreview);
            element.addEventListener('change', updatePreview);
        }
    });

    // Form validation
    document.getElementById('planForm').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const price = document.getElementById('price').value;

        if (!name) {
            e.preventDefault();
            showNotification('O nome do plano é obrigatório', 'error');
            document.getElementById('name').focus();
            return;
        }

        if (!price || parseFloat(price) < 0) {
            e.preventDefault();
            showNotification('O preço deve ser um valor válido', 'error');
            document.getElementById('price').focus();
            return;
        }

        // Show loading state
        const submitButton = e.target.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Atualizando...';
        submitButton.disabled = true;
    });

    // Notification system
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300`;

        const colors = {
            success: 'bg-green-50 border border-green-200 text-green-800',
            error: 'bg-red-50 border border-red-200 text-red-800',
            info: 'bg-blue-50 border border-blue-200 text-blue-800',
            warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800'
        };

        notification.className += ` ${colors[type]}`;
        notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-3">
                    <button class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none" onclick="this.closest('div').remove()">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // Initialize preview
    updatePreview();
});
</script>
@endpush

@push('styles')
<style>
/* Loading animation */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Form input focus effects */
input:focus, select:focus, textarea:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Button hover effects */
button:hover:not(:disabled), .btn:hover {
    transform: translateY(-1px);
}

button:disabled {
    transform: none;
    opacity: 0.6;
    cursor: not-allowed;
}

/* Card hover effects */
.bg-white.shadow:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Color picker styling */
input[type="color"] {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-color: transparent;
    border: none;
    cursor: pointer;
}

input[type="color"]::-webkit-color-swatch-wrapper {
    padding: 0;
    border: none;
    border-radius: 0.5rem;
}

input[type="color"]::-webkit-color-swatch {
    border: none;
    border-radius: 0.5rem;
}

/* Checkbox styling */
input[type="checkbox"]:checked {
    background-color: currentColor;
    border-color: transparent;
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m13.854 3.646-7.5 7.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6 10.293l7.146-7.147a.5.5 0 0 1 .708.708z'/%3e%3c/svg%3e");
}

/* Feature and limitation item animations */
.feature-item, .limitation-item {
    animation: slideInDown 0.3s ease-out;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Sticky sidebar */
.sticky {
    position: sticky;
    top: 2rem;
}

/* Preview card styling */
#planPreview {
    transition: all 0.3s ease;
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Responsive improvements */
@media (max-width: 640px) {
    .grid.grid-cols-1.gap-6.md\\:grid-cols-2 {
        grid-template-columns: 1fr;
    }

    .grid.grid-cols-1.gap-6.md\\:grid-cols-3 {
        grid-template-columns: 1fr;
    }

    .flex.space-x-3 {
        flex-direction: column;
        gap: 0.75rem;
    }

    .flex.space-x-3 > * + * {
        margin-left: 0;
    }

    .sticky {
        position: static;
    }
}

/* Form validation styling */
.border-red-300 {
    border-color: #fca5a5;
}

.text-red-600 {
    color: #dc2626;
}

/* Success/error states */
.border-green-300 {
    border-color: #86efac;
}

.text-green-600 {
    color: #16a34a;
}
</style>
@endpush

@endsection
