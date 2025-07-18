@extends('layouts.app')

@section('title', 'SFS – Sistema de Faturação e Subscrição')

@section('content')
<div class="container ">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Nova Nota de Débito</h1>
                <p class="mt-2 text-gray-600">Crie uma nota de débito para cobranças adicionais</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('debit-notes.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar
                </a>
                @if($invoice)
                <a href="{{ route('invoices.show', $invoice) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Ver Fatura Original
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Formulário de Criação de Nota de Débito -->
    <form action="{{ route('debit-notes.store') }}" method="POST" id="debitNoteForm">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Informações da Nota de Débito</h3>
                                <p class="text-sm text-gray-600">Dados básicos da cobrança adicional</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="client_id" class="block mb-2 text-sm font-medium text-gray-700">
                                    Cliente *
                                </label>
                                <select name="client_id" id="client_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg select2 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent" required>
                                    <option value="">Selecione um cliente</option>
                                    @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ (($invoice && $invoice->client_id == $client->id) || old('client_id') == $client->id) ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                <div class="mt-2 text-sm text-red-600">
                                    <i class="mr-1 fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div>
                                <label for="invoice_date" class="block mb-2 text-sm font-medium text-gray-700">Data da Nota *</label>
                                <input type="date" name="invoice_date" id="invoice_date" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('invoice_date') border-red-300 @enderror" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                                @error('invoice_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="due_date" class="block mb-2 text-sm font-medium text-gray-700">Data de Vencimento *</label>
                                <input type="date" name="due_date" id="due_date" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('due_date') border-red-300 @enderror" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                                @error('due_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            @if($invoice)
                            <div>
                                <input type="hidden" name="related_invoice_id" value="{{ $invoice->id }}">
                                <label for="related_invoice" class="block mb-2 text-sm font-medium text-gray-700">Fatura Relacionada</label>
                                <div class="flex items-center px-4 py-3 border border-gray-300 rounded-lg bg-gray-50">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-gray-700">{{ $invoice->invoice_number }}</span>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div>
                            <label for="adjustment_reason" class="block mb-2 text-sm font-medium text-gray-700">Motivo do Débito *</label>
                            <textarea name="adjustment_reason" id="adjustment_reason" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('adjustment_reason') border-red-300 @enderror" placeholder="Descreva o motivo desta cobrança adicional..." required>{{ old('adjustment_reason') }}</textarea>
                            @error('adjustment_reason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Itens da Nota de Débito -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-2 mr-3 bg-orange-100 rounded-lg">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Itens a Debitar</h3>
                                    <p class="text-sm text-gray-600">Adicione os itens da cobrança adicional</p>
                                </div>
                            </div>
                            <button type="button" id="addDebitItemBtn" class="inline-flex items-center px-4 py-2 text-sm font-medium text-orange-700 border border-orange-200 rounded-md bg-orange-50 hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Adicionar Item
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <!-- Lista de Itens -->
                        <div id="debitItems" class="space-y-4">
                            <div class="py-12 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <p class="text-lg font-medium">Nenhum item adicionado</p>
                                <p class="text-sm">Clique em "Adicionar Item" para começar</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observações -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-purple-100 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Observações Adicionais</h3>
                                <p class="text-sm text-gray-600">Informações complementares sobre a nota</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div>
                            <label for="notes" class="block mb-2 text-sm font-medium text-gray-700">Observações</label>
                            <textarea name="notes" id="notes" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent" placeholder="Observações adicionais sobre a nota de débito...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Resumo Financeiro -->
                <div class="sticky bg-white border border-gray-200 shadow-sm rounded-xl top-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-red-100 rounded-lg">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Resumo do Débito</h3>
                                <p class="text-sm text-gray-600">Totais da cobrança</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-700">Subtotal:</span>
                                <span id="subtotalDisplay" class="text-sm font-bold text-gray-900">0,00 MT</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-700">IVA:</span>
                                <span id="taxDisplay" class="text-sm font-bold text-gray-900">0,00 MT</span>
                            </div>
                            <div class="flex items-center justify-between px-4 py-3 border-l-4 border-red-500 rounded-lg bg-gradient-to-r from-red-50 to-red-100">
                                <span class="text-lg font-bold text-red-800">TOTAL A DEBITAR:</span>
                                <span id="totalDisplay" class="text-xl font-bold text-red-800">0,00 MT</span>
                            </div>
                        </div>

                        <!-- Estatísticas Rápidas -->
                        <div class="pt-6 mt-6 border-t border-gray-200">
                            <div class="grid grid-cols-1 gap-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-orange-600" id="itemCount">0</div>
                                    <div class="text-xs text-gray-500">Itens</div>
                                </div>
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

                            <a href="{{ route('debit-notes.index') }}" class="inline-flex items-center justify-center w-full px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
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
                                Campos marcados com * são obrigatórios
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
    let itemIndex = 0;
    let debitItems = [];

    // Event Listeners
    document.getElementById('addDebitItemBtn').addEventListener('click', addDebitItem);
    document.getElementById('debitNoteForm').addEventListener('submit', handleFormSubmit);

    // Adicionar item de débito
    function addDebitItem() {
        const newItem = {
            index: itemIndex++,
            description: '',
            quantity: 1,
            unit_price: 0,
            tax_rate: 16
        };

        debitItems.push(newItem);
        renderDebitItems();
        calculateTotals();
    }

    // Renderizar itens
    function renderDebitItems() {
        const container = document.getElementById('debitItems');

        if (debitItems.length === 0) {
            container.innerHTML = `
                <div class="py-12 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <p class="text-lg font-medium">Nenhum item adicionado</p>
                    <p class="text-sm">Clique em "Adicionar Item" para começar</p>
                </div>
            `;
            return;
        }

        container.innerHTML = debitItems.map(item => `
            <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 item-card" data-index="${item.index}">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-2 space-x-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                Item de Débito
                            </span>
                        </div>

                        <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                            <div class="md:col-span-2">
                                <label class="block mb-1 text-xs font-medium text-gray-700">Descrição *</label>
                                <input type="text"
                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-yellow-500 item-description"
                                       value="${item.description}"
                                       placeholder="Descrição do item a debitar"
                                       data-index="${item.index}"
                                       required>
                            </div>
                            <div>
                                <label class="block mb-1 text-xs font-medium text-gray-700">Quantidade *</label>
                                <input type="number"
                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-yellow-500 item-quantity"
                                       value="${item.quantity}"
                                       min="0.1"
                                       step="0.1"
                                       data-index="${item.index}"
                                       required>
                            </div>
                            <div>
                                <label class="block mb-1 text-xs font-medium text-gray-700">Preço Unit. *</label>
                                <input type="number"
                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-yellow-500 item-price"
                                       value="${item.unit_price}"
                                       min="0"
                                       step="0.01"
                                       data-index="${item.index}"
                                       required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block mb-1 text-xs font-medium text-gray-700">IVA (%)</label>
                                <input type="number"
                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-yellow-500 item-tax"
                                       value="${item.tax_rate}"
                                       min="0"
                                       max="100"
                                       step="0.01"
                                       data-index="${item.index}">
                            </div>
                            <div>
                                <label class="block mb-1 text-xs font-medium text-gray-700">Total</label>
                                <div class="px-2 py-1 text-sm font-medium text-red-600 rounded bg-red-50 item-total" data-index="${item.index}">
                                    ${formatCurrency(calculateItemTotal(item))}
                                </div>
                            </div>
                        </div>

                        <!-- Hidden inputs para o form -->
                        <input type="hidden" name="items[${item.index}][description]" value="${item.description}" class="hidden-description">
                        <input type="hidden" name="items[${item.index}][quantity]" value="${item.quantity}" class="hidden-quantity">
                        <input type="hidden" name="items[${item.index}][unit_price]" value="${item.unit_price}" class="hidden-price">
                        <input type="hidden" name="items[${item.index}][tax_rate]" value="${item.tax_rate}" class="hidden-tax">
                    </div>
                    <button type="button"
                            class="p-1 ml-4 text-red-600 hover:text-red-800 remove-item"
                            data-index="${item.index}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        `).join('');

        // Anexar event listeners
        attachItemEventListeners();
    }

    // Anexar event listeners aos itens
    function attachItemEventListeners() {
        // Remover itens
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                debitItems = debitItems.filter(item => item.index !== index);
                renderDebitItems();
                calculateTotals();
                showNotification('Item removido', 'info');
            });
        });

        // Atualizar valores
        document.querySelectorAll('.item-description, .item-quantity, .item-price, .item-tax').forEach(input => {
            input.addEventListener('input', function() {
                const index = parseInt(this.dataset.index);
                const item = debitItems.find(item => item.index === index);

                if (item) {
                    if (this.classList.contains('item-description')) {
                        item.description = this.value;
                        document.querySelector(`input[name="items[${index}][description]"]`).value = item.description;
                    } else if (this.classList.contains('item-quantity')) {
                        item.quantity = parseFloat(this.value) || 0;
                        document.querySelector(`input[name="items[${index}][quantity]"]`).value = item.quantity;
                    } else if (this.classList.contains('item-price')) {
                        item.unit_price = parseFloat(this.value) || 0;
                        document.querySelector(`input[name="items[${index}][unit_price]"]`).value = item.unit_price;
                    } else if (this.classList.contains('item-tax')) {
                        item.tax_rate = parseFloat(this.value) || 0;
                        document.querySelector(`input[name="items[${index}][tax_rate]"]`).value = item.tax_rate;
                    }

                    // Atualizar total do item
                    const totalElement = document.querySelector(`.item-total[data-index="${index}"]`);
                    if (totalElement) {
                        totalElement.textContent = formatCurrency(calculateItemTotal(item));
                    }

                    calculateTotals();
                }
            });
        });
    }

    // Calcular total do item
    function calculateItemTotal(item) {
        const subtotal = item.quantity * item.unit_price;
        const tax = subtotal * (item.tax_rate / 100);
        return subtotal + tax;
    }

    // Calcular totais
    function calculateTotals() {
        let subtotal = 0;
        let totalTax = 0;
        let totalItems = debitItems.length;

        debitItems.forEach(item => {
            const itemSubtotal = item.quantity * item.unit_price;
            const itemTax = itemSubtotal * (item.tax_rate / 100);

            subtotal += itemSubtotal;
            totalTax += itemTax;
        });

        const total = subtotal + totalTax;

        // Atualizar display
        document.getElementById('subtotalDisplay').textContent = formatCurrency(subtotal);
        document.getElementById('taxDisplay').textContent = formatCurrency(totalTax);
        document.getElementById('totalDisplay').textContent = formatCurrency(total);
        document.getElementById('itemCount').textContent = totalItems;
    }

    // Submissão do formulário
    function handleFormSubmit(e) {
        if (debitItems.length === 0) {
            e.preventDefault();
            showNotification('Adicione pelo menos um item à nota de débito', 'warning');
            return false;
        }

        // Validar se todos os itens têm dados válidos
        let hasInvalidItems = false;
        debitItems.forEach(item => {
            if (!item.description.trim() || item.quantity <= 0 || item.unit_price < 0) {
                hasInvalidItems = true;
            }
        });

        if (hasInvalidItems) {
            e.preventDefault();
            showNotification('Verifique se todos os itens têm descrição e valores válidos', 'warning');
            return false;
        }

        // Show loading state
        const submitButton = document.getElementById('saveDebitNoteBtn');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Criando...';
        submitButton.disabled = true;

        // Restaurar botão em caso de erro
        setTimeout(() => {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }, 10000);
    }

    // Utilitários
    function formatCurrency(value) {
        return new Intl.NumberFormat('pt-MZ', {
            style: 'currency',
            currency: 'MZN',
            minimumFractionDigits: 2
        }).format(value).replace('MTn', 'MT');
    }

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

    // Inicializar Select2
    $('.select2').select2({
        placeholder: 'Digite para buscar...',
        allowClear: true,
        width: '100%',
        minimumInputLength: 0,
        language: {
            noResults: function() {
                return "Nenhum resultado encontrado";
            },
            searching: function() {
                return "Procurando...";
            },
            inputTooShort: function() {
                return "Digite para buscar";
            },
            loadingMore: function() {
                return "Carregando mais...";
            }
        }
    });

    // Manter seleção após erro de validação Laravel
    @if(old('client_id'))
        $('#client_id').val('{{ old('client_id') }}').trigger('change');
    @endif

    // Aplicar estilo de erro se houver erro do Laravel
    @error('client_id')
        $('#client_id').next('.select2-container').addClass('select2-container--error');
    @enderror

    // Remover erro ao selecionar
    $('#client_id').on('change', function() {
        if ($(this).val()) {
            $(this).next('.select2-container').removeClass('select2-container--error');
        }
    });

    // Adicionar primeiro item automaticamente
    addDebitItem();
});
</script>
@endpush

