@extends('layouts.app')

@section('title', 'Nova Venda à Dinheiro')

@section('content')
<div class="container px-4 mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Nova Venda à Dinheiro</h1>
        <p class="mt-2 text-gray-600">Registre uma venda com pagamento imediato</p>
    </div>

    <form id="cashSaleForm" action="{{ route('cash-sales.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Coluna Principal -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Seleção de Cliente -->
                <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Cliente</h3>

                    <select name="client_id" id="client_id" class="w-full border-gray-300 rounded-lg" required>
                        <option value="">Selecione um cliente</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Itens -->
                <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Itens da Venda</h3>
                        <button type="button" onclick="addItem()"
                                class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                            Adicionar Item
                        </button>
                    </div>

                    <div id="items-container">
                        <!-- Items serão adicionados aqui -->
                    </div>
                </div>

                <!-- Observações -->
                <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Observações</h3>
                    <textarea name="notes" rows="3"
                              class="w-full border-gray-300 rounded-lg"
                              placeholder="Observações opcionais..."></textarea>
                </div>
            </div>

            <!-- Coluna Lateral - Resumo -->
            <div class="space-y-6">
                <!-- Resumo Financeiro -->
                <div class="sticky p-6 bg-white border border-gray-200 shadow-sm rounded-xl top-6">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Resumo da Venda</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span id="subtotal" class="font-medium">0,00 MT</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600">IVA:</span>
                            <span id="tax" class="font-medium">0,00 MT</span>
                        </div>

                        <!-- Desconto -->
                        <div class="pt-3 border-t">
                            <div class="mb-2">
                                <label class="text-sm text-gray-600">Desconto:</label>
                                <div class="flex gap-2 mt-1">
                                    <input type="number" name="discount_percentage"
                                           id="discount_percentage"
                                           min="0" max="100" step="0.01"
                                           class="w-20 text-sm border-gray-300 rounded"
                                           placeholder="%">
                                    <input type="number" name="discount_amount"
                                           id="discount_amount"
                                           min="0" step="0.01"
                                           class="flex-1 text-sm border-gray-300 rounded"
                                           placeholder="Valor">
                                </div>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Desconto:</span>
                                <span id="discount_display" class="font-medium text-red-600">0,00 MT</span>
                            </div>
                        </div>

                        <div class="pt-3 pb-3 border-t">
                            <div class="flex justify-between text-lg font-bold">
                                <span>TOTAL:</span>
                                <span id="total" class="text-blue-600">0,00 MT</span>
                            </div>
                        </div>

                        <!-- Pagamento -->
                        <div class="pt-3 border-t">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Valor Recebido:
                            </label>
                            <input type="number" name="cash_received" id="cash_received"
                                   min="0" step="0.01" required
                                   class="w-full text-lg font-bold text-center border-gray-300 rounded-lg"
                                   placeholder="0,00">
                        </div>

                        <div class="p-3 rounded-lg bg-gray-50">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Troco:</span>
                                <span id="change" class="text-green-600">0,00 MT</span>
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="mt-6 space-y-3">
                        <button type="submit" id="submitBtn"
                                class="w-full py-3 font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            Finalizar Venda
                        </button>

                        <a href="{{ route('invoices.index') }}"
                           class="block w-full py-3 font-medium text-center text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let itemIndex = 0;

function addItem() {
    const container = document.getElementById('items-container');
    const itemHtml = `
        <div class="p-4 mb-4 border rounded-lg item-row" data-index="${itemIndex}">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-5">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Descrição</label>
                    <input type="text" name="items[${itemIndex}][description]"
                           class="w-full border-gray-300 rounded" required>
                </div>
                <div class="col-span-2">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Qtd</label>
                    <input type="number" name="items[${itemIndex}][quantity]"
                           class="w-full border-gray-300 rounded item-quantity"
                           min="0.01" step="0.01" value="1" required>
                </div>
                <div class="col-span-2">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Preço Unit.</label>
                    <input type="number" name="items[${itemIndex}][unit_price]"
                           class="w-full border-gray-300 rounded item-price"
                           min="0" step="0.01" value="0" required>
                </div>
                <div class="col-span-2">
                    <label class="block mb-1 text-sm font-medium text-gray-700">IVA %</label>
                    <input type="number" name="items[${itemIndex}][tax_rate]"
                           class="w-full border-gray-300 rounded item-tax"
                           min="0" max="100" step="0.01" value="16">
                </div>
                <div class="flex items-end col-span-1">
                    <button type="button" onclick="removeItem(this)"
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
    itemIndex++;
    attachEventListeners();
}

function removeItem(button) {
    button.closest('.item-row').remove();
    calculateTotals();
}

function attachEventListeners() {
    // Quantidade, preço e taxa
    document.querySelectorAll('.item-quantity, .item-price, .item-tax').forEach(input => {
        input.removeEventListener('input', calculateTotals);
        input.addEventListener('input', calculateTotals);
    });

    // Desconto
    document.getElementById('discount_percentage').addEventListener('input', function() {
        if (this.value) {
            document.getElementById('discount_amount').value = '';
        }
        calculateTotals();
    });

    document.getElementById('discount_amount').addEventListener('input', function() {
        if (this.value) {
            document.getElementById('discount_percentage').value = '';
        }
        calculateTotals();
    });

    // Valor recebido
    document.getElementById('cash_received').addEventListener('input', calculateChange);
}

function calculateTotals() {
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

    // Calcular desconto
    let discountAmount = 0;
    const discountPercentage = parseFloat(document.getElementById('discount_percentage').value) || 0;
    const discountValue = parseFloat(document.getElementById('discount_amount').value) || 0;

    if (discountPercentage > 0) {
        discountAmount = (subtotal + taxAmount) * (discountPercentage / 100);
    } else if (discountValue > 0) {
        discountAmount = discountValue;
    }

    const total = subtotal + taxAmount - discountAmount;

    // Atualizar display
    document.getElementById('subtotal').textContent = formatCurrency(subtotal);
    document.getElementById('tax').textContent = formatCurrency(taxAmount);
    document.getElementById('discount_display').textContent = formatCurrency(discountAmount);
    document.getElementById('total').textContent = formatCurrency(total);

    calculateChange();
}

function calculateChange() {
    const total = parseCurrency(document.getElementById('total').textContent);
    const received = parseFloat(document.getElementById('cash_received').value) || 0;
    const change = Math.max(0, received - total);

    document.getElementById('change').textContent = formatCurrency(change);

    // Habilitar/desabilitar botão de submit
    const submitBtn = document.getElementById('submitBtn');
    if (received >= total && total > 0) {
        submitBtn.disabled = false;
    } else {
        submitBtn.disabled = true;
    }
}

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-MZ', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value) + ' MT';
}

function parseCurrency(text) {
    return parseFloat(text.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
}

// Adicionar primeiro item ao carregar
addItem();

// Submit do formulário
document.getElementById('cashSaleForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Processando...';

    try {
        const response = await fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            // Mostrar sucesso
            alert(`Venda realizada!\nFatura: ${data.invoice_number}\nTroco: ${formatCurrency(data.change)}`);
            window.location.href = data.redirect_url;
        } else {
            throw new Error(data.message || 'Erro ao processar venda');
        }
    } catch (error) {
        alert('Erro: ' + error.message);
        submitBtn.disabled = false;
        submitBtn.textContent = 'Finalizar Venda';
    }
});
</script>
@endpush
@endsection
