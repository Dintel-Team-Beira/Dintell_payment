<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscrição Renovada - {{ $company['name'] }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            background: #ffffff;
            padding: 40px 20px;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        .email-container {
            max-width: 740px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e5e5e5;
        }

        .header {
            background: #ffffff;
            border-bottom: 1px solid #e5e5e5;
        }

        .logo-section {
            margin-bottom: 32px;
        }

        .logo-table {
            width: 100%;
        }

        .logo {
            width: 40px;
            height: 40px;
            background: #10b981;
            border-radius: 6px;
            display: inline-block;
            text-align: center;
            line-height: 40px;
            margin-right: 16px;
            vertical-align: middle;
        }

        .logo-text {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
            vertical-align: middle;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
            letter-spacing: -0.01em;
        }

        .subtitle {
            font-size: 16px;
            color: #666666;
            font-weight: 400;
        }

        .content {
            padding: 40px;
        }

        .status-section {
            padding: 16px 20px;
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            margin-bottom: 32px;
        }

        .status-table {
            width: 100%;
        }

        .status-icon {
            width: 20px;
            height: 20px;
            background: #10b981;
            border-radius: 50%;
            display: inline-block;
            text-align: center;
            line-height: 20px;
            color: white;
            font-size: 12px;
            font-weight: bold;
            margin-right: 12px;
            vertical-align: middle;
        }

        .status-text {
            font-size: 15px;
            font-weight: 500;
            color: #1a1a1a;
            vertical-align: middle;
        }

        .greeting {
            font-size: 20px;
            color: #1a1a1a;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .intro {
            font-size: 16px;
            color: #666666;
            margin-bottom: 40px;
            line-height: 1.7;
        }

        .section-title {
            font-size: 18px;
            color: #1a1a1a;
            font-weight: 600;
            margin: 40px 0 20px 0;
            letter-spacing: -0.01em;
        }

        .renewal-summary {
            background: #f0fdf4;
            border: 1px solid #10b981;
            border-left: 4px solid #10b981;
            padding: 24px;
            border-radius: 5px;
            margin: 32px 0;
        }

        .renewal-summary h3 {
            color: #10b981;
            font-size: 16px;
            margin: 0 0 16px 0;
            font-weight: 600;
        }

        .renewal-summary p {
            margin: 8px 0;
            font-size: 14px;
            color: #1a1a1a;
        }

        /* Date Comparison Grid */
        .dates-table {
            width: 100%;
            margin-bottom: 32px;
        }

        .date-card {
            width: 48%;
            padding: 20px;
            border: 1px solid #e5e5e5;
            background: #f8f9fa;
            vertical-align: top;
            margin-bottom: 16px;
        }

        .date-card.expired {
            border-left: 3px solid #dc2626;
        }

        .date-card.active {
            border-left: 3px solid #10b981;
        }

        .date-spacer {
            width: 4%;
        }

        .date-label {
            font-size: 12px;
            color: #666666;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
            font-weight: 500;
            display: block;
        }

        .date-value {
            font-size: 15px;
            color: #1a1a1a;
            font-weight: 600;
            display: block;
        }

        .date-value.expired {
            color: #dc2626;
        }

        .date-value.active {
            color: #10b981;
        }

        /* Service Features Grid */
        .features-table {
            width: 100%;
            margin-bottom: 32px;
        }

        .feature-card {
            width: 48%;
            padding: 16px;
            background: #f8f9fa;
            border: 1px solid #e5e5e5;
            border-left: 2px solid #10b981;
            vertical-align: top;
            margin-bottom: 16px;
        }

        .feature-spacer {
            width: 4%;
        }

        .feature-label {
            font-size: 12px;
            color: #666666;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 500;
            display: block;
        }

        .feature-value {
            font-size: 14px;
            color: #1a1a1a;
            font-weight: 600;
            display: block;
        }

        .financial-details {
            background: #f0fdf4;
            border: 1px solid #10b981;
            padding: 24px;
            border-radius: 5px;
            margin: 32px 0;
        }

        .financial-details h3 {
            color: #10b981;
            font-size: 16px;
            margin: 0 0 16px 0;
            font-weight: 600;
        }

        .financial-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #d1fae5;
            font-size: 14px;
        }

        .financial-item:last-child {
            border-bottom: none;
            font-weight: 600;
            font-size: 16px;
            color: #10b981;
            margin-top: 8px;
            padding-top: 16px;
            border-top: 2px solid #10b981;
        }

        .next-steps {
            background: #f0fdf4;
            border: 1px solid #10b981;
            padding: 24px;
            border-radius: 5px;
            margin: 32px 0;
        }

        .next-steps h3 {
            color: #10b981;
            font-size: 16px;
            margin: 0 0 16px 0;
            font-weight: 600;
        }

        .next-steps ul {
            margin: 0;
            padding-left: 20px;
            font-size: 14px;
        }

        .next-steps li {
            margin: 8px 0;
            color: #1a1a1a;
        }

        .company-section {
            border-top: 1px solid #e5e5e5;
            padding-top: 32px;
            margin-top: 40px;
        }

        .company-title {
            font-size: 16px;
            color: #1a1a1a;
            font-weight: 600;
            margin-bottom: 24px;
        }

        .company-table {
            width: 100%;
        }

        .company-card {
            width: 48%;
            padding: 16px;
            background: #f8f9fa;
            border: 1px solid #e5e5e5;
            vertical-align: top;
            margin-bottom: 16px;
        }

        .company-spacer {
            width: 4%;
        }

        .company-label {
            font-size: 12px;
            color: #666666;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
            font-weight: 500;
            display: block;
        }

        .company-value {
            font-size: 14px;
            color: #1a1a1a;
            font-weight: 500;
            display: block;
        }

        .footer {
            background: #1a1a1a;
            color: #ffffff;
            padding: 24px 40px;
            font-size: 13px;
        }

        .footer-table {
            width: 100%;
        }

        .footer-left {
            text-align: left;
            vertical-align: middle;
        }

        .footer-right {
            text-align: right;
            vertical-align: middle;
        }

        .footer a {
            color: #ffffff;
            text-decoration: none;
            opacity: 0.8;
            margin-left: 24px;
        }

        .footer a:hover {
            opacity: 1;
        }

        /* Mobile Styles */
        @media only screen and (max-width: 640px) {
            body {
                padding: 20px 16px !important;
            }

            .content {
                padding: 24px !important;
            }

            .header {
                padding: 32px 24px !important;
            }

            .header h1 {
                font-size: 24px !important;
            }

            .date-card,
            .feature-card,
            .company-card {
                width: 100% !important;
                display: block !important;
                margin-bottom: 16px !important;
            }

            .date-spacer,
            .feature-spacer,
            .company-spacer {
                display: none !important;
            }

            .footer-left,
            .footer-right {
                text-align: center !important;
                display: block !important;
                margin-bottom: 16px !important;
            }

            .footer a {
                margin: 0 12px !important;
            }
        }

        /* Outlook-specific fixes */
        <!--[if mso]>
        .date-card,
        .feature-card,
        .company-card {
            width: 48% !important;
        }
        <![endif]-->
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">

                <img src="https://beyondbusiness.co.mz/logo.png" style="width: 200px; margin-left:20px;" alt="Logo">
        </div>

        <div class="content">
            <div class="status-section">
                <table class="status-table" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <div class="status-icon">✓</div>
                        </td>
                        <td class="status-text">Renovação processada com sucesso</td>
                    </tr>
                </table>
            </div>

            <div class="greeting">Olá {{ $client->name }},</div>

            <p class="intro">
                Sua subscrição foi renovada com sucesso. O serviço permanece ativo sem interrupções.
                Agradecemos pela confiança continuada em nossos serviços.
            </p>

            <!-- Renewal Summary -->
            <div class="renewal-summary">
                <h3>📋 Resumo da Renovação</h3>
                <p><strong>Domínio:</strong> {{ $subscription->domain }}</p>
                <p><strong>Plano:</strong> {{ $plan->name }}</p>
                <p><strong>Valor Pago:</strong> MT {{ number_format($amount, 2) }}</p>
                <p><strong>Data de Renovação:</strong> {{ $renewalDate->format('d/m/Y') }}</p>
                <p><strong>Período Estendido:</strong> {{ $daysExtended}} dias</p>
            </div>

            <!-- Date Comparison Grid -->
            <h2 class="section-title">Comparação de Datas</h2>
            <table class="dates-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="date-card expired">
                        <span class="date-label">Data Anterior</span>
                        <span class="date-value expired">
                            {{ $oldExpiryDate ? $oldExpiryDate->format('d/m/Y') : 'Expirada' }}
                        </span>
                    </td>
                    <td class="date-spacer"></td>
                    <td class="date-card active">
                        <span class="date-label">Nova Data de Expiração</span>
                        <span class="date-value active">{{ $nextBillingDate->format('d/m/Y') }}</span>
                    </td>
                </tr>
            </table>

            <!-- Service Features Grid -->
            <h2 class="section-title">Recursos do Serviço</h2>
            <table class="features-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="feature-card">
                        <span class="feature-label">Website</span>
                        <span class="feature-value">{{ $subscription->domain }}</span>
                    </td>
                    <td class="feature-spacer"></td>
                    <td class="feature-card">
                        <span class="feature-label">Status</span>
                        <span class="feature-value">{{ $plan->name }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="feature-card">
                        <span class="feature-label">Armazenamento</span>
                        <span class="feature-value">{{ $plan->max_storage }}GB</span>
                    </td>
                    <td class="feature-spacer"></td>
                    <td class="feature-card">
                        <span class="feature-label">Tráfego</span>
                        <span class="feature-value">{{ $plan->max_bandwidth }}GB/mês</span>
                    </td>
                </tr>
            </table>

            <!-- Financial Details -->
            <div class="financial-details">
                <h3>💰 Detalhes Financeiros</h3>
                <div class="financial-item">
                    <span>Subtotal:</span>
                    <span>MT {{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="financial-item">
                    <span>IVA (16%):</span>
                    <span>MT {{ number_format($iva_amount, 2) }}</span>
                </div>
                <div class="financial-item">
                    <span>Total Pago:</span>
                    <span>MT {{ number_format($amount, 2) }}</span>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="next-steps">
                <h3>🚀 Próximos Passos</h3>
                <ul>
                    <li>O seu website continua ativo sem interrupções</li>
                    <li>Próxima renovação: <strong>{{ $nextBillingDate->format('d/m/Y') }}</strong></li>
                    <li>Receberá um lembrete 7 dias antes do vencimento</li>
                    <li>Comprovativo oficial será enviado em anexo</li>
                    <li>Todos os recursos permanecem disponíveis</li>
                </ul>
            </div>

            <div class="company-section">
                <h3 class="company-title">Informações de Contacto</h3>
                <table class="company-table" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="company-card">
                            <span class="company-label">NUIT</span>
                            <span class="company-value">{{ $company['nuit'] }}</span>
                        </td>
                        <td class="company-spacer"></td>
                        <td class="company-card">
                            <span class="company-label">Telefone</span>
                            <span class="company-value">{{ $company['phone'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="company-card">
                            <span class="company-label">Escritório Maputo</span>
                            <span class="company-value">{{ $company['address_maputo'] }}</span>
                        </td>
                        <td class="company-spacer"></td>
                        <td class="company-card">
                            <span class="company-label">Escritório Beira</span>
                            <span class="company-value">{{ $company['address_beira'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="company-card">
                            <span class="company-label">Email</span>
                            <span class="company-value">{{ $company['email'] }}</span>
                        </td>
                        <td class="company-spacer"></td>
                        <td class="company-card">
                            <span class="company-label">Website</span>
                            <span class="company-value">{{ $company['website'] }}</span>
                        </td>
                    </tr>
                </table>
            </div>

            <p style="margin-top: 32px; color: #666666; font-size: 15px;">
                Obrigado por confiar nos nossos serviços. Continuamos comprometidos em oferecer a melhor experiência digital.
            </p>
        </div>

        <div class="footer">
            <table class="footer-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="footer-left">© {{ date('Y') }} {{ $company['name'] }} - Todos os direitos reservados</td>
                    <td class="footer-right">
                        <a href="mailto:{{ $company['email'] }}">Suporte</a>
                        <a href="{{ $company['website'] }}">Website</a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>