@extends('layouts.app')

@section('title', 'Serviço: ' . $service->name)

@section('content')
<div class="container px-4 mx-auto sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <div class="flex items-center space-x-3">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $service->name }}</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $service->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                    @if($service->category)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{  $service->category?->name ?? 'Sem categoria' }}
                        </span>
                    @endif
                </div>
                <p class="mt-2 text-gray-600">{{ $service->description ?: 'Nenhuma descrição disponível' }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('services.edit', $service) }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('services.index') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar à Lista
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 xl:grid-cols-3">
        <!-- Coluna Principal -->
        <div class="space-y-8 xl:col-span-2">
            <!-- Informações Gerais -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Informações Gerais</h3>
                            <p class="text-sm text-gray-600">Dados básicos do serviço</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Nome do Serviço</label>
                            <p class="p-3 text-sm text-gray-900 rounded-lg bg-gray-50">{{ $service->name }}</p>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Código</label>
                            <p class="p-3 font-mono text-sm text-gray-900 rounded-lg bg-gray-50">{{ $service->code ?? 'Não definido' }}</p>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Categoria</label>
                            <p class="p-3 text-sm text-gray-900 rounded-lg bg-gray-50">
                               {{  $service->category?->name ?? 'Sem categoria' }}
                            </p>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Nível de Complexidade</label>
                            <p class="p-3 text-sm text-gray-900 rounded-lg bg-gray-50">
                                {{ ucfirst($service->complexity_level ?? 'Não definido') }}
                            </p>
                        </div>
                    </div>

                    @if($service->description)
                    <div class="mt-6">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Descrição</label>
                        <div class="p-4 text-sm text-gray-900 rounded-lg bg-gray-50">
                            {!! nl2br(e($service->description)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Preços e Tempo -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Preços e Tempo</h3>
                            <p class="text-sm text-gray-600">Valores e estimativas de tempo</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @if($service->fixed_price > 0)
                        <div class="p-4 text-center border border-green-200 rounded-lg bg-green-50">
                            <div class="text-2xl font-bold text-green-600">{{ number_format($service->fixed_price, 2, ',', '.') }} MT</div>
                            <div class="mt-1 text-sm text-green-700">Preço Fixo</div>
                        </div>
                        @endif

                        @if($service->hourly_rate > 0)
                        <div class="p-4 text-center border border-blue-200 rounded-lg bg-blue-50">
                            <div class="text-2xl font-bold text-blue-600">{{ number_format($service->hourly_rate, 2, ',', '.') }} MT</div>
                            <div class="mt-1 text-sm text-blue-700">Por Hora</div>
                        </div>
                        @endif

                        @if($service->estimated_hours)
                        <div class="p-4 text-center border border-purple-200 rounded-lg bg-purple-50">
                            <div class="text-2xl font-bold text-purple-600">{{ $service->estimated_hours }}h</div>
                            <div class="mt-1 text-sm text-purple-700">Tempo Estimado</div>
                        </div>
                        @endif
                    </div>

                    @if($service->fixed_price > 0 && $service->estimated_hours)
                    <div class="p-4 mt-6 rounded-lg bg-gray-50">
                        <h4 class="mb-3 text-sm font-medium text-gray-700">Cálculo de Valor</h4>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="text-center">
                                <div class="text-lg font-bold text-gray-900">{{ number_format($service->fixed_price, 2, ',', '.') }} MT</div>
                                <div class="text-xs text-gray-500">Valor Total</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-gray-900">{{ $service->estimated_hours }}h</div>
                                <div class="text-xs text-gray-500">Tempo Total</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-gray-900">{{ number_format($service->fixed_price / $service->estimated_hours, 2, ',', '.') }} MT/h</div>
                                <div class="text-xs text-gray-500">Valor por Hora</div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($service->tax_rate)
                    <div class="mt-6">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Taxa de IVA</label>
                        <p class="p-3 text-sm text-gray-900 rounded-lg bg-gray-50">{{ $service->tax_rate }}%</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Requisitos e Observações -->
  @if($service->requirements || $service->deliverables)
<div class="bg-white border border-gray-200 shadow-sm rounded-xl">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center">
            <div class="p-2 mr-3 bg-orange-100 rounded-lg">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Requisitos e Entregáveis</h3>
                <p class="text-sm text-gray-600">Especificações técnicas e resultados esperados</p>
            </div>
        </div>
    </div>
    <div class="p-6 space-y-6">
        @if($service->requirements)
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Requisitos</label>
            <div class="p-4 text-sm text-gray-900 rounded-lg bg-gray-50">
                {{-- CORREÇÃO: Garantir que seja string --}}
                {!! nl2br(e(is_string($service->requirements) ? $service->requirements : '')) !!}
            </div>
        </div>
        @endif

        @if($service->deliverables)
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Entregáveis</label>
            <div class="p-4 text-sm text-gray-900 rounded-lg bg-gray-50">
                {{-- CORREÇÃO: Garantir que seja string --}}
                {!! nl2br(e(is_string($service->deliverables) ? $service->deliverables : '')) !!}
            </div>
        </div>
        @endif
    </div>
</div>
@endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status e Informações -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-indigo-100 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Informações</h3>
                            <p class="text-sm text-gray-600">Status e dados do sistema</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Status</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $service->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>

                    @if($service->code)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Código</span>
                        <span class="font-mono text-sm text-gray-900">{{ $service->code }}</span>
                    </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Criado em</span>
                        <span class="text-sm text-gray-900">{{ $service->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Atualizado em</span>
                        <span class="text-sm text-gray-900">{{ $service->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Estatísticas de Uso -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Estatísticas</h3>
                            <p class="text-sm text-gray-600">Uso em faturas e cotações</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">0</div>
                            <div class="text-xs text-gray-500">Vezes usado em faturas</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">0</div>
                            <div class="text-xs text-gray-500">Vezes usado em cotações</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">0,00 MT</div>
                            <div class="text-xs text-gray-500">Receita total gerada</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Ações Rápidas</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('services.edit', $service) }}"
                       class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar Serviço
                    </a>

                    @if(Route::has('services.duplicate'))
                    <a href="{{ route('services.duplicate', $service) }}"
                       class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Duplicar
                    </a>
                    @endif

                    <form action="{{ route('services.toggle-status', $service) }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium {{ $service->is_active ? 'text-red-700 bg-red-50 border-red-200 hover:bg-red-100' : 'text-green-700 bg-green-50 border-green-200 hover:bg-green-100' }} border rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            @if($service->is_active)
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                </svg>
                                Desativar
                            @else
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Ativar
                            @endif
                        </button>
                    </form>

                    <div class="pt-3 border-t">
                        <form action="{{ route('services.destroy', $service) }}" method="POST" class="w-full"
                              onsubmit="return confirm('Tem certeza que deseja excluir este serviço? Esta ação não pode ser desfeita.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-red-700 border border-red-200 rounded-md bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Excluir Serviço
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirmação para ações destrutivas
        const deleteForm = document.querySelector('form[onsubmit]');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                if (!confirm('Tem certeza que deseja excluir este serviço? Esta ação não pode ser desfeita.')) {
                    e.preventDefault();
                }
            });
        }

        // Toggle de status com confirmação
        const toggleForm = document.querySelector('form[action*="toggle-status"]');
        if (toggleForm) {
            toggleForm.addEventListener('submit', function(e) {
                const isActive = {{ $service->is_active ? 'true' : 'false' }};
                const action = isActive ? 'desativar' : 'ativar';

                if (!confirm(`Tem certeza que deseja ${action} este serviço?`)) {
                    e.preventDefault();
                }
            });
        }

        // Copiar código para clipboard
        const codeElements = document.querySelectorAll('.font-mono');
        codeElements.forEach(element => {
            element.style.cursor = 'pointer';
            element.title = 'Clique para copiar';

            element.addEventListener('click', function() {
                navigator.clipboard.writeText(this.textContent).then(() => {
                    // Feedback visual
                    const originalText = this.textContent;
                    this.textContent = 'Copiado!';
                    this.classList.add('text-green-600');

                    setTimeout(() => {
                        this.textContent = originalText;
                        this.classList.remove('text-green-600');
                    }, 1000);
                }).catch(err => {
                    console.error('Erro ao copiar:', err);
                });
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Hover effects para cards */
    .bg-white:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transition: box-shadow 0.3s ease;
    }

    /* Animação para botões */
    .transition-colors {
        transition-property: color, background-color, border-color;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    /* Estilo para código copiável */
    .font-mono:hover {
        background-color: #f3f4f6;
        padding: 2px 4px;
        border-radius: 4px;
    }

    /* Cards de preço */
    .bg-green-50 {
        background-color: rgb(240 253 244);
    }

    .bg-blue-50 {
        background-color: rgb(239 246 255);
    }

    .bg-purple-50 {
        background-color: rgb(250 245 255);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .xl\:grid-cols-3 {
            grid-template-columns: 1fr;
        }

        .xl\:col-span-2 {
            grid-column: span 1;
        }
    }
</style>
@endpush
@endsection

