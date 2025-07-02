@extends('layouts.app')

@section('title', 'Novo Serviço')

@section('content')
<div class="sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                {{-- <h1 class="text-3xl font-bold text-gray-900">Novo Serviço</h1>
                <p class="mt-2 text-gray-600">Cadastre um novo serviço para a software house</p> --}}
            </div>
            <div class="flex gap-3">
                <a href="/servicos/dintell"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar
                </a>
                <button type="button"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        onclick="openTemplateModal()">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Usar Template
                </button>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <form id="serviceForm" action="{{ route('services.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Header do Form -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="p-2 mr-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Informações do Serviço</h3>
                        <p class="text-sm text-gray-600">Preencha os dados básicos do serviço</p>
                    </div>
                </div>
            </div>

            <div class="px-6 pb-6 space-y-8">
                <!-- Seção 1: Informações Básicas -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Nome do Serviço -->
                    <div class="lg:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Nome do Serviço *
                        </label>
                        <input type="text"
                               name="name"
                               id="service_name"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Ex: Desenvolvimento de Sistema Web"
                               value="{{ old('name') }}"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Código -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Código do Serviço
                            <span class="text-xs text-gray-500">(Gerado automaticamente)</span>
                        </label>
                        <input type="text"
                               name="code"
                               id="service_code"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50"
                               placeholder="AUTO"
                               value="{{ old('code') }}"
                               readonly>
                        <p class="mt-1 text-xs text-gray-500">O código será gerado baseado no nome do serviço</p>
                    </div>

                    <!-- Categoria -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Categoria *
                        </label>
                        <select name="category"
                                id="service_category"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                            <option value="">Selecione uma categoria</option>
                            @foreach(App\Models\Service::getCategories() as $key => $category)
                                <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Seção 2: Descrição -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">
                        Descrição
                    </label>
                    <textarea name="description"
                              id="service_description"
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Descreva detalhadamente o que este serviço inclui...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Seção 3: Preços e Complexidade -->
                <div class="pt-8 border-t border-gray-200">
                    <div class="flex items-center mb-6">
                        <div class="p-2 mr-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Preços e Complexidade</h3>
                            <p class="text-sm text-gray-600">Configure os valores e nível de complexidade</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <!-- Preço por Hora -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Preço por Hora (MT)
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">MT</span>
                                <input type="number"
                                       name="hourly_rate"
                                       id="hourly_rate"
                                       class="w-full py-3 pl-12 pr-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="0.00"
                                       value="{{ old('hourly_rate') }}"
                                       step="0.01"
                                       min="0">
                            </div>
                            @error('hourly_rate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preço Fixo -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Preço Fixo (MT)
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">MT</span>
                                <input type="number"
                                       name="fixed_price"
                                       id="fixed_price"
                                       class="w-full py-3 pl-12 pr-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="0.00"
                                       value="{{ old('fixed_price') }}"
                                       step="0.01"
                                       min="0">
                            </div>
                            @error('fixed_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Complexidade -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Nível de Complexidade *
                            </label>
                            <select name="complexity_level"
                                    id="complexity_level"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                                @foreach(App\Models\Service::getComplexityLevels() as $key => $level)
                                    <option value="{{ $key }}" {{ old('complexity_level') == $key ? 'selected' : '' }}>
                                        {{ $level }}
                                    </option>
                                @endforeach
                            </select>
                            @error('complexity_level')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Info sobre preços -->
                    <div class="p-4 mt-4 border border-blue-200 rounded-lg bg-blue-50">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm">
                                <p class="mb-1 font-medium text-blue-800">Informações sobre preços:</p>
                                <ul class="space-y-1 text-blue-700">
                                    <li>• Você deve informar pelo menos um dos preços (por hora OU fixo)</li>
                                    <li>• O preço fixo tem prioridade sobre o preço por hora</li>
                                    <li>• Use preço por hora para serviços com duração variável</li>
                                    <li>• Use preço fixo para pacotes ou serviços padronizados</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção 4: Configurações Adicionais -->
                <div class="pt-8 border-t border-gray-200">
                    <div class="flex items-center mb-6">
                        <div class="p-2 mr-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Configurações Adicionais</h3>
                            <p class="text-sm text-gray-600">Definições opcionais para o serviço</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Horas Estimadas -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Horas Estimadas
                            </label>
                            <div class="relative">
                                <input type="number"
                                       name="estimated_hours"
                                       id="estimated_hours"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="0"
                                       value="{{ old('estimated_hours') }}"
                                       step="0.5"
                                       min="0">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">horas</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Tempo estimado para conclusão do serviço</p>
                            @error('estimated_hours')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Status Inicial
                            </label>
                            <div class="flex items-center space-x-6">
                                <label class="flex items-center">
                                    <input type="radio"
                                           name="is_active"
                                           value="1"
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2"
                                           {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                    <span class="flex items-center ml-2 text-sm text-gray-700">
                                        <span class="w-2 h-2 mr-2 bg-green-400 rounded-full"></span>
                                        Ativo
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio"
                                           name="is_active"
                                           value="0"
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2"
                                           {{ old('is_active') == '0' ? 'checked' : '' }}>
                                    <span class="flex items-center ml-2 text-sm text-gray-700">
                                        <span class="w-2 h-2 mr-2 bg-gray-400 rounded-full"></span>
                                        Inativo
                                    </span>
                                </label>
                            </div>
                            @error('is_active')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Seção 5: Requisitos e Tags -->
                <div class="pt-8 border-t border-gray-200">
                    <div class="flex items-center mb-6">
                        <div class="p-2 mr-3 bg-orange-100 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Requisitos e Tags</h3>
                            <p class="text-sm text-gray-600">Informações adicionais sobre o serviço</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Requisitos -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Requisitos Técnicos
                            </label>
                            <textarea name="requirements"
                                      id="requirements"
                                      rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Liste os requisitos técnicos, habilidades necessárias, ferramentas, etc.">{{ old('requirements') }}</textarea>
                            @error('requirements')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tags -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Tags
                                <span class="text-xs text-gray-500">(separadas por vírgula)</span>
                            </label>
                            <input type="text"
                                   name="tags"
                                   id="tags"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Ex: php, laravel, react, api, frontend"
                                   value="{{ old('tags') }}">
                            <p class="mt-1 text-xs text-gray-500">Use tags para facilitar a busca e categorização</p>
                            @error('tags')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer do Form -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Campos marcados com * são obrigatórios
                    </div>
                    <div class="flex gap-3">
                        <button type="button"
                                onclick="resetForm()"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Limpar
                        </button>
                        <button type="button"
                                onclick="previewService()"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Visualizar
                        </button>
                        <button type="submit"
                                id="submitButton"
                                class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Criar Serviço
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal de Templates -->
    <div id="templateModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <div class="px-6 pt-6 pb-4 bg-white">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="flex items-center text-lg font-medium text-gray-900">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Preview do Serviço
                        </h3>
                        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closePreviewModal()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div id="previewContent" class="space-y-6">
                        <!-- Conteúdo do preview será inserido aqui -->
                    </div>
                </div>

                <div class="flex justify-between px-6 py-3 bg-gray-50">
                    <button type="button"
                            class="inline-flex justify-center px-4 py-2 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm"
                            onclick="closePreviewModal()">
                        Fechar
                    </button>
                    <button type="button"
                            class="inline-flex justify-center px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm"
                            onclick="submitFromPreview()">
                        Confirmar e Criar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-generate code based on name
document.getElementById('service_name').addEventListener('input', function() {
    const name = this.value;
    const code = generateServiceCode(name);
    document.getElementById('service_code').value = code;
});

function generateServiceCode(name) {
    return name
        .toLowerCase()
        .replace(/[^a-z0-9\s]/g, '')
        .replace(/\s+/g, '-')
        .substring(0, 20)
        .toUpperCase();
}

// Price validation
function validatePrices() {
    const hourlyRate = parseFloat(document.getElementById('hourly_rate').value) || 0;
    const fixedPrice = parseFloat(document.getElementById('fixed_price').value) || 0;

    if (hourlyRate <= 0 && fixedPrice <= 0) {
        showNotification('Você deve informar pelo menos um preço (por hora ou fixo)', 'warning');
        return false;
    }
    return true;
}

// Form submission
document.getElementById('serviceForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!validatePrices()) {
        return;
    }

    const submitButton = document.getElementById('submitButton');
    const originalText = submitButton.innerHTML;

    // Show loading state
    submitButton.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Criando...';
    submitButton.disabled = true;

    // Submit form
    this.submit();
});

// Reset form
function resetForm() {
    if (confirm('Tem certeza que deseja limpar todos os campos?')) {
        document.getElementById('serviceForm').reset();
        document.getElementById('service_code').value = '';
        showNotification('Formulário limpo', 'info');
    }
}

// Template Modal Functions
function openTemplateModal() {
    document.getElementById('templateModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    loadTemplates();
}

function closeTemplateModal() {
    document.getElementById('templateModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function loadTemplates() {
    const templateList = document.getElementById('templateList');
    templateList.innerHTML = '<div class="col-span-2 py-4 text-center"><div class="w-8 h-8 mx-auto border-b-2 border-blue-600 rounded-full animate-spin"></div><p class="mt-2 text-sm text-gray-600">Carregando templates...</p></div>';

    fetch('/api/services/templates')
        .then(response => response.json())
        .then(templates => {
            templateList.innerHTML = '';

            if (templates.length === 0) {
                templateList.innerHTML = `
                    <div class="col-span-2 py-8 text-center">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-gray-500">Nenhum template disponível</p>
                    </div>
                `;
                return;
            }

            templates.forEach(template => {
                const templateCard = document.createElement('div');
                templateCard.className = 'border border-gray-200 rounded-lg p-4 hover:border-blue-300 cursor-pointer transition-colors';
                templateCard.onclick = () => applyTemplate(template);

                templateCard.innerHTML = `
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            ${template.category}
                        </span>
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            ${template.complexity_level}
                        </span>
                    </div>
                    <h4 class="mb-1 font-medium text-gray-900">${template.name}</h4>
                    <p class="mb-2 text-sm text-gray-600">${template.description || 'Sem descrição'}</p>
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>${template.hourly_rate ? 'MT ' + template.hourly_rate + '/h' : ''}</span>
                        <span>${template.fixed_price ? 'MT ' + template.fixed_price + ' fixo' : ''}</span>
                    </div>
                `;

                templateList.appendChild(templateCard);
            });
        })
        .catch(error => {
            console.error('Error loading templates:', error);
            templateList.innerHTML = `
                <div class="col-span-2 py-8 text-center">
                    <svg class="w-12 h-12 mx-auto mb-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-2.734-.833-3.464 0L2.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <p class="text-red-500">Erro ao carregar templates</p>
                </div>
            `;
        });
}

function applyTemplate(template) {
    if (confirm('Aplicar este template? Os dados atuais serão substituídos.')) {
        document.getElementById('service_name').value = template.name;
        document.getElementById('service_code').value = template.code || generateServiceCode(template.name);
        document.getElementById('service_category').value = template.category;
        document.getElementById('service_description').value = template.description || '';
        document.getElementById('hourly_rate').value = template.hourly_rate || '';
        document.getElementById('fixed_price').value = template.fixed_price || '';
        document.getElementById('complexity_level').value = template.complexity_level;
        document.getElementById('estimated_hours').value = template.estimated_hours || '';
        document.getElementById('requirements').value = template.requirements || '';
        document.getElementById('tags').value = template.tags || '';

        closeTemplateModal();
        showNotification('Template aplicado com sucesso!', 'success');
    }
}

// Preview Modal Functions
function previewService() {
    const formData = new FormData(document.getElementById('serviceForm'));
    const data = Object.fromEntries(formData.entries());

    // Validate required fields
    if (!data.name || !data.category || !data.complexity_level) {
        showNotification('Preencha pelo menos os campos obrigatórios para visualizar', 'warning');
        return;
    }

    if (!validatePrices()) {
        return;
    }

    const previewContent = document.getElementById('previewContent');

    // Categories and complexity mapping
    const categories = {
        'desenvolvimento': 'Desenvolvimento',
        'consultoria': 'Consultoria',
        'design': 'Design',
        'suporte': 'Suporte'
    };

    const complexityLevels = {
        'baixa': 'Baixa',
        'media': 'Média',
        'alta': 'Alta'
    };

    const complexityColors = {
        'baixa': 'bg-green-100 text-green-800',
        'media': 'bg-yellow-100 text-yellow-800',
        'alta': 'bg-red-100 text-red-800'
    };

    previewContent.innerHTML = `
        <div class="p-6 border border-gray-200 rounded-lg">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h3 class="mb-2 text-xl font-semibold text-gray-900">${data.name}</h3>
                    <div class="flex items-center mb-3 space-x-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            ${data.code || 'AUTO'}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            ${categories[data.category] || data.category}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${complexityColors[data.complexity_level] || 'bg-gray-100 text-gray-800'}">
                            ${complexityLevels[data.complexity_level] || data.complexity_level}
                        </span>
                    </div>
                </div>
                <div class="flex items-center">
                    ${data.is_active === '1' ?
                        '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><span class="w-2 h-2 mr-2 bg-green-400 rounded-full"></span>Ativo</span>' :
                        '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"><span class="w-2 h-2 mr-2 bg-gray-400 rounded-full"></span>Inativo</span>'
                    }
                </div>
            </div>

            ${data.description ? `
                <div class="mb-4">
                    <h4 class="mb-2 text-sm font-medium text-gray-700">Descrição:</h4>
                    <p class="text-sm text-gray-600">${data.description}</p>
                </div>
            ` : ''}

            <div class="grid grid-cols-1 gap-6 mb-4 md:grid-cols-2">
                <div>
                    <h4 class="mb-2 text-sm font-medium text-gray-700">Preços:</h4>
                    <div class="space-y-2">
                        ${data.hourly_rate ? `
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm text-gray-600">Por hora: <strong class="text-green-600">MT ${parseFloat(data.hourly_rate).toFixed(2)}</strong></span>
                            </div>
                        ` : ''}
                        ${data.fixed_price ? `
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                                <span class="text-sm text-gray-600">Preço fixo: <strong class="text-green-600">MT ${parseFloat(data.fixed_price).toFixed(2)}</strong></span>
                            </div>
                        ` : ''}
                    </div>
                </div>

                ${data.estimated_hours ? `
                    <div>
                        <h4 class="mb-2 text-sm font-medium text-gray-700">Estimativas:</h4>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm text-gray-600">${data.estimated_hours} horas estimadas</span>
                        </div>
                        ${data.hourly_rate && data.estimated_hours ? `
                            <div class="p-3 mt-2 rounded-lg bg-blue-50">
                                <span class="text-sm text-blue-800">Custo estimado: <strong>MT ${(parseFloat(data.hourly_rate) * parseFloat(data.estimated_hours)).toFixed(2)}</strong></span>
                            </div>
                        ` : ''}
                    </div>
                ` : ''}
            </div>

            ${data.requirements ? `
                <div class="mb-4">
                    <h4 class="mb-2 text-sm font-medium text-gray-700">Requisitos Técnicos:</h4>
                    <p class="text-sm text-gray-600">${data.requirements}</p>
                </div>
            ` : ''}

            ${data.tags ? `
                <div>
                    <h4 class="mb-2 text-sm font-medium text-gray-700">Tags:</h4>
                    <div class="flex flex-wrap gap-2">
                        ${data.tags.split(',').map(tag =>
                            `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">${tag.trim()}</span>`
                        ).join('')}
                    </div>
                </div>
            ` : ''}
        </div>
    `;

    document.getElementById('previewModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closePreviewModal() {
    document.getElementById('previewModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function submitFromPreview() {
    closePreviewModal();
    document.getElementById('serviceForm').dispatchEvent(new Event('submit'));
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+S to save
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        document.getElementById('serviceForm').dispatchEvent(new Event('submit'));
    }

    // Ctrl+R to reset
    if (e.ctrlKey && e.key === 'r') {
        e.preventDefault();
        resetForm();
    }

    // Ctrl+P to preview
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        previewService();
    }

    // Escape to close modals
    if (e.key === 'Escape') {
        closeTemplateModal();
        closePreviewModal();
    }
});

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;

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
        notification.classList.remove('translate-x-full');
        notification.classList.add('translate-x-0');
    }, 100);

    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}

