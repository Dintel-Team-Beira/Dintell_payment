{{-- resources/views/admin/settings/email/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Configurações de Email')

@section('content')
<div class="container px-4 py-6 mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Configurações de Email</h1>
                <p class="mt-2 text-gray-600">Configure as definições de envio de email do sistema</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.settings.email.preview') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Preview de Emails
                </a>
                <button type="button" id="testConnectionBtn"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 border border-green-200 rounded-md bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Testar Conexão
                </button>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="px-4 py-3 mb-6 text-green-800 border border-green-200 rounded-md bg-green-50">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="px-4 py-3 mb-6 text-red-800 border border-red-200 rounded-md bg-red-50">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                Existem erros nos dados informados:
            </div>
            <ul class="list-disc list-inside ml-7">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Content -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Form Section -->
        <div class="lg:col-span-2">
            <form action="{{ route('admin.settings.email.update') }}" method="POST" id="emailSettingsForm">
                @csrf

                <!-- SMTP Configuration -->
                <div class="mb-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Configurações SMTP</h3>
                                <p class="text-sm text-gray-600">Configure o servidor de email para envio</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- SMTP Host -->
                            <div>
                                <label for="smtp_host" class="block mb-2 text-sm font-medium text-gray-700">
                                    Servidor SMTP *
                                </label>
                                <input type="text"
                                       name="smtp_host"
                                       id="smtp_host"
                                       value="{{ old('smtp_host', $settings['smtp_host']) }}"
                                       placeholder="smtp.gmail.com"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('smtp_host') border-red-300 @enderror"
                                       required>
                                @error('smtp_host')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- SMTP Port -->
                            <div>
                                <label for="smtp_port" class="block mb-2 text-sm font-medium text-gray-700">
                                    Porta SMTP *
                                </label>
                                <select name="smtp_port"
                                        id="smtp_port"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('smtp_port') border-red-300 @enderror"
                                        required>
                                    <option value="587" {{ old('smtp_port', $settings['smtp_port']) == '587' ? 'selected' : '' }}>587 (TLS)</option>
                                    <option value="465" {{ old('smtp_port', $settings['smtp_port']) == '465' ? 'selected' : '' }}>465 (SSL)</option>
                                    <option value="25" {{ old('smtp_port', $settings['smtp_port']) == '25' ? 'selected' : '' }}>25 (Sem criptografia)</option>
                                    <option value="2525" {{ old('smtp_port', $settings['smtp_port']) == '2525' ? 'selected' : '' }}>2525 (Alternativo)</option>
                                </select>
                                @error('smtp_port')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- SMTP Username -->
                            <div>
                                <label for="smtp_username" class="block mb-2 text-sm font-medium text-gray-700">
                                    Usuário/Email *
                                </label>
                                <input type="email"
                                       name="smtp_username"
                                       id="smtp_username"
                                       value="{{ old('smtp_username', $settings['smtp_username']) }}"
                                       placeholder="seu-email@gmail.com"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('smtp_username') border-red-300 @enderror"
                                       required>
                                @error('smtp_username')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- SMTP Password -->
                            <div>
                                <label for="smtp_password" class="block mb-2 text-sm font-medium text-gray-700">
                                    Senha
                                    @if($settings['smtp_username'])
                                        <span class="text-xs text-gray-500">(deixe em branco para manter atual)</span>
                                    @endif
                                </label>
                                <div class="relative">
                                    <input type="password"
                                           name="smtp_password"
                                           id="smtp_password"
                                           placeholder="{{ $settings['smtp_username'] ? '••••••••' : 'Sua senha ou App Password' }}"
                                           class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('smtp_password') border-red-300 @enderror">
                                    <button type="button"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3"
                                            onclick="togglePassword('smtp_password')">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                @error('smtp_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Encryption -->
                        <div>
                            <label for="smtp_encryption" class="block mb-2 text-sm font-medium text-gray-700">
                                Criptografia *
                            </label>
                            <select name="smtp_encryption"
                                    id="smtp_encryption"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('smtp_encryption') border-red-300 @enderror"
                                    required>
                                <option value="tls" {{ old('smtp_encryption', $settings['smtp_encryption']) == 'tls' ? 'selected' : '' }}>TLS (Recomendado)</option>
                                <option value="ssl" {{ old('smtp_encryption', $settings['smtp_encryption']) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="null" {{ old('smtp_encryption', $settings['smtp_encryption']) == null ? 'selected' : '' }}>Nenhuma</option>
                            </select>
                            @error('smtp_encryption')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Sender Configuration -->
                <div class="mb-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Informações do Remetente</h3>
                                <p class="text-sm text-gray-600">Como os emails aparecerão para os destinatários</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- From Address -->
                            <div>
                                <label for="from_address" class="block mb-2 text-sm font-medium text-gray-700">
                                    Email do Remetente *
                                </label>
                                <input type="email"
                                       name="from_address"
                                       id="from_address"
                                       value="{{ old('from_address', $settings['from_address']) }}"
                                       placeholder="noreply@seudominio.com"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('from_address') border-red-300 @enderror"
                                       required>
                                @error('from_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- From Name -->
                            <div>
                                <label for="from_name" class="block mb-2 text-sm font-medium text-gray-700">
                                    Nome do Remetente *
                                </label>
                                <input type="text"
                                       name="from_name"
                                       id="from_name"
                                       value="{{ old('from_name', $settings['from_name']) }}"
                                       placeholder="SFS - Sistema de Facturação"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('from_name') border-red-300 @enderror"
                                       required>
                                @error('from_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col space-y-3 sm:flex-row sm:justify-between sm:items-center sm:space-y-0">
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Campos marcados com * são obrigatórios
                    </div>

                    <div class="flex space-x-3">
                        <button type="button"
                                onclick="window.location.reload()"
                                class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancelar
                        </button>
                        <button type="submit"
                                id="saveButton"
                                class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Salvar Configurações
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar with Information -->
        <div class="space-y-6 lg:col-span-1">
            <!-- Current Status -->
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Status Atual</h3>

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">SMTP Configurado:</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $settings['smtp_host'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $settings['smtp_host'] ? 'Sim' : 'Não' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Remetente Definido:</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $settings['from_address'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $settings['from_address'] ? 'Sim' : 'Não' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Help -->
            <div class="p-6 border border-blue-200 rounded-lg bg-blue-50">
                <h3 class="mb-4 text-lg font-semibold text-blue-900">
                    <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Ajuda Rápida
                </h3>

                <div class="space-y-3 text-sm text-blue-800">
                    <div>
                        <h4 class="font-semibold">Gmail/Google Workspace:</h4>
                        <ul class="ml-2 space-y-1 list-disc list-inside">
                            <li>Host: smtp.gmail.com</li>
                            <li>Porta: 587 (TLS)</li>
                            <li>Use App Password se 2FA ativo</li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-semibold">Outlook/Hotmail:</h4>
                        <ul class="ml-2 space-y-1 list-disc list-inside">
                            <li>Host: smtp-mail.outlook.com</li>
                            <li>Porta: 587 (TLS)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Email Templates -->
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Templates Disponíveis</h3>

                <div class="space-y-2">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Facturas</span>
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                            Ativo
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Cotações</span>
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                            Ativo
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Lembretes</span>
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-800 bg-yellow-100 rounded-full">
                            Pendente
                        </span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.settings.email.preview') }}"
                       class="text-sm font-medium text-blue-600 hover:text-blue-800">
                        Ver todos os templates →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Connection Modal -->
<div id="testConnectionModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-6 pt-6 pb-4 bg-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        Testar Conexão SMTP
                    </h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeTestModal()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="testEmail" class="block mb-2 text-sm font-medium text-gray-700">
                            Email para Teste
                        </label>
                        <input type="email"
                               id="testEmail"
                               placeholder="seu@email.com"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div id="testResult" class="hidden">
                        <!-- Resultado do teste aparecerá aqui -->
                    </div>
                </div>
            </div>

            <div class="flex justify-between px-6 py-3 bg-gray-50">
                <button type="button"
                        class="inline-flex justify-center px-4 py-2 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm"
                        onclick="closeTestModal()">
                    Fechar
                </button>
                <button type="button"
                        id="runTestBtn"
                        class="inline-flex justify-center px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm"
                        onclick="runConnectionTest()">
                    Executar Teste
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('emailSettingsForm');
    const saveButton = document.getElementById('saveButton');
    const testConnectionBtn = document.getElementById('testConnectionBtn');
    const modal = document.getElementById('testConnectionModal');

    // Auto-sync port with encryption
    document.getElementById('smtp_encryption').addEventListener('change', function() {
        const portSelect = document.getElementById('smtp_port');
        const encryption = this.value;

        if (encryption === 'tls') {
            portSelect.value = '587';
        } else if (encryption === 'ssl') {
            portSelect.value = '465';
        } else if (encryption === 'null') {
            portSelect.value = '25';
        }
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        const originalText = saveButton.innerHTML;
        saveButton.innerHTML = '<svg class="inline w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Salvando...';
        saveButton.disabled = true;

        // Restore button after 10 seconds if still loading
        setTimeout(() => {
            if (saveButton.disabled) {
                saveButton.innerHTML = originalText;
                saveButton.disabled = false;
            }
        }, 10000);
    });

    // Test connection button
    testConnectionBtn.addEventListener('click', function() {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    });
});

/**
 * Toggle password visibility
 */
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
    field.setAttribute('type', type);
}

/**
 * Close test modal
 */
function closeTestModal() {
    const modal = document.getElementById('testConnectionModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');

    // Reset form
    document.getElementById('testEmail').value = '';
    document.getElementById('testResult').classList.add('hidden');
}

/**
 * Run connection test
 */
function runConnectionTest() {
    const email = document.getElementById('testEmail').value.trim();
    const runTestBtn = document.getElementById('runTestBtn');
    const testResult = document.getElementById('testResult');

    if (!email) {
        showTestResult('Erro: Digite um email para teste.', 'error');
        return;
    }

    if (!validateEmail(email)) {
        showTestResult('Erro: Digite um email válido.', 'error');
        return;
    }

    // Show loading state
    const originalText = runTestBtn.innerHTML;
    runTestBtn.innerHTML = '<svg class="inline w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Testando...';
    runTestBtn.disabled = true;

    // Get current form data
    const formData = {
        smtp_host: document.getElementById('smtp_host').value,
        smtp_port: document.getElementById('smtp_port').value,
        smtp_username: document.getElementById('smtp_username').value,
        smtp_password: document.getElementById('smtp_password').value,
        smtp_encryption: document.getElementById('smtp_encryption').value,
        from_address: document.getElementById('from_address').value,
        from_name: document.getElementById('from_name').value,
        test_email: email
    };

    // Send test request
    fetch('{{ route("admin.settings.email.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showTestResult('✅ Teste realizado com sucesso! Email enviado para ' + email, 'success');
        } else {
            showTestResult('❌ Falha no teste: ' + (data.message || 'Erro desconhecido'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showTestResult('❌ Erro ao executar teste: ' + error.message, 'error');
    })
    .finally(() => {
        runTestBtn.innerHTML = originalText;
        runTestBtn.disabled = false;
    });
}

/**
 * Show test result
 */
function showTestResult(message, type) {
    const testResult = document.getElementById('testResult');
    const bgColor = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';

    testResult.innerHTML = `
        <div class="p-4 rounded-md border ${bgColor}">
            <p class="text-sm">${message}</p>
        </div>
    `;
    testResult.classList.remove('hidden');
}

/**
 * Validate email format
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Show notification (reusable function)
 */
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
    /* Loading animation */
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Animation for notifications */
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

    /* Custom focus styles */
    .focus\:ring-2:focus {
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
    }

    /* Hover transitions */
    .transition-colors {
        transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    /* Button hover effects */
    button:hover:not(:disabled),
    a:hover {
        transform: translateY(-1px);
    }

    button:disabled {
        transform: none;
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Enhanced form inputs */
    input:focus,
    select:focus,
    textarea:focus {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 0 2px rgba(59, 130, 246, 0.5);
    }

    /* Modal overlay */
    .modal-overlay {
        backdrop-filter: blur(4px);
    }

    /* Status badge styles */
    .bg-green-100 {
        background-color: rgb(220 252 231);
    }

    .text-green-800 {
        color: rgb(22 101 52);
    }

    .bg-red-100 {
        background-color: rgb(254 226 226);
    }

    .text-red-800 {
        color: rgb(153 27 27);
    }

    .bg-yellow-100 {
        background-color: rgb(254 249 195);
    }

    .text-yellow-800 {
        color: rgb(133 77 14);
    }

    /* Enhanced card styling */
    .bg-white {
        transition: all 0.2s ease-in-out;
    }

    .bg-white:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* Password field styling */
    .relative input[type="password"],
    .relative input[type="text"] {
        padding-right: 3rem;
    }

    /* Enhanced visibility for required fields */
    label:has(+ input[required])::after,
    label:has(+ select[required])::after {
        content: ' *';
        color: #ef4444;
    }

    /* Responsive improvements */
    @media (max-width: 640px) {
        .grid.grid-cols-1.md\:grid-cols-2 {
            grid-template-columns: 1fr;
        }

        .flex.sm\:flex-row {
            flex-direction: column;
        }

        .space-x-3 > * + * {
            margin-left: 0;
            margin-top: 0.75rem;
        }
    }

    /* Custom scrollbar */
    .overflow-y-auto::-webkit-scrollbar {
        width: 8px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Print styles */
    @media print {
        .no-print {
            display: none;
        }
    }

    /* Enhanced modal styling */
    .fixed.inset-0.z-50 {
        backdrop-filter: blur(8px);
        background-color: rgba(0, 0, 0, 0.6);
    }

    /* Blue theme adjustments */
    .bg-blue-50 {
        background-color: rgb(239 246 255);
    }

    .text-blue-600 {
        color: rgb(37 99 235);
    }

    .text-blue-800 {
        color: rgb(30 64 175);
    }

    .text-blue-900 {
        color: rgb(30 58 138);
    }

    .border-blue-200 {
        border-color: rgb(191 219 254);
    }

    /* Green theme adjustments */
    .bg-green-50 {
        background-color: rgb(240 253 244);
    }

    .text-green-600 {
        color: rgb(22 163 74);
    }

    /* Enhanced selection styling */
    select:focus {
        background-color: #ffffff;
    }

    /* Improved checkbox and radio styling */
    input[type="checkbox"]:checked,
    input[type="radio"]:checked {
        background-color: currentColor;
        border-color: transparent;
    }
</style>
@endpush
@endsection
