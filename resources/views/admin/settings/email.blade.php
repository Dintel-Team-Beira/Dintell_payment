@extends('layouts.admin')

@section('title', 'Configurações de Email')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Configurações de Email</h1>
                    <p class="mt-1 text-sm text-gray-600">Configure o servidor de email e templates de notificação</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="testEmailConnection()"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Testar Conexão
                    </button>
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
        <form action="{{ route('admin.settings.email.update') }}" method="POST" id="emailForm">
            @csrf
            @method('PUT')

            <!-- Configurações SMTP -->
            <div class="mb-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8l6 6 6-6"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Configurações SMTP</h3>
                            <p class="text-sm text-gray-600">Configure o servidor de email para envio de notificações</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Driver de Email -->
                    <div>
                        <label for="mail_driver" class="block text-sm font-medium text-gray-700">Driver de Email</label>
                        <select name="mail_driver" id="mail_driver"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                            <option value="smtp" {{ old('mail_driver', $settings['mail_driver'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                            <option value="sendmail" {{ old('mail_driver', $settings['mail_driver'] ?? '') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                            <option value="mailgun" {{ old('mail_driver', $settings['mail_driver'] ?? '') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                            <option value="ses" {{ old('mail_driver', $settings['mail_driver'] ?? '') == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                            <option value="log" {{ old('mail_driver', $settings['mail_driver'] ?? '') == 'log' ? 'selected' : '' }}>Log (Desenvolvimento)</option>
                        </select>
                        @error('mail_driver')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="smtpSettings" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="mail_host" class="block text-sm font-medium text-gray-700">Servidor SMTP</label>
                                <input type="text" name="mail_host" id="mail_host"
                                       value="{{ old('mail_host', $settings['mail_host'] ?? '') }}"
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                                       placeholder="smtp.gmail.com">
                                @error('mail_host')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="mail_port" class="block text-sm font-medium text-gray-700">Porta</label>
                                <input type="number" name="mail_port" id="mail_port" min="1" max="65535"
                                       value="{{ old('mail_port', $settings['mail_port'] ?? 587) }}"
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                                @error('mail_port')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="mail_username" class="block text-sm font-medium text-gray-700">Nome de Usuário</label>
                                <input type="text" name="mail_username" id="mail_username"
                                       value="{{ old('mail_username', $settings['mail_username'] ?? '') }}"
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                                       placeholder="seu-email@gmail.com">
                                @error('mail_username')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="mail_password" class="block text-sm font-medium text-gray-700">Senha</label>
                                <div class="relative">
                                    <input type="password" name="mail_password" id="mail_password"
                                           value="{{ old('mail_password', $settings['mail_password'] ?? '') }}"
                                           class="block w-full pr-10 mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                                           placeholder="Senha do email">
                                    <button type="button" onclick="togglePassword('mail_password')"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 mt-1">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                @error('mail_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="mail_encryption" class="block text-sm font-medium text-gray-700">Encriptação</label>
                                <select name="mail_encryption" id="mail_encryption"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                                    <option value="" {{ old('mail_encryption', $settings['mail_encryption'] ?? '') == '' ? 'selected' : '' }}>Nenhuma</option>
                                    <option value="tls" {{ old('mail_encryption', $settings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ old('mail_encryption', $settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                </select>
                                @error('mail_encryption')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="mail_timeout" class="block text-sm font-medium text-gray-700">Timeout (segundos)</label>
                                <input type="number" name="mail_timeout" id="mail_timeout" min="5" max="300"
                                       value="{{ old('mail_timeout', $settings['mail_timeout'] ?? 30) }}"
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                                @error('mail_timeout')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações do Remetente -->
            <div class="mb-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Remetente Padrão</h3>
                            <p class="text-sm text-gray-600">Informações do remetente que aparecerão nos emails</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="mail_from_name" class="block text-sm font-medium text-gray-700">Nome do Remetente</label>
                            <input type="text" name="mail_from_name" id="mail_from_name"
                                   value="{{ old('mail_from_name', $settings['mail_from_name'] ?? config('app.name')) }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="SFS - Sistema de Faturação">
                            @error('mail_from_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mail_from_address" class="block text-sm font-medium text-gray-700">Email do Remetente</label>
                            <input type="email" name="mail_from_address" id="mail_from_address"
                                   value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="noreply@suaempresa.com">
                            @error('mail_from_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="mail_reply_to_name" class="block text-sm font-medium text-gray-700">Nome para Resposta</label>
                            <input type="text" name="mail_reply_to_name" id="mail_reply_to_name"
                                   value="{{ old('mail_reply_to_name', $settings['mail_reply_to_name'] ?? '') }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Suporte SFS">
                            @error('mail_reply_to_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mail_reply_to_address" class="block text-sm font-medium text-gray-700">Email para Resposta</label>
                            <input type="email" name="mail_reply_to_address" id="mail_reply_to_address"
                                   value="{{ old('mail_reply_to_address', $settings['mail_reply_to_address'] ?? '') }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="suporte@suaempresa.com">
                            @error('mail_reply_to_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações de Notificação -->
            <div class="mb-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM15 17H9a2 2 0 01-2-2V5a2 2 0 012-2h6a2 2 0 012 2v10z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Notificações por Email</h3>
                            <p class="text-sm text-gray-600">Configure quais eventos devem gerar notificações por email</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="notifications[]" id="new_invoice" value="new_invoice"
                                       {{ in_array('new_invoice', old('notifications', $settings['notifications'] ?? ['new_invoice'])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="new_invoice" class="font-medium text-gray-700">Nova Fatura</label>
                                <p class="text-gray-500">Notificar quando uma nova fatura é criada</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="notifications[]" id="invoice_paid" value="invoice_paid"
                                       {{ in_array('invoice_paid', old('notifications', $settings['notifications'] ?? [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="invoice_paid" class="font-medium text-gray-700">Fatura Paga</label>
                                <p class="text-gray-500">Notificar quando uma fatura é marcada como paga</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="notifications[]" id="invoice_overdue" value="invoice_overdue"
                                       {{ in_array('invoice_overdue', old('notifications', $settings['notifications'] ?? [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="invoice_overdue" class="font-medium text-gray-700">Fatura Vencida</label>
                                <p class="text-gray-500">Notificar quando uma fatura está vencida</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="notifications[]" id="new_client" value="new_client"
                                       {{ in_array('new_client', old('notifications', $settings['notifications'] ?? [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="new_client" class="font-medium text-gray-700">Novo Cliente</label>
                                <p class="text-gray-500">Notificar quando um novo cliente é registrado</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="notifications[]" id="payment_received" value="payment_received"
                                       {{ in_array('payment_received', old('notifications', $settings['notifications'] ?? [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="payment_received" class="font-medium text-gray-700">Pagamento Recebido</label>
                                <p class="text-gray-500">Notificar quando um pagamento é registrado</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="notifications[]" id="low_stock" value="low_stock"
                                       {{ in_array('low_stock', old('notifications', $settings['notifications'] ?? [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="low_stock" class="font-medium text-gray-700">Estoque Baixo</label>
                                <p class="text-gray-500">Notificar quando produtos estão com estoque baixo</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Templates de Email -->
            <div class="mb-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="p-2 mr-3 bg-purple-100 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Templates de Email</h3>
                                <p class="text-sm text-gray-600">Personalize os templates de email do sistema</p>
                            </div>
                        </div>
                        <button type="button" onclick="previewTemplate()"
                                class="inline-flex items-center px-3 py-2 text-xs font-medium text-purple-700 border border-purple-200 rounded-md bg-purple-50 hover:bg-purple-100">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Preview
                        </button>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label for="email_logo" class="block text-sm font-medium text-gray-700">Logo do Email</label>
                        <input type="url" name="email_logo" id="email_logo"
                               value="{{ old('email_logo', $settings['email_logo'] ?? '') }}"
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
                               placeholder="https://exemplo.com/logo.png">
                        <p class="mt-1 text-xs text-gray-500">URL completa para o logo que aparecerá nos emails</p>
                        @error('email_logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="email_header_color" class="block text-sm font-medium text-gray-700">Cor do Cabeçalho</label>
                            <input type="color" name="email_header_color" id="email_header_color"
                                   value="{{ old('email_header_color', $settings['email_header_color'] ?? '#3B82F6') }}"
                                   class="block w-full h-10 mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            @error('email_header_color')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email_button_color" class="block text-sm font-medium text-gray-700">Cor dos Botões</label>
                            <input type="color" name="email_button_color" id="email_button_color"
                                   value="{{ old('email_button_color', $settings['email_button_color'] ?? '#10B981') }}"
                                   class="block w-full h-10 mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            @error('email_button_color')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="email_footer" class="block text-sm font-medium text-gray-700">Rodapé dos Emails</label>
                        <textarea name="email_footer" id="email_footer" rows="4"
                                  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
                                  placeholder="© 2024 Sua Empresa. Todos os direitos reservados.">{{ old('email_footer', $settings['email_footer'] ?? '') }}</textarea>
                        @error('email_footer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-yellow-600 border border-transparent rounded-md shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
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
        document.getElementById('emailForm').reset();
    }
}

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
    field.setAttribute('type', type);
}

function testEmailConnection() {
    const button = event.target;
    const originalText = button.innerHTML;

    button.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Testando...';
    button.disabled = true;

    fetch('{{ route("admin.settings.email.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            mail_host: document.getElementById('mail_host').value,
            mail_port: document.getElementById('mail_port').value,
            mail_username: document.getElementById('mail_username').value,
            mail_password: document.getElementById('mail_password').value,
            mail_encryption: document.getElementById('mail_encryption').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Conexão testada com sucesso!', 'success');
        } else {
            showNotification('Erro na conexão: ' + (data.message || 'Verifique as configurações'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao testar conexão', 'error');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// function previewTemplate() {
//     window.open('{{ route("admin.settings.email.preview") }}', '_blank', 'width=800,height=600');
// }

function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300`;

    const colors = {
        success: 'bg-green-50 border border-green-200 text-green-800',
        error: 'bg-red-50 border border-red-200 text-red-800',
        info: 'bg-blue-50 border border-blue-200 text-blue-800'
    };

    const icons = {
        success: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
        error: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
        info: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
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

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Show/hide SMTP settings based on driver
document.getElementById('mail_driver').addEventListener('change', function() {
    const smtpSettings = document.getElementById('smtpSettings');
    if (this.value === 'smtp') {
        smtpSettings.style.display = 'block';
    } else {
        smtpSettings.style.display = 'none';
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    const driver = document.getElementById('mail_driver').value;
    const smtpSettings = document.getElementById('smtpSettings');
    if (driver !== 'smtp') {
        smtpSettings.style.display = 'none';
    }
});
</script>
@endpush
@endsection
