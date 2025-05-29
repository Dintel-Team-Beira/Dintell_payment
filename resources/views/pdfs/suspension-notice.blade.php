<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Aviso de Suspensão {{ $noticeNumber }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 5px;
        }

        .company-slogan {
            font-size: 11px;
            color: #666;
            margin-bottom: 8px;
        }

        .company-contact {
            font-size: 11px;
            color: #666;
        }

        .document-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #1a365d;
            margin: 15px 0;
        }

        .info-list {
            margin: 15px 0;
        }

        .info-item {
            border-left: 3px solid #1a365d;
            padding: 8px 12px;
            margin-bottom: 8px;
            background: #f8f9fa;
            border-radius: 3px;
        }

        .info-item h4 {
            margin: 0 0 4px 0;
            font-size: 11px;
            color: #374151;
            font-weight: 600;
        }

        .info-item p {
            margin: 0;
            font-size: 12px;
            color: #1a365d;
            font-weight: 500;
        }

        .suspension-details {
            background: #f8f9fa;
            border: 1px solid #dc3545;
            padding: 10px;
            margin: 15px 0;
            border-radius: 5px;
        }

        .suspension-details h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 8px 0;
            font-weight: 600;
        }

        .suspension-details p {
            margin: 4px 0;
            font-size: 12px;
        }

        .timeline-list {
            margin: 15px 0;
            background: #f8f9fa;
            border: 1px solid #dc3545;
            padding: 10px;
            border-radius: 5px;
        }

        .timeline-list h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 8px 0;
            font-weight: 600;
        }

        .timeline-item {
            margin: 8px 0;
        }

        .timeline-item strong {
            color: #1a365d;
            font-weight: 600;
        }

        .timeline-item .past,
        .timeline-item .current {
            color: #dc3545;
            font-weight: bold;
        }

        .payment-section {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 10px;
            margin: 15px 0;
            border-radius: 5px;
        }

        .payment-section h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 8px 0;
            font-weight: 600;
        }

        .amount-box {
            text-align: center;
            padding: 10px;
            margin: 10px 0;
            background: #f8f9fa;
            border: 1px solid #1a365d;
            border-radius: 5px;
        }

        .amount-box .amount {
            font-size: 16px;
            font-weight: bold;
            color: #1a365d;
        }

        .payment-list {
            margin: 10px 0;
        }

        .payment-item {
            border-left: 3px solid #1a365d;
            padding: 8px 12px;
            margin-bottom: 8px;
            background: #f8f9fa;
            border-radius: 3px;
        }

        .payment-item h4 {
            margin: 0 0 4px 0;
            font-size: 11px;
            color: #374151;
            font-weight: 600;
        }

        .payment-item p {
            margin: 0;
            font-size: 12px;
            color: #1a365d;
            font-weight: 500;
        }

        .steps-section {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 10px;
            margin: 15px 0;
            border-radius: 5px;
        }

        .steps-section h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 8px 0;
            font-weight: 600;
        }

        .steps-section ol {
            margin: 8px 0;
            padding-left: 15px;
            font-size: 12px;
        }

        .steps-section li {
            margin: 6px 0;
        }

        .contact-section {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 10px;
            margin: 15px 0;
            border-radius: 5px;
        }

        .contact-section h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 8px 0;
            font-weight: 600;
        }

        .contact-list {
            margin: 8px 0;
        }

        .contact-item {
            margin-bottom: 6px;
            font-size: 12px;
        }

        .contact-item strong {
            color: #1a365d;
        }

        .legal-info {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px;
            margin: 15px 0;
            border-radius: 5px;
            font-size: 11px;
        }

        .legal-info h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 8px 0;
            font-weight: 600;
        }

        .legal-info ul {
            margin: 8px 0;
            padding-left: 15px;
            font-size: 11px;
        }

        .legal-info li {
            margin: 6px 0;
            color: #dc3545;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
        }

        .header, .document-title, .info-list, .suspension-details, .timeline-list, .payment-section, .steps-section, .contact-section, .legal-info, .footer {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="header">
        <image src="https://beyondbusiness.co.mz/logo.png" alt="DINTELL Logo" style="width: 150px; height: auto;">
        <div class="company-contact">
            NUIT: {{ $company['nuit'] }} | {{ $company['phone'] }} | {{ $company['email'] }}<br>
            {{ $company['address_maputo'] }} | {{ $company['address_beira'] }}
        </div>
    </div>

    <div class="document-title">Aviso de Suspensão {{ $noticeNumber }}</div>

    <div class="info-list">
        <div class="info-item">
            <h4>Nome do Cliente</h4>
            <p>{{ $client->name }}</p>
        </div>
        <div class="info-item">
            <h4>Email</h4>
            <p>{{ $client->email }}</p>
        </div>
        <div class="info-item">
            <h4>Telefone</h4>
            <p>{{ $client->phone ?? 'N/A' }}</p>
        </div>
        @if($client->nuit)
        <div class="info-item">
            <h4>NUIT</h4>
            <p>{{ $client->nuit }}</p>
        </div>
        @endif
        <div class="info-item">
            <h4>Cliente Nº</h4>
            <p>{{ str_pad($client->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div class="info-item">
            <h4>Domínio</h4>
            <p>{{ $subscription->domain }}</p>
        </div>
        <div class="info-item">
            <h4>Plano</h4>
            <p>{{ $plan->name }}</p>
        </div>
        <div class="info-item">
            <h4>Status</h4>
            <p style="color: #dc3545; font-weight: bold;">Suspenso</p>
        </div>
        <div class="info-item">
            <h4>Data da Suspensão</h4>
            <p>{{ $suspensionDate->format('d/m/Y H:i') }}</p>
        </div>
        <div class="info-item">
            <h4>ID da Subscrição</h4>
            <p>{{ $subscription->id }}</p>
        </div>
    </div>

    <div class="suspension-details">
        <h3>Detalhes da Suspensão</h3>
        @if($suspensionReason)
        <p><strong>Motivo:</strong> {{ $suspensionReason }}</p>
        @endif
        <p><strong>Data de Vencimento:</strong> <span style="color: #dc3545; font-weight: bold;">{{ $subscription->ends_at ? $subscription->ends_at->format('d/m/Y') : 'N/A' }}</span></p>
        @if($daysOverdue > 0)
        <p><strong>Tempo em Atraso:</strong> <span style="color: #dc3545; font-weight: bold;">{{ $daysOverdue }} dias</span></p>
        @endif
        <p><strong>Período de Carência:</strong> Até <span style="color: #dc3545; font-weight: bold;">{{ $gracePeriodEnd->format('d/m/Y') }}</span></p>
    </div>

    <div class="timeline-list">
        <h3>Cronograma</h3>
        <div class="timeline-item past">
            <strong>Vencimento:</strong> {{ $subscription->ends_at ? $subscription->ends_at->format('d/m/Y') : 'N/A' }} - Prazo de pagamento expirado
        </div>
        <div class="timeline-item past">
            <strong>Suspensão:</strong> {{ $suspensionDate->format('d/m/Y') }} - Serviço suspenso
        </div>
        <div class="timeline-item current">
            <strong>Carência:</strong> Até {{ $gracePeriodEnd->format('d/m/Y') }} - Período para regularização
        </div>
        <div class="timeline-item">
            <strong>Após:</strong> Após {{ $gracePeriodEnd->format('d/m/Y') }} - Remoção de dados
        </div>
    </div>

    <div class="payment-section">
        <h3>Regularização</h3>
        <div class="amount-box">
            <div class="amount">MT {{ number_format($amountDue, 2) }}</div>
            <p>Valor para Reativação</p>
        </div>
        <div class="payment-list">
            <div class="payment-item">
                <h4>Banco</h4>
                <p>{{ $company['bank_name'] }}</p>
            </div>
            <div class="payment-item">
                <h4>Número da Conta</h4>
                <p>{{ $company['bank_account'] }}</p>
            </div>
            <div class="payment-item">
                <h4>NIB</h4>
                <p>{{ $company['bank_nib'] }}</p>
            </div>
            <div class="payment-item">
                <h4>Beneficiário</h4>
                <p>{{ $company['name'] }}</p>
            </div>
            <div class="payment-item">
                <h4>Referência</h4>
                <p>{{ $subscription->domain }} - {{ $client->id }}</p>
            </div>
        </div>
    </div>

    <div class="steps-section">
        <h3>Passos para Reativação</h3>
        <ol>
            @foreach($reactivationSteps as $step)
                <li>{{ $step }}</li>
            @endforeach
        </ol>
        <p style="font-weight: 600; color: #1a365d;">
            Reativação em até 2 horas após pagamento.
        </p>
    </div>

    <div class="contact-section">
        <h3>Contactos</h3>
        <div class="contact-list">
            <div class="contact-item"><strong>Telefone:</strong> {{ $company['phone'] }}</div>
            <div class="contact-item"><strong>Email:</strong> {{ $company['email'] }}</div>
            <div class="contact-item"><strong>Suporte:</strong> {{ $company['support_email'] }}</div>
            <div class="contact-item"><strong>WhatsApp:</strong> {{ $company['whatsapp'] }}</div>
        </div>
    </div>

    <div class="legal-info">
        <h3>Informações Críticas</h3>
        <p>Esta suspensão está conforme os Termos de Serviço, cláusula de inadimplência. Contrato: Subscrição #{{ $subscription Authority id }}-{{ $subscription->created_at->format('d/m/Y') }}.</p>
        <ul>
            <li style="color: #dc3545;">Dados seguros até {{ $gracePeriodEnd->format('d/m/Y') }}.</li>
            <li style="color: #dc3545;">Emails e domínio suspensos.</li>
            <li style="color: #dc3545;">Após {{ $gracePeriodEnd->format('d/m/Y') }}, dados serão removidos.</li>
        </ul>
    </div>

    <div class="footer">
        <p>{{ $company['name'] }} - {{ $company['slogan'] }} | Emitido em {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>