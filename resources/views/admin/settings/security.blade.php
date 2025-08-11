 @extends('layouts.admin')

@section('title', 'Configurações de Segurança')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Configurações de Segurança</h1>
                    <p class="mt-1 text-sm text-gray-600">Gerencie políticas de segurança e autenticação</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="generateSecurityReport()"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-700 border border-indigo-200 rounded-md bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Relatório de Segurança
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
        <form action="{{ route('admin.settings.security.update') }}" method="POST" id="securityForm">
            @csrf
            @method('PUT')

            <!-- Políticas de Senha -->
            <div class="mb-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-indigo-100 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Políticas de Senha</h3>
                            <p class="text-sm text-gray-600">Configure os requisitos para senhas de usuário</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="min_password_length" class="block text-sm font-medium text-gray-700">Comprimento Mínimo</label>
                            <input type="number" name="min_password_length" id="min_password_length" min="4" max="50"
                                   value="{{ old('min_password_length', $settings['min_password_length'] ?? 8) }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('min_password_length')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_expires_days" class="block text-sm font-medium text-gray-700">Expiração (dias)</label>
                            <input type="number" name="password_expires_days" id="password_expires_days" min="0" max="365"
                                   value="{{ old('password_expires_days', $settings['password_expires_days'] ?? 0) }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">0 = nunca expira</p>
                            @error('password_expires_days')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="require_uppercase" id="require_uppercase" value="1"
                                       {{ old('require_uppercase', $settings['require_uppercase'] ?? true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="require_uppercase" class="font-medium text-gray-700">Letras Maiúsculas</label>
                                <p class="text-gray-500">Exigir pelo menos uma letra maiúscula</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="require_lowercase" id="require_lowercase" value="1"
                                       {{ old('require_lowercase', $settings['require_lowercase'] ?? true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="require_lowercase" class="font-medium text-gray-700">Letras Minúsculas</label>
                                <p class="text-gray-500">Exigir pelo menos uma letra minúscula</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="require_numbers" id="require_numbers" value="1"
                                       {{ old('require_numbers', $settings['require_numbers'] ?? true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="require_numbers" class="font-medium text-gray-700">Números</label>
                                <p class="text-gray-500">Exigir pelo menos um número</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="require_symbols" id="require_symbols" value="1"
                                       {{ old('require_symbols', $settings['require_symbols'] ?? false) ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="require_symbols" class="font-medium text-gray-700">Símbolos</label>
                                <p class="text-gray-500">Exigir pelo menos um símb

<div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="require_symbols" id="require_symbols" value="1"
                                       {{ old('require_symbols', $settings['require_symbols'] ?? false) ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="require_symbols" class="font-medium text-gray-700">Símbolos</label>
                                <p class="text-gray-500">Exigir pelo menos um símbolo especial (!@#$%^&*)</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="prevent_password_reuse" id="prevent_password_reuse" value="1"
                                       {{ old('prevent_password_reuse', $settings['prevent_password_reuse'] ?? false) ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="prevent_password_reuse" class="font-medium text-gray-700">Prevenir Reutilização</label>
                                <p class="text-gray-500">Impedir que usuários reutilizem as últimas 5 senhas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações de Sessão -->
            <div class="mb-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Gerenciamento de Sessão</h3>
                            <p class="text-sm text-gray-600">Configure timeouts e políticas de sessão</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="session_timeout" class="block text-sm font-medium text-gray-700">Timeout de Inatividade (minutos)</label>
                            <input type="number" name="session_timeout" id="session_timeout" min="5" max="1440"
                                   value="{{ old('session_timeout', $settings['session_timeout'] ?? 30) }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            @error('session_timeout')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="max_concurrent_sessions" class="block text-sm font-medium text-gray-700">Máximo de Sessões Simultâneas</label>
                            <input type="number" name="max_concurrent_sessions" id="max_concurrent_sessions" min="1" max="10"
                                   value="{{ old('max_concurrent_sessions', $settings['max_concurrent_sessions'] ?? 3) }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            @error('max_concurrent_sessions')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="remember_me_enabled" id="remember_me_enabled" value="1"
                                   {{ old('remember_me_enabled', $settings['remember_me_enabled'] ?? true) ? 'checked' : '' }}
                                   class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="remember_me_enabled" class="font-medium text-gray-700">Permitir "Lembrar de Mim"</label>
                            <p class="text-gray-500">Permitir que usuários mantenham sessões por mais tempo</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="force_logout_password_change" id="force_logout_password_change" value="1"
                                   {{ old('force_logout_password_change', $settings['force_logout_password_change'] ?? true) ? 'checked' : '' }}
                                   class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="force_logout_password_change" class="font-medium text-gray-700">Logout Forçado ao Alterar Senha</label>
                            <p class="text-gray-500">Desconectar todas as sessões quando a senha for alterada</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Autenticação de Dois Fatores -->
              <!-- Autenticação de Dois Fatores -->
            <div class="mb-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Autenticação de Dois Fatores (2FA)</h3>
                            <p class="text-sm text-gray-600">Configure autenticação adicional para maior segurança</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="two_factor_enabled" id="two_factor_enabled" value="1"
                                   {{ old('two_factor_enabled', $settings['two_factor_enabled'] ?? false) ? 'checked' : '' }}
                                   class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="two_factor_enabled" class="font-medium text-gray-700">Ativar 2FA</label>
                            <p class="text-gray-500">Permitir que usuários configurem autenticação de dois fatores</p>
                        </div>
                    </div>

                    <div id="twoFactorSettings" class="space-y-6">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="two_factor_required_admin" id="two_factor_required_admin" value="1"
                                       {{ old('two_factor_required_admin', $settings['two_factor_required_admin'] ?? false) ? 'checked' : '' }}
                                       class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="two_factor_required_admin" class="font-medium text-gray-700">Obrigatório para Administradores</label>
                                <p class="text-gray-500">Exigir 2FA para todos os administradores</p>
                            </div>
                        </div>

                        <div>
                            <label for="two_factor_methods" class="block text-sm font-medium text-gray-700">Métodos Permitidos</label>
                            <div class="mt-2 space-y-2">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="two_factor_methods[]" id="totp" value="totp"
                                               {{ in_array('totp', old('two_factor_methods', $settings['two_factor_methods'] ?? ['totp'])) ? 'checked' : '' }}
                                               class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="totp" class="font-medium text-gray-700">App Autenticador (TOTP)</label>
                                        <p class="text-gray-500">Google Authenticator, Authy, etc.</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="two_factor_methods[]" id="sms" value="sms"
                                               {{ in_array('sms', old('two_factor_methods', $settings['two_factor_methods'] ?? [])) ? 'checked' : '' }}
                                               class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="sms" class="font-medium text-gray-700">SMS</label>
                                        <p class="text-gray-500">Código via mensagem de texto</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="two_factor_methods[]" id="email" value="email"
                                               {{ in_array('email', old('two_factor_methods', $settings['two_factor_methods'] ?? [])) ? 'checked' : '' }}
                                               class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="email" class="font-medium text-gray-700">Email</label>
                                        <p class="text-gray-500">Código via email</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Proteção contra Ataques -->
            <div class="mb-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-red-100 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.864-.833-2.634 0L4.18 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Proteção contra Ataques</h3>
                            <p class="text-sm text-gray-600">Configure medidas contra tentativas maliciosas</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="max_login_attempts" class="block text-sm font-medium text-gray-700">Máximo de Tentativas de Login</label>
                            <input type="number" name="max_login_attempts" id="max_login_attempts" min="3" max="10"
                                   value="{{ old('max_login_attempts', $settings['max_login_attempts'] ?? 5) }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            @error('max_login_attempts')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="lockout_duration" class="block text-sm font-medium text-gray-700">Duração do Bloqueio (minutos)</label>
                            <input type="number" name="lockout_duration" id="lockout_duration" min="5" max="1440"
                                   value="{{ old('lockout_duration', $settings['lockout_duration'] ?? 15) }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            @error('lockout_duration')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="rate_limiting_enabled" id="rate_limiting_enabled" value="1"
                                   {{ old('rate_limiting_enabled', $settings['rate_limiting_enabled'] ?? true) ? 'checked' : '' }}
                                   class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="rate_limiting_enabled" class="font-medium text-gray-700">Limitação de Taxa</label>
                            <p class="text-gray-500">Limitar número de requisições por IP</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="ip_whitelist_enabled" id="ip_whitelist_enabled" value="1"
                                   {{ old('ip_whitelist_enabled', $settings['ip_whitelist_enabled'] ?? false) ? 'checked' : '' }}
                                   class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="ip_whitelist_enabled" class="font-medium text-gray-700">Lista Branca de IPs</label>
                            <p class="text-gray-500">Permitir acesso apenas de IPs específicos para administradores</p>
                        </div>
                    </div>

                    <div id="ipWhitelistSettings" class="hidden">
                        <label for="allowed_ips" class="block text-sm font-medium text-gray-700">IPs Permitidos</label>
                        <textarea name="allowed_ips" id="allowed_ips" rows="4"
                                  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                  placeholder="192.168.1.100&#10;10.0.0.50&#10;203.0.113.0/24">{{ old('allowed_ips', $settings['allowed_ips'] ?? '') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Um IP por linha. Suporta CIDR (ex: 192.168.1.0/24)</p>
                        @error('allowed_ips')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Log de Auditoria -->
            <div class="mb-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Log de Auditoria</h3>
                            <p class="text-sm text-gray-600">Configure o registro de atividades de segurança</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="log_successful_logins" id="log_successful_logins" value="1"
                                       {{ old('log_successful_logins', $settings['log_successful_logins'] ?? true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="log_successful_logins" class="font-medium text-gray-700">Logins Bem-sucedidos</label>
                                <p class="text-gray-500">Registrar todos os logins bem-sucedidos</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="log_failed_logins" id="log_failed_logins" value="1"
                                       {{ old('log_failed_logins', $settings['log_failed_logins'] ?? true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="log_failed_logins" class="font-medium text-gray-700">Tentativas de Login Falhadas</label>
                                <p class="text-gray-500">Registrar tentativas de login mal-sucedidas</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="log_password_changes" id="log_password_changes" value="1"
                                       {{ old('log_password_changes', $settings['log_password_changes'] ?? true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="log_password_changes" class="font-medium text-gray-700">Mudanças de Senha</label>
                                <p class="text-gray-500">Registrar alterações de senha</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="log_admin_actions" id="log_admin_actions" value="1"
                                       {{ old('log_admin_actions', $settings['log_admin_actions'] ?? true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="log_admin_actions" class="font-medium text-gray-700">Ações Administrativas</label>
                                <p class="text-gray-500">Registrar todas as ações dos administradores</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="log_data_exports" id="log_data_exports" value="1"
                                       {{ old('log_data_exports', $settings['log_data_exports'] ?? true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="log_data_exports" class="font-medium text-gray-700">Exportação de Dados</label>
                                <p class="text-gray-500">Registrar exportações e downloads de dados</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="log_permission_changes" id="log_permission_changes" value="1"
                                       {{ old('log_permission_changes', $settings['log_permission_changes'] ?? true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="log_permission_changes" class="font-medium text-gray-700">Mudanças de Permissão</label>
                                <p class="text-gray-500">Registrar alterações de perfis e permissões</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="audit_log_retention" class="block text-sm font-medium text-gray-700">Retenção dos Logs (dias)</label>
                        <input type="number" name="audit_log_retention" id="audit_log_retention" min="30" max="2555"
                               value="{{ old('audit_log_retention', $settings['audit_log_retention'] ?? 90) }}"
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        <p class="mt-1 text-sm text-gray-500">Logs mais antigos serão automaticamente removidos</p>
                        @error('audit_log_retention')
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
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
        document.getElementById('securityForm').reset();
        toggleTwoFactorSettings();
        toggleIpWhitelistSettings();
    }
}

function toggleTwoFactorSettings() {
    const enabled = document.getElementById('two_factor_enabled').checked;
    const settings = document.getElementById('twoFactorSettings');

    if (enabled) {
        settings.style.display = 'block';
    } else {
        settings.style.display = 'none';
    }
}

function toggleIpWhitelistSettings() {
    const enabled = document.getElementById('ip_whitelist_enabled').checked;
    const settings = document.getElementById('ipWhitelistSettings');

    if (enabled) {
        settings.classList.remove('hidden');
    } else {
        settings.classList.add('hidden');
    }
}

function generateSecurityReport() {
    const button = event.target;
    const originalText = button.innerHTML;

    button.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Gerando Relatório...';
    button.disabled = true;

    fetch('{{ route("admin.settings.security.report") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        }
        throw new Error('Erro ao gerar relatório');
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'relatorio-seguranca-' + new Date().toISOString().slice(0, 10) + '.pdf';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        showNotification('Relatório de segurança gerado com sucesso!', 'success');
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao gerar relatório de segurança', 'error');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Sistema de notificações
function showNotification(message, type = 'info') {
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300`;

    const colors = {
        success: 'bg-green-50 border border-green-200 text-green-800',
        error: 'bg-red-50 border border-red-200 text-red-800',
        info: 'bg-blue-50 border border-blue-200 text-blue-800',
        warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800'
    };

    notification.className += ` ${colors[type]}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-1">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-3">
                <button class="text-gray-400 hover:text-gray-600" onclick="this.closest('.notification').remove()">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 5000);
}

// Event listeners
document.getElementById('two_factor_enabled').addEventListener('change', toggleTwoFactorSettings);
document.getElementById('ip_whitelist_enabled').addEventListener('change', toggleIpWhitelistSettings);

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    toggleTwoFactorSettings();
    toggleIpWhitelistSettings();
});
</script>
@endpush
@endsection
