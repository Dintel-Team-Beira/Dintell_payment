{{-- resources/views/components/subscription-popup.blade.php --}}
{{-- ESTE POPUP AGORA TRABALHA EM CONJUNTO COM A MIDDLEWARE --}}
{{-- A middleware bloqueia casos críticos, este popup mostra AVISOS --}}

@props([
    'company' => null,
    'plan' => null,
])

@php
    // Pegar avisos da sessão (colocados pela middleware)
    $trialExpiring = session('trial_expiring');
    $usageWarnings = session('usage_warnings', []);
    $generalWarning = session('warning');

    // Decidir se deve mostrar o popup
    $shouldShow = false;
    $warnings = [];

    // Trial expirando
    if ($trialExpiring) {
        $shouldShow = true;
        $warnings[] = [
            'type' => 'trial',
            'title' => 'Período de Teste Expirando',
            'message' => $trialExpiring['message'],
            'priority' => 'high',
        ];
    }

    // Avisos de uso
    if (!empty($usageWarnings)) {
        $shouldShow = true;
        foreach ($usageWarnings as $warning) {
            $warnings[] = [
                'type' => $warning['type'],
                'title' => ucfirst($warning['type']),
                'message' => $warning['message'],
                'priority' => $warning['priority'] ?? 'medium',
            ];
        }
    }

    // Aviso geral
    if ($generalWarning) {
        $shouldShow = true;
        $warnings[] = [
            'type' => 'general',
            'title' => 'Aviso',
            'message' => $generalWarning,
            'priority' => 'medium',
        ];
    }

    // Se não há avisos, não mostra
    if (!$shouldShow || empty($warnings)) {
        return;
    }

    // Pegar limites para mostrar barras de progresso
    $limits = [];
    if ($company && $company->plan) {
        // Limite de usuários
        // $userUsage = $company->getUserUsage();
        $userUsage = $company->getUserUsageFeatured();
        if ($userUsage['max'] > 0) {
            $limits[] = [
                'name' => 'Usuários',
                'used' => $userUsage['current'],
                'total' => $userUsage['max'],
                'percentage' => $userUsage['percentage'],
            ];
        }

        // Limite de faturas
        $invoiceUsage = $company->getInvoiceUsage();
        // $invoiceUsage = $company->getInvoiceUsageFeatured();
        if ($invoiceUsage['max'] > 0) {
            $limits[] = [
                'name' => 'Faturas este mês',
                'used' => $invoiceUsage['current'],
                'total' => $invoiceUsage['max'],
                'percentage' => $invoiceUsage['percentage'],
            ];
        }
        // Limites de clientes
        $clientUsage = $company->getClientUsage();
        if($clientUsage['max']>0)
        {
            $limits[] = [
                'name' => 'Clientes',
                'used' => $clientUsage['current'],
                'total' => $clientUsage['max'],
                'percentage' => $clientUsage['percentage'],
            ];
        }
    }

    // Cores do aviso (amarelo para warnings)
    $headerClass = 'bg-yellow-50';
    $iconClass = 'bg-yellow-100 text-yellow-600';
    $textColorClass = 'text-yellow-900';
    $subtextClass = 'text-yellow-600';

    // Controlar tempo
    $throttleKey = 'sub_popup_closed_at';
    $throttleMinutes = 3; // Tempo de espera em minutos
@endphp
@if (!$shouldShow || empty($warnings))
    @return
