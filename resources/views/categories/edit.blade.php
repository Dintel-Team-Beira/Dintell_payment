@extends('layouts.app')

@section('title', 'Editar Categoria')
@section('subtitle', 'Alterar informações da categoria')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Editar: {{ $category->name }}</h3>
        </div>

        <form action="{{ route('categories.update', $category) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Nome -->
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-700">
                    Nome da Categoria *
                </label>
                <input type="text" name="name" id="name" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                       value="{{ old('name', $category->name) }}">
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
                          class="w-full px-4 py-2 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $category->description) }}</textarea>
            </div>

            <!-- Tipo -->
            <div>
                <label for="type" class="block mb-2 text-sm font-medium text-gray-700">
                    Tipo *
                </label>
                <select name="type" id="type" required
                        class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="both" {{ old('type', $category->type) == 'both' ? 'selected' : '' }}>Produtos e Serviços</option>
                    <option value="product" {{ old('type', $category->type) == 'product' ? 'selected' : '' }}>Apenas Produtos</option>
                    <option value="service" {{ old('type', $category->type) == 'service' ? 'selected' : '' }}>Apenas Serviços</option>
                </select>
            </div>

            <!-- Categoria Pai -->
            <div>
                <label for="parent_id" class="block mb-2 text-sm font-medium text-gray-700">
                    Categoria Pai (Opcional)
                </label>
                <select name="parent_id" id="parent_id"
                        class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Nenhuma (Categoria Principal)</option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Cor -->
                <div>
                    <label for="color" class="block mb-2 text-sm font-medium text-gray-700">
                        Cor
                    </label>
                    <input type="color" name="color" id="color"
                           class="w-full h-10 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           value="{{ old('color', $category->color ?? '#3B82F6') }}">
                </div>

                <!-- Ordem -->
                <div>
                    <label for="order" class="block mb-2 text-sm font-medium text-gray-700">
                        Ordem
                    </label>
                    <input type="number" name="order" id="order" min="0"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           value="{{ old('order', $category->order) }}">
                </div>
            </div>

            <!-- Ícone -->
            <div>
                <label for="icon" class="block mb-2 text-sm font-medium text-gray-700">
                    Ícone (Opcional)
                </label>
                <input type="text" name="icon" id="icon"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       value="{{ old('icon', $category->icon) }}">
            </div>

            <!-- Status -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                       {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
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
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection