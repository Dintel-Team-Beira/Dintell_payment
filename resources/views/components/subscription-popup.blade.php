{{-- Component: resources/views/components/subscription-popup.blade.php --}}

@if(isset($subscription_status))
    @php
        $limits = $subscription_status['limits'];
        $warnings = $subscription_status['warnings'];
        $company = $subscription_status['company'];
        $plan = $subscription_status['plan'];
    @endphp

    {{-- Modal de Bloqueio --}}
    @if($limits['blocked'])
        <div id="subscription-block-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="w-full max-w-md mx-4 bg-white rounded-lg shadow-xl">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.314 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Acesso Bloqueado</h3>
                            <p class="text-sm text-gray-600">Você atingiu os limites do seu plano</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="mb-2 font-medium text-gray-900">Motivos do bloqueio:</h4>
                        <ul class="space-y-1 text-sm text-gray-700 list-disc list-inside">
                            @foreach($limits['reasons'] as $reason)
                                <li>{{ $reason }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mb-6">
                        <h4 class="mb-3 font-medium text-gray-900">Uso atual:</h4>

                        {{-- Usuários --}}
                        <div class="mb-3">
                            <div class="flex justify-between text-sm">
                                <span>Usuários</span>
                                <span>{{ $limits['users']['current'] }} / {{ $limits['users']['max'] }}</span>
                            </div>
                            <div class="w-full h-2 mt-1 bg-gray-200 rounded-full">
                                <div class="h-2 bg-red-600 rounded-full" style="width: {{ $limits['users']['percentage'] }}%"></div>
                            </div>
                        </div>

                        {{-- Faturas --}}
                        <div class="mb-3">
                            <div class="flex justify-between text-sm">
                                <span>Faturas (este mês)</span>
                                <span>{{ $limits['invoices']['current'] }} / {{ $limits['invoices']['max'] }}</span>
                            </div>
                            <div class="w-full h-2 mt-1 bg-gray-200 rounded-full">
                                <div class="h-2 bg-red-600 rounded-full" style="width: {{ $limits['invoices']['percentage'] }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <a href="{{ route('subscription.upgrade') }}" class="flex-1 px-4 py-2 text-center text-white transition-colors bg-blue-600 rounded-md hover:bg-blue-700">
                            Fazer Upgrade
                        </a>
                        <a href="{{ route('subscription.index') }}" class="flex-1 px-4 py-2 text-center text-gray-700 transition-colors bg-gray-300 rounded-md hover:bg-gray-400">
                            Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Avisos/Warnings --}}
    @if(!empty($warnings) && !$limits['blocked'])
        @foreach($warnings as $warning)
            <div id="warning-{{ $warning['type'] }}" class="fixed top-4 right-4 z-40 max-w-sm bg-white rounded-lg shadow-lg border-l-4 {{ $warning['type'] === 'expiration' ? 'border-red-500' : 'border-yellow-500' }}">
                <div class="p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            @if($warning['type'] === 'expiration')
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 ml-3">
                            <p class="text-sm font-medium text-gray-800">
                                {{ $warning['message'] }}
                            </p>
                            @if(isset($warning['percentage']))
                                <div class="mt-2">
                                    <div class="w-full h-1 bg-gray-200 rounded-full">
                                        <div class="h-1 rounded-full {{ $warning['percentage'] >= 90 ? 'bg-red-500' : 'bg-yellow-500' }}" style="width: {{ $warning['percentage'] }}%"></div>
                                    </div>
                                </div>
                            @endif
                            <div class="flex mt-3 space-x-2">
                                @if($warning['type'] === 'expiration')
                                    <a href="{{ route('subscription.renewal') }}" class="px-2 py-1 text-xs text-red-800 bg-red-100 rounded hover:bg-red-200">
                                        Renovar Agora
                                    </a>
                                @else
                                    <a href="{{ route('subscription.upgrade') }}" class="px-2 py-1 text-xs text-blue-800 bg-blue-100 rounded hover:bg-blue-200">
                                        Fazer Upgrade
                                    </a>
                                @endif
                                <button onclick="dismissWarning('{{ $warning['type'] }}')" class="text-xs text-gray-500 hover:text-gray-700">
                                    Dispensar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    {{-- Barra de Status (sempre visível) --}}
    <div id="subscription-status-bar" class="fixed bottom-0 left-0 right-0 z-30 px-4 py-2 bg-white border-t border-gray-200 shadow-lg">
        <div class="flex items-center justify-between mx-auto max-w-7xl">
            <div class="flex items-center space-x-4 text-sm">
                <div class="flex items-center">
                    <span class="text-gray-600">Plano:</span>
                    <span class="ml-1 font-medium">{{ $plan->name ?? 'N/A' }}</span>
                    @if($company->subscription_type === 'trial')
                        <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded">TESTE</span>
                    @endif
                </div>

                <div class="flex items-center">
                    <span class="text-gray-600">Usuários:</span>
                    <span class="ml-1">{{ $limits['users']['current'] }}/{{ $limits['users']['max'] }}</span>
                    <div class="w-12 h-1 ml-2 bg-gray-200 rounded-full">
                        <div class="h-1 rounded-full {{ $limits['users']['percentage'] >= 90 ? 'bg-red-500' : ($limits['users']['percentage'] >= 80 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $limits['users']['percentage'] }}%"></div>
                    </div>
                </div>

                <div class="flex items-center">
                    <span class="text-gray-600">Faturas:</span>
                    <span class="ml-1">{{ $limits['invoices']['current'] }}/{{ $limits['invoices']['max'] }}</span>
                    <div class="w-12 h-1 ml-2 bg-gray-200 rounded-full">
                        <div class="h-1 rounded-full {{ $limits['invoices']['percentage'] >= 90 ? 'bg-red-500' : ($limits['invoices']['percentage'] >= 80 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $limits['invoices']['percentage'] }}%"></div>
                    </div>
                </div>

                @if($limits['subscription']['expires_at'])
                    <div class="flex items-center">
                        <span class="text-gray-600">Expira em:</span>
                        <span class="ml-1 {{ $limits['subscription']['days_left'] <= 7 ? 'text-red-600 font-medium' : '' }}">
                            {{ $limits['subscription']['days_left'] }} dias
                        </span>
                    </div>
                @endif
            </div>

            <div class="flex items-center space-x-2">
                <button onclick="toggleStatusBar()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
