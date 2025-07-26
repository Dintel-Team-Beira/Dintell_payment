<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Nota de Crédito {{ $creditNote->invoice_number }}</title>
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
            color: #e74c3c;
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
            border: 1px solid #e74c3c;
            padding: 10px;
            background-color: #fff5f5;
            font-size: 10px;
        }

        .document-header {
            text-align: left;
            margin: 15px 0;
        }

        .original-label {
            border: 1px solid #e74c3c;
            display: inline-block;
            padding: 4px 10px;
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #e74c3c;
        }

        .document-title {
            background-color: #e74c3c;
            color: white;
            padding: 8px;
            font-size: 14px;
            font-weight: bold;
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
            font-size: 12px;
            margin-bottom: 5px;
        }

        .credit-alert p {
            color: #6c5707;
            font-size: 10px;
        }

        .document-details {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .document-details td {
            border: 1px solid #e74c3c;
            padding: 5px;
            font-size: 9px;
        }

        .document-details .label {
            background-color: #fff5f5;
            font-weight: bold;
            width: 15%;
        }

        /* Related Invoice Info */
        .related-invoice {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            font-size: 10px;
        }

        .related-invoice h4 {
            font-size: 11px;
            font-weight: bold;
            color: #004085;
            margin-bottom: 5px;
        }

        .related-invoice p {
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
            font-size: 11px;
            font-weight: bold;
            color: #721c24;
            margin-bottom: 5px;
        }

        .adjustment-reason p {
            font-size: 10px;
            color: #a0283e;
            line-height: 1.4;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #e74c3c;
            padding: 6px;
            font-size: 10px;
        }

        .items-table th {
            background-color: #e74c3c;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
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
            border: 1px solid #e74c3c;
            padding: 4px;
            font-size: 9px;
            text-align: center;
        }

        .iva-table th {
            background-color: #fff5f5;
            font-weight: bold;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            border: 1px solid #e74c3c;
            padding: 5px;
            font-size: 10px;
        }

        .totals-table .label {
            background-color: #fff5f5;
            font-weight: bold;
        }

        .total-final {
            background-color: #e74c3c;
            color: white;
            font-weight: bold;
            font-size: 11px;
        }

        .observations {
            background-color: #e74c3c;
            color: white;
            padding: 6px 10px;
            margin: 15px 0;
            font-size: 10px;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 8px;
            text-align: center;
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

        /* Status da nota */
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            margin-left: 10px;
        }

        .status-processed {
            background-color: #28a745;
            color: white;
        }

        .status-pending {
            background-color: #ffc107;
            color: black;
        }

        .status-cancelled {
            background-color: #6c757d;
            color: white;
        }

        /* Signature section */
        .signature-section {
            margin-top: 50px;
            overflow: hidden;
        }

        .signature-box {
            float: left;
            width: 48%;
            text-align: center;
            margin-right: 4%;
        }

        .signature-box:last-child {
            margin-right: 0;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="clearfix header">
        <div class="company-section">
            <div class="company-info">
                @if(file_exists(public_path('logo.png')))
                <img src="{{ public_path('logo.png') }}" style="width: 200px;" alt="Logo">
                @else
                <div class="company-name">{{ $settings->company_name ?? 'Minha Empresa' }}</div>
                <div class="company-slogan">beyond technology, intelligence.</div>
                @endif
                <div class="company-details">
                    <strong>Contribuinte Nº:</strong> {{ $settings->company_nuit ?? '123456789' }}<br>
                    {{ $settings->company_address ?? 'Av. Principal nº 123, R/C' }}<br>
                    {{ $settings->company_address_maputo ?? 'Maputo, Moçambique' }}<br>
                    {{ $settings->company_phone ?? '+258 84 123 4567' }} | {{ $settings->company_email ?? 'geral@empresa.co.mz' }}
                </div>
            </div>
        </div>

        <div class="client-section">
            <div class="client-box">
                <strong>Exmo.(s) Sr.(s)</strong><br>
                <strong>{{ $creditNote->client->name }}</strong><br>
                <strong>Nº CLIENTE:</strong> {{ str_pad($creditNote->client->id, 10, '0', STR_PAD_LEFT) }}<br>
                {{ $creditNote->client->phone ?? 'N/A' }}<br>
                {{ $creditNote->client->address ?? $creditNote->client->city ?? 'Moçambique' }}
            </div>
        </div>
    </div>

    <div class="document-header">
        <div class="original-label">Original</div>
        <div class="document-title">
            Nota de Crédito Nº {{ $creditNote->invoice_number }}
            {{-- <span class="status-badge status-processed">PROCESSADA</span> --}}
        </div>
    </div>

    <!-- Credit Note Alert -->
    {{-- <div class="credit-alert">
        <h3>📋 DOCUMENTO DE CRÉDITO</h3>
        <p>Este documento representa um crédito a favor do cliente no valor abaixo especificado.</p>
    </div> --}}

    <!-- Related Invoice (if exists) -->
    @if($creditNote->relatedInvoice)
    <div class="related-invoice">
        <h4>📄 FATURA RELACIONADA</h4>
        <p>
            <strong>Número:</strong> {{ $creditNote->relatedInvoice->invoice_number }} |
            <strong>Data:</strong> {{ $creditNote->relatedInvoice->invoice_date->format('d/m/Y') }} |
            <strong>Valor Original:</strong> {{ number_format($creditNote->relatedInvoice->total, 2, ',', '.') }} MT
        </p>
    </div>
    @endif

    <!-- Adjustment Reason -->
    <div class="adjustment-reason">
        <h4>MOTIVO DO AJUSTE</h4>
        <p>{{ $creditNote->adjustment_reason }}</p>
    </div>

    <table class="document-details no-break">
        <tr>
            <td class="label">V/Nº CONTRIB.</td>
            <td>{{ $creditNote->client->nuit ?? 'N/A' }}</td>
            <td class="label">FATURA ORIG.</td>
            <td>{{ $creditNote->relatedInvoice->invoice_number ?? '-' }}</td>
            <td class="label">MOEDA</td>
            <td>MT</td>
            <td class="label">CÂMBIO</td>
            <td>1,00</td>
            <td class="label">DATA</td>
            <td>{{ $creditNote->invoice_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">TIPO</td>
            <td>CRÉDITO</td>
            <td class="label">VALOR ORIG.</td>
            <td>{{ $creditNote->relatedInvoice ? number_format($creditNote->relatedInvoice->total, 2, ',', '.') : '0,00' }} MT</td>
            <td class="label">PROCESSADO</td>
            <td>{{ now()->format('d/m/Y') }}</td>
            <td class="label" colspan="4">
                CRÉDITO APLICADO AO CLIENTE
            </td>
        </tr>
    </table>

    <table class="items-table no-break">
        <thead>
            <tr>
                <th style="width: 10%;">ITEM</th>
                <th style="width: 45%;">DESCRIÇÃO</th>
                <th style="width: 10%;">QUANT.</th>
                <th style="width: 15%;">PR. UNITÁRIO</th>
                <th style="width: 10%;">IVA</th>
                <th style="width: 10%;">VALOR CRÉDITO</th>
            </tr>
        </thead>
        <tbody>
            @php
                $subtotalGeral = 0;
                $totalTax = 0;
            @endphp
            @foreach($creditNote->items as $index => $item)
            @php
                $itemSubtotal = $item->quantity * $item->unit_price;
                $itemTax = $itemSubtotal * (($item->tax_rate ?? 0) / 100);
                $itemTotal = $itemSubtotal;

                $subtotalGeral += $itemSubtotal;
                $totalTax += $itemTax;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->description }}</strong>
                </td>
                <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }} MT</td>
                <td class="text-right">{{ number_format($itemTax, 2) }} MT</td>
                <td class="text-right">{{ number_format($itemTotal, 2) }} MT</td>
            </tr>
            @endforeach
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
                    @php
                        $taxGroups = $creditNote->items->groupBy('tax_rate');
                    @endphp
                    @foreach($taxGroups as $taxRate => $items)
                    @php
                        $taxIncidencia = $items->sum(function($item) {
                            return $item->quantity * $item->unit_price;
                        });
                        $taxAmount = $taxIncidencia * (($taxRate ?? 0) / 100);
                    @endphp
                    <tr>
                        <td>{{ number_format($taxRate ?? 0, 0) }}.00</td>
                        <td>{{ number_format($taxIncidencia, 2) }}</td>
                        <td>{{ number_format($taxAmount, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr style="font-weight: bold;">
                        <td>TOTAL</td>
                        <td>{{ number_format($subtotalGeral, 2) }}</td>
                        <td>{{ number_format($totalTax, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="totals-summary">
            <div class="section-title">TOTAIS</div>
            <table class="totals-table">
                <tr>
                    <td class="label">SUBTOTAL:</td>
                    <td class="text-right">{{ number_format($creditNote->subtotal, 2) }} MT</td>
                </tr>
                <tr>
                    <td class="label">TOTAL IVA:</td>
                    <td class="text-right">{{ number_format($creditNote->tax_amount, 2) }} MT</td>
                </tr>
                @if($creditNote->discount_amount > 0)
                <tr>
                    <td class="label">DESCONTO:</td>
                    <td class="text-right">-{{ number_format($creditNote->discount_amount, 2) }} MT</td>
                </tr>
                @endif
                <tr class="total-final">
                    <td style="color: #000000" class="label">TOTAL A CREDITAR:</td>
                    <td class="text-right">{{ number_format($creditNote->total, 2) }} MT</td>
                </tr>
            </table>
        </div>
    </div>

    @if($creditNote->notes)
    <div class="observations">
        OBSERVAÇÕES: {{ $creditNote->notes }}
    </div>
    @endif

    <!-- Signature Section -->
    <div class="clearfix signature-section">
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

    <div class="footer">
        <p>Este documento foi processado por computador e é válido sem assinatura e carimbo.</p>
        <p>Gerado em {{ now()->format('d/m/Y H:i:s') }} |
           sub360 v{{ config('app.version', '1.0') }} |  {{ $settings->company_name ?? config('app.name') }}</p>
        <p style="font-size: 8px; margin-top: 5px; color: #e74c3c;">
            <strong>NOTA DE CRÉDITO:</strong> Este documento representa um crédito a favor do cliente.
        </p>
    </div>
</body>
</html>
