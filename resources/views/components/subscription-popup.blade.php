{{-- resources/views/components/subscription-popup.blade.php --}}
@props([
    'limits' => [],
    'warnings' => [],
    'company' => null,
    'plan' => null,
    'forceShow' => false
])

@php
// ✅ LÓGICA SIMPLES COM IF/ELSE - SEM CLASSES COMPLEXAS

// Verificar se é bloqueio crítico
$isCriticalBlock = false;
if ($company) {
    if ($company->status === 'suspended' || $company->status === 'expired') {
        $isCriticalBlock = true;
    }
    // Trial expirado
    if ($company->status === 'trial' && isset($company->trial_ends_at) && $company->trial_ends_at && $company->trial_ends_at->isPast()) {
        $isCriticalBlock = true;
    }
}

// Título baseado no status
$statusTitle = 'Status da Subscrição';
if ($company) {
    if ($company->status === 'suspended') {
        $statusTitle = 'Conta Suspensa';
    } elseif ($company->status === 'expired') {
        $statusTitle = 'Subscrição Expirada';
    } elseif ($company->status === 'trial') {
        $statusTitle = 'Período de Teste';
    } elseif ($company->status === 'active') {
        $statusTitle = 'Conta Ativa';
    }
}

// Classes CSS baseadas no status
$headerClass = 'bg-gray-50';
$iconClass = 'bg-gray-100 text-gray-600';
$textColorClass = 'text-gray-900';
$subtextClass = 'text-gray-600';

if ($company) {
    if ($company->status === 'suspended') {
        $headerClass = 'bg-red-50';
        $iconClass = 'bg-red-100 text-red-600';
        $textColorClass = 'text-red-900';
        $subtextClass = 'text-red-600';
    } elseif ($company->status === 'expired') {
        $headerClass = 'bg-orange-50';
        $iconClass = 'bg-orange-100 text-orange-600';
        $textColorClass = 'text-orange-900';
        $subtextClass = 'text-orange-600';
    } elseif ($company->status === 'trial') {
        $headerClass = 'bg-yellow-50';
        $iconClass = 'bg-yellow-100 text-yellow-600';
        $textColorClass = 'text-yellow-900';
        $subtextClass = 'text-yellow-600';
    } else {
        $headerClass = 'bg-blue-50';
        $iconClass = 'bg-blue-100 text-blue-600';
        $textColorClass = 'text-blue-900';
        $subtextClass = 'text-blue-600';
    }
}

// Ícone baseado no status
$statusIcon = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';

if ($company) {
    if ($company->status === 'suspended') {
        $statusIcon = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/></svg>';
    } elseif ($company->status === 'expired') {
        $statusIcon = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
    } elseif ($company->status === 'trial') {
        $statusIcon = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
    } else {
        $statusIcon = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
    }
}

// Verificar se deve mostrar popup
$shouldShow = $forceShow;
if ($company && !$shouldShow) {
    if ($company->status === 'suspended' || $company->status === 'expired') {
        $shouldShow = true;
    }
    // Trial expirando em breve
    if ($company->status === 'trial' && isset($company->trial_ends_at) && $company->trial_ends_at) {
        $daysUntilExpiry = $company->trial_ends_at->diffInDays(now());
        if ($daysUntilExpiry <= 7) {
            $shouldShow = true;
        }
    }
}
@endphp

