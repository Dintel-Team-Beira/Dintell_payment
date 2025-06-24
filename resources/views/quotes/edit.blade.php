@extends('layouts.app')

@section('title', 'Editar Cotação #{{ $quote->quote_number }}')

@section('header-actions')
<div class="flex items-center gap-x-4">
    <a href="{{ route('quotes.show', $quote) }}"
       class="flex items-center px-3 py-2 text-sm font-semibold text-white bg-gray-600 rounded-md shadow-sm hover:bg-gray-500">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Voltar
    </a>
</div>
@endsection

@section('content')
<div class="container px-4 mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Editar Cotação #{{ $quote->quote_number }}</h1>
        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
            {{ $quote->status === 'draft' ? 'bg-gray-100 text-gray-800' :
               ($quote->status === 'sent' ? 'bg-yellow-100 text-yellow-800' :
               ($quote->status === 'accepted' ? 'bg-green-100 text-green-800' :
               ($quote->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-300 text-gray-900'))) }}">
            {{ $quote->status === 'draft' ? 'Rascunho' :
               ($quote->status === 'sent' ? 'Enviada' :
               ($quote->status === 'accepted' ? 'Aceita' :
               ($quote->status === 'rejected' ? 'Rejeitada' : 'Expirada'))) }}
        </span>
    </div>

    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="p-6">
            <form action="{{ route('quotes.update', $quote) }}" method="POST" id="quoteForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="sm:col-span-2">
                        <label for="client_id" class="block text-sm font-medium text-gray-700">Cliente *</label>
                        <select name="client_id" id="client_id"
                                class="mt-1 block w-full rounded-md border-0 py-1.5 pl-3 pr-8 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm @error('client_id') ring-red-500 @enderror"
                                required>
                            <option value="">Selecione um cliente</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $quote->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="quote_date" class="block text-sm font-medium text-gray-700">Data da Cotação *</label>
                        <input type="date" name="quote_date" id="quote_date"
                               class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm @error('quote_date') ring-red-500 @enderror"
                               value="{{ old('quote_date', $quote->quote_date->format('Y-m-d')) }}" required>
                        @error('quote_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Itens da Cotação -->
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900">Itens da Cotação</h3>
                    <p class="mt-1 text-sm text-gray-500">Adicione os itens da cotação abaixo</p>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300" id="itemsTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Descrição</th>
                                    <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Qtd</th>
                                    <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Preço Unit.</th>
                                    <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">IVA (%)</th>
                                    <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Total</th>
                                    <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Ação</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody" class="bg-white divide-y divide-gray-200">
                                @foreach($quote->items as $index => $item)
                                <tr class="item-row">
                                    <td class="px-4 py-3">
                                        <input type="text" name="items[{{ $index }}][description]"
                                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm"
                                               placeholder="Descrição do item" value="{{ old('items.' . $index . '.description', $item->description) }}" required>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" name="items[{{ $index }}][quantity]"
                                               class="item-quantity block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm"
                                               value="{{ old('items.' . $index . '.quantity', $item->quantity) }}" min="1" step="1" required>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" name="items[{{ $index }}][unit_price]"
                                               class="item-price block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm"
                                               value="{{ old('items.' . $index . '.unit_price', $item->unit_price) }}" min="0" step="0.01" required>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" name="items[{{ $index }}][tax_rate]"
                                               class="item-tax block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm"
                                               value="{{ old('items.' . $index . '.tax_rate', $item->tax_rate) }}" min="0" max="100" step="0.01">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" class="item-total block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 bg-gray-50 sm:text-sm"
                                               value="{{ number_format($item->quantity * $item->unit_price * (1 + $item->tax_rate / 100), 2) }}" readonly>
                                    </td>
                                    <td class="px-4 py-3">
                                        <button type="button" class="text-red-600 remove-item hover:text-red-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <button type="button" id="addItem"
                            class="inline-flex items-center px-3 py-2 mt-4 text-sm font-semibold text-white bg-green-600 rounded-md shadow-sm hover:bg-green-500">
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
                        </svg>
                        Adicionar Item
                    </button>
                </div>

                <!-- Totais e Observações -->
                <div class="grid grid-cols-1 gap-6 mt-6 lg:grid-cols-3">
                    <div class="space-y-6 lg:col-span-2">
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Observações</label>
                            <textarea name="notes" id="notes"
                                      class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm"
                                      rows="3">{{ old('notes', $quote->notes) }}</textarea>
                        </div>

                        <div>
                            <label for="terms_conditions" class="block text-sm font-medium text-gray-700">Termos e Condições</label>
                            <textarea name="terms_conditions" id="terms_conditions"
                                      class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm"
                                      rows="3">{{ old('terms_conditions', $quote->terms_conditions) }}</textarea>
                        </div>
                    </div>

                    <div>
                        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Totais</h3>
                            </div>
                            <div class="p-4 space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Subtotal:</span>
                                    <span id="subtotalDisplay" class="font-medium text-gray-900">
                                        {{ number_format($quote->items->sum(fn($item) => $item->quantity * $item->unit_price), 2) }} MT
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">IVA:</span>
                                    <span id="taxDisplay" class="font-medium text-gray-900">
                                        {{ number_format($quote->items->sum(fn($item) => $item->quantity * $item->unit_price * ($item->tax_rate / 100)), 2) }} MT
                                    </span>
                                </div>
                                <div class="flex justify-between pt-2 text-lg font-semibold text-gray-900 border-t">
                                    <span>Total:</span>
                                    <span id="totalDisplay">
                                        {{ number_format($quote->items->sum(fn($item) => $item->quantity * $item->unit_price * (1 + $item->tax_rate / 100)), 2) }} MT
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center mt-6 gap-x-4">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Salvar Cotação
                    </button>
                    <a href="{{ route('quotes.show', $quote) }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-md shadow-sm hover:bg-gray-200">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = {{ $quote->items->count() }};

    // Adicionar item
    document.getElementById('addItem').addEventListener('click', function() {
        const tbody = document.getElementById('itemsTableBody');
        const newRow = document.querySelector('.item-row').cloneNode(true);

        // Atualizar índices dos inputs
        const inputs = newRow.querySelectorAll('input');
        inputs.forEach(input => {
            if (input.name) {
                input.name = input.name.replace(/\[\d+\]/, '[' + itemIndex + ']');
                input.value = input.name.includes('quantity') ? '1' :
                             input.name.includes('tax_rate') ? '{{ $settings->default_tax_rate }}' :
                             input.name.includes('unit_price') ? '0' : '';
                if (input.classList.contains('item-total')) {
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
});
</script>
@endsection