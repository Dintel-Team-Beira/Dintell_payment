<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $config['title'] ?? 'Serviço Indisponível' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <!-- Main Card -->
        <div class="glass-effect rounded-3xl p-8 md:p-12 text-center text-white shadow-2xl">
            <!-- Icon -->
            <div class="floating mb-8">
                <div class="w-24 h-24 mx-auto bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
                {{ $config['title'] }}
            </h1>

            <!-- Message -->
            <p class="text-xl md:text-2xl text-white/90 mb-8 leading-relaxed">
                {{ $config['message'] }}
            </p>

            <!-- Domain Info -->
            <div class="bg-white/10 rounded-2xl p-6 mb-8">
                <p class="text-lg text-white/80 mb-2">Domínio afetado:</p>
                <p class="text-2xl font-semibold text-white">{{ $domain }}</p>
            </div>

            <!-- Support Info -->
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                @if(isset($config['support_email']))
                <div class="bg-white/10 rounded-xl p-6">
                    <svg class="w-8 h-8 text-white mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-white/80 text-sm mb-1">Email de Suporte</p>
                    <a href="mailto:{{ $config['support_email'] }}"
                       class="text-white font-semibold hover:text-white/80 transition-colors">
                        {{ $config['support_email'] }}
                    </a>
                </div>
                @endif

                @if(isset($config['support_phone']))
                <div class="bg-white/10 rounded-xl p-6">
                    <svg class="w-8 h-8 text-white mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <p class="text-white/80 text-sm mb-1">Telefone de Suporte</p>
                    <a href="tel:{{ $config['support_phone'] }}"
                       class="text-white font-semibold hover:text-white/80 transition-colors">
                        {{ $config['support_phone'] }}
                    </a>
                </div>
                @endif
            </div>

            <!-- Subscription Details -->
            @if($subscription)
            <div class="bg-white/10 rounded-xl p-6 mb-8 text-left">
                <h3 class="text-lg font-semibold text-white mb-4">Detalhes da Subscrição</h3>
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-white/70">Plano:</p>
                        <p class="text-white font-medium">{{ $subscription->plan->name }}</p>
                    </div>
                    <div>
                        <p class="text-white/70">Status:</p>
                        <p class="text-white font-medium capitalize">{{ $subscription->status }}</p>
                    </div>
                    @if($subscription->ends_at)
                    <div>
                        <p class="text-white/70">Data de Expiração:</p>
                        <p class="text-white font-medium">{{ $subscription->ends_at->format('d/m/Y') }}</p>
                    </div>
                    @endif
                    @if($subscription->suspended_at)
                    <div>
                        <p class="text-white/70">Suspenso em:</p>
                        <p class="text-white font-medium">{{ $subscription->suspended_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>

                @if($subscription->suspension_reason)
                <div class="mt-4 pt-4 border-t border-white/20">
                    <p class="text-white/70 text-sm">Motivo da Suspensão:</p>
                    <p class="text-white">{{ $subscription->suspension_reason }}</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Footer -->
            <p class="text-white/60 text-sm">
                Para reativar seu serviço, entre em contato com nossa equipe de suporte.<br>
                Agradecemos sua compreensão.
            </p>
        </div>

        <!-- Powered By -->
        <div class="text-center mt-6">
            <p class="text-white/60 text-sm">
                Powered by <span class="font-semibold">SubManager</span>
            </p>
        </div>
    </div>
</body>
</html>