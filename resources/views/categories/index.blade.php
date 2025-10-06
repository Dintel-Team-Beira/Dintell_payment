@extends('layouts.app')

@section('title', 'Categorias')
@section('subtitle', 'Gerencie as categorias de produtos e serviços')

@section('header-actions')
<div class="flex space-x-3">
    <a href="{{ route('categories.create') }}" 
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nova Categoria
    </a>
</div>
@endsection

@section('content')
<!-- Estatísticas -->
<div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
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
                <h3 class="text-sm font-medium text-gray-900">Total de Categorias</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_categories'] }}</p>
            </div>
        </div>
    </div>

    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-emerald-100">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Ativas</h3>
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['active_categories'] }}</p>
            </div>
        </div>
    </div>

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
                <p class="text-2xl font-bold text-purple-600">{{ $stats['product_categories'] }}</p>
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
                <p class="text-2xl font-bold text-indigo-600">{{ $stats['service_categories'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filtros e Pesquisa -->
<div class="mb-8 bg-white border border-gray-200 shadow-sm rounded-xl" x-data="{ showFilters: {{ request()->hasAny(['status', 'type', 'level']) ? 'true' : 'false' }} }">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Pesquisar e Filtrar</h3>
        <button @click="showFilters = !showFilters"
                class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"/>
            </svg>
            <span x-text="showFilters ? 'Ocultar Filtros' : 'Mostrar Filtros'"></span>
        </button>
    </div>

    <div class="p-6">
        <!-- Barra de Pesquisa -->
        <div class="mb-6">
            <form method="GET" action="{{ route('categories.index') }}" class="relative">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="type" value="{{ request('type') }}">
                <input type="hidden" name="level" value="{{ request('level') }}">

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" placeholder="Pesquisar por nome ou descrição..."
                           class="w-full py-3 pl-10 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           value="{{ request('search') }}">

                    @if(request('search'))
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <a href="{{ route('categories.index', request()->except('search')) }}"
                               class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>

        <!-- Filtros Avançados -->
        <div x-show="showFilters" x-collapse>
            <form method="GET" action="{{ route('categories.index') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label for="status" class="block mb-2 text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativas</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativas</option>
                        </select>
                    </div>

                    <div>
                        <label for="type" class="block mb-2 text-sm font-medium text-gray-700">Tipo</label>
                        <select name="type" id="type" class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="product" {{ request('type') == 'product' ? 'selected' : '' }}>Produtos</option>
                            <option value="service" {{ request('type') == 'service' ? 'selected' : '' }}>Serviços</option>
                            <option value="both" {{ request('type') == 'both' ? 'selected' : '' }}>Ambos</option>
                        </select>
                    </div>

                    <div>
                        <label for="level" class="block mb-2 text-sm font-medium text-gray-700">Nível</label>
                        <select name="level" id="level" class="w-full p-2 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="main" {{ request('level') == 'main' ? 'selected' : '' }}>Principais</option>
                            <option value="sub" {{ request('level') == 'sub' ? 'selected' : '' }}>Subcategorias</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center mt-4 space-x-2">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                        Filtrar
                    </button>
                    <a href="{{ route('categories.index') }}" class="px-4 py-2 text-sm font-medium text-white transition-colors bg-gray-500 rounded-lg hover:bg-gray-600">
                        Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lista de Categorias -->
<div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Categorias</h3>
            @if($categories->total() > 0)
                <p class="text-sm text-gray-500">
                    Mostrando {{ $categories->firstItem() }} a {{ $categories->lastItem() }} de {{ $categories->total() }} categorias
                </p>
            @endif
        </div>
    </div>

    @if($categories->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nome</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Categoria Pai</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Produtos</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Serviços</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($categories as $category)
                <tr class="transition-colors hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($category->color)
                                <div class="flex-shrink-0 w-8 h-8 mr-3 rounded-lg" style="background-color: {{ $category->color }}"></div>
                            @endif
                            <div>
                                <a href="{{ route('categories.show', $category) }}" class="text-sm font-medium text-blue-600 hover:text-blue-900">
                                    {{ $category->name }}
                                </a>
                                @if($category->description)
                                    <div class="text-xs text-gray-500">{{ Str::limit($category->description, 50) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
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
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$category->type] }}">
                            {{ $typeLabels[$category->type] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                        @if($category->parent)
                            <span class="text-gray-600">{{ $category->parent->name }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-center text-gray-900 whitespace-nowrap">
                        {{ $category->products_count }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-center text-gray-900 whitespace-nowrap">
                        {{ $category->services_count }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <button onclick="toggleStatus({{ $category->id }})"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }} transition-colors">
                            {{ $category->is_active ? 'Ativa' : 'Inativa' }}
                        </button>
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="{{ route('categories.show', $category) }}"
                               class="p-2 text-blue-600 transition-colors rounded-lg bg-blue-50 hover:bg-blue-100"
                               title="Ver detalhes">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            <a href="{{ route('categories.edit', $category) }}"
                               class="p-2 text-yellow-600 transition-colors rounded-lg bg-yellow-50 hover:bg-yellow-100"
                               title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            <button onclick="deleteCategory({{ $category->id }})"
                                    class="p-2 text-red-600 transition-colors rounded-lg bg-red-50 hover:bg-red-100"
                                    title="Excluir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200">
        <div class="flex items-center text-sm text-gray-700">
            <span>Mostrar</span>
            <select onchange="changePerPage(this.value)" class="mx-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
            </select>
            <span>registos por página</span>
        </div>

        {{ $categories->withQueryString()->links() }}
    </div>

    @else
    <div class="px-6 py-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
        </svg>

        @if(request()->hasAny(['search', 'status', 'type', 'level']))
            <h3 class="mt-4 text-lg font-medium text-gray-900">Nenhuma categoria encontrada</h3>
            <p class="mt-2 text-sm text-gray-500">Tente ajustar os filtros ou termo de pesquisa.</p>
            <div class="mt-6">
                <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Limpar filtros
                </a>
            </div>
        @else
            <h3 class="mt-4 text-lg font-medium text-gray-900">Nenhuma categoria cadastrada</h3>
            <p class="mt-2 text-sm text-gray-500">Comece criando sua primeira categoria de produtos ou serviços.</p>
            <div class="mt-6">
                <a href="{{ route('categories.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nova Categoria
                </a>
            </div>
        @endif
    </div>
    @endif
</div>

@push('scripts')
<script>
function changePerPage(perPage) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

function toggleStatus(categoryId) {
    fetch(`/categories/${categoryId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Erro ao alterar status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao alterar status', 'error');
    });
}

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

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;

    const colors = {
        'success': 'bg-green-500 text-white',
        'error': 'bg-red-500 text-white',
        'info': 'bg-blue-500 text-white',
        'warning': 'bg-yellow-500 text-white'
    };

    notification.className += ` ${colors[type]}`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => notification.classList.remove('translate-x-full'), 100);
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>
@endpush
@endsection