@extends('layouts.app')

@section('title', 'Novo Plano')

@section('content')
<div class="max-w-8xl mx-auto">
    <form action="{{ route('plans.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-6">Informações do Plano</h2>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div>
                    {{-- user_id type hidden --}}

                    <label class="block text-sm font-medium text-gray-700">Nome do Plano *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Slug *</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('slug')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    @error('description')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Preço (MT) *</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('price')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Taxa de Instalação (MT)</label>
                    <input type="number" step="0.01" name="setup_fee" value="{{ old('setup_fee', 0) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('setup_fee')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Ciclo de Cobrança *</label>
                    <select name="billing_cycle" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="monthly" {{ old('billing_cycle') === 'monthly' ? 'selected' : '' }}>Mensal</option>
                        <option value="quarterly" {{ old('billing_cycle') === 'quarterly' ? 'selected' : '' }}>Trimestral</option>
                        <option value="yearly" {{ old('billing_cycle') === 'yearly' ? 'selected' : '' }}>Anual</option>
                        <option value="lifetime" {{ old('billing_cycle') === 'lifetime' ? 'selected' : '' }}>Vitalício</option>
                    </select>
                    @error('billing_cycle')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Dias do Ciclo *</label>
                    <input type="number" name="billing_cycle_days" value="{{ old('billing_cycle_days', 30) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('billing_cycle_days')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-6">Limites e Recursos</h2>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Máx. Domínios *</label>
                    <input type="number" name="max_domains" value="{{ old('max_domains', 1) }}" required min="1"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('max_domains')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Storage (GB) *</label>
                    <input type="number" name="max_storage_gb" value="{{ old('max_storage_gb', 1) }}" required min="1"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('max_storage_gb')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Bandwidth (GB) *</label>
                    <input type="number" name="max_bandwidth_gb" value="{{ old('max_bandwidth_gb', 10) }}" required min="1"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('max_bandwidth_gb')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Dias de Trial</label>
                    <input type="number" name="trial_days" value="{{ old('trial_days', 0) }}" min="0"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('trial_days')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-6">Funcionalidades</h2>

            <div id="features-container">
                <div class="feature-item flex items-center space-x-2 mb-2">
                    <input type="text" name="features[]" placeholder="Ex: Suporte 24/7"
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <button type="button" onclick="removeFeature(this)" class="text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="button" onclick="addFeature()" class="mt-2 text-blue-600 hover:text-blue-800 text-sm">
                + Adicionar Funcionalidade
            </button>
        </div>

        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-6">Configurações</h2>

            <div class="space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">Plano ativo</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">Plano em destaque</label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Cor do Tema</label>
                    <input type="color" name="color_theme" value="{{ old('color_theme', '#3B82F6') }}"
                           class="mt-1 h-10 w-20 rounded border border-gray-300">
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('plans.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                Criar Plano
            </button>
        </div>
    </form>
</div>

<script>
function addFeature() {
    const container = document.getElementById('features-container');
    const newFeature = document.createElement('div');
    newFeature.className = 'feature-item flex items-center space-x-2 mb-2';
    newFeature.innerHTML = `
        <input type="text" name="features[]" placeholder="Ex: Suporte 24/7"
               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <button type="button" onclick="removeFeature(this)" class="text-red-600 hover:text-red-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    container.appendChild(newFeature);
}

function removeFeature(button) {
    button.closest('.feature-item').remove();
}
</script>
@endsection