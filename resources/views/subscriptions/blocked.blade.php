{{-- resources/views/subscription/blocked.blade.php --}}
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Acesso Bloqueado' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="w-full max-w-md">
            
            @php
                // Definir cores baseadas no motivo
                $colors = match($reason) {
                    'suspended', 'subscription_suspended', 'inactive' => [
                        'bg' => 'bg-red-50',
                        'border' => 'border-red-200',
                        'icon_bg' => 'bg-red-100',
                        'icon_text' => 'text-red-600',
                        'title' => 'text-red-900',
                        'text' => 'text-red-700',
                        'button' => 'bg-red-600 hover:bg-red-700',
                    ],
                    'expired', 'trial_expired', 'cancelled' => [
                        'bg' => 'bg-orange-50',
                        'border' => 'border-orange-200',
                        'icon_bg' => 'bg-orange-100',
                        'icon_text' => 'text-orange-600',
                        'title' => 'text-orange-900',
                        'text' => 'text-orange-700',
                        'button' => 'bg-orange-600 hover:bg-orange-700',
                    ],
                    'pending' => [
                        'bg' => 'bg-yellow-50',
                        'border' => 'border-yellow-200',
                        'icon_bg' => 'bg-yellow-100',
                        'icon_text' => 'text-yellow-600',
                        'title' => 'text-yellow-900',
                        'text' => 'text-yellow-700',
                        'button' => 'bg-yellow-600 hover:bg-yellow-700',
                    ],
                    default => [
                        'bg' => 'bg-gray-50',
                        'border' => 'border-gray-200',
                        'icon_bg' => 'bg-gray-100',
                        'icon_text' => 'text-gray-600',
                        'title' => 'text-gray-900',
                        'text' => 'text-gray-700',
                        'button' => 'bg-blue-600 hover:bg-blue-700',
                    ],
                };
            @endphp

            {{-- Card Principal --}}
            <div class="overflow-hidden bg-white rounded-lg shadow-xl">
                
                {{-- Header com ícone --}}
                <div class="px-6 pt-8 pb-6 {{ $colors['bg'] }} border-b {{ $colors['border'] }}">
                    <div class="flex flex-col items-center text-center">
                        <div class="p-4 rounded-full {{ $colors['icon_bg'] }}">
                            @if(in_array($reason, ['suspended', 'subscription_suspended', 'inactive']))
                                {{-- Ícone de bloqueio/suspenso --}}
                                <svg class="w-12 h-12 {{ $colors['icon_text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                </svg>
                            @elseif(in_array($reason, ['expired', 'trial_expired', 'cancelled']))
                                {{-- Ícone de relógio/expirado --}}
                                <svg class="w-12 h-12 {{ $colors['icon_text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @elseif($reason === 'pending')
                                {{-- Ícone de pendente --}}
                                <svg class="w-12 h-12 {{ $colors['icon_text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @else
                                {{-- Ícone de aviso genérico --}}
                                <svg class="w-12 h-12 {{ $colors['icon_text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            @endif
                        </div>
                        
                        <h1 class="mt-4 text-2xl font-bold {{ $colors['title'] }}">
                            {{ $title }}
                        </h1>
                        
                        @if($company)
                            <p class="mt-2 text-sm {{ $colors['text'] }}">
                                {{ $company->name }}
                                @if($plan)
                                    <span class="mx-2">•</span>
                                    <span class="font-medium">{{ $plan->name }}</span>
                                @endif
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Conteúdo --}}
                <div class="px-6 py-6 space-y-6">
                    {{-- Mensagem principal --}}
                    <div class="p-4 border-l-4 {{ $colors['border'] }} {{ $colors['bg'] }} rounded">
                        <p class="text-sm {{ $colors['text'] }}">
                            {{ $message }}
                        </p>
                    </div>

                    {{-- Informações da empresa --}}
                    @if($company)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Informações da Conta</h3>
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Status:</dt>
                                    <dd class="font-medium text-gray-900">
                                        {{ $company->status_name ?? ucfirst($company->status) }}
                                    </dd>
                                </div>
                                
                                @if($company->subscription_type)
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Tipo:</dt>
                                    <dd class="font-medium text-gray-900">
                                        {{ $company->subscription_type === 'trial' ? 'Período de Teste' : 'Plano Pago' }}
                                    </dd>
                                </div>
                                @endif

                                @if($company->subscription_expires_at)
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">
                                        {{ $company->subscription_type === 'trial' ? 'Trial expira:' : 'Expira em:' }}
                                    </dt>
                                    <dd class="font-medium text-gray-900">
                                        {{ $company->subscription_expires_at->format('d/m/Y') }}
                                        <span class="text-xs text-gray-500">
                                            ({{ $company->subscription_expires_at->diffForHumans() }})
                                        </span>
                                    </dd>
                                </div>
                                @endif

                                @if($plan)
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Plano Atual:</dt>
                                    <dd class="font-medium text-gray-900">
                                        {{ $plan->name }}
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    @endif

                    {{-- Informações adicionais baseadas no motivo --}}
                    @if(in_array($reason, ['expired', 'trial_expired', 'cancelled']))
                        <div class="space-y-4">
                            <h3 class="font-semibold text-gray-900">O que fazer agora?</h3>
                            <ul class="space-y-3 text-sm text-gray-600">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Escolha um plano que atenda às suas necessidades</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Realize o pagamento de forma segura</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Retome suas atividades imediatamente</span>
                                </li>
                            </ul>
                        </div>
                    @endif

                    @if(in_array($reason, ['suspended', 'subscription_suspended', 'inactive']))
                        <div class="p-4 border border-yellow-200 rounded-lg bg-yellow-50">
                            <div class="flex">
                                <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div class="text-sm text-yellow-800">
                                    <p class="font-medium">Precisa de ajuda?</p>
                                    <p class="mt-1">Nossa equipe de suporte está pronta para ajudá-lo a resolver esta situação o mais rápido possível.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Botões de ação --}}
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-3 sm:justify-between">
                        
                        {{-- Botão de ação principal --}}
                        <a  href="#"
                        {{-- href="{{ route($actionRoute) }}"  --}}
                           class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white rounded-md {{ $colors['button'] }} focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors">
                            @if(in_array($reason, ['expired', 'trial_expired', 'cancelled']))
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            @endif
                            {{ $actionText }}
                        </a>

                        {{-- Botão Sair --}}
                        <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                Sair
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <p class="mt-6 text-xs text-center text-gray-500">
                Tem dúvidas? Entre em contato: 
                <a href="mailto:suporte@sub360.co.mz" class="text-blue-600 hover:text-blue-700 hover:underline">
                    suporte@sub360.co.mz
                </a>
            </p>
        </div>
    </div>
</body>
</html>