@extends('layouts.app')

@section('title', 'Configurações da Empresa')

@section('content')
<div class="py-4 container-fluid">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Configurações da Empresa</h1>
                <p class="mt-2 text-gray-600">Gerencie as informações da sua empresa</p>
            </div>
            <div>
                <a href="{{ route('settings.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Formulário de Configurações -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Informações da Empresa</h3>
                            <p class="text-sm text-gray-600">Configure os dados básicos da empresa</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <form action="{{ route('settings.company.update') }}" method="POST" enctype="multipart/form-data" id="companyForm">
                        @csrf

                        <div class="space-y-6">
                            <!-- Nome e NUIT -->
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- Nome da Empresa -->
                                <div>
                                    <label for="company_name" class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Nome da Empresa *
                                    </label>
                                    <input type="text"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('company_name') border-red-300 @enderror"
                                           id="company_name"
                                           name="company_name"
                                           value="{{ old('company_name', $settings->company_name) }}"
                                           required>
                                    @error('company_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- NUIT -->
                                <div>
                                    <label for="tax_number" class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                        <svg class="w-4 h-4 mr-2 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                        </svg>
                                        NUIT
                                    </label>
                                    <input type="text"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tax_number') border-red-300 @enderror"
                                           id="tax_number"
                                           name="tax_number"
                                           value="{{ old('tax_number', $settings->tax_number) }}"
                                           placeholder="123456789">
                                    @error('tax_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Endereço -->
                            <div>
                                <label for="company_address" class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <svg class="w-4 h-4 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Endereço Completo *
                                </label>
                                <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('company_address') border-red-300 @enderror"
                                          id="company_address"
                                          name="company_address"
                                          rows="3"
                                          required>{{ old('company_address', $settings->company_address) }}</textarea>
                                @error('company_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Telefone e E-mail -->
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- Telefone -->
                                <div>
                                    <label for="company_phone" class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                        <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        Telefone
                                    </label>
                                    <input type="text"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent phone-mask @error('company_phone') border-red-300 @enderror"
                                           id="company_phone"
                                           name="company_phone"
                                           value="{{ old('company_phone', $settings->company_phone) }}"
                                           placeholder="(84) 1234-5678">
                                    @error('company_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- E-mail -->
                                <div>
                                    <label for="company_email" class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        E-mail
                                    </label>
                                    <input type="email"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('company_email') border-red-300 @enderror"
                                           id="company_email"
                                           name="company_email"
                                           value="{{ old('company_email', $settings->company_email) }}"
                                           placeholder="contato@empresa.com">
                                    @error('company_email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Website -->
                            <div>
                                <label for="company_website" class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <svg class="w-4 h-4 mr-2 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Website
                                </label>
                                <input type="url"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('company_website') border-red-300 @enderror"
                                       id="company_website"
                                       name="company_website"
                                       value="{{ old('company_website', $settings->company_website) }}"
                                       placeholder="https://www.empresa.com">
                                @error('company_website')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Logo -->
                            <div>
                                <label for="company_logo" class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <svg class="w-4 h-4 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Logo da Empresa
                                </label>
                                <input type="file"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('company_logo') border-red-300 @enderror"
                                       id="company_logo"
                                       name="company_logo"
                                       accept="image/*">
                                @error('company_logo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-gray-500">
                                    Formatos aceitos: JPEG, PNG, JPG, GIF. Tamanho máximo: 2MB
                                </p>

                                @if($settings->company_logo)
                                    <div class="mt-4">
                                        <img src="{{ asset('storage/' . $settings->company_logo) }}"
                                             alt="Logo atual"
                                             class="border border-gray-200 rounded-lg shadow-sm"
                                             style="max-height: 100px;">
                                        <p class="mt-2 text-sm text-gray-500">Logo atual</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Divisor -->
                            <div class="border-t border-gray-200"></div>

                            <!-- Botões de Ação -->
                            <div class="flex justify-between">
                                <button type="button"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 border border-red-200 rounded-md bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                        onclick="resetForm()">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Resetar
                                </button>
                                <button type="submit"
                                        class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                    </svg>
                                    Salvar Configurações
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Preview da Empresa -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Preview</h3>
                            <p class="text-sm text-gray-600">Como aparecerá nos documentos</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 text-center">
                    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg bg-gray-50">
                        @if($settings->company_logo)
                            <img src="{{ asset('storage/' . $settings->company_logo) }}"
                                 alt="Logo"
                                 class="mx-auto mb-4"
                                 style="max-height: 80px;">
                        @else
                            <div class="inline-flex items-center justify-center w-20 h-20 mb-4 text-2xl font-bold text-white bg-blue-600 rounded-full">
                                {{ strtoupper(substr($settings->company_name, 0, 2)) }}
                            </div>
                        @endif

                        <h5 class="mb-3 text-lg font-semibold text-gray-900 company-name">{{ $settings->company_name }}</h5>

                        <div class="space-y-2 text-sm text-gray-600">
                            @if($settings->tax_number)
                                <div class="flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                    NUIT: {{ $settings->tax_number }}
                                </div>
                            @endif

                            <div class="flex items-start justify-center">
                                <svg class="w-4 h-4 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-left">{{ Str::limit($settings->company_address, 50) }}</span>
                            </div>

                            @if($settings->company_phone)
                                <div class="flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $settings->company_phone }}
                                </div>
                            @endif

                            @if($settings->company_email)
                                <div class="flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ $settings->company_email }}
                                </div>
                            @endif

                            @if($settings->company_website)
                                <div class="flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <a href="{{ $settings->company_website }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        {{ $settings->company_website }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 text-center bg-gray-50 rounded-b-xl">
                    <div class="flex items-center justify-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Essas informações aparecerão nas suas facturas e orçamentos
                    </div>
                </div>
            </div>

            <!-- Dicas -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 rounded-lg bg-cyan-100">
                            <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Dicas</h3>
                            <p class="text-sm text-gray-600">Recomendações importantes</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-3 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm text-gray-700">O NUIT é obrigatório para emissão de facturas</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-3 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm text-gray-700">Verifique o preview antes de salvar</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function resetForm() {
    if (confirm('Tem certeza que deseja resetar o formulário?')) {
        document.querySelector('#companyForm').reset();

        // Reset do preview
        const companyNamePreview = document.querySelector('.company-name');
        if (companyNamePreview) {
            companyNamePreview.textContent = 'Nome da Empresa';
        }

        showNotification('Formulário resetado', 'info');
    }
}

// Preview em tempo real
document.addEventListener('DOMContentLoaded', function() {
    const companyNameInput = document.getElementById('company_name');
    const companyNamePreview = document.querySelector('.company-name');

    if (companyNameInput && companyNamePreview) {
        companyNameInput.addEventListener('input', function() {
            companyNamePreview.textContent = this.value || 'Nome da Empresa';
        });
    }

    // Máscara para telefone
    const phoneInput = document.getElementById('company_phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            e.target.value = value;
        });
    }

    // Preview do logo em tempo real
    const logoInput = document.getElementById('company_logo');
    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Criar/atualizar preview do logo
                    let previewImg = document.querySelector('.logo-preview');
                    if (!previewImg) {
                        previewImg = document.createElement('img');
                        previewImg.className = 'logo-preview mx-auto mb-4';
                        previewImg.style.maxHeight = '80px';

                        // Inserir antes do nome da empresa
                        const companyNameElement = document.querySelector('.company-name');
                        companyNameElement.parentNode.insertBefore(previewImg, companyNameElement);

                        // Esconder o círculo com iniciais
                        const initialsCircle = document.querySelector('.company-name').previousElementSibling;
                        if (initialsCircle && initialsCircle.tagName === 'DIV') {
                            initialsCircle.style.display = 'none';
                        }
                    }
                    previewImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Validação de formulário em tempo real
    const form = document.getElementById('companyForm');
    const inputs = form.querySelectorAll('input[required], textarea[required]');

    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });

        input.addEventListener('input', function() {
            if (this.classList.contains('border-red-300')) {
                validateField(this);
            }
        });
    });

    function validateField(field) {
        const isValid = field.checkValidity();

        if (isValid) {
            field.classList.remove('border-red-300');
            field.classList.add('border-green-300');
        } else {
            field.classList.remove('border-green-300');
            field.classList.add('border-red-300');
        }
    }

    // Animação de sucesso no submit
    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        submitBtn.innerHTML = `
            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Salvando...
        `;
        submitBtn.disabled = true;

        // Restaurar o botão após 5 segundos (fallback)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 5000);
    });
});

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
</script>
@endpush