{{-- Mostrar popup apenas se necessário --}}
@if($shouldShow)
<div id="subscriptionPopup"
     class="fixed inset-0 z-50 overflow-y-auto"
     role="dialog"
     aria-modal="true">

    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

        {{-- Overlay - se for crítico não permite fechar clicando fora --}}
        @if($isCriticalBlock)
            <div class="fixed inset-0 bg-gray-900 bg-opacity-90" aria-hidden="true"></div>
        @else
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75" onclick="closeSubscriptionPopup()" aria-hidden="true"></div>
        @endif

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        {{-- Modal Content --}}
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

            {{-- Header --}}
            <div class="px-6 pt-6 pb-4 {{ $headerClass }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 rounded-lg {{ $iconClass }}">
                            {!! $statusIcon !!}
                        </div>
                        <div>
                            <h3 class="text-xl font-bold {{ $textColorClass }}">
                                {{ $statusTitle }}
                            </h3>
                            @if($company)
                            <p class="mt-1 text-sm {{ $subtextClass }}">
                                {{ $company->name }}
                                @if($plan)
                                    - {{ $plan->name }}
                                @endif
                            </p>
                            @endif
                        </div>
                    </div>

                    {{-- Botão fechar (apenas se não for crítico) --}}
                    @if(!$isCriticalBlock)
                        <button type="button"
                                onclick="closeSubscriptionPopup()"
                                class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Conteúdo --}}
            <div class="px-6 py-4 space-y-4">

                {{-- Mensagem principal baseada no status --}}
                @if($company)
                    @if($company->status === 'suspended')
                        <div class="p-4 border-l-4 border-red-500 bg-red-50">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-red-800">Conta Suspensa</h4>
                                    <p class="mt-1 text-sm text-red-700">
                                        @if(isset($company->suspension_reason))
                                            {{ $company->suspension_reason }}
                                        @else
                                            Sua conta foi suspensa. Entre em contato com o suporte para resolver.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                    @elseif($company->status === 'expired')
                        <div class="p-4 border-l-4 border-orange-500 bg-orange-50">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-orange-800">Subscrição Expirada</h4>
                                    <p class="mt-1 text-sm text-orange-700">
                                        Sua subscrição expirou. Renove agora para continuar usando o sistema.
                                    </p>
                                </div>
                            </div>
                        </div>

                    @elseif($company->status === 'trial')
                        <div class="p-4 border-l-4 border-yellow-500 bg-yellow-50">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-yellow-800">Período de Teste</h4>
                                    <p class="mt-1 text-sm text-yellow-700">
                                        @if(isset($company->trial_ends_at) && $company->trial_ends_at)
                                            @php
                                                $daysLeft = $company->trial_ends_at->diffInDays(now());
                                            @endphp
                                            @if($daysLeft <= 1)
                                                Seu período de teste expira hoje!
                                            @else
                                                Seu período de teste expira em {{ $daysLeft }} dias.
                                            @endif
                                        @else
                                            Você está no período de teste.
                                        @endif
                                        Faça upgrade para continuar após o trial.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                {{-- Mostrar limites se existirem --}}
                @if(!empty($limits))
                    <div class="space-y-3">
                        <h4 class="font-semibold text-gray-900">Limites de Uso:</h4>
                        @foreach($limits as $limit)
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span>{{ $limit['name'] ?? 'Limite' }}</span>
                                    <span>{{ $limit['used'] ?? 0 }}/{{ $limit['total'] ?? 0 }}</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 rounded-full">
                                    @php
                                        $percentage = $limit['percentage'] ?? 0;
                                        if ($percentage >= 90) {
                                            $barColor = 'bg-red-500';
                                        } elseif ($percentage >= 70) {
                                            $barColor = 'bg-yellow-500';
                                        } else {
                                            $barColor = 'bg-green-500';
                                        }
                                    @endphp
                                    <div class="{{ $barColor }} h-2 rounded-full transition-all duration-500"
                                         style="width: {{ min($percentage, 100) }}%"></div>
                                </div>
                                @if($percentage >= 90)
                                    <p class="text-xs text-red-600">⚠️ Limite quase atingido!</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Mostrar warnings se existirem --}}
                @if(!empty($warnings))
                    <div class="space-y-2">
                        @foreach($warnings as $warning)
                            <div class="flex items-start p-3 border border-yellow-200 rounded-lg bg-yellow-50">
                                <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <h5 class="text-sm font-medium text-yellow-800">
                                        {{ $warning['title'] ?? 'Aviso' }}
                                    </h5>
                                    <p class="text-sm text-yellow-700">
                                        {{ $warning['message'] ?? '' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Botões de Ação --}}
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between space-x-4">

                    {{-- Botão Fechar (se não for crítico) --}}
                    @if(!$isCriticalBlock)
                        <button type="button"
                                onclick="closeSubscriptionPopup()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Fechar
                        </button>
                    @endif

                    {{-- Botões baseados no status --}}
                    <div class="flex space-x-3">
                        @if($company)
                            @if($company->status === 'suspended')
                                <button type="button"
                                        onclick="contactSupport()"
                                        class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    Contatar Suporte
                                </button>

                            @elseif($company->status === 'expired' || $company->status === 'trial')
                                <button type="button"
                                        onclick="upgradeSubscription()"
                                        class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                    </svg>
                                    Fazer Upgrade
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript simples --}}
<script>
function closeSubscriptionPopup() {
    @if(!$isCriticalBlock)
        document.getElementById('subscriptionPopup').style.display = 'none';
    @endif
}

function upgradeSubscription() {
    // Redirecionar para página de planos
    window.location.href = '/billing/plans';
}

function contactSupport() {
    // Redirecionar para suporte
    window.location.href = '/support/contact';
}

// Fechar com ESC (apenas se não for crítico)
@if(!$isCriticalBlock)
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSubscriptionPopup();
    }
});
@endif

// Bloquear navegação se for crítico
@if($isCriticalBlock)
window.addEventListener('beforeunload', function(e) {
    e.preventDefault();
    return e.returnValue = 'Resolva os problemas da sua conta antes de continuar.';
});
@endif
</script>

{{-- CSS simples --}}
<style>
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

@endif
