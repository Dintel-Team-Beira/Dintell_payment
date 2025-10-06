
@extends('layouts.app')

@section('title', isset($product) ? 'Editar Produto' : 'Novo Produto')

@section('content')
<div class=" sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ isset($product) ? 'Editar Produto' : 'Novo Produto' }}
                </h1>
                <p class="mt-2 text-gray-600">
                    {{ isset($product) ? 'Atualize as informações do produto' : 'Cadastre um novo produto/software' }}
                </p>
            </div>
            <div>
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar à Lista
                </a>
            </div>
        </div>
    </div>

    <form action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="auto-save"
          id="productForm">
        @csrf
        @if(isset($product))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Formulário Principal -->
            <div class="space-y-8 lg:col-span-2">
                <!-- Informações Básicas -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Informações Básicas</h3>
                                <p class="text-sm text-gray-600">Dados principais do produto</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                            <div class="md:col-span-3">
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-700">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        Nome do Produto *
                                    </span>
                                </label>
                                <input type="text"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-300 @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $product->name ?? '') }}"
                                       required
                                       placeholder="Ex: Sistema de Gestão Escolar">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="code" class="block mb-2 text-sm font-medium text-gray-700">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V6a1 1 0 011-1h2a1 1 0 011 1v1a1 1 0 001 1h2a1 1 0 011-1V6a1 1 0 011-1h2a1 1 0 011 1v1a1 1 0 001 1h2"/>
                                        </svg>
                                        Código
                                    </span>
                                </label>
                                <input type="text"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 @error('code') border-red-300 @enderror"
                                       id="code"
                                       name="code"
                                       value="{{ old('code', $product->code ?? '') }}"
                                       placeholder="AUTO"
                                       readonly>
                                @error('code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Gerado automaticamente</p>
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block mb-2 text-sm font-medium text-gray-700">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                    </svg>
                                    Descrição
                                </span>
                            </label>
                            <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-300 @enderror"
                                      id="description"
                                      name="description"
                                      rows="4"
                                      placeholder="Descreva detalhadamente o produto/serviço...">{{ old('description', $product->description ?? '') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="category" class="block mb-2 text-sm font-medium text-gray-700">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        Categoria *
                                    </span>
                                </label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category') border-red-300 @enderror"
                                        id="category"
                                        name="category_id"
                                        required>
                                    <option value="">Selecione uma categoria</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id}}"
                                                {{ old('category', $category->id ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="unit" class="block mb-2 text-sm font-medium text-gray-700">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        Unidade
                                    </span>
                                </label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('unit') border-red-300 @enderror"
                                        id="unit"
                                        name="unit">
                                    @foreach(App\Models\Product::getUnits() as $key => $unit)
                                        <option value="{{ $key }}"
                                                {{ old('unit', $product->unit ?? 'unidade') == $key ? 'selected' : '' }}>
                                            {{ $unit }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preços e Custos -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Preços e Custos</h3>
                                <p class="text-sm text-gray-600">Configuração de valores e margens</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div>
                                <label for="price" class="block mb-2 text-sm font-medium text-gray-700">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        Preço de Venda *
                                    </span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">MT</span>
                                    <input type="number"
                                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right @error('price') border-red-300 @enderror"
                                           id="price"
                                           name="price"
                                           value="{{ old('price', $product->price ?? '') }}"
                                           step="0.01"
                                           min="0"
                                           required>
                                </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="cost" class="block mb-2 text-sm font-medium text-gray-700">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        Custo
                                    </span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">MT</span>
                                    <input type="number"
                                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right @error('cost') border-red-300 @enderror"
                                           id="cost"
                                           name="cost"
                                           value="{{ old('cost', $product->cost ?? '') }}"
                                           step="0.01"
                                           min="0">
                                </div>
                                @error('cost')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tax_rate" class="block mb-2 text-sm font-medium text-gray-700">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        Taxa de IVA (%)
                                    </span>
                                </label>
                                <input type="number"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right @error('tax_rate') border-red-300 @enderror"
                                       id="tax_rate"
                                       name="tax_rate"
                                       value="{{ old('tax_rate', $product->tax_rate ?? 17) }}"
                                       step="0.01"
                                       min="0"
                                       max="100">
                                @error('tax_rate')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Calculador de Margem -->
                        <div id="margin-calculator" class="hidden">
                            <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <h4 class="text-sm font-semibold text-blue-800">Calculadora de Margem</h4>
                                </div>
                                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                                    <div class="text-center">
                                        <p class="text-xs font-medium text-blue-700">Margem de Lucro</p>
                                        <p id="profit-margin" class="text-lg font-bold text-green-600">0%</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xs font-medium text-blue-700">Lucro por Unidade</p>
                                        <p id="profit-per-unit" class="text-lg font-bold text-green-600">0 MT</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xs font-medium text-blue-700">Preço com IVA</p>
                                        <p id="price-with-tax" class="text-lg font-bold text-blue-600">0 MT</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xs font-medium text-blue-700">Valor do IVA</p>
                                        <p id="tax-amount" class="text-lg font-bold text-blue-600">0 MT</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Controle de Estoque -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-yellow-100 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Controle de Estoque</h3>
                                <p class="text-sm text-gray-600">Gerenciamento de inventário</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="stock_quantity" class="block mb-2 text-sm font-medium text-gray-700">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        Quantidade em Estoque
                                    </span>
                                </label>
                                <input type="number"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('stock_quantity') border-red-300 @enderror"
                                       id="stock_quantity"
                                       name="stock_quantity"
                                       value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}"
                                       min="0">
                                @error('stock_quantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="min_stock_level" class="block mb-2 text-sm font-medium text-gray-700">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-2.734-.833-3.464 0L2.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                        Estoque Mínimo
                                    </span>
                                </label>
                                <input type="number"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('min_stock_level') border-red-300 @enderror"
                                       id="min_stock_level"
                                       name="min_stock_level"
                                       value="{{ old('min_stock_level', $product->min_stock_level ?? 5) }}"
                                       min="0">
                                @error('min_stock_level')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Aviso quando estoque atingir este nível</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status e Configurações -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-gray-100 rounded-lg">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Configurações</h3>
                                <p class="text-sm text-gray-600">Status e preferências</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Status -->
                        <div>
                            <label class="block mb-3 text-sm font-medium text-gray-700">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                    Status
                                </span>
                            </label>
                            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50">
                                <span class="text-sm font-medium text-gray-700">Produto Ativo</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           id="is_active"
                                           name="is_active"
                                           value="1"
                                           class="sr-only peer"
                                           {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>

                        <!-- Imagem -->
                        <div>
                            <label for="image" class="block mb-2 text-sm font-medium text-gray-700">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Imagem do Produto
                                </span>
                            </label>
                            <input type="file"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('image') border-red-300 @enderror"
                                   id="image"
                                   name="image"
                                   accept="image/*">
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            @if(isset($product) && $product->image)
                                <div class="mt-3">
                                    <img src="{{ asset('storage/' . $product->image) }}"
                                         alt="Imagem atual"
                                         class="object-cover w-full h-32 border-2 border-gray-200 rounded-lg">
                                    <p class="mt-1 text-xs text-center text-gray-500">Imagem atual</p>
                                </div>
                            @endif

                            <!-- Preview -->
                            <div id="image-preview" class="hidden mt-3">
                                <img id="preview-img" class="object-cover w-full h-32 border-2 border-blue-200 rounded-lg">
                                <p class="mt-1 text-xs text-center text-blue-600">Preview da nova imagem</p>
                            </div>
                        </div>

                        <!-- Dimensões e Peso -->
                        <div class="space-y-4">
                            <div>
                                <label for="weight" class="block mb-2 text-sm font-medium text-gray-700">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l3-3m-3 3l-3-3"/>
                                        </svg>
                                        Peso (kg)
                                    </span>
                                </label>
                                <input type="number"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('weight') border-red-300 @enderror"
                                       id="weight"
                                       name="weight"
                                       value="{{ old('weight', $product->weight ?? '') }}"
                                       step="0.01"
                                       min="0">
                                @error('weight')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="dimensions" class="block mb-2 text-sm font-medium text-gray-700">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                        </svg>
                                        Dimensões
                                    </span>
                                </label>
                                <input type="text"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('dimensions') border-red-300 @enderror"
                                       id="dimensions"
                                       name="dimensions"
                                       value="{{ old('dimensions', $product->dimensions ?? '') }}"
                                       placeholder="Ex: 30x20x10 cm">
                                @error('dimensions')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit"
                                    id="submitButton"
                                    class="inline-flex items-center justify-center w-full px-6 py-3 text-sm font-medium text-white transition-colors bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ isset($product) ? 'Atualizar Produto' : 'Salvar Produto' }}
                            </button>

                            @if(isset($product))
                                <a href="{{ route('products.show', $product) }}"
                                   class="inline-flex items-center justify-center w-full px-6 py-2 text-sm font-medium text-blue-700 transition-colors border border-blue-200 rounded-md bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Visualizar
                                </a>
                                <a href="{{ route('products.duplicate', $product) }}"
                                   class="inline-flex items-center justify-center w-full px-6 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    Duplicar
                                </a>
                            @endif

                            <a href="{{ route('products.index') }}"
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
                                Os campos marcados com * são obrigatórios
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Auto-save Status -->
                <div id="autoSaveStatus" class="hidden">
                    <div class="p-3 border border-yellow-200 rounded-lg bg-yellow-50">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-yellow-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span class="text-sm font-medium text-yellow-800">Salvando rascunho...</span>
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
    // Preview da imagem
    const imageInput = document.getElementById('image');
    const previewDiv = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewDiv.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            previewDiv.classList.add('hidden');
        }
    });

    // Calculadora de margem
    const priceInput = document.getElementById('price');
    const costInput = document.getElementById('cost');
    const taxRateInput = document.getElementById('tax_rate');
    const marginCalculator = document.getElementById('margin-calculator');

    function updateCalculations() {
        const price = parseFloat(priceInput.value) || 0;
        const cost = parseFloat(costInput.value) || 0;
        const taxRate = parseFloat(taxRateInput.value) || 0;

        if (price > 0 && cost > 0) {
            marginCalculator.classList.remove('hidden');

            // Margem de lucro
            const profitMargin = ((price - cost) / cost) * 100;
            document.getElementById('profit-margin').textContent = profitMargin.toFixed(1) + '%';

            // Lucro por unidade
            const profitPerUnit = price - cost;
            document.getElementById('profit-per-unit').textContent = profitPerUnit.toFixed(2) + ' MT';

            // Preço com IVA
            const priceWithTax = price * (1 + taxRate / 100);
            document.getElementById('price-with-tax').textContent = priceWithTax.toFixed(2) + ' MT';

            // Valor do IVA
            const taxAmount = price * (taxRate / 100);
            document.getElementById('tax-amount').textContent = taxAmount.toFixed(2) + ' MT';
        } else {
            marginCalculator.classList.add('hidden');
        }
    }

    priceInput.addEventListener('input', updateCalculations);
    costInput.addEventListener('input', updateCalculations);
    taxRateInput.addEventListener('input', updateCalculations);

    // Inicializar cálculos
    updateCalculations();

    // Auto-geração de código baseado no nome
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('code');

    nameInput.addEventListener('input', function() {
        if (!codeInput.value || codeInput.readOnly) {
            const name = this.value;
            const words = name.split(' ');
            let code = 'PROD';

            if (words.length >= 2) {
                code = words.slice(0, 2).map(word => word.substring(0, 3).toUpperCase()).join('');
            } else if (words[0]) {
                code = words[0].substring(0, 6).toUpperCase();
            }

            // Adicionar número aleatório para unicidade
            code += Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            codeInput.value = code;
        }
    });

    // Validação de estoque
    const stockInput = document.getElementById('stock_quantity');
    const minStockInput = document.getElementById('min_stock_level');

    function validateStock() {
        const stock = parseInt(stockInput.value) || 0;
        const minStock = parseInt(minStockInput.value) || 0;

        // Remove existing warning
        const existingWarning = stockInput.parentNode.querySelector('.stock-warning');
        if (existingWarning) {
            existingWarning.remove();
        }

        if (stock <= minStock && stock > 0) {
            stockInput.classList.add('border-yellow-400', 'bg-yellow-50');
            stockInput.classList.remove('border-gray-300');

            const warning = document.createElement('div');
            warning.className = 'stock-warning mt-1 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-700 flex items-center';
            warning.innerHTML = `
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-2.734-.833-3.464 0L2.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                Estoque está no nível mínimo ou abaixo
            `;
            stockInput.parentNode.appendChild(warning);
        } else {
            stockInput.classList.remove('border-yellow-400', 'bg-yellow-50');
            stockInput.classList.add('border-gray-300');
        }
    }

    stockInput.addEventListener('input', validateStock);
    minStockInput.addEventListener('input', validateStock);

    // Validação inicial
    validateStock();

    // Form submission with loading state
    const form = document.getElementById('productForm');
    const submitButton = document.getElementById('submitButton');

    form.addEventListener('submit', function(e) {
        // Show loading state
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = `
            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Salvando...
        `;
        submitButton.disabled = true;

        // Prevent double submission
        setTimeout(() => {
            if (submitButton.disabled) {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }
        }, 10000);
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+S to save
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            form.submit();
        }

        // Ctrl+R to reset (only for new products)
        if (e.ctrlKey && e.key === 'r' && !{{ isset($product) ? 'true' : 'false' }}) {
            e.preventDefault();
            if (confirm('Limpar todos os campos?')) {
                form.reset();
                updateCalculations();
                validateStock();
            }
        }
    });
});

