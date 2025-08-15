@extends('layouts.app')

@section('title', 'Editar Serviço - ' . $service->name)

@section('content')
<div class="container px-4">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Editar Serviço</h1>
                <p class="mt-2 text-gray-600">Atualize as informações do serviço "{{ $service->name }}"</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('services.show', $service) }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 border border-green-200 rounded-md bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Visualizar
                </a>
                <a href="{{ route('services.index') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar à Lista
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('services.update', $service) }}" method="POST" id="serviceForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-8 xl:grid-cols-3">
            <!-- Coluna Principal -->
            <div class="space-y-8 xl:col-span-2">
                <!-- Informações Básicas -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Informações Básicas</h3>
                                <p class="text-sm text-gray-600">Dados principais do serviço</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-700">
                                    Nome do Serviço *
                                </label>
                                <input type="text" name="name" id="name" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('name') border-red-300 @enderror"
                                       value="{{ old('name', $service->name) }}"
                                       placeholder="Digite o nome do serviço">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="code" class="block mb-2 text-sm font-medium text-gray-700">
                                    Código do Serviço
                                </label>
                                <input type="text" name="code" id="code"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('code') border-red-300 @enderror"
                                       value="{{ old('code', $service->code) }}"
                                       placeholder="Código único (gerado automaticamente se vazio)">
                                @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Deixe vazio para gerar automaticamente</p>
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block mb-2 text-sm font-medium text-gray-700">
                                Descrição
                            </label>
                            <textarea name="description" id="description" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('description') border-red-300 @enderror"
                                      placeholder="Descrição detalhada do serviço">{{ old('description', $service->description) }}</textarea>
                            @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="category" class="block mb-2 text-sm font-medium text-gray-700">
                                    Categoria *
                                </label>
                                <select name="category" id="category" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('category') border-red-300 @enderror">
                                    <option value="">Selecione uma categoria</option>
                                    @foreach(\App\Models\Service::getCategories() as $key => $label)
                                        <option value="{{ $key }}" {{ old('category', $service->category) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="complexity_level" class="block mb-2 text-sm font-medium text-gray-700">
                                    Nível de Complexidade
                                </label>
                                <select name="complexity_level" id="complexity_level"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('complexity_level') border-red-300 @enderror">
                                    <option value="">Selecione o nível</option>
                                    <option value="basic" {{ old('complexity_level', $service->complexity_level) == 'basic' ? 'selected' : '' }}>Básico</option>
                                    <option value="intermediate" {{ old('complexity_level', $service->complexity_level) == 'intermediate' ? 'selected' : '' }}>Intermediário</option>
                                    <option value="advanced" {{ old('complexity_level', $service->complexity_level) == 'advanced' ? 'selected' : '' }}>Avançado</option>
                                    <option value="expert" {{ old('complexity_level', $service->complexity_level) == 'expert' ? 'selected' : '' }}>Especialista</option>
                                </select>
                                @error('complexity_level')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preços e Tempo -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Preços e Tempo</h3>
                                <p class="text-sm text-gray-600">Configurações de valores e estimativas</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Tipo de Preço -->
                        <div>
                            <label class="block mb-3 text-sm font-medium text-gray-700">Tipo de Preço</label>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <label class="relative flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="pricing_type" value="fixed" class="text-green-600 focus:ring-green-500"
                                           {{ old('pricing_type', $service->fixed_price > 0 ? 'fixed' : 'hourly') == 'fixed' ? 'checked' : '' }}>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Preço Fixo</div>
                                        <div class="text-xs text-gray-500">Valor único para todo o serviço</div>
                                    </div>
                                </label>
                                <label class="relative flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="pricing_type" value="hourly" class="text-green-600 focus:ring-green-500"
                                           {{ old('pricing_type', $service->fixed_price > 0 ? 'fixed' : 'hourly') == 'hourly' ? 'checked' : '' }}>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Por Hora</div>
                                        <div class="text-xs text-gray-500">Valor calculado por hora trabalhada</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div id="fixed_price_field">
                                <label for="fixed_price" class="block mb-2 text-sm font-medium text-gray-700">
                                    Preço Fixo
                                </label>
                                <div class="relative">
                                    <span class="absolute text-gray-500 transform -translate-y-1/2 left-3 top-1/2">MT</span>
                                    <input type="number" name="fixed_price" id="fixed_price" step="0.01" min="0"
                                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('fixed_price') border-red-300 @enderror"
                                           value="{{ old('fixed_price', $service->fixed_price) }}"
                                           placeholder="0.00">
                                </div>
                                @error('fixed_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div id="hourly_rate_field">
                                <label for="hourly_rate" class="block mb-2 text-sm font-medium text-gray-700">
                                    Valor por Hora
                                </label>
                                <div class="relative">
                                    <span class="absolute text-gray-500 transform -translate-y-1/2 left-3 top-1/2">MT</span>
                                    <input type="number" name="hourly_rate" id="hourly_rate" step="0.01" min="0"
                                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('hourly_rate') border-red-300 @enderror"
                                           value="{{ old('hourly_rate', $service->hourly_rate) }}"
                                           placeholder="0.00">
                                </div>
                                @error('hourly_rate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="estimated_hours" class="block mb-2 text-sm font-medium text-gray-700">
                                    Horas Estimadas
                                </label>
                                <input type="number" name="estimated_hours" id="estimated_hours" step="0.5" min="0"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('estimated_hours') border-red-300 @enderror"
                                       value="{{ old('estimated_hours', $service->estimated_hours) }}"
                                       placeholder="0">
                                @error('estimated_hours')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="tax_rate" class="block mb-2 text-sm font-medium text-gray-700">
                                Taxa de IVA (%)
                            </label>
                            <input type="number" name="tax_rate" id="tax_rate" step="0.01" min="0" max="100"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('tax_rate') border-red-300 @enderror"
                                   value="{{ old('tax_rate', $service->tax_rate ?? 16) }}"
                                   placeholder="16.00">
                            @error('tax_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Calculadora de Valor -->
                        <div class="p-4 rounded-lg bg-gray-50">
                            <h4 class="mb-3 text-sm font-medium text-gray-700">Estimativa de Valor</h4>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-green-600" id="total_estimate">0.00 MT</div>
                                    <div class="text-xs text-gray-500">Valor Total Estimado</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-blue-600" id="effective_hourly">0.00 MT/h</div>
                                    <div class="text-xs text-gray-500">Valor Efetivo/Hora</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-purple-600" id="tax_amount">0.00 MT</div>
                                    <div class="text-xs text-gray-500">IVA</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requisitos e Entregáveis -->
   <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center">
            <div class="p-2 mr-3 bg-orange-100 rounded-lg">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Requisitos e Entregáveis</h3>
                <p class="text-sm text-gray-600">Especificações técnicas e resultados esperados</p>
            </div>
        </div>
    </div>
    <div class="p-6 space-y-6">
        <div>
            <label for="requirements" class="block mb-2 text-sm font-medium text-gray-700">
                Requisitos
            </label>
            <textarea name="requirements" id="requirements" rows="4"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('requirements') border-red-300 @enderror"
                      placeholder="Liste os requisitos necessários para este serviço...">{{ old('requirements', is_string($service->requirements) ? $service->requirements : '') }}</textarea>
            @error('requirements')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="deliverables" class="block mb-2 text-sm font-medium text-gray-700">
                Entregáveis
            </label>
            <textarea name="deliverables" id="deliverables" rows="4"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('deliverables') border-red-300 @enderror"
                      placeholder="Descreva o que será entregue ao cliente...">{{ old('deliverables', is_string($service->deliverables) ? $service->deliverables : '') }}</textarea>
            @error('deliverables')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>


            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status e Ações -->
                <div class="sticky bg-white border border-gray-200 shadow-sm rounded-xl top-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-indigo-100 rounded-lg">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Status e Ações</h3>
                                <p class="text-sm text-gray-600">Configure o status do serviço</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Status Ativo -->
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Serviço Ativo</label>
                                <p class="text-xs text-gray-500">Serviço disponível para contratação</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                            </label>
                        </div>

                        <!-- Informações do Serviço -->
                        <div class="pt-6 border-t border-gray-200">
                            <h4 class="mb-4 text-sm font-medium text-gray-700">Informações</h4>
                            <div class="space-y-3 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Criado em:</span>
                                    <span class="text-gray-900">{{ $service->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Atualizado em:</span>
                                    <span class="text-gray-900">{{ $service->updated_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @if($service->code)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Código:</span>
                                    <span class="font-mono text-gray-900">{{ $service->code }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="p-6 space-y-3">
                        <button type="submit" class="inline-flex items-center justify-center w-full px-6 py-3 text-sm font-medium text-white transition-colors bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Atualizar Serviço
                        </button>

                        <a href="{{ route('services.show', $service) }}"
                           class="inline-flex items-center justify-center w-full px-6 py-2 text-sm font-medium text-green-700 transition-colors border border-green-200 rounded-md bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Visualizar
                        </a>

                        @if(Route::has('services.duplicate'))
                        <a href="{{ route('services.duplicate', $service) }}"
                           class="inline-flex items-center justify-center w-full px-6 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Duplicar
                        </a>
                        @endif

                        <a href="{{ route('services.index') }}"
                           class="inline-flex items-center justify-center w-full px-6 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
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
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pricingTypeRadios = document.querySelectorAll('input[name="pricing_type"]');
        const fixedPriceField = document.getElementById('fixed_price_field');
        const hourlyRateField = document.getElementById('hourly_rate_field');

        const fixedPriceInput = document.getElementById('fixed_price');
        const hourlyRateInput = document.getElementById('hourly_rate');
        const estimatedHoursInput = document.getElementById('estimated_hours');
        const taxRateInput = document.getElementById('tax_rate');

        const totalEstimateDisplay = document.getElementById('total_estimate');
        const effectiveHourlyDisplay = document.getElementById('effective_hourly');
        const taxAmountDisplay = document.getElementById('tax_amount');

        // Controle de visibilidade dos campos de preço
        function togglePriceFields() {
            const selectedType = document.querySelector('input[name="pricing_type"]:checked').value;

            if (selectedType === 'fixed') {
                fixedPriceField.style.opacity = '1';
                hourlyRateField.style.opacity = '0.5';
                fixedPriceInput.required = true;
                hourlyRateInput.required = false;
            } else {
                fixedPriceField.style.opacity = '0.5';
                hourlyRateField.style.opacity = '1';
                fixedPriceInput.required = false;
                hourlyRateInput.required = true;
            }

            calculateEstimates();
        }

        // Calcular estimativas em tempo real
        function calculateEstimates() {
            const pricingType = document.querySelector('input[name="pricing_type"]:checked').value;
            const fixedPrice = parseFloat(fixedPriceInput.value) || 0;
            const hourlyRate = parseFloat(hourlyRateInput.value) || 0;
            const estimatedHours = parseFloat(estimatedHoursInput.value) || 0;
            const taxRate = parseFloat(taxRateInput.value) || 0;

            let totalEstimate = 0;
            let effectiveHourly = 0;

            if (pricingType === 'fixed' && fixedPrice > 0) {
                totalEstimate = fixedPrice;
                effectiveHourly = estimatedHours > 0 ? fixedPrice / estimatedHours : 0;
            } else if (pricingType === 'hourly' && hourlyRate > 0) {
                totalEstimate = estimatedHours > 0 ? hourlyRate * estimatedHours : hourlyRate;
                effectiveHourly = hourlyRate;
            }

            const taxAmount = totalEstimate * (taxRate / 100);
            const totalWithTax = totalEstimate + taxAmount;

            // Atualizar displays
            totalEstimateDisplay.textContent = formatCurrency(totalWithTax);
            effectiveHourlyDisplay.textContent = formatCurrency(effectiveHourly) + '/h';
            taxAmountDisplay.textContent = formatCurrency(taxAmount);

            // Cores baseadas no valor
            if (totalEstimate < 1000) {
                totalEstimateDisplay.className = 'text-lg font-bold text-red-600';
            } else if (totalEstimate < 5000) {
                totalEstimateDisplay.className = 'text-lg font-bold text-yellow-600';
            } else {
                totalEstimateDisplay.className = 'text-lg font-bold text-green-600';
            }
        }

        // Event listeners
        pricingTypeRadios.forEach(radio => {
            radio.addEventListener('change', togglePriceFields);
        });

        [fixedPriceInput, hourlyRateInput, estimatedHoursInput, taxRateInput].forEach(input => {
            input.addEventListener('input', calculateEstimates);
        });

        // Geração automática de código baseada no nome
        const nameInput = document.getElementById('name');
        const codeInput = document.getElementById('code');

        nameInput.addEventListener('input', function() {
            if (!codeInput.value) {
                const name = this.value.trim();
                if (name) {
                    // Gerar código baseado no nome
                    const code = name
                        .normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '') // Remove acentos
                        .toUpperCase()
                        .replace(/[^A-Z0-9\s]/g, '') // Remove caracteres especiais
                        .replace(/\s+/g, '-') // Substitui espaços por hífens
                        .substring(0, 20); // Limita a 20 caracteres

                    codeInput.value = 'SRV-' + code;
                }
            }
        });

        // Validação do formulário
        const form = document.getElementById('serviceForm');
        form.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const category = document.getElementById('category').value;
            const pricingType = document.querySelector('input[name="pricing_type"]:checked').value;
            const fixedPrice = parseFloat(fixedPriceInput.value);
            const hourlyRate = parseFloat(hourlyRateInput.value);

            let hasErrors = false;

            // Validar campos obrigatórios
            if (!name) {
                showFieldError('name', 'Nome do serviço é obrigatório');
                hasErrors = true;
            }

            if (!category) {
                showFieldError('category', 'Categoria é obrigatória');
                hasErrors = true;
            }

            // Validar preços baseado no tipo selecionado
            if (pricingType === 'fixed' && (!fixedPrice || fixedPrice <= 0)) {
                showFieldError('fixed_price', 'Preço fixo deve ser maior que zero');
                hasErrors = true;
            }

            if (pricingType === 'hourly' && (!hourlyRate || hourlyRate <= 0)) {
                showFieldError('hourly_rate', 'Valor por hora deve ser maior que zero');
                hasErrors = true;
            }

            if (hasErrors) {
                e.preventDefault();
                showNotification('Corrija os erros antes de continuar', 'error');
                return false;
            }

            // Mostrar loading
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Atualizando...';
            submitButton.disabled = true;

            // Restaurar botão em caso de erro
            setTimeout(() => {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }, 10000);
        });

        // Função para mostrar erro em campo específico
        function showFieldError(fieldId, message) {
            const field = document.getElementById(fieldId);
            field.classList.add('border-red-300');

            // Remover erro existente
            const existingError = field.parentNode.querySelector('.field-error');
            if (existingError) {
                existingError.remove();
            }

            // Adicionar nova mensagem de erro
            const errorElement = document.createElement('p');
            errorElement.className = 'mt-1 text-sm text-red-600 field-error';
            errorElement.textContent = message;
            field.parentNode.appendChild(errorElement);

            // Remover erro quando o campo for focado
            field.addEventListener('focus', function() {
                this.classList.remove('border-red-300');
                const error = this.parentNode.querySelector('.field-error');
                if (error) {
                    error.remove();
                }
            });
        }

        // Função de notificação
        function showNotification(message, type) {
            if (typeof window.showNotification === 'function') {
                window.showNotification(message, type);
            } else {
                alert(message);
            }
        }

        // Função para formatar moeda
        function formatCurrency(value) {
            return new Intl.NumberFormat('pt-MZ', {
                style: 'currency',
                currency: 'MZN',
                minimumFractionDigits: 2
            }).format(value).replace('MTn', 'MT');
        }

        // Inicialização
        togglePriceFields();
        calculateEstimates();
    });
</script>
@endpush

@push('styles')
<style>
    /* Transições suaves para campos de preço */
    #fixed_price_field,
    #hourly_rate_field {
        transition: opacity 0.3s ease;
    }

    /* Estilo para radio buttons customizados */
    input[type="radio"]:checked + div {
        background-color: #f0fff4;
        border-color: #10b981;
    }

    /* Animação para o switch toggle */
    .peer:checked ~ .peer-checked\:after\:translate-x-full::after {
        transform: translateX(100%);
    }

    /* Estilo para campos com erro */
    .border-red-300 {
        border-color: #fca5a5;
    }

    .border-red-300:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    /* Loading spinner */
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Hover effects */
    .transition-colors {
        transition-property: color, background-color, border-color;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    /* Focus states */
    .focus\:ring-2:focus {
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.5);
    }

    .focus\:border-transparent:focus {
        border-color: transparent;
    }

    /* Cores para os valores calculados */
    .text-red-600 {
        color: #dc2626;
    }

    .text-yellow-600 {
        color: #d97706;
    }

    .text-green-600 {
        color: #16a34a;
    }

    /* Melhorias responsivas */
    @media (max-width: 768px) {
        .xl\:grid-cols-3 {
            grid-template-columns: 1fr;
        }

        .xl\:col-span-2 {
            grid-column: span 1;
        }

        .sticky {
            position: static;
        }

        .md\:grid-cols-2 {
            grid-template-columns: 1fr;
        }

        .md\:grid-cols-3 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
@endsection
