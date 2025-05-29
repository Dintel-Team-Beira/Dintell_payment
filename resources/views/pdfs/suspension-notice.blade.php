<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Aviso de Suspensão {{ $noticeNumber }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: A4;
            margin: 15mm; /* Margens padrão para A4 */
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
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 5px;
        }

        .company-slogan {
            font-size: 11px;
            color: #666;
            margin-bottom: 10px;
        }

        .company-contact {
            font-size: 11px;
            color: #666;
        }

        .document-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #1a365d;
            margin: 20px 0 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
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
            border: 1px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .suspension-details h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .suspension-details p {
            margin: 5px 0;
            font-size: 12px;
        }

        .timeline-section {
            margin: 20px 0;
        }

        .timeline-section h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .timeline-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .timeline-table th,
        .timeline-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }

        .timeline-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #555;
        }

        .timeline-table .past {
            color: #dc3545;
        }

        .timeline-table .current {
            background: #d1ecf1;
        }

        .timeline-table .future {
            color: #6b7280;
        }

        .payment-section {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            page-break-before: always; /* Força quebra para a segunda página */
        }

        .payment-section h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .amount-box {
            text-align: center;
            padding: 15px;
            margin: 15px 0;
            background: #f8f9fa;
            border: 1px solid #1a365d;
            border-radius: 5px;
        }

        .amount-box .amount {
            font-size: 20px;
            font-weight: bold;
            color: #1a365d;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
        }

        .payment-table td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 11px;
        }

        .payment-table td:first-child {
            background: #f8f9fa;
            font-weight: bold;
            width: 40%;
        }

        .steps-section {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .steps-section h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .steps-section ol {
            margin: 10px 0;
            padding-left: 15px;
            font-size: 12px;
        }

        .steps-section li {
            margin: 8px 0;
        }

        .contact-section {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .contact-section h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .contact-list {
            margin: 10px 0;
        }

        .contact-item {
            margin-bottom: 8px;
            font-size: 12px;
        }

        .contact-item strong {
            color: #1a365d;
        }

        .critical-info {
            background: #f8f9fa;
            border: 1px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .critical-info h3 {
            color: #dc3545;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .critical-info ul {
            margin: 0;
            padding-left: 15px;
            font-size: 12px;
            color: #333;
        }

        .legal-info {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-size: 11px;
        }

        .legal-info h3 {
            color: #1a365d;
            font-size: 12px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 11px;
        }

        .footer {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(220, 38, 38, 0.08);
            z-index: -1;
        }

        .page-number {
            position: fixed;
            bottom: 15mm;
            right: 15mm;
            font-size: 10px;
            color: #666;
        }

        /* Evitar quebras indesejadas dentro das seções */
        .header, .document-title, .info-list, .suspension-details, .timeline-section, .payment-section, .steps-section, .contact-section, .critical-info, .legal-info, .signature-section, .footer {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <!-- Página 1 -->
    <div class="header">
        <div class="company-name">{{ $company['name'] }}</div>
        <div class="company-slogan">{{ $company['slogan'] }}</div>
        <div class="company-contact">
            NUIT: {{ $company['nuit'] }} | {{ $company['phone'] }} | {{ $company['email'] }}<br>
            {{ $company['address_maputo'] }}<br>
            {{ $company['address_beira'] }}
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
            <p style="color: #dc3545; font-weight: bold;">SUSPENSO</p>
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
        <p><strong>Motivo da Suspensão:</strong> {{ $suspensionReason }}</p>
        @endif
        <p><strong>Data de Vencimento Original:</strong> <span style="color: #dc3545; font-weight: bold;">{{ $subscription->ends_at ? $subscription->ends_at->format('d/m/Y') : 'N/A' }}</span></p>
        @if($daysOverdue > 0)
        <p><strong>Tempo em Atraso:</strong> <span style="color: #dc3545; font-weight: bold;">{{ $daysOverdue }} dias</span></p>
        @endif
        <p><strong>Período de Carência:</strong> Até {{ $gracePeriodEnd->format('d/m/Y') }}</p>
        <p style="color: #dc3545; font-weight: bold;">Após o período de carência, os dados serão permanentemente removidos.</p>
    </div>

    <div class="timeline-section">
        <h3>Cronograma de Ações</h3>
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
                    <td>Vencimento</td>
                    <td>{{ $subscription->ends_at ? $subscription->ends_at->format('d/m/Y') : 'N/A' }}</td>
                    <td>Vencido</td>
                    <td>Prazo de pagamento expirado</td>
                </tr>
                <tr class="past">
                    <td>Suspensão</td>
                    <td>{{ $suspensionDate->format('d/m/Y') }}</td>
                    <td>Executado</td>
                    <td>Serviço suspenso temporariamente</td>
                </tr>
                <tr class="current">
                    <td>Carência</td>
                    <td>{{ $suspensionDate->format('d/m/Y') }} - {{ $gracePeriodEnd->format('d/m/Y') }}</td>
                    <td>Em Curso</td>
                    <td>Período para regularização</td>
                </tr>
                <tr class="future">
                    <td>Cancelamento</td>
                    <td>Após {{ $gracePeriodEnd->format('d/m/Y') }}</td>
                    <td>Pendente</td>
                    <td>Remoção permanente dos dados</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Página 2 -->
    <div class="payment-section">
        <h3>Informações para Regularização</h3>
        <div class="amount-box">
            <div class="amount">MT {{ number_format($amountDue, 2) }}</div>
            <p>Valor Total para Reativação</p>
        </div>
        <table class="payment-table">
            <tr>
                <td>Banco:</td>
                <td>{{ $company['bank_name'] }}</td>
            </tr>
            <tr>
                <td>Número da Conta:</td>
                <td>{{ $company['bank_account'] }}</td>
            </tr>
            <tr>
                <td>NIB:</td>
                <td>{{ $company['bank_nib'] }}</td>
            </tr>
            <tr>
                <td>Beneficiário:</td>
                <td>{{ $company['name'] }}</td>
            </tr>
            <tr>
                <td>Referência:</td>
                <td>{{ $subscription->domain }} - {{ $client->name }}</td>
            </tr>
        </table>
    </div>

    <div class="steps-section">
        <h3>Passos para Reativação</h3>
        <ol>
            @foreach($reactivationSteps as $step)
                <li>{{ $step }}</li>
            @endforeach
        </ol>
        <p style="margin-top: 15px; font-weight: 600; color: #1a365d;">
            Tempo de Reativação: Até 2 horas úteis após confirmação do pagamento
        </p>
    </div>

    <div class="contact-section">
        <h3>Contactos para Suporte</h3>
        <div class="contact-list">
            <div class="contact-item">
                <strong>Telefone:</strong> {{ $company['phone'] }}
            </div>
            <div class="contact-item">
                <strong>Email Comercial:</strong> {{ $company['email'] }}
            </div>
            <div class="contact-item">
                <strong>Suporte Técnico:</strong> {{ $company['support_email'] }}
            </div>
            <div class="contact-item">
                <strong>WhatsApp:</strong> {{ $company['whatsapp'] }}
            </div>
        </div>
    </div>

    <div class="critical-info">
        <h3>Informações Críticas</h3>
        <ul>
            <li><strong>Backup dos Dados:</strong> Os seus dados estão seguros durante o período de carência.</li>
            <li><strong>Emails:</strong> Contas de email associadas também estão suspensas.</li>
            <li><strong>Domínio:</strong> O domínio permanece registado durante a carência.</li>
            <li><strong>Reativação:</strong> Após pagamento, tudo voltará ao normal automaticamente.</li>
            <li><strong>Prazo Final:</strong> <span style="color: #dc3545; font-weight: bold;">{{ $gracePeriodEnd->format('d/m/Y') }}</span> - Não perca esta data!</li>
        </ul>
    </div>

    <div class="legal-info">
        <h3>Base Legal</h3>
        <p>Esta suspensão está em conformidade com os Termos de Serviço aceitos no momento da contratação, especificamente as cláusulas relacionadas ao pagamento e suspensão por inadimplência.</p>
        <p><strong>Referência do Contrato:</strong> Subscrição #{{ $subscription->id }} - {{ $subscription->created_at->format('d/m/Y') }}</p>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">Ciência do Cliente</div>
            <p>Data: ___/___/___</p>
        </div>
        <div class="signature-box">
            <div class="signature-line">{{ $company['name'] }}</div>
            <p>{{ $suspensionDate->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="footer">
        <p><strong>{{ $company['name'] }}</strong> - {{ $company['slogan'] }}</p>
        <p>NUIT: {{ $company['nuit'] }} | {{ $company['address_maputo'] }} | {{ $company['address_beira'] }}</p>
        <p>Documento emitido automaticamente em {{ now()->format('d/m/Y H:i:s') }}</p>
        <p><strong>Este é um documento oficial. Guarde-o para os seus registos.</strong></p>
    </div>

    <div class="watermark">SUSPENSO</div>

    <div class="page-number">Página <span class="page-current"></span> de 2</div>
</body>
</html>