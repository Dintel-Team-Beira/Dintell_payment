@extends('layouts.app')

@section('title', 'Nova Cotação')
@section('subtitle', 'Crie uma nova proposta comercial para seu cliente')

@section('header-actions')
<div class="flex space-x-3">
    <a href="{{ route('quotes.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-gray-500 rounded-lg hover:bg-gray-600">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Voltar
    </a>
</div>
@endsection

@section('content')
<div class="mx-auto ">
    <form action="{{ route('quotes.store') }}" method="POST" id="quoteForm">
        @csrf

        <!-- Informações da Cotação -->
        <div class="mb-8 bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Informações da Cotação</h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="client_id" class="block mb-2 text-sm font-medium text-gray-700">Cliente *</label>
                        <select name="client_id" id="client_id"
                                class="w-full rounded-lg p-2 border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('client_id') border-red-300 @enderror"
                                required>
                            <option value="">Selecione um cliente</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="quote_date" class="block mb-2 text-sm font-medium text-gray-700">Data da Cotação *</label>
                        <input type="date" name="quote_date" id="quote_date"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('quote_date') border-red-300 @enderror"
                               value="{{ old('quote_date', date('Y-m-d')) }}" required>
                        @error('quote_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="valid_until" class="block mb-2 text-sm font-medium text-gray-700">Válida até *</label>
                    <input type="date" name="valid_until" id="valid_until"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('valid_until') border-red-300 @enderror"
                           value="{{ old('valid_until', date('Y-m-d', strtotime('+30 days'))) }}" required>
                    @error('valid_until')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Itens da Cotação -->
        <div class="mb-8 bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Itens da Cotação</h3>
                    <button type="button" id="addItem"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Adicionar Item
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full" id="itemsTable">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-2 py-3 text-sm font-medium text-left text-gray-700">Descrição</th>
                                <th class="w-20 px-2 py-3 text-sm font-medium text-center text-gray-700">Qtd</th>
                                <th class="w-32 px-2 py-3 text-sm font-medium text-right text-gray-700">Preço Unit.</th>
                                <th class="w-20 px-2 py-3 text-sm font-medium text-center text-gray-700">IVA (%)</th>
                                <th class="w-32 px-2 py-3 text-sm font-medium text-right text-gray-700">Total</th>
                                <th class="w-16 px-2 py-3 text-sm font-medium text-center text-gray-700">Ação</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <tr class="border-b border-gray-100 item-row">
                                <td class="px-2 py-3">
                                    <input type="text" name="items[0][description]"
                                           class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500"
                                           placeholder="Descrição do item" required>
                                </td>
                                <td class="px-2 py-3">
                                    <input type="number" name="items[0][quantity]"
                                           class="w-full text-sm text-center border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 item-quantity"
                                           value="1" min="1" step="1" required>
                                </td>
                                <td class="px-2 py-3">
                                    <input type="number" name="items[0][unit_price]"
                                           class="w-full text-sm text-right border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 item-price"
                                           value="0" min="0" step="0.01" required>
                                </td>
                                <td class="px-2 py-3">
                                    <input type="number" name="items[0][tax_rate]"
                                           class="w-full text-sm text-center border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 item-tax"
                                           value="{{ $settings->default_tax_rate }}" min="0" max="100" step="0.01">
                                </td>
                                <td class="px-2 py-3">
                                    <input type="text" class="w-full text-sm font-medium text-right text-gray-900 border-gray-300 rounded-lg bg-gray-50 item-total" readonly>
                                </td>
                                <td class="px-2 py-3 text-center">
                                    <button type="button" class="p-1 text-red-600 hover:text-red-800 remove-item">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Totais e Observações -->
        <div class="grid grid-cols-1 gap-8 mb-8 lg:grid-cols-2">
            <!-- Observações -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Observações e Termos</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label for="notes" class="block mb-2 text-sm font-medium text-gray-700">Observações</label>
                        <textarea name="notes" id="notes" rows="4"
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500"
                                  placeholder="Observações sobre a cotação...">{{ old('notes') }}</textarea>
                    </div>

                    <div>
                        <label for="terms_conditions" class="block mb-2 text-sm font-medium text-gray-700">Termos e Condições</label>
                        <textarea name="terms_conditions" id="terms_conditions" rows="4"
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500"
                                  placeholder="Termos e condições da cotação...">{{ old('terms_conditions') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Totais -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Totais</h3>
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
                        <div class="flex items-center justify-between px-4 py-3 border-t-2 border-green-200 rounded-lg bg-green-50">
                            <span class="text-lg font-bold text-green-800">TOTAL:</span>
                            <span id="totalDisplay" class="text-xl font-bold text-green-800">0,00 MT</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('quotes.index') }}"
                   class="px-6 py-2 font-medium text-white transition-colors bg-gray-500 rounded-lg hover:bg-gray-600">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2 font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
                    <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Salvar Cotação
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 1;

    // Adicionar item
    document.getElementById('addItem').addEventListener('click', function() {
        const tbody = document.getElementById('itemsTableBody');
        const newRow = document.querySelector('.item-row').cloneNode(true);

        // Atualizar índices dos inputs
        const inputs = newRow.querySelectorAll('input');
        inputs.forEach(input => {
            if (input.name) {
                input.name = input.name.replace('[0]', '[' + itemIndex + ']');
                if (!input.classList.contains('item-total')) {
                    if (input.name.includes('quantity')) {
                        input.value = '1';
                    } else if (input.name.includes('tax_rate')) {
                        input.value = '{{ $settings->default_tax_rate }}';
                    } else if (input.name.includes('unit_price')) {
                        input.value = '0';
                    } else {
                        input.value = '';
                    }
                } else {
                    input.value = '';
                }
            }
        });

        tbody.appendChild(newRow);
        itemIndex++;

        attachEventListeners(newRow);
        calculateTotals();
    });

    // Remover item
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                e.target.closest('.item-row').remove();
                calculateTotals();
            } else {
                alert('Deve haver pelo menos um item na cotação.');
            }
        }
    });

    // Anexar eventos aos campos existentes
    document.querySelectorAll('.item-row').forEach(row => {
        attachEventListeners(row);
    });

    function attachEventListeners(row) {
        const quantity = row.querySelector('.item-quantity');
        const price = row.querySelector('.item-price');
        const tax = row.querySelector('.item-tax');

        [quantity, price, tax].forEach(input => {
            input.addEventListener('input', calculateRowTotal);
        });
    }

    function calculateRowTotal(e) {
        const row = e.target.closest('.item-row');
        const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const taxRate = parseFloat(row.querySelector('.item-tax').value) || 0;

        const subtotal = quantity * price;
        const tax = subtotal * (taxRate / 100);
        const total = subtotal + tax;

        row.querySelector('.item-total').value = total.toFixed(2);

        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        let totalTax = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const taxRate = parseFloat(row.querySelector('.item-tax').value) || 0;

            const itemSubtotal = quantity * price;
            const itemTax = itemSubtotal * (taxRate / 100);

            subtotal += itemSubtotal;
            totalTax += itemTax;
        });

        const total = subtotal + totalTax;

        document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2) + ' MT';
        document.getElementById('taxDisplay').textContent = totalTax.toFixed(2) + ' MT';
        document.getElementById('totalDisplay').textContent = total.toFixed(2) + ' MT';
    }

    // Calcular totais iniciais
    calculateTotals();

    // Validar datas
    document.getElementById('quote_date').addEventListener('change', function() {
        const quoteDate = new Date(this.value);
        const validUntilField = document.getElementById('valid_until');
        const validUntilDate = new Date(validUntilField.value);

        if (validUntilDate <= quoteDate) {
            const newValidDate = new Date(quoteDate);
            newValidDate.setDate(newValidDate.getDate() + 30);
            validUntilField.value = newValidDate.toISOString().split('T')[0];
        }
    });
});
</script>
@endpush
@endsection