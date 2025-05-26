<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $config['title'] ?? 'Serviço Indisponível' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        .gradient-bg {
            background: linear-gradient(135deg, {{ $config['color_primary'] ?? '#667eea' }} 0%, {{ $config['color_secondary'] ?? '#764ba2' }} 100%);
        }

        .glass-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite alternate;
        }

        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes pulse-glow {
            from { box-shadow: 0 0 20px rgba(255, 255, 255, 0.2); }
            to { box-shadow: 0 0 30px rgba(255, 255, 255, 0.4), 0 0 40px rgba(255, 255, 255, 0.3); }
        }

        .status-indicator {
            animation: blink 1.5s infinite;
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0.3; }
        }

        .button-hover {
            transition: all 0.3s ease;
        }

        .button-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Background Animation -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-1/2 -left-1/2 w-full h-full opacity-10">
            <div class="w-96 h-96 bg-white rounded-full floating"></div>
        </div>
        <div class="absolute -bottom-1/2 -right-1/2 w-full h-full opacity-10">
            <div class="w-96 h-96 bg-white rounded-full floating" style="animation-delay: 1s;"></div>
        </div>
    </div>

    <div class="max-w-4xl w-full relative z-10">
        <!-- Main Card -->
        <div class="glass-card rounded-3xl p-8 md:p-12 text-center text-white shadow-2xl pulse-glow">

            <!-- Status Indicator -->
            <div class="flex justify-center mb-6">
                <div class="status-indicator w-4 h-4 bg-red-500 rounded-full"></div>
            </div>

            <!-- Icon -->
            <div class="floating mb-8">
                <div class="w-32 h-32 mx-auto bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    @switch($config['icon'] ?? 'warning')
                        @case('tools')
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            @break
                        @case('clock')
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @break
                        @case('star')
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            @break
                        @case('x-circle')
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @break
                        @default
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                    @endswitch
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                {{ $config['title'] }}
            </h1>

            <!-- Message -->
            <p class="text-xl md:text-2xl text-white/90 mb-8 leading-relaxed max-w-3xl mx-auto">
                {{ $config['message'] }}
            </p>

            <!-- Domain Info -->
            <div class="bg-white/10 rounded-2xl p-6 mb-8 backdrop-blur-sm">
                <div class="flex items-center justify-center space-x-3 mb-3">
                    <div class="w-3 h-3 bg-red-400 rounded-full status-indicator"></div>
                    <p class="text-lg text-white/80 font-medium">Domínio Afetado</p>
                </div>
                <p class="text-3xl font-bold text-white">{{ $domain }}</p>
                @if($subscription)
                    <p class="text-sm text-white/70 mt-2">ID: #{{ $subscription->id }}</p>
                @endif
            </div>

            <!-- Subscription Details -->
            @if($subscription && ($config['show_details'] ?? false))
            <div class="bg-white/10 rounded-2xl p-6 mb-8 text-left backdrop-blur-sm">
                <h3 class="text-xl font-bold text-white mb-4 text-center">Detalhes da Subscrição</h3>
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div class="space-y-3">
                        <div>
                            <p class="text-white/70">Cliente:</p>
                            <p class="text-white font-medium">{{ $subscription->client->name }}</p>
                        </div>
                        <div>
                            <p class="text-white/70">Plano:</p>
                            <p class="text-white font-medium">{{ $subscription->plan->name }}</p>
                        </div>
                        <div>
                            <p class="text-white/70">Status:</p>
                            <span class="inline-block px-3 py-1 bg-red-500/20 text-red-200 rounded-full text-xs font-medium capitalize">
                                {{ $subscription->status }}
                            </span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @if($subscription->ends_at)
                        <div>
                            <p class="text-white/70">Expiração:</p>
                            <p class="text-white font-medium">{{ $subscription->ends_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                        @if($subscription->suspended_at)
                        <div>
                            <p class="text-white/70">Suspenso em:</p>
                            <p class="text-white font-medium">{{ $subscription->suspended_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                        @if($subscription->last_payment_date)
                        <div>
                            <p class="text-white/70">Último Pagamento:</p>
                            <p class="text-white font-medium">{{ $subscription->last_payment_date->format('d/m/Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                @if(isset($config['suspension_details']))
                <div class="mt-4 pt-4 border-t border-white/20">
                    <p class="text-white/70 text-sm">Motivo:</p>
                    <p class="text-white">{{ $config['suspension_details'] }}</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                @if(isset($config['show_renewal']) && $config['show_renewal'])
                    <a href="{{ $config['renewal_url'] ?? '#' }}"
                       class="button-hover bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-8 rounded-2xl inline-flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Renovar Agora (MT {{ number_format($config['renewal_amount'] ?? 0, 2) }})
                    </a>
                @endif

                @if(isset($config['show_upgrade']) && $config['show_upgrade'])
                    <a href="#upgrade-plans"
                       class="button-hover bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-2xl inline-flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Fazer Upgrade
                    </a>
                @endif
            </div>

            <!-- Contact Info -->
            @if($config['show_contact'] ?? false)
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                @if(isset($config['support_email']))
                <div class="bg-white/10 rounded-xl p-6 backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-white/80 text-sm mb-1">Email</p>
                    <a href="mailto:{{ $config['support_email'] }}"
                       class="text-white font-semibold hover:text-white/80 transition-colors">
                        {{ $config['support_email'] }}
                    </a>
                </div>
                @endif

                @if(isset($config['support_phone']))
                <div class="bg-white/10 rounded-xl p-6 backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <p class="text-white/80 text-sm mb-1">Telefone</p>
                    <a href="tel:{{ $config['support_phone'] }}"
                       class="text-white font-semibold hover:text-white/80 transition-colors">
                        {{ $config['support_phone'] }}
                    </a>
                </div>
                @endif

                @if(isset($config['support_whatsapp']))
                <div class="bg-white/10 rounded-xl p-6 backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white mx-auto mb-3" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                    </svg>
                    <p class="text-white/80 text-sm mb-1">WhatsApp</p>
                    <a href="https://wa.me/{{ str_replace('+', '', $config['support_whatsapp']) }}"
                       class="text-white font-semibold hover:text-white/80 transition-colors">
                        {{ $config['support_whatsapp'] }}
                    </a>
                </div>
                @endif
            </div>
            @endif

            <!-- Upgrade Plans -->
            @if(isset($config['upgrade_plans']) && count($config['upgrade_plans']) > 0)
            <div id="upgrade-plans" class="bg-white/10 rounded-2xl p-6 mb-8 backdrop-blur-sm">
                <h3 class="text-2xl font-bold text-white mb-6">Escolha seu Plano</h3>
                <div class="grid md:grid-cols-3 gap-4">
                    @foreach($config['upgrade_plans'] as $plan)
                    <div class="bg-white/10 rounded-xl p-4 hover:bg-white/20 transition-colors cursor-pointer">
                        <h4 class="text-lg font-bold text-white mb-2">{{ $plan->name }}</h4>
                        <p class="text-2xl font-bold text-white mb-3">MT {{ number_format($plan->price, 2) }}</p>
                        <ul class="text-sm text-white/80 space-y-1">
                            @foreach($plan->features as $feature)
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $feature }}
                            </li>
                            @endforeach
                        </ul>
                        <button class="w-full mt-4 bg-white/20 hover:bg-white/30 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            Escolher Plano
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif