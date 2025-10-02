@extends('layouts.admin')

@section('title', 'Criar Nova Subscrição')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="mx-5 bg-white rounded-md shadow">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Nova Subscrição</h1>
                    <p class="mt-1 text-sm text-gray-600">Crie uma nova subscrição para uma empresa</p>
                </div>
                <div>
                    <a href="{{ route('admin.subscriptions.index') }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-8">
        <form action="{{ route('admin.subscriptions.store') }}" method="POST" id="subscriptionForm">
            @csrf

            <div class="grid grid-cols-1 gap-8 xl:grid-cols-3">
                <!-- Coluna Principal -->
                <div class="space-y-8 xl:col-span-2">
                    
                    <!-- Seleção de Empresa -->
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="p-2 mr-3 bg-blue-100 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Empresa</h3>
                                    <p class="text-sm text-gray-600">Selecione a empresa que receberá a subscrição</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div>
                                <label for="company_id" class="block mb-2 text-sm font-medium text-gray-700">
                                    Empresa *
                                </label>
                                <select name="company_id" id="company_id" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg select2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('company_id') border-red-300 @enderror">
                                    <option value="">Selecione uma empresa</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" 
                                                data-name="{{ $company->name }}"
                                                data-email="{{ $company->email }}"
                                                {{ old('company_id', $selectedCompany?->id) == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }} - {{ $company->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Preview da empresa selecionada -->
                            <div id="companyPreview" class="hidden p-4 mt-4 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center justify-center w-12 h-12 text-white bg-blue-600 rounded-full">
                                            <span id="companyInitials" class="text-lg font-semibold"></span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h4 id="companyName" class="text-sm font-medium text-gray-900"></h4>
                                        <p id="companyEmail" class="text-sm text-gray-500"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seleção de Plano -->
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="p-2 mr-3 bg-purple-100 rounded-lg">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Plano de Subscrição</h3>
                                    <p class="text-sm text-gray-600">Escolha o plano adequado</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                @foreach($plans as $plan)
                                <label class="relative flex cursor-pointer plan-card">
                                    <input type="radio" 
                                           name="plan_id" 
                                           value="{{ $plan->id }}"
                                           data-price="{{ $plan->price }}"
                                           data-name="{{ $plan->name }}"
                                           data-billing-cycle="{{ $plan->billing_cycle }}"
                                           data-features="{{ json_encode($plan->features) }}"
                                           class="sr-only peer"
                                           required
                                           {{ old('plan_id') == $plan->id ? 'checked' : '' }}>
                                    
                                    <div class="flex-1 p-4 transition-all border-2 border-gray-200 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300">
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-center">
                                                <div class="flex items-center justify-center w-10 h-10 rounded-lg" style="background-color: {{ $plan->color }}20;">
                                                    <svg class="w-6 h-6" style="color: {{ $plan->color }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <h4 class="text-sm font-semibold text-gray-900">{{ $plan->name }}</h4>
                                                    @if($plan->is_popular)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            Popular
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="hidden peer-checked:block">
                                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <div class="text-2xl font-bold text-gray-900">
                                                {{ number_format($plan->price, 2) }} MT
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                por {{ $plan->billing_cycle === 'monthly' ? 'mês' : ($plan->billing_cycle === 'quarterly' ? 'trimestre' : 'ano') }}
                                            </div>
                                        </div>

                                        <div class="mt-3 space-y-1 text-xs text-gray-600">
                                            <div>✓ Usuários: {{ $plan->max_users ?? 'Ilimitado' }}</div>
                                            <div>✓ Faturas/mês: {{ $plan->max_invoices_per_month ?? 'Ilimitado' }}</div>
                                            <div>✓ Clientes: {{ $plan->max_clients ?? 'Ilimitado' }}</div>
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('plan_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Configurações da Subscrição -->
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="p-2 mr-3 bg-green-100 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Configurações</h3>
                                    <p class="text-sm text-gray-600">Personalize a subscrição</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- Ciclo de Cobrança -->
                                <div>
                                    <label for="billing_cycle" class="block mb-2 text-sm font-medium text-gray-700">
                                        Ciclo de Cobrança *
                                    </label>
                                    <select name="billing_cycle" id="billing_cycle" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="monthly" {{ old('billing_cycle') == 'monthly' ? 'selected' : '' }}>Mensal</option>
                                        <option value="quarterly" {{ old('billing_cycle') == 'quarterly' ? 'selected' : '' }}>Trimestral</option>
                                        <option value="yearly" {{ old('billing_cycle') == 'yearly' ? 'selected' : '' }}>Anual</option>
                                    </select>
                                </div>

                                <!-- Data de Início -->
                                <div>
                                    <label for="starts_at" class="block mb-2 text-sm font-medium text-gray-700">
                                        Data de Início
                                    </label>
                                    <input type="date" name="starts_at" id="starts_at"
                                           value="{{ old('starts_at', date('Y-m-d')) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <!-- Auto Renovar -->
                            <div class="flex items-start p-4 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="auto_renew" id="auto_renew" value="1" checked
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                </div>
                                <div class="ml-3">
                                    <label for="auto_renew" class="text-sm font-medium text-gray-700">
                                        Renovação Automática
                                    </label>
                                    <p class="text-sm text-gray-500">
                                        A subscrição será renovada automaticamente ao final de cada período
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Descontos (Opcional) -->
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="p-2 mr-3 bg-yellow-100 rounded-lg">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Descontos</h3>
                                        <p class="text-sm text-gray-600">Opcional - Aplique descontos promocionais</p>
                                    </div>
                                </div>
                                <button type="button" id="toggleDiscount" class="text-sm text-blue-600 hover:text-blue-700">
                                    Adicionar Desconto
                                </button>
                            </div>
                        </div>
                        <div id="discountSection" class="hidden p-6 space-y-4">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div>
                                    <label for="coupon_code" class="block mb-2 text-sm font-medium text-gray-700">
                                        Código do Cupom
                                    </label>
                                    <input type="text" name="coupon_code" id="coupon_code"
                                           value="{{ old('coupon_code') }}"
                                           placeholder="PROMO2024"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="discount_amount" class="block mb-2 text-sm font-medium text-gray-700">
                                        Valor Fixo (MT)
                                    </label>
                                    <input type="number" name="discount_amount" id="discount_amount"
                                           value="{{ old('discount_amount') }}"
                                           min="0" step="0.01"
                                           placeholder="0.00"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="discount_percentage" class="block mb-2 text-sm font-medium text-gray-700">
                                        Percentual (%)
                                    </label>
                                    <input type="number" name="discount_percentage" id="discount_percentage"
                                           value="{{ old('discount_percentage') }}"
                                           min="0" max="100" step="0.01"
                                           placeholder="0"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="p-3 text-sm text-blue-700 border border-blue-200 rounded-lg bg-blue-50">
                                <strong>Nota:</strong> Se ambos forem informados, o desconto percentual será aplicado primeiro, seguido do valor fixo.
                            </div>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="p-2 mr-3 bg-gray-100 rounded-lg">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Observações</h3>
                                    <p class="text-sm text-gray-600">Notas internas sobre a subscrição</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <textarea name="notes" id="notes" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Adicione observações relevantes...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                </div>

                <!-- Sidebar - Resumo -->
                <div class="space-y-6">
                    <!-- Resumo Financeiro -->
                    <div class="sticky bg-white border border-gray-200 shadow-sm rounded-xl top-8">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Resumo da Subscrição</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- Plano Selecionado -->
                            <div id="selectedPlanInfo" class="hidden">
                                <div class="text-sm font-medium text-gray-700">Plano Selecionado</div>
                                <div id="planName" class="mt-1 text-lg font-semibold text-gray-900"></div>
                                <div id="planCycle" class="text-sm text-gray-500"></div>
                            </div>

                            <div class="pt-4 border-t border-gray-200">
                                <!-- Preço Base -->
                                <div class="flex justify-between mb-2 text-sm">
                                    <span class="text-gray-600">Preço Base:</span>
                                    <span id="basePrice" class="font-medium text-gray-900">0,00 MT</span>
                                </div>

                                <!-- Desconto -->
                                <div id="discountRow" class="flex justify-between hidden mb-2 text-sm text-green-600">
                                    <span>Desconto:</span>
                                    <span id="discountValue">- 0,00 MT</span>
                                </div>

                                <!-- Total -->
                                <div class="flex justify-between pt-4 mt-4 border-t border-gray-200">
                                    <span class="text-lg font-semibold text-gray-900">Total:</span>
                                    <span id="totalPrice" class="text-2xl font-bold text-blue-600">0,00 MT</span>
                                </div>
                            </div>

                            <!-- Info adicional -->
                            <div class="p-3 text-sm text-gray-600 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-gray-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <p>A subscrição será criada e a empresa terá acesso imediato aos recursos do plano selecionado.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="p-6 space-y-3">
                            <button type="submit" id="submitBtn"
                                    class="inline-flex items-center justify-center w-full px-6 py-3 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Criar Subscrição
                            </button>

                            <a href="{{ route('admin.subscriptions.index') }}"
                               class="inline-flex items-center justify-center w-full px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const companySelect = document.getElementById('company_id');
    const planRadios = document.querySelectorAll('input[name="plan_id"]');
    const billingCycleSelect = document.getElementById('billing_cycle');
    const discountAmountInput = document.getElementById('discount_amount');
    const discountPercentageInput = document.getElementById('discount_percentage');
    const toggleDiscountBtn = document.getElementById('toggleDiscount');
    const discountSection = document.getElementById('discountSection');

    // Preview da empresa
    companySelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const preview = document.getElementById('companyPreview');
        
        if (this.value) {
            const name = selected.dataset.name;
            const email = selected.dataset.email;
            const initials = name.split(' ').map(word => word[0]).join('').substring(0, 2).toUpperCase();
            
            document.getElementById('companyInitials').textContent = initials;
            document.getElementById('companyName').textContent = name;
            document.getElementById('companyEmail').textContent = email;
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    });

    // Atualizar resumo quando plano é selecionado
    planRadios.forEach(radio => {
        radio.addEventListener('change', updateSummary);
    });

    billingCycleSelect.addEventListener('change', updateSummary);
    discountAmountInput.addEventListener('input', updateSummary);
    discountPercentageInput.addEventListener('input', updateSummary);

    // Toggle seção de desconto
    toggleDiscountBtn.addEventListener('click', function() {
        discountSection.classList.toggle('hidden');
        this.textContent = discountSection.classList.contains('hidden') 
            ? 'Adicionar Desconto' 
            : 'Ocultar Desconto';
    });

    function updateSummary() {
        const selectedPlan = document.querySelector('input[name="plan_id"]:checked');
        
        if (!selectedPlan) {
            document.getElementById('selectedPlanInfo').classList.add('hidden');
            return;
        }

        const planName = selectedPlan.dataset.name;
        const basePrice = parseFloat(selectedPlan.dataset.price);
        const billingCycle = billingCycleSelect.value;

        // Mostrar info do plano
        document.getElementById('selectedPlanInfo').classList.remove('hidden');
        document.getElementById('planName').textContent = planName;
        
        const cycleText = {
            'monthly': 'Mensal',
            'quarterly': 'Trimestral',
            'yearly': 'Anual'
        };
        document.getElementById('planCycle').textContent = cycleText[billingCycle];

        // Calcular desconto
        let discountAmount = parseFloat(discountAmountInput.value) || 0;
        const discountPercentage = parseFloat(discountPercentageInput.value) || 0;

        let finalPrice = basePrice;

        // Aplicar desconto percentual primeiro
        if (discountPercentage > 0) {
            const percentDiscount = (basePrice * discountPercentage) / 100;
            finalPrice -= percentDiscount;
            discountAmount += percentDiscount;
        }

        // Aplicar desconto fixo
        if (parseFloat(discountAmountInput.value) > 0) {
            finalPrice -= parseFloat(discountAmountInput.value);
            discountAmount = parseFloat(discountAmountInput.value);
        }

        // Não permitir valores negativos
        finalPrice = Math.max(0, finalPrice);

        // Atualizar display
        document.getElementById('basePrice').textContent = formatCurrency(basePrice);
        
        if (discountAmount > 0) {
            document.getElementById('discountRow').classList.remove('hidden');
            document.getElementById('discountValue').textContent = '- ' + formatCurrency(discountAmount);
        } else {
            document.getElementById('discountRow').classList.add('hidden');
        }

        document.getElementById('totalPrice').textContent = formatCurrency(finalPrice);
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat('pt-MZ', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value) + ' MT';
    }

    // Validação do formulário
    document.getElementById('subscriptionForm').addEventListener('submit', function(e) {
        const companyId = document.getElementById('company_id').value;
        const planId = document.querySelector('input[name="plan_id"]:checked');

        if (!companyId) {
            e.preventDefault();
            showNotification('Selecione uma empresa', 'error');
            return false;
        }

        if (!planId) {
            e.preventDefault();
            showNotification('Selecione um plano', 'error');
            return false;
        }

        // Loading state no botão
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = `
            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Criando...
        `;
        submitBtn.disabled = true;

        // Restaurar em caso de erro
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 10000);
    });

    function showNotification(message, type = 'info') {
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(n => n.remove());

        const notification = document.createElement('div');
        notification.className = `notification fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300`;

        const colors = {
            success: 'bg-green-50 border border-green-200 text-green-800',
            error: 'bg-red-50 border border-red-200 text-red-800',
            info: 'bg-blue-50 border border-blue-200 text-blue-800',
        };

        notification.className += ` ${colors[type]}`;
        notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-1"><p class="text-sm font-medium">${message}</p></div>
                <button class="ml-3 text-gray-400 hover:text-gray-600" onclick="this.closest('.notification').remove()">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 5000);
    }

    // Inicializar Select2
    $('.select2').select2({
        placeholder: 'Digite para buscar...',
        allowClear: true,
        width: '100%'
    });

    // Trigger inicial se houver empresa pré-selecionada
    if (companySelect.value) {
        companySelect.dispatchEvent(new Event('change'));
    }

    // Trigger inicial se houver plano pré-selecionado
    const preselectedPlan = document.querySelector('input[name="plan_id"]:checked');
    if (preselectedPlan) {
        updateSummary();
    }
});
</script>

<style>
/* Animação para os cards de plano */
.plan-card input:checked + div {
    transform: scale(1.02);
}

.plan-card:hover input + div {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Loading animation */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Sticky sidebar */
/* .sticky {
    position: sticky;
    top: 2rem;
} */

/* Select2 custom styling */
.select2-container--default .select2-selection--single {
    height: 48px !important;
    border: 1px solid #d1d5db !important;
    border-radius: 0.5rem !important;
    padding: 0 1rem !important;
    display: flex !important;
    align-items: center !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 48px !important;
    padding-left: 0 !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 46px !important;
}

/* Transições suaves */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Responsivo */
@media (max-width: 768px) {
    .xl\:grid-cols-3 {
        grid-template-columns: 1fr;
    }

    .xl\:col-span-2 {
        grid-column: span 1;
    }

    /* .sticky {
        position: static;
    } */
}
</style>
@endpush