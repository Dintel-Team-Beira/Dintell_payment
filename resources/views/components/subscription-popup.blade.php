{{-- resources/views/components/subscription-popup.blade.php --}}
@props([
    'limits' => [],
    'warnings' => [],
    'company' => null,
    'plan' => null,
    'forceShow' => false
])

{{-- Modal de Status da Subscrição com Bloqueio --}}
<div id="subscriptionPopup"
     class="fixed inset-0 z-50 {{ $forceShow ? '' : 'hidden' }} overflow-y-auto"
     aria-labelledby="subscription-modal"
     role="dialog"
     aria-modal="true">

    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Overlay não clicável para bloqueios críticos --}}
        <div class="fixed inset-0 transition-opacity bg-gray-900 {{ $this->isCriticalBlock() ? 'bg-opacity-90' : 'bg-opacity-75' }}"
             @if(!$this->isCriticalBlock()) onclick="closeSubscriptionPopup()" @endif
             aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal Content --}}
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">

            {{-- Header com Status Critical --}}
            <div class="px-6 pt-6 pb-4 {{ $this->getHeaderClass() }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 rounded-lg {{ $this->getIconClass() }}">
                            {!! $this->getStatusIcon() !!}
                        </div>
                        <div>
                            <h3 class="text-xl font-bold {{ $this->getTextColorClass() }}">
                                {{ $this->getStatusTitle() }}
                            </h3>
                            <p class="mt-1 text-sm {{ $this->getSubtextClass() }}">
                                {{ $company ? $company->name : 'Empresa' }} - {{ $this->getPlanDisplayName() }}
                            </p>
                        </div>
                    </div>

                    {{-- Botão de fechar (só se não for bloqueio crítico) --}}
                    @if(!$this->isCriticalBlock())
                    <button type="button"
                            onclick="closeSubscriptionPopup()"
                            class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    @endif
                </div>

                {{-- Barra de status crítico --}}
                @if($this->isCriticalBlock())
                <div class="p-3 mt-4 bg-red-100 border-l-4 border-red-500 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-semibold text-red-800">Acesso Bloqueado - Ação Necessária</span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="px-6 pb-6 overflow-y-auto max-h-96">

                {{-- Mensagens de Aviso Crítico --}}
                @if($this->getCriticalWarnings())
                <div class="mb-6">
                    @foreach($this->getCriticalWarnings() as $warning)
                    <div class="mb-3 p-4 border-l-4 {{ $warning['border_class'] }} {{ $warning['bg_class'] }}">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-3 {{ $warning['icon_class'] }}">
                                {!! $warning['icon'] !!}
                            </div>
                            <div>
                                <h4 class="font-semibold {{ $warning['title_class'] }}">{{ $warning['title'] }}</h4>
                                <p class="mt-1 {{ $warning['message_class'] }}">{{ $warning['message'] }}</p>

                                @if(isset($warning['action']))
                                <div class="mt-3">
                                    <button onclick="{{ $warning['action']['callback'] }}"
                                            class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $warning['action']['class'] }}">
                                        {{ $warning['action']['text'] }}
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Informações da Empresa e Plano --}}
                <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
                    {{-- Status da Empresa --}}
                    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <h4 class="mb-3 text-sm font-semibold text-gray-800">🏢 Status da Empresa</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="px-2 py-1 text-xs rounded-full {{ $this->getStatusBadgeClass() }}">
                                    {{ $this->getStatusDisplay() }}
                                </span>
                            </div>

                            @if($this->getTrialInfo())
                            <div class="flex justify-between">
                                <span class="text-gray-600">Trial:</span>
                                <span class="font-medium {{ $this->getTrialInfo()['class'] }}">
                                    {{ $this->getTrialInfo()['text'] }}
                                </span>
                            </div>
                            @endif

                            @if($this->getExpirationInfo())
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ $this->getExpirationInfo()['label'] }}:</span>
                                <span class="font-medium {{ $this->getExpirationInfo()['class'] }}">
                                    {{ $this->getExpirationInfo()['text'] }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Plano Atual --}}
                    <div class="p-4 border border-gray-200 rounded-lg {{ $this->getPlanBgClass() }}">
                        <h4 class="mb-3 text-sm font-semibold text-gray-800">📋 Plano Atual</h4>
                        <div class="text-sm">
                            <div class="mb-2">
                                <span class="text-lg font-bold {{ $this->getPlanTextClass() }}">
                                    {{ $this->getPlanDisplayName() }}
                                </span>
                                <div class="text-gray-600">
                                    {{ $this->getPlanPrice() }}/mês
                                </div>
                            </div>

                            @if($this->getPlanFeatures())
                            <div class="mt-3 space-y-1">
                                @foreach(array_slice($this->getPlanFeatures(), 0, 3) as $feature)
                                <div class="flex items-center text-xs text-gray-700">
                                    <svg class="w-3 h-3 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $feature }}
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Limites de Uso --}}
                @if($limits && count($limits) > 0)
                <div class="mb-6">
                    <h4 class="mb-4 text-sm font-semibold text-gray-800">📊 Uso dos Recursos</h4>
                    <div class="space-y-4">
                        @foreach($this->getProcessedLimits() as $limit)
                        <div class="p-3 border border-gray-200 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">{{ $limit['label'] }}</span>
                                <span class="text-sm {{ $limit['status_class'] }}">
                                    {{ $limit['usage_text'] }}
                                </span>
                            </div>

                            <div class="w-full h-3 bg-gray-200 rounded-full">
                                <div class="h-3 rounded-full transition-all duration-300 {{ $limit['bar_class'] }}"
                                     style="width: {{ min(100, $limit['percentage']) }}%"></div>
                            </div>

                            <div class="flex justify-between mt-1 text-xs {{ $limit['percentage'] >= 100 ? 'text-red-600' : 'text-gray-600' }}">
                                <span>{{ number_format($limit['percentage'], 1) }}% usado</span>
                                @if($limit['percentage'] >= 90)
                                <span class="font-medium">
                                    {{ $limit['percentage'] >= 100 ? '⚠️ Limite excedido!' : '⚠️ Próximo do limite' }}
                                </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Timeline de Pagamentos --}}
                @if($this->getPaymentTimeline())
                <div class="mb-6">
                    <h4 class="mb-3 text-sm font-semibold text-gray-800">💳 Timeline de Pagamentos</h4>
                    <div class="p-4 border border-gray-200 rounded-lg bg-blue-50">
                        @foreach($this->getPaymentTimeline() as $event)
                        <div class="flex items-center mb-2 last:mb-0">
                            <div class="w-3 h-3 mr-3 rounded-full {{ $event['color'] }}"></div>
                            <span class="text-sm text-gray-700">{{ $event['text'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Actions Footer --}}
            <div class="px-6 py-4 border-t border-gray-200 {{ $this->getActionsBgClass() }}">
                <div class="flex flex-col justify-end gap-3 sm:flex-row">

                    {{-- Ação Primária baseada no status --}}
                    @if($this->getPrimaryAction())
                    <button type="button"
                            onclick="{{ $this->getPrimaryAction()['callback'] }}"
                            class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white border border-transparent rounded-md {{ $this->getPrimaryAction()['class'] }} hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2">
                        {!! $this->getPrimaryAction()['icon'] !!}
                        {{ $this->getPrimaryAction()['text'] }}
                    </button>
                    @endif

                    {{-- Ações Secundárias --}}
                    @foreach($this->getSecondaryActions() as $action)
                    <button type="button"
                            onclick="{{ $action['callback'] }}"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium border rounded-md {{ $action['class'] }} hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2">
                        @if(isset($action['icon']))
                            {!! $action['icon'] !!}
                        @endif
                        {{ $action['text'] }}
                    </button>
                    @endforeach

                    {{-- Botão de fechar só aparece se não for bloqueio crítico --}}
                    @if(!$this->isCriticalBlock())
                    <button type="button"
                            onclick="closeSubscriptionPopup()"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Fechar
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript para controlar o modal --}}
{{-- @push('scripts') --}}
<script>
// Controle do Modal
let subscriptionModal = {
    isBlocking: {{ $this->isCriticalBlock() ? 'true' : 'false' }},
    autoShowDelay: {{ $forceShow || count($warnings) > 0 ? '1000' : '0' }},

    init() {
        // Auto-show se necessário
        if (this.autoShowDelay > 0) {
            setTimeout(() => this.open(), this.autoShowDelay);
        }

        // Interceptar navegação se for bloqueio crítico
        if (this.isBlocking) {
            this.blockNavigation();
        }

        // Event listeners
        this.attachEventListeners();
    },

    open() {
        const modal = document.getElementById('subscriptionPopup');
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        // Focar no modal para acessibilidade
        modal.focus();

        // Log de abertura
        this.logAction('popup_opened');
    },

    close() {
        if (this.isBlocking) {
            this.showBlockingMessage();
            return false;
        }

        const modal = document.getElementById('subscriptionPopup');
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');

        this.logAction('popup_closed');
        return true;
    },

    blockNavigation() {
        // Prevenir navegação
        window.addEventListener('beforeunload', (e) => {
            e.preventDefault();
            e.returnValue = 'Você precisa resolver problemas com sua subscrição antes de continuar.';
            return e.returnValue;
        });

        // Bloquear links
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');
            if (link && !link.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                this.showBlockingMessage();
            }
        });
    },

    showBlockingMessage() {
        alert('⚠️ Sua conta está suspensa ou com problemas. Resolva os pendências para continuar usando o sistema.');
    },

    attachEventListeners() {
        // Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.close();
            }
        });
    },

    logAction(action) {
        fetch('/api/subscription-popup/log', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                action: action,
                company_id: {{ $company->id ?? 'null' }},
                timestamp: new Date().toISOString()
            })
        }).catch(console.error);
    }
};

