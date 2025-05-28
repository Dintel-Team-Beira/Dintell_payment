<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Aviso de Suspensão {{ $noticeNumber }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #dc2626; padding-bottom: 20px; }
        .company-name { font-size: 28px; font-weight: bold; color: #dc2626; margin-bottom: 5px; }
        .company-slogan { font-size: 12px; color: #666; margin-bottom: 10px; }
        .document-title { font-size: 24px; font-weight: bold; color: #dc2626; margin: 20px 0; text-align: center; }
        .urgent-stamp { border: 3px solid #dc2626; padding: 10px; text-align: center; margin: 20px 0; background: #fef2f2; }
        .urgent-stamp h2 { color: #dc2626; margin: 0; font-size: 18px; }
        .notice-info { display: table; width: 100%; margin: 20px 0; }
        .left-info, .right-info { display: table-cell; width: 50%; vertical-align: top; padding: 0 10px; }
        .info-section { margin-bottom: 20px; }
        .info-section h3 { background: #f8f9fa; padding: 8px; margin: 0 0 10px 0; font-size: 14px; border-left: 4px solid #dc2626; }
        .suspension-details { background: #fef2f2; border: 2px solid #dc2626; padding: 20px; margin: 20px 0; }
        .suspension-details h3 { color: #dc2626; margin-top: 0; }
        .reason-box { background: #fffbeb; border: 1px solid #f59e0b; padding: 15px; margin: 15px 0; }
        .timeline-section { margin: 30px 0; }
        .timeline-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .timeline-table th, .timeline-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .timeline-table th { background: #f8f9fa; font-weight: bold; }
        .timeline-table .past { background: #fef2f2; }
        .timeline-table .current { background: #fffbeb; }
        .timeline-table .future { background: #f3f4f6; }
        .payment-section { background: #f0f9ff; border: 2px solid #3b82f6; padding: 20px; margin: 20px 0; }
        .payment-section h3 { color: #1e40af; margin-top: 0; }
        .amount-box { background: #dbeafe; border: 1px solid #3b82f6; padding: 15px; text-align: center; margin: 15px 0; }
        .amount-box .amount { font-size: 24px; font-weight: bold; color: #1e40af; }
        .steps-section { background: #f0fdf4; border-left: 4px solid #10b981; padding: 20px; margin: 20px 0; }
        .steps-section h3 { color: #059669; margin-top: 0; }
        .steps-section ol { margin: 10px 0; padding-left: 20px; }
        .steps-section li { margin: 8px 0; font-weight: 500; }
        .contact-section { background: #f8fafc; padding: 20px; margin: 20px 0; }
        .contact-grid { display: table; width: 100%; }
        .contact-item { display: table-cell; width: 25%; padding: 10px; text-align: center; vertical-align: top; }
        .footer { margin-top: 50px; border-top: 2px solid #e5e7eb; padding-top: 20px; text-align: center; font-size: 11px; color: #6b7280; }
        .signature-section { margin-top: 60px; display: table; width: 100%; }
        .signature-box { display: table-cell; width: 50%; text-align: center; }
        .signature-line { border-top: 1px solid #000; margin-top: 50px; padding-top: 5px; }
        .watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 60px; color: rgba(220, 38, 38, 0.1); z-index: -1; }
        .page-number { position: fixed; bottom: 20px; right: 20px; font-size: 10px; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ $company['name'] }}</div>
        <div class="company-slogan">{{ $company['slogan'] }}</div>
        <div style="font-size: 11px; margin-top: 15px;">
            NUIT: {{ $company['nuit'] }} | {{ $company['phone'] }} | {{ $company['email'] }}<br>
            {{ $company['address_maputo'] }}<br>
            {{ $company['address_beira'] }}
        </div>
    </div>

    <!-- Urgent Stamp -->
    <div class="urgent-stamp">
        <h2>🚨 AVISO URGENTE DE SUSPENSÃO DE SERVIÇO</h2>
        <p style="margin: 5px 0 0 0;">Documento Nº {{ $noticeNumber }}</p>
    </div>

    <!-- Document Title -->
    <div class="document-title">
        NOTIFICAÇÃO DE SUSPENSÃO DE SERVIÇO
    </div>

    <!-- Notice Information -->
    <div class="notice-info">
        <div class="left-info">
            <div class="info-section">
                <h3>👤 Dados do Cliente</h3>
                <strong>Nome:</strong> {{ $client->name }}<br>
                <strong>Email:</strong> {{ $client->email }}<br>
                <strong>Telefone:</strong> {{ $client->phone ?? 'N/A' }}<br>
                @if($client->nuit)
                <strong>NUIT:</strong> {{ $client->nuit }}<br>
                @endif
                <strong>Cliente Nº:</strong> {{ str_pad($client->id, 6, '0', STR_PAD_LEFT) }}
            </div>
        </div>
        <div class="right-info">
            <div class="info-section">
                <h3>🌐 Dados do Serviço</h3>
                <strong>Domínio:</strong> {{ $subscription->domain }}<br>
                <strong>Plano:</strong> {{ $plan->name }}<br>
                <strong>Status:</strong> <span style="color: #dc2626; font-weight: bold;">SUSPENSO</span><br>
                <strong>Data Suspensão:</strong> {{ $suspensionDate->format('d/m/Y H:i') }}<br>
                <strong>ID Subscrição:</strong> {{ $subscription->id }}
            </div>
        </div>
    </div>

    <!-- Suspension Details -->
    <div class="suspension-details">
        <h3>📋 DETALHES DA SUSPENSÃO</h3>

        @if($suspensionReason)
        <div class="reason-box">
            <strong>💡 Motivo da Suspensão:</strong><br>
            {{ $suspensionReason }}
        </div>
        @endif

        <p><strong>Data de Vencimento Original:</strong> {{ $subscription->ends_at ? $subscription->ends_at->format('d/m/Y') : 'N/A' }}</p>

        @if($daysOverdue > 0)
        <p><strong>Tempo em Atraso:</strong> <span style="color: #dc2626; font-weight: bold;">{{ $daysOverdue }} dias</span></p>
        @endif

        <p><strong>Período de Carência:</strong> Até {{ $gracePeriodEnd->format('d/m/Y') }}</p>

        <p style="color: #dc2626; font-weight: bold;">
            ⚠️ IMPORTANTE: Após o período de carência, os dados serão permanentemente removidos.
        </p>
    </div>

    <!-- Timeline Section -->
    <div class="timeline-section">
        <h3>📅 CRONOGRAMA DE AÇÕES</h3>
        <table class="timeline-table">
            <thead>
                <tr>
                    <th>Etapa</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Descrição</th>
                </tr>
            </thead>
            <tbody>
                <tr class="past">
                    <td><strong>1. Vencimento</strong></td>
                    <td>{{ $subscription->ends_at ? $subscription->ends_at->format('d/m/Y') : 'N/A' }}</td>
                    <td style="color: #dc2626;">❌ Vencido</td>
                    <td>Prazo de pagamento expirado</td>
                </tr>
                <tr class="past">
                    <td><strong>2. Suspensão</strong></td>
                    <td>{{ $suspensionDate->format('d/m/Y') }}</td>
                    <td style="color: #dc2626;">🚫 Executado</td>
                    <td>Serviço suspenso temporariamente</td>
                </tr>
                <tr class="current">
                    <td><strong>3. Carência</strong></td>
                    <td>{{ $suspensionDate->format('d/m/Y') }} - {{ $gracePeriodEnd->format('d/m/Y') }}</td>
                    <td style="color: #f59e0b;">⏳ Em Curso</td>
                    <td>Período para regularização</td>
                </tr>
                <tr class="future">
                    <td><strong>4. Cancelamento</strong></td>
                    <td>Após {{ $gracePeriodEnd->format('d/m/Y') }}</td>
                    <td style="color: #6b7280;">⚠️ Pendente</td>
                    <td>Remoção permanente dos dados</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Payment Section -->
    <div class="payment-section">
        <h3>💰 INFORMAÇÕES PARA REGULARIZAÇÃO</h3>

        <div class="amount-box">
            <div class="amount">MT {{ number_format($amountDue, 2) }}</div>
            <p style="margin: 5px 0 0 0;">Valor Total para Reativação</p>
        </div>

        <h4>📋 Dados Bancários:</h4>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px; background: #f8f9fa; font-weight: bold;">Banco:</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $company['bank_name'] }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px; background: #f8f9fa; font-weight: bold;">Número da Conta:</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $company['bank_account'] }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px; background: #f8f9fa; font-weight: bold;">NIB:</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $company['bank_nib'] }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px; background: #f8f9fa; font-weight: bold;">Beneficiário:</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $company['name'] }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px; background: #f8f9fa; font-weight: bold;">Referência:</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $subscription->domain }} - {{ $client->name }}</td>
            </tr>
        </table>
    </div>

    <!-- Steps Section -->
    <div class="steps-section">
        <h3>🔧 PASSOS PARA REATIVAÇÃO</h3>
        <ol>
            @foreach($reactivationSteps as $step)
                <li>{{ $step }}</li>
            @endforeach
        </ol>

        <p style="margin-top: 15px; font-weight: 600; color: #059669;">
            ⏱️ <strong>Tempo de Reativação:</strong> Até 2 horas úteis após confirmação do pagamento
        </p>
    </div>

    <!-- Contact Section -->
    <div class="contact-section">
        <h3>📞 CONTACTOS PARA SUPORTE</h3>
        <div class="contact-grid">
            <div class="contact-item">
                <strong>Telefone</strong><br>
                {{ $company['phone'] }}
            </div>
            <div class="contact-item">
                <strong>Email Comercial</strong><br>
                {{ $company['email'] }}
            </div>
            <div class="contact-item">
                <strong>Suporte Técnico</strong><br>
                {{ $company['support_email'] }}
            </div>
            <div class="contact-item">
                <strong>WhatsApp</strong><br>
                {{ $company['whatsapp'] }}
            </div>
        </div>
    </div>

    <!-- Important Notes -->
    <div style="background: #fffbeb; border: 2px solid #f59e0b; padding: 15px; margin: 20px 0;">
        <h3 style="color: #92400e; margin-top: 0;">⚠️ INFORMAÇÕES CRÍTICAS</h3>
        <ul style="margin: 0; padding-left: 20px; color: #92400e;">
            <li><strong>Backup dos Dados:</strong> Os seus dados estão seguros durante o período de carência</li>
            <li><strong>Emails:</strong> Contas de email associadas também estão suspensas</li>
            <li><strong>Domínio:</strong> O domínio permanece registado durante a carência</li>
            <li><strong>Reativação:</strong> Após pagamento, tudo voltará ao normal automaticamente</li>
            <li><strong>Prazo Final:</strong> {{ $gracePeriodEnd->format('d/m/Y') }} - Não perca esta data!</li>
        </ul>
    </div>

    <!-- Legal Information -->
    <div style="background: #f3f4f6; padding: 15px; margin: 20px 0; font-size: 11px;">
        <h3 style="margin-top: 0;">📋 BASE LEGAL</h3>
        <p>Esta suspensão está em conformidade com os Termos de Serviço aceitos no momento da contratação, especificamente as cláusulas relacionadas ao pagamento e suspensão por inadimplência.</p>
        <p><strong>Referência do Contrato:</strong> Subscrição #{{ $subscription->id }} - {{ $subscription->created_at->format('d/m/Y') }}</p>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                Ciência do Cliente
            </div>
            <p style="font-size: 10px; margin: 5px 0;">Data: ___/___/___</p>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                {{ $company['name'] }}
            </div>
            <p style="font-size: 10px; margin: 5px 0;">{{ $suspensionDate->format('d/m/Y') }}</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>{{ $company['name'] }}</strong> - {{ $company['slogan'] }}</p>
        <p>NUIT: {{ $company['nuit'] }} | {{ $company['address_maputo'] }} | {{ $company['address_beira'] }}</p>
        <p>Documento emitido automaticamente em {{ now()->format('d/m/Y H:i:s') }}</p>
        <p><strong>Este é um documento oficial. Guarde-o para os seus registos.</strong></p>
    </div>

    <!-- Watermark -->
    <div class="watermark">SUSPENSO</div>

    <!-- Page Number -->
    <div class="page-number">Página 1 de 1</div>
</body>
</html>