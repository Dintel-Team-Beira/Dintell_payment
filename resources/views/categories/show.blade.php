@extends('layouts.app')

@section('title', $category->name)
@section('subtitle', 'Detalhes da categoria')

@section('header-actions')
<div class="flex space-x-3">
    <a href="{{ route('categories.edit', $category) }}"
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Editar
    </a>

    <button onclick="deleteCategory({{ $category->id }})"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        Excluir
    </button>
</div>
@endsection

@section('content')
<!-- Informações Principais -->
<div class="mb-8 overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Informações da Categoria</h3>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Nome e Cor -->
            <div>
                <div class="flex items-center mb-4">
                    @if($category->color)
                        <div class="flex-shrink-0 w-12 h-12 mr-4 rounded-lg" style="background-color: {{ $category->color }}"></div>
                    @endif
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nome</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $category->name }}</p>
                    </div>
                </div>

                @if($category->description)
                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-500">Descrição</label>
                    <p class="text-gray-900">{{ $category->description }}</p>
                </div>
                @endif

                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-500">Tipo</label>
                    <div class="mt-1">
                        @php
                            $typeColors = [
                                'product' => 'bg-purple-100 text-purple-800',
                                'service' => 'bg-indigo-100 text-indigo-800',
                                'both' => 'bg-blue-100 text-blue-800'
                            ];
                            $typeLabels = [
                                'product' => 'Produto',
                                'service' => 'Serviço',
                                'both' => 'Ambos'
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full {{ $typeColors[$category->type] }}">
                            {{ $typeLabels[$category->type] }}
                        </span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-500">Status</label>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $category->is_active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Hierarquia e Metadados -->
            <div>
                @if($category->parent)
                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-500">Categoria Pai</label>
                    <p class="text-gray-900">
                        <a href="{{ route('categories.show', $category->parent) }}" class="text-blue-600 hover:text-blue-800">
                            {{ $category->parent->name }}
                        </a>
                    </p>
                </div>
                @endif

                @if($category->children->count() > 0)
                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-500">Subcategorias ({{ $category->children->count() }})</label>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($category->children as $child)
                            <a href="{{ route('categories.show', $child) }}"
                               class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-700 transition-colors bg-gray-100 rounded-full hover:bg-gray-200">
                                {{ $child->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-500">Caminho Completo</label>
                    <p class="text-gray-900">{{ $category->full_name }}</p>
                </div>

                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-500">Nível de Profundidade</label>
                    <p class="text-gray-900">{{ $category->depth }}</p>
                </div>

                @if($category->icon)
                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-500">Ícone</label>
                    <p class="text-gray-900">{{ $category->icon }}</p>
                </div>
                @endif

                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-500">Ordem</label>
                    <p class="text-gray-900">{{ $category->order }}</p>
                </div>
            </div>
        </div>

        <!-- Datas -->
        <div class="grid grid-cols-1 gap-6 pt-6 mt-6 border-t border-gray-200 md:grid-cols-2">
            <div>
                <label class="text-sm font-medium text-gray-500">Criada em</label>
                <p class="text-gray-900">{{ $category->created_at->format('d/m/Y H:i') }}</p>
                <p class="text-xs text-gray-500">{{ $category->created_at->diffForHumans() }}</p>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-500">Última atualização</label>
                <p class="text-gray-900">{{ $category->updated_at->format('d/m/Y H:i') }}</p>
                <p class="text-xs text-gray-500">{{ $category->updated_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas -->
<div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-3">
    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Produtos</h3>
                <p class="text-2xl font-bold text-purple-600">{{ $category->products->count() }}</p>
            </div>
        </div>
    </div>

    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-indigo-100 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Serviços</h3>
                <p class="text-2xl font-bold text-indigo-600">{{ $category->services->count() }}</p>
            </div>
        </div>
    </div>

    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Subcategorias</h3>
                <p class="text-2xl font-bold text-blue-600">{{ $category->children->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Tabs: Produtos e Serviços -->
<div class="bg-white border border-gray-200 shadow-sm rounded-xl" x-data="{ tab: 'products' }">
    <!-- Tab Headers -->
    <div class="border-b border-gray-200">
        <nav class="flex px-6 -mb-px space-x-8">
            <button @click="tab = 'products'"
                    :class="tab === 'products' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 text-sm font-medium border-b-2 whitespace-nowrap">
                Produtos ({{ $products->total() }})
            </button>
            <button @click="tab = 'services'"
                    :class="tab === 'services' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 text-sm font-medium border-b-2 whitespace-nowrap">
                Serviços ({{ $services->total() }})
            </button>
        </nav>
    </div>

    <!-- Tab Content: Produtos -->
    <div x-show="tab === 'products'" class="p-6">
        @if($products->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Código</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Preço</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Stock</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($products as $product)
                    <tr class="transition-colors hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $product->code }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                            @if($product->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($product->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-right text-gray-900 whitespace-nowrap">
                            {{ number_format($product->price, 2, ',', '.') }} MT
                        </td>
                        <td class="px-6 py-4 text-sm text-center text-gray-900 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->stock_quantity <= $product->min_stock_level ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ $product->stock_quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <a href="{{ route('products.show', $product) }}"
                               class="text-blue-600 hover:text-blue-900">
                                Ver
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginação Produtos -->
        <div class="mt-4">
            {{ $products->links() }}
        </div>
        @else
        <div class="py-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum produto</h3>
            <p class="mt-1 text-sm text-gray-500">Esta categoria ainda não possui produtos.</p>
        </div>
        @endif
    </div>

    <!-- Tab Content: Serviços -->
    <div x-show="tab === 'services'" class="p-6">
        @if($services->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Código</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Preço</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Duração</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($services as $service)
                    <tr class="transition-colors hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $service->code }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $service->name }}</div>
                            @if($service->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($service->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-right text-gray-900 whitespace-nowrap">
                            @if($service->fixed_price)
                                {{ number_format($service->fixed_price, 2, ',', '.') }} MT
                            @elseif($service->hourly_rate)
                                {{ number_format($service->hourly_rate, 2, ',', '.') }} MT/h
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-center text-gray-900 whitespace-nowrap">
                            @if($service->estimated_hours)
                                {{ $service->estimated_hours }}h
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $service->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <a href="{{ route('services.show', $service) }}"
                               class="text-blue-600 hover:text-blue-900">
                                Ver
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginação Serviços -->
        <div class="mt-4">
            {{ $services->links() }}
        </div>
        @else
        <div class="py-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum serviço</h3>
            <p class="mt-1 text-sm text-gray-500">Esta categoria ainda não possui serviços.</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function deleteCategory(categoryId) {
    if (!confirm('Tem certeza que deseja excluir esta categoria? Esta ação não pode ser desfeita.')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/categories/${categoryId}`;
    form.innerHTML = `
        @csrf
        @method('DELETE')
    `;
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
@endsection