@endif
{{-- Popup de Avisos (não bloqueia, apenas informa) --}}
<div id="subscriptionPopup" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true"
    x-data="{
        show: true, // 2. Controla o tempo
        canShow() {
            const lastClosed = localStorage.getItem('{{ $throttleKey }}');
            if (!lastClosed) {
                return true; // Nunca fechou, pode mostrar
            }
            const minutesAgo = (Date.now() - lastClosed) / (1000 * 60);
            return minutesAgo >= {{ $throttleMinutes }}; // Mostra se passou o tempo
        },
        // 3. Função para fechar e salvar o tempo
        closeAndThrottle() {
            localStorage.setItem('{{ $throttleKey }}', Date.now());
            this.show = false;
        }
    }" x-init="show = canShow()" x-show="show" x-cloak>

    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

        {{-- Overlay (pode fechar clicando fora) --}}
        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50" {{-- @click="show = false"  --}}
            @click="closeAndThrottle()" aria-hidden="true">
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        {{-- Modal Content --}}
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
            x-show="show" x-transition>

            {{-- Header --}}
            <div class="px-6 pt-6 pb-4 {{ $headerClass }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 rounded-lg {{ $iconClass }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold {{ $textColorClass }}">
                                Avisos Importantes
                            </h3>
                            @if ($company)
                                <p class="mt-1 text-sm {{ $subtextClass }}">
                                    {{ $company->name }}
                                    @if ($plan)
                                        - {{ $plan->name }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Botão fechar --}}
                    <button type="button" {{-- @click="show = false"  --}} @click="closeAndThrottle()"
                        class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Conteúdo --}}
            <div class="px-6 py-4 space-y-4">

                {{-- Mostrar warnings --}}
                @if (!empty($warnings))
                    <div class="space-y-3">
                        @foreach ($warnings as $warning)
                            @php
                                // Cores baseadas na prioridade
                                $warningColors = match ($warning['priority']) {
                                    'urgent' => [
                                        'border' => 'border-red-200',
                                        'bg' => 'bg-red-50',
                                        'icon' => 'text-red-600',
                                        'title' => 'text-red-800',
                                        'text' => 'text-red-700',
                                    ],
                                    'high' => [
                                        'border' => 'border-orange-200',
                                        'bg' => 'bg-orange-50',
                                        'icon' => 'text-orange-600',
                                        'title' => 'text-orange-800',
                                        'text' => 'text-orange-700',
                                    ],
                                    default => [
                                        'border' => 'border-yellow-200',
                                        'bg' => 'bg-yellow-50',
                                        'icon' => 'text-yellow-600',
                                        'title' => 'text-yellow-800',
                                        'text' => 'text-yellow-700',
                                    ],
                                };
                            @endphp

                            <div
                                class="flex items-start p-3 border rounded-lg {{ $warningColors['border'] }} {{ $warningColors['bg'] }}">
                                <svg class="w-5 h-5 {{ $warningColors['icon'] }} mt-0.5 mr-3 flex-shrink-0"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <h5 class="text-sm font-medium {{ $warningColors['title'] }}">
                                        {{ $warning['title'] }}
                                    </h5>
                                    <p class="mt-1 text-sm {{ $warningColors['text'] }}">
                                        {{ $warning['message'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Mostrar limites se existirem --}}
                @if (!empty($limits))
                    <div class="pt-4 space-y-3 border-t border-gray-200">
                        <h4 class="font-semibold text-gray-900">Uso Atual:</h4>
                        @foreach ($limits as $limit)
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-700">{{ $limit['name'] }}</span>
                                    <span class="font-medium text-gray-900">
                                        {{ $limit['used'] }}/{{ $limit['total'] }}
                                    </span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    @php
                                        $percentage = $limit['percentage'];
                                        if ($percentage >= 90) {
                                            $barColor = 'bg-red-500';
                                        } elseif ($percentage >= 70) {
                                            $barColor = 'bg-yellow-500';
                                        } else {
                                            $barColor = 'bg-green-500';
                                        }
                                    @endphp
                                    <div class="{{ $barColor }} h-2 transition-all duration-500"
                                        style="width: {{ min($percentage, 100) }}%"></div>
                                </div>
                                @if ($percentage >= 90)
                                    <p class="text-xs text-red-600">⚠️ Limite quase atingido!</p>
                                @elseif($percentage >= 70)
                                    <p class="text-xs text-yellow-600">Atenção ao limite</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Dica --}}
                <div class="p-3 border border-blue-200 rounded-lg bg-blue-50">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-blue-800">
                            <strong>Dica:</strong> Faça upgrade do seu plano para evitar interrupções no serviço e ter
                            acesso a mais recursos.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Botões de Ação --}}
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between space-x-4">

                    {{-- Botão Fechar --}}
                    <button type="button" {{-- @click="show = false" --}} @click="closeAndThrottle()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Entendi
                    </button>

                    {{-- Botões de ação --}}
                    <div class="flex space-x-3">
                        @if ($trialExpiring || (!empty($warnings) && collect($warnings)->where('type', 'expiration')->count() > 0))
                            <a href="#"
                                class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                </svg>
                                Fazer Upgrade Agora
                            </a>
                        @else
                            <a href="#"
                                class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Ver Planos
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Alpine.js para controle do popup --}}
@push('scripts')
    <script>
        // Fechar com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                // const popup = document.getElementById('subscriptionPopup');
                // if (popup) {
                //     popup.style.display = 'none';
                // }
                const popup = document.getElementById('subscriptionPopup');
                const throttleKey = '{{ $throttleKey }}'; // A chave definida no Blade

                if (popup && popup.__x && popup.__x.data.show) {
                    // Chama a função de fechar e salvar no Alpine.js (mais limpo)
                    popup.__x.data.closeAndThrottle();
                } else if (popup) {
                    // Caso de fallback (se Alpine não estiver totalmente pronto)
                    localStorage.setItem(throttleKey, Date.now());
                    popup.style.display = 'none';
                }
            }
        });

        // Pode adicionar lógica para não mostrar novamente por X horas
        // usando localStorage (se desejar)
    </script>
@endpush

{{-- CSS --}}
<style>
    [x-cloak] {
        display: none !important;
    }

    #subscriptionPopup {
        backdrop-filter: blur(4px);
    }

    #subscriptionPopup .sm\:align-middle {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
</style>
