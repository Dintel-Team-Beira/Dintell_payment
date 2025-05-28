<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscrição Ativada - {{ $company['name'] }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background: #f8f9fa; margin: 0; padding: 0; }
        .container { max-width: 650px; margin: 0 auto; background: white; }
        .header { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 300; }
        .header .slogan { font-size: 14px; opacity: 0.9; margin-top: 5px; }
        .content { padding: 40px 30px; }
        .success-badge { background: #10b981; color: white; padding: 8px 16px; border-radius: 20px; font-size: 14px; display: inline-block; margin-bottom: 20px; }
        .greeting { font-size: 18px; color: #1f2937; margin-bottom: 20px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 30px 0; }
        .info-card { background: #f8fafc; border-left: 4px solid #3b82f6; padding: 15px; border-radius: 5px; }
        .info-card h4 { margin: 0 0 8px 0; color: #374151; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-card p { margin: 0; font-size: 16px; font-weight: 600; color: #111827; }
        .features { background: #f0f9ff; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .features h3 { color: #1e40af; margin-top: 0; }
        .features ul { list-style: none; padding: 0; }
        .features li { padding: 5px 0; }
        .features li:before { content: "✓"; color: #10b981; font-weight: bold; margin-right: 8px; }
        .cta-button { background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; }
        .company-info { background: #f9fafb; padding: 20px; border-radius: 8px; margin-top: 30px; font-size: 14px; }
        .company-info h4 { margin-top: 0; color: #374151; }
        .footer { background: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280; }
        .highlight { background: #fef3c7; padding: 2px 6px; border-radius: 3px; }
        @media (max-width: 600px) {
            .info-grid { grid-template-columns: 1fr; }
            .content { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $company['name'] }}</h1>
            <div class="slogan">{{ $company['slogan'] }}</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="success-badge">✅ SUBSCRIÇÃO ATIVADA</div>

            <div class="greeting">
                Parabéns, <strong>{{ $client->name }}</strong>!
            </div>

            <p>É com grande satisfação que confirmamos a ativação da sua subscrição. O seu website está agora <span class="highlight">online e funcionando perfeitamente</span>!</p>

            <!-- Subscription Details -->
            <div class="info-grid">
                <div class="info-card">
                    <h4>Domínio</h4>
                    <p>{{ $subscription->domain }}</p>
                </div>
                <div class="info-card">
                    <h4>Plano Contratado</h4>
                    <p>{{ $plan->name }}</p>
                </div>
                <div class="info-card">
                    <h4>Valor Pago</h4>
                    <p>MT {{ number_format($subscription->amount_paid ?? $plan->price, 2) }}</p>
                </div>
                <div class="info-card">
                    <h4>Próximo Vencimento</h4>
                    <p>{{ $subscription->ends_at ? $subscription->ends_at->format('d/m/Y') : 'Sem vencimento' }}</p>
                </div>
                <div class="info-card">
                    <h4>Data de Ativação</h4>
                    <p>{{ $subscription->starts_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="info-card">
                    <h4>Status do Serviço</h4>
                    <p style="color: #059669;">🟢 Ativo</p>
                </div>
            </div>

            <!-- Plan Features -->
            <div class="features">
                <h3>🎯 Recursos Incluídos no seu Plano</h3>
                <ul>
                    @if($plan->features)
                        @foreach($plan->features as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    @endif
                    <li>Hospedagem Premium com {{ $plan->max_storage_gb }}GB de Armazenamento</li>
                    <li>{{ $plan->max_bandwidth_gb }}GB de Tráfego Mensal</li>
                    <li>Suporte Técnico Especializado</li>
                    <li>Monitoramento 24/7</li>
                    <li>Backup Automático Diário</li>
                </ul>
            </div>

            <!-- CTA Button -->
            <div style="text-align: center;">
                <a href="https://{{ $subscription->domain }}" class="cta-button">🌐 Visitar Meu Website</a>
            </div>

            <!-- Important Information -->
            <div style="background: #fef7cd; border: 1px solid #f59e0b; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <h4 style="color: #92400e; margin-top: 0;">📋 Informações Importantes:</h4>
                <ul style="margin: 0; color: #92400e;">
                    <li><strong>Chave API:</strong> {{ substr($subscription->api_key, 0, 20) }}... <em>(disponível no painel de controle)</em></li>
                    <li><strong>Renovação:</strong> {{ $plan->billing_cycle_days }} dias antes do vencimento</li>
                    <li><strong>Suporte:</strong> {{ $company['email'] }} | {{ $company['phone'] }}</li>
                </ul>
            </div>

            <!-- Company Information -->
            <div class="company-info">
                <h4>📞 Contactos da {{ $company['name'] }}</h4>
                <p><strong>NUIT:</strong> {{ $company['nuit'] }}</p>
                <p><strong>Maputo:</strong> {{ $company['address_maputo'] }}</p>
                <p><strong>Beira:</strong> {{ $company['address_beira'] }}</p>
                <p><strong>Telefone:</strong> {{ $company['phone'] }}</p>
                <p><strong>Email:</strong> {{ $company['email'] }}</p>
                <p><strong>Website:</strong> {{ $company['website'] }}</p>
            </div>

            <p style="margin-top: 30px; color: #6b7280;">
                Agradecemos a sua confiança em escolher a DINTELL para o seu projeto digital.
                Estamos aqui para garantir que a sua presença online seja um sucesso!
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $company['name'] }} - {{ $company['slogan'] }}</p>
            <p>Este email foi enviado automaticamente. Por favor, não responda diretamente.</p>
            <p>Se precisar de ajuda, entre em contacto através de {{ $company['email'] }}</p>
        </div>
    </div>
</body>
</html>
