@extends('layouts.app')

@section('title', 'Gerenciamento de Produtos')

@section('content')
<div class="px-4 py-8 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Gerenciamento de Produtos</h1>
                <p class="mt-2 text-gray-600">Gerencie produtos para software house</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <button type="button"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 bg-white border border-green-300 rounded-md hover-lift hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        onclick="exportData()">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exportar
                </button>
                <button type="button"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover-lift hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        onclick="openQuickAddModal()">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Adição Rápida
                </button>
                <a href="{{ route('products.create') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover-lift hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Novo Produto
                </a>
            </div>
        </div>
    </div>

    <!-- Estatísticas rápidas -->
    <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
        <div class="p-6 bg-white border border-gray-200 shadow-sm hover-scale rounded-xl">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Produtos</p>
                    <p class="text-2xl font-semibold text-gray-900" id="total-products">{{ $products->total() }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 shadow-sm hover-scale rounded-xl">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Produtos Ativos</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Product::active()->count() }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 shadow-sm hover-scale rounded-xl">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-2.734-.833-3.464 0L2.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Estoque Baixo</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Product::lowStock()->count() }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 shadow-sm hover-scale rounded-xl">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Valor Total</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format(\App\Models\Product::sum(\DB::raw('price * stock_quantity')), 2, ',', '.') }} MT</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="mb-8">
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Filtros</h3>
                    <button type="button" class="text-sm text-gray-500 hover:text-gray-700" onclick="clearAllFilters()">
                        Limpar filtros
                    </button>
                </div>
                <form method="GET" action="{{ route('products.index') }}" id="filterForm" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-6">
                    <div class="lg:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Buscar:</label>
                        <div class="relative">
                            <input type="text"
                                   name="search"
                                   id="searchInput"
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="{{ request('search') }}"
                                   placeholder="Nome, código ou descrição">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Categoria:</label>
                        <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todas</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Status:</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todos</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativo</option>
                            <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Estoque Baixo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Faixa de Preço:</label>
                        <div class="flex space-x-2">
                            <input type="number"
                                   name="price_min"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="{{ request('price_min') }}"
                                   placeholder="Min">
                            <input type="number"
                                   name="price_max"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="{{ request('price_max') }}"
                                   placeholder="Max">
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="inline-flex items-center justify-center flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover-lift hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Filtrar
                        </button>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover-lift hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabela de Produtos -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="flex flex-col px-6 py-4 border-b border-gray-200 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900">Lista de Produtos</h3>
                <span class="ml-2 text-sm text-gray-500">({{ $products->total() }} produtos)</span>
            </div>
            <div class="hidden mt-4 bulk-actions sm:mt-0">
                <div class="flex items-center gap-4 px-4 py-2 border border-blue-200 rounded-lg bg-blue-50 fade-in">
                    <span class="text-sm text-blue-800">
                        <span class="font-semibold selected-count">0</span> selecionados
                    </span>
                    <div class="flex gap-2">
                        <button type="button" class="inline-flex items-center px-3 py-1 text-xs font-medium text-red-700 bg-white border border-red-300 rounded bulk-action hover-lift hover:bg-red-50" data-action="delete">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Excluir
                        </button>
                        <button type="button" class="inline-flex items-center px-3 py-1 text-xs font-medium text-yellow-700 bg-white border border-yellow-300 rounded bulk-action hover-lift hover:bg-yellow-50" data-action="deactivate">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                            </svg>
                            Desativar
                        </button>
                        <button type="button" class="inline-flex items-center px-3 py-1 text-xs font-medium text-green-700 bg-white border border-green-300 rounded bulk-action hover-lift hover:bg-green-50" data-action="activate">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Ativar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded select-all focus:ring-blue-500 focus:ring-2">
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Produto</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Código</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Categoria</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Preço</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Estoque</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="productsTableBody">
                    @forelse($products as $product)
                        <tr class="transition-colors hover:bg-gray-50" data-status="{{ $product->is_active ? 'active' : 'inactive' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded item-checkbox focus:ring-blue-500 focus:ring-2" value="{{ $product->id }}">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                             alt="{{ $product->name }}"
                                             class="object-cover w-12 h-12 mr-4 border border-gray-200 rounded-lg">
                                    @else
                                        <div class="flex items-center justify-center w-12 h-12 mr-4 text-sm font-medium text-white bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg">
                                            {{ strtoupper(substr($product->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                        @if($product->description)
                                            <div class="mt-1 text-sm text-gray-500">{{ Str::limit($product->description, 50) }}</div>
                                        @endif
                                        <div class="mt-1 text-xs text-gray-400">{{ $product->unit }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $product->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $product->category->name??'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-semibold text-green-600">{{ $product->formatted_price }}</div>
                                    @if($product->cost > 0)
                                        <div class="text-xs text-gray-500">
                                            Custo: {{ number_format($product->cost, 2, ',', '.') }} MT
                                        </div>
                                        <div class="text-xs font-medium text-blue-600">
                                            Margem: {{ number_format($product->profit_margin, 1) }}%
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($product->stock_status == 'in_stock') bg-green-100 text-green-800
                                        @elseif($product->stock_status == 'low_stock') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $product->stock_quantity }} unid.
                                    </span>
                                    @if($product->stock_status == 'low_stock')
                                        <span class="flex items-center mt-1 text-xs text-yellow-600">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-2.734-.833-3.464 0L2.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                            </svg>
                                            Estoque baixo
                                        </span>
                                    @elseif($product->stock_status == 'out_of_stock')
                                        <span class="flex items-center mt-1 text-xs text-red-600">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Sem estoque
                                        </span>
                                    @endif
                                    @if($product->min_stock_level > 0)
                                        <div class="mt-1 text-xs text-gray-400">
                                            Min: {{ $product->min_stock_level }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                            </td>
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div class="relative dropdown">
                                        <button type="button"
                                                class="inline-flex items-center px-3 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md dropdown-toggle hover-lift hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                onclick="toggleDropdown(this)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                            </svg>
                                        </button>
                                        <div class="absolute right-0 z-10 hidden w-48 py-1 mt-2 bg-white border border-gray-200 rounded-md shadow-lg dropdown-menu">
                                            <a href="{{ route('products.show', $product) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Visualizar
                                            </a>
                                            <a href="{{ route('products.edit', $product) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Editar
                                            </a>
                                            <button type="button" onclick="duplicateProduct({{ $product->id }})" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                                Duplicar
                                            </button>
                                            {{-- <button type="button" onclick="adjustStock({{ $product->id }}, '{{ $product->name }}')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v4a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                                                </svg>
                                                Ajustar Estoque
                                            </button> --}}
                                            <div class="border-t border-gray-100"></div>
                                            <button type="button" onclick="toggleStatus({{ $product->id }}, {{ $product->is_active ? 'false' : 'true' }})" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                @if($product->is_active)
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                                                    </svg>
                                                    Desativar
                                                @else
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Ativar
                                                @endif
                                            </button>
                                            <button type="button" onclick="deleteProduct({{ $product->id }}, '{{ $product->name }}')" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Excluir
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <h3 class="mt-4 text-lg font-medium text-gray-900">Nenhum produto encontrado</h3>
                                    <p class="mt-2 text-sm text-gray-500">Comece criando seu primeiro produto ou ajuste os filtros</p>
                                    <div class="mt-6">
                                        <a href="{{ route('products.create') }}"
                                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover-lift hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Criar Primeiro Produto
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        @if($products->hasPages())
            <div class="flex flex-col items-center justify-between px-6 py-4 border-t border-gray-200 sm:flex-row">
                <div class="mb-4 text-sm text-gray-700 sm:mb-0">
                    Mostrando {{ $products->firstItem() }} até {{ $products->lastItem() }}
                    de {{ $products->total() }} resultados
                </div>
                <div>
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Modal de Adição Rápida -->
    <div id="quickAddModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeQuickAddModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="flex items-center text-lg font-medium text-gray-900">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Adição Rápida de Produto
                        </h3>
                        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeQuickAddModal()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form id="quickAddForm" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Nome do Produto *</label>
                            <input type="text"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   name="name"
                                   required
                                   placeholder="Ex: Microsoft Office 365">
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Categoria</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        name="category">
                                    @foreach(\App\Models\Product::getCategories() as $key => $category)
                                        <option value="{{ $key }}">{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Unidade</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        name="unit">
                                    @foreach(\App\Models\Product::getUnits() as $key => $unit)
                                        <option value="{{ $key }}">{{ $unit }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Preço de Venda *</label>
                                <input type="number"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       name="price"
                                       step="0.01"
                                       min="0"
                                       required
                                       placeholder="0.00">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Preço de Custo</label>
                                <input type="number"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       name="cost"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Quantidade em Estoque</label>
                                <input type="number"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       name="stock_quantity"
                                       min="0"
                                       value="0"
                                       placeholder="0">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Estoque Mínimo</label>
                                <input type="number"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       name="min_stock_level"
                                       min="0"
                                       value="5"
                                       placeholder="5">
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Descrição</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      name="description"
                                      rows="3"
                                      placeholder="Descreva o produto..."></textarea>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="is_active" 
                                   id="is_active"
                                   value="1"
                                   checked
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Produto ativo</label>
                        </div>
                    </form>
                </div>

                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            form="quickAddForm"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover-lift hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="button-text">Salvar Produto</span>
                    </button>
                    <button type="button"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover-lift hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            onclick="closeQuickAddModal()">
                        Cancelar
                    </button>
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
                        <p class="text-sm text-gray-600">Produto: <span id="stockProductName" class="font-medium"></span></p>
                        <p class="text-sm text-gray-600">Estoque atual: <span id="currentStock" class="font-medium"></span></p>
                    </div>

                    <form id="stockForm" class="space-y-4">
                        @csrf
                        <input type="hidden" name="product_id" id="stockProductId">
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
</div>

@push('styles')
<style>
/* Custom animations */
@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.notification {
    animation: slideInRight 0.3s ease-out;
}

.fade-in {
    animation: fadeIn 0.3s ease-out;
}

.pulse-loading {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Custom scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    height: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Enhanced hover effects */
.hover-lift:hover {
    transform: translateY(-2px);
    transition: all 0.2s ease-in-out;
}

.hover-scale:hover {
    transform: scale(1.02);
    transition: all 0.2s ease-in-out;
}

/* Loading skeleton */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Indeterminate checkbox */
input[type="checkbox"]:indeterminate {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

input[type="checkbox"]:indeterminate::before {
    content: '';
    display: block;
    width: 8px;
    height: 2px;
    background-color: white;
    margin: 5px auto;
}

/* Dropdown improvements */
.dropdown-menu {
    min-width: 12rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Responsive improvements */
@media (max-width: 640px) {
    .overflow-x-auto {
        font-size: 0.875rem;
    }
}

/* Status indicators */
.bg-green-100 { background-color: #dcfce7; }
.text-green-800 { color: #166534; }
.bg-yellow-100 { background-color: #fef3c7; }
.text-yellow-800 { color: #92400e; }
.bg-red-100 { background-color: #fee2e2; }
.text-red-800 { color: #991b1b; }
.bg-gray-100 { background-color: #f3f4f6; }
.text-gray-800 { color: #1f2937; }
.bg-blue-100 { background-color: #dbeafe; }
.text-blue-800 { color: #1e40af; }
</style>
@endpush

@push('scripts')
<script>
// Variáveis globais
let currentStockProductId = null;
let searchTimeout = null;

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    setupBulkActions();
    setupSearchFunctionality();
});

function initializeEventListeners() {
    // Fechar dropdowns ao clicar fora
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });

    // Atalhos de teclado
    document.addEventListener('keydown', function(e) {
        // Ctrl+N para nova adição rápida
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            openQuickAddModal();
        }
        // Escape para fechar modais
        if (e.key === 'Escape') {
            closeQuickAddModal();
            closeStockModal();
        }
    });
}

// Modais
function openQuickAddModal() {
    document.getElementById('quickAddModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    document.querySelector('#quickAddForm input[name="name"]').focus();
}

function closeQuickAddModal() {
    document.getElementById('quickAddModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('quickAddForm').reset();
}

function openStockModal() {
    document.getElementById('stockModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeStockModal() {
    document.getElementById('stockModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('stockForm').reset();
}

// Dropdown
function toggleDropdown(button) {
    const dropdown = button.nextElementSibling;
    const isHidden = dropdown.classList.contains('hidden');

    // Fechar todos os dropdowns
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.classList.add('hidden');
    });

    // Alternar dropdown atual
    if (isHidden) {
        dropdown.classList.remove('hidden');
    }
}

// Ações de produto
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
                setTimeout(() => location.reload(), 1500);
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

function toggleStatus(id, status) {
    showLoading();
    fetch(`/api/products/${id}/toggle-status-id`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ is_active: status })
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
                setTimeout(() => location.reload(), 1500);
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

function adjustStock(id, name) {
    currentStockProductId = id;
    document.getElementById('stockProductName').textContent = name;
    document.getElementById('stockProductId').value = id;
    
    // Buscar estoque atual
    fetch(`/api/products/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('currentStock').textContent = data.product.stock_quantity;
            }
        });
    
    openStockModal();
}

function exportData() {
    const params = new URLSearchParams(window.location.search);
    const link = document.createElement('a');
    link.href = `/products/export?${params.toString()}`;
    link.target = '_blank';
    link.click();
    showNotification('Exportação iniciada!', 'info');
}

function clearAllFilters() {
    window.location.href = '{{ route("products.index") }}';
}

// Formulários
document.getElementById('quickAddForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const buttonText = submitButton.querySelector('.button-text');
    const originalText = buttonText.innerHTML;
    
    // Estado de carregamento
    buttonText.innerHTML = 'Salvando...';
    submitButton.disabled = true;
    submitButton.classList.add('pulse-loading');

    fetch('{{ route("products.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Produto criado com sucesso!', 'success');
            closeQuickAddModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('Erro ao criar produto: ' + (data.message || 'Erro desconhecido'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao criar produto', 'error');
    })
    .finally(() => {
        buttonText.innerHTML = originalText;
        submitButton.disabled = false;
        submitButton.classList.remove('pulse-loading');
    });
});

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

// Ações em lote
function setupBulkActions() {
    const selectAll = document.querySelector('.select-all');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkActions = document.querySelector('.bulk-actions');
    const selectedCount = document.querySelector('.selected-count');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        const count = checkedBoxes.length;

        if (count > 0) {
            bulkActions.classList.remove('hidden');
            selectedCount.textContent = count;
        } else {
            bulkActions.classList.add('hidden');
        }

        if (selectAll) {
            selectAll.checked = count === itemCheckboxes.length && count > 0;
            selectAll.indeterminate = count > 0 && count < itemCheckboxes.length;
        }
    }

    // Handlers de ações em lote
    document.querySelectorAll('.bulk-action').forEach(button => {
        button.addEventListener('click', function() {
            const action = this.dataset.action;
            const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
            const ids = Array.from(checkedBoxes).map(cb => cb.value);

            if (ids.length === 0) {
                showNotification('Nenhum item selecionado', 'warning');
                return;
            }

            let confirmMessage = '';
            let endpoint = '';
            let successMessage = '';

            switch (action) {
                case 'delete':
                    confirmMessage = `Tem certeza que deseja excluir ${ids.length} produto(s)?`;
                    endpoint = '/api/products/bulk-delete';
                    successMessage = 'Produtos excluídos com sucesso!';
                    break;
                case 'deactivate':
                    confirmMessage = `Tem certeza que deseja desativar ${ids.length} produto(s)?`;
                    endpoint = '/api/products/bulk-deactivate';
                    successMessage = 'Produtos desativados com sucesso!';
                    break;
                case 'activate':
                    confirmMessage = `Tem certeza que deseja ativar ${ids.length} produto(s)?`;
                    endpoint = '/api/products/bulk-activate';
                    successMessage = 'Produtos ativados com sucesso!';
                    break;
            }

            if (confirm(confirmMessage)) {
                showLoading();
                fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        showNotification(successMessage, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Erro na operação: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showNotification('Erro na operação', 'error');
                });
            }
        });
    });
}

// Busca com debounce
function setupSearchFunctionality() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value;

            if (query.length >= 2 || query.length === 0) {
                searchTimeout = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 500);
            }
        });
    }
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

// Atualizar estatísticas em tempo real
function updateStats() {
    fetch('/api/products/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('total-products').textContent = data.stats.total;
                // Atualizar outras estatísticas conforme necessário
            }
        })
        .catch(error => console.error('Erro ao atualizar estatísticas:', error));
}

// Atualizar estatísticas a cada 30 segundos
setInterval(updateStats, 30000);
</script>
@endpush
@endsection