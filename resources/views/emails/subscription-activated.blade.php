<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscrição Ativada - {{ $company['name'] }}</title>
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
            padding:-6px 40px;
            border-bottom: 1px solid #e5e5e5;
        }


        .logo-table {
            width: 100%;
        }

        .logo {
            width: 40px;
            height: 40px;
            background: #1a1a1a;
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
            background: #f8f9fa;
            border-left: 4px solid #1a1a1a;
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

        .section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 18px;
            color: #1a1a1a;
            font-weight: 600;
            margin-bottom: 20px;
            letter-spacing: -0.01em;
        }

        .details-table {
            width: 100%;
            margin-bottom: 32px;
        }

        .details-row {
            margin-bottom: 24px;
        }

        .detail-card {
            width: 48%;
            padding: 20px;
            border: 1px solid #e5e5e5;
            background: #fafafa;
            vertical-align: top;

        }

        .detail-card-right {
            width: 48%;
            padding: 20px;
            border: 1px solid #e5e5e5;
            background: #fafafa;
            vertical-align: top;
            margin-bottom: 16px;
        }

        .detail-spacer {
            width: 4%;
        }

        .detail-label {
            font-size: 12px;
            color: #666666;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
            font-weight: 500;
            display: block;
        }

        .detail-value {
            font-size: 15px;
            color: #1a1a1a;
            font-weight: 600;
            display: block;
        }

        .detail-value.highlight {
            color: #dc2626;
        }

        .detail-value.success {
            color: #10b981;
        }

        .cta-section {
            margin: 40px 0;
            padding: 24px;
            background: #f8f9fa;
            border: 1px solid #e5e5e5;
        }

        .cta-button {
            display: inline-block;
            background: #1a1a1a;
            color: white;
            padding: 14px 28px;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            border: none;
        }

        .cta-text {
            font-size: 14px;
            color: #666666;
            margin-top: 12px;
        }

        .features-table {
            width: 100%;
        }

        .feature-card {
            width: 48%;
            font-size: 14px;
            color: #666666;
            padding: 12px 16px;
            background: #fafafa;
            border-left: 2px solid #e5e5e5;
            vertical-align: top;
            margin-bottom: 16px;
        }

        .feature-spacer {
            width: 4%;
        }

        .info-section {
            background: #f8f9fa;
            padding: 24px;
            border: 1px solid #e5e5e5;
            margin: 32px 0;
        }

        .info-title {
            font-size: 16px;
            color: #1a1a1a;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .info-item {
            font-size: 14px;
            color: #666666;
            padding: 8px 0;
            border-bottom: 1px solid #e5e5e5;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-item strong {
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
            background: #fafafa;
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

            .detail-card,
            .detail-card-right,
            .feature-card,
            .company-card {
                width: 100% !important;
                display: block !important;
                margin-bottom: 16px !important;
            }

            .detail-spacer,
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
        .detail-card,
        .detail-card-right,
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
                        <td class="status-text">Subscrição Activa</td>
                    </tr>
                </table>
            </div>

            <div class="greeting">Olá {{ $client->name }},</div>

            <p class="intro">
                Confirmamos que a sua subscrição foi ativada com sucesso. O seu website está agora online e completamente funcional. Todos os serviços contratados estão disponíveis e prontos para uso.
            </p>

            <div class="section">
                <h2 class="section-title">Informações da Subscrição</h2>

                <table class="details-table" cellpadding="0" cellspacing="0">
                    <tr class="details-row">
                        <td class="detail-card">
                            <span class="detail-label">URL do Website</span>
                            <span class="detail-value">{{ $subscription->domain }}</span>
                        </td>
                        <td class="detail-spacer"></td>
                        <td class="detail-card-right">
                            <span class="detail-label">Plano Contratado</span>
                            <span class="detail-value">{{ $plan->name }}</span>
                        </td>
                    </tr>
                    <tr class="details-row">
                        <td class="detail-card">
                            <span class="detail-label">Valor Pago</span>
                            <span class="detail-value">MT {{ number_format($subscription->amount_paid ?? $plan->price, 2) }}</span>
                        </td>
                        <td class="detail-spacer"></td>
                        <td class="detail-card-right">
                            <span class="detail-label">Próximo Vencimento</span>
                            <span class="detail-value highlight">
                                {{ $subscription->ends_at ? $subscription->ends_at->format('d/m/Y') : 'Sem vencimento' }}
                            </span>
                        </td>
                    </tr>
                    <tr class="details-row">
                        <td class="detail-card">
                            <span class="detail-label">Data de Ativação</span>
                            <span class="detail-value">{{ $subscription->starts_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="detail-spacer"></td>
                        <td class="detail-card-right">
                            <span class="detail-label">Status</span>
                            <span class="detail-value success">Online</span>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="cta-section">
                <a href="https://{{ $subscription->domain }}" class="cta-button">
                    Acessar Meu Website
                </a>
                <div class="cta-text">Clique para visitar o seu website agora mesmo</div>
            </div>

            <div class="section">
                <h2 class="section-title">Recursos Incluídos</h2>

                <table class="features-table" cellpadding="0" cellspacing="0">
                    @if($plan->features && count($plan->features) > 0)
                        @php
                            $features = $plan->features;
                            $chunks = array_chunk($features, 2);
                        @endphp
                        @foreach($chunks as $chunk)
                        <tr>
                            <td class="feature-card">{{ $chunk[0] }}</td>
                            <td class="feature-spacer"></td>
                            <td class="feature-card">{{ $chunk[1] ?? '' }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="feature-card">{{ $plan->max_storage_gb }}GB de Armazenamento SSD</td>
                            <td class="feature-spacer"></td>
                            <td class="feature-card">{{ $plan->max_bandwidth_gb }}GB de Tráfego Mensal</td>
                        </tr>
                        <tr>
                            <td class="feature-card">Suporte Técnico Especializado</td>
                            <td class="feature-spacer"></td>
                            <td class="feature-card">Monitoramento 24/7</td>
                        </tr>
                        <tr>
                            <td class="feature-card">Backup Automático Diário</td>
                            <td class="feature-spacer"></td>
                            <td class="feature-card">Certificado SSL Gratuito</td>
                        </tr>
                    @endif
                </table>
            </div>

            <div class="info-section">
                <h3 class="info-title">Informações Importantes</h3>
                <div class="info-item">
                    <strong>Chave da API:</strong> {{ substr($subscription->api_key ?? 'N/A', 0, 20) }}... (disponível no painel de controle)
                </div>
                <div class="info-item">
                    <strong>Renovação:</strong> Receberá lembrete {{ $plan->billing_cycle_days }} dias antes do vencimento
                </div>
                <div class="info-item">
                    <strong>Suporte Técnico:</strong> {{ $company['email'] }} | {{ $company['phone'] }}
                </div>

            </div>
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