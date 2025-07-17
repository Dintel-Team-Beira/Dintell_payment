@extends('layouts.app')

@section('title', 'Nova Nota de Crédito')

@section('content')
<div class="container px-4 mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Nova Nota de Crédito</h1>
        <p class="mt-2 text-gray-600">Crie uma nota de crédito para ajustar valores de faturas</p>
    </div>

    <form action="{{ route('credit-notes.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <!-- Informações Básicas -->
                <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Informações Básicas</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Cliente *</label>
                            <select name="client_id" class="w-full p-2 border-gray-300 rounded-lg" required>
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
                            <input type="date" name="invoice_date"
                                   value="{{ date('Y-m-d') }}"
                                   class="w-full border-gray-300 rounded-lg" required>
                        </div>
                    </div>

                    @if($invoice)
                        <input type="hidden" name="related_invoice_id" value="{{ $invoice->id }}">
                        <div class="p-4 mt-4 rounded-lg bg-blue-50">
                            <p class="text-sm text-blue-800">
                                <strong>Fatura Relacionada:</strong> {{ $invoice->invoice_number }}
                            </p>
                        </div>
                    @endif

                    <div class="mt-4">
                        <label class="block mb-1 text-sm font-medium text-gray-700">
                            Motivo do Ajuste *
                        </label>
                        <textarea name="adjustment_reason" rows="3"
                                  class="w-full p-2 border-gray-300 rounded-lg"
                                  placeholder="Descreva o motivo desta nota de crédito..."
                                  required></textarea>
                    </div>
                </div>

                <!-- Itens -->
                <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Itens a Creditar</h3>

                    <div id="credit-items-container">
                        <!-- Items dinâmicos -->
                    </div>

                    <button type="button" onclick="addCreditItem()"
                            class="px-4 py-2 mt-4 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Adicionar Item
                    </button>
                </div>

                <!-- Observações -->
                <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Observações</h3>
                    <textarea name="notes" rows="3"
                              class="w-full p-2 border-gray-300 rounded-lg"
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
                            <span id="credit-subtotal">0,00 MT</span>
                        </div>
                        <div class="flex justify-between">
                            <span>IVA:</span>
                            <span id="credit-tax">0,00 MT</span>
                        </div>
                        <div class="flex justify-between pt-3 font-bold border-t">
                            <span>Total a Creditar:</span>
                            <span id="credit-total" class="text-red-600">0,00 MT</span>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full py-3 mt-6 font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700">
                        Criar Nota de Crédito
                    </button>
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
