@extends('layouts.app')

@section('title', 'Cotação')
@section('subtitle', 'Gerencie suas Cotações com facilidade')

@section('content')
<div class="">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Nova Cotação</h1>
                <p class="mt-2 text-gray-600">Crie uma proposta comercial personalizada para seu cliente</p>
            </div>
            <div>
                <a href="{{ route('quotes.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('quotes.store') }}" method="POST" id="quoteForm">
        @csrf

        <div class="grid grid-cols-1 gap-8 xl:grid-cols-3">
            <!-- Coluna Principal -->
            <div class="space-y-8 xl:col-span-2">
                <!-- Informações Básicas -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Informações da Cotação</h3>
                                <p class="text-sm text-gray-600">Dados básicos da proposta</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="client_id" class="block mb-2 text-sm font-medium text-gray-700">
                                    Cliente *
                                </label>
                                <select name="client_id" id="client_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg select2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                    <option value="">Selecione um cliente</option>
                                    @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id')==$client->id ? 'selected' : ''
                                        }}>
                                        {{ $client->name }}
                                    </option>
                                    @endforeach
                                </select>

                                @error('client_id')
                                <div class="mt-2 text-sm text-red-600">
                                    <i class="mr-1 fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div>
                                <label for="quote_date" class="block mb-2 text-sm font-medium text-gray-700">Data da Cotação *</label>
                                <input type="date" name="quote_date" id="quote_date" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('quote_date') border-red-300 @enderror" value="{{ old('quote_date', date('Y-m-d')) }}" required>
                                @error('quote_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="valid_until" class="block mb-2 text-sm font-medium text-gray-700">Válida até *</label>
                            <input type="date" name="valid_until" id="valid_until" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('valid_until') border-red-300 @enderror" value="{{ old('valid_until', date('Y-m-d', strtotime('+30 days'))) }}" required>
                            @error('valid_until')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Seleção de Produtos e Serviços -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-2 mr-3 bg-green-100 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Produtos & Serviços</h3>
                                    <p class="text-sm text-gray-600">Adicione itens à sua cotação</p>
                                </div>
                            </div>
                            <div class="flex space-x-3">
                                <button type="button" id="addProductBtn" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    Adicionar Produto
                                </button>
                                <button type="button" id="addServiceBtn" class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 border border-green-200 rounded-md bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Adicionar Serviço
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <!-- Lista de Itens Selecionados -->
                        <div id="selectedItems" class="space-y-4">
                            <div class="py-12 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <p class="text-lg font-medium">Nenhum item selecionado</p>
                                <p class="text-sm">Adicione produtos ou serviços para começar</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observações e Termos -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-purple-100 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Observações e Termos</h3>
                                <p class="text-sm text-gray-600">Informações adicionais da proposta</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label for="notes" class="block mb-2 text-sm font-medium text-gray-700">Observações</label>
                            <textarea name="notes" id="notes" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Observações sobre a cotação...">{{ old('notes') }}</textarea>
                        </div>

                        <div>
                            <label for="terms_conditions" class="block mb-2 text-sm font-medium text-gray-700">Termos e Condições</label>
                            <textarea name="terms_conditions" id="terms_conditions" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Termos e condições da cotação...">{{ old('terms_conditions') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Resumo Financeiro -->
                <div class="sticky bg-white border border-gray-200 shadow-sm rounded-xl top-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-yellow-100 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Resumo Financeiro</h3>
                                <p class="text-sm text-gray-600">Totais da cotação</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-700">Subtotal:</span>
                                <span id="subtotalDisplay" class="text-sm font-bold text-gray-900">0,00 MT</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-700">IVA:</span>
                                <span id="taxDisplay" class="text-sm font-bold text-gray-900">0,00 MT</span>
                            </div>
                            <div class="flex items-center justify-between px-4 py-3 border-l-4 border-blue-500 rounded-lg bg-gradient-to-r from-blue-50 to-blue-100">
                                <span class="text-lg font-bold text-blue-800">TOTAL:</span>
                                <span id="totalDisplay" class="text-xl font-bold text-blue-800">0,00 MT</span>
                            </div>
                        </div>

                        <!-- Estatísticas Rápidas -->
                        <div class="pt-6 mt-6 border-t border-gray-200">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600" id="itemCount">0</div>
                                    <div class="text-xs text-gray-500">Itens</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600" id="avgPrice">0</div>
                                    <div class="text-xs text-gray-500">Preço Médio</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit" id="saveQuoteBtn" class="inline-flex items-center justify-center w-full px-6 py-3 text-sm font-medium text-white transition-colors bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Salvar Cotação
                            </button>

                            {{-- <button type="button" @click="showPreviewModal = true" :disabled="!selectedItems || selectedItems.length === 0"
                            class="inline-flex items-center justify-center w-full px-6 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Visualizar
                    </button> --}}

                            <a href="{{ route('quotes.index') }}" class="inline-flex items-center justify-center w-full px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancelar
                            </a>
                        </div>

                        <div class="pt-6 mt-6 border-t border-gray-200">
                            <div class="flex items-center justify-center text-xs text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Campos marcados com * são obrigatórios
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal de Seleção de Produtos -->
    <div id="productModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="px-6 pt-6 pb-4 bg-white">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="flex items-center text-lg font-medium text-gray-900">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Selecionar Produtos
                        </h3>
                        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeProductModal()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Busca de Produtos -->
                    <div class="mb-4">
                        <input type="text" id="productSearch" placeholder="Buscar produtos..." class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Lista de Produtos -->
                    <div class="overflow-y-auto max-h-96" id="productList">
                        <!-- Produtos serão carregados via JavaScript -->
                    </div>
                </div>

                <div class="flex justify-between px-6 py-3 bg-gray-50">
                    <button type="button" class="inline-flex justify-center px-4 py-2 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm" onclick="closeProductModal()">
                        Fechar
                    </button>
                    <button type="button" class="inline-flex justify-center px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm" onclick="addSelectedProducts()">
                        Adicionar Selecionados
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Seleção de Serviços -->
    <div id="serviceModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="px-6 pt-6 pb-4 bg-white">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="flex items-center text-lg font-medium text-gray-900">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Selecionar Serviços
                        </h3>
                        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeServiceModal()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Busca de Serviços -->
                    <div class="mb-4">
                        <input type="text" id="serviceSearch" placeholder="Buscar serviços..." class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <!-- Lista de Serviços -->
                    <div class="overflow-y-auto max-h-96" id="serviceList">
                        <!-- Serviços serão carregados via JavaScript -->
                    </div>
                </div>

                <div class="flex justify-between px-6 py-3 bg-gray-50">
                    <button type="button" class="inline-flex justify-center px-4 py-2 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm" onclick="closeServiceModal()">
                        Fechar
                    </button>
                    <button type="button" class="inline-flex justify-center px-4 py-2 text-base font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:text-sm" onclick="addSelectedServices()">
                        Adicionar Selecionados
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itemIndex = 0;
        let selectedItems = [];
        let products = [];
        let services = [];

        // Carregar produtos e serviços
        loadProducts();
        loadServices();

        // Event Listeners
        document.getElementById('addProductBtn').addEventListener('click', openProductModal);
        document.getElementById('addServiceBtn').addEventListener('click', openServiceModal);
        document.getElementById('productSearch').addEventListener('input', filterProducts);
        document.getElementById('serviceSearch').addEventListener('input', filterServices);
        document.getElementById('quoteForm').addEventListener('submit', handleFormSubmit);

        // Funções de Modal
        function openProductModal() {
            document.getElementById('productModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            renderProducts();
        }

        function closeProductModal() {
            document.getElementById('productModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function openServiceModal() {
            document.getElementById('serviceModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            renderServices();
        }

        function closeServiceModal() {
            document.getElementById('serviceModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Carregar dados
        function loadProducts() {
            fetch(`/api/products/active/{{ auth()->user()->company_id }}`)
                .then(response => response.json())
                .then(data => {
                    products = data;
                    console.log(data);
                    
                    renderProducts();
                })
                .catch(error => {
                    console.error('Erro ao carregar produtos:', error);
                    showNotification('Erro ao carregar produtos'+data, 'error');
                });
        }

        function loadServices() {
            fetch('/api/services/active/{{ auth()->user()->company_id }}')
                .then(response => response.json())
                .then(data => {
                    services = data;
                    renderServices();
                })
                .catch(error => {
                    console.error('Erro ao carregar serviços:', error);
                    showNotification('Erro ao carregar serviços', 'error');
                });
        }

        // Renderizar produtos
        function renderProducts(filter = '') {
            const productList = document.getElementById('productList');
            const filteredProducts = products.filter(product =>
                product.name.toLowerCase().includes(filter.toLowerCase()) ||
                product.code.toLowerCase().includes(filter.toLowerCase())
            );

            if (filteredProducts.length === 0) {
                productList.innerHTML = `
        <div class="py-8 text-center text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p>Nenhum produto encontrado</p>
        </div>
    `;
                return;
            }

            productList.innerHTML = filteredProducts.map(product => `
    <div class="flex items-center p-4 mb-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 product-item" data-id="${product.id}">
        <input type="checkbox" class="w-4 h-4 mr-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
        <div class="flex-1">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-gray-900">${product.name}</h4>
                    <p class="text-xs text-gray-500">${product.code}</p>
                    ${product.description ? `<p class="mt-1 text-xs text-gray-600">${product.description.substring(0, 100)}...</p>` : ''}
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-green-600">${formatCurrency(product.price)}</p>
                    <p class="text-xs text-gray-500">Estoque: ${product.stock_quantity}</p>
                </div>
            </div>
        </div>
    </div>
`).join('');

            // Adicionar event listeners aos checkboxes
            productList.querySelectorAll('.product-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    if (e.target.type !== 'checkbox') {
                        const checkbox = this.querySelector('input[type="checkbox"]');
                        checkbox.checked = !checkbox.checked;
                    }
                });
            });
        }

        // Renderizar serviços
        function renderServices(filter = '') {
            const serviceList = document.getElementById('serviceList');
            const filteredServices = services.filter(service =>
                service.name.toLowerCase().includes(filter.toLowerCase()) ||
                service.code.toLowerCase().includes(filter.toLowerCase())
            );

            if (filteredServices.length === 0) {
                serviceList.innerHTML = `
        <div class="py-8 text-center text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p>Nenhum serviço encontrado</p>
        </div>
    `;
                return;
            }

            serviceList.innerHTML = filteredServices.map(service => `
    <div class="flex items-center p-4 mb-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 service-item" data-id="${service.id}">
        <input type="checkbox" class="w-4 h-4 mr-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2">
        <div class="flex-1">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-gray-900">${service.name}</h4>
                    <p class="text-xs text-gray-500">${service.code}</p>
                    ${service.description ? `<p class="mt-1 text-xs text-gray-600">${service.description.substring(0, 100)}...</p>` : ''}
                    <div class="flex items-center mt-1 space-x-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            ${service.category}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                            ${service.complexity_level}
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    ${service.fixed_price > 0 ?
                        `<p class="text-sm font-semibold text-green-600">${formatCurrency(service.fixed_price)}</p>
                         <p class="text-xs text-gray-500">Preço fixo</p>` :
                        `<p class="text-sm font-semibold text-green-600">${formatCurrency(service.hourly_rate)}/h</p>
                         <p class="text-xs text-gray-500">Por hora</p>`
                    }
                    ${service.estimated_hours ? `<p class="text-xs text-gray-500">${service.estimated_hours}h estimadas</p>` : ''}
                </div>
            </div>
        </div>
    </div>
`).join('');

            // Adicionar event listeners aos checkboxes
            serviceList.querySelectorAll('.service-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    if (e.target.type !== 'checkbox') {
                        const checkbox = this.querySelector('input[type="checkbox"]');
                        checkbox.checked = !checkbox.checked;
                    }
                });
            });
        }

        // Filtros
        function filterProducts(e) {
            renderProducts(e.target.value);
        }

        function filterServices(e) {
            renderServices(e.target.value);
        }

        // Adicionar produtos selecionados
        window.addSelectedProducts = function() {
            const checkedProducts = document.querySelectorAll('#productList input[type="checkbox"]:checked');

            checkedProducts.forEach(checkbox => {
                const productElement = checkbox.closest('.product-item');
                const productId = productElement.dataset.id;
                const product = products.find(p => p.id == productId);

                if (product && !selectedItems.find(item => item.type === 'product' && item.id == productId)) {
                    addItemToQuote(product, 'product');
                }
            });

            closeProductModal();
            showNotification('Produtos adicionados com sucesso!', 'success');
        };

        // Adicionar serviços selecionados
        window.addSelectedServices = function() {
            const checkedServices = document.querySelectorAll('#serviceList input[type="checkbox"]:checked');

            checkedServices.forEach(checkbox => {
                const serviceElement = checkbox.closest('.service-item');
                const serviceId = serviceElement.dataset.id;
                const service = services.find(s => s.id == serviceId);

                if (service && !selectedItems.find(item => item.type === 'service' && item.id == serviceId)) {
                    addItemToQuote(service, 'service');
                }
            });

            closeServiceModal();
            showNotification('Serviços adicionados com sucesso!', 'success');
        };

        // Adicionar item à cotação
        function addItemToQuote(item, type) {
            const newItem = {
                index: itemIndex++
                , id: item.id
                , type: type
                , name: item.name
                , code: item.code
                , description: item.description || ''
                , quantity: type === 'service' && item.estimated_hours ? item.estimated_hours : 1
                , unit_price: type === 'product' ? item.price : (item.fixed_price > 0 ? item.fixed_price : item.hourly_rate)
                , tax_rate: item.tax_rate || {{$settings -> default_tax_rate }}
                , category: item.category || ''
                , complexity: item.complexity_level || ''
            };

            selectedItems.push(newItem);
            renderSelectedItems();
            calculateTotals();
        }

        // Renderizar itens selecionados
        function renderSelectedItems() {
            const container = document.getElementById('selectedItems');

            if (selectedItems.length === 0) {
                container.innerHTML = `
        <div class="py-12 text-center text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <p class="text-lg font-medium">Nenhum item selecionado</p>
            <p class="text-sm">Adicione produtos ou serviços para começar</p>
        </div>
    `;
                return;
            }

            container.innerHTML = selectedItems.map(item => `
    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 item-card" data-index="${item.index}">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center mb-2 space-x-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${item.type === 'product' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}">
                        ${item.type === 'product' ? 'Produto' : 'Serviço'}
                    </span>
                    <span class="text-xs text-gray-500">${item.code}</span>
                </div>
                <h4 class="mb-1 text-sm font-medium text-gray-900">${item.name}</h4>
                ${item.description ? `<p class="mb-3 text-xs text-gray-600">${item.description.substring(0, 100)}...</p>` : ''}

                <div class="grid grid-cols-4 gap-3">
                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-700">Quantidade</label>
                        <input type="number"
                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 item-quantity"
                               value="${item.quantity}"
                               min="0.1"
                               step="0.1"
                               data-index="${item.index}">
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-700">Preço Unit.</label>
                        <input type="number"
                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 item-price"
                               value="${item.unit_price}"
                               min="0"
                               step="0.01"
                               data-index="${item.index}">
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-700">IVA (%)</label>
                        <input type="number"
                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 item-tax"
                               value="${item.tax_rate}"
                               min="0"
                               max="100"
                               step="0.01"
                               data-index="${item.index}">
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-700">Total</label>
                        <div class="px-2 py-1 text-sm font-medium text-green-600 rounded bg-green-50 item-total" data-index="${item.index}">
                            ${formatCurrency(calculateItemTotal(item))}
                        </div>
                    </div>
                </div>

                <!-- Hidden inputs para o form -->
                <input type="hidden" name="items[${item.index}][type]" value="${item.type}">
                <input type="hidden" name="items[${item.index}][item_id]" value="${item.id}">
                <input type="hidden" name="items[${item.index}][name]" value="${item.name}">
                <input type="hidden" name="items[${item.index}][description]" value="${item.description}">
                <input type="hidden" name="items[${item.index}][quantity]" value="${item.quantity}" class="hidden-quantity">
                <input type="hidden" name="items[${item.index}][unit_price]" value="${item.unit_price}" class="hidden-price">
                <input type="hidden" name="items[${item.index}][tax_rate]" value="${item.tax_rate}" class="hidden-tax">
            </div>
            <button type="button"
                    class="p-1 ml-4 text-red-600 hover:text-red-800 remove-item"
                    data-index="${item.index}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
    </div>
`).join('');

            // Adicionar event listeners
            attachItemEventListeners();
        }

        // Anexar event listeners aos itens
        function attachItemEventListeners() {
            // Remover itens
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    const index = parseInt(this.dataset.index);
                    selectedItems = selectedItems.filter(item => item.index !== index);
                    renderSelectedItems();
                    calculateTotals();
                    showNotification('Item removido', 'info');
                });
            });

            // Atualizar valores
            document.querySelectorAll('.item-quantity, .item-price, .item-tax').forEach(input => {
                input.addEventListener('input', function() {
                    const index = parseInt(this.dataset.index);
                    const item = selectedItems.find(item => item.index === index);

                    if (item) {
                        if (this.classList.contains('item-quantity')) {
                            item.quantity = parseFloat(this.value) || 0;
                            document.querySelector(`input[name="items[${index}][quantity]"]`).value = item.quantity;
                        } else if (this.classList.contains('item-price')) {
                            item.unit_price = parseFloat(this.value) || 0;
                            document.querySelector(`input[name="items[${index}][unit_price]"]`).value = item.unit_price;
                        } else if (this.classList.contains('item-tax')) {
                            item.tax_rate = parseFloat(this.value) || 0;
                            document.querySelector(`input[name="items[${index}][tax_rate]"]`).value = item.tax_rate;
                        }

                        // Atualizar total do item
                        const totalElement = document.querySelector(`.item-total[data-index="${index}"]`);
                        if (totalElement) {
                            totalElement.textContent = formatCurrency(calculateItemTotal(item));
                        }

                        calculateTotals();
                    }
                });
            });
        }

        // Calcular total do item
        function calculateItemTotal(item) {
            const subtotal = item.quantity * item.unit_price;
            const tax = subtotal * (item.tax_rate / 100);
            return subtotal + tax;
        }

        // Calcular totais
        function calculateTotals() {
            let subtotal = 0;
            let totalTax = 0;
            let totalItems = selectedItems.length;

            selectedItems.forEach(item => {
                const itemSubtotal = item.quantity * item.unit_price;
                const itemTax = itemSubtotal * (item.tax_rate / 100);

                subtotal += itemSubtotal;
                totalTax += itemTax;
            });

            const total = subtotal + totalTax;
            const avgPrice = totalItems > 0 ? total / totalItems : 0;

            // Atualizar display
            document.getElementById('subtotalDisplay').textContent = formatCurrency(subtotal);
            document.getElementById('taxDisplay').textContent = formatCurrency(totalTax);
            document.getElementById('totalDisplay').textContent = formatCurrency(total);
            document.getElementById('itemCount').textContent = totalItems;
            document.getElementById('avgPrice').textContent = formatCurrency(avgPrice);
        }

        // Validar datas
        document.getElementById('quote_date').addEventListener('change', function() {
            const quoteDate = new Date(this.value);
            const validUntilField = document.getElementById('valid_until');
            const validUntilDate = new Date(validUntilField.value);

            if (validUntilDate <= quoteDate) {
                const newValidDate = new Date(quoteDate);
                newValidDate.setDate(newValidDate.getDate() + 30);
                validUntilField.value = newValidDate.toISOString().split('T')[0];
            }
        });

        // Submissão do formulário
        function handleFormSubmit(e) {
            if (selectedItems.length === 0) {
                e.preventDefault();
                showNotification('Adicione pelo menos um item à cotação', 'warning');
                return false;
            }

            // Show loading state
            const submitButton = document.getElementById('saveQuoteBtn');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Salvando...';
            submitButton.disabled = true;
        }

        // Utilitários
        function formatCurrency(value) {
            return new Intl.NumberFormat('pt-MZ', {
                style: 'currency'
                , currency: 'MZN'
                , minimumFractionDigits: 2
            }).format(value).replace('MTn', 'MT');
        }

        // Sistema de notificações
        function showNotification(message, type = 'info') {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.notification');
            existingNotifications.forEach(notification => notification.remove());

            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;

            const colors = {
                success: 'bg-green-50 border border-green-200 text-green-800'
                , error: 'bg-red-50 border border-red-200 text-red-800'
                , info: 'bg-blue-50 border border-blue-200 text-blue-800'
                , warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800'
            };

            const icons = {
                success: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
                , error: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>'
                , info: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
                , warning: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>'
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

        // Expor funções globais
        window.closeProductModal = closeProductModal;
        window.closeServiceModal = closeServiceModal;


        // selecionar o cliente

        // Inicializar Select2
        $('.select2').select2({
            placeholder: 'Digite para buscar...'
            , allowClear: true
            , width: '100%'
            , minimumInputLength: 0
            , language: {
                noResults: function() {
                    return "Nenhum resultado encontrado";
                }
                , searching: function() {
                    return "Procurando...";
                }
                , inputTooShort: function() {
                    return "Digite para buscar";
                }
                , loadingMore: function() {
                    return "Carregando mais...";
                }
            }
        });

        // Manter seleção após erro de validação Laravel
        @if(old('client_id'))
        $('#client_id').val('{{ old('
            client_id ') }}').trigger('change');
        @endif

        // Aplicar estilo de erro se houver erro do Laravel
        @error('client_id')
        $('#client_id').next('.select2-container').addClass('select2-container--error');
        @enderror

        // Remover erro ao selecionar
        $('#client_id').on('change', function() {
            if ($(this).val()) {
                $(this).next('.select2-container').removeClass('select2-container--error');
            }
        });

    });

