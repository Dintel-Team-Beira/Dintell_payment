@extends('layouts.admin')

@section('title', 'Editar Usuário')

@section('content')
<div class="container px-6 py-8 mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Editar Usuário</h1>
                <p class="mt-2 text-gray-600">Atualize as informações de {{ $user->name }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.users.show', $user) }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Visualizar
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar à Lista
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data" id="editUserForm">
            @csrf
            @method('PUT')

            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Informações do Usuário</h3>
                        <p class="text-sm text-gray-500">Atualize os dados do usuário</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">Criado em:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Current Avatar Display -->
                <div class="flex items-center space-x-6">
                    <div class="flex-shrink-0">
                        <div class="w-20 h-20">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}"
                                     alt="{{ $user->name }}"
                                     class="object-cover w-20 h-20 rounded-full"
                                     id="current-avatar">
                            @else
                                <div class="flex items-center justify-center w-20 h-20 bg-gray-200 rounded-full" id="current-avatar">
                                    <span class="text-xl font-semibold text-gray-600">
                                        {{ substr($user->name, 0, 2) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h4 class="text-lg font-medium text-gray-900">{{ $user->name }}</h4>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        <p class="mt-1 text-xs text-gray-400">
                            Último login: {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca' }}
                        </p>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome Completo *</label>
                        <input type="text" name="name" id="name" required
                               value="{{ old('name', $user->name) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                        <input type="email" name="email" id="email" required
                               value="{{ old('email', $user->email) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-300 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Telefone</label>
                        <input type="tel" name="phone" id="phone"
                               value="{{ old('phone', $user->phone) }}"
                               placeholder="+258 XX XXX XXXX"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('phone') border-red-300 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="company_id" class="block text-sm font-medium text-gray-700">Empresa *</label>
                        <select name="company_id" id="company_id" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('company_id') border-red-300 @enderror">
                            <option value="">Selecione uma empresa</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}"
                                        {{ old('company_id', $user->company_id) == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Password Change Section -->
                <div class="pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-medium text-gray-900">Alterar Senha</h4>
                        <div class="flex items-center">
                            <input type="checkbox" id="change_password" name="change_password" value="1"
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="change_password" class="ml-2 text-sm text-gray-700">
                                Alterar senha do usuário
                            </label>
                        </div>
                    </div>

                    <div id="password_fields" class="grid hidden grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Nova Senha</label>
                            <div class="relative mt-1">
                                <input type="password" name="password" id="password"
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('password') border-red-300 @enderror">
                                <button type="button" onclick="togglePassword('password')"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Deixe em branco para manter a senha atual</p>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Nova Senha</label>
                            <div class="relative mt-1">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <button type="button" onclick="togglePassword('password_confirmation')"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role and Status -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Função</label>
                        <select name="role" id="role"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="user" {{ old('role', $user->role ?? 'user') == 'user' ? 'selected' : '' }}>Usuário</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
                            <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Gerente</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="is_active" id="is_active"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="1" {{ old('is_active', $user->is_active) == 1 ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ old('is_active', $user->is_active) == 0 ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                </div>

                <!-- Avatar Upload -->
                <div>
                    <label for="avatar" class="block text-sm font-medium text-gray-700">Atualizar Foto do Perfil</label>
                    <div class="flex items-center mt-1 space-x-4">
                        <div class="flex-shrink-0">
                            <div id="avatar-preview" class="flex items-center justify-center w-16 h-16 bg-gray-200 rounded-full">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}"
                                         class="object-cover w-16 h-16 rounded-full">
                                @else
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="avatar" id="avatar" accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF até 2MB</p>
                        </div>
                        @if($user->avatar)
                            <div class="flex items-center">
                                <label class="flex items-center">
                                    <input type="checkbox" name="remove_avatar" value="1"
                                           class="text-red-600 border-gray-300 rounded shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-red-600">Remover foto atual</span>
                                </label>
                            </div>
                        @endif
                    </div>
                    @error('avatar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Additional Options -->
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="notify_user" id="notify_user" value="1"
                                   {{ old('notify_user', true) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="notify_user" class="font-medium text-gray-700">Notificar usuário sobre alterações</label>
                            <p class="text-gray-500">O usuário receberá um email informando sobre as mudanças no perfil</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="force_password_change" id="force_password_change" value="1"
                                   {{ old('force_password_change', $user->force_password_change ?? false) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="force_password_change" class="font-medium text-gray-700">Forçar alteração de senha</label>
                            <p class="text-gray-500">O usuário será obrigado a alterar a senha no próximo login</p>
                        </div>
                    </div>
                </div>

                <!-- Audit Information -->
                <div class="pt-6 border-t border-gray-200">
                    <h4 class="mb-4 text-lg font-medium text-gray-900">Informações de Auditoria</h4>
                    <div class="p-4 rounded-lg bg-gray-50">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Criado em</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Última atualização</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Último login</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca' }}
                                </dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between px-6 py-4 border-t border-gray-200 rounded-b-lg bg-gray-50">
                <div class="flex space-x-3">
                    <a href="{{ route('admin.users.show', $user) }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancelar
                    </a>
                    <button type="button" onclick="resetForm()"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Resetar
                    </button>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" name="action" value="save"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Salvar Alterações
                    </button>
                    <button type="submit" name="action" value="save_and_continue"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Salvar e Continuar Editando
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="mt-8 border border-red-200 rounded-lg bg-red-50">
        <div class="px-6 py-4 border-b border-red-200">
            <h3 class="text-lg font-medium text-red-900">Zona de Perigo</h3>
            <p class="text-sm text-red-600">Ações irreversíveis que afetam permanentemente este usuário</p>
        </div>
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-red-900">Excluir usuário</h4>
                    <p class="text-sm text-red-600">
                        Esta ação não pode ser desfeita. Todos os dados do usuário serão permanentemente removidos.
                    </p>
                </div>
                <button type="button" onclick="deleteUser({{ $user->id }})"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-600 bg-transparent border border-red-600 rounded-md hover:bg-red-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Excluir Usuário
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password fields
        document.getElementById('change_password').addEventListener('change', function() {
            const passwordFields = document.getElementById('password_fields');
            const passwordInput = document.getElementById('password');
            const passwordConfirmation = document.getElementById('password_confirmation');

            if (this.checked) {
                passwordFields.classList.remove('hidden');
                passwordInput.setAttribute('required', 'required');
                passwordConfirmation.setAttribute('required', 'required');
            } else {
                passwordFields.classList.add('hidden');
                passwordInput.removeAttribute('required');
                passwordConfirmation.removeAttribute('required');
                passwordInput.value = '';
                passwordConfirmation.value = '';
            }
        });

        // Preview avatar
        document.getElementById('avatar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatar-preview');
                    preview.innerHTML = `<img src="${e.target.result}" class="object-cover w-16 h-16 rounded-full">`;
                };
                reader.readAsDataURL(file);
            }
        });

        // Phone mask
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 12) {
                value = value.replace(/(\d{3})(\d{2})(\d{3})(\d{4})/, '+$1 $2 $3 $4');
                e.target.value = value;
            }
        });
    });

    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
        field.setAttribute('type', type);
    }

    // Reset form to original values
    function resetForm() {
        if (confirm('Tem certeza que deseja resetar o formulário? Todas as alterações não salvas serão perdidas.')) {
            document.getElementById('editUserForm').reset();
            // Reset avatar preview
            const avatarPreview = document.getElementById('avatar-preview');
            @if($user->avatar)
                avatarPreview.innerHTML = `<img src="{{ asset('storage/' . $user->avatar) }}" class="object-cover w-16 h-16 rounded-full">`;
            @else
                avatarPreview.innerHTML = `<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>`;
            @endif
        }
    }

    // Delete user
    function deleteUser(userId) {
        if (confirm('ATENÇÃO: Esta ação não pode ser desfeita!\n\nTem certeza que deseja excluir permanentemente este usuário?\n\nTodos os dados relacionados (faturas, cotações, etc.) também serão removidos.')) {
            const secondConfirm = confirm('Digite "CONFIRMAR" para prosseguir com a exclusão:');
            if (secondConfirm) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/users/${userId}`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }
    }

    // Form validation
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        const changePassword = document.getElementById('change_password').checked;

        if (changePassword) {
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;

            if (password !== passwordConfirmation) {
                e.preventDefault();
                alert('As senhas não coincidem!');
                return false;
            }

            if (password.length > 0 && password.length < 8) {
                e.preventDefault();
                alert('A senha deve ter pelo menos 8 caracteres!');
                return false;
            }
        }

        // Show loading state
        const submitButtons = this.querySelectorAll('button[type="submit"]');
        submitButtons.forEach(button => {
            const originalText = button.innerHTML;
            button.innerHTML = `
                <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Salvando...
            `;
            button.disabled = true;

            // Reset after 10 seconds if still processing
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            }, 10000);
        });
    });
</script>
@endpush

@push('styles')
<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    .file\:mr-4::file-selector-button { margin-right: 1rem; }
    .file\:py-2::file-selector-button { padding-top: 0.5rem; padding-bottom: 0.5rem; }
    .file\:px-4::file-selector-button { padding-left: 1rem; padding-right: 1rem; }
    .file\:rounded-full::file-selector-button { border-radius: 9999px; }
    .file\:border-0::file-selector-button { border-width: 0; }
    .file\:text-sm::file-selector-button { font-size: 0.875rem; line-height: 1.25rem; }
    .file\:font-semibold::file-selector-button { font-weight: 600; }
    .file\:bg-blue-50::file-selector-button { background-color: rgb(239 246 255); }
    .file\:text-blue-700::file-selector-button { color: rgb(29 78 216); }
    .hover\:file\:bg-blue-100::file-selector-button:hover { background-color: rgb(219 234 254); }
</style>
@endpush
@endsection
