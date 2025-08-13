{{-- resources/views/admin/settings/email/preview.blade.php --}}
@extends('layouts.admin')

@section('title', 'Preview de Configurações de Email')

@section('content')
<div class="container px-4 py-6 mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Preview de Email</h1>
                <p class="mt-2 text-gray-600">Visualize como seus emails aparecerão para os destinatários</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.settings.email.index') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar às Configurações
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Sidebar de Controles -->
        <div class="lg:col-span-1">
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Controles de Preview</h3>

                <!-- Seleção de Template -->
                <div class="mb-6">
                    <label for="templateSelect" class="block mb-2 text-sm font-medium text-gray-700">
                        Template de Email
                    </label>
                    <select id="templateSelect"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($templates as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Teste de Email -->
                <div class="mb-6">
                    <label for="testEmail" class="block mb-2 text-sm font-medium text-gray-700">
                        Email para Teste
                    </label>
                    <input type="email"
                           id="testEmail"
                           placeholder="seu@email.com"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button id="sendTestBtn"
                            class="w-full px-4 py-2 mt-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Enviar Teste
                    </button>
                </div>

                <!-- Configurações Atuais -->
                <div class="pt-6 border-t border-gray-200">
                    <h4 class="mb-3 text-sm font-semibold text-gray-900">Configurações Atuais</h4>
                    <div class="space-y-2 text-sm">
                        <div>
                            <span class="text-gray-500">SMTP Host:</span>
                            <span class="font-medium text-gray-900">{{ $emailSettings['smtp_host'] ?: 'Não configurado' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Porta:</span>
                            <span class="font-medium text-gray-900">{{ $emailSettings['smtp_port'] ?: 'Não configurada' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">De:</span>
                            <span class="font-medium text-gray-900">{{ $emailSettings['from_address'] ?: 'Não configurado' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Nome:</span>
                            <span class="font-medium text-gray-900">{{ $emailSettings['from_name'] ?: 'Não configurado' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Area -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <!-- Header do Preview -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Preview do Email</h3>
                        <div class="flex items-center space-x-2">
                            <button id="refreshPreview"
                                    class="p-2 text-gray-500 hover:text-gray-700 focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                            <span class="text-sm text-gray-500" id="previewStatus">Carregando...</span>
                        </div>
                    </div>
                </div>

                <!-- Iframe do Preview -->
                <div class="p-6">
                    <div class="overflow-hidden border border-gray-300 rounded-lg" style="height: 600px;">
                        <iframe id="emailPreview"
                                src="about:blank"
                                class="w-full h-full"
                                sandbox="allow-same-origin">
                        </iframe>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="previewLoading" class="hidden p-6">
                    <div class="flex items-center justify-center py-12">
                        <div class="w-8 h-8 border-b-2 border-blue-600 rounded-full animate-spin"></div>
                        <span class="ml-3 text-gray-600">Carregando preview...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const templateSelect = document.getElementById('templateSelect');
    const testEmailInput = document.getElementById('testEmail');
    const sendTestBtn = document.getElementById('sendTestBtn');
    const refreshBtn = document.getElementById('refreshPreview');
    const previewIframe = document.getElementById('emailPreview');
    const previewStatus = document.getElementById('previewStatus');
    const previewLoading = document.getElementById('previewLoading');

    // Carregar preview inicial
    loadPreview();

    // Event listeners
    templateSelect.addEventListener('change', loadPreview);
    refreshBtn.addEventListener('click', loadPreview);
    sendTestBtn.addEventListener('click', sendTestEmail);

    /**
     * Carregar preview do email
     */
    function loadPreview() {
        const template = templateSelect.value;

        showLoading(true);
        previewStatus.textContent = 'Carregando...';

        fetch(`{{ route('admin.settings.email.preview') }}?ajax=1`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                template: template
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Criar um blob com o HTML e definir como src do iframe
                const blob = new Blob([data.html], { type: 'text/html' });
                const url = URL.createObjectURL(blob);
                previewIframe.src = url;

                previewStatus.textContent = 'Preview carregado';

                // Limpar URL após carregar
                previewIframe.onload = () => {
                    URL.revokeObjectURL(url);
                };
            } else {
                previewStatus.textContent = 'Erro ao carregar';
                showNotification('Erro ao carregar preview', 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            previewStatus.textContent = 'Erro ao carregar';
            showNotification('Erro ao carregar preview', 'error');
        })
        .finally(() => {
            showLoading(false);
        });
    }

    /**
     * Enviar email de teste
     */
    function sendTestEmail() {
        const email = testEmailInput.value.trim();
        const template = templateSelect.value;

        if (!email) {
            showNotification('Digite um email para teste', 'warning');
            return;
        }

        if (!validateEmail(email)) {
            showNotification('Digite um email válido', 'warning');
            return;
        }

        sendTestBtn.disabled = true;
        const originalText = sendTestBtn.innerHTML;
        sendTestBtn.innerHTML = '<svg class="inline w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Enviando...';

        fetch(`{{ route('admin.settings.email.test') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                test_email: email,
                template: template
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                testEmailInput.value = '';
            } else {
                showNotification(data.message || 'Erro ao enviar email', 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showNotification('Erro ao enviar email', 'error');
        })
        .finally(() => {
            sendTestBtn.disabled = false;
            sendTestBtn.innerHTML = originalText;
        });
    }

    /**
     * Mostrar/ocultar loading
     */
    function showLoading(show) {
        if (show) {
            previewLoading.classList.remove('hidden');
            previewIframe.parentElement.classList.add('hidden');
        } else {
            previewLoading.classList.add('hidden');
            previewIframe.parentElement.classList.remove('hidden');
        }
    }

    /**
     * Validar email
     */
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    /**
     * Sistema de notificações
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
});
</script>
@endpush

@push('styles')
<style>
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

    /* Iframe styling */
    iframe {
        border: none;
        background: #f9fafb;
    }

    /* Button hover effects */
    button:hover:not(:disabled) {
        transform: translateY(-1px);
    }

    button:disabled {
        transform: none;
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
@endpush
@endsection
