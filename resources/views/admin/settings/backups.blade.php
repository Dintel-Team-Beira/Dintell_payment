@extends('layouts.admin')

@section('title', 'Configurações de Backup')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Configurações de Backup</h1>
                    <p class="mt-1 text-sm text-gray-600">Gerencie backups automáticos e manuais do sistema</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="createBackup()"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                        </svg>
                        Criar Backup Agora
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
        <!-- Estatísticas de Backup -->
        <div class="grid grid-cols-1 gap-5 mb-8 sm:grid-cols-2 lg:grid-cols-4">
            <div class="overflow-hidden bg-white rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total de Backups</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_backups'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/>
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Último Backup</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['last_backup'] ?? 'Nunca' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Tamanho Total</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_size'] ?? '0 MB' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Status</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $stats['status'] == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $stats['status'] == 'active' ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.settings.backups.update') }}" method="POST" id="backupForm">
            @csrf
            @method('PUT')

            <!-- Configurações Automáticas -->
            <div class="mb-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Backup Automático</h3>
                            <p class="text-sm text-gray-600">Configure backups automáticos regulares</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="auto_backup_enabled" id="auto_backup_enabled" value="1"
                                   {{ old('auto_backup_enabled', $settings['auto_backup_enabled'] ?? false) ? 'checked' : '' }}
                                   class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="auto_backup_enabled" class="font-medium text-gray-700">Ativar backup automático</label>
                            <p class="text-gray-500">Criar backups automaticamente conforme a programação definida</p>
                        </div>
                    </div>

                    <div id="autoBackupSettings" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="backup_frequency" class="block text-sm font-medium text-gray-700">Frequência</label>
                                <select name="backup_frequency" id="backup_frequency"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                    <option value="daily" {{ old('backup_frequency', $settings['backup_frequency'] ?? 'daily') == 'daily' ? 'selected' : '' }}>Diário</option>
                                    <option value="weekly" {{ old('backup_frequency', $settings['backup_frequency'] ?? '') == 'weekly' ? 'selected' : '' }}>Semanal</option>
                                    <option value="monthly" {{ old('backup_frequency', $settings['backup_frequency'] ?? '') == 'monthly' ? 'selected' : '' }}>Mensal</option>
                                </select>
                                @error('backup_frequency')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="backup_time" class="block text-sm font-medium text-gray-700">Horário</label>
                                <input type="time" name="backup_time" id="backup_time"
                                       value="{{ old('backup_time', $settings['backup_time'] ?? '02:00') }}"
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                @error('backup_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="backup_retention_days" class="block text-sm font-medium text-gray-700">Manter backups por (dias)</label>
                                <input type="number" name="backup_retention_days" id="backup_retention_days" min="1" max="365"
                                       value="{{ old('backup_retention_days', $settings['backup_retention_days'] ?? 30) }}"
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                @error('backup_retention_days')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="max_backups" class="block text-sm font-medium text-gray-700">Máximo de backups</label>
                                <input type="number" name="max_backups" id="max_backups" min="1" max="100"
                                       value="{{ old('max_backups', $settings['max_backups'] ?? 10) }}"
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                @error('max_backups')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações de Conteúdo -->
            <div class="mb-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Conteúdo do Backup</h3>
                            <p class="text-sm text-gray-600">Selecione o que incluir nos backups</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="backup_database" id="backup_database" value="1"
                                       {{ old('backup_database', $settings['backup_database'] ?? true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="backup_database" class="font-medium text-gray-700">Base de Dados</label>
                                <p class="text-gray-500">Incluir todas as tabelas da base de dados</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="backup_files" id="backup_files" value="1"
                                       {{ old('backup_files', $settings['backup_files'] ?? true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="backup_files" class="font-medium text-gray-700">Arquivos</label>
                                <p class="text-gray-500">Incluir arquivos enviados pelos usuários</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="backup_config" id="backup_config" value="1"
                                       {{ old('backup_config', $settings['backup_config'] ?? true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="backup_config" class="font-medium text-gray-700">Configurações</label>
                                <p class="text-gray-500">Incluir arquivos de configuração</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="backup_logs" id="backup_logs" value="1"
                                       {{ old('backup_logs', $settings['backup_logs'] ?? false) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="backup_logs" class="font-medium text-gray-700">Logs</label>
                                <p class="text-gray-500">Incluir arquivos de log do sistema</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações de Armazenamento -->
            <div class="mb-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Armazenamento</h3>
                            <p class="text-sm text-gray-600">Configure onde armazenar os backups</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label for="backup_storage" class="block text-sm font-medium text-gray-700">Local de Armazenamento</label>
                        <select name="backup_storage" id="backup_storage"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="local" {{ old('backup_storage', $settings['backup_storage'] ?? 'local') == 'local' ? 'selected' : '' }}>Servidor Local</option>
                            <option value="ftp" {{ old('backup_storage', $settings['backup_storage'] ?? '') == 'ftp' ? 'selected' : '' }}>FTP</option>
                            <option value="google_drive" {{ old('backup_storage', $settings['backup_storage'] ?? '') == 'google_drive' ? 'selected' : '' }}>Google Drive</option>
                            <option value="dropbox" {{ old('backup_storage', $settings['backup_storage'] ?? '') == 'dropbox' ? 'selected' : '' }}>Dropbox</option>
                        </select>
                        @error('backup_storage')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="ftpSettings" class="hidden space-y-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label for="ftp_host" class="block text-sm font-medium text-gray-700">Servidor FTP</label>
                                <input type="text" name="ftp_host" id="ftp_host"
                                       value="{{ old('ftp_host', $settings['ftp_host'] ?? '') }}"
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                       placeholder="ftp.exemplo.com">
                            </div>
                            <div>
                                <label for="ftp_port" class="block text-sm font-medium text-gray-700">Porta</label>
                                <input type="number" name="ftp_port" id="ftp_port"
                                       value="{{ old('ftp_port', $settings['ftp_port'] ?? 21) }}"
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label for="ftp_username" class="block text-sm font-medium text-gray-700">Usuário</label>
                                <input type="text" name="ftp_username" id="ftp_username"
                                       value="{{ old('ftp_username', $settings['ftp_username'] ?? '') }}"
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label for="ftp_password" class="block text-sm font-medium text-gray-700">Senha</label>
                                <input type="password" name="ftp_password" id="ftp_password"
                                       value="{{ old('ftp_password', $settings['ftp_password'] ?? '') }}"
                                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>
                        <div>
                            <label for="ftp_path" class="block text-sm font-medium text-gray-700">Caminho</label>
                            <input type="text" name="ftp_path" id="ftp_path"
                                   value="{{ old('ftp_path', $settings['ftp_path'] ?? '/backups') }}"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="/backups">
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="compress_backups" id="compress_backups" value="1"
                                   {{ old('compress_backups', $settings['compress_backups'] ?? true) ? 'checked' : '' }}
                                   class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="compress_backups" class="font-medium text-gray-700">Compactar backups</label>
                            <p class="text-gray-500">Reduzir o tamanho dos arquivos de backup usando compressão</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notificações -->
            <div class="mb-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.086 2.086a1 1 0 001.414 0L13.657 4.757a1 1 0 000-1.414L11.571 1.257a1 1 0 00-1.414 0L4.828 6.586A2 2 0 004 8.172V16a2 2 0 002 2h6.586a1 1 0 00.707-.293l.707-.707M13 13h3a2 2 0 012 2v4a2 2 0 01-2 2h-3a2 2 0 01-2-2v-4a2 2 0 012-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Notificações</h3>
                            <p class="text-sm text-gray-600">Configure notificações sobre backups</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="notify_success" id="notify_success" value="1"
                                       {{ old('notify_success', $settings['notify_success'] ?? true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="notify_success" class="font-medium text-gray-700">Backup Bem-sucedido</label>
                                <p class="text-gray-500">Notificar quando backup é criado com sucesso</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="notify_failure" id="notify_failure" value="1"
                                       {{ old('notify_failure', $settings['notify_failure'] ?? true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="notify_failure" class="font-medium text-gray-700">Falha no Backup</label>
                                <p class="text-gray-500">Notificar quando backup falha</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="notification_emails" class="block text-sm font-medium text-gray-700">Emails para Notificação</label>
                        <textarea name="notification_emails" id="notification_emails" rows="3"
                                  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                                  placeholder="admin@empresa.com&#10;backup@empresa.com">{{ old('notification_emails', $settings['notification_emails'] ?? '') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Um email por linha</p>
                        @error('notification_emails')
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
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Salvar Configurações
                </button>
            </div>
        </form>

        <!-- Lista de Backups Existentes -->
        <div class="mt-8 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-indigo-100 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Backups Existentes</h3>
                            <p class="text-sm text-gray-600">Lista dos backups disponíveis</p>
                        </div>
                    </div>
                    <button onclick="refreshBackupList()"
                            class="inline-flex items-center px-3 py-1 text-xs font-medium text-indigo-700 border border-indigo-200 rounded-md bg-indigo-50 hover:bg-indigo-100">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Atualizar
                    </button>
                </div>
            </div>
            <div class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nome</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Data</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tamanho</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tipo</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="backupTableBody">
                            @forelse($backups ?? [] as $backup)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                    {{ $backup['name'] }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                    {{ $backup['date'] }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                    {{ $backup['size'] }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $backup['type'] == 'automatic' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $backup['type'] == 'automatic' ? 'Automático' : 'Manual' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $backup['status'] == 'completed' ? 'bg-green-100 text-green-800' : ($backup['status'] == 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $backup['status'] == 'completed' ? 'Completo' : ($backup['status'] == 'failed' ? 'Falhou' : 'Processando') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 space-x-2 text-sm font-medium text-right whitespace-nowrap">
                                    @if($backup['status'] == 'completed')
                                        <button onclick="downloadBackup('{{ $backup['id'] }}')"
                                                class="text-blue-600 hover:text-blue-900">
                                            Download
                                        </button>
                                        <button onclick="restoreBackup('{{ $backup['id'] }}')"
                                                class="text-green-600 hover:text-green-900">
                                            Restaurar
                                        </button>
                                    @endif
                                    <button onclick="deleteBackup('{{ $backup['id'] }}')"
                                            class="text-red-600 hover:text-red-900">
                                        Excluir
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                                    <div class="flex flex-col items-center py-12">
                                        <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-900">Nenhum backup encontrado</p>
                                        <p class="text-sm text-gray-500">Crie seu primeiro backup clicando no botão acima</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function resetForm() {
    if (confirm('Tem certeza que deseja descartar as alterações?')) {
        document.getElementById('backupForm').reset();
        toggleAutoBackupSettings();
        toggleStorageSettings();
    }
}

function toggleAutoBackupSettings() {
    const enabled = document.getElementById('auto_backup_enabled').checked;
    const settings = document.getElementById('autoBackupSettings');

    if (enabled) {
        settings.style.display = 'block';
    } else {
        settings.style.display = 'none';
    }
}

function toggleStorageSettings() {
    const storage = document.getElementById('backup_storage').value;
    const ftpSettings = document.getElementById('ftpSettings');

    if (storage === 'ftp') {
        ftpSettings.classList.remove('hidden');
    } else {
        ftpSettings.classList.add('hidden');
    }
}

function createBackup() {
    if (confirm('Tem certeza que deseja criar um backup agora? Este processo pode levar alguns minutos.')) {
        const button = event.target;
        const originalText = button.innerHTML;

        button.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Criando Backup...';
        button.disabled = true;

        fetch('{{ route("admin.settings.backups.create") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Backup criado com sucesso!', 'success');
                refreshBackupList();
            } else {
                showNotification('Erro ao criar backup: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao criar backup', 'error');
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}

function downloadBackup(backupId) {
    window.location.href = `{{ route("admin.settings.backups.download", ":id") }}`.replace(':id', backupId);
}

function restoreBackup(backupId) {
    if (confirm('ATENÇÃO: Restaurar um backup irá sobrescrever todos os dados atuais. Esta ação não pode ser desfeita. Tem certeza que deseja continuar?')) {
        fetch(`{{ route("admin.settings.backups.restore", ":id") }}`.replace(':id', backupId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Backup restaurado com sucesso!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showNotification('Erro ao restaurar backup: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao restaurar backup', 'error');
        });
    }
}

function deleteBackup(backupId) {
    if (confirm('Tem certeza que deseja excluir este backup? Esta ação não pode ser desfeita.')) {
        fetch(`{{ route("admin.settings.backups.delete", ":id") }}`.replace(':id', backupId), {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Backup excluído com sucesso!', 'success');
                refreshBackupList();
            } else {
                showNotification('Erro ao excluir backup: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao excluir backup', 'error');
        });
    }
}

function refreshBackupList() {
    fetch('{{ route("admin.settings.backups.list") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateBackupTable(data.backups);
            } else {
                showNotification('Erro ao carregar lista de backups', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao carregar lista de backups', 'error');
        });
}

function updateBackupTable(backups) {
    const tbody = document.getElementById('backupTableBody');

    if (backups.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                    <div class="flex flex-col items-center py-12">
                        <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                        </svg>
                        <p class="text-lg font-medium text-gray-900">Nenhum backup encontrado</p>
                        <p class="text-sm text-gray-500">Crie seu primeiro backup clicando no botão acima</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = backups.map(backup => `
        <tr>
            <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                ${backup.name}
            </td>
            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                ${backup.date}
            </td>
            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                ${backup.size}
            </td>
            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${backup.type === 'automatic' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}">
                    ${backup.type === 'automatic' ? 'Automático' : 'Manual'}
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${backup.status === 'completed' ? 'bg-green-100 text-green-800' : (backup.status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')}">
                    ${backup.status === 'completed' ? 'Completo' : (backup.status === 'failed' ? 'Falhou' : 'Processando')}
                </span>
            </td>
            <td class="px-6 py-4 space-x-2 text-sm font-medium text-right whitespace-nowrap">
                ${backup.status === 'completed' ? `
                    <button onclick="downloadBackup('${backup.id}')" class="text-blue-600 hover:text-blue-900">Download</button>
                    <button onclick="restoreBackup('${backup.id}')" class="text-green-600 hover:text-green-900">Restaurar</button>
                ` : ''}
                <button onclick="deleteBackup('${backup.id}')" class="text-red-600 hover:text-red-900">Excluir</button>
            </td>
        </tr>
    `).join('');
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
document.getElementById('auto_backup_enabled').addEventListener('change', toggleAutoBackupSettings);
document.getElementById('backup_storage').addEventListener('change', toggleStorageSettings);

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    toggleAutoBackupSettings();
    toggleStorageSettings();
});
</script>
@endpush
@endsection
