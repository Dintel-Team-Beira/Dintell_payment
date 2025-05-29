<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscrição Ativada - {{ $company['name'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            background: #1a365d;
            color: white;
            padding: 20px;
            text-align: center;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 500;
        }
        .header .slogan {
            font-size: 11px;
            opacity: 0.9;
            margin-top: 5px;
        }
        .header img {
            max-width: 150px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .content {
            padding: 20px;
        }
        .greeting {
            font-size: 16px;
            color: #1a365d;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .info-list {
            margin: 20px 0;
        }
        .info-item {
            border-left: 3px solid #1a365d;
            padding: 10px 15px;
            margin-bottom: 8px;
            background: #f8f9fa;
            border-radius: 3px;
        }
        .info-item h4 {
            margin: 0 0 5px 0;
            font-size: 12px;
            color: #374151;
            font-weight: 600;
        }
        .info-item p {
            margin: 0;
            font-size: 14px;
            color: #1a365d;
            font-weight: 500;
        }
        .features {
            margin: 20px 0;
            padding: 15px;
            border-left: 3px solid #1a365d;
            background: #f8f9fa;
            border-radius: 3px;
        }
        .features h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .features ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .features li {
            padding: 5px 0;
            font-size: 12px;
            color: #333;
        }
        .cta-button {
            display: inline-block;
            background: #1a365d;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .important-info {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .important-info h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .important-info ul {
            margin: 0;
            padding-left: 15px;
            font-size: 12px;
            color: #333;
        }
        .company-info {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 11px;
        }
        .company-info h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .company-info p {
            margin: 5px 0;
        }
        .footer {
            background: #f3f4f6;
            padding: 15px;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
            border-bottom-left-radius: 5px;
            border-bottom-right-radius: 5px;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
            }
            .content {
                padding: 15px;
            }
            .header {
                padding: 15px;
            }
            .header img {
                max-width: 120px;
            }
            .cta-button {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            @if($company['logo'])
            <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }} Logo" style="max-width: 150px; height: auto;">
            @else
            <h1>{{ $company['name'] }}</h1>
            @endif
            <div class="slogan">{{ $company['slogan'] }}</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Prezado(a) {{ $client->name }},
            </div>

            <p>Confirmamos com satisfação a ativação da sua subscrição. O seu website está agora online e funcionando perfeitamente.</p>

            <!-- Subscription Details -->
            <div class="info-list">
                <div class="info-item">
                    <h4>Domínio</h4>
                    <p>{{ $subscription->domain }}</p>
                </div>
                <div class="info-item">
                    <h4>Plano Contratado</h4>
                    <p>{{ $plan->name }}</p>
                </div>
                <div class="info-item">
                    <h4>Valor Pago</h4>
                    <p>MT {{ number_format($subscription->amount_paid ?? $plan->price, 2) }}</p>
                </div>
                <div class="info-item">
                    <h4>Próximo Vencimento</h4>
                    <p style="color: #dc3545; font-weight: bold;">
                        {{ $subscription->ends_at ? $subscription->ends_at->format('d/m/Y') : 'Sem vencimento' }}
                    </p>
                </div>
                <div class="info-item">
                    <h4>Data de Ativação</h4>
                    <p>{{ $subscription->starts_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="info-item">
                    <h4>Status do Serviço</h4>
                    <p style="color: #1a365d; font-weight: bold;">Ativo</p>
                </div>
            </div>

            <!-- Plan Features -->
            <div class="features">
                <h3>Recursos do seu Plano</h3>
                <ul>
                    @if($plan->features && count($plan->features) > 0)
                        @foreach($plan->features as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    @else
                        <li>Hospedagem Premium com {{ $plan->max_storage_gb }}GB de Armazenamento</li>
                        <li>{{ $plan->max_bandwidth_gb }}GB de Tráfego Mensal</li>
                        <li>Suporte Técnico Especializado</li>
                        <li>Monitoramento 24/7</li>
                        <li>Backup Automático Diário</li>
                    @endif
                </ul>
            </div>

            <!-- CTA Button -->
            <div style="text-align: center;">
                <a href="https://{{ $subscription->domain }}" class="cta-button">Visitar Meu Website</a>
            </div>

            <!-- Important Information -->
            <div class="important-info">
                <h3>Informações Importantes</h3>
                <ul>
                    <li><strong>Chave API:</strong> {{ substr($subscription->api_key ?? 'N/A', 0, 20) }}... (disponível no painel de controle)</li>
                    <li><strong>Renovação:</strong> Lembrete será enviado {{ $plan->billing_cycle_days }} dias antes do vencimento (<span style="color: #dc3545; font-weight: bold;">{{ $subscription->ends_at ? $subscription->ends_at->format('d/m/Y') : 'N/A' }}</span>)</li>
                    <li><strong>Suporte:</strong> {{ $company['email'] }} | {{ $company['phone'] }}</li>
                </ul>
            </div>

            <!-- Company Information -->
            <div class="company-info">
                <h3>Contactos da {{ $company['name'] }}</h3>
                <p><strong>NUIT:</strong> {{ $company['nuit'] }}</p>
                <p><strong>Maputo:</strong> {{ $company['address_maputo'] }}</p>
                <p><strong>Beira:</strong> {{ $company['address_beira'] }}</p>
                <p><strong>Telefone:</strong> {{ $company['phone'] }}</p>
                <p><strong>Email:</strong> {{ $company['email'] }}</p>
                <p><strong>Website:</strong> {{ $company['website'] }}</p>
            </div>

            <p style="margin-top: 20px; color: #6b7280;">
                Agradecemos a sua confiança em escolher a {{ $company['name'] }} para o seu projeto digital.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} {{ $company['name'] }} - {{ $company['slogan'] }}</p>
            <p>Para suporte, entre em contacto: {{ $company['email'] }} | {{ $company['phone'] }}</p>
        </div>
    </div>
</body>
</html>