@push('styles')
<style>
    /* Loading animation */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Animation for notifications */
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    .notification {
        animation: slideInRight 0.3s ease-out;
    }

    /* Custom focus styles */
    .focus\:ring-2:focus {
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.5);
    }

    /* Hover transitions */
    .transition-colors {
        transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    /* Item card hover effects */
    .item-card {
        transition: all 0.2s ease-in-out;
    }

    .item-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    /* Sticky sidebar */
    .sticky {
        position: sticky;
        top: 2rem;
    }

    /* Enhanced form inputs */
    input:focus, select:focus, textarea:focus {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 0 2px rgba(245, 158, 11, 0.5);
    }

    /* Button hover effects */
    button:hover:not(:disabled), a:hover {
        transform: translateY(-1px);
    }

    button:disabled {
        transform: none;
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Custom scrollbar */
    .overflow-y-auto::-webkit-scrollbar {
        width: 8px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Responsive improvements */
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
    }

    /* Enhanced card styling */
    .bg-white {
        transition: all 0.2s ease-in-out;
    }

    .bg-white:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* Gradient backgrounds */
    .bg-gradient-to-r {
        background-image: linear-gradient(to right, var(--tw-gradient-stops));
    }

    /* Yellow/Orange theme for debit notes */
    .bg-yellow-100 {
        background-color: rgb(254 249 195);
    }

    .text-yellow-600 {
        color: rgb(202 138 4);
    }

    .bg-orange-100 {
        background-color: rgb(255 237 213);
    }

    .text-orange-600 {
        color: rgb(234 88 12);
    }

    .text-orange-700 {
        color: rgb(194 65 12);
    }

    .border-orange-200 {
        border-color: rgb(254 215 170);
    }

    .bg-orange-50 {
        background-color: rgb(255 247 237);
    }

    .hover\:bg-orange-100:hover {
        background-color: rgb(255 237 213);
    }

    .focus\:ring-orange-500:focus {
        box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.5);
    }

    /* Red theme for totals */
    .bg-red-100 {
        background-color: rgb(254 226 226);
    }

    .text-red-600 {
        color: rgb(220 38 38);
    }

    .text-red-800 {
        color: rgb(153 27 27);
    }

    .border-red-500 {
        border-color: rgb(239 68 68);
    }

    .bg-red-50 {
        background-color: rgb(254 242 242);
    }

    /* Purple theme for notes */
    .bg-purple-100 {
        background-color: rgb(243 232 255);
    }

    .text-purple-600 {
        color: rgb(147 51 234);
    }

    /* Select2 custom styles */
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
        background-color: #ffffff !important;
        font-size: 0.875rem !important;
        transition: all 0.2s ease-in-out !important;
    }

    .select2-container--default .select2-selection--single:focus {
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.5) !important;
    }

    .select2-container--error .select2-selection--single {
        border-color: #ef4444 !important;
    }

    /* Enhanced visibility for required fields */
    label:has(+ input[required])::after,
    label:has(+ select[required])::after,
    label:has(+ textarea[required])::after {
        content: ' *';
        color: #ef4444;
    }

    /* Print styles */
    @media print {
        .no-print {
            display: none;
        }
    }
</style>
@endpush
@endsection
