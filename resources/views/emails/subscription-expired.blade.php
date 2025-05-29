<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscrição Expirada - DINTELL</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 20px;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e5e5;
            margin-bottom: 30px;
        }
        .logo {
            color: #1a365d;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .slogan {
            color: #666;
            font-size: 12px;
            font-style: italic;
        }
        .alert-box {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert-title {
            font-weight: bold;
            color: #721c24;
            margin-bottom: 5px;
        }
        .grace-period-box {
            background-color: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .grace-period-title {
            font-weight: bold;
            color: #0c5460;
            margin-bottom: 5px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .info-table td {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 40%;
            color: #555;
        }
        .amount-highlight {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        .amount-value {
            font-size: 24px;
            font-weight: bold;
            color: #1a365d;
        }
        .btn-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn-renew {
            background-color: #dc3545;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            transition: background-color 0.3s;
        }
        .btn-renew:hover {
            background-color: #c82333;
        }
        .warning-section {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .timeline {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .timeline-item {
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
        }
        .timeline-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
            color: white;
        }
        .timeline-icon.expired {
            background-color: #dc3545;
        }
        .timeline-icon.grace {
            background-color: #17a2b8;
        }
        .timeline-icon.suspended {
            background-color: #6c757d;
        }
        .footer {
            border-top: 2px solid #e5e5e5;
            padding-top: 20px;
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .contact-info {
            margin-top: 15px;
            font-size: 12px;
        }
        .urgent-notice {
            background-color: #dc3545;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <image src="https://beyondbusiness.co.mz/logo.png" alt="DINTELL Logo" style="width: 150px; height: auto;">
        </div>

        <div class="urgent-notice">
            ⚠️ ATENÇÃO: SUBSCRIÇÃO EXPIRADA
        </div>

        <div class="alert-box">
            <div class="alert-title">Subscrição Expirada</div>
            <p>Sua subscrição expirou em <strong>{{ $subscription->ends_at->format('d/m/Y H:i') }}</strong> e precisa ser renovada urgentemente.</p>
        </div>

        <p>Prezado(a) <strong>{{ $client->name }}</strong>,</p>

        <p>Informamos que sua subscrição expirou. Para evitar a suspensão completa dos serviços, efetue a renovação durante o período de carência.</p>

        <table class="info-table">
            <tr>
                <td>Domínio:</td>
                <td><strong>{{ $subscription->domain }}</strong></td>
            </tr>
            <tr>
                <td>Plano:</td>
                <td><strong>{{ $plan->name }}</strong></td>
            </tr>
            <tr>
                <td>Data de Expiração:</td>
                <td><strong style="color: #dc3545;">{{ $subscription->ends_at->format('d/m/Y H:i') }}</strong></td>
            </tr>
            <tr>
                <td>Status Atual:</td>
                <td><strong style="color: #dc3545;">EXPIRADO</strong></td>
            </tr>
        </table>

        <div class="grace-period-box">
            <div class="grace-period-title">⏰ Período de Carência Ativo</div>
            <p>Seus serviços continuarão funcionando até <strong>{{ $gracePeriodEnd->format('d/m/Y') }}</strong> ({{ $gracePeriodDays }} dias de carência).</p>
        </div>

        <div class="timeline">
            <h4 style="margin-bottom: 15px; color: #1a365d;">Cronograma de Ações:</h4>

            <div class="timeline-item">
                <div class="timeline-icon expired">✗</div>
                <div>
                    <strong>{{ $subscription->ends_at->format('d/m/Y') }}</strong> - Subscrição Expirou<br>
                    <small>Período de carência iniciado</small>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-icon grace">⏳</div>
                <div>
                    <strong>{{ $gracePeriodEnd->format('d/m/Y') }}</strong> - Fim do Período de Carência<br>
                    <small>Últimos {{ $gracePeriodDays }} dias para renovar</small>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-icon suspended">⚠</div>
                <div>
                    <strong>Após {{ $gracePeriodEnd->format('d/m/Y') }}</strong> - Suspensão Total<br>
                    <small>Serviços serão completamente suspensos</small>
                </div>
            </div>
        </div>

        <div class="amount-highlight">
            <div>Valor para Renovação</div>
            <div class="amount-value">MT {{ number_format($plan->price, 2) }}</div>
        </div>


        <div class="warning-section">
            <strong>⚠️ CONSEQUÊNCIAS DA NÃO RENOVAÇÃO:</strong>
            <ul style="margin: 10px 0;">
                <li>Website ficará offline após {{ $gracePeriodEnd->format('d/m/Y') }}</li>
                <li>Emails corporativos serão suspensos</li>
                <li>Backup de dados será mantido por 30 dias</li>
                <li>Taxa adicional de 5% pode ser aplicada após o período de carência</li>
            </ul>
        </div>

        <p><strong>Formas de Pagamento Urgente:</strong></p>
        <ul>
            <li><strong>Transferência bancária</strong> (confirmação imediata)</li>
            <li><strong>Depósito bancário</strong> (enviar comprovativo)</li>
            <li><strong>Pagamento presencial</strong> (escritório Maputo/Beira)</li>
        </ul>

        <div style="background-color: #d1ecf1; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Precisa de Ajuda?</strong></p>
            <p>Entre em contacto connosco imediatamente:</p>
            <p>📞 <strong>{{ $company['phone'] }}</strong> | 📧 <strong>{{ $company['email'] }}</strong></p>
        </div>


    </div>
</body>
</html>