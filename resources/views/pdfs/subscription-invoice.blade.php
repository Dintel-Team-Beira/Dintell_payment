<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: A4;
            margin: 15mm; /* Margens padrão para A4 */
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            border-bottom: 2px solid #1a365d;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .company-logo {
            float: left;
            width: 60%;
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
            font-style: italic;
            margin-bottom: 10px;
        }

        .company-details {
            font-size: 11px;
            line-height: 1.3;
        }

        .client-info {
            float: right;
            width: 35%;
            text-align: right;
        }

        .client-box {
            border: 1px solid #ddd;
            padding: 12px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .document-type {
            text-align: center;
            margin: 20px 0;
        }

        .document-type span {
            border: 2px solid #1a365d;
            padding: 5px 10px;
            font-weight: bold;
            color: #1a365d;
            border-radius: 5px;
        }

        .invoice-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: white;
            margin: 20px 0 15px;
            padding: 12px;
            background-color: #1a365d;
            border-radius: 5px;
        }

        .invoice-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .invoice-details td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 11px;
        }

        .invoice-details .label {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 20%;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }

        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #555;
        }

        .items-table .text-right {
            text-align: right;
        }

        .iva-summary {
            margin: 15px 0;
            page-break-before: always; /* Força a quebra para a segunda página */
        }

        .iva-title {
            font-size: 14px;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 8px;
        }

        .iva-table {
            width: 60%;
            border-collapse: collapse;
        }

        .iva-table th,
        .iva-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            font-size: 11px;
        }

        .iva-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #555;
        }

        .totals-table {
            width: 50%;
            margin-left: auto;
            margin-top: 15px;
            border-collapse: collapse;
        }

        .totals-table td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 11px;
        }

        .totals-table .label {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .totals-table .total-row {
            background-color: #1a365d;
            color: white;
            font-weight: bold;
            font-size: 13px;
        }

        .payment-info {
            background-color: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 12px;
            margin: 15px 0;
            border-radius: 5px;
        }

        .payment-title {
            font-size: 12px;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 8px;
        }

        .payment-details {
            font-size: 11px;
            margin-left: 15px;
        }

        .footer-info {
            border-top: 1px solid #ddd;
            padding-top: 12px;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
            text-align: center;
        }

        .page-number {
            position: fixed;
            bottom: 15mm;
            right: 15mm;
            font-size: 10px;
            color: #666;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(0, 0, 0, 0.08);
            z-index: -1;
        }

        /* Evitar quebras indesejadas dentro das seções */
        .header, .document-type, .invoice-title, .invoice-details, .items-table, .iva-summary, .totals-table, .payment-info, .footer-info {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <!-- Página 1 -->
    <div class="clearfix header">
        <div class="company-logo">
            <div class="company-name">{{ $company['name'] }}</div>
            <div class="company-slogan">{{ $company['slogan'] }}</div>
            <div class="company-details">
                <strong>Contribuinte Nº:</strong> {{ $company['nuit'] }}<br>
                {{ $company['address_maputo'] }}<br>
                {{ $company['address_beira'] }}<br>
                {{ $company['country'] }}<br>
                {{ $company['phone'] }}<br>
                {{ $company['email'] }}
            </div>
        </div>
        <div class="client-info">
            <div class="client-box">
                <strong>Exmo.(s) Sr.(s)</strong><br>
                <strong>{{ $client->name }}</strong><br>
                Nº CLIENTE: {{ str_pad($client->id, 10, '0', STR_PAD_LEFT) }}<br>
                {{ $client->phone ?? 'N/A' }}<br>
                {{ $client->address ?? 'Moçambique' }}
            </div>
        </div>
    </div>

    <div class="document-type">
        <span>Original</span>
    </div>

    <div class="invoice-title">Factura Nº {{ $invoice_number }}</div>

    <table class="invoice-details">
        <tr>
            <td class="label">V/Nº CONTRIB.</td>
            <td>{{ $client->nuit ?? 'N/A' }}</td>
            <td class="label">REQUISIÇÃO</td>
            <td>-</td>
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
            <td class="label" colspan="2">CONDIÇÃO DE PAGAMENTO</td>
            <td colspan="2">PRONTO PAGAMENTO</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>ARTIGO</th>
                <th>DESCRIÇÃO</th>
                <th>QUANT.</th>
                <th>PR. UNITÁRIO</th>
                <th>DESCONTOS</th>
                <th>IVA</th>
                <th>TOTAL LÍQUIDO</th>
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

    <!-- Página 2 -->
    <div class="iva-summary">
        <div class="iva-title">QUADRO RESUMO DO IVA</div>
        <table class="iva-table">
            <thead>
                <tr>
                    <th>TAXA</th>
                    <th>INCIDÊNCIA</th>
                    <th>TOTAL IVA</th>
                    <th>MOTIVO ISENÇÃO</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $iva_rate }}.00</td>
                    <td>{{ number_format($subtotal, 2) }}</td>
                    <td>{{ number_format($iva_amount, 2) }}</td>
                    <td>-</td>
                </tr>
            </tbody>
        </table>
    </div>

    <table class="totals-table">
        <tr>
            <td class="label">SUBTOTAL</td>
            <td class="text-right">{{ number_format($subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="label">DESCONTOS COMERCIAIS</td>
            <td class="text-right">0.00</td>
        </tr>
        <tr>
            <td class="label">DESCONTO FINANCEIRO</td>
            <td class="text-right">0.00</td>
        </tr>
        <tr>
            <td class="label">IVA</td>
            <td class="text-right">{{ number_format($iva_amount, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td class="label">TOTAL (MT)</td>
            <td class="text-right">{{ number_format($total, 2) }}</td>
        </tr>
    </table>

    <div class="payment-info">
        <div class="payment-title">INFORMAÇÕES DE PAGAMENTO</div>
        <div class="payment-details">
            O pagamento pode ser efectuado em numerário, cheque, depósito ou transferência<br>
            <strong>Banco:</strong> {{ $company['bank_name'] ?? 'BCI' }}<br>
            <strong>Moeda:</strong> MT<br>
            <strong>Conta:</strong> {{ $company['bank_account'] ?? '222 038 724 100 01' }}<br>
            <strong>NIB:</strong> {{ $company['bank_nib'] ?? '0008 0000 2203 8724 101 13' }}
        </div>
    </div>

    <div class="footer-info">
        <p><strong>Data de Impressão:</strong> {{ now()->format('d/m/Y') }}</p>
        <p>Documento processado por computador © onGest | Impresso por: DINTELL ADMIN</p>
        <p style="margin-top: 8px;">
            <strong>Contacto:</strong> {{ $company['phone'] }} | {{ $company['email'] }}
        </p>
    </div>

    <div class="page-number">Página <span class="page-current"></span> de 2</div>

    <div class="watermark">DINTELL</div>
</body>
</html>