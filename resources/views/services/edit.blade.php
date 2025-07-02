{{-- resources/views/services/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Serviço')

@section('content')
<div class="sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Editar Serviço</h1>
                <p class="mt-2 text-gray-600">Atualize as informações do serviço</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('services.index') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar
                </a>
                <a href="{{ route('services.show', $service) }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Visualizar
                </a>
            </div>
        </div>
    </div>

    <!-- Grid Principal -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Coluna Principal -->
        <div class="space-y-8 lg:col-span-2">
            <!-- Descrição -->
            @if($service->description)
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Descrição</h3>
                </div>
                <div class="p-6">
                    <p class="leading-relaxed text-gray-700">{{ $service->description }}</p>
                </div>
            </div>
            @endif

            <!-- Requisitos Técnicos -->
            @if($service->requirements)
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-orange-100 rounded-lg">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Requisitos Técnicos</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="prose-sm prose max-w-none">
                        <p class="text-gray-700 whitespace-pre-line">{{ $service->requirements }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Tags -->
            @if($service->tags)
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Tags</h3>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $service->tags) as $tag)
                            <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-indigo-800 bg-indigo-100 rounded-full">
                                {{ trim($tag) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Estatísticas de Uso -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Estatísticas de Uso</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ $service->quote_items_count ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Vezes em Cotações</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600">{{ $service->invoice_items_count ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Vezes Faturado</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Informações de Preço -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-green-100 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Preços</h3>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    @if($service->hourly_rate)
                    <div class="flex items-center justify-between p-3 border border-green-200 rounded-lg bg-green-50">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium text-green-800">Por Hora</span>
                        </div>
                        <span class="text-lg font-bold text-green-800">MT {{ number_format($service->hourly_rate, 2) }}</span>
                    </div>
                    @endif

                    @if($service->fixed_price)
                    <div class="flex items-center justify-between p-3 border border-blue-200 rounded-lg bg-blue-50">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                            <span class="text-sm font-medium text-blue-800">Preço Fixo</span>
                        </div>
                        <span class="text-lg font-bold text-blue-800">MT {{ number_format($service->fixed_price, 2) }}</span>
                    </div>
                    @endif

                    @if($service->estimated_hours)
                    <div class="p-3 border border-gray-200 rounded-lg bg-gray-50">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Horas Estimadas</span>
                            <span class="text-sm font-bold text-gray-900">{{ $service->estimated_hours }}h</span>
                        </div>
                        @if($service->hourly_rate && $service->estimated_hours)
                        <div class="pt-2 mt-2 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Custo Estimado</span>
                                <span class="text-sm font-bold text-purple-600">MT {{ number_format($service->hourly_rate * $service->estimated_hours, 2) }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Informações do Sistema -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informações do Sistema</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Criado em:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $service->created_at}}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Última atualização:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $service->updated_atcreated_at}}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">ID do Sistema:</span>
                        <span class="font-mono text-sm text-gray-900">#{{ $service->id }}</span>
                    </div>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Ações Rápidas</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('quotes.create', ['service_id' => $service->id]) }}"
                       class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover:bg-blue-100">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Nova Cotação
                    </a>

                    <button onclick="toggleStatus()"
                            class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium rounded-md
                                {{ $service->is_active ? 'text-yellow-700 border-yellow-200 bg-yellow-50 hover:bg-yellow-100' : 'text-green-700 border-green-200 bg-green-50 hover:bg-green-100' }}">
                        @if($service->is_active)
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                            </svg>
                            Desativar Serviço
                        @else
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Ativar Serviço
                        @endif
                    </button>

                    <button onclick="exportService()"
                            class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-md hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Exportar Dados
                    </button>

                    @if(!$service->quote_items_count && !$service->invoice_items_count)
                    <button onclick="deleteService()"
                            class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-red-700 border border-red-200 rounded-md bg-red-50 hover:bg-red-100">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Excluir Serviço
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Duplicar serviço
function duplicateService() {
    if (confirm('Deseja criar uma cópia deste serviço?')) {
        fetch(`/servicos/{{ $service->id }}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = `/servicos/${data.service.id}/edit`;
            } else {
                alert('Erro ao duplicar serviço');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao duplicar serviço');
        });
    }
}

// Toggle status
function toggleStatus() {
    const currentStatus = {{ $service->is_active ? 'true' : 'false' }};
    const action = currentStatus ? 'desativar' : 'ativar';

    if (confirm(`Deseja ${action} este serviço?`)) {
        fetch(`/api/services/{{ $service->id }}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                is_active: !currentStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao alterar status do serviço');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao alterar status do serviço');
        });
    }
}

