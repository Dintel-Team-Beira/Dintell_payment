<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscrição Expirada - {{ $company['name'] }}</title>
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
            padding: 20px;
        }
        .header {
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 2px solid #1a365d;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 150px;
            height: auto;
        }
        .greeting {
            font-size: 16px;
            color: #1a365d;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .alert-box {
            background: #f8f9fa;
            border: 1px solid #dc3545;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .alert-box h3 {
            color: #dc3545;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .grace-period-box {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .grace-period-box h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .info-list {
            margin: 20px 0;
        }
        .info-item {
            border-left: 3px solid #1a365d;
            padding: 10px 15px;
            margin-bottom: 10px;
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
        .timeline-list {
            background: #f8f9fa;
            border: 1px solid #dc3545;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .timeline-list h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .timeline-item {
            margin: 10px 0;
        }
        .timeline-item strong {
            color: #1a365d;
            font-weight: 600;
        }
        .timeline-item small {
            color: #dc3545;
        }
        .amount-highlight {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin: 20px 0;
        }
        .amount-highlight h4 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 5px 0;
            font-weight: 600;
        }
        .amount-highlight .amount-value {
            font-size: 18px;
            font-weight: bold;
            color: #1a365d;
        }
        .warning-section {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .warning-section h3 {
            color: #dc3545;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .warning-section ul {
            margin: 0;
            padding-left: 15px;
            font-size: 12px;
        }
        .warning-section li {
            margin: 8px 0;
            color: #dc3545;
        }
        .payment-info {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .payment-info h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .payment-info ul {
            margin: 0;
            padding-left: 15px;
            font-size: 12px;
        }
        .payment-info li {
            margin: 8px 0;
        }
        .contact-info-section {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .contact-info-section h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .btn-container {
            text-align: center;
            margin: 20px 0;
        }
        .btn-renew {
            background: #1a365d;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .footer {
            background: #f3f4f6;
            padding: 15px;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
            margin-top: 20px;
            border-top: 1px solid #ddd;
        }
        .footer .contact-info {
            margin-top: 10px;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                padding: 15px;
            }
            .header {
                padding-bottom: 10px;
            }
            .header img {
                max-width: 120px;
            }
            .btn-renew {
                display: block;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <image src="https://beyondbusiness.co.mz/logo.png" alt="DINTELL Logo" style="width: 150px; height: auto;">
        </div>

        <div class="alert-box">
            <h3>Subscrição Expirada</h3>
            <p>Sua subscrição expirou em <span style="color: #dc3545; font-weight: bold;">{{ $subscription->ends_at->format('d/m/Y H:i') }}</span>. Renove urgentemente para evitar a suspensão.</p>
        </div>

        <div class="greeting">
            Prezado(a) {{ $client->name }},
        </div>

        <p>Informamos que sua subscrição expirou. Para evitar a suspensão completa dos serviços, efetue a renovação durante o período de carência.</p>

        <div class="info-list">
            <div class="info-item">
                <h4>Domínio</h4>
                <p>{{ $subscription->domain }}</p>
            </div>
            <div class="info-item">
                <h4>Plano</h4>
                <p>{{ $plan->name }}</p>
            </div>
            <div class="info-item">
                <h4>Data de Expiração</h4>
                <p style="color: #dc3545; font-weight: bold;">{{ $subscription->ends_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="info-item">
                <h4>Status Atual</h4>
                <p style="color: #dc3545; font-weight: bold;">Expirado</p>
            </div>
        </div>

        <div class="grace-period-box">
            <h3>Período de Carência Ativo</h3>
            <p>Seus serviços estão ativos até <span style="color: #dc3545; font-weight: bold;">{{ $gracePeriodEnd->format('d/m/Y') }}</span> ({{ $gracePeriodDays }} dias restantes).</p>
        </div>

        <div class="timeline-list">
            <h3>Cronograma de Ações</h3>
            <div class="timeline-item">
                <strong>{{ $subscription->ends_at->format('d/m/Y') }}</strong> - Subscrição Expirou<br>
                <small style="color: #dc3545;">Período de carência iniciado</small>
            </div>
            <div class="timeline-item">
                <strong>{{ $gracePeriodEnd->format('d/m/Y') }}</strong> - Fim do Período de Carência<br>
                <small style="color: #dc3545;">Últimos {{ $gracePeriodDays }} dias para renovar</small>
            </div>
            <div class="timeline-item">
                <strong>Após {{ $gracePeriodEnd->format('d/m/Y') }}</strong> - Suspensão Total<br>
                <small style="color: #dc3545;">Serviços serão completamente suspensos</small>
            </div>
        </div>

        <div class="amount-highlight">
            <h4>Valor para Renovação</h4>
            <div class="amount-value">MT {{ number_format($plan->price, 2) }}</div>
        </div>

        <div class="warning-section">
            <h3>Consequências da Não Renovação</h3>
            <ul>
                <li>Website ficará offline após {{ $gracePeriodEnd->format('d/m/Y') }}.</li>
                <li>Emails corporativos serão suspensos.</li>
                <li>Backup de dados será mantido por 30 dias.</li>
                <li>Taxa adicional de 5% pode ser aplicada após o período de carência.</li>
            </ul>
        </div>

        <div class="payment-info">
            <h3>Formas de Pagamento</h3>
            <ul>
                <li>Transferência bancária (confirmação imediata).</li>
                <li>Depósito bancário (enviar comprovativo).</li>
                <li>Pagamento presencial (escritório Maputo/Beira).</li>
            </ul>
        </div>

        <div class="contact-info-section">
            <h3>Precisa de Ajuda?</h3>
            <p>Entre em contacto connosco:</p>
            <p><strong>Telefone:</strong> {{ $company['phone'] }} | <strong>Email:</strong> {{ $company['email'] }}</p>
        </div>


        <div class="footer">
            <p>{{ $company['name'] }} - {{ $company['slogan'] }}</p>
            <div class="contact-info">
                <p>{{ $company['address_maputo'] }} | {{ $company['address_beira'] }}</p>
                <p>Telefone: {{ $company['phone'] }} | Email: {{ $company['email'] }}</p>
            </div>
            <p>© {{ date('Y') }} {{ $company['name'] }}</p>
        </div>
    </div>
</body>
</html>
