@extends('layouts.app')

@section('title', 'Nova Categoria')
@section('subtitle', 'Criar uma nova categoria')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Informações da Categoria</h3>
        </div>

        <form action="{{ route('categories.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Nome -->
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-700">
                    Nome da Categoria *
                </label>
                <input type="text" name="name" id="name" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                       value="{{ old('name') }}"
                       placeholder="Ex: Eletrônicos, Consultoria, etc.">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Descrição -->
            <div>
                <label for="description" class="block mb-2 text-sm font-medium text-gray-700">
                    Descrição
                </label>
                <textarea name="description" id="description" rows="3"
                          class="w-full p-4 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Descrição opcional da categoria">{{ old('description') }}</textarea>
            </div>

            <!-- Tipo -->
            <div>
                <label for="type" class="block mb-2 text-sm font-medium text-gray-700">
                    Tipo *
                </label>
                <select name="type" id="type" required
                        class="w-full border-gray-300 p-2 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="both" {{ old('type') == 'both' ? 'selected' : '' }}>Produtos e Serviços</option>
                    <option value="product" {{ old('type') == 'product' ? 'selected' : '' }}>Apenas Produtos</option>
                    <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>Apenas Serviços</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">Define se esta categoria pode ser usada para produtos, serviços ou ambos</p>
            </div>

            <!-- Categoria Pai -->
            <div>
                <label for="parent_id" class="block mb-2 text-sm font-medium text-gray-700">
                    Categoria Pai (Opcional)
                </label>
                <select name="parent_id" id="parent_id"
                        class="w-full border-gray-300 p-2 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Nenhuma (Categoria Principal)</option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Selecione uma categoria pai para criar uma subcategoria</p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Cor -->
                <div>
                    <label for="color" class="block mb-2 text-sm font-medium text-gray-700">
                        Cor
                    </label>
                    <input type="color" name="color" id="color"
                           class="w-full h-10 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           value="{{ old('color', '#3B82F6') }}">
                    <p class="mt-1 text-xs text-gray-500">Cor para identificação visual</p>
                </div>

                <!-- Ordem -->
                <div>
                    <label for="order" class="block mb-2 text-sm font-medium text-gray-700">
                        Ordem
                    </label>
                    <input type="number" name="order" id="order" min="0"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           value="{{ old('order', 0) }}">
                    <p class="mt-1 text-xs text-gray-500">Ordem de exibição (menor primeiro)</p>
                </div>
            </div>

            <!-- Ícone -->
            <div>
                <label for="icon" class="block mb-2 text-sm font-medium text-gray-700">
                    Ícone (Opcional)
                </label>
                <input type="text" name="icon" id="icon"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       value="{{ old('icon') }}"
                       placeholder="Ex: laptop, briefcase, shopping-cart">
                <p class="mt-1 text-xs text-gray-500">Nome do ícone (caso use biblioteca de ícones)</p>
            </div>

            <!-- Status -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                       {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" class="ml-2 text-sm text-gray-700">
                    Categoria ativa
                </label>
            </div>

            <!-- Botões -->
            <div class="flex items-center justify-end pt-6 space-x-3 border-t border-gray-200">
                <a href="{{ route('categories.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                    Criar Categoria
                </button>
            </div>
        </form>
    </div>
</div>
@endsection