// Auto-save to localStorage as draft
let draftTimer;
function saveDraft() {
    const formData = new FormData(document.getElementById('serviceForm'));
    const data = Object.fromEntries(formData.entries());
    localStorage.setItem('service_draft', JSON.stringify(data));
}

function loadDraft() {
    const draft = localStorage.getItem('service_draft');
    if (draft) {
        const data = JSON.parse(draft);
        Object.keys(data).forEach(key => {
            const field = document.querySelector(`[name="${key}"]`);
            if (field) {
                if (field.type === 'radio') {
                    const radio = document.querySelector(`[name="${key}"][value="${data[key]}"]`);
                    if (radio) radio.checked = true;
                } else {
                    field.value = data[key];
                }
            }
        });
        showNotification('Rascunho carregado', 'info');
    }
}

// Auto-save every 30 seconds
document.addEventListener('DOMContentLoaded', function() {
    // Load draft on page load
    if (confirm('Deseja carregar o rascunho salvo?')) {
        loadDraft();
    }

    // Auto-save form changes
    const form = document.getElementById('serviceForm');
    form.addEventListener('input', function() {
        clearTimeout(draftTimer);
        draftTimer = setTimeout(saveDraft, 3000); // Save after 3 seconds of inactivity
    });
});

// Clear draft on successful submission
window.addEventListener('beforeunload', function() {
    // Only clear if form was successfully submitted
    if (document.getElementById('submitButton').disabled) {
        localStorage.removeItem('service_draft');
    }
});
</script>
{{-- @endpush --}}

{{-- @push('styles') --}}
<style>
/* Custom focus styles */
.focus\:ring-2:focus {
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

/* Loading animation */
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

/* Animation for notifications */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.notification {
    animation: slideInRight 0.3s ease-out;
}

/* Hover transitions */
.transition-colors {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Custom radio button styling */
input[type="radio"]:checked {
    background-color: #3B82F6;
    border-color: #3B82F6;
}

/* Form section styling */
.form-section {
    position: relative;
}

.form-section::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(to bottom, #3B82F6, #1D4ED8);
    border-radius: 2px;
    opacity: 0.6;
}

/* Responsive improvements */
@media (max-width: 640px) {
    .grid-cols-1 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
}

/* Enhanced form field styling */
input:focus, select:focus, textarea:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 0 2px rgba(59, 130, 246, 0.5);
}

/* Button hover effects */
button:hover, a:hover {
    transform: translateY(-1px);
}

/* Template card hover effect */
.template-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style>
@endpush
@endsection