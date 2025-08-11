@extends('layouts.admin')

@section('title', 'Configurações de Faturação')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Configurações de Faturação</h1>
                    <p class="mt-1 text-sm text-gray-600">Gerencie configurações relacionadas à faturação e pagamentos</p>
                </div>
                <div>
                    <a href="{{ route('admin.settings.index') }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-8">
        <form action="{{ route('admin.settings.billing.update') }}" method="POST" id="billingForm">
            @csrf
            @method('PUT')

            <!-- Configurações de Moeda e Formatos -->
            <div class="mb-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Configurações de Moeda</h3>
                            <p class="text-sm text-gray-600">Configurações relacionadas à moeda e formatação de valores</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <label for="default_currency" class="block text-sm font-medium text-gray-700">Moeda Padrão</label>
                            <select name="default_currency" id="default_currency"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="MZN" {{ old('default_currency', $settings['default_currency'] ?? 'MZN') == 'MZN' ? 'selected' : '' }}>MZN - Metical Moçambicano</option>
                                <option value="USD" {{ old('default_currency', $settings['default_currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD - Dólar Americano</option>
                                <option value="EUR" {{ old('default_currency', $settings['default_currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="ZAR" {{ old('default_currency', $settings['default_currency'] ?? '') == 'ZAR' ? 'selected' : '' }}>ZAR - Rand Sul-Africano</option>
                            </select>
                            @error('default_currency')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="currency_symbol" class="block text-sm font-medium text-gray-700">Símbolo da Moeda</label>
                            <input type="text" name="currency_symbol" id="currency_symbol"
                                   value="{{ old('currency_symbol', $settings['currency_symbol'] ?? 'MT') }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="MT">
                            @error('currency_symbol')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="currency_position" class="block text-sm font-medium text-gray-700">Posição do Símbolo</label>
                            <select name="currency_position" id="currency_position"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="before" {{ old('currency_position', $settings['currency_position'] ?? 'after') == 'before' ? 'selected' : '' }}>Antes (MT 100.00)</option>
                                <option value="after" {{ old('currency_position', $settings['currency_position'] ?? 'after') == 'after' ? 'selected' : '' }}>Depois (100.00 MT)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="decimal_places" class="block text-sm font-medium text-gray-700">Casas Decimais</label>
                            <input type="number" name="decimal_places" id="decimal_places" min="0" max="4"
                                   value="{{ old('decimal_places', $settings['decimal_places'] ?? 2) }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>

                        <div>
                            <label for="thousand_separator" class="block text-sm font-medium text-gray-700">Separador de Milhares</label>
                            <select name="thousand_separator" id="thousand_separator"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="," {{ old('thousand_separator', $settings['thousand_separator'] ?? ',') == ',' ? 'selected' : '' }}>Vírgula (,)</option>
                                <option value="." {{ old('thousand_separator', $settings['thousand_separator'] ?? '') == '.' ? 'selected' : '' }}>Ponto (.)</option>
                                <option value=" " {{ old('thousand_separator', $settings['thousand_separator'] ?? '') == ' ' ? 'selected' : '' }}>Espaço ( )</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações de Impostos -->
            <div class="mb-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Configurações de Impostos</h3>
                            <p class="text-sm text-gray-600">Configurações de IVA e outros impostos</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="default_tax_rate" class="block text-sm font-medium text-gray-700">Taxa de IVA Padrão (%)</label>
                            <input type="number" name="default_tax_rate" id="default_tax_rate" min="0" max="100" step="0.01"
                                   value="{{ old('default_tax_rate', $settings['default_tax_rate'] ?? 16) }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('default_tax_rate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tax_number" class="block text-sm font-medium text-gray-700">NUIT da Empresa</label>
                            <input type="text" name="tax_number" id="tax_number"
                                   value="{{ old('tax_number', $settings['tax_number'] ?? '') }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="123456789">
                            @error('tax_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="tax_inclusive" id="tax_inclusive" value="1"
                                   {{ old('tax_inclusive', $settings['tax_inclusive'] ?? false) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="tax_inclusive" class="font-medium text-gray-700">Preços incluem IVA</label>
                            <p class="text-gray-500">Quando ativado, os preços dos produtos já incluem o IVA</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações de Fatura -->
            <div class="mb-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Configurações de Fatura</h3>
                            <p class="text-sm text-gray-600">Numeração e formato das faturas</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="invoice_prefix" class="block text-sm font-medium text-gray-700">Prefixo da Fatura</label>
                            <input type="text" name="invoice_prefix" id="invoice_prefix"
                                   value="{{ old('invoice_prefix', $settings['invoice_prefix'] ?? 'FAT') }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="FAT">
                            @error('invoice_prefix')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="invoice_start_number" class="block text-sm font-medium text-gray-700">Número Inicial</label>
                            <input type="number" name="invoice_start_number" id="invoice_start_number" min="1"
                                   value="{{ old('invoice_start_number', $settings['invoice_start_number'] ?? 1) }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            @error('invoice_start_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="invoice_due_days" class="block text-sm font-medium text-gray-700">Prazo Padrão de Pagamento (dias)</label>
                            <input type="number" name="invoice_due_days" id="invoice_due_days" min="0" max="365"
                                   value="{{ old('invoice_due_days', $settings['invoice_due_days'] ?? 30) }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            @error('invoice_due_days')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="invoice_footer" class="block text-sm font-medium text-gray-700">Rodapé da Fatura</label>
                            <textarea name="invoice_footer" id="invoice_footer" rows="3"
                                      class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
                                      placeholder="Obrigado pela preferência!">{{ old('invoice_footer', $settings['invoice_footer'] ?? '') }}</textarea>
                            @error('invoice_footer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações de Pagamento -->
            <div class="mb-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Métodos de Pagamento</h3>
                            <p class="text-sm text-gray-600">Configure os métodos de pagamento aceitos</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="payment_methods[]" id="cash" value="cash"
                                       {{ in_array('cash', old('payment_methods', $settings['payment_methods'] ?? ['cash'])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="cash" class="font-medium text-gray-700">Dinheiro</label>
                                <p class="text-gray-500">Pagamento em dinheiro</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="payment_methods[]" id="bank_transfer" value="bank_transfer"
                                       {{ in_array('bank_transfer', old('payment_methods', $settings['payment_methods'] ?? [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="bank_transfer" class="font-medium text-gray-700">Transferência Bancária</label>
                                <p class="text-gray-500">Transferência para conta bancária</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="payment_methods[]" id="mpesa" value="mpesa"
                                       {{ in_array('mpesa', old('payment_methods', $settings['payment_methods'] ?? [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="mpesa" class="font-medium text-gray-700">M-Pesa</label>
                                <p class="text-gray-500">Pagamento via M-Pesa</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="payment_methods[]" id="emola" value="emola"
                                       {{ in_array('emola', old('payment_methods', $settings['payment_methods'] ?? [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="emola" class="font-medium text-gray-700">e-Mola</label>
                                <p class="text-gray-500">Pagamento via e-Mola</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="payment_methods[]" id="cheque" value="cheque"
                                       {{ in_array('cheque', old('payment_methods', $settings['payment_methods'] ?? [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="cheque" class="font-medium text-gray-700">Cheque</label>
                                <p class="text-gray-500">Pagamento por cheque</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="payment_methods[]" id="credit_card" value="credit_card"
                                       {{ in_array('credit_card', old('payment_methods', $settings['payment_methods'] ?? [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="credit_card" class="font-medium text-gray-700">Cartão de Crédito</label>
                                <p class="text-gray-500">Pagamento por cartão de crédito</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dados Bancários -->
            <div class="mb-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-indigo-100 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Dados Bancários</h3>
                            <p class="text-sm text-gray-600">Informações bancárias para transferências</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="bank_name" class="block text-sm font-medium text-gray-700">Nome do Banco</label>
                            <input type="text" name="bank_name" id="bank_name"
                                   value="{{ old('bank_name', $settings['bank_name'] ?? '') }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Banco Comercial e de Investimentos">
                            @error('bank_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="bank_account" class="block text-sm font-medium text-gray-700">Número da Conta</label>
                            <input type="text" name="bank_account" id="bank_account"
                                   value="{{ old('bank_account', $settings['bank_account'] ?? '') }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="1234567890">
                            @error('bank_account')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="iban" class="block text-sm font-medium text-gray-700">IBAN</label>
                            <input type="text" name="iban" id="iban"
                                   value="{{ old('iban', $settings['iban'] ?? '') }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="MZ59000100000001234567890">
                            @error('iban')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="swift_code" class="block text-sm font-medium text-gray-700">Código SWIFT</label>
                            <input type="text" name="swift_code" id="swift_code"
                                   value="{{ old('swift_code', $settings['swift_code'] ?? '') }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="BCOMMZMX">
                            @error('swift_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="resetForm()"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Salvar Configurações
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function resetForm() {
    if (confirm('Tem certeza que deseja descartar as alterações?')) {
        document.getElementById('billingForm').reset();
    }
}

// Preview da formatação de moeda
function updateCurrencyPreview() {
    const symbol = document.getElementById('currency_symbol').value || 'MT';
    const position = document.getElementById('currency_position').value;
    const decimals = parseInt(document.getElementById('decimal_places').value) || 2;
    const thousand = document.getElementById('thousand_separator').value;

    let amount = '1000.50';
    let formatted = parseFloat(amount).toFixed(decimals);

    if (thousand) {
        formatted = formatted.replace(/\B(?=(\d{3})+(?!\d))/g, thousand);
    }

    if (position === 'before') {
        formatted = symbol + ' ' + formatted;
    } else {
        formatted = formatted + ' ' + symbol;
    }

    // Adicionar preview se não existir
    if (!document.getElementById('currencyPreview')) {
        const preview = document.createElement('div');
        preview.id = 'currencyPreview';
        preview.className = 'mt-2 text-sm text-gray-600';
        document.getElementById('currency_symbol').closest('.grid').after(preview);
    }

    document.getElementById('currencyPreview').innerHTML = '<strong>Preview:</strong> ' + formatted;
}

// Adicionar event listeners
document.getElementById('currency_symbol').addEventListener('input', updateCurrencyPreview);
document.getElementById('currency_position').addEventListener('change', updateCurrencyPreview);
document.getElementById('decimal_places').addEventListener('input', updateCurrencyPreview);
document.getElementById('thousand_separator').addEventListener('change', updateCurrencyPreview);

// Inicializar preview
document.addEventListener('DOMContentLoaded', updateCurrencyPreview);
</script>
@endpush
@endsection
