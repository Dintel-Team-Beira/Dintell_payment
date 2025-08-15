@extends('layouts.app')

@section('title', 'Produto: ' . $product->name)

@section('content')
<div class=" py-8 ">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <a href="{{ route('products.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                        Produtos
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ Str::limit($product->name, 30) }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header com ações -->
    <div class="flex flex-col justify-between mb-8 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
            <p class="mt-2 text-lg text-gray-600">{{ $product->code }}</p>
        </div>
        <div class="flex flex-col gap-3 mt-4 sm:flex-row sm:mt-0">
            <button type="button" 
                    onclick="printProduct()"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover-lift hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimir
            </button>
            <button type="button" 
                    onclick="duplicateProduct({{ $product->id }})"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover-lift hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                Duplicar
            </button>
            <a href="{{ route('products.edit', $product) }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover-lift hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar Produto
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Coluna Principal (2/3) -->
        <div class="lg:col-span-2">
            <!-- Informações Básicas -->
            <div class="mb-8 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="flex items-center text-lg font-semibold text-gray-900">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Informações Básicas
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nome do Produto</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Código</label>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $product->code }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Categoria</label>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ \App\Models\Product::getCategories()[$product->category] ?? $product->category }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Unidade</label>
                            <p class="mt-1 text-sm text-gray-900">{{ \App\Models\Product::getUnits()[$product->unit] ?? $product->unit }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <p class="mt-1">
                                @if($product->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <div class="w-2 h-2 mr-1 bg-green-500 rounded-full"></div>
                                        Ativo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <div class="w-2 h-2 mr-1 bg-gray-500 rounded-full"></div>
                                        Inativo
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data de Criação</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @if($product->description)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700">Descrição</label>
                            <div class="mt-2 p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $product->description }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Preços e Financeiro -->
            <div class="mb-8 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="flex items-center text-lg font-semibold text-gray-900">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                        Informações Financeiras
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div class="p-4 border border-green-200 rounded-lg bg-green-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-green-800">Preço de Venda</p>
                                    <p class="text-2xl font-bold text-green-900">{{ $product->formatted_price }}</p>
                                </div>
                                <div class="p-2 bg-green-200 rounded-full">
                                    <svg class="w-6 h-6 text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        @if($product->cost > 0)
                            <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-blue-800">Preço de Custo</p>
                                        <p class="text-2xl font-bold text-blue-900">{{ number_format($product->cost, 2, ',', '.') }} MT</p>
                                    </div>
                                    <div class="p-2 bg-blue-200 rounded-full">
                                        <svg class="w-6 h-6 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 13l5 5m0 0l5-5m-5 5V6"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-4 border border-purple-200 rounded-lg bg-purple-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-purple-800">Margem de Lucro</p>
                                        <p class="text-2xl font-bold text-purple-900">{{ number_format($product->profit_margin, 1) }}%</p>
                                        <p class="text-sm text-purple-700">{{ number_format($product->price - $product->cost, 2, ',', '.') }} MT</p>
                                    </div>
                                    <div class="p-2 bg-purple-200 rounded-full">
                                        <svg class="w-6 h-6 text-purple-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="md:col-span-2 p-4 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="text-center">
                                    <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">Preço de custo não informado</p>
                                    <p class="text-xs text-gray-400">Adicione o custo para calcular a margem de lucro</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    @if($product->tax_rate > 0)
                        <div class="mt-4 p-4 border border-yellow-200 rounded-lg bg-yellow-50">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                                <span class="text-sm font-medium text-yellow-800">Taxa de Imposto: {{ $product->tax_rate }}%</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Histórico de Movimentações (placeholder) -->
            <div class="mb-8 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="flex items-center text-lg font-semibold text-gray-900">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Histórico Recente
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center p-3 border border-gray-200 rounded-lg">
                            <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-full">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900">Produto criado</p>
                                <p class="text-xs text-gray-500">{{ $product->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        
                        @if($product->updated_at != $product->created_at)
                            <div class="flex items-center p-3 border border-gray-200 rounded-lg">
                                <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900">Produto atualizado</p>
                                    <p class="text-xs text-gray-500">{{ $product->updated_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar (1/3) -->
        <div class="space-y-8">
            <!-- Imagem do Produto -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Imagem do Produto</h3>
                </div>
                <div class="p-6">
                    @if($product->image)
                        <div class="relative group">
                            <img src="{{ asset('storage/' . $product->image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-64 object-cover rounded-lg border border-gray-200 cursor-pointer hover:opacity-90 transition-opacity"
                                 onclick="openImageModal('{{ asset('storage/' . $product->image) }}', '{{ $product->name }}')">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-200 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-center w-full h-64 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300">
                            <div class="text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Nenhuma imagem</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Estoque -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Controle de Estoque</h3>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Quantidade Atual -->
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 mb-4 border-4 rounded-full
                            @if($product->stock_status == 'in_stock') border-green-500 bg-green-50
                            @elseif($product->stock_status == 'low_stock') border-yellow-500 bg-yellow-50
                            @else border-red-500 bg-red-50 @endif">
                            <span class="text-2xl font-bold
                                @if($product->stock_status == 'in_stock') text-green-700
                                @elseif($product->stock_status == 'low_stock') text-yellow-700
                                @else text-red-700 @endif">
                                {{ $product->stock_quantity }}
                            </span>
                        </div>
                        <p class="text-sm font-medium text-gray-900">Quantidade em Estoque</p>
                        <p class="text-xs text-gray-500">{{ $product->unit }}</p>
                    </div>

                    <!-- Status do Estoque -->
                    <div class="flex items-center justify-center">
                        @if($product->stock_status == 'in_stock')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Estoque Normal
                            </span>
                        @elseif($product->stock_status == 'low_stock')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-2.734-.833-3.464 0L2.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                Estoque Baixo
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Sem Estoque
                            </span>
                        @endif
                    </div>

                    <!-- Detalhes do Estoque -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Estoque Mínimo:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $product->min_stock_level }} {{ $product->unit }}</span>
                        </div>
                        
                        @if($product->weight)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Peso:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $product->weight }} kg</span>
                            </div>
                        @endif
                        
                        @if($product->dimensions)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Dimensões:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $product->dimensions }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Ação de Ajuste -->
                    <button type="button" 
                            onclick="adjustStock({{ $product->id }}, '{{ $product->name }}')"
                            class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover-lift hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v4a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                        </svg>
                        Ajustar Estoque
                    </button>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Ações Rápidas</h3>
                </div>
                <div class="p-6 space-y-3">
                    <button type="button"
                            onclick="toggleStatus({{ $product->id }}, {{ $product->is_active ? 'false' : 'true' }})"
                            class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium border rounded-md hover-lift focus:outline-none focus:ring-2 focus:ring-offset-2
                                @if($product->is_active)
                                    text-yellow-700 border-yellow-200 bg-yellow-50 hover:bg-yellow-100 focus:ring-yellow-500
                                @else
                                    text-green-700 border-green-200 bg-green-50 hover:bg-green-100 focus:ring-green-500
                                @endif">
                        @if($product->is_active)
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                            </svg>
                            Desativar Produto
                        @else
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Ativar Produto
                        @endif
                    </button>

                    <button type="button"
                            onclick="generateQRCode()"
                            class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-purple-700 border border-purple-200 rounded-md bg-purple-50 hover-lift hover:bg-purple-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        Gerar QR Code
                    </button>

                    <div class="border-t border-gray-200 pt-3">
                        <button type="button"
                                onclick="deleteProduct({{ $product->id }}, '{{ $product->name }}')"
                                class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-red-700 border border-red-200 rounded-md bg-red-50 hover-lift hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Excluir Produto
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empresa -->
            @if($product->company)
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Empresa</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full">
                                <span class="text-sm font-semibold text-blue-700">
                                    {{ strtoupper(substr($product->company->name, 0, 2)) }}
                                </span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $product->company->name }}</p>
                                <p class="text-xs text-gray-500">Empresa responsável</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Imagem -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" onclick="closeImageModal()"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900" id="imageModalTitle">Imagem do Produto</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeImageModal()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-4">
                    <img id="imageModalImg" src="" alt="" class="w-full h-auto max-h-96 object-contain rounded-lg">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Ajuste de Estoque -->
    <div id="stockModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeStockModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="flex items-center text-lg font-medium text-gray-900">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v4a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                            </svg>
                            Ajustar Estoque
                        </h3>
                        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeStockModal()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Produto: <span id="stockProductName" class="font-medium">{{ $product->name }}</span></p>
                        <p class="text-sm text-gray-600">Estoque atual: <span id="currentStock" class="font-medium">{{ $product->stock_quantity }}</span></p>
                    </div>

                    <form id="stockForm" class="space-y-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Tipo de Operação</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="operation" value="add" checked class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
                                    <span class="ml-2 text-sm text-gray-700">Adicionar</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="operation" value="subtract" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
                                    <span class="ml-2 text-sm text-gray-700">Subtrair</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="operation" value="set" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
                                    <span class="ml-2 text-sm text-gray-700">Definir</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Quantidade</label>
                            <input type="number"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   name="quantity"
                                   min="0"
                                   required
                                   placeholder="0">
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Motivo (opcional)</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      name="reason"
                                      rows="2"
                                      placeholder="Ex: Compra de mercadoria, venda, ajuste de inventário..."></textarea>
                        </div>
                    </form>
                </div>

                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            form="stockForm"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover-lift hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Confirmar Ajuste
                    </button>
                    <button type="button"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover-lift hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            onclick="closeStockModal()">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de QR Code -->
    <div id="qrModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" onclick="closeQRModal()"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">QR Code do Produto</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeQRModal()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-6 text-center">
                    <div id="qrCodeContainer" class="flex justify-center mb-4">
                        <!-- QR Code será gerado aqui -->
                    </div>
                    <p class="text-sm text-gray-600 mb-4">{{ $product->name }}</p>
                    <p class="text-xs text-gray-500">Código: {{ $product->code }}</p>
                    <button type="button" 
                            onclick="downloadQRCode()"
                            class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover:bg-blue-100">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Baixar QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@section('styles')
