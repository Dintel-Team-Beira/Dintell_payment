@extends('layouts.app')

@section('title', 'Nota de Débito')
@section('subtitle', 'Crie uma nota de débito para ajustar valores de faturas')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Nova Nota de Débito</h1>
                <p class="mt-2 text-gray-600">Crie uma nota de débito de forma rápida e automatizada</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ company_route('debit-notes.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar
                </a>
                @if($invoice)
                <a href="{{ company_route('invoices.show', $invoice) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover:bg-blue-100">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Ver Fatura Original
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Formulário -->
    <form action="{{ company_route('debit-notes.store') }}" method="POST" id="debitNoteForm">
        @csrf

        <div class="grid grid-cols-1 gap-8 xl:grid-cols-3">
            <!-- Coluna Principal -->
            <div class="space-y-8 xl:col-span-2">
                <!-- Informações Básicas -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-yellow-100 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Informações Básicas</h3>
                                <p class="text-sm text-gray-600">Dados da nota de débito</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="client_id" class="block mb-2 text-sm font-medium text-gray-700">Cliente *</label>
                                <select name="client_id" id="client_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg select2" required>
                                    <option value="">Selecione um cliente</option>
                                    @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ (($invoice && $invoice->client_id == $client->id) || old('client_id') == $client->id) ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="invoice_date" class="block mb-2 text-sm font-medium text-gray-700">Data da Nota *</label>
                                <input type="date" name="invoice_date" id="invoice_date" class="w-full px-4 py-3 border border-gray-300 rounded-lg" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                                @error('invoice_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="due_date" class="block mb-2 text-sm font-medium text-gray-700">Data de Vencimento *</label>
                                <input type="date" name="due_date" id="due_date" class="w-full px-4 py-3 border border-gray-300 rounded-lg" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                                @error('due_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            @if($invoice)
                            <div>
                                <input type="hidden" name="related_invoice_id" value="{{ $invoice->id }}">
                                <label class="block mb-2 text-sm font-medium text-gray-700">Fatura Relacionada</label>
                                <div class="flex items-center px-4 py-3 border border-gray-300 rounded-lg bg-gray-50">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-gray-700">{{ $invoice->invoice_number }} - {{ number_format($invoice->total, 2) }} MT</span>
                                </div>
                                <input type="hidden" name="base_amount" value="{{ $invoice->total }}" id="baseAmount">
                            </div>
                            @else
                            <div>
                                <label for="base_amount" class="block mb-2 text-sm font-medium text-gray-700">Valor Base para Cálculo *</label>
                                <input type="number" name="base_amount" id="base_amount" step="0.01" min="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg" value="{{ old('base_amount', 0) }}" required>
                                <p class="mt-1 text-xs text-gray-500">Valor sobre o qual será calculado o débito</p>
                                @error('base_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tipo de Débito -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-orange-100 rounded-lg">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Tipo de Débito</h3>
                                <p class="text-sm text-gray-600">Selecione o tipo de cobrança adicional</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Seleção do Tipo -->
                        <div>
                            <label for="debit_type" class="block mb-3 text-sm font-medium text-gray-700">Tipo de Cobrança *</label>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($commonDebitItems as $key => $item)
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="debit_type" value="{{ $key }}" class="sr-only debit-type-radio" data-item="{{ json_encode($item) }}" required>
                                    <div class="p-4 transition-colors border-2 border-gray-200 rounded-lg hover:border-orange-300 debit-type-card">
                                        <div class="flex flex-col items-center text-center">
                                            <div class="p-2 mb-2 bg-orange-100 rounded-lg">
                                                @switch($key)
                                                    @case('late_payment')
                                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        @break
                                                    @case('penalty_fee')
                                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                        </svg>
                                                        @break
                                                    @case('additional_services')
                                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                                        </svg>
                                                        @break
                                                    @case('material_adjustment')
                                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2M7 4h10M7 4l-1 16h12l-1-16M9 9v6M15 9v6" />
                                                        </svg>
                                                        @break
                                                    @case('administrative_fee')
                                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        @break
                                                    @case('correction_adjustment')
                                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                                        </svg>
                                                        @break
                                                    @default
                                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                                        </svg>
                                                @endswitch
                                            </div>
                                            <h4 class="text-sm font-medium text-gray-900">{{ $item['name'] }}</h4>
                                            <p class="mt-1 text-xs text-center text-gray-500">{{ $item['description'] }}</p>
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('debit_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Motivo Específico -->
                        <div id="debitReasonSection" class="hidden">
                            <label for="debit_reason" class="block mb-2 text-sm font-medium text-gray-700">Motivo Específico *</label>
                            <select name="debit_reason" id="debit_reason" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                <option value="">Selecione o motivo específico</option>
                            </select>
                            @error('debit_reason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Configuração de Valor -->
                        <div id="valueConfigSection" class="hidden space-y-4">
                            <div>
                                <label class="block mb-3 text-sm font-medium text-gray-700">Método de Cálculo</label>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <input type="radio" name="calculation_method" value="percentage" class="mr-3 text-orange-600">
                                        <div>
                                            <div class="font-medium text-gray-900">Percentual</div>
                                            <div class="text-sm text-gray-500">Baseado em % do valor</div>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <input type="radio" name="calculation_method" value="fixed" class="mr-3 text-orange-600">
                                        <div>
                                            <div class="font-medium text-gray-900">Valor Fixo</div>
                                            <div class="text-sm text-gray-500">Valor específico</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Campos de Valor -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div id="percentageField" class="hidden">
                                    <label for="percentage" class="block mb-2 text-sm font-medium text-gray-700">Percentual (%)</label>
                                    <input type="number" name="percentage" id="percentage" step="0.01" min="0" max="100" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                    <p class="mt-1 text-xs text-gray-500">Ex: 2.5 para 2.5%</p>
                                </div>

                                <div id="fixedAmountField" class="hidden">
                                    <label for="fixed_amount" class="block mb-2 text-sm font-medium text-gray-700">Valor Fixo (MT)</label>
                                    <input type="number" name="fixed_amount" id="fixed_amount" step="0.01" min="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                    <p class="mt-1 text-xs text-gray-500">Valor específico em MT</p>
                                </div>

                                <div>
                                    <label for="tax_rate" class="block mb-2 text-sm font-medium text-gray-700">IVA (%)</label>
                                    <input type="number" name="tax_rate" id="tax_rate" step="0.01" min="0" max="100" value="16" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                    <p class="mt-1 text-xs text-gray-500">Taxa de IVA aplicável</p>
                                </div>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div>
                            <label for="notes" class="block mb-2 text-sm font-medium text-gray-700">Observações</label>
                            <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg" placeholder="Observações adicionais sobre esta nota de débito...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Preview dos Cálculos -->
                <div class="sticky bg-white border border-gray-200 shadow-sm rounded-xl top-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Preview do Débito</h3>
                                <p class="text-sm text-gray-600">Cálculo automático</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div id="calculationPreview" class="space-y-4">
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-700">Valor Base:</span>
                                <span id="baseValueDisplay" class="text-sm font-bold text-gray-900">0,00 MT</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-700">Cálculo:</span>
                                <span id="calculationDisplay" class="text-sm text-gray-600">-</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-700">Subtotal:</span>
                                <span id="subtotalDisplay" class="text-sm font-bold text-gray-900">0,00 MT</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-700">IVA:</span>
                                <span id="taxDisplay" class="text-sm font-bold text-gray-900">0,00 MT</span>
                            </div>
                            <div class="flex items-center justify-between px-4 py-3 border-l-4 border-red-500 rounded-lg bg-gradient-to-r from-red-50 to-red-100">
                                <span class="text-lg font-bold text-red-800">TOTAL:</span>
                                <span id="totalDisplay" class="text-xl font-bold text-red-800">0,00 MT</span>
                            </div>
                        </div>

                        <!-- Descrição do Item -->
                        <div class="pt-6 mt-6 border-t border-gray-200">
                            <h4 class="mb-2 text-sm font-medium text-gray-700">Descrição do Item:</h4>
                            <div id="itemDescription" class="p-3 text-sm text-gray-600 rounded-lg bg-gray-50">
                                Selecione um tipo de débito para ver a descrição
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit" id="saveDebitNoteBtn" class="inline-flex items-center justify-center w-full px-6 py-3 text-sm font-medium text-white transition-colors bg-yellow-600 border border-transparent rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Criar Nota de Débito
                            </button>

                            <a href="{{ company_route('debit-notes.index') }}" class="inline-flex items-center justify-center w-full px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancelar
                            </a>
                        </div>

                        <div class="pt-6 mt-6 border-t border-gray-200">
                            <div class="flex items-center justify-center text-xs text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Todos os cálculos são feitos automaticamente
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedDebitType = null;
    let commonDebitItems = @json($commonDebitItems);

    // Elementos
    const debitTypeRadios = document.querySelectorAll('.debit-type-radio');
    const debitReasonSection = document.getElementById('debitReasonSection');
    const debitReasonSelect = document.getElementById('debit_reason');
    const valueConfigSection = document.getElementById('valueConfigSection');
    const calculationMethodRadios = document.querySelectorAll('input[name="calculation_method"]');
    const percentageField = document.getElementById('percentageField');
    const fixedAmountField = document.getElementById('fixedAmountField');

    // Event Listeners
    debitTypeRadios.forEach(radio => {
        radio.addEventListener('change', handleDebitTypeChange);
    });

    calculationMethodRadios.forEach(radio => {
        radio.addEventListener('change', handleCalculationMethodChange);
    });

    // Campos que afetam o cálculo
    document.getElementById('percentage')?.addEventListener('input', updateCalculation);
    document.getElementById('fixed_amount')?.addEventListener('input', updateCalculation);
    document.getElementById('tax_rate')?.addEventListener('input', updateCalculation);
    document.getElementById('base_amount')?.addEventListener('input', updateCalculation);

    debitReasonSelect.addEventListener('change', updateItemDescription);

    // Manipular mudança de tipo de débito
    function handleDebitTypeChange(e) {
        const radio = e.target;
        const itemData = JSON.parse(radio.dataset.item);
        selectedDebitType = radio.value;

        // Atualizar visual dos cards
        document.querySelectorAll('.debit-type-card').forEach(card => {
            card.classList.remove('border-orange-500', 'bg-orange-50');
            card.classList.add('border-gray-200');
        });

        radio.closest('label').querySelector('.debit-type-card').classList.remove('border-gray-200');
        radio.closest('label').querySelector('.debit-type-card').classList.add('border-orange-500', 'bg-orange-50');

        // Mostrar seção de motivo
        debitReasonSection.classList.remove('hidden');

        // Preencher opções de motivo
        debitReasonSelect.innerHTML = '<option value="">Selecione o motivo específico</option>';
        Object.entries(itemData.reasons).forEach(([key, reason]) => {
            debitReasonSelect.innerHTML += `<option value="${key}">${reason}</option>`;
        });

        // Mostrar configuração de valor
        valueConfigSection.classList.remove('hidden');

        // Pré-selecionar método de cálculo baseado nos defaults
        if (itemData.default_percentage && itemData.default_percentage > 0) {
            document.querySelector('input[name="calculation_method"][value="percentage"]').checked = true;
            document.getElementById('percentage').value = itemData.default_percentage;
            handleCalculationMethodChange({ target: { value: 'percentage' } });
        } else if (itemData.default_fixed_amount && itemData.default_fixed_amount > 0) {
            document.querySelector('input[name="calculation_method"][value="fixed"]').checked = true;
            document.getElementById('fixed_amount').value = itemData.default_fixed_amount;
            handleCalculationMethodChange({ target: { value: 'fixed' } });
        }

        // Atualizar taxa de IVA
        if (itemData.tax_rate) {
            document.getElementById('tax_rate').value = itemData.tax_rate;
        }

        updateCalculation();
        updateItemDescription();
    }

    // Manipular mudança de método de cálculo
    function handleCalculationMethodChange(e) {
        const method = e.target ? e.target.value : e;

        percentageField.classList.add('hidden');
        fixedAmountField.classList.add('hidden');

        if (method === 'percentage') {
            percentageField.classList.remove('hidden');
            document.getElementById('percentage').required = true;
            document.getElementById('fixed_amount').required = false;
        } else if (method === 'fixed') {
            fixedAmountField.classList.remove('hidden');
            document.getElementById('fixed_amount').required = true;
            document.getElementById('percentage').required = false;
        }

        updateCalculation();
    }

    // Atualizar cálculos
    function updateCalculation() {
        const baseAmount = parseFloat(document.getElementById('base_amount')?.value || document.getElementById('baseAmount')?.value || 0);
        const percentage = parseFloat(document.getElementById('percentage')?.value || 0);
        const fixedAmount = parseFloat(document.getElementById('fixed_amount')?.value || 0);
        const taxRate = parseFloat(document.getElementById('tax_rate')?.value || 0);

        let subtotal = 0;
        let calculationText = '-';

        const checkedMethod = document.querySelector('input[name="calculation_method"]:checked');
        if (checkedMethod) {
            if (checkedMethod.value === 'percentage' && percentage > 0) {
                subtotal = baseAmount * (percentage / 100);
                calculationText = `${formatCurrency(baseAmount)} × ${percentage}% = ${formatCurrency(subtotal)}`;
            } else if (checkedMethod.value === 'fixed' && fixedAmount > 0) {
                subtotal = fixedAmount;
                calculationText = `Valor fixo: ${formatCurrency(subtotal)}`;
            }
        }

        const taxAmount = subtotal * (taxRate / 100);
        const total = subtotal + taxAmount;

        // Atualizar displays
        document.getElementById('baseValueDisplay').textContent = formatCurrency(baseAmount);
        document.getElementById('calculationDisplay').textContent = calculationText;
        document.getElementById('subtotalDisplay').textContent = formatCurrency(subtotal);
        document.getElementById('taxDisplay').textContent = formatCurrency(taxAmount);
        document.getElementById('totalDisplay').textContent = formatCurrency(total);
    }

    // Atualizar descrição do item
    function updateItemDescription() {
        if (!selectedDebitType) return;

        const reasonKey = debitReasonSelect.value;
        const itemData = commonDebitItems[selectedDebitType];

        let description = itemData.name;
        if (reasonKey && itemData.reasons[reasonKey]) {
            description += ` - ${itemData.reasons[reasonKey]}`;
        }

        document.getElementById('itemDescription').textContent = description;
    }

    // Formatar moeda
    function formatCurrency(value) {
        return new Intl.NumberFormat('pt-MZ', {
            style: 'currency',
            currency: 'MZN',
            minimumFractionDigits: 2
        }).format(value).replace('MTn', 'MT');
    }

    // Inicializar Select2
    $('.select2').select2({
        placeholder: 'Digite para buscar...',
        allowClear: true,
        width: '100%'
    });

    // Validação do formulário
    document.getElementById('debitNoteForm').addEventListener('submit', function(e) {
        const checkedType = document.querySelector('input[name="debit_type"]:checked');
        const checkedMethod = document.querySelector('input[name="calculation_method"]:checked');

        if (!checkedType) {
            e.preventDefault();
            showNotification('Por favor, selecione um tipo de débito.', 'warning');
            return;
        }

        if (!checkedMethod) {
            e.preventDefault();
            showNotification('Por favor, selecione um método de cálculo.', 'warning');
            return;
        }

        const reasonValue = document.getElementById('debit_reason').value;
        if (!reasonValue) {
            e.preventDefault();
            showNotification('Por favor, selecione um motivo específico.', 'warning');
            return;
        }

        // Validar valores
        if (checkedMethod.value === 'percentage') {
            const percentage = parseFloat(document.getElementById('percentage').value || 0);
            if (percentage <= 0) {
                e.preventDefault();
                showNotification('Por favor, informe um percentual válido.', 'warning');
                return;
            }
        } else if (checkedMethod.value === 'fixed') {
            const fixedAmount = parseFloat(document.getElementById('fixed_amount').value || 0);
            if (fixedAmount <= 0) {
                e.preventDefault();
                showNotification('Por favor, informe um valor fixo válido.', 'warning');
                return;
            }
        }

        // Mostrar estado de loading
        const submitBtn = document.getElementById('saveDebitNoteBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Criando...';
        submitBtn.disabled = true;

        // Restaurar em caso de erro
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 10000);
    });

    // Sistema de notificações
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());

        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;

        const colors = {
            success: 'bg-green-50 border border-green-200 text-green-800',
            error: 'bg-red-50 border border-red-200 text-red-800',
            info: 'bg-blue-50 border border-blue-200 text-blue-800',
            warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800'
        };

        const icons = {
            success: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
            error: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
            info: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
            warning: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>'
        };

        notification.className += ` ${colors[type]}`;
        notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0 mr-3">
                    ${icons[type]}
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-3">
                    <button class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none" onclick="this.closest('.notification').remove()">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
            notification.classList.add('translate-x-0');
        }, 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }

    // Inicializar cálculos se há valor base
    @if($invoice)
    updateCalculation();
    @endif
});
</script>
@endpush

@push('styles')
<style>
    .debit-type-card {
        transition: all 0.2s ease-in-out;
    }

    .debit-type-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Notification animations */
    .notification {
        animation: slideInRight 0.3s ease-out;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 48px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        padding: 0 1rem !important;
        display: flex !important;
        align-items: center !important;
    }

    /* Required field indicators */
    label:has(+ input[required])::after,
    label:has(+ select[required])::after {
        content: ' *';
        color: #ef4444;
    }

    /* Card selection styling */
    input[type="radio"]:checked + .debit-type-card {
        border-color: #f97316 !important;
        background-color: #fff7ed !important;
    }

    /* Hover effects */
    .hover\:bg-gray-50:hover {
        background-color: #f9fafb;
    }

    .hover\:border-orange-300:hover {
        border-color: #fdba74;
    }

    /* Focus styles */
    .focus\:outline-none:focus {
        outline: 2px solid transparent;
        outline-offset: 2px;
    }

    .focus\:ring-2:focus {
        box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.5);
    }

    /* Button styles */
    .bg-yellow-600 {
        background-color: #ca8a04;
    }

    .hover\:bg-yellow-700:hover {
        background-color: #a16207;
    }

    .focus\:ring-yellow-500:focus {
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.5);
    }

    /* Card gradients */
    .bg-gradient-to-r {
        background-image: linear-gradient(to right, var(--tw-gradient-stops));
    }

    .from-red-50 {
        --tw-gradient-from: #fef2f2;
    }

    .to-red-100 {
        --tw-gradient-to: #fee2e2;
    }

    /* Sticky positioning */
    .sticky {
        position: sticky;
        top: 2rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .xl\:grid-cols-3 {
            grid-template-columns: 1fr;
        }

        .xl\:col-span-2 {
            grid-column: span 1;
        }

        .sticky {
            position: static;
        }

        .lg\:grid-cols-3 {
            grid-template-columns: 1fr;
        }

        .sm\:grid-cols-2 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
@endsection
