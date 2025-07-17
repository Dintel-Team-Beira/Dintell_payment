@extends('layouts.app')

@section('title', 'Nova Nota de Débito')

@section('content')
<div class="container px-4 mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Nova Nota de Débito</h1>
        <p class="mt-2 text-gray-600">Crie uma nota de débito para cobranças adicionais</p>
    </div>

    <form action="{{ route('debit-notes.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <!-- Informações Básicas -->
                <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Informações Básicas</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Cliente *</label>
                            <select name="client_id" id="client_id" class="w-full border-gray-300 rounded-lg" required>
                                <option value="">Selecione um cliente</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}"
                                            {{ ($invoice && $invoice->client_id == $client->id) ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Data *</label>
                            <input type="date" name="invoice_date" id="invoice_date"
                                   value="{{ date('Y-m-d') }}"
                                   class="w-full border-gray-300 rounded-lg" required>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Vencimento *</label>
                            <input type="date" name="due_date" id="due_date"
                                   value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                                   class="w-full border-gray-300 rounded-lg" required>
                        </div>

                        <div>
                            @if($invoice)
                                <input type="hidden" name="related_invoice_id" value="{{ $invoice->id }}">
                                <label class="block mb-1 text-sm font-medium text-gray-700">Fatura Relacionada</label>
                                <input type="text" value="{{ $invoice->invoice_number }}"
                                       class="w-full border-gray-300 rounded-lg bg-gray-50" readonly>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block mb-1 text-sm font-medium text-gray-700">
                            Motivo do Débito *
                        </label>
                        <textarea name="adjustment_reason" rows="3"
                                  class="w-full border-gray-300 rounded-lg"
                                  placeholder="Descreva o motivo desta cobrança adicional..."
                                  required></textarea>
                    </div>
                </div>

                <!-- Itens -->
                <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Itens a Debitar</h3>

                    <div id="debit-items-container">
                        <!-- Items dinâmicos -->
                    </div>

                    <button type="button" onclick="addDebitItem()"
                            class="px-4 py-2 mt-4 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Adicionar Item
                    </button>
                </div>

                <!-- Observações -->
                <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Observações</h3>
                    <textarea name="notes" rows="3"
                              class="w-full border-gray-300 rounded-lg"
                              placeholder="Observações adicionais..."></textarea>
                </div>
            </div>

            <!-- Sidebar com totais -->
            <div class="space-y-6">
                <div class="sticky p-6 bg-white border border-gray-200 shadow-sm rounded-xl top-6">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Resumo</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span id="debit-subtotal">0,00 MT</span>
                        </div>
                        <div class="flex justify-between">
                            <span>IVA:</span>
                            <span id="debit-tax">0,00 MT</span>
                        </div>
                        <div class="flex justify-between pt-3 font-bold border-t">
                            <span>Total a Debitar:</span>
                            <span id="debit-total" class="text-green-600">0,00 MT</span>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full py-3 mt-6 font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-700">
                        Criar Nota de Débito
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let debitItemIndex = 0;

function addDebitItem() {
    const container = document.getElementById('debit-items-container');
    const itemHtml = `
        <div class="p-4 mb-4 border rounded-lg item-row" data-index="${debitItemIndex}">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-5">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Descrição</label>
                    <input type="text" name="items[${debitItemIndex}][description]"
                           class="w-full border-gray-300 rounded" required>
                </div>
                <div class="col-span-2">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Qtd</label>
                    <input type="number" name="items[${debitItemIndex}][quantity]"
                           class="w-full border-gray-300 rounded item-quantity"
                           min="0.01" step="0.01" value="1" required>
                </div>
                <div class="col-span-2">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Preço Unit.</label>
                    <input type="number" name="items[${debitItemIndex}][unit_price]"
                           class="w-full border-gray-300 rounded item-price"
                           min="0" step="0.01" value="0" required>
                </div>
                <div class="col-span-2">
                    <label class="block mb-1 text-sm font-medium text-gray-700">IVA %</label>
                    <input type="number" name="items[${debitItemIndex}][tax_rate]"
                           class="w-full border-gray-300 rounded item-tax"
                           min="0" max="100" step="0.01" value="16">
                </div>
                <div class="flex items-end col-span-1">
                    <button type="button" onclick="removeDebitItem(this)"
                            class="w-full py-2 text-white bg-red-500 rounded hover:bg-red-600">
                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', itemHtml);
    debitItemIndex++;
    attachDebitEventListeners();
}

function removeDebitItem(button) {
    button.closest('.item-row').remove();
    calculateDebitTotals();
}

function attachDebitEventListeners() {
    document.querySelectorAll('.item-quantity, .item-price, .item-tax').forEach(input => {
        input.removeEventListener('input', calculateDebitTotals);
        input.addEventListener('input', calculateDebitTotals);
    });
}

function calculateDebitTotals() {
    let subtotal = 0;
    let taxAmount = 0;

    document.querySelectorAll('.item-row').forEach(row => {
        const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const taxRate = parseFloat(row.querySelector('.item-tax').value) || 0;

        const itemSubtotal = quantity * price;
        const itemTax = itemSubtotal * (taxRate / 100);

        subtotal += itemSubtotal;
        taxAmount += itemTax;
    });

    const total = subtotal + taxAmount;

    document.getElementById('debit-subtotal').textContent = formatCurrency(subtotal);
    document.getElementById('debit-tax').textContent = formatCurrency(taxAmount);
    document.getElementById('debit-total').textContent = formatCurrency(total);
}

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-MZ', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value) + ' MT';
}

// Adicionar primeiro item ao carregar
addDebitItem();
</script>
@endpush
@endsection
