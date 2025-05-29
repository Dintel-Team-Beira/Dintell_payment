<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscrição Ativada - {{ $company['name'] }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            line-height: 1.4;
            color: #333333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 10px;
        }
        .email-container {
            max-width: 620px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: #1a365d;
            color: white;
            padding: 15px 20px;
            text-align: center;
        }
        .header img {
            max-width: 150px;
            height: auto;
        }
        .header h1 {
            margin: 8px 0 0 0;
            font-size: 18px;
            font-weight: 600;
        }
        .header .slogan {
            font-size: 11px;
            opacity: 0.9;
            margin-top: 3px;
        }
        .content {
            padding: 20px;
        }
        .greeting {
            font-size: 15px;
            color: #1a365d;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .intro-text {
            margin-bottom: 18px;
            line-height: 1.5;
        }
        .compact-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 15px 0;
        }
        .compact-card {
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 10px;
            background: #f9f9f9;
        }
        .card-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 3px;
            font-weight: 600;
            display: block;
        }
        .card-value {
            font-size: 13px;
            color: #1a365d;
            font-weight: 500;
            word-break: break-word;
        }
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        .status-suspended {
            color: #dc3545;
            font-weight: bold;
        }
        .expiration-date {
            color: #dc3545;
            font-weight: bold;
        }
        .cta-container {
            text-align: center;
            margin: 20px 0;
        }
        .cta-button {
            display: inline-block;
            background: #1a365d;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
        }
        .footer {
            background: #f3f4f6;
            padding: 12px;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
            border-top: 1px solid #e0e0e0;
        }
        @media (max-width: 600px) {
            .compact-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 400px) {
            .compact-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <img src="{{ $company['logo_url'] ?? 'https://beyondbusiness.co.mz/logo.png' }}" alt="{{ $company['name'] }} Logo">
            <h1>Subscrição @if($subscription->is_active) Ativada @else Suspensa @endif</h1>
            <div class="slogan">{{ $company['slogan'] }}</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Prezado(a) {{ $client->name }},
            </div>

            <p class="intro-text">
                @if($subscription->is_active)
                Confirmamos com satisfação a ativação da sua subscrição. O seu website está agora online e funcionando perfeitamente.
                @else
                Informamos que o seu serviço foi suspenso temporariamente. Esta situação pode ser resolvida seguindo as instruções abaixo.
                @endif
            </p>

            <!-- Compact Cards Grid -->
            <div class="compact-grid">
                <div class="compact-card">
                    <span class="card-label">Domínio</span>
                    <span class="card-value">{{ $subscription->domain }}</span>
                </div>
                <div class="compact-card">
                    <span class="card-label">Plano Contratado</span>
                    <span class="card-value">{{ $plan->name }}</span>
                </div>
                <div class="compact-card">
                    <span class="card-label">Valor Pago</span>
                    <span class="card-value">MT {{ number_format($subscription->amount_paid ?? $plan->price, 2) }}</span>
                </div>
                <div class="compact-card">
                    <span class="card-label">Próximo Vencimento</span>
                    <span class="card-value expiration-date">
                        {{ $subscription->ends_at ? $subscription->ends_at->format('d/m/Y') : 'Sem vencimento' }}
                    </span>
                </div>
                <div class="compact-card">
                    <span class="card-label">Data de @if($subscription->is_active) Ativação @else Suspensão @endif</span>
                    <span class="card-value">
                        @if($subscription->is_active)
                            {{ $subscription->starts_at->format('d/m/Y H:i') }}
                        @else
                            {{ $subscription->updated_at->format('d/m/Y H:i') }}
                        @endif
                    </span>
                </div>
                <div class="compact-card">
                    <span class="card-label">Status do Serviço</span>
                    <span class="card-value @if($subscription->is_active) status-active @else status-suspended @endif">
                        @if($subscription->is_active) Ativo @else Suspenso @endif
                    </span>
                </div>
                <div class="compact-card">
                    <span class="card-label">Chave API</span>
                    <span class="card-value">
                        {{ substr($subscription->api_key ?? 'N/A', 0, 6) }}...{{ substr($subscription->api_key ?? '', -4) }}
                    </span>
                </div>
                <div class="compact-card">
                    <span class="card-label">Período de Carência</span>
                    <span class="card-value">
                        {{ $plan->grace_period_days ?? 15 }} dias
                    </span>
                </div>
                <div class="compact-card">
                    <span class="card-label">Contato de Suporte</span>
                    <span class="card-value">{{ $company['email'] }}</span>
                </div>

                @if(!$subscription->is_active)
                <div class="compact-card" style="grid-column: span 3;">
                    <span class="card-label">Motivo da Suspensão</span>
                    <span class="card-value">{{ $subscription->suspension_reason ?? 'Pagamento pendente' }}</span>
                </div>
                @endif
            </div>

            <!-- CTA Button -->
            <div class="cta-container">
                <a href="https://{{ $subscription->domain }}" class="cta-button">
                    @if($subscription->is_active)
                        Acessar Meu Website
                    @else
                        Regularizar Situação
                    @endif
                </a>
            </div>

            @if(!$subscription->is_active)
            <div style="background: #fff8e1; border-left: 4px solid #ffc107; padding: 12px; margin: 15px 0; border-radius: 4px;">
                <p style="margin: 0; font-size: 12px; color: #5d4037;">
                    <strong>O que isso significa?</strong> O seu website {{ $subscription->domain }} está fora do ar e exibe uma página de manutenção até a reativação.
                </p>
            </div>
            @endif

            <p style="margin-top: 15px; color: #6b7280; font-size: 12px; text-align: center;">
                Agradecemos pela sua confiança em nossos serviços.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} {{ $company['name'] }} - Todos os direitos reservados</p>
            <p>Suporte: {{ $company['phone'] }} | {{ $company['email'] }}</p>
        </div>
    </div>
</body>
</html>