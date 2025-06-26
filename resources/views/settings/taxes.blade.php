{{-- settings/taxes.blade.php --}}
@extends('layouts.app')

@section('title', 'Configurações de Impostos')

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
        <h1 class="text-2xl font-semibold text-gray-900">Configurações de Impostos</h1>
    </div>

    <!-- Formulário de Impostos -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Configuração de IVA</h3>
            <p class="mt-1 text-sm text-gray-500">Configure as taxas de impostos aplicadas automaticamente</p>
        </div>
        <div class="p-6">
            <form action="{{ route('settings.taxes.update') }}" method="POST" class="space-y-6">
                @csrf

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
                        <p class="mt-1 text-xs text-gray-500">Taxa padrão aplicada em Moçambique: 17%</p>
                    </div>

                    <div>
                        <label for="tax_name" class="block mb-2 text-sm font-medium text-gray-700">
                            Nome do Imposto
                        </label>
                        <input type="text"
                               name="tax_name"
                               id="tax_name"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 @error('tax_name') ring-red-500 @enderror"
                               value="{{ old('tax_name', $settings->tax_name ?? 'IVA') }}"
                               placeholder="IVA">
                        @error('tax_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="tax_registration" class="block mb-2 text-sm font-medium text-gray-700">
                        Número de Registo Fiscal
                    </label>
                    <input type="text"
                           name="tax_registration"
                           id="tax_registration"
                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 @error('tax_registration') ring-red-500 @enderror"
                           value="{{ old('tax_registration', $settings->tax_registration) }}"
                           placeholder="Número de registo na AT">
                    @error('tax_registration')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input id="include_tax_in_price"
                           name="include_tax_in_price"
                           type="checkbox"
                           value="1"
                           {{ old('include_tax_in_price', $settings->include_tax_in_price) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="include_tax_in_price" class="block ml-3 text-sm text-gray-700">
                        Incluir imposto no preço dos produtos
                    </label>
                </div>
                <p class="text-xs text-gray-500 ml-7">Se marcado, os preços já incluem o IVA. Caso contrário, o IVA será adicionado ao total.</p>

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
</div>
@endsection