// Ações específicas
function upgradeSubscription() {
    subscriptionModal.logAction('upgrade_clicked');
    window.location.href = '{{ route("billing.plans") }}';
}

function renewSubscription() {
    subscriptionModal.logAction('renew_clicked');
    window.location.href = '{{ route("billing.payment") }}';
}

function contactSupport() {
    subscriptionModal.logAction('support_clicked');
    window.location.href = '{{ route("support.contact") }}';
}

function viewBillingDashboard() {
    subscriptionModal.logAction('billing_dashboard_clicked');
    window.location.href = '{{ route("billing.dashboard") }}';
}

function closeSubscriptionPopup() {
    subscriptionModal.close();
}

// Inicializar quando o DOM carregar
document.addEventListener('DOMContentLoaded', () => {
    subscriptionModal.init();
});

// Verificação periódica de status (a cada 5 minutos)
setInterval(() => {
    fetch('/api/subscription/status-check', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.requires_attention && !document.getElementById('subscriptionPopup').classList.contains('hidden')) {
            // Se o status mudou e requer atenção, recarregar a página
            window.location.reload();
        }
    })
    .catch(console.error);
}, 300000); // 5 minutos
</script>
@endpush

@push('styles')
<style>
.modal-overlay {
    backdrop-filter: blur(8px);
}