// Exportar serviço
function exportService() {
    window.open(`/servicos/{{ $service->id }}/export`, '_blank');
}

// Deletar serviço
function deleteService() {
    if (confirm('Tem certeza que deseja excluir este serviço? Esta ação não pode ser desfeita.')) {
        fetch(`/servicos/{{ $service->id }}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/servicos';
            } else {
                alert(data.message || 'Erro ao excluir serviço');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao excluir serviço');
        });
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // E para editar
    if (e.key === 'e' && !e.ctrlKey && !e.metaKey) {
        const activeElement = document.activeElement;
        if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
            window.location.href = '{{ route("services.edit", $service) }}';
        }
    }

    // D para duplicar
    if (e.key === 'd' && !e.ctrlKey && !e.metaKey) {
        const activeElement = document.activeElement;
        if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
            duplicateService();
        }
    }
});
</script>

<style>
/* Hover effects */
.hover\:scale-105:hover {
    transform: scale(1.05);
}

/* Smooth transitions */
* {
    transition: all 0.2s ease-in-out;
}

/* Badge animations */
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .5;
    }
}
</style>
@endsection

    <!-- Form Container -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <form id="serviceForm" action="{{ route('services.update', $service) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

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
                        <p class="text-sm text-gray-600">Atualize os dados do serviço</p>
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
                               value="{{ old('name', $service->name) }}"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Código -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Código do Serviço
                        </label>
                        <input type="text"
                               name="code"
                               id="service_code"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               value="{{ old('code', $service->code) }}">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                                <option value="{{ $key }}" {{ old('category', $service->category) == $key ? 'selected' : '' }}>
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
                              placeholder="Descreva detalhadamente o que este serviço inclui...">{{ old('description', $service->description) }}</textarea>
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
                                       value="{{ old('hourly_rate', $service->hourly_rate) }}"
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
                                       value="{{ old('fixed_price', $service->fixed_price) }}"
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
                                    <option value="{{ $key }}" {{ old('complexity_level', $service->complexity_level) == $key ? 'selected' : '' }}>
                                        {{ $level }}
                                    </option>
                                @endforeach
                            </select>
                            @error('complexity_level')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                                       value="{{ old('estimated_hours', $service->estimated_hours) }}"
                                       step="0.5"
                                       min="0">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">horas</span>
                            </div>
                            @error('estimated_hours')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Status
                            </label>
                            <div class="flex items-center space-x-6">
                                <label class="flex items-center">
                                    <input type="radio"
                                           name="is_active"
                                           value="1"
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2"
                                           {{ old('is_active', $service->is_active) == '1' ? 'checked' : '' }}>
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
                                           {{ old('is_active', $service->is_active) == '0' ? 'checked' : '' }}>
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
                                      placeholder="Liste os requisitos técnicos, habilidades necessárias, ferramentas, etc.">{{ old('requirements', $service->requirements) }}</textarea>
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
                                   value="{{ old('tags', $service->tags) }}">
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
                        Última atualização: {{ $service->updated_at->format('d/m/Y H:i') }}
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('services.index') }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancelar
                        </a>
                        <button type="submit"
                                id="submitButton"
                                class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Atualizar Serviço
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Validação de preços
function validatePrices() {
    const hourlyRate = parseFloat(document.getElementById('hourly_rate').value) || 0;
    const fixedPrice = parseFloat(document.getElementById('fixed_price').value) || 0;

    if (hourlyRate <= 0 && fixedPrice <= 0) {
        alert('Você deve informar pelo menos um preço (por hora ou fixo)');
        return false;
    }
    return true;
}

// Form submission
document.getElementById('serviceForm').addEventListener('submit', function(e) {
    if (!validatePrices()) {
        e.preventDefault();
        return;
    }

    const submitButton = document.getElementById('submitButton');
    const originalText = submitButton.innerHTML;

    submitButton.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Atualizando...';
    submitButton.disabled = true;
});
</script>
@endsection

