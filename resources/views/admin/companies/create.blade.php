@extends('layouts.admin')

@section('title', 'Nova Empresa')

@section('header-actions')
<div class="flex items-center gap-x-4">
    <a href="{{ route('admin.companies.index') }}"
       class="flex items-center px-3 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Voltar
    </a>
</div>
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Criar Nova Empresa</h1>
    </div>

    {{-- DEBUG INFO - Remova em produção --}}
    <div class="p-4 border border-yellow-200 rounded-lg bg-yellow-50" id="debugPanel">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-medium text-yellow-800">🔍 Debug Panel</h3>
            <button type="button" onclick="toggleDebug()" class="px-2 py-1 text-xs bg-yellow-200 rounded">Toggle</button>
        </div>
        <div id="debugContent" class="text-xs text-yellow-700">
            <p><strong>Planos disponíveis:</strong> {{ count($subscriptionPlans ?? []) }}</p>
            @if(isset($subscriptionPlans) && count($subscriptionPlans) > 0)
                <ul class="mt-1 ml-4">
                    @foreach($subscriptionPlans as $key => $plan)
                        <li>• {{ $key }}: {{ $plan['name'] ?? 'N/A' }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-red-600">⚠️ Nenhum plano encontrado!</p>
            @endif
            <button onclick="window.debugCompanyForm()" class="px-2 py-1 mt-2 text-xs text-blue-800 bg-blue-200 rounded">Debug Console</button>
        </div>
    </div>

    <form action="{{ route('admin.companies.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="companyForm">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Informações da Empresa</h3>
                                <p class="text-sm text-gray-600">Dados básicos da empresa</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-700">
                                    Nome da Empresa *
                                </label>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       value="{{ old('name') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-300 @enderror">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="slug" class="block mb-2 text-sm font-medium text-gray-700">
                                    Slug (URL)
                                </label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 text-sm text-gray-500 border border-r-0 border-gray-300 rounded-l-md bg-gray-50">
                                        https://
                                    </span>
                                    <input type="text"
                                           name="slug"
                                           id="slug"
                                           value="{{ old('slug') }}"
                                           class="flex-1 block w-full px-3 py-3 text-sm border border-gray-300 rounded-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-300 @enderror">
                                    <span class="inline-flex items-center px-3 text-sm text-gray-500 border border-l-0 border-gray-300 rounded-r-md bg-gray-50">
                                        .{{ config('app.domain', 'localhost') }}
                                    </span>
                                </div>
                                @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Deixe em branco para gerar automaticamente</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="email" class="block mb-2 text-sm font-medium text-gray-700">
                                    Email *
                                </label>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       value="{{ old('email') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-300 @enderror">
                                @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block mb-2 text-sm font-medium text-gray-700">
                                    Telefone
                                </label>
                                <input type="text"
                                       name="phone"
                                       id="phone"
                                       value="{{ old('phone') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-300 @enderror">
                                @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="address" class="block mb-2 text-sm font-medium text-gray-700">
                                    Endereço
                                </label>
                                <textarea name="address"
                                          id="address"
                                          rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-300 @enderror">{{ old('address') }}</textarea>
                                @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="city" class="block mb-2 text-sm font-medium text-gray-700">
                                        Cidade
                                    </label>
                                    <input type="text"
                                           name="city"
                                           id="city"
                                           value="{{ old('city') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('city') border-red-300 @enderror">
                                    @error('city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="tax_number" class="block mb-2 text-sm font-medium text-gray-700">
                                        NUIT
                                    </label>
                                    <input type="text"
                                           name="tax_number"
                                           id="tax_number"
                                           value="{{ old('tax_number') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tax_number') border-red-300 @enderror">
                                    @error('tax_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="logo" class="block mb-2 text-sm font-medium text-gray-700">
                                Logo da Empresa
                            </label>
                            <div class="flex items-center justify-center w-full">
                                <label for="logo" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Clique para enviar</span> ou arraste</p>
                                        <p class="text-xs text-gray-500">PNG, JPG ou GIF (MAX. 2MB)</p>
                                    </div>
                                    <input id="logo" name="logo" type="file" class="hidden" accept="image/*" />
                                </label>
                            </div>
                            @error('logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Configurações Avançadas -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-purple-100 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Configurações Avançadas</h3>
                                <p class="text-sm text-gray-600">Configurações especiais e recursos premium</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="flex items-center space-x-6">
                            <div class="flex items-center">
                                <input id="custom_domain_enabled"
                                       name="custom_domain_enabled"
                                       type="checkbox"
                                       value="1"
                                       {{ old('custom_domain_enabled') ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                <label for="custom_domain_enabled" class="ml-2 text-sm font-medium text-gray-900">
                                    Domínio personalizado habilitado
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input id="api_access_enabled"
                                       name="api_access_enabled"
                                       type="checkbox"
                                       value="1"
                                       {{ old('api_access_enabled') ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                <label for="api_access_enabled" class="ml-2 text-sm font-medium text-gray-900">
                                    Acesso à API habilitado
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Plano e Status -->
                <div class="sticky bg-white border border-gray-200 shadow-sm rounded-xl top-8" id="sidebarContainer">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Plano & Status</h3>
                                <p class="text-sm text-gray-600">Configurações de assinatura</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6" id="planSection">
                        <div>
                            <label for="subscription_plan" class="block mb-2 text-sm font-medium text-gray-700">
                                Plano de Assinatura *
                            </label>
                            <select name="subscription_plan"
                                    id="subscription_plan"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('subscription_plan') border-red-300 @enderror">
                                <option value="">Selecione um plano</option>
                                @if(isset($subscriptionPlans) && count($subscriptionPlans) > 0)
                                    @foreach($subscriptionPlans as $key => $plan)
                                    <option value="{{ $key }}"
                                            {{ old('subscription_plan') === $key ? 'selected' : '' }}
                                            data-id="{{ $plan['id'] ?? '' }}"
                                            data-price="{{ $plan['price'] ?? 0 }}"
                                            data-formatted-price="{{ $plan['formatted_price'] ?? '0,00 MT' }}"
                                            data-users="{{ $plan['max_users'] ?? 'Ilimitado' }}"
                                            data-companies="{{ $plan['max_companies'] ?? 'Ilimitado' }}"
                                            data-invoices="{{ $plan['max_invoices_per_month'] ?? 'Ilimitado' }}"
                                            data-clients="{{ $plan['max_clients'] ?? 'Ilimitado' }}"
                                            data-products="{{ $plan['max_products'] ?? 'Ilimitado' }}"
                                            data-storage="{{ $plan['storage_formatted'] ?? 'Ilimitado' }}"
                                            data-features="{{ json_encode($plan['features'] ?? []) }}"
                                            data-billing-cycle="{{ $plan['billing_cycle_text'] ?? 'Mensal' }}"
                                            data-trial-days="{{ $plan['trial_days'] ?? 0 }}"
                                            data-has-trial="{{ ($plan['has_trial'] ?? false) ? 'true' : 'false' }}"
                                            data-popular="{{ ($plan['is_popular'] ?? false) ? 'true' : 'false' }}"
                                            data-color="{{ $plan['color'] ?? '#3B82F6' }}"
                                            data-description="{{ $plan['description'] ?? '' }}">
                                        @if($plan['is_popular'] ?? false)
                                            ⭐
                                        @endif
                                        {{ $plan['name'] ?? 'Plano' }} - {{ $plan['formatted_price'] ?? '0,00 MT' }}
                                        @if(($plan['billing_cycle'] ?? 'monthly') !== 'monthly')
                                            ({{ $plan['billing_cycle_text'] ?? 'Anual' }})
                                        @endif
                                    </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Nenhum plano disponível</option>
                                @endif
                            </select>
                            @error('subscription_plan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Detalhes do Plano -->
                        <div id="planDetails" class="hidden p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-medium text-gray-900">Detalhes do Plano:</h4>
                                <span id="planPopularBadge" class="hidden px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">
                                    ⭐ Popular
                                </span>
                            </div>

                            <div id="planDescription" class="mb-3 text-sm text-gray-600"></div>

                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span>Usuários:</span>
                                    <span id="planUsers">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Empresas:</span>
                                    <span id="planCompanies">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Faturas/mês:</span>
                                    <span id="planInvoices">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Clientes:</span>
                                    <span id="planClients">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Produtos:</span>
                                    <span id="planProducts">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Armazenamento:</span>
                                    <span id="planStorage">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Ciclo de cobrança:</span>
                                    <span id="planBillingCycle">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Trial:</span>
                                    <span id="planTrial">-</span>
                                </div>
                                <div class="flex justify-between pt-2 mt-2 font-semibold border-t">
                                    <span>Preço:</span>
                                    <span id="planPrice">-</span>
                                </div>
                            </div>

                            <div id="planFeatures" class="mt-3">
                                <h5 class="mb-2 text-xs font-medium text-gray-700">Recursos inclusos:</h5>
                                <ul id="featuresList" class="space-y-1 text-xs text-gray-600"></ul>
                            </div>
                        </div>

                        <div>
                            <label for="status" class="block mb-2 text-sm font-medium text-gray-700">
                                Status *
                            </label>
                            <select name="status"
                                    id="status"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-300 @enderror">
                                <option value="trial" {{ old('status', 'trial') === 'trial' ? 'selected' : '' }}>Trial</option>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Ativo</option>
                                <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspenso</option>
                            </select>
                            @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="trialDaysField" class="{{ old('status', 'trial') === 'trial' ? '' : 'hidden' }}">
                            <label for="trial_days" class="block mb-2 text-sm font-medium text-gray-700">
                                Dias de Trial
                            </label>
                            <input type="number"
                                   name="trial_days"
                                   id="trial_days"
                                   value="{{ old('trial_days', 30) }}"
                                   min="1"
                                   max="90"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('trial_days') border-red-300 @enderror">
                            @error('trial_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Entre 1 e 90 dias</p>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl" id="actionsContainer">
                    <div class="p-6">
                        <div class="space-y-3" id="buttonContainer">
                            <button type="submit"
                                    id="submitButton"
                                    class="inline-flex items-center justify-center w-full px-6 py-3 text-sm font-medium text-white transition-colors bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Criar Empresa
                            </button>

                            <a href="{{ route('admin.companies.index') }}"
                               class="inline-flex items-center justify-center w-full px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
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
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Variáveis globais para debug
window.companyFormDebug = {
    elements: {},
    logs: [],
    initialized: false
};

// Função para toggle do debug
function toggleDebug() {
    const content = document.getElementById('debugContent');
    content.style.display = content.style.display === 'none' ? 'block' : 'none';
}

// Função de log personalizada
function debugLog(message, data = null) {
    const timestamp = new Date().toLocaleTimeString();
    const logEntry = `[${timestamp}] ${message}`;
    console.log(logEntry, data || '');
    window.companyFormDebug.logs.push({ timestamp, message, data });
}

// Função para restaurar o botão de submit (mantida para evitar o problema anterior)
function restoreButton() {
    const buttonContainer = window.companyFormDebug.elements.buttonContainer;
    if (buttonContainer && !document.getElementById('submitButton')) {
        debugLog('🔧 Tentando restaurar botão...');
        const newButton = document.createElement('button');
        newButton.type = 'submit';
        newButton.id = 'submitButton';
        newButton.className = 'inline-flex items-center justify-center w-full px-6 py-3 text-sm font-medium text-white transition-colors bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500';
        newButton.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Criar Empresa
        `;
        buttonContainer.insertBefore(newButton, buttonContainer.firstChild);
        window.companyFormDebug.elements.submitButton = newButton;
        debugLog('✅ Botão restaurado com sucesso!');
    }
}

// Função para verificar a visibilidade do botão
function checkButtonVisibility() {
    const btn = window.companyFormDebug.elements.submitButton;
    if (!btn) {
        debugLog('❌ Botão de submit não existe mais!', {
            parentExists: !!window.companyFormDebug.elements.buttonContainer,
            parentVisible: window.companyFormDebug.elements.buttonContainer ? window.getComputedStyle(window.companyFormDebug.elements.buttonContainer).display !== 'none' : false
        });
        restoreButton();
        return false;
    }

    const rect = btn.getBoundingClientRect();
    const computedStyle = window.getComputedStyle(btn);

    const isVisible = {
        exists: document.contains(btn),
        display: computedStyle.display !== 'none',
        visibility: computedStyle.visibility !== 'hidden',
        opacity: parseFloat(computedStyle.opacity) > 0,
        hasSize: rect.width > 0 && rect.height > 0,
        inViewport: rect.top < window.innerHeight && rect.bottom > 0
    };

    const visible = Object.values(isVisible).every(v => v === true);

    if (!visible) {
        debugLog('⚠️ Botão não está completamente visível:', isVisible);
        restoreButton();
    }

    return visible;
}

document.addEventListener('DOMContentLoaded', function() {
    debugLog('🚀 Inicializando formulário de empresa');

    // Mapear elementos
    const elements = {
        form: document.getElementById('companyForm'),
        nameInput: document.getElementById('name'),
        slugInput: document.getElementById('slug'),
        planSelect: document.getElementById('subscription_plan'),
        statusSelect: document.getElementById('status'),
        trialDaysField: document.getElementById('trialDaysField'),
        planDetails: document.getElementById('planDetails'),
        submitButton: document.getElementById('submitButton'),
        buttonContainer: document.getElementById('buttonContainer'),
        actionsContainer: document.getElementById('actionsContainer'),
        sidebarContainer: document.getElementById('sidebarContainer'),
        planUsers: document.getElementById('planUsers'),
        planCompanies: document.getElementById('planCompanies'),
        planInvoices: document.getElementById('planInvoices'),
        planClients: document.getElementById('planClients'),
        planProducts: document.getElementById('planProducts'),
        planStorage: document.getElementById('planStorage'),
        planBillingCycle: document.getElementById('planBillingCycle'),
        planPrice: document.getElementById('planPrice'),
        planTrial: document.getElementById('planTrial'),
        planDescription: document.getElementById('planDescription'),
        planPopularBadge: document.getElementById('planPopularBadge'),
        featuresList: document.getElementById('featuresList')
    };

    window.companyFormDebug.elements = elements;

    // Verificar elementos
    Object.keys(elements).forEach(key => {
        if (!elements[key]) {
            debugLog(`❌ ELEMENTO FALTANDO: ${key}`);
        } else {
            debugLog(`✅ Elemento encontrado: ${key}`);
        }
    });

    if (!elements.planSelect || !elements.submitButton || !elements.planDetails) {
        debugLog('🚨 ERRO CRÍTICO: Elementos essenciais não encontrados!');
        alert('ERRO: Elementos críticos do formulário não foram encontrados.');
        return;
    }

    // Função para atualizar os detalhes do plano
    function updatePlanDetails() {
        debugLog('🔄 Atualizando detalhes do plano...');
        const selectedOption = elements.planSelect.options[elements.planSelect.selectedIndex];

        // Verificar se há um plano selecionado
        if (!selectedOption || !selectedOption.value) {
            debugLog('❌ Nenhum plano selecionado');
            elements.planDetails.classList.add('hidden');
            elements.planDetails.innerHTML = `
                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <p class="text-sm text-gray-600">Selecione um plano para visualizar os detalhes.</p>
                </div>
            `;
            return;
        }

        debugLog('✅ Plano selecionado:', selectedOption.text);

        // Função segura para obter dados
        const getData = (attribute, defaultValue = '-') => {
            const value = selectedOption.dataset[attribute];
            return value !== undefined && value !== null && value !== '' ? value : defaultValue;
        };

        // Coletar dados do plano
        const planData = {
            name: selectedOption.text || 'Plano Desconhecido',
            price: getData('formattedPrice', getData('price', '0,00 MT')),
            users: getData('users', 'Ilimitado'),
            companies: getData('companies', 'Ilimitado'),
            invoices: getData('invoices', 'Ilimitado'),
            clients: getData('clients', 'Ilimitado'),
            products: getData('products', 'Ilimitado'),
            storage: getData('storage', 'Ilimitado'),
            billingCycle: getData('billingCycle', 'Mensal'),
            trialDays: getData('trialDays', '0'),
            hasTrial: getData('hasTrial', 'false') === 'true',
            isPopular: getData('popular', 'false') === 'true',
            description: getData('description', 'Nenhuma descrição disponível'),
            features: getData('features', '[]'),
            color: getData('color', '#3B82F6')
        };

        debugLog('📊 Dados coletados do plano:', planData);

        // Atualizar elementos de forma segura
        const updateElement = (element, value, logUpdate = true) => {
            if (element) {
                element.textContent = value;
                if (logUpdate) debugLog(`✅ Atualizado ${element.id}:`, value);
                return true;
            } else {
                debugLog(`⚠️ Elemento ${element?.id} não encontrado`);
                return false;
            }
        };

        try {
            // Atualizar campos
            updateElement(elements.planUsers, planData.users);
            updateElement(elements.planCompanies, planData.companies);
            updateElement(elements.planInvoices, planData.invoices);
            updateElement(elements.planClients, planData.clients);
            updateElement(elements.planProducts, planData.products);
            updateElement(elements.planStorage, planData.storage);
            updateElement(elements.planBillingCycle, planData.billingCycle);
            updateElement(elements.planPrice, planData.price);
            updateElement(elements.planDescription, planData.description);
            updateElement(elements.planTrial, planData.hasTrial ? `${planData.trialDays} dias de trial gratuito` : 'Sem trial');

            // Atualizar badge de popularidade
            if (elements.planPopularBadge) {
                elements.planPopularBadge.classList.toggle('hidden', !planData.isPopular);
                debugLog('🏆 Badge popular:', planData.isPopular);
            }

            // Atualizar lista de recursos
            if (elements.featuresList) {
                let features;
                try {
                    features = JSON.parse(planData.features);
                    if (!Array.isArray(features) || features.length === 0) {
                        elements.featuresList.innerHTML = '<li class="text-gray-400">Nenhum recurso especificado</li>';
                        debugLog('⚠️ Nenhum recurso válido encontrado');
                    } else {
                        elements.featuresList.innerHTML = features
                            .map(feature => `<li class="flex items-start"><span class="mr-2 text-green-500">✓</span><span>${feature}</span></li>`)
                            .join('');
                        debugLog('✅ Features carregadas:', features.length);
                    }
                } catch (e) {
                    debugLog('❌ Erro ao processar features:', e.message);
                    elements.featuresList.innerHTML = '<li class="text-red-600">Erro ao carregar recursos</li>';
                }
            }

            // Aplicar cor do plano
            if (planData.color) {
                elements.planDetails.style.borderLeftColor = planData.color;
                elements.planDetails.style.borderLeftWidth = '4px';
            }

            // Mostrar a seção de detalhes
            elements.planDetails.classList.remove('hidden');
            debugLog('✅ Detalhes do plano exibidos');

            // Ajustar trial days automaticamente
            if (planData.hasTrial && elements.statusSelect && elements.statusSelect.value === 'trial') {
                const trialDaysInput = document.getElementById('trial_days');
                if (trialDaysInput && !trialDaysInput.value) {
                    trialDaysInput.value = planData.trialDays;
                    debugLog('⏱️ Trial days ajustado:', planData.trialDays);
                }
            }
        } catch (error) {
            debugLog('❌ ERRO ao atualizar detalhes do plano:', error.message);
            elements.planDetails.innerHTML = `
                <div class="p-4 border border-red-200 rounded-lg bg-red-50">
                    <p class="text-sm text-red-800">Erro ao carregar detalhes do plano. Tente novamente ou contate o suporte.</p>
                    <details class="mt-2">
                        <summary class="text-xs text-red-600 cursor-pointer">Detalhes técnicos</summary>
                        <p class="mt-1 font-mono text-xs text-red-500">${error.message}</p>
                    </details>
                </div>
            `;
            elements.planDetails.classList.remove('hidden');
        }

        // Verificar visibilidade do botão
        setTimeout(() => checkButtonVisibility(), 200);
    }

    // Handler do select de planos
    elements.planSelect.addEventListener('change', updatePlanDetails);

    // Disparar atualização inicial se houver plano pré-selecionado
    if (elements.planSelect.value) {
        debugLog('🔄 Disparando atualização inicial para plano pré-selecionado');
        updatePlanDetails();
    }

    // Auto-geração de slug
    if (elements.nameInput && elements.slugInput) {
        elements.nameInput.addEventListener('input', function() {
            if (!elements.slugInput.value || elements.slugInput.dataset.autoGenerated) {
                const slug = this.value
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9\s]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
                elements.slugInput.value = slug;
                elements.slugInput.dataset.autoGenerated = 'true';
                debugLog('📝 Slug gerado automaticamente:', slug);
            }
        });

        elements.slugInput.addEventListener('input', function() {
            delete this.dataset.autoGenerated;
            debugLog('✏️ Slug editado manualmente');
        });
    }

    // Controle de trial days
    if (elements.statusSelect && elements.trialDaysField) {
        elements.statusSelect.addEventListener('change', function() {
            debugLog('📊 Status alterado para:', this.value);
            elements.trialDaysField.classList.toggle('hidden', this.value !== 'trial');
            setTimeout(() => checkButtonVisibility(), 100);
        });
    }

    // Validação do formulário
    if (elements.form) {
        elements.form.addEventListener('submit', function(e) {
            debugLog('📝 Submetendo formulário...');

            if (!elements.planSelect.value) {
                e.preventDefault();
                alert('❌ Por favor, selecione um plano de assinatura.');
                elements.planSelect.focus();
                debugLog('❌ Submissão cancelada: nenhum plano selecionado');
                return;
            }

            if (elements.statusSelect.value === 'trial') {
                const trialDaysInput = document.getElementById('trial_days');
                if (trialDaysInput && (!trialDaysInput.value || trialDaysInput.value < 1)) {
                    e.preventDefault();
                    alert('❌ Para status de trial, especifique o número de dias (mínimo 1).');
                    trialDaysInput.focus();
                    debugLog('❌ Submissão cancelada: dias de trial inválidos');
                    return;
                }
            }

            if (elements.submitButton) {
                elements.submitButton.disabled = true;
                const originalText = elements.submitButton.innerHTML;
                elements.submitButton.innerHTML = `
                    <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Criando empresa...
                `;

                window.addEventListener('error', function() {
                    if (elements.submitButton) {
                        elements.submitButton.disabled = false;
                        elements.submitButton.innerHTML = originalText;
                        debugLog('🚨 Erro detectado, botão restaurado');
                    }
                }, { once: true });

                setTimeout(() => {
                    if (elements.submitButton && elements.submitButton.disabled) {
                        elements.submitButton.disabled = false;
                        elements.submitButton.innerHTML = originalText;
                        debugLog('⏰ Botão restaurado por timeout');
                    }
                }, 15000);
            }

            debugLog('✅ Formulário válido, enviando dados...');
        });
    }

    // Observer para monitorar mudanças no DOM
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.removedNodes.forEach(node => {
                    if (node.nodeType === 1 && (node.id === 'submitButton' || (node.contains && elements.submitButton && node.contains(elements.submitButton)))) {
                        debugLog('🚨 BOTÃO REMOVIDO DO DOM!', {
                            target: mutation.target,
                            removedNodes: Array.from(mutation.removedNodes).map(n => n.id || n.nodeName)
                        });
                        restoreButton();
                    }
                });
            }
        });
    });

    if (elements.actionsContainer) {
        observer.observe(elements.actionsContainer, { childList: true, subtree: true });
        debugLog('👀 Observer ativo no container de ações');
    }

    // Monitoramento periódico
    setInterval(() => {
        if (!checkButtonVisibility()) {
            debugLog('⚠️ Verificação periódica: botão não está visível');
        }
    }, 10000);

    // Upload de logo
    const logoInput = document.getElementById('logo');
    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                debugLog('📁 Logo selecionado:', { name: file.name, size: file.size });
                if (file.size > 2048000) {
                    alert('❌ Arquivo muito grande! Máximo 2MB.');
                    this.value = '';
                    return;
                }
                if (!file.type.startsWith('image/')) {
                    alert('❌ Por favor, selecione apenas arquivos de imagem.');
                    this.value = '';
                    return;
                }
            }
        });
    }

    window.companyFormDebug.initialized = true;
    debugLog('🎉 Formulário de empresa inicializado com sucesso!');
});

// Funções de debug
window.debugCompanyForm = function() {
    const debug = window.companyFormDebug;
    console.log('🔍 ESTADO DO FORMULÁRIO:');
    console.log('Inicializado:', debug.initialized);
    console.log('Elementos:', debug.elements);
    console.log('Logs recentes:', debug.logs.slice(-10));

    const planSelect = debug.elements.planSelect;
    if (planSelect) {
        console.log('📋 Planos disponíveis:');
        Array.from(planSelect.options).forEach((option, index) => {
            if (option.value) {
                console.log(`${index}: ${option.text} (${option.value})`, option.dataset);
            }
        });
    }

    console.log('🔍 Status do botão:', {
        exists: !!debug.elements.submitButton,
        visible: debug.elements.submitButton ? window.getComputedStyle(debug.elements.submitButton).display !== 'none' : false,
        enabled: debug.elements.submitButton ? !debug.elements.submitButton.disabled : false
    });

    console.log('🔍 Status dos detalhes do plano:', {
        planDetailsExists: !!debug.elements.planDetails,
        planDetailsVisible: debug.elements.planDetails ? window.getComputedStyle(debug.elements.planDetails).display !== 'none' : false,
        selectedPlan: planSelect.value,
        planDetailsContent: debug.elements.planDetails ? debug.elements.planDetails.innerHTML : 'N/A'
    });

    return debug;
};

window.showDebugLogs = function() {
    const logs = window.companyFormDebug.logs;
    console.log('📋 TODOS OS LOGS:');
    logs.forEach(log => {
        console.log(`[${log.timestamp}] ${log.message}`, log.data || '');
    });
};
</script>
@endpush

@push('styles')
<style>
/* Garantir visibilidade dos elementos críticos */
#actionsContainer,
#buttonContainer,
#submitButton,
#planDetails {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: relative !important;
    z-index: 100 !important;
}

/* Estilizar a seção de detalhes do plano */
#planDetails {
    transition: opacity 0.3s ease, transform 0.3s ease;
    transform: translateY(0);
}

#planDetails.hidden {
    display: none !important;
}

@media (max-width: 1280px) {
    #actionsContainer,
    #buttonContainer,
    #submitButton,
    #planDetails {
        display: block !important;
        visibility: visible !important;
        position: relative !important;
    }
}

/* Debug styles */
.debug-mode #submitButton {
    border: 2px solid green !important;
    box-shadow: 0 0 10px rgba(0, 255, 0, 0.3) !important;
}

.debug-mode #buttonContainer {
    background: rgba(255, 255, 0, 0.1) !important;
    border: 1px dashed orange !important;
    position: relative;
}

.debug-mode #buttonContainer::before {
    content: "CONTAINER DOS BOTÕES";
    position: absolute;
    top: -15px;
    left: 0;
    background: orange;
    color: white;
    font-size: 10px;
    padding: 2px 4px;
    z-index: 1000;
}

.debug-mode #planDetails {
    outline: 2px solid blue !important;
    background: rgba(0, 0, 255, 0.05) !important;
}

#submitButton {
    min-height: 44px !important;
}

.space-y-3 > * + * {
    margin-top: 0.75rem !important;
}

.sticky {
    z-index: 20 !important;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

#debugContent {
    display: none;
}
</style>
@endpush
