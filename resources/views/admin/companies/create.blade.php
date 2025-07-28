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

    <form action="{{ route('admin.companies.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
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
                <div class="sticky bg-white border border-gray-200 shadow-sm rounded-xl top-8">
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
                    <div class="p-6 space-y-6">
                        <div>
                            <label for="subscription_plan" class="block mb-2 text-sm font-medium text-gray-700">
                                Plano de Assinatura *
                            </label>
                            <select name="subscription_plan"
                                    id="subscription_plan"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('subscription_plan') border-red-300 @enderror">
                                <option value="">Selecione um plano</option>
                                @foreach($subscriptionPlans as $key => $plan)
                                <option value="{{ $key }}"
                                        {{ old('subscription_plan') === $key ? 'selected' : '' }}
                                        data-price="{{ $plan['price'] }}"
                                        data-users="{{ $plan['max_users'] }}"
                                        data-invoices="{{ $plan['max_invoices_per_month'] }}"
                                        data-clients="{{ $plan['max_clients'] }}">
                                    {{ $plan['name'] }} - {{ number_format($plan['price'], 2, ',', '.') }} MT/mês
                                </option>
                                @endforeach
                            </select>
                            @error('subscription_plan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Detalhes do Plano -->
                        <div id="planDetails" class="hidden p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <h4 class="mb-3 text-sm font-medium text-gray-900">Detalhes do Plano:</h4>
                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span>Usuários:</span>
                                    <span id="planUsers">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Faturas/mês:</span>
                                    <span id="planInvoices">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Clientes:</span>
                                    <span id="planClients">-</span>
                                </div>
                                <div class="flex justify-between font-semibold">
                                    <span>Preço mensal:</span>
                                    <span id="planPrice">-</span>
                                </div>
                            </div>
                            <div id="planFeatures" class="mt-3">
                                <h5 class="text-xs font-medium text-gray-700">Recursos inclusos:</h5>
                                <ul id="featuresList" class="mt-1 text-xs text-gray-600"></ul>
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
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit"
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
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const planSelect = document.getElementById('subscription_plan');
    const statusSelect = document.getElementById('status');
    const trialDaysField = document.getElementById('trialDaysField');
    const planDetails = document.getElementById('planDetails');

    // Auto-generate slug from name
    nameInput.addEventListener('input', function() {
        if (!slugInput.value || slugInput.dataset.autoGenerated) {
            const slug = this.value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '') // Remove acentos
                .replace(/[^a-z0-9\s]/g, '') // Remove caracteres especiais
                .replace(/\s+/g, '-') // Substitui espaços por hífens
                .replace(/-+/g, '-') // Remove hífens duplos
                .replace(/^-|-$/g, ''); // Remove hífens do início e fim

            slugInput.value = slug;
            slugInput.dataset.autoGenerated = 'true';
        }
    });

    // Mark slug as manually edited
    slugInput.addEventListener('input', function() {
        delete this.dataset.autoGenerated;
    });

    // Show/hide trial days based on status
    statusSelect.addEventListener('change', function() {
        if (this.value === 'trial') {
            trialDaysField.classList.remove('hidden');
        } else {
            trialDaysField.classList.add('hidden');
        }
    });

    // Show plan details
    planSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (selectedOption.value) {
            const price = selectedOption.dataset.price;
            const users = selectedOption.dataset.users;
            const invoices = selectedOption.dataset.invoices;
            const clients = selectedOption.dataset.clients;

            document.getElementById('planUsers').textContent = users === '999' ? 'Ilimitado' : users;
            document.getElementById('planInvoices').textContent = invoices === '999999' ? 'Ilimitado' : invoices;
            document.getElementById('planClients').textContent = clients === '999999' ? 'Ilimitado' : clients;
            document.getElementById('planPrice').textContent = new Intl.NumberFormat('pt-MZ', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(price) + ' MT';

            // Show features based on plan
            const featuresList = document.getElementById('featuresList');
            const planKey = selectedOption.value;

            let features = [];
            if (planKey === 'basic') {
                features = ['Faturação básica', 'Relatórios simples', 'Suporte por email'];
            } else if (planKey === 'premium') {
                features = ['Faturação avançada', 'API access', 'Relatórios avançados', 'Suporte prioritário'];
            } else if (planKey === 'enterprise') {
                features = ['Recursos ilimitados', 'Domínio personalizado', 'Integração avançada', 'Suporte dedicado'];
            }

            featuresList.innerHTML = features.map(feature => `<li>• ${feature}</li>`).join('');

            planDetails.classList.remove('hidden');
        } else {
            planDetails.classList.add('hidden');
        }
    });

    // Trigger plan details on page load if plan is selected
    if (planSelect.value) {
        planSelect.dispatchEvent(new Event('change'));
    }

    // File upload preview
    const logoInput = document.getElementById('logo');
    logoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // You can add preview functionality here
                console.log('Logo selecionado:', file.name);
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
