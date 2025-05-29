<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
            margin: 20px;
        }

        .header {
            margin-bottom: 20px;
            overflow: hidden;
        }

        .company-section {
            float: left;
            width: 60%;
        }

        .logo {
            float: left;
            margin-right: 15px;
            margin-bottom: 10px;
        }

        .logo img {
            max-width: 80px;
            max-height: 60px;
        }

        .company-info {
            overflow: hidden;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 2px;
        }

        .company-slogan {
            font-size: 10px;
            color: #666;
            font-style: italic;
            margin-bottom: 8px;
        }

        .company-details {
            font-size: 10px;
            line-height: 1.4;
        }

        .client-section {
            float: right;
            width: 35%;
        }

        .client-box {
            border: 1px solid #666;
            padding: 10px;
            background-color: #f9f9f9;
            font-size: 10px;
        }

        .invoice-header {
            text-align: center;
            margin: 15px 0;
        }

        .original-label {
            border: 1px solid #333;
            display: inline-block;
            padding: 4px 10px;
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .invoice-title {
            background-color: #333;
            color: white;
            padding: 8px;
            font-size: 14px;
            font-weight: bold;
        }

        .invoice-details {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .invoice-details td {
            border: 1px solid #666;
            padding: 5px;
            font-size: 9px;
        }

        .invoice-details .label {
            background-color: #f5f5f5;
            font-weight: bold;
            width: 15%;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #666;
            padding: 6px;
            font-size: 10px;
        }

        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .summary-row {
            margin-top: 15px;
            overflow: hidden;
        }

        .iva-summary {
            float: left;
            width: 48%;
        }

        .totals-summary {
            float: right;
            width: 48%;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .iva-table {
            width: 100%;
            border-collapse: collapse;
        }

        .iva-table th,
        .iva-table td {
            border: 1px solid #666;
            padding: 4px;
            font-size: 9px;
            text-align: center;
        }

        .iva-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            border: 1px solid #666;
            padding: 5px;
            font-size: 10px;
        }

        .totals-table .label {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .total-final {
            background-color: #333;
            color: white;
            font-weight: bold;
            font-size: 11px;
        }

        .observations {
            background-color: #4a9b8e;
            color: white;
            padding: 6px 10px;
            margin: 15px 0;
            font-size: 10px;
            font-weight: bold;
        }

        .payment-section {
            margin: 15px 0;
            font-size: 10px;
            overflow: hidden;
        }

        .bank-logo {
            float: left;
            color: white;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 8px;
            margin-right: 10px;
            margin-top: 2px;
        }

        .payment-details {
            overflow: hidden;
            line-height: 1.4;
        }

        .footer {
            margin-top: 20px;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }

        /* Limpar floats */
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        /* Otimizações para dompdf */
        table {
            border-spacing: 0;
        }

        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="clearfix header">
        <div class="company-section">
            <div class="company-info">
                <img src="{{ public_path('logo.png') }}"style="width: 200px;" alt="Logo">
                <div class="company-details">
                    <strong>Contribuinte Nº:</strong> {{ $company['nuit'] }}<br>
                    {{ $company['address_maputo'] }}<br>
                    {{ $company['address_beira'] }}<br>
                    {{ $company['country'] }}<br>
                    {{ $company['phone'] }} | {{ $company['email'] }}
                </div>
            </div>
        </div>

        <div class="client-section">
            <div class="client-box">
                <strong>Exmo.(s) Sr.(s)</strong><br>
                <strong>{{ $client->name }}</strong><br>
                <strong>Nº CLIENTE:</strong> {{ str_pad($client->id, 10, '0', STR_PAD_LEFT) }}<br>
                {{ $client->phone ?? 'N/A' }}<br>
                {{ $client->address ?? 'Moçambique' }}
            </div>
        </div>
    </div>

    <div class="invoice-header">
        <div class="invoice-title">Factura Nº {{ $invoice_number }}</div>
    </div>

    <table class="invoice-details no-break">
        <tr>
            <td class="label">V/Nº CONTRIB.</td>
            <td>{{ $client->nuit ?? 'N/A' }}</td>
            <td class="label">MOEDA</td>
            <td>MT</td>
            <td class="label">CÂMBIO</td>
            <td>1,00</td>
            <td class="label">DATA</td>
            <td>{{ $invoice_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">DESC. CLI.</td>
            <td>0.00</td>
            <td class="label">DESC. FIN.</td>
            <td>0.00</td>
            <td class="label">VENCIMENTO</td>
            <td>{{ $due_date->format('d/m/Y') }}</td>
            <td class="label" colspan="2">PRONTO PAGAMENTO</td>
        </tr>
    </table>

    <table class="items-table no-break">
        <thead>
            <tr>
                <th style="width: 15%;">ARTIGO</th>
                <th style="width: 35%;">DESCRIÇÃO</th>
                <th style="width: 8%;">QUANT.</th>
                <th style="width: 12%;">PR. UNITÁRIO</th>
                <th style="width: 10%;">DESCONTOS</th>
                <th style="width: 10%;">IVA</th>
                <th style="width: 10%;">TOTAL LÍQUIDO</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>WEB-{{ strtoupper(substr($plan->slug, 0, 6)) }}</td>
                <td>{{ $plan->name }} - Subscrição {{ ucfirst($plan->billing_cycle) }}</td>
                <td class="text-right">1.00</td>
                <td class="text-right">{{ number_format($subtotal, 2) }}</td>
                <td class="text-right">0.00</td>
                <td class="text-right">{{ number_format($iva_amount, 2) }}</td>
                <td class="text-right">{{ number_format($subtotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="clearfix summary-row no-break">
        <div class="iva-summary">
            <div class="section-title">QUADRO RESUMO DO IVA</div>
            <table class="iva-table">
                <thead>
                    <tr>
                        <th>TAXA</th>
                        <th>INCIDÊNCIA</th>
                        <th>TOTAL IVA</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $iva_rate }}.00</td>
                        <td>{{ number_format($subtotal, 2) }}</td>
                        <td>{{ number_format($iva_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="totals-summary">
            <table class="totals-table">
                <tr>
                    <td class="label">SUBTOTAL</td>
                    <td class="text-right">{{ number_format($subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">DESCONTOS</td>
                    <td class="text-right">0.00</td>
                </tr>
                <tr>
                    <td class="label">IVA</td>
                    <td class="text-right">{{ number_format($iva_amount, 2) }}</td>
                </tr>
                <tr class="total-final">
                    <td style="color: black" class="label">TOTAL (MT)</td>
                    <td class="text-right">{{ number_format($total, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="observations">OBSERVAÇÕES</div>

    <div class="clearfix payment-section">
        <div class="bank-logo">
            <img src="{{ public_path('bci.svg') }}"style="width: 20px;" alt="Logo">
        </div>
        <div class="payment-details">
            O pagamento pode ser efectuado em numerário, cheque, depósito ou transferência<br>
            <strong>Conta:</strong> {{ $company['bank_account'] ?? '222 038 724 100 01' }} |
            <strong>NIB:</strong> {{ $company['bank_nib'] ?? '0008 0000 2203 8724 101 13' }}
        </div>
    </div>

    <div class="footer">
        <strong>Data de Impressão:</strong> {{ now()->format('d/m/Y') }} |
        Documento processado por computador © onGest |
        <strong>Contacto:</strong> {{ $company['phone'] }}
    </div>
</body>
</html>