@endif

<style>
    .subscription-warning {
        animation: slideInRight 0.3s ease-out;
    }

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

    #subscription-status-bar.hidden {
        transform: translateY(100%);
        transition: transform 0.3s ease-in-out;
    }

    #subscription-status-bar:not(.hidden) {
        transform: translateY(0);
        transition: transform 0.3s ease-in-out;
    }
</style>

<script>
function dismissWarning(type) {
    const warning = document.getElementById('warning-' + type);
    if (warning) {
        warning.style.transform = 'translateX(100%)';
        warning.style.opacity = '0';
        setTimeout(() => warning.remove(), 300);

        // Salvar no localStorage para não mostrar novamente hoje
        localStorage.setItem('dismissed_warning_' + type, new Date().toDateString());
    }
}

function toggleStatusBar() {
    const statusBar = document.getElementById('subscription-status-bar');
    statusBar.classList.toggle('hidden');

    // Salvar preferência
    localStorage.setItem('status_bar_hidden', statusBar.classList.contains('hidden'));
}

// Verificar se avisos foram dispensados hoje
document.addEventListener('DOMContentLoaded', function() {
    @if(!empty($warnings))
        @foreach($warnings as $warning)
            const dismissedDate{{ $warning['type'] }} = localStorage.getItem('dismissed_warning_{{ $warning['type'] }}');
            if (dismissedDate{{ $warning['type'] }} === new Date().toDateString()) {
                const warning{{ $warning['type'] }} = document.getElementById('warning-{{ $warning['type'] }}');
                if (warning{{ $warning['type'] }}) {
                    warning{{ $warning['type'] }}.remove();
                }
            }
        @endforeach
    @endif

    // Restaurar estado da barra de status
    if (localStorage.getItem('status_bar_hidden') === 'true') {
        document.getElementById('subscription-status-bar').classList.add('hidden');
    }

    // Auto-hide warnings after 10 seconds
    setTimeout(() => {
        document.querySelectorAll('[id^="warning-"]').forEach(warning => {
            if (warning && !warning.querySelector('[id^="warning-expiration"]')) {
                dismissWarning(warning.id.replace('warning-', ''));
            }
        });
    }, 10000);
});

// Prevenir fechamento do modal de bloqueio
@if($limits['blocked'] ?? false)
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        e.preventDefault();
    }
});

document.getElementById('subscription-block-modal').addEventListener('click', function(e) {
    e.stopPropagation();
});
@endif
</script>
