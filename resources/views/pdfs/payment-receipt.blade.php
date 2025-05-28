<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo {{ $receiptNumber }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1e3a8a; padding-bottom: 20px; }
        .company-name { font-size: 28px; font-weight: bold; color: #1e3a8a; margin-bottom: 5px; }
        .company-slogan { font-size: 12px; color: #666; margin-bottom: 10px; }
        .document-title { font-size: 24px; font-weight: bold; color: #059669; margin: 20px 0; }
        .receipt-info { display: table; width: 100%; margin: 20px 0; }
        .left-info, .right-info { display: table-cell; width: 50%; vertical-align: top; padding: 0 10px; }
        .info-section { margin-bottom: 20px; }
        .info-section h3 { background: #f8f9fa; padding: 8px; margin: 0 0 10px 0; font-size: 14px; border-left: 4px solid #059669; }
        .amount-box { background: #d1fae5; border: 3px solid #059669; padding: 30px; text-align: center; margin: 30px 0; border-radius: 10px; }
        .amount-box .amount { font-size: 36px; font-weight: bold; color: #059669; margin: 0; }
        .amount-box .currency { font-size: 14px; color: #374151; margin-top: 5px; }
        .details-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .details-table td { padding: 12px; border-bottom: 1px solid #e5e7eb; }
        .details-table .label { font-weight: bold; background: #f8f9fa; width: 40%; }
        .footer { margin-top: 50px; border-top: 1px solid #ddd; padding-top: 20px; font-size: 10px; text-align: center; }
        .signature-section { margin-top: 60px; display: table; width: 100%; }
        .signature-box { display: table-cell; width: 50%; text-align: center; }
        .signature-line { border-top: 1px solid #000; margin-top: 50px; padding-top: 5px; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ $company['name'] }}</div>
        <div class="company-slogan">{{ $company['slogan'] }}</div>
        <div style="font-size: 11px; margin-top: 15px;">
            NUIT: {{ $company['nuit'] }} | {{ $company['phone'] }} | {{ $company['email'] }}<br>
            {{ $company['address_maputo'] }}
        </div>
    </div>

    <!-- Document Title -->
    <div class="document-title">
        💰 RECIBO DE PAGAMENTO
        <div style="font-size: 16px; color: #374151; margin-top: 5px;">
            Nº {{ $receiptNumber }}
        </div>
    </div>

    <!-- Amount Box -->
    <div class="amount-box">
        <div class="amount">MT {{ number_format($amount, 2) }}</div>
        <div class="currency">Meticais Moçambicanos</div>
    </div>

    <!-- Receipt Information -->
    <div class="receipt-info">
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
                <h3>📄 Dados do Pagamento</h3>
                <strong>Data:</strong> {{ $paymentDate->format('d/m/Y H:i') }}<br>
                <strong>Método:</strong> {{ $paymentMethod }}<br>
                @if($paymentReference)
                <strong>Referência:</strong> {{ $paymentReference }}<br>
                @endif
                <strong>Status:</strong> <span style="color: #059669;">✅ Confirmado</span>
            </div>
        </div>
    </div>

    <!-- Service Details -->
    <table class="details-table">
        <tr>
            <td class="label">Serviço</td>
            <td>{{ $plan->name }} - Subscrição de Website</td>
        </tr>
        <tr>
            <td class="label">Domínio</td>
            <td>{{ $subscription->domain }}</td>
        </tr>
        <tr>
            <td class="label">Período</td>
            <td>{{ $subscription->starts_at->format('d/m/Y') }} até {{ $subscription->ends_at->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Subtotal</td>
            <td>MT {{ number_format($subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="label">IVA (16%)</td>
            <td>MT {{ number_format($iva_amount, 2) }}</td>
        </tr>
        <tr style="background: #f0fdf4; font-weight: bold; font-size: 14px;">
            <td class="label">Total Pago</td>
            <td>MT {{ number_format($amount, 2) }}</td>
        </tr>
    </table>

    <!-- Additional Information -->
    <div style="background: #f0f9ff; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #1e40af;">ℹ️ Informações Adicionais</h3>
        <p style="margin: 5px 0;">• O serviço está ativo e funcionando normalmente</p>
        <p style="margin: 5px 0;">• Válido para efeitos fiscais e contabilísticos</p>
        <p style="margin: 5px 0;">• Em caso de dúvidas, contacte-nos através dos dados acima</p>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                Assinatura do Cliente
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                DINTELL, LDA
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>{{ $company['name'] }}</strong> - {{ $company['slogan'] }}</p>
        <p>Documento emitido automaticamente em {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Este recibo é válido sem assinatura, conforme legislação em vigor</p>
    </div>
</body>
</html>