/* Animações do modal */
#subscriptionPopup .sm\:align-middle {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

#subscriptionPopup:not(.hidden) .sm\:align-middle {
    opacity: 1;
    transform: translateY(0) scale(1);
}

/* Barras de progresso animadas */
.progress-bar {
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Pulso para alertas críticos */
@keyframes pulse-red {
    0%, 100% { background-color: rgb(220, 38, 38); }
    50% { background-color: rgb(239, 68, 68); }
}

.pulse-red {
    animation: pulse-red 2s infinite;
}

/* Efeitos hover bloqueados */
.blocked-hover:hover {
    cursor: not-allowed !important;
    opacity: 0.5;
}

/* Responsividade melhorada */
@media (max-width: 640px) {
    #subscriptionPopup .sm\:max-w-3xl {
        max-width: calc(100vw - 1rem);
        margin: 0.5rem;
    }

    .grid-cols-2 {
        grid-template-columns: 1fr;
    }
}
</style>
{{-- @endpush --}}

@php
// MÉTODOS HELPER PARA LÓGICA DE NEGÓCIO
class SubscriptionPopupHelper {
    private $limits, $warnings, $company, $plan;

    public function __construct($limits, $warnings, $company, $plan) {
        $this->limits = $limits;
        $this->warnings = $warnings;
        $this->company = $company;
        $this->plan = $plan;
    }

    public function isCriticalBlock(): bool {
        if (!$this->company) return false;

        $criticalStatuses = ['suspended', 'expired'];
        if (in_array($this->company->status, $criticalStatuses)) {
            return true;
        }

        // Trial expirado
        if ($this->company->status === 'trial' &&
            $this->company->trial_ends_at &&
            $this->company->trial_ends_at->isPast()) {
            return true;
        }

        // Limites críticos excedidos
        if ($this->limits) {
            foreach ($this->limits as $limit) {
                if (isset($limit['percentage']) && $limit['percentage'] >= 100) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getStatusTitle(): string {
        if (!$this->company) return 'Status da Subscrição';

        return match($this->company->status) {
            'suspended' => 'Conta Suspensa',
            'expired' => 'Subscrição Expirada',
            'trial' => $this->company->trial_ends_at && $this->company->trial_ends_at->isPast()
                ? 'Trial Expirado' : 'Período de Teste',
            'active' => 'Conta Ativa',
            default => 'Status da Subscrição'
        };
    }

    public function getCriticalWarnings(): array {
        $criticalWarnings = [];

        if (!$this->company) return $criticalWarnings;

        // Conta suspensa
        if ($this->company->status === 'suspended') {
            $criticalWarnings[] = [
                'title' => 'Conta Suspensa',
                'message' => $this->company->suspension_reason ?? 'Sua conta foi suspensa. Entre em contato com o suporte.',
                'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
                'border_class' => 'border-red-500',
                'bg_class' => 'bg-red-50',
                'icon_class' => 'text-red-600',
                'title_class' => 'text-red-800',
                'message_class' => 'text-red-700',
                'action' => [
                    'text' => 'Contatar Suporte',
                    'callback' => 'contactSupport()',
                    'class' => 'bg-red-600 text-white hover:bg-red-700'
                ]
            ];
        }

        return $criticalWarnings;
    }

    // Outros métodos helper...
    public function getPrimaryAction(): ?array {
        if (!$this->company) return null;

        return match($this->company->status) {
            'suspended' => [
                'text' => 'Contatar Suporte',
                'callback' => 'contactSupport()',
                'class' => 'bg-red-600 hover:bg-red-700',
                'icon' => '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>'
            ],
            'expired', 'trial' => [
                'text' => 'Fazer Upgrade',
                'callback' => 'upgradeSubscription()',
                'class' => 'bg-blue-600 hover:bg-blue-700',
                'icon' => '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>'
            ],
            default => null
        };
    }

    // Implementar outros métodos conforme necessário...
}

// Instanciar helper
$helper = new SubscriptionPopupHelper($limits, $warnings, $company, $plan);
@endphp