</script>
@endpush

@push('styles')
<style>
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

    /* Custom focus styles */
    .focus\:ring-2:focus {
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
    }

    /* Hover transitions */
    .transition-colors {
        transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    /* Item card hover effects */
    .item-card {
        transition: all 0.2s ease-in-out;
    }

    .item-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    /* Modal overlay */
    .modal-overlay {
        backdrop-filter: blur(4px);
    }

    /* Sticky sidebar */
    .sticky {
        position: sticky;
        top: 2rem;
    }

    /* Enhanced form inputs */
    input:focus,
    select:focus,
    textarea:focus {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 0 2px rgba(59, 130, 246, 0.5);
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
        .grid-cols-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .space-x-3>*+* {
            margin-left: 0;
            margin-top: 0.75rem;
        }

        .flex.space-x-3 {
            flex-direction: column;
        }
    }

    /* Enhanced card styling */
    .bg-white {
        transition: all 0.2s ease-in-out;
    }

    .bg-white:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* Gradient backgrounds */
    .bg-gradient-to-r {
        background-image: linear-gradient(to right, var(--tw-gradient-stops));
    }

    /* Price display styling */
    .item-total {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    }

    /* Selection styling */
    .product-item:hover,
    .service-item:hover {
        border-color: #3b82f6;
        background-color: #eff6ff;
    }

    .product-item input:checked+*,
    .service-item input:checked+* {
        background-color: #dbeafe;
    }

    /* Badge styling improvements */
    .inline-flex.items-center {
        align-items: center;
    }

    /* Enhanced visibility for required fields */
    label:has(+ input[required])::after,
    label:has(+ select[required])::after {
        content: ' *';
        color: #ef4444;
    }

</style>
@endpush
@endsection
