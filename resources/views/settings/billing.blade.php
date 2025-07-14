@extends('layouts.app')

@section('title', 'Configurações de Faturamento')

@section('header-actions')
<div class="flex items-center gap-x-4">
    <a href="{{ route('settings.index') }}"
       class="flex items-center px-3 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
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
        <h1 class="text-2xl font-semibold text-gray-900">Configurações de Faturamento</h1>
    </div>

    <!-- Formulário -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Configurações de Numeração e Impostos</h3>
            <p class="mt-1 text-sm text-gray-500">Configure prefixos, numeração automática e impostos padrão</p>
        </div>
        <div class="p-6">
            <form action="{{ route('settings.billing.update') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Prefixos e Numeração -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="invoice_prefix" class="block mb-2 text-sm font-medium text-gray-700">
                            Prefixo das Facturas
                        </label>
                        <input type="text"
                               name="invoice_prefix"
                               id="invoice_prefix"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 @error('invoice_prefix') ring-red-500 @enderror"
                               value="{{ old('invoice_prefix', $settings->invoice_prefix) }}"
                               placeholder="FAT"
                               maxlength="10">
                        @error('invoice_prefix')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Exemplo: FAT001234</p>
                    </div>

                    <div>
                        <label for="quote_prefix" class="block mb-2 text-sm font-medium text-gray-700">
                            Prefixo dos Orçamentos
                        </label>
                        <input type="text"
                               name="quote_prefix"
                               id="quote_prefix"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 @error('quote_prefix') ring-red-500 @enderror"
                               value="{{ old('quote_prefix', $settings->quote_prefix) }}"
                               placeholder="COT"
                               maxlength="10">
                        @error('quote_prefix')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Exemplo: COT001234</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="next_invoice_number" class="block mb-2 text-sm font-medium text-gray-700">
                            Próximo Número de Fatura
                        </label>
                        <input type="number"
                               name="next_invoice_number"
                               id="next_invoice_number"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 @error('next_invoice_number') ring-red-500 @enderror"
                               value="{{ old('next_invoice_number', $settings->next_invoice_number) }}"
                               min="1">
                        @error('next_invoice_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Próxima fatura será: {{ $settings->invoice_prefix }}{{ str_pad($settings->next_invoice_number, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>

                    <div>
                        <label for="next_quote_number" class="block mb-2 text-sm font-medium text-gray-700">
                            Próximo Número de Orçamento
                        </label>
                        <input type="number"
                               name="next_quote_number"
                               id="next_quote_number"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 @error('next_quote_number') ring-red-500 @enderror"
                               value="{{ old('next_quote_number', $settings->next_quote_number) }}"
                               min="1">
                        @error('next_quote_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Próximo orçamento será: {{ $settings->quote_prefix }}{{ str_pad($settings->next_quote_number, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>

                <!-- Configurações de Impostos -->
                <div class="pt-6 border-t border-gray-200">
                    <h4 class="mb-4 text-base font-medium text-gray-900">Configurações de Impostos</h4>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="default_tax_rate" class="block mb-2 text-sm font-medium text-gray-700">
                                Taxa Padrão de IVA (%)
                            </label>
                            <input type="number"
                                   name="default_tax_rate"
                                   id="default_tax_rate"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 @error('default_tax_rate') ring-red-500 @enderror"
                                   value="{{ old('default_tax_rate', $settings->default_tax_rate) }}"
                                   step="0.01"
                                   min="0"
                                   max="100">
                            @error('default_tax_rate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Taxa aplicada automaticamente nos novos produtos/serviços</p>
                        </div>

                        <div>
                            <label for="currency" class="block mb-2 text-sm font-medium text-gray-700">
                                Moeda Padrão
                            </label>
                            <select name="currency"
                                    id="currency"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 @error('currency') ring-red-500 @enderror">
                                <option value="MZN" {{ old('currency', $settings->currency ?? 'MZN') === 'MZN' ? 'selected' : '' }}>MZN - Metical</option>
                                <option value="USD" {{ old('currency', $settings->currency) === 'USD' ? 'selected' : '' }}>USD - Dólar Americano</option>
                                <option value="EUR" {{ old('currency', $settings->currency) === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="ZAR" {{ old('currency', $settings->currency) === 'ZAR' ? 'selected' : '' }}>ZAR - Rand Sul-Africano</option>
                            </select>
                            @error('currency')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Configurações de Pagamento -->
                <div class="pt-6 border-t border-gray-200">
                    <h4 class="mb-4 text-base font-medium text-gray-900">Configurações de Pagamento</h4>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="default_payment_terms" class="block mb-2 text-sm font-medium text-gray-700">
                                Prazo Padrão de Pagamento (dias)
                            </label>
                            <input type="number"
                                   name="default_payment_terms"
                                   id="default_payment_terms"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 @error('default_payment_terms') ring-red-500 @enderror"
                                   value="{{ old('default_payment_terms', $settings->default_payment_terms ?? 30) }}"
                                   min="1"
                                   max="365">
                            @error('default_payment_terms')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Prazo padrão para vencimento das facturas</p>
                        </div>

                        <div>
                            <label for="late_fee_percentage" class="block mb-2 text-sm font-medium text-gray-700">
                                Taxa de Juros por Atraso (%)
                            </label>
                            <input type="number"
                                   name="late_fee_percentage"
                                   id="late_fee_percentage"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 @error('late_fee_percentage') ring-red-500 @enderror"
                                   value="{{ old('late_fee_percentage', $settings->late_fee_percentage) }}"
                                   step="0.01"
                                   min="0"
                                   max="100">
                            @error('late_fee_percentage')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Taxa aplicada em facturas vencidas (opcional)</p>
                        </div>
                    </div>
                </div>

                <!-- Formatação -->
                <div class="pt-6 border-t border-gray-200">
                    <h4 class="mb-4 text-base font-medium text-gray-900">Formatação</h4>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Formato de Números
                        </label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input id="number_format_comma"
                                       name="number_format"
                                       type="radio"
                                       value="comma"
                                       {{ old('number_format', $settings->number_format ?? 'comma') === 'comma' ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <label for="number_format_comma" class="block ml-3 text-sm font-medium text-gray-700">
                                    Vírgula para decimais (1.234,56)
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="number_format_dot"
                                       name="number_format"
                                       type="radio"
                                       value="dot"
                                       {{ old('number_format', $settings->number_format) === 'dot' ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <label for="number_format_dot" class="block ml-3 text-sm font-medium text-gray-700">
                                    Ponto para decimais (1,234.56)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="flex justify-end pt-6 space-x-3 border-t border-gray-200">
                    <a href="{{ route('settings.index') }}"
                       class="px-3 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Salvar Configurações
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview das Configurações -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Preview das Configurações</h3>
            <p class="mt-1 text-sm text-gray-500">Veja como as configurações atuais aparecerão</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="p-4 rounded-lg bg-gray-50">
                    <h4 class="mb-3 font-medium text-gray-900">Exemplo de Fatura</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Número:</span>
                            <span class="font-medium">{{ $settings->invoice_prefix }}{{ str_pad($settings->next_invoice_number, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Data:</span>
                            <span>{{ now()->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Vencimento:</span>
                            <span>{{ now()->addDays($settings->default_payment_terms ?? 30)->format('d/m/Y') }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span>1.000,00 {{ $settings->currency ?? 'MZN' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">IVA ({{ $settings->default_tax_rate }}%):</span>
                            <span>{{ number_format($settings->default_tax_rate * 10, 2, ',', '.') }} {{ $settings->currency ?? 'MZN' }}</span>
                        </div>
                        <div class="flex justify-between pt-2 font-medium border-t">
                            <span>Total:</span>
                            <span>{{ number_format(1000 + ($settings->default_tax_rate * 10), 2, ',', '.') }} {{ $settings->currency ?? 'MZN' }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-lg bg-gray-50">
                    <h4 class="mb-3 font-medium text-gray-900">Exemplo de Orçamento</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Número:</span>
                            <span class="font-medium">{{ $settings->quote_prefix }}{{ str_pad($settings->next_quote_number, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Data:</span>
                            <span>{{ now()->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Validade:</span>
                            <span>{{ now()->addDays(30)->format('d/m/Y') }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span>2.500,00 {{ $settings->currency ?? 'MZN' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">IVA ({{ $settings->default_tax_rate }}%):</span>
                            <span>{{ number_format($settings->default_tax_rate * 25, 2, ',', '.') }} {{ $settings->currency ?? 'MZN' }}</span>
                        </div>
                        <div class="flex justify-between pt-2 font-medium border-t">
                            <span>Total:</span>
                            <span>{{ number_format(2500 + ($settings->default_tax_rate * 25), 2, ',', '.') }} {{ $settings->currency ?? 'MZN' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
