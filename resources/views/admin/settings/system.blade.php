@extends('layouts.admin')

@section('title', 'Configurações do Sistema')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="mx-5 bg-white rounded-md shadow">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Configurações do Sistema</h1>
                    <p class="mt-1 text-sm text-gray-600">Gerencie as configurações gerais do sistema</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="exportSettings()"
                            class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Exportar
                    </button>
                    <label for="importFile" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-blue-600 border border-transparent rounded-md cursor-pointer hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Importar
                    </label>
                    <input type="file" id="importFile" class="hidden" accept=".json" onchange="importSettings(this)">
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-6">
        <form action="{{ route('admin.settings.system.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Informações Básicas -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="flex items-center text-lg font-medium text-gray-900">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Informações Básicas
                    </h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="app_name" class="block text-sm font-medium text-gray-700">Nome da Aplicação</label>
                            <input type="text" name="app_name" id="app_name"
                                   value="{{ old('app_name', $settings['app_name']) }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   required>
                            @error('app_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="app_description" class="block text-sm font-medium text-gray-700">Descrição</label>
                            <input type="text" name="app_description" id="app_description"
                                   value="{{ old('app_description', $settings['app_description']) }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('app_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="app_logo" class="block text-sm font-medium text-gray-700">Logo da Aplicação</label>
                            <div class="flex items-center mt-1 space-x-4">
                                @if(isset($settings['app_logo']) && $settings['app_logo'])
                                    <img src="{{ asset('storage/' . $settings['app_logo']) }}"
                                         alt="Logo atual" class="object-contain w-16 h-16 border rounded-lg">
                                @else
                                    <div class="flex items-center justify-center w-16 h-16 bg-gray-200 rounded-lg">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <input type="file" name="app_logo" id="app_logo" accept="image/*"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            @error('app_logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="app_favicon" class="block text-sm font-medium text-gray-700">Favicon</label>
                            <div class="flex items-center mt-1 space-x-4">
                                @if(isset($settings['app_favicon']) && $settings['app_favicon'])
                                    <img src="{{ asset('storage/' . $settings['app_favicon']) }}"
                                         alt="Favicon atual" class="object-contain w-8 h-8 rounded">
                                @else
                                    <div class="flex items-center justify-center w-8 h-8 bg-gray-200 rounded">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <input type="file" name="app_favicon" id="app_favicon" accept="image/*,.ico"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            @error('app_favicon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações Regionais -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="flex items-center text-lg font-medium text-gray-900">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Configurações Regionais
                    </h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700">Fuso Horário</label>
                            <select name="timezone" id="timezone"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="Africa/Maputo" {{ $settings['timezone'] === 'Africa/Maputo' ? 'selected' : '' }}>África/Maputo</option>
                                <option value="Africa/Johannesburg" {{ $settings['timezone'] === 'Africa/Johannesburg' ? 'selected' : '' }}>África/Johannesburg</option>
                                <option value="Europe/Lisbon" {{ $settings['timezone'] === 'Europe/Lisbon' ? 'selected' : '' }}>Europa/Lisboa</option>
                                <option value="UTC" {{ $settings['timezone'] === 'UTC' ? 'selected' : '' }}>UTC</option>
                            </select>
                        </div>

                        <div>
                            <label for="locale" class="block text-sm font-medium text-gray-700">Idioma</label>
                            <select name="locale" id="locale"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="pt_BR" {{ $settings['locale'] === 'pt_BR' ? 'selected' : '' }}>Português (Brasil)</option>
                                <option value="pt_PT" {{ $settings['locale'] === 'pt_PT' ? 'selected' : '' }}>Português (Portugal)</option>
                                <option value="en_US" {{ $settings['locale'] === 'en_US' ? 'selected' : '' }}>English (US)</option>
                            </select>
                        </div>

                        <div>
                            <label for="date_format" class="block text-sm font-medium text-gray-700">Formato de Data</label>
                            <select name="date_format" id="date_format"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="d/m/Y" {{ $settings['date_format'] === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                <option value="m/d/Y" {{ $settings['date_format'] === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                <option value="Y-m-d" {{ $settings['date_format'] === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                            </select>
                        </div>

                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700">Moeda</label>
                            <div class="flex mt-1 rounded-md shadow-sm">
                                <select name="currency" id="currency"
                                        class="flex-1 border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="MZN" {{ $settings['currency'] === 'MZN' ? 'selected' : '' }}>MZN</option>
                                    <option value="USD" {{ $settings['currency'] === 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="EUR" {{ $settings['currency'] === 'EUR' ? 'selected' : '' }}>EUR</option>
                                    <option value="ZAR" {{ $settings['currency'] === 'ZAR' ? 'selected' : '' }}>ZAR</option>
                                </select>
                                <input type="text" name="currency_symbol" id="currency_symbol"
                                       value="{{ old('currency_symbol', $settings['currency_symbol']) }}"
                                       placeholder="MT"
                                       class="w-16 border-gray-300 rounded-r-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações de Acesso -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="flex items-center text-lg font-medium text-gray-900">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Configurações de Acesso
                    </h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="registration_enabled" id="registration_enabled"
                                       value="1" {{ $settings['registration_enabled'] ? 'checked' : '' }}
                                       class="text-blue-600 border-gray-300 rounded shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="registration_enabled" class="block ml-2 text-sm text-gray-900">
                                    Permitir registro de novos usuários
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="email_verification_required" id="email_verification_required"
                                       value="1" {{ $settings['email_verification_required'] ? 'checked' : '' }}
                                       class="text-blue-600 border-gray-300 rounded shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="email_verification_required" class="block ml-2 text-sm text-gray-900">
                                    Exigir verificação de email
                                </label>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label for="session_lifetime" class="block text-sm font-medium text-gray-700">
                                    Tempo de vida da sessão (minutos)
                                </label>
                                <input type="number" name="session_lifetime" id="session_lifetime"
                                       value="{{ old('session_lifetime', $settings['session_lifetime']) }}"
                                       min="5" max="1440"
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="auto_logout_time" class="block text-sm font-medium text-gray-700">
                                    Auto logout por inatividade (minutos)
                                </label>
                                <input type="number" name="auto_logout_time" id="auto_logout_time"
                                       value="{{ old('auto_logout_time', $settings['auto_logout_time']) }}"
                                       min="5" max="240"
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Limites do Sistema -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="flex items-center text-lg font-medium text-gray-900">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Limites do Sistema
                    </h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="max_users_per_company" class="block text-sm font-medium text-gray-700">
                                Máximo de usuários por empresa
                            </label>
                            <input type="number" name="max_users_per_company" id="max_users_per_company"
                                   value="{{ old('max_users_per_company', $settings['max_users_per_company']) }}"
                                   min="1" max="1000"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-xs text-gray-500">
                                Define quantos usuários cada empresa pode ter no sistema
                            </p>
                        </div>

                        <div>
                            <label for="max_invoices_per_month" class="block text-sm font-medium text-gray-700">
                                Máximo de facturas por mês
                            </label>
                            <input type="number" name="max_invoices_per_month" id="max_invoices_per_month"
                                   value="{{ old('max_invoices_per_month', $settings['max_invoices_per_month']) }}"
                                   min="1" max="10000"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-xs text-gray-500">
                                Limite de facturas que podem ser criadas por empresa por mês
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modo de Manutenção -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="flex items-center text-lg font-medium text-gray-900">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        Modo de Manutenção
                    </h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="maintenance_mode" id="maintenance_mode"
                                   value="1" {{ $settings['maintenance_mode'] ? 'checked' : '' }}
                                   class="text-red-600 border-gray-300 rounded shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                        </div>
                        <div class="flex-1 min-w-0">
                            <label for="maintenance_mode" class="text-sm font-medium text-gray-900">
                                Ativar modo de manutenção
                            </label>
                            <p class="text-sm text-gray-500">
                                Quando ativado, apenas administradores podem acessar o sistema
                            </p>
                        </div>
                    </div>

                    <div>
                        <label for="maintenance_message" class="block text-sm font-medium text-gray-700">
                            Mensagem de manutenção
                        </label>
                        <textarea name="maintenance_message" id="maintenance_message" rows="3"
                                  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Sistema em manutenção. Voltamos em breve!">{{ old('maintenance_message', $settings['maintenance_message']) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">
                            Esta mensagem será exibida para os usuários quando o modo de manutenção estiver ativo
                        </p>
                    </div>
                </div>
            </div>

            <!-- Ações do Sistema -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="flex items-center text-lg font-medium text-gray-900">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Ações do Sistema
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <button type="button" onclick="clearCache()"
                                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Limpar Cache
                        </button>

                        <button type="button" onclick="optimizeSystem()"
                                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Otimizar Sistema
                        </button>

                        <button type="button" onclick="showSystemInfo()"
                                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Info do Sistema
                        </button>
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
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Salvar Configurações
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Informações do Sistema -->
<div id="systemInfoModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeSystemInfoModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-blue-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="w-full mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">
                            Informações do Sistema
                        </h3>
                        <div class="mt-4">
                            <div id="systemInfoContent" class="text-sm text-gray-600">
                                <!-- Conteúdo será carregado via JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeSystemInfoModal()"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function clearCache() {
    if (confirm('Tem certeza que deseja limpar o cache do sistema?')) {
        fetch('{{ route("admin.settings.cache.clear") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Cache limpo com sucesso!', 'success');
            } else {
                showNotification('Erro ao limpar cache: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao limpar cache', 'error');
        });
    }
}

function optimizeSystem() {
    if (confirm('Tem certeza que deseja otimizar o sistema?')) {
        fetch('{{ route("admin.settings.system.optimize") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Sistema otimizado com sucesso!', 'success');
            } else {
                showNotification('Erro ao otimizar sistema: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao otimizar sistema', 'error');
        });
    }
}

function showSystemInfo() {
    fetch('{{ route("admin.settings.system.info") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const info = data.data;
                const content = `
                    <div class="space-y-3">
                        <div><strong>PHP:</strong> ${info.php_version}</div>
                        <div><strong>Laravel:</strong> ${info.laravel_version}</div>
                        <div><strong>Servidor:</strong> ${info.server_software}</div>
                        <div><strong>Memory Limit:</strong> ${info.memory_limit}</div>
                        <div><strong>Upload Max:</strong> ${info.upload_max_filesize}</div>
                        <div><strong>Espaço em Disco:</strong> ${info.disk_space.free} livres de ${info.disk_space.total}</div>
                    </div>
                `;
                document.getElementById('systemInfoContent').innerHTML = content;
                document.getElementById('systemInfoModal').classList.remove('hidden');
            } else {
                showNotification('Erro ao obter informações do sistema', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao obter informações do sistema', 'error');
        });
}

function closeSystemInfoModal() {
    document.getElementById('systemInfoModal').classList.add('hidden');
}

function exportSettings() {
    window.location.href = '{{ route("admin.settings.export") }}';
}

function importSettings(input) {
    if (input.files && input.files[0]) {
        const formData = new FormData();
        formData.append('settings_file', input.files[0]);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        fetch('{{ route("admin.settings.import") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                showNotification('Configurações importadas com sucesso!', 'success');
                setTimeout(() => location.reload(), 2000);
            } else {
                showNotification('Erro ao importar configurações', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao importar configurações', 'error');
        });
    }
}

function resetForm() {
    if (confirm('Tem certeza que deseja cancelar? Todas as alterações não salvas serão perdidas.')) {
        location.reload();
    }
}

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
</script>
@endpush
@endsection
