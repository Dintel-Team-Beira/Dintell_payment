<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Comprovativo de Renovação {{ $renewalNumber }}</title>
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

        .timeline {
            margin: 20px 0;
            padding: 15px;
            border-left: 3px solid #1a365d;
            background: #f8f9fa;
            border-radius: 3px;
        }

        .timeline h3 {
            color: #1a365d;
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
            color: #dc3545; /* Vermelho para expiração, azul para outros */
        }

        .timeline-item.payment small {
            color: #1a365d;
        }

        .timeline-item.new-expiry small {
            color: #1a365d;
        }

        .amount-highlight {
            text-align: center;
            padding: 15px;
            margin: 20px 0;
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            border-radius: 5px;
        }

        .amount-highlight h2 {
            color: #1a365d;
            font-size: 20px;
            margin: 0;
            font-weight: 600;
        }

        .amount-highlight p {
            color: #374151;
            margin: 5px 0 0 0;
            font-size: 12px;
        }

        .service-details {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            page-break-before: always; /* Força quebra para a segunda página */
        }

        .service-details th,
        .service-details td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }

        .service-details th {
            background: #f8f9fa;
            font-weight: bold;
            color: #555;
        }

        .service-details .total-row {
            background: #1a365d;
            color: white;
            font-weight: bold;
            font-size: 13px;
        }

        .status-info {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .status-info h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .status-info p {
            margin: 5px 0;
            font-size: 12px;
        }

        .important-notes {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .important-notes h4 {
            color: #1a365d;
            font-size: 12px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .important-notes ul {
            margin: 0;
            padding-left: 15px;
            font-size: 12px;
            color: #333;
        }

        .footer {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
        }

        /* Evitar quebras indesejadas dentro das seções */
        .header, .document-title, .info-list, .timeline, .amount-highlight, .service-details, .status-info, .important-notes, .footer {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <!-- Página 1 -->
    <div class="header">
        <image src="https://beyondbusiness.co.mz/logo.png" alt="DINTELL Logo" style="width: 150px; height: auto;">
        <div class="company-contact">
            NUIT: {{ $company['nuit'] }} | {{ $company['address_maputo'] }}<br>
            {{ $company['phone'] }} | {{ $company['email'] }}
        </div>
    </div>

    <div class="document-title">Comprovativo de Renovação {{ $renewalNumber }}</div>

    <div class="info-list">
        <div class="info-item">
            <h4>Cliente</h4>
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
        <div class="info-item">
            <h4>Domínio</h4>
            <p>{{ $subscription->domain }}</p>
        </div>
        <div class="info-item">
            <h4>Plano</h4>
            <p>{{ $plan->name }}</p>
        </div>
        <div class="info-item">
            <h4>Data da Renovação</h4>
            <p>{{ $renewalDate->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="timeline">
        <h3>Cronologia da Renovação</h3>
        <div class="timeline-item">
            <strong>Expiração Anterior:</strong> {{ $oldExpiryDate ? $oldExpiryDate->format('d/m/Y') : 'Serviço Expirado' }}<br>
            <small style="color: #dc3545;">O serviço estava prestes a expirar</small>
        </div>
        <div class="timeline-item payment">
            <strong>Pagamento Processado:</strong> {{ $renewalDate->format('d/m/Y H:i') }}<br>
            <small>Valor: MT {{ number_format($amount, 2) }}</small>
        </div>
        <div class="timeline-item new-expiry">
            <strong>Nova Expiração:</strong> {{ $newExpiryDate->format('d/m/Y') }}<br>
            <small>Serviço estendido por {{ $subscription->plan->billing_cycle_days }} dias</small>
        </div>
    </div>

    <div class="amount-highlight">
        <h2>MT {{ number_format($amount, 2) }}</h2>
        <p>Valor da Renovação</p>
    </div>

    <!-- Página 2 -->
    <table class="service-details">
        <thead>
            <tr>
                <th>Descrição</th>
                <th>Período</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $plan->name }} - Renovação</td>
                <td>{{ $renewalDate->format('d/m/Y') }} até {{ $newExpiryDate->format('d/m/Y') }}</td>
                <td>MT {{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right; font-weight: bold;">Subtotal:</td>
                <td>MT {{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right; font-weight: bold;">IVA ({{ $iva_rate }}%):</td>
                <td>MT {{ number_format($iva_amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="2" style="text-align: right;">Total Pago:</td>
                <td>MT {{ number_format($amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="status-info">
        <h3>Status do Serviço</h3>
        <p><strong>Website:</strong> {{ $subscription->domain }} - Ativo</p>
        <p><strong>Plano:</strong> {{ $plan->name }}</p>
        <p><strong>Recursos:</strong> {{ $plan->max_storage_gb }}GB Storage, {{ $plan->max_bandwidth_gb }}GB Bandwidth</p>
        <p><strong>Próxima Renovação:</strong> {{ $newExpiryDate->format('d/m/Y') }}</p>
    </div>

    <div class="important-notes">
        <h4>Informações Importantes</h4>
        <ul>
            <li>Este comprovativo confirma a renovação do seu serviço.</li>
            <li>O seu website continuará funcionando normalmente.</li>
            <li>Receberá um lembrete antes da próxima renovação.</li>
            <li>Guarde este documento para seus registos.</li>
        </ul>
    </div>

    <div class="footer">
        <p><strong>{{ $company['name'] }}</strong> - {{ $company['slogan'] }}</p>
        <p>Documento gerado automaticamente em {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Válido sem assinatura • Para suporte: {{ $company['email'] }} • {{ $company['phone'] }}</p>
    </div>
</body>
</html>