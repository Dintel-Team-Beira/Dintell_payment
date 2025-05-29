<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: A4;
            margin: 40mm 30mm; /* Margens generosas como no PDF */
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px; /* Tamanho padrão normal */
            line-height: 1.4;
            color: #333;
            max-width: 100%;
        }

        .header {
            margin-bottom: 25px;
            min-height: 140px;
        }

        .company-info {
            float: left;
            width: 55%;
        }

        .company-name {
            font-size: 18px; /* Tamanho 14+ conforme solicitado */
            font-weight: bold;
            color: #000;
            margin-bottom: 4px;
        }

        .company-slogan {
            font-size: 12px;
            color: #666;
            font-style: italic;
            margin-bottom: 12px;
        }

        .company-details {
            font-size: 12px;
            line-height: 1.5;
            margin-top: 8px;
        }

        .client-info {
            float: right;
            width: 40%;
            text-align: right;
        }

        .client-box {
            border: 2px solid #000;
            padding: 15px;
            background-color: white;
            font-size: 12px;
            line-height: 1.5;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .document-type {
            text-align: center;
            margin: 30px 0;
        }

        .document-type span {
            border: 2px solid #000;
            padding: 8px 15px;
            font-weight: bold;
            font-size: 12px;
        }

        .invoice-title {
            text-align: center;
            font-size: 18px; /* Tamanho 14+ */
            font-weight: bold;
            margin: 25px 0;
            padding: 12px;
            background-color: #000;
            color: white;
        }

        .invoice-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .invoice-details td {
            border: 1px solid #000;
            padding: 8px 10px;
            font-size: 12px;
            text-align: left;
        }

        .invoice-details .label {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 10px;
            font-size: 12px;
            text-align: left;
        }

        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }

        .items-table .text-right {
            text-align: right;
        }

        .bottom-section {
            display: table;
            width: 100%;
            margin-top: 30px;
            margin-bottom: 25px;
        }

        .iva-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 30px;
        }

        .totals-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .iva-title {
            font-size: 14px; /* Tamanho 14 */
            font-weight: bold;
            margin-bottom: 12px;
        }

        .iva-table {
            width: 100%;
            border-collapse: collapse;
        }

        .iva-table th,
        .iva-table td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 12px;
            text-align: center;
        }

        .iva-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
        }

        .totals-table td {
            border: 1px solid #000;
            padding: 10px;
            font-size: 12px;
        }

        .totals-table .label {
            background-color: #f5f5f5;
            font-weight: bold;
            width: 70%;
        }

        .totals-table .text-right {
            text-align: right;
            width: 30%;
        }

        .total-row {
            background-color: #000;
            color: white;
            font-weight: bold;
            font-size: 14px; /* Destacado com tamanho 14 */
        }

        .observations {
            margin: 25px 0;
            background-color: #4a9b8e;
            color: white;
            padding: 12px 15px;
            font-size: 14px; /* Tamanho 14 */
            font-weight: bold;
        }

        .payment-info {
            margin: 25px 0;
            font-size: 12px;
            line-height: 1.6;
            padding: 15px 0;
        }

        .bank-logo {
            float: left;
            margin-right: 15px;
            margin-top: 8px;
        }

        .bank-info {
            margin-left: 60px;
        }

        .footer-info {
            margin-top: 30px;
            font-size: 11px;
            color: #666;
            line-height: 1.4;
            text-align: left;
        }

        /* Espaçamentos extras para não ocupar toda página */
        .container {
            max-width: 90%;
            margin: 0 auto;
            padding: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="clearfix header">
            <div class="company-info">
                <div class="company-name">{{ $company['name'] }}</div>
                <div class="company-slogan">{{ $company['slogan'] }}</div>
                <div class="company-details">
                    <strong>Contribuinte Nº:</strong> {{ $company['nuit'] }}<br>
                    {{ $company['address_maputo'] }}<br>
                    {{ $company['address_beira'] }}<br>
                    {{ $company['country'] }}<br><br>
                    {{ $company['phone'] }}<br>
                    {{ $company['email'] }}
                </div>
            </div>
            <div class="client-info">
                <div class="client-box">
                    <strong>Exmo.(s) Sr.(s)</strong><br><br>
                    <strong>{{ $client->name }}</strong><br>
                    <strong>Nº CLIENTE:</strong> {{ str_pad($client->id, 10, '0', STR_PAD_LEFT) }}<br><br>
                    {{ $client->phone ?? 'N/A' }}<br><br>
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

        <div class="bottom-section">
            <div class="iva-section">
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

            <div class="totals-section">
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
            </div>
        </div>

        <div class="observations">
            OBSERVAÇÕES
        </div>

        <div class="clearfix payment-info">
            <div class="bank-logo">
                <div style="width: 45px; height: 30px; background-color: #e74c3c; color: white; text-align: center; line-height: 30px; font-weight: bold; font-size: 10px;">BCI</div>
            </div>
            <div class="bank-info">
                O pagamento pode ser efectuado em numerário, cheque, depósito ou transferência<br>
                <strong>Banco:</strong> {{ $company['bank_name'] ?? 'BCI' }}<br>
                <strong>Moeda:</strong> MT<br>
                <strong>Conta:</strong> {{ $company['bank_account'] ?? '222 038 724 100 01' }}<br>
                <strong>NIB:</strong> {{ $company['bank_nib'] ?? '0008 0000 2203 8724 101 13' }}
            </div>
        </div>

        <div class="footer-info">
            <strong>Data de Impressão:</strong> {{ now()->format('d/m/Y') }}<br>
            Documento processado por computador © onGest/ Impresso por: DINTELL ADMIN<br><br>
            <strong>Contacto:</strong> {{ $company['phone'] }} | {{ $company['email'] }}
        </div>
    </div>
</body>
</html>