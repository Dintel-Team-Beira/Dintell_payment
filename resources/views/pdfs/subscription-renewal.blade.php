<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprovativo de Renovação {{ $renewalNumber }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #7c3aed; padding-bottom: 20px; }
        .company-name { font-size: 28px; font-weight: bold; color: #7c3aed; margin-bottom: 5px; }
        .document-title { font-size: 22px; font-weight: bold; color: #7c3aed; margin: 20px 0; }
        .renewal-badge { background: #7c3aed; color: white; padding: 8px 16px; border-radius: 20px; font-size: 14px; display: inline-block; margin: 10px 0; }
        .info-grid { display: table; width: 100%; margin: 20px 0; }
        .info-left, .info-right { display: table-cell; width: 50%; vertical-align: top; padding: 0 15px; }
        .info-box { background: #f8fafc; border-left: 4px solid #7c3aed; padding: 15px; margin-bottom: 15px; }
        .info-box h4 { margin: 0 0 8px 0; color: #374151; font-size: 14px; text-transform: uppercase; }
        .info-box p { margin: 0; font-size: 16px; font-weight: 600; color: #111827; }
        .timeline { background: #faf5ff; border: 2px solid #a855f7; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .timeline h3 { color: #7c3aed; margin-top: 0; }
        .timeline-item { display: flex; align-items: center; margin: 15px 0; }
        .timeline-icon { width: 30px; height: 30px; border-radius: 50%; background: #7c3aed; color: white; display: flex; align-items: center; justify-content: center; margin-right: 15px; font-weight: bold; }
        .timeline-content { flex: 1; }
        .amount-highlight { background: #f0fdf4; border: 2px solid #10b981; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0; }
        .amount-highlight h2 { color: #059669; margin: 0; font-size: 24px; }
        .service-details { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .service-details th, .service-details td { border: 1px solid #d1d5db; padding: 12px; text-align: left; }
        .service-details th { background: #f9fafb; font-weight: bold; }
        .footer { margin-top: 40px; border-top: 2px solid #e5e7eb; padding-top: 20px; text-align: center; font-size: 11px; color: #6b7280; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ $company['name'] }}</div>
        <div style="font-size: 12px; color: #666; margin-bottom: 10px;">{{ $company['slogan'] }}</div>
        <div style="font-size: 11px;">
            NUIT: {{ $company['nuit'] }} | {{ $company['address_maputo'] }}<br>
            {{ $company['phone'] }} | {{ $company['email'] }}
        </div>
    </div>

    <!-- Document Title -->
    <div class="document-title">
        🔄 COMPROVATIVO DE RENOVAÇÃO
        <div class="renewal-badge">{{ $renewalNumber }}</div>
    </div>

    <!-- Client and Service Info -->
    <div class="info-grid">
        <div class="info-left">
            <div class="info-box">
                <h4>Cliente</h4>
                <p>{{ $client->name }}</p>
            </div>
            <div class="info-box">
                <h4>Email</h4>
                <p>{{ $client->email }}</p>
            </div>
            <div class="info-box">
                <h4>Telefone</h4>
                <p>{{ $client->phone ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="info-right">
            <div class="info-box">
                <h4>Domínio</h4>
                <p>{{ $subscription->domain }}</p>
            </div>
            <div class="info-box">
                <h4>Plano</h4>
                <p>{{ $plan->name }}</p>
            </div>
            <div class="info-box">
                <h4>Data da Renovação</h4>
                <p>{{ $renewalDate->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="timeline">
        <h3>📅 Cronologia da Renovação</h3>

        <div class="timeline-item">
            <div class="timeline-icon">1</div>
            <div class="timeline-content">
                <strong>Expiração Anterior:</strong> {{ $oldExpiryDate ? $oldExpiryDate->format('d/m/Y') : 'Serviço Expirado' }}<br>
                <small style="color: #ef4444;">O serviço estava prestes a expirar</small>
            </div>
        </div>

        <div class="timeline-item">
            <div class="timeline-icon">2</div>
            <div class="timeline-content">
                <strong>Pagamento Processado:</strong> {{ $renewalDate->format('d/m/Y H:i') }}<br>
                <small style="color: #059669;">Valor: MT {{ number_format($amount, 2) }}</small>
            </div>
        </div>

        <div class="timeline-item">
            <div class="timeline-icon">3</div>
            <div class="timeline-content">
                <strong>Nova Expiração:</strong> {{ $newExpiryDate->format('d/m/Y') }}<br>
                <small style="color: #10b981;">Serviço estendido por {{ $subscription->plan->billing_cycle_days }} dias</small>
            </div>
        </div>
    </div>

    <!-- Amount Highlight -->
    <div class="amount-highlight">
        <h2>MT {{ number_format($amount, 2) }}</h2>
        <p style="margin: 5px 0 0 0; color: #374151;">Valor da Renovação</p>
    </div>

    <!-- Service Details -->
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
            <tr style="background: #f0fdf4; font-weight: bold;">
                <td colspan="2" style="text-align: right; font-size: 14px;">Total Pago:</td>
                <td style="font-size: 14px;">MT {{ number_format($amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Status Information -->
    <div style="background: #dbeafe; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <h3 style="color: #1e40af; margin-top: 0;">📊 Status do Serviço</h3>
        <p style="margin: 5px 0;"><strong>Website:</strong> {{ $subscription->domain }} - <span style="color: #059669;">🟢 Ativo</span></p>
        <p style="margin: 5px 0;"><strong>Plano:</strong> {{ $plan->name }}</p>
        <p style="margin: 5px 0;"><strong>Recursos:</strong> {{ $plan->max_storage_gb }}GB Storage, {{ $plan->max_bandwidth_gb }}GB Bandwidth</p>
        <p style="margin: 5px 0;"><strong>Próxima Renovação:</strong> {{ $newExpiryDate->format('d/m/Y') }}</p>
    </div>

    <!-- Important Notes -->
    <div style="background: #fef3c7; border: 1px solid #f59e0b; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <h4 style="color: #92400e; margin-top: 0;">⚠️ Informações Importantes</h4>
        <ul style="margin: 0; padding-left: 20px; color: #92400e;">
            <li>Este comprovativo confirma a renovação do seu serviço</li>
            <li>O seu website continuará funcionando normalmente</li>
            <li>Receberá lembrete antes da próxima renovação</li>
            <li>Guarde este documento para seus registos</li>
        </ul>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>{{ $company['name'] }}</strong> - {{ $company['slogan'] }}</p>
        <p>Documento gerado automaticamente em {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Válido sem assinatura • Para suporte: {{ $company['email'] }} • {{ $company['phone'] }}</p>
    </div>
</body>
</html>