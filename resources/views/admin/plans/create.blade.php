@extends('layouts.admin')

@section('title', 'Criar Plano')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Criar Novo Plano</h1>
                    <p class="mt-1 text-sm text-gray-600">Configure um novo plano de subscrição para o sistema</p>
                </div>
                <div>
                    <a href="{{ route('admin.plans.index') }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Voltar para Planos
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-8">
        <form action="{{ route('admin.plans.store') }}" method="POST" id="planForm">
            @csrf

            <div class="grid grid-cols-1 gap-8 xl:grid-cols-3">
                <!-- Coluna Principal -->
                <div class="space-y-8 xl:col-span-2">

                    <!-- Informações Básicas -->
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="p-2 mr-3 bg-blue-100 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Informações Básicas</h3>
                                    <p class="text-sm text-gray-600">Dados principais do plano</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nome do Plano *</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="slug" class="block text-sm font-medium text-gray-700">Slug (opcional)</label>
                                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('slug') border-red-300 @enderror"
                                           placeholder="sera-gerado-automaticamente">
                                    <p class="mt-1 text-xs text-gray-500">Deixe vazio para gerar automaticamente</p>
                                    @error('slug')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                                <textarea name="description" id="description" rows="3"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-300 @enderror"
                                          placeholder="Descrição detalhada do plano...">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700">Preço *</label>
                                    <input type="number" name="price" id="price" value="{{ old('price', 0) }}" min="0" step="0.01" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('price') border-red-300 @enderror">
                                    @error('price')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="currency" class="block text-sm font-medium text-gray-700">Moeda *</label>
                                    <select name="currency" id="currency" required
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('currency') border-red-300 @enderror">
                                        @foreach($currencies as $code => $name)
                                            <option value="{{ $code }}" {{ old('currency', 'MZN') == $code ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('currency')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="billing_cycle" class="block text-sm font-medium text-gray-700">Ciclo de Cobrança *</label>
                                    <select name="billing_cycle" id="billing_cycle" required
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('billing_cycle') border-red-300 @enderror">
                                        @foreach($billingCycles as $value => $label)
                                            <option value="{{ $value }}" {{ old('billing_cycle', 'monthly') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('billing_cycle')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Limitações do Plano -->
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="p-2 mr-3 bg-yellow-100 rounded-lg">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Limitações do Plano</h3>
                                    <p class="text-sm text-gray-600">Configure os limites de uso (deixe vazio para ilimitado)</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                                <div>
                                    <label for="max_users" class="block text-sm font-medium text-gray-700">Máximo de Usuários</label>
                                    <input type="number" name="max_users" id="max_users" value="{{ old('max_users') }}" min="1"
                                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                                           placeholder="Ilimitado">
                                    @error('max_users')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="max_companies" class="block text-sm font-medium text-gray-700">Máximo de Empresas</label>
                                    <input type="number" name="max_companies" id="max_companies" value="{{ old('max_companies') }}" min="1"
                                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                                           placeholder="Ilimitado">
                                    @error('max_companies')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="max_invoices_per_month" class="block text-sm font-medium text-gray-700">Faturas por Mês</label>
                                    <input type="number" name="max_invoices_per_month" id="max_invoices_per_month" value="{{ old('max_invoices_per_month') }}" min="1"
                                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                                           placeholder="Ilimitado">
                                    @error('max_invoices_per_month')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="max_clients" class="block text-sm font-medium text-gray-700">Máximo de Clientes</label>
                                    <input type="number" name="max_clients" id="max_clients" value="{{ old('max_clients') }}" min="1"
                                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                                           placeholder="Ilimitado">
                                    @error('max_clients')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="max_products" class="block text-sm font-medium text-gray-700">Máximo de Produtos</label>
                                    <input type="number" name="max_products" id="max_products" value="{{ old('max_products') }}" min="1"
                                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                                           placeholder="Ilimitado">
                                    @error('max_products')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="max_storage_mb" class="block text-sm font-medium text-gray-700">Armazenamento (MB)</label>
                                    <input type="number" name="max_storage_mb" id="max_storage_mb" value="{{ old('max_storage_mb') }}" min="1"
                                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                                           placeholder="Ilimitado">
                                    @error('max_storage_mb')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Funcionalidades -->
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="p-2 mr-3 bg-green-100 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Funcionalidades Incluídas</h3>
                                    <p class="text-sm text-gray-600">Selecione as funcionalidades disponíveis neste plano</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div id="featuresContainer" class="space-y-3">
                                @foreach($availableFeatures as $feature)
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="features[]" value="{{ $feature }}" id="feature_{{ $loop->index }}"
                                                   {{ in_array($feature, old('features', [])) ? 'checked' : '' }}
                                                   class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="feature_{{ $loop->index }}" class="font-medium text-gray-700">{{ $feature }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                <button type="button" onclick="addCustomFeature()"
                                        class="inline-flex items-center px-3 py-1 text-sm font-medium text-green-700 border border-green-200 rounded-md bg-green-50 hover:bg-green-100">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Adicionar Funcionalidade Personalizada
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Período de Teste -->
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="p-2 mr-3 bg-purple-100 rounded-lg">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Período de Teste</h3>
                                    <p class="text-sm text-gray-600">Configure se o plano oferece período de teste gratuito</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="has_trial" id="has_trial" value="1"
                                           {{ old('has_trial') ? 'checked' : '' }}
                                           class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="has_trial" class="font-medium text-gray-700">Oferecer período de teste gratuito</label>
                                    <p class="text-gray-500">Permitir que usuários testem o plano antes de pagar</p>
                                </div>
                            </div>

                            <div id="trialSettings" class="hidden">
                                <div class="max-w-xs">
                                    <label for="trial_days" class="block text-sm font-medium text-gray-700">Duração do Teste (dias)</label>
                                    <input type="number" name="trial_days" id="trial_days" value="{{ old('trial_days', 0) }}" min="0" max="365"
                                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                    @error('trial_days')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">

                    <!-- Preview do Plano -->
                    <div class="sticky bg-white border border-gray-200 shadow-sm rounded-xl top-8">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Preview do Plano</h3>
                        </div>
                        <div class="p-6">
                            <div id="planPreview" class="text-center">
                                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-lg">
                                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900" id="previewName">Nome do Plano</h4>
                                <p class="mb-4 text-sm text-gray-600" id="previewDescription">Descrição do plano aparecerá aqui</p>
                                <div class="mb-2 text-3xl font-bold text-blue-600" id="previewPrice">0,00 MT</div>
                                <div class="mb-4 text-sm text-gray-500" id="previewCycle">por mês</div>
                                <div class="text-xs text-gray-400" id="previewLimits">Sem limitações definidas</div>
                            </div>
                        </div>
                    </div>

                    <!-- Configurações de Aparência -->
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Aparência</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700">Cor do Plano</label>
                                <div class="grid grid-cols-5 gap-2 mt-2">
                                    @foreach($colors as $hex => $name)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="color" value="{{ $hex }}"
                                                   {{ old('color', '#3B82F6') == $hex ? 'checked' : '' }}
                                                   class="sr-only">
                                            <div class="w-8 h-8 border-2 border-gray-300 rounded-full ring-2 ring-transparent hover:ring-gray-400"
                                                 style="background-color: {{ $hex }}"></div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label for="icon" class="block text-sm font-medium text-gray-700">Ícone</label>
                                <select name="icon" id="icon"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Selecione um ícone</option>
                                    @foreach($icons as $value => $label)
                                        <option value="{{ $value }}" {{ old('icon') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-start space-y-4">
                                <div class="w-full">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_popular" id="is_popular" value="1"
                                               {{ old('is_popular') ? 'checked' : '' }}
                                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <label for="is_popular" class="ml-2 text-sm font-medium text-gray-700">Marcar como Popular</label>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Destacar este plano como recomendado</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-full">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_active" id="is_active" value="1"
                                               {{ old('is_active', true) ? 'checked' : '' }}
                                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Plano Ativo</label>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Plano disponível para subscrição</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="p-6">
                            <div class="space-y-3">
                                <button type="submit"
                                        class="inline-flex items-center justify-center w-full px-6 py-3 text-sm font-medium text-white transition-colors bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Criar Plano
                                </button>

                                <a href="{{ route('admin.plans.index') }}"
                                   class="inline-flex items-center justify-center w-full px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Preview em tempo real
function updatePreview() {
    const name = document.getElementById('name').value || 'Nome do Plano';
    const description = document.getElementById('description').value || 'Descrição do plano aparecerá aqui';
    const price = parseFloat(document.getElementById('price').value) || 0;
    const currency = document.getElementById('currency').value;
    const billingCycle = document.getElementById('billing_cycle').value;

    document.getElementById('previewName').textContent = name;
    document.getElementById('previewDescription').textContent = description;

    // Formatação do preço
    const formattedPrice = new Intl.NumberFormat('pt-MZ', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(price);

    const currencySymbol = currency === 'MZN' ? 'MT' : currency;
    document.getElementById('previewPrice').textContent = `${formattedPrice} ${currencySymbol}`;

    // Ciclo de cobrança
    const cycleText = {
        'monthly': 'por mês',
        'quarterly': 'por trimestre',
        'yearly': 'por ano'
    };
    document.getElementById('previewCycle').textContent = cycleText[billingCycle] || 'por mês';

    // Limitações
    const limits = [];
    const maxUsers = document.getElementById('max_users').value;
    const maxCompanies = document.getElementById('max_companies').value;
    const maxInvoices = document.getElementById('max_invoices_per_month').value;

    if (maxUsers) limits.push(`${maxUsers} usuários`);
    if (maxCompanies) limits.push(`${maxCompanies} empresas`);
    if (maxInvoices) limits.push(`${maxInvoices} faturas/mês`);

    document.getElementById('previewLimits').textContent = limits.length > 0 ? limits.join(', ') : 'Sem limitações definidas';
}

// Toggle período de teste
function toggleTrialSettings() {
    const hasTrial = document.getElementById('has_trial').checked;
    const trialSettings = document.getElementById('trialSettings');

    if (hasTrial) {
        trialSettings.classList.remove('hidden');
    } else {
        trialSettings.classList.add('hidden');
        document.getElementById('trial_days').value = 0;
    }
}

// Adicionar funcionalidade personalizada
function addCustomFeature() {
    const featureName = prompt('Nome da funcionalidade:');
    if (featureName && featureName.trim()) {
        const container = document.getElementById('featuresContainer');
        const index = container.children.length;

        const featureDiv = document.createElement('div');
        featureDiv.className = 'flex items-start';
        featureDiv.innerHTML = `
            <div class="flex items-center h-5">
                <input type="checkbox" name="features[]" value="${featureName.trim()}" id="feature_${index}" checked
                       class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
            </div>
            <div class="flex-1 ml-3 text-sm">
                <label for="feature_${index}" class="font-medium text-gray-700">${featureName.trim()}</label>
            </div>
            <button type="button" onclick="this.closest('.flex').remove()"
                    class="ml-2 text-red-600 hover:text-red-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        `;

        container.appendChild(featureDiv);
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Preview em tempo real
    const previewFields = ['name', 'description', 'price', 'currency', 'billing_cycle', 'max_users', 'max_companies', 'max_invoices_per_month'];
    previewFields.forEach(field => {
        const element = document.getElementById(field);
        if (element) {
            element.addEventListener('input', updatePreview);
            element.addEventListener('change', updatePreview);
        }
    });

    // Toggle período de teste
    document.getElementById('has_trial').addEventListener('change', toggleTrialSettings);

    // Atualização inicial do preview
    updatePreview();
    toggleTrialSettings();

    // Cor do plano - atualizar preview
    document.querySelectorAll('input[name="color"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Atualizar cor no preview se necessário
        });
    });
});
</script>
@endpush
@endsection
