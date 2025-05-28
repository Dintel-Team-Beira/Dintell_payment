<!-- resources/views/emails/subscription-suspended.blade.php -->
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviço Suspenso - {{ $company['name'] }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background: #f8f9fa; margin: 0; padding: 0; }
        .container { max-width: 650px; margin: 0 auto; background: white; }
        .header { background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 300; }
        .header .slogan { font-size: 14px; opacity: 0.9; margin-top: 5px; }
        .content { padding: 40px 30px; }
        .urgent-badge { background: #dc2626; color: white; padding: 10px 20px; border-radius: 25px; font-size: 16px; font-weight: bold; display: inline-block; margin-bottom: 20px; animation: pulse 2s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.7; } 100% { opacity: 1; } }
        .greeting { font-size: 18px; color: #1f2937; margin-bottom: 20px; }
        .alert-box { background: #fef2f2; border: 2px solid #ef4444; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .alert-box h3 { color: #dc2626; margin-top: 0; }
        .status-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 30px 0; }
        .status-card { background: #f8fafc; border-left: 4px solid #dc2626; padding: 15px; border-radius: 5px; }
        .status-card.critical { background: #fef2f2; border-left-color: #dc2626; }
        .status-card.warning { background: #fffbeb; border-left-color: #f59e0b; }
        .status-card.info { background: #eff6ff; border-left-color: #3b82f6; }
        .status-card h4 { margin: 0 0 8px 0; color: #374151; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-card p { margin: 0; font-size: 16px; font-weight: 600; color: #111827; }
        .suspension-details { background: #f9fafb; border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .suspension-details h3 { color: #374151; margin-top: 0; }
        .reason-box { background: #fef3c7; border: 1px solid #f59e0b; padding: 15px; border-radius: 6px; margin: 15px 0; }
        .reason-box h4 { color: #92400e; margin-top: 0; }
        .payment-info { background: #dbeafe; border: 2px solid #3b82f6; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .payment-info h3 { color: #1e40af; margin-top: 0; }
        .steps-list { background: #f0fdf4; border-left: 4px solid #10b981; padding: 20px; margin: 20px 0; }
        .steps-list h3 { color: #059669; margin-top: 0; }
        .steps-list ol { margin: 10px 0; padding-left: 20px; }
        .steps-list li { margin: 8px 0; font-weight: 500; }
        .urgency-timeline { background: #fef2f2; border: 2px solid #ef4444; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .timeline-item { display: flex; align-items: center; margin: 15px 0; }
        .timeline-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; font-weight: bold; color: white; }
        .timeline-icon.past { background: #ef4444; }
        .timeline-icon.current { background: #f59e0b; animation: pulse 2s infinite; }
        .timeline-icon.future { background: #6b7280; }
        .contact-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .contact-card { background: #f8fafc; padding: 15px; border-radius: 6px; text-align: center; border: 1px solid #e5e7eb; }
        .contact-card h4 { margin: 0 0 10px 0; color: #374151; }
        .contact-card a { color: #3b82f6; text-decoration: none; font-weight: 600; }
        .cta-buttons { text-align: center; margin: 30px 0; }
        .cta-button { display: inline-block; padding: 12px 24px; margin: 5px 10px; border-radius: 6px; text-decoration: none; font-weight: 600; }
        .cta-button.primary { background: #3b82f6; color: white; }
        .cta-button.secondary { background: #10b981; color: white; }
        .footer { background: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280; }
        @media (max-width: 600px) {
            .status-grid, .contact-grid { grid-template-columns: 1fr; }
            .content { padding: 20px; }
            .cta-button { display: block; margin: 10px 0; }
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
            <div class="urgent-badge">🚨 AÇÃO URGENTE NECESSÁRIA</div>

            <div class="greeting">
                Caro(a) <strong>{{ $client->name }}</strong>,
            </div>

            <p>É com pesar que informamos que o seu serviço foi <strong style="color: #dc2626;">suspenso temporariamente</strong>. Esta é uma situação que pode ser facilmente resolvida seguindo as instruções abaixo.</p>

            <!-- Alert Box -->
            <div class="alert-box">
                <h3>⚠️ O que isso significa?</h3>
                <p>O seu website <strong>{{ $subscription->domain }}</strong> não está mais acessível ao público. Visitantes verão uma página de manutenção até que o serviço seja reativado.</p>
            </div>

            <!-- Status Grid -->
            <div class="status-grid">
                <div class="status-card critical">
                    <h4>Status Atual</h4>
                    <p style="color: #dc2626;">🔴 Suspenso</p>
                </div>
                <div class="status-card critical">
                    <h4>Website</h4>
                    <p>{{ $subscription->domain }}</p>
                </div>
                <div class="status-card warning">
                    <h4>Data da Suspensão</h4>
                    <p>{{ $suspensionDate->format('d/m/Y H:i') }}</p>
                </div>
                <div class="status-card info">
                    <h4>Plano Contratado</h4>
                    <p>{{ $plan->name }}</p>
                </div>
            </div>

            <!-- Suspension Details -->
            <div class="suspension-details">
                <h3>📋 Detalhes da Suspensão</h3>

                @if($suspensionReason)
                <div class="reason-box">
                    <h4>💡 Motivo da Suspensão:</h4>
                    <p style="color: #92400e; font-weight: 600;">{{ $suspensionReason }}</p>
                </div>
                @endif

                <p><strong>Data de Expiração Original:</strong> {{ $subscription->ends_at ? $subscription->ends_at->format('d/m/Y') : 'N/A' }}</p>

                @if($daysOverdue > 0)
                <p><strong>Dias em Atraso:</strong> <span style="color: #dc2626; font-weight: bold;">{{ $daysOverdue }} dias</span></p>
                @endif

                <p><strong>Período de Carência:</strong> Até {{ $gracePeriodEnd->format('d/m/Y') }}</p>
            </div>

            <!-- Payment Information -->
            <div class="payment-info">
                <h3>💰 Informações de Pagamento</h3>
                <p><strong>Valor para Reativação:</strong> <span style="font-size: 18px; color: #1e40af; font-weight: bold;">MT {{ number_format($amountDue, 2) }}</span></p>

                <h4>📋 Dados para Transferência:</h4>
                <p><strong>Banco:</strong> {{ $company['bank_name'] }}</p>
                <p><strong>Número da Conta:</strong> {{ $company['bank_account'] }}</p>
                <p><strong>NIB:</strong> {{ $company['bank_nib'] }}</p>
                <p><strong>Beneficiário:</strong> {{ $company['name'] }}</p>
                <p><strong>Referência:</strong> {{ $subscription->domain }} - {{ $client->name }}</p>
            </div>

            <!-- Reactivation Steps -->
            <div class="steps-list">
                <h3>🔧 Como Reativar o Seu Serviço</h3>
                <ol>
                    @foreach($reactivationSteps as $step)
                        <li>{{ $step }}</li>
                    @endforeach
                </ol>
                <p style="margin-top: 15px; font-weight: 600; color: #059669;">
                    ⏱️ <strong>Tempo de Reativação:</strong> Até 2 horas úteis após confirmação do pagamento
                </p>
            </div>

            <!-- Urgency Timeline -->
            <div class="urgency-timeline">
                <h3 style="color: #dc2626; margin-top: 0;">⏰ Cronograma de Ações</h3>

                <div class="timeline-item">
                    <div class="timeline-icon past">1</div>
                    <div>
                        <strong>Serviço Suspenso</strong> - {{ $suspensionDate->format('d/m/Y') }}<br>
                        <small>Website fora do ar</small>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon current">2</div>
                    <div>
                        <strong>Período de Carência</strong> - Até {{ $gracePeriodEnd->format('d/m/Y') }}<br>
                        <small>Tempo para regularização sem perda de dados</small>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon future">3</div>
                    <div>
                        <strong>Cancelamento Definitivo</strong> - Após {{ $gracePeriodEnd->format('d/m/Y') }}<br>
                        <small style="color: #dc2626;">⚠️ Perda permanente de dados e configurações</small>
                    </div>
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="cta-buttons">
                <a href="tel:{{ $company['phone'] }}" class="cta-button primary">📞 Ligar Agora</a>
                <a href="https://wa.me/{{ str_replace(['+', ' '], '', $company['whatsapp']) }}" class="cta-button secondary">💬 WhatsApp</a>
            </div>

            <!-- Contact Information -->
            <div class="contact-grid">
                <div class="contact-card">
                    <h4>📞 Telefone</h4>
                    <a href="tel:{{ $company['phone'] }}">{{ $company['phone'] }}</a>
                </div>
                <div class="contact-card">
                    <h4>📧 Email Comercial</h4>
                    <a href="mailto:{{ $company['email'] }}">{{ $company['email'] }}</a>
                </div>
                <div class="contact-card">
                    <h4>🛠️ Suporte Técnico</h4>
                    <a href="mailto:{{ $company['support_email'] }}">{{ $company['support_email'] }}</a>
                </div>
                <div class="contact-card">
                    <h4>💬 WhatsApp</h4>
                    <a href="https://wa.me/{{ str_replace(['+', ' '], '', $company['whatsapp']) }}">{{ $company['whatsapp'] }}</a>
                </div>
            </div>

            <!-- Important Notice -->
            <div style="background: #fffbeb; border: 1px solid #f59e0b; padding: 20px; border-radius: 8px; margin: 30px 0;">
                <h3 style="color: #92400e; margin-top: 0;">📢 Informações Importantes</h3>
                <ul style="margin: 0; padding-left: 20px; color: #92400e;">
                    <li><strong>Backup dos Dados:</strong> Os seus dados estão seguros durante o período de carência</li>
                    <li><strong>Emails:</strong> Contas de email associadas também estão suspensas</li>
                    <li><strong>Reativação:</strong> Após pagamento, tudo voltará ao normal automaticamente</li>
                    <li><strong>Suporte:</strong> Nossa equipe está disponível para ajudar</li>
                </ul>
            </div>

            <p style="margin-top: 30px; color: #6b7280;">
                Lamentamos qualquer inconveniente causado e estamos prontos para ajudá-lo a resolver esta situação rapidamente.
                A DINTELL valoriza a sua parceria e espera continuar prestando nossos serviços com excelência.
            </p>

            <p style="font-weight: 600; color: #1f2937;">
                Atenciosamente,<br>
                <strong>Equipe {{ $company['name'] }}</strong>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $company['name'] }} - {{ $company['slogan'] }}</p>
            <p><strong>Endereços:</strong> {{ $company['address_maputo'] }} | {{ $company['address_beira'] }}</p>
            <p>Este é um aviso automático importante. Para suporte imediato, contacte {{ $company['phone'] }}</p>
        </div>
    </div>
</body>
</html>