// Auto-save functionality
let autoSaveTimer;
const autoSaveStatus = document.getElementById('autoSaveStatus');

function showAutoSaveStatus() {
    autoSaveStatus.classList.remove('hidden');
    setTimeout(() => {
        autoSaveStatus.classList.add('hidden');
    }, 2000);
}

function saveDraft() {
    const formData = new FormData(document.getElementById('productForm'));
    formData.append('is_draft', 'true');

    showAutoSaveStatus();

    fetch('{{ route("products.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Rascunho salvo automaticamente', 'info');
        }
    })
    .catch(error => {
        console.warn('Auto-save failed:', error);
    });
}

// Auto-save every 2 minutes for new products
@if(!isset($product))
const form = document.getElementById('productForm');
form.addEventListener('input', function() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(saveDraft, 120000); // 2 minutes
});
@endif

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
</script>
@endpush

@push('styles')
<style>
/* Custom toggle switch styling */
.peer:checked ~ .peer-checked\:bg-blue-600 {
    background-color: #2563eb;
}

.peer:checked ~ .peer-checked\:after\:translate-x-full::after {
    transform: translateX(100%);
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

/* Form transitions */
.transition-colors {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Focus styles */
input:focus, select:focus, textarea:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 0 2px rgba(59, 130, 246, 0.5);
}

/* Button hover effects */
button:hover:not(:disabled), a:hover {
    transform: translateY(-1px);
}

button:disabled {
    transform: none;
    opacity: 0.6;
    cursor: not-allowed;
}

/* Card hover effects */
.bg-white {
    transition: all 0.2s ease-in-out;
}

.bg-white:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
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

/* Image preview styling */
#image-preview img {
    transition: all 0.3s ease-in-out;
}

