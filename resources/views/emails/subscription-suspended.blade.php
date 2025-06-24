<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviço Suspenso - {{ $company['name'] }}</title>
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
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background: #f8f9fa;
            padding: 20px 10px;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        .container {
            max-width: 740px;
            margin: 0 auto;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .header {
            background: #f8f7f7;
            color: white;
            text-align: left;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .header .slogan {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 8px;
        }

        .content {
            padding: 40px;
        }

        .greeting {
            font-size: 18px;
            color: #1a1a1a;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .intro-text {
            font-size: 16px;
            color: #666666;
            margin-bottom: 32px;
            line-height: 1.7;
        }

        .alert-box {
            background: #fef2f2;
            border: 1px solid #dc3545;
            border-left: 4px solid #dc3545;
            padding: 20px;
            border-radius: 5px;
            margin: 32px 0;
        }

        .alert-box h3 {
            color: #dc3545;
            font-size: 16px;
            margin: 0 0 12px 0;
            font-weight: 600;
        }

        .alert-box p {
            margin: 0;
            color: #333;
        }

        .section-title {
            font-size: 18px;
            color: #1a1a1a;
            font-weight: 600;
            margin: 40px 0 20px 0;
            letter-spacing: -0.01em;
        }

        /* Status Information Grid */
        .status-table {
            width: 100%;
            margin-bottom: 32px;
        }

        .status-card {
            width: 48%;
            padding: 16px;
            border: 1px solid #e5e5e5;
            background: #f8f9fa;
            border-left: 3px solid #dc3545;
            vertical-align: top;
            margin-bottom: 16px;
        }

        .status-card.normal {
            border-left: 3px solid #1a365d;
        }

        .status-spacer {
            width: 4%;
        }

        .status-label {
            font-size: 12px;
            color: #666666;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
            font-weight: 500;
            display: block;
        }

        .status-value {
            font-size: 15px;
            color: #1a1a1a;
            font-weight: 600;
            display: block;
        }

        .status-value.critical {
            color: #dc3545;
        }

        .suspension-details {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 24px;
            border-radius: 5px;
            margin: 32px 0;
        }

        .suspension-details h3 {
            color: #1a365d;
            font-size: 16px;
            margin: 0 0 16px 0;
            font-weight: 600;
        }

        .reason-box {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 16px;
            margin: 16px 0;
            border-radius: 5px;
        }

        .reason-box h4 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 8px 0;
            font-weight: 600;
        }

        .payment-info {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 24px;
            border-radius: 5px;
            margin: 32px 0;
        }

        .payment-info h3 {
            color: #1a365d;
            font-size: 16px;
            margin: 0 0 16px 0;
            font-weight: 600;
        }

        .payment-info p {
            margin: 8px 0;
            font-size: 14px;
        }

        .steps-list {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 24px;
            border-radius: 5px;
            margin: 32px 0;
        }

        .steps-list h3 {
            color: #1a365d;
            font-size: 16px;
            margin: 0 0 16px 0;
            font-weight: 600;
        }

        .steps-list ol {
            margin: 16px 0;
            padding-left: 20px;
            font-size: 14px;
        }

        .steps-list li {
            margin: 12px 0;
        }

        /* Timeline Grid */
        .timeline-section {
            background: #fef2f2;
            border: 1px solid #dc3545;
            border-left: 4px solid #dc3545;
            padding: 24px;
            border-radius: 5px;
            margin: 32px 0;
        }

        .timeline-section h3 {
            color: #dc3545;
            font-size: 16px;
            margin: 0 0 20px 0;
            font-weight: 600;
        }

        .timeline-table {
            width: 100%;
        }

        .timeline-card {
            width: 48%;
            padding: 16px;
            background: #fff;
            border: 1px solid #f3c6cb;
            border-radius: 5px;
            vertical-align: top;
            margin-bottom: 16px;
        }

        .timeline-spacer {
            width: 4%;
        }

        .timeline-title {
            font-size: 14px;
            color: #1a365d;
            font-weight: 600;
            margin-bottom: 4px;
            display: block;
        }

        .timeline-date {
            font-size: 13px;
            color: #666666;
            margin-bottom: 8px;
            display: block;
        }

        .timeline-desc {
            font-size: 12px;
            color: #dc3545;
            font-style: italic;
        }

        .timeline-desc.normal {
            color: #666666;
        }

        .important-notice {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 24px;
            border-radius: 5px;
            margin: 32px 0;
        }

        .important-notice h3 {
            color: #1a365d;
            font-size: 16px;
            margin: 0 0 16px 0;
            font-weight: 600;
        }

        .important-notice ul {
            margin: 0;
            padding-left: 20px;
            font-size: 14px;
            color: #333;
        }

        .important-notice li {
            margin: 8px 0;
        }

        .footer {
            background: #1a1a1a;
            color: #ffffff;
            padding: 24px 40px;
            text-align: center;
            font-size: 13px;
            border-bottom-left-radius: 5px;
            border-bottom-right-radius: 5px;
        }

        .footer p {
            margin: 6px 0;
        }

        /* Mobile Styles */
        @media only screen and (max-width: 640px) {
            body {
                padding: 10px 5px !important;
            }

            .content {
                padding: 24px !important;
            }

            .header {
                padding: 24px !important;
            }

            .header h1 {
                font-size: 20px !important;
            }

            .status-card,
            .timeline-card {
                width: 100% !important;
                display: block !important;
                margin-bottom: 16px !important;
            }

            .status-spacer,
            .timeline-spacer {
                display: none !important;
            }

            .footer {
                padding: 20px !important;
            }
        }

        /* Outlook-specific fixes */
        <!--[if mso]>
        .status-card,
        .timeline-card {
            width: 48% !important;
        }
        <![endif]-->
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="https://beyondbusiness.co.mz/logo.png" style="width: 200px; margin-left:20px;" alt="Logo">
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Prezado(a) {{ $client->name }},
            </div>

            <p class="intro-text">Informamos que o seu serviço foi <strong style="color: #dc3545;">suspenso temporariamente</strong>. Esta situação pode ser resolvida seguindo as instruções abaixo.</p>

            <!-- Alert Box -->
            <div class="alert-box">
                <h3>⚠️ O que isso significa?</h3>
                <p>O seu website <strong>{{ $subscription->domain }}</strong> está <strong style="color: #dc3545;">fora do ar</strong> e exibe uma página de manutenção até a reativação.</p>
            </div>

            <!-- Status Information Grid -->
            <h3 class="section-title">Informações do Status</h3>
            <table class="status-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="status-card">
                        <span class="status-label">Status Atual</span>
                        <span class="status-value critical">🔴 Suspenso</span>
                    </td>
                    <td class="status-spacer"></td>
                    <td class="status-card normal">
                        <span class="status-label">Website</span>
                        <span class="status-value">{{ $subscription->domain }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="status-card normal">
                        <span class="status-label">Data da Suspensão</span>
                        <span class="status-value">{{ $suspensionDate->format('d/m/Y H:i') }}</span>
                    </td>
                    <td class="status-spacer"></td>
                    <td class="status-card normal">
                        <span class="status-label">Plano Contratado</span>
                        <span class="status-value">{{ $plan->name }}</span>
                    </td>
                </tr>
            </table>

            <!-- Suspension Details -->
            <div class="suspension-details">
                <h3>📋 Detalhes da Suspensão</h3>
                @if($suspensionReason)
                <div class="reason-box">
                    <h4>Motivo da Suspensão</h4>
                    <p>{{ $suspensionReason }}</p>
                </div>
                @endif
                <p><strong>Data de Expiração Original:</strong> {{ $subscription->ends_at ? $subscription->ends_at->format('d/m/Y') : 'N/A' }}</p>
                @if($daysOverdue > 0)
                <p><strong>Dias em Atraso:</strong> <span style="color: #dc3545; font-weight: bold;">{{ $daysOverdue }} dias</span></p>
                @endif
                <p><strong>Período de Carência:</strong> Até <span style="color: #dc3545; font-weight: bold;">{{ $gracePeriodEnd->format('d/m/Y') }}</span></p>
            </div>

            <!-- Payment Information -->
            <div class="payment-info">
                <h3>💳 Informações de Pagamento</h3>
                <p><strong>Valor para Reativação:</strong> <span style="font-size: 18px; color: #1a365d; font-weight: bold;">MT {{ number_format($amountDue, 2) }}</span></p>
                <p><strong>Banco:</strong> {{ $company['bank_name'] }}</p>
                <p><strong>Número da Conta:</strong> {{ $company['bank_account'] }}</p>
                <p><strong>NIB:</strong> {{ $company['bank_nib'] }}</p>
                <p><strong>Beneficiário:</strong> {{ $company['name'] }}</p>
                <p><strong>Referência:</strong> {{ $subscription->domain }} - {{ $client->name }}</p>
            </div>

            <!-- Reactivation Steps -->
            <div class="steps-list">
                <h3>🔄 Como Reativar o Seu Serviço</h3>
                <ol>
                    @foreach($reactivationSteps as $step)
                        <li>{{ $step }}</li>
                    @endforeach
                </ol>
                <p style="margin-top: 16px; font-weight: 600; color: #1a365d;">
                    ⏱️ Tempo de Reativação: Até 2 horas úteis após confirmação do pagamento
                </p>
            </div>

            <!-- Timeline Grid -->
            <div class="timeline-section">
                <h3>📅 Cronograma de Ações</h3>
                <table class="timeline-table" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="timeline-card">
                            <span class="timeline-title">🔴 Serviço Suspenso</span>
                            <span class="timeline-date">{{ $suspensionDate->format('d/m/Y') }}</span>
                            <div class="timeline-desc">Website fora do ar</div>
                        </td>
                        <td class="timeline-spacer"></td>
                        <td class="timeline-card">
                            <span class="timeline-title">⏳ Período de Carência</span>
                            <span class="timeline-date">Até {{ $gracePeriodEnd->format('d/m/Y') }}</span>
                            <div class="timeline-desc normal">Tempo para regularização</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="timeline-card">
                            <span class="timeline-title">❌ Cancelamento Definitivo</span>
                            <span class="timeline-date">Após {{ $gracePeriodEnd->format('d/m/Y') }}</span>
                            <div class="timeline-desc">Perda permanente de dados</div>
                        </td>
                        <td class="timeline-spacer"></td>
                        <td class="timeline-card">
                            <span class="timeline-title">✅ Reativação Possível</span>
                            <span class="timeline-date">Após pagamento</span>
                            <div class="timeline-desc normal">Até 2 horas úteis</div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Important Notice -->
            <div class="important-notice">
                <h3>ℹ️ Informações Importantes</h3>
                <ul>
                    <li><strong>Backup dos Dados:</strong> Os seus dados estão seguros durante o período de carência.</li>
                    <li><strong>Emails:</strong> Contas de email associadas também estão suspensas.</li>
                    <li><strong>Reativação:</strong> Após pagamento, tudo voltará ao normal automaticamente.</li>
                    <li><strong>Suporte:</strong> Nossa equipe está disponível para ajudar.</li>
                </ul>
            </div>

            <p style="margin-top: 32px; color: #666666; font-size: 15px;">
                Lamentamos qualquer inconveniente e estamos prontos para ajudá-lo a resolver esta situação rapidamente.
            </p>

            <p style="font-weight: 600; color: #1a365d; margin-top: 24px;">
                Atenciosamente,<br>
                Equipe {{ $company['name'] }}
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} {{ $company['name'] }} - {{ $company['slogan'] }}</p>
            <p>{{ $company['address_maputo'] }} | {{ $company['address_beira'] }}</p>
            <p>Para suporte imediato, contacte {{ $company['phone'] }}</p>
        </div>
    </div>
</body>
</html>