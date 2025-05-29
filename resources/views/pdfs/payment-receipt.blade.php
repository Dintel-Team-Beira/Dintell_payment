<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Recibo {{ $receiptNumber }}</title>
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

        .document-number {
            font-size: 14px;
            color: #374151;
            margin-top: 5px;
        }

        .amount-box {
            text-align: center;
            padding: 15px;
            margin: 20px 0;
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            border-radius: 5px;
        }

        .amount-box .amount {
            font-size: 20px;
            font-weight: bold;
            color: #1a365d;
            margin: 0;
        }

        .amount-box .currency {
            font-size: 12px;
            color: #374151;
            margin-top: 5px;
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

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .details-table td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 11px;
        }

        .details-table .label {
            background: #f8f9fa;
            font-weight: bold;
            width: 40%;
        }

        .details-table .total-row {
            background: #1a365d;
            color: white;
            font-weight: bold;
            font-size: 13px;
        }

        .additional-info {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            page-break-before: always; /* Força quebra para a segunda página */
        }

        .additional-info h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .additional-info p {
            margin: 5px 0;
            font-size: 12px;
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
            border-top: 1px solid #168;
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
            color: #666;
        }

        .page-number {
            position: fixed;
            bottom: 15mm;
            right: 15mm;
            font-size: 10px;
            color: #666;
        }

        /* Evitar quebras indesejadas */
        .header, .document-title, .amount-box, .info-list, .details-table, .additional-info, .signature-section, .footer {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <!-- Página 1 -->
    <div class="header">
        <div class="company-name">{{ $company['name'] }}</div>
        <div class="company-slogan">{{ $company['name'] }}</div>
        <div class="company-contact">
            NUIT: {{ $company['nuit'] }} | {{ $company['phone'] }} | {{ $company['email'] }}<br>
            {{ $company['address_maputo'] }}
        </div>
    </div>

    <div class="document-title">
        Recibo de Pagamento
        <div class="document-number">N.º {{ $receiptNumber }}</div>
    </div>

    <div class="amount-box">
        <div class="amount">MT {{ $amount }}</div>
        <div class="currency">Meticais Moçambicanos</div>
    </div>

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
            <p>{{ $client->id ? str_pad($client->id, 6, '0', STR_PAD_LEFT) : 'N/A' }}</p>
        </div>
        <div class="info-item">
            <h4>Data do Pagamento</h4>
            <p>{{ $paymentDate->format('d/m/Y H:i:s') }}</p>
        </div>
        <div class="info-item">
            <h4>Método de Pagamento</h4>
            <p>{{ $paymentMethod->name }}</p>
        </div>
        @if($paymentReference)
        <div class="info-item">
            <h4>Referência</h4>
            <p>{{ $paymentReference }}</p>.
        </div>
        @endif
        <div class="info-item">
            <h4>Status</h4>
            <p style="color: #1a365d;">Confirmado</p>
        </div>
    </div>

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
        <tr class="total-row">
            <td class="label">Total Pago</td>
            <td>MT {{ number_format($amount, 2) }}</td>
        </tr>
    </table>

    <!-- Página 2 -->
    <div class="additional-info">
        <h3>Informações Adicionais</h3>
        <p>O serviço está ativo e funcionando normalmente.</p>
        <p>Válido para efeitos fiscais e contabilísticos.</p>
        <p>Em caso de dúvidas, contacte-nos através dos dados acima.</p>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">Assinatura do Cliente</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">DINTELL, LDA</div>
        </div>
    </div>

    <div class="footer">
        <p><strong>{{ $company['name'] }}</strong> - {{ $company['slogan'] }}</p>
        <p>Documento emitido automaticamente em {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Este recibo é válido sem assinatura, conforme legislação em vigor.</p>
    </div>

    <div class="page-number">Página <span class="page-current"></span> de 2</div>
</body>
</html>