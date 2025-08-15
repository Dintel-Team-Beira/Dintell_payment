@extends('layouts.app')

@section('title', 'Editar Produto - ' . $product->name)

@section('content')
<div class="container px-4 mx-auto sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Editar Produto</h1>
                <p class="mt-2 text-gray-600">Atualize as informações do produto "{{ $product->name }}"</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('products.show', $product) }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Visualizar
                </a>
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar à Lista
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-8 xl:grid-cols-3">
            <!-- Coluna Principal -->
            <div class="space-y-8 xl:col-span-2">
                <!-- Informações Básicas -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Informações Básicas</h3>
                                <p class="text-sm text-gray-600">Dados principais do produto</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-700">
                                    Nome do Produto *
                                </label>
                                <input type="text" name="name" id="name" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-300 @enderror"
                                       value="{{ old('name', $product->name) }}"
                                       placeholder="Digite o nome do produto">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="code" class="block mb-2 text-sm font-medium text-gray-700">
                                    Código do Produto
                                </label>
                                <input type="text" name="code" id="code"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('code') border-red-300 @enderror"
                                       value="{{ old('code', $product->code) }}"
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
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-300 @enderror"
                                      placeholder="Descrição detalhada do produto">{{ old('description', $product->description) }}</textarea>
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
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category') border-red-300 @enderror">
                                    <option value="">Selecione uma categoria</option>
                                    @foreach(\App\Models\Product::getCategories() as $key => $label)
                                        <option value="{{ $key }}" {{ old('category', $product->category) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="unit" class="block mb-2 text-sm font-medium text-gray-700">
                                    Unidade *
                                </label>
                                <input type="text" name="unit" id="unit" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('unit') border-red-300 @enderror"
                                       value="{{ old('unit', $product->unit) }}"
                                       placeholder="Ex: pç, kg, m, L">
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
                                <p class="text-sm text-gray-600">Informações financeiras do produto</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div>
                                <label for="cost_price" class="block mb-2 text-sm font-medium text-gray-700">
                                    Preço de Custo
                                </label>
                                <div class="relative">
                                    <span class="absolute text-gray-500 transform -translate-y-1/2 left-3 top-1/2">MT</span>
                                    <input type="number" name="cost_price" id="cost_price" step="0.01" min="0"
                                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('cost_price') border-red-300 @enderror"
                                           value="{{ old('cost_price', $product->cost_price) }}"
                                           placeholder="0.00">
                                </div>
                                @error('cost_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="price" class="block mb-2 text-sm font-medium text-gray-700">
                                    Preço de Venda *
                                </label>
                                <div class="relative">
                                    <span class="absolute text-gray-500 transform -translate-y-1/2 left-3 top-1/2">MT</span>
                                    <input type="number" name="price" id="price" step="0.01" min="0" required
                                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('price') border-red-300 @enderror"
                                           value="{{ old('price', $product->price) }}"
                                           placeholder="0.00">
                                </div>
                                @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tax_rate" class="block mb-2 text-sm font-medium text-gray-700">
                                    Taxa de IVA (%)
                                </label>
                                <input type="number" name="tax_rate" id="tax_rate" step="0.01" min="0" max="100"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tax_rate') border-red-300 @enderror"
                                       value="{{ old('tax_rate', $product->tax_rate ?? 16) }}"
                                       placeholder="16.00">
                                @error('tax_rate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Calculadora de Margem -->
                        <div class="p-4 rounded-lg bg-gray-50">
                            <h4 class="mb-3 text-sm font-medium text-gray-700">Cálculo de Margem</h4>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-green-600" id="margin">{{ $product->cost_price > 0 ? number_format((($product->price - $product->cost_price) / $product->price) * 100, 2) : 0 }}%</div>
                                    <div class="text-xs text-gray-500">Margem</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-blue-600" id="markup">{{ $product->cost_price > 0 ? number_format((($product->price - $product->cost_price) / $product->cost_price) * 100, 2) : 0 }}%</div>
                                    <div class="text-xs text-gray-500">Markup</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-purple-600" id="profit">{{ number_format($product->price - $product->cost_price, 2) }} MT</div>
                                    <div class="text-xs text-gray-500">Lucro Unit.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estoque e Informações Físicas -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-yellow-100 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Estoque e Informações Físicas</h3>
                                <p class="text-sm text-gray-600">Controle de estoque e características físicas</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div>
                                <label for="stock_quantity" class="block mb-2 text-sm font-medium text-gray-700">
                                    Quantidade em Estoque
                                </label>
                                <input type="number" name="stock_quantity" id="stock_quantity" min="0" step="0.01"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('stock_quantity') border-red-300 @enderror"
                                       value="{{ old('stock_quantity', $product->stock_quantity) }}"
                                       placeholder="0">
                                @error('stock_quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="min_stock" class="block mb-2 text-sm font-medium text-gray-700">
                                    Estoque Mínimo
                                </label>
                                <input type="number" name="min_stock" id="min_stock" min="0" step="0.01"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('min_stock') border-red-300 @enderror"
                                       value="{{ old('min_stock', $product->min_stock) }}"
                                       placeholder="0">
                                @error('min_stock')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="weight" class="block mb-2 text-sm font-medium text-gray-700">
                                    Peso (kg)
                                </label>
                                <input type="number" name="weight" id="weight" step="0.001" min="0"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('weight') border-red-300 @enderror"
                                       value="{{ old('weight', $product->weight) }}"
                                       placeholder="0.000">
                                @error('weight')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="dimensions" class="block mb-2 text-sm font-medium text-gray-700">
                                Dimensões (L x A x P)
                            </label>
                            <input type="text" name="dimensions" id="dimensions"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('dimensions') border-red-300 @enderror"
                                   value="{{ old('dimensions', $product->dimensions) }}"
                                   placeholder="Ex: 10cm x 5cm x 2cm">
                            @error('dimensions')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Imagem do Produto -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-purple-100 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Imagem do Produto</h3>
                                <p class="text-sm text-gray-600">Upload da imagem do produto</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @if($product->image)
                                <div class="flex items-center space-x-4">
                                    <img src="{{ asset('storage/' . $product->image) }}"
                                         alt="{{ $product->name }}"
                                         class="object-cover w-20 h-20 border border-gray-200 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Imagem atual</p>
                                        <p class="text-xs text-gray-500">Selecione uma nova imagem para substituir</p>
                                    </div>
                                </div>
                            @endif

                            <div>
                                <label for="image" class="block mb-2 text-sm font-medium text-gray-700">
                                    {{ $product->image ? 'Nova Imagem' : 'Imagem do Produto' }}
                                </label>
                                <input type="file" name="image" id="image" accept="image/*"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('image') border-red-300 @enderror">
                                @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</p>
                            </div>
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
                                <p class="text-sm text-gray-600">Configure o status do produto</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Status Ativo -->
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Produto Ativo</label>
                                <p class="text-xs text-gray-500">Produto visível e disponível para venda</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <!-- Informações do Produto -->
                        <div class="pt-6 border-t border-gray-200">
                            <h4 class="mb-4 text-sm font-medium text-gray-700">Informações</h4>
                            <div class="space-y-3 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Criado em:</span>
                                    <span class="text-gray-900">{{ $product->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Atualizado em:</span>
                                    <span class="text-gray-900">{{ $product->updated_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @if($product->code)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Código:</span>
                                    <span class="font-mono text-gray-900">{{ $product->code }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="p-6 space-y-3">
                        <button type="submit" class="inline-flex items-center justify-center w-full px-6 py-3 text-sm font-medium text-white transition-colors bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Atualizar Produto
                        </button>

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
        // Calculadora de margem em tempo real
        const costPriceInput = document.getElementById('cost_price');
        const priceInput = document.getElementById('price');
        const marginDisplay = document.getElementById('margin');
        const markupDisplay = document.getElementById('markup');
        const profitDisplay = document.getElementById('profit');

        function calculateMargins() {
            const costPrice = parseFloat(costPriceInput.value) || 0;
            const salePrice = parseFloat(priceInput.value) || 0;

            if (costPrice > 0 && salePrice > 0) {
                // Margem = ((Preço de Venda - Preço de Custo) / Preço de Venda) * 100
                const margin = ((salePrice - costPrice) / salePrice) * 100;

                // Markup = ((Preço de Venda - Preço de Custo) / Preço de Custo) * 100
                const markup = ((salePrice - costPrice) / costPrice) * 100;

                // Lucro = Preço de Venda - Preço de Custo
                const profit = salePrice - costPrice;

                marginDisplay.textContent = margin.toFixed(2) + '%';
                markupDisplay.textContent = markup.toFixed(2) + '%';
                profitDisplay.textContent = profit.toFixed(2) + ' MT';

                // Cores baseadas na margem
                if (margin < 10) {
                    marginDisplay.className = 'text-lg font-bold text-red-600';
                } else if (margin < 20) {
                    marginDisplay.className = 'text-lg font-bold text-yellow-600';
                } else {
                    marginDisplay.className = 'text-lg font-bold text-green-600';
                }
            } else {
                marginDisplay.textContent = '0%';
                markupDisplay.textContent = '0%';
                profitDisplay.textContent = '0.00 MT';
                marginDisplay.className = 'text-lg font-bold text-gray-600';
            }
        }

        // Event listeners para cálculo em tempo real
        costPriceInput.addEventListener('input', calculateMargins);
        priceInput.addEventListener('input', calculateMargins);

        // Preview da imagem
        const imageInput = document.getElementById('image');
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Criar ou atualizar preview da imagem
                    let preview = document.getElementById('image-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.id = 'image-preview';
                        preview.className = 'object-cover w-20 h-20 mt-3 border border-gray-200 rounded-lg';
                        imageInput.parentNode.appendChild(preview);
                    }
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
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

                    codeInput.value = code;
                }
            }
        });

        // Validação do formulário
        const form = document.getElementById('productForm');
        form.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const category = document.getElementById('category').value;
            const unit = document.getElementById('unit').value.trim();
            const price = parseFloat(document.getElementById('price').value);

            let hasErrors = false;

            // Validar campos obrigatórios
            if (!name) {
                showFieldError('name', 'Nome do produto é obrigatório');
                hasErrors = true;
            }

            if (!category) {
                showFieldError('category', 'Categoria é obrigatória');
                hasErrors = true;
            }

            if (!unit) {
                showFieldError('unit', 'Unidade é obrigatória');
                hasErrors = true;
            }

            if (!price || price <= 0) {
                showFieldError('price', 'Preço de venda deve ser maior que zero');
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

        // Função de notificação (assumindo que existe globalmente)
        function showNotification(message, type) {
            if (typeof window.showNotification === 'function') {
                window.showNotification(message, type);
            } else {
                alert(message);
            }
        }

        // Auto-save (opcional - salvar rascunho a cada 30 segundos)
        let autoSaveTimeout;
        const formInputs = form.querySelectorAll('input, select, textarea');

        formInputs.forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(autoSave, 30000); // 30 segundos
            });
        });

        function autoSave() {
            const formData = new FormData(form);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('auto_save', '1');

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Rascunho salvo automaticamente', 'info');
                }
            })
            .catch(error => {
                console.log('Erro no auto-save:', error);
            });
        }

        // Cálculo inicial das margens
        calculateMargins();
    });
</script>
@endpush

@push('styles')
<style>
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

    /* Preview de imagem */
    #image-preview {
        transition: all 0.3s ease;
    }

    #image-preview:hover {
        transform: scale(1.05);
    }

    /* Calculadora de margem */
    .text-red-600 {
        color: #dc2626;
    }

    .text-yellow-600 {
        color: #d97706;
    }

    .text-green-600 {
        color: #16a34a;
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
    }

    /* Hover effects */
    .transition-colors {
        transition-property: color, background-color, border-color;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    /* Focus states */
    .focus\:ring-2:focus {
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
    }

    .focus\:border-transparent:focus {
        border-color: transparent;
    }
</style>
@endpush
@endsection