@push('styles')
<style>
/* Animações customizadas */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Efeitos hover para cards */
.hover\:shadow-lg:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Transições suaves */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Efeitos de focus aprimorados */
.focus\:ring-2:focus {
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

/* Animação para notificações */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.notification {
    animation: slideInRight 0.3s ease-out;
}

/* Validação visual melhorada */
.border-green-300 {
    border-color: #86efac;
    box-shadow: 0 0 0 1px #86efac;
}

.border-red-300 {
    border-color: #fca5a5;
    box-shadow: 0 0 0 1px #fca5a5;
}

/* Preview aprimorado */
.company-preview {
    transition: all 0.3s ease;
}

.company-preview:hover {
    transform: translateY(-2px);
}

/* Loading states */
button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Responsive melhorias */
@media (max-width: 768px) {
    .grid-cols-1.md\:grid-cols-2 {
        grid-template-columns: 1fr;
    }

    .lg\:col-span-2 {
        grid-column: span 1;
    }

    .space-y-6 > * + * {
        margin-top: 1.5rem;
    }
}

/* Melhorias de acessibilidade */
input:focus, textarea:focus, select:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
}

/* Estados de error mais visíveis */
.border-red-300:focus {
    border-color: #ef4444;
    box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.5);
}

/* Estados de sucesso */
.border-green-300:focus {
    border-color: #22c55e;
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.5);
}
</style>
@endpush
@endsection 
