<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota de Crédito {{ $creditNote->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 20px;
        }

        .company-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .document-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 11px;
            color: #666;
            line-height: 1.3;
        }

        .document-title {
            font-size: 20px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }

        .document-number {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .document-date {
            font-size: 11px;
            color: #666;
        }

        /* Credit Note Alert */
        .credit-alert {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }

        .credit-alert h3 {
            color: #856404;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .credit-alert p {
            color: #6c5707;
            font-size: 11px;
        }

        /* Client Info */
        .client-section {
            margin: 30px 0;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        .client-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
        }

        .client-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .client-details {
            font-size: 11px;
            color: #666;
            line-height: 1.3;
        }

        /* Related Invoice Info */
        .related-invoice {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
        }

        .related-invoice h4 {
            font-size: 12px;
            font-weight: bold;
            color: #004085;
            margin-bottom: 5px;
        }

        .related-invoice p {
            font-size: 11px;
            color: #0056b3;
        }

        /* Adjustment Reason */
        .adjustment-reason {
            margin: 20px 0;
            padding: 15px;
            background-color: #fff5f5;
            border-left: 4px solid #e74c3c;
            border-radius: 0 4px 4px 0;
        }

        .adjustment-reason h4 {
            font-size: 12px;
            font-weight: bold;
            color: #721c24;
            margin-bottom: 5px;
        }

        .adjustment-reason p {
            font-size: 11px;
            color: #a0283e;
            line-height: 1.4;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }

        .items-table th {
            background-color: #e74c3c;
            color: white;
            padding: 12px 8px;
            font-size: 11px;
            font-weight: bold;
            text-align: left;
            border: 1px solid #c0392b;
        }

        .items-table td {
            padding: 10px 8px;
            font-size: 11px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .items-table tbody tr:hover {
            background-color: #ffe6e6;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        /* Totals */
        .totals-section {
            margin-top: 30px;
            display: table;
            width: 100%;
        }

        .totals-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .totals-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-left: auto;
        }

        .totals-table td {
            padding: 8px 12px;
            font-size: 12px;
            border: 1px solid #ddd;
        }

        .totals-table .label {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: right;
            width: 60%;
        }

        .totals-table .value {
            text-align: right;
            font-weight: bold;
            width: 40%;
        }

        .total-final {
            background-color: #e74c3c !important;
            color: white !important;
            font-size: 14px !important;
            font-weight: bold !important;
        }

        /* Notes */
        .notes-section {
            margin-top: 30px;
        }

        .notes-content {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            font-size: 11px;
            line-height: 1.4;
            color: #555;
        }

        /* Footer */
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .signature-section {
            margin-top: 50px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
            font-size: 10px;
            color: #666;
        }

        /* Print styles */
        @media print {
            body {
                font-size: 11px;
            }

            .container {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }

        /* Currency formatting */
        .currency {
            font-family: 'Courier New', monospace;
        }

        /* Status indicators */
        .status-processed {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-name">{{ $settings->company_name ?? 'Minha Empresa' }}</div>
                <div class="company-details">
                    @if($settings->company_address)
                        {{ $settings->company_address }}<br>
                    @endif
                    @if($settings->company_phone)
                        Tel: {{ $settings->company_phone }}<br>
                    @endif
                    @if($settings->company_email)
                        Email: {{ $settings->company_email }}<br>
                    @endif
                    @if($settings->company_nuit)
                        NUIT: {{ $settings->company_nuit }}
                    @endif
                </div>
            </div>
            <div class="document-info">
                <div class="document-title">NOTA DE CRÉDITO</div>
                <div class="document-number">{{ $creditNote->invoice_number }}</div>
                <div class="document-date">
                    Data: {{ $creditNote->invoice_date->format('d/m/Y') }}
                </div>
                <div class="status-processed">PROCESSADA</div>
            </div>
        </div>

        <!-- Credit Note Alert -->
        <div class="credit-alert">
            <h3>📋 DOCUMENTO DE CRÉDITO</h3>
            <p>Este documento representa um crédito a favor do cliente no valor abaixo especificado.</p>
        </div>

        <!-- Client Information -->
        <div class="client-section">
            <div class="section-title">DADOS DO CLIENTE</div>
            <div class="client-info">
                <div class="client-name">{{ $creditNote->client->name }}</div>
                <div class="client-details">
                    @if($creditNote->client->email)
                        Email: {{ $creditNote->client->email }}<br>
                    @endif
                    @if($creditNote->client->phone)
                        Telefone: {{ $creditNote->client->phone }}<br>
                    @endif
                    @if($creditNote->client->address)
                        Endereço: {{ $creditNote->client->address }}<br>
                    @endif
                    @if($creditNote->client->nuit)
                        NUIT: {{ $creditNote->client->nuit }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Related Invoice (if exists) -->
        @if($creditNote->relatedInvoice)
        <div class="related-invoice">
            <h4>📄 FATURA RELACIONADA</h4>
            <p>
                <strong>Número:</strong> {{ $creditNote->relatedInvoice->invoice_number }} |
                <strong>Data:</strong> {{ $creditNote->relatedInvoice->invoice_date->format('d/m/Y') }} |
                <strong>Valor Original:</strong> <span class="currency">{{ number_format($creditNote->relatedInvoice->total, 2, ',', '.') }} MT</span>
            </p>
        </div>
        @endif

        <!-- Adjustment Reason -->
        <div class="adjustment-reason">
            <h4>MOTIVO DO AJUSTE</h4>
            <p>{{ $creditNote->adjustment_reason }}</p>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 45%;">DESCRIÇÃO</th>
                    <th style="width: 10%;" class="text-center">QTDE</th>
                    <th style="width: 15%;" class="text-right">PREÇO UNIT.</th>
                    <th style="width: 10%;" class="text-center">IVA %</th>
                    <th style="width: 15%;" class="text-right">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($creditNote->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="text-center">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                    <td class="text-right currency">{{ number_format($item->unit_price, 2, ',', '.') }} MT</td>
                    <td class="text-center">{{ number_format($item->tax_rate, 1, ',', '.') }}%</td>
                    <td class="font-bold text-right currency">
                        {{ number_format($item->quantity * $item->unit_price * (1 + $item->tax_rate/100), 2, ',', '.') }} MT
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <div class="totals-left">
                @if($creditNote->notes)
                <div class="notes-section">
                    <div class="section-title">OBSERVAÇÕES</div>
                    <div class="notes-content">
                        {{ $creditNote->notes }}
                    </div>
                </div>
                @endif
            </div>
            <div class="totals-right">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal:</td>
                        <td class="value currency">{{ number_format($creditNote->subtotal, 2, ',', '.') }} MT</td>
                    </tr>
                    <tr>
                        <td class="label">IVA:</td>
                        <td class="value currency">{{ number_format($creditNote->tax_amount, 2, ',', '.') }} MT</td>
                    </tr>
                    @if($creditNote->discount_amount > 0)
                    <tr>
                        <td class="label">Desconto:</td>
                        <td class="value currency">-{{ number_format($creditNote->discount_amount, 2, ',', '.') }} MT</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label total-final">TOTAL A CREDITAR:</td>
                        <td class="value total-final currency">{{ number_format($creditNote->total, 2, ',', '.') }} MT</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    Emitido por<br>
                    {{ $settings->company_name ?? 'Empresa' }}
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    Recebido por<br>
                    {{ $creditNote->client->name }}
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Este documento foi gerado eletronicamente em {{ now()->format('d/m/Y H:i:s') }}<br>
                Nota de Crédito {{ $creditNote->invoice_number }} - {{ $settings->company_name ?? 'Sistema de Faturação' }}
            </p>
        </div>
    </div>
</body>
</html>
