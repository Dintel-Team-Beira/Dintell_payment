<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviço Suspenso - {{ $company['name'] }}</title>
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
        .content {
            padding: 20px;
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
        .suspension-details {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .suspension-details h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .reason-box {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 10px;
            margin: 10px 0;
            border-radius: 3px;
        }
        .reason-box h4 {
            color: #1a365d;
            font-size: 12px;
            margin: 0 0 5px 0;
            font-weight: 600;
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
        .steps-list {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .steps-list h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .steps-list ol {
            margin: 10px 0;
            padding-left: 15px;
            font-size: 12px;
        }
        .steps-list li {
            margin: 8px 0;
        }
        .timeline-list {
            background: #f8f9fa;
            border: 1px solid #dc3545;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .timeline-list h3 {
            color: #dc3545;
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
        .contact-list {
            margin: 20px 0;
        }
        .contact-item {
            border-left: 3px solid #1a365d;
            padding: 10px 15px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 3px;
        }
        .contact-item h4 {
            margin: 0 0 5px 0;
            font-size: 12px;
            color: #374151;
            font-weight: 600;
        }
        .contact-item a {
            color: #1a365d;
            text-decoration: none;
            font-weight: 500;
        }
        .cta-buttons {
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
            font-size: 12px;
            font-weight: 600;
            margin: 5px;
        }
        .important-notice {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .important-notice h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .important-notice ul {
            margin: 0;
            padding-left: 15px;
            font-size: 12px;
            color: #333;
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
            .cta-button {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <image src="https://beyondbusiness.co.mz/logo.png" alt="DINTELL Logo" style="width: 150px; height: auto;">
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Prezado(a) {{ $client->name }},
            </div>

            <p>Informamos que o seu serviço foi <span style="color: #dc3545; font-weight: bold;">suspenso temporariamente</span>. Esta situação pode ser resolvida seguindo as instruções abaixo.</p>

            <!-- Alert Box -->
            <div class="alert-box">
                <h3>O que isso significa?</h3>
                <p>O seu website <strong>{{ $subscription->domain }}</strong> está <span style="color: #dc3545; font-weight: bold;">fora do ar</span> e exibe uma página de manutenção até a reativação.</p>
            </div>

            <!-- Status Information -->
            <div class="info-list">
                <div class="info-item critical">
                    <h4>Status Atual</h4>
                    <p style="color: #dc3545; font-weight: bold;">Suspenso</p>
                </div>
                <div class="info-item">
                    <h4>Website</h4>
                    <p>{{ $subscription->domain }}</p>
                </div>
                <div class="info-item">
                    <h4>Data da Suspensão</h4>
                    <p>{{ $suspensionDate->format('d/m/Y H:i') }}</p>
                </div>
                <div class="info-item">
                    <h4>Plano Contratado</h4>
                    <p>{{ $plan->name }}</p>
                </div>
            </div>

            <!-- Suspension Details -->
            <div class="suspension-details">
                <h3>Detalhes da Suspensão</h3>
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
                <h3>Informações de Pagamento</h3>
                <p><strong>Valor para Reativação:</strong> <span style="font-size: 16px; color: #1a365d; font-weight: bold;">MT {{ number_format($amountDue, 2) }}</span></p>
                <p><strong>Banco:</strong> {{ $company['bank_name'] }}</p>
                <p><strong>Número da Conta:</strong> {{ $company['bank_account'] }}</p>
                <p><strong>NIB:</strong> {{ $company['bank_nib'] }}</p>
                <p><strong>Beneficiário:</strong> {{ $company['name'] }}</p>
                <p><strong>Referência:</strong> {{ $subscription->domain }} - {{ $client->name }}</p>
            </div>

            <!-- Reactivation Steps -->
            <div class="steps-list">
                <h3>Como Reativar o Seu Serviço</h3>
                <ol>
                    @foreach($reactivationSteps as $step)
                        <li>{{ $step }}</li>
                    @endforeach
                </ol>
                <p style="margin-top: 15px; font-weight: 600; color: #1a365d;">
                    Tempo de Reativação: Até 2 horas úteis após confirmação do pagamento
                </p>
            </div>

            <!-- Urgency Timeline -->
            <div class="timeline-list">
                <h3>Cronograma de Ações</h3>
                <div class="timeline-item">
                    <strong>Serviço Suspenso</strong> - {{ $suspensionDate->format('d/m/Y') }}<br>
                    <small style="color: #dc3545;">Website fora do ar</small>
                </div>
                <div class="timeline-item">
                    <strong>Período de Carência</strong> - Até {{ $gracePeriodEnd->format('d/m/Y') }}<br>
                    <small>Tempo para regularização sem perda de dados</small>
                </div>
                <div class="timeline-item">
                    <strong>Cancelamento Definitivo</strong> - Após {{ $gracePeriodEnd->format('d/m/Y') }}<br>
                    <small style="color: #dc3545;">Perda permanente de dados e configurações</small>
                </div>
            </div>


            <!-- Important Notice -->
            <div class="important-notice">
                <h3>Informações Importantes</h3>
                <ul>
                    <li><strong>Backup dos Dados:</strong> Os seus dados estão seguros durante o período de carência.</li>
                    <li><strong>Emails:</strong> Contas de email associadas também estão suspensas.</li>
                    <li><strong>Reativação:</strong> Após pagamento, tudo voltará ao normal automaticamente.</li>
                    <li><strong>Suporte:</strong> Nossa equipe está disponível para ajudar.</li>
                </ul>
            </div>

            <p style="margin-top: 20px; color: #6b7280;">
                Lamentamos qualquer inconveniente e estamos prontos para ajudá-lo a resolver esta situação rapidamente.
            </p>

            <p style="font-weight: 600; color: #1a365d;">
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
