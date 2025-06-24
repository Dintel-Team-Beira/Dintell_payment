<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Comprovativo de Renovação {{ $renewalNumber }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            color: #333;
            margin: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #1a365d;
        }

        .logo {
            margin-bottom: 5px;
        }

        .logo img {
            max-width: 80px;
            max-height: 50px;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 2px;
        }

        .company-slogan {
            font-size: 9px;
            color: #666;
            font-style: italic;
            margin-bottom: 5px;
        }

        .company-contact {
            font-size: 9px;
            color: #666;
        }

        .document-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: white;
            background-color: #1a365d;
            padding: 6px;
            margin: 10px 0;
        }

        .main-content {
            overflow: hidden;
        }

        .left-column {
            float: left;
            width: 50%;
            padding-right: 15px;
        }

        .right-column {
            float: right;
            width: 50%;
            padding-left: 15px;
        }

        .info-section {
            margin-bottom: 10px;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 5px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 2px;
        }

        .info-row {
            margin-bottom: 3px;
            overflow: hidden;
        }

        .info-label {
            float: left;
            width: 40%;
            font-weight: bold;
            font-size: 9px;
        }

        .info-value {
            float: right;
            width: 58%;
            font-size: 9px;
        }

        .amount-highlight {
            text-align: center;
            background-color: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 10px;
            margin: 10px 0;
            clear: both;
        }

        .amount-highlight .amount {
            font-size: 18px;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 3px;
        }

        .amount-highlight .label {
            font-size: 10px;
            color: #666;
        }

        .timeline {
            clear: both;
            margin: 10px 0;
            padding: 8px;
            background-color: #f8f9fa;
            border-left: 3px solid #1a365d;
        }

        .timeline-title {
            font-size: 10px;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 5px;
        }

        .timeline-item {
            margin-bottom: 4px;
            font-size: 9px;
        }

        .timeline-item .date {
            font-weight: bold;
            color: #1a365d;
        }

        .timeline-item .status {
            color: #666;
        }

        .service-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .service-table th,
        .service-table td {
            border: 1px solid #666;
            padding: 4px;
            font-size: 9px;
        }

        .service-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            background-color: #1a365d;
            color: white;
            font-weight: bold;
        }

        .status-box {
            background-color: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 6px;
            margin: 8px 0;
        }

        .status-title {
            font-size: 10px;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 4px;
        }

        .status-item {
            font-size: 9px;
            margin-bottom: 2px;
        }

        .notes {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 6px;
            margin: 8px 0;
        }

        .notes-title {
            font-size: 10px;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 4px;
        }

        .notes ul {
            margin: 0;
            padding-left: 12px;
            font-size: 9px;
        }

        .notes li {
            margin-bottom: 2px;
        }

        .footer {
            clear: both;
            margin-top: 15px;
            border-top: 1px solid #ddd;
            padding-top: 6px;
            text-align: center;
            font-size: 8px;
            color: #666;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('images/logo.png') }}" alt="Logo">
        </div>
        <div class="company-name">{{ $company['name'] }}</div>
        <div class="company-slogan">{{ $company['slogan'] }}</div>
        <div class="company-contact">
            NUIT: {{ $company['nuit'] }} | {{ $company['phone'] }} | {{ $company['email'] }}
        </div>
    </div>

    <div class="document-title">Comprovativo de Renovação {{ $renewalNumber }}</div>

    <div class="clearfix main-content">
        <div class="left-column">
            <div class="info-section">
                <div class="section-title">Dados do Cliente</div>
                <div class="clearfix info-row">
                    <div class="info-label">Nome:</div>
                    <div class="info-value">{{ $client->name }}</div>
                </div>
                <div class="clearfix info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $client->email }}</div>
                </div>
                <div class="clearfix info-row">
                    <div class="info-label">Telefone:</div>
                    <div class="info-value">{{ $client->phone ?? 'N/A' }}</div>
                </div>
                <div class="clearfix info-row">
                    <div class="info-label">Cliente ID:</div>
                    <div class="info-value">{{ str_pad($client->id, 10, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>

            <div class="info-section">
                <div class="section-title">Detalhes do Serviço</div>
                <div class="clearfix info-row">
                    <div class="info-label">Domínio:</div>
                    <div class="info-value">{{ $subscription->domain }}</div>
                </div>
                <div class="clearfix info-row">
                    <div class="info-label">Plano:</div>
                    <div class="info-value">{{ $plan->name }}</div>
                </div>
                <div class="clearfix info-row">
                    <div class="info-label">Data Renovação:</div>
                    <div class="info-value">{{ $renewalDate->format('d/m/Y H:i') }}</div>
                </div>
                <div class="clearfix info-row">
                    <div class="info-label">Tipo Pagamento:</div>
                    <div class="info-value">{{ $paymentMethod ?? 'Automático' }}</div>
                </div>
            </div>
        </div>

        <div class="right-column">
            <div class="timeline no-break">
                <div class="timeline-title">Cronologia da Renovação</div>
                <div class="timeline-item">
                    <span class="date">Expiração Anterior:</span><br>
                    <span class="status">{{ $oldExpiryDate ? $oldExpiryDate->format('d/m/Y') : 'Serviço Expirado' }}</span>
                </div>
                <div class="timeline-item">
                    <span class="date">Pagamento:</span><br>
                    <span class="status">{{ $renewalDate->format('d/m/Y H:i') }} - MT {{ number_format($amount, 2) }}</span>
                </div>
                <div class="timeline-item">
                    <span class="date">Nova Expiração:</span><br>
                    <span class="status">{{ $newExpiryDate->format('d/m/Y') }}</span>
                </div>
                <div class="timeline-item">
                    <span class="date">Período Estendido:</span><br>
                    <span class="status">{{ $plan->billing_cycle ?? '30' }} dias adicionais</span>
                </div>
            </div>
        </div>
    </div>

    <div class="amount-highlight no-break">
        <div class="amount">MT {{ number_format($amount, 2) }}</div>
        <div class="label">Valor da Renovação</div>
    </div>

    <table class="service-table no-break">
        <thead>
            <tr>
                <th style="width: 50%;">Descrição</th>
                <th style="width: 25%;">Período</th>
                <th style="width: 25%;">Valor</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $plan->name }} - Renovação {{ ucfirst($plan->billing_cycle ?? 'mensal') }}</td>
                <td>{{ $renewalDate->format('d/m/Y') }} até {{ $newExpiryDate->format('d/m/Y') }}</td>
                <td class="text-right">{{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" class="text-right"><strong>Subtotal:</strong></td>
                <td class="text-right">{{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" class="text-right"><strong>IVA ({{ $iva_rate ?? 16 }}%):</strong></td>
                <td class="text-right">{{ number_format($iva_amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="2" class="text-right">Total Pago:</td>
                <td class="text-right">{{ number_format($amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="clearfix main-content">
        <div class="left-column">
            <div class="status-box no-break">
                <div class="status-title">Status do Serviço</div>
                <div class="status-item"><strong>Website:</strong> {{ $subscription->domain }} - Ativo</div>
                <div class="status-item"><strong>Plano:</strong> {{ $plan->name }}</div>
                <div class="status-item"><strong>Recursos:</strong> {{ $plan->max_storage_gb ?? '10' }}GB Storage</div>
                <div class="status-item"><strong>Bandwidth:</strong> {{ $plan->max_bandwidth_gb ?? 'Ilimitado' }}</div>
                <div class="status-item"><strong>Próxima Renovação:</strong> {{ $newExpiryDate->format('d/m/Y') }}</div>
                <div class="status-item"><strong>Status:</strong> <span style="color: #28a745; font-weight: bold;">ATIVO</span></div>
            </div>
        </div>

        <div class="right-column">
            <div class="notes no-break">
                <div class="notes-title">Informações Importantes</div>
                <ul>
                    <li>Comprovativo de renovação do serviço</li>
                    <li>Website continuará funcionando normalmente</li>
                    <li>Lembrete antes da próxima renovação</li>
                    <li>Guarde este documento para registos</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer">
        <strong>{{ $company['name'] }}</strong> - {{ $company['slogan'] }}<br>
        Documento gerado em {{ now()->format('d/m/Y H:i:s') }} |
        Válido sem assinatura |
        Suporte: {{ $company['email'] }}
    </div>
</body>
</html>