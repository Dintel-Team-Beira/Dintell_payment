<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura {{ $invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; margin: 0; padding: 20px; }
        .header { display: table; width: 100%; margin-bottom: 30px; }
        .company-logo { display: table-cell; width: 60%; vertical-align: top; }
        .client-info { display: table-cell; width: 40%; vertical-align: top; text-align: right; }
        .company-name { font-size: 24px; font-weight: bold; color: #1e3a8a; margin-bottom: 5px; }
        .company-slogan { font-size: 11px; color: #666; margin-bottom: 15px; }
        .company-details { font-size: 10px; line-height: 1.3; }
        .invoice-title { font-size: 20px; font-weight: bold; margin: 30px 0 20px 0; }
        .invoice-details { display: table; width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .invoice-details td { padding: 8px; border: 1px solid #ddd; }
        .invoice-details .label { background: #f5f5f5; font-weight: bold; width: 30%; }
        .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .items-table th { background: #f8f9fa; font-weight: bold; }
        .items-table .text-right { text-align: right; }
        .totals-table { width: 50%; margin-left: auto; margin-top: 20px; border-collapse: collapse; }
        .totals-table td { padding: 8px; border: 1px solid #ddd; }
        .totals-table .label { background: #f5f5f5; font-weight: bold; }
        .totals-table .total-row { background: #e3f2fd; font-weight: bold; font-size: 14px; }
        .iva-summary { margin: 20px 0; }
        .iva-table { width: 60%; border-collapse: collapse; }
        .iva-table th, .iva-table td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        .iva-table th { background: #f8f9fa; font-weight: bold; }
        .footer-info { margin-top: 40px; font-size: 10px; }
        .payment-info { background: #f0f9ff; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .page-number { position: fixed; bottom: 20px; right: 20px; font-size: 10px; }
        .watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 60px; color: rgba(0,0,0,0.1); z-index: -1; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
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
            <strong>Exmo.(s) Sr.(s)</strong><br>
            <strong>{{ $client->name }}</strong><br>
            Nº CLIENTE: {{ str_pad($client->id, 10, '0', STR_PAD_LEFT) }}<br>
            {{ $client->phone ?? 'N/A' }}<br>
            {{ $client->address ?? 'Moçambique' }}
        </div>
    </div>

    <!-- Document Type -->
    <div style="text-align: center; margin: 20px 0;">
        <strong style="border: 2px solid #000; padding: 5px 10px;">Original</strong>
    </div>

    <!-- Invoice Title -->
    <div class="invoice-title">Factura Nº {{ $invoice_number }}</div>

    <!-- Invoice Details -->
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

    <!-- Items Table -->
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

    <!-- IVA Summary -->
    <div class="iva-summary">
        <strong>QUADRO RESUMO DO IVA</strong>
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

    <!-- Totals -->
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

    <!-- Payment Information -->
    <div class="payment-info">
        <strong>OBSERVAÇÕES</strong><br>
        O pagamento pode ser efectuado em numerário, cheque, depósito ou transferência<br>
        <strong>Banco:</strong> {{ $company['bank_name'] ?? 'BCI' }}<br>
        <strong>Moeda:</strong> MT<br>
        <strong>Conta:</strong> {{ $company['bank_account'] ?? '222 038 724 100 01' }}<br>
        <strong>NIB:</strong> {{ $company['bank_nib'] ?? '0008 0000 2203 8724 101 13' }}
    </div>

    <!-- Footer -->
    <div class="footer-info">
        <strong>Data de Impressão:</strong> {{ now()->format('d/m/Y') }}<br>
        Documento processado por computador © onGest/ Impresso por: DINTELL ADMIN
    </div>

    <!-- Page Number -->
    <div class="page-number">Page 1 of 1</div>

    <!-- Watermark -->
    <div class="watermark">DINTELL</div>
</body>
</html>