#image-preview img:hover {
    transform: scale(1.05);
}

/* Stock warning animation */
.stock-warning {
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

/* Margin calculator animation */
#margin-calculator {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
        max-height: 0;
    }
    to {
        opacity: 1;
        transform: translateY(0);
        max-height: 200px;
    }
}

/* Auto-save status animation */
#autoSaveStatus {
    animation: pulse 0.5s ease-in-out;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

/* Responsive improvements */
@media (max-width: 640px) {
    .grid-cols-1 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .md\:grid-cols-2 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .md\:grid-cols-3 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .md\:grid-cols-4 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Form validation styles */
.border-red-300 {
    border-color: #fca5a5 !important;
}

.border-yellow-400 {
    border-color: #fbbf24 !important;
}

.bg-yellow-50 {
    background-color: #fffbeb !important;
}

/* Loading states */
.loading {
    position: relative;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

/* Enhanced switch styling */
.peer:checked + .peer-checked\:bg-blue-600 {
    background-color: #2563eb;
}

.peer:focus + div {
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3);
    border-radius: 9999px;
}

/* File input styling */
input[type="file"] {
    transition: all 0.2s ease-in-out;
}

input[type="file"]:hover {
    background-color: #f8fafc;
}

input[type="file"]:focus {
    background-color: #f1f5f9;
}

/* Grid improvements */
.grid {
    display: grid;
    gap: 1.5rem;
}

.space-y-6 > * + * {
    margin-top: 1.5rem;
}

.space-y-4 > * + * {
    margin-top: 1rem;
}

.space-y-3 > * + * {
    margin-top: 0.75rem;
}

/* Button group improvements */
.space-y-3 button,
.space-y-3 a {
    width: 100%;
    justify-content: center;
}

/* Icon improvements */
svg {
    flex-shrink: 0;
}

/* Text improvements */
.text-right {
    text-align: right;
}

/* Utility classes */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

.hidden {
    display: none;
}

/* Enhanced error styling */
.text-red-600 {
    color: #dc2626;
}

.border-red-300 {
    border-color: #fca5a5;
}

/* Enhanced success styling */
.text-green-600 {
    color: #059669;
}

.bg-green-50 {
    background-color: #ecfdf5;
}

.border-green-200 {
    border-color: #bbf7d0;
}

/* Enhanced warning styling */
.text-yellow-600 {
    color: #d97706;
}

.bg-yellow-50 {
    background-color: #fffbeb;
}

.border-yellow-200 {
    border-color: #fde68a;
}

/* Enhanced info styling */
.text-blue-600 {
    color: #2563eb;
}

.bg-blue-50 {
    background-color: #eff6ff;
}

.border-blue-200 {
    border-color: #bfdbfe;
}
</style>
@endpush
@endsection