<style>
/* Animações personalizadas */
@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.hover-lift:hover {
    transform: translateY(-2px);
    transition: all 0.2s ease-in-out;
}

.notification {
    animation: slideInRight 0.3s ease-out;
}

/* Impressão */
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        font-size: 12pt;
        line-height: 1.4;
    }
    
    .print-title {
        font-size: 18pt;
        font-weight: bold;
        margin-bottom: 20pt;
    }
    
    .print-section {
        margin-bottom: 15pt;
        page-break-inside: avoid;
    }
}

/* Gradientes para cards de preços */
.price-gradient-green {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
}

.price-gradient-blue {
    background: linear-gradient(135deg, #dbeafe 0%, #93c5fd 100%);
}

.price-gradient-purple {
    background: linear-gradient(135deg, #e9d5ff 0%, #c4b5fd 100%);
}
</style>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode/1.5.3/qrcode.min.js"></script>
<script>
// Variáveis globais
let qrCodeGenerated = false;

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    // Qualquer inicialização necessária
});

// Modal de imagem
function openImageModal(src, title) {
    document.getElementById('imageModalImg').src = src;
    document.getElementById('imageModalTitle').textContent = title;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// Modal de estoque
function adjustStock(id, name) {
    document.getElementById('stockProductName').textContent = name;
    document.getElementById('stockModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeStockModal() {
    document.getElementById('stockModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('stockForm').reset();
}

// Modal de QR Code
function generateQRCode() {
    const modal = document.getElementById('qrModal');
    const container = document.getElementById('qrCodeContainer');
    
    if (!qrCodeGenerated) {
        // Limpar container
        container.innerHTML = '';
        
        // Gerar QR Code
        const canvas = document.createElement('canvas');
        QRCode.toCanvas(canvas, `{{ route('products.show', $product) }}`, {
            width: 200,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#FFFFFF'
            }
        }, function (error) {
            if (error) {
                console.error('Erro ao gerar QR Code:', error);
                showNotification('Erro ao gerar QR Code', 'error');
                return;
            }
            container.appendChild(canvas);
            qrCodeGenerated = true;
        });
    }
    
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeQRModal() {
    document.getElementById('qrModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function downloadQRCode() {
    const canvas = document.querySelector('#qrCodeContainer canvas');
    if (canvas) {
        const link = document.createElement('a');
        link.download = `qrcode-{{ $product->code }}.png`;
        link.href = canvas.toDataURL();
        link.click();
        showNotification('QR Code baixado com sucesso!', 'success');
    }
}

// Ações do produto
function duplicateProduct(id) {
    if (confirm('Deseja criar uma cópia deste produto?')) {
        showLoading();
        fetch(`/api/products/${id}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showNotification('Produto duplicado com sucesso!', 'success');
                setTimeout(() => {
                    window.location.href = `/products/${data.product.id}`;
                }, 1500);
            } else {
                showNotification('Erro ao duplicar produto: ' + data.message, 'error');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showNotification('Erro ao duplicar produto', 'error');
        });
    }
}

function toggleStatus(id, status) {
    const statusText = status === 'true' ? 'ativar' : 'desativar';
    if (confirm(`Tem certeza que deseja ${statusText} este produto?`)) {
        showLoading();
        fetch(`/api/products/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ is_active: status === 'true' })
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showNotification('Status alterado com sucesso!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Erro ao alterar status', 'error');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showNotification('Erro ao alterar status', 'error');
        });
    }
}

function deleteProduct(id, name) {
    if (confirm(`Tem certeza que deseja excluir o produto "${name}"? Esta ação não pode ser desfeita.`)) {
        showLoading();
        fetch(`/api/products/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showNotification('Produto excluído com sucesso!', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("products.index") }}';
                }, 1500);
            } else {
                showNotification('Erro ao excluir produto: ' + data.message, 'error');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showNotification('Erro ao excluir produto', 'error');
        });
    }
}

// Formulário de ajuste de estoque
document.getElementById('stockForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    submitButton.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Processando...';
    submitButton.disabled = true;

    fetch('/api/products/adjust-stock', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Estoque ajustado com sucesso!', 'success');
            closeStockModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('Erro ao ajustar estoque: ' + (data.message || 'Erro desconhecido'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao ajustar estoque', 'error');
    })
    .finally(() => {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
});

// Impressão
function printProduct() {
    // Criar janela de impressão
    const printWindow = window.open('', '_blank');
    
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Produto: {{ $product->name }}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
                .section { margin-bottom: 20px; }
                .section h3 { background-color: #f5f5f5; padding: 10px; margin: 0 0 10px 0; }
                .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
                .info-item { margin-bottom: 10px; }
                .label { font-weight: bold; }
                .value { margin-left: 10px; }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>{{ $product->name }}</h1>
                <p>Código: {{ $product->code }}</p>
                <p>Data: ${new Date().toLocaleDateString('pt-BR')}</p>
            </div>
            
            <div class="section">
                <h3>Informações Básicas</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Nome:</span>
                        <span class="value">{{ $product->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Código:</span>
                        <span class="value">{{ $product->code }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Categoria:</span>
                        <span class="value">{{ \App\Models\Product::getCategories()[$product->category] ?? $product->category }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Unidade:</span>
                        <span class="value">{{ \App\Models\Product::getUnits()[$product->unit] ?? $product->unit }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Status:</span>
                        <span class="value">{{ $product->is_active ? 'Ativo' : 'Inativo' }}</span>
                    </div>
                </div>
                @if($product->description)
                <div class="info-item">
                    <span class="label">Descrição:</span>
                    <div class="value">{{ $product->description }}</div>
                </div>
                @endif
            </div>
            
            <div class="section">
                <h3>Informações Financeiras</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Preço de Venda:</span>
                        <span class="value">{{ $product->formatted_price }}</span>
                    </div>
                    @if($product->cost > 0)
                    <div class="info-item">
                        <span class="label">Preço de Custo:</span>
                        <span class="value">{{ number_format($product->cost, 2, ',', '.') }} MT</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Margem de Lucro:</span>
                        <span class="value">{{ number_format($product->profit_margin, 1) }}% ({{ number_format($product->price - $product->cost, 2, ',', '.') }} MT)</span>
                    </div>
                    @endif
                    @if($product->tax_rate > 0)
                    <div class="info-item">
                        <span class="label">Taxa de Imposto:</span>
                        <span class="value">{{ $product->tax_rate }}%</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="section">
                <h3>Controle de Estoque</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Quantidade em Estoque:</span>
                        <span class="value">{{ $product->stock_quantity }} {{ $product->unit }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Estoque Mínimo:</span>
                        <span class="value">{{ $product->min_stock_level }} {{ $product->unit }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Status do Estoque:</span>
                        <span class="value">
                            @if($product->stock_status == 'in_stock') Estoque Normal
                            @elseif($product->stock_status == 'low_stock') Estoque Baixo
                            @else Sem Estoque @endif
                        </span>
                    </div>
                    @if($product->weight)
                    <div class="info-item">
                        <span class="label">Peso:</span>
                        <span class="value">{{ $product->weight }} kg</span>
                    </div>
                    @endif
                    @if($product->dimensions)
                    <div class="info-item">
                        <span class="label">Dimensões:</span>
                        <span class="value">{{ $product->dimensions }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            @if($product->company)
            <div class="section">
                <h3>Empresa</h3>
                <div class="info-item">
                    <span class="label">Nome:</span>
                    <span class="value">{{ $product->company->name }}</span>
                </div>
            </div>
            @endif
            
            <div class="section">
                <h3>Datas</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Criado em:</span>
                        <span class="value">{{ $product->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($product->updated_at != $product->created_at)
                    <div class="info-item">
                        <span class="label">Atualizado em:</span>
                        <span class="value">{{ $product->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    
    // Aguardar carregamento e imprimir
    printWindow.onload = function() {
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    };
}

// Sistema de notificações
function showNotification(message, type = 'info') {
    // Remover notificações existentes
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    // Criar elemento de notificação
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

    // Animar entrada
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
        notification.classList.add('translate-x-0');
    }, 100);

    // Auto remover após 5 segundos
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}

// Loading overlay
function showLoading() {
    let loadingOverlay = document.getElementById('loadingOverlay');
    if (!loadingOverlay) {
        loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'loadingOverlay';
        loadingOverlay.className = 'fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50';
        loadingOverlay.innerHTML = `
            <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
                <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-700 font-medium">Processando...</span>
            </div>
        `;
        document.body.appendChild(loadingOverlay);
    }
    loadingOverlay.classList.remove('hidden');
}

function hideLoading() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.classList.add('hidden');
    }
}

// Atalhos de teclado
document.addEventListener('keydown', function(e) {
    // Escape para fechar modais
    if (e.key === 'Escape') {
        closeImageModal();
        closeStockModal();
        closeQRModal();
    }
    
    // Ctrl+P para imprimir
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        printProduct();
    }
    
    // Ctrl+E para editar
    if (e.ctrlKey && e.key === 'e') {
        e.preventDefault();
        window.location.href = '{{ route("products.edit", $product) }}';
    }
});

// Inicializar tooltips (se necessário)
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar qualquer inicialização de tooltips ou outros componentes
});
</script>
@endsection
@endsection