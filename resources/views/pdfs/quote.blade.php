<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cotação {{ $quote->quote_number }}</title>
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

        .quote-header {
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

        .quote-title {
            background-color: #333;
            color: white;
            padding: 8px;
            font-size: 14px;
            font-weight: bold;
        }

        .quote-details {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .quote-details td {
            border: 1px solid #666;
            padding: 5px;
            font-size: 9px;
        }

        .quote-details .label {
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

        .validity-section {
            margin: 15px 0;
            font-size: 10px;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
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

        /* Status da cotação */
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            margin-left: 10px;
        }

        .status-draft { background-color: #6c757d; color: white; }
        .status-sent { background-color: #007bff; color: white; }
        .status-accepted { background-color: #28a745; color: white; }
        .status-rejected { background-color: #dc3545; color: white; }
        .status-expired { background-color: #fd7e14; color: white; }
    </style>
</head>
<body>
    <div class="clearfix header">
        <div class="company-section">
            <div class="company-info">
                @if(file_exists(public_path('logo.png')))
                <img src="{{ public_path('logo.png') }}" style="width: 200px;" alt="Logo">
                @else
                <div class="company-name">{{ config('company.name', config('app.name')) }}</div>
                <div class="company-slogan">beyond technology, intelligence.</div>
                @endif
                <div class="company-details">
                    <strong>Contribuinte Nº:</strong> {{ config('company.nuit', '123456789') }}<br>
                    {{ config('company.address', 'Av. Principal nº 123, R/C') }}<br>
                    {{ config('company.city', 'Maputo') }} - {{ config('company.country', 'Moçambique') }}<br>
                    {{ config('company.phone', '+258 84 123 4567') }} | {{ config('company.email', 'geral@empresa.co.mz') }}
                </div>
            </div>
        </div>

        <div class="client-section">
            <div class="client-box">
                <strong>Exmo.(s) Sr.(s)</strong><br>
                <strong>{{ $quote->client->name }}</strong><br>
                <strong>Nº CLIENTE:</strong> {{ str_pad($quote->client->id, 10, '0', STR_PAD_LEFT) }}<br>
                {{ $quote->client->phone ?? 'N/A' }}<br>
                {{ $quote->client->address ?? $quote->client->city ?? 'Moçambique' }}
            </div>
        </div>
    </div>

    <div class="quote-header">
        <div class="original-label">Original</div>
        <div class="quote-title">
            Cotação Nº {{ $quote->quote_number }}
            <span class="status-badge status-{{ $quote->status }}">{{ strtoupper($quote->status_label) }}</span>
        </div>
    </div>

    <table class="quote-details no-break">
        <tr>
            <td class="label">V/Nº CONTRIB.</td>
            <td>{{ $quote->client->nuit ?? 'N/A' }}</td>
            <td class="label">MOEDA</td>
            <td>MT</td>
            <td class="label">CÂMBIO</td>
            <td>1,00</td>
            <td class="label">DATA</td>
            <td>{{ $quote->quote_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">VÁLIDA ATÉ</td>
            <td>{{ $quote->valid_until->format('d/m/Y') }}</td>
            <td class="label">PRAZO</td>
            <td>{{ $quote->quote_date->diffInDays($quote->valid_until) }} dias</td>
            <td class="label">STATUS</td>
            <td colspan="3">{{ strtoupper($quote->status_label) }}</td>
        </tr>
    </table>

    @if($quote->valid_until->isPast() && $quote->status !== 'accepted')
    <div class="validity-section">
        <strong>⚠️ ATENÇÃO:</strong> Esta cotação expirou em {{ $quote->valid_until->format('d/m/Y') }}.
        Para renovar a validade, solicite uma nova cotação.
    </div>
    @endif

    <table class="items-table no-break">
        <thead>
            <tr>
                <th style="width: 15%;">CÓDIGO</th>
                <th style="width: 35%;">DESCRIÇÃO</th>
                <th style="width: 8%;">QUANT.</th>
                <th style="width: 12%;">PR. UNITÁRIO</th>
                <th style="width: 10%;">DESCONTOS</th>
                <th style="width: 10%;">IVA</th>
                <th style="width: 10%;">TOTAL LÍQUIDO</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quote->items as $item)
            @php
                $itemSubtotal = $item->quantity * $item->unit_price;
                $itemTax = $itemSubtotal * ($item->tax_rate / 100);
                $itemTotal = $itemSubtotal;
            @endphp
            <tr>
                <td>{{ $item->code ?? strtoupper(substr($item->name, 0, 8)) }}</td>
                <td>
                    <strong>{{ $item->name }}</strong>
                    @if($item->description)
                    <br><small>{{ $item->description }}</small>
                    @endif
                </td>
                <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">0.00</td>
                <td class="text-right">{{ number_format($itemTax, 2) }}</td>
                <td class="text-right">{{ number_format($itemTotal, 2) }}</td>
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
                        <th>MOTIVO ISENÇÃO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>17.00</td>
                        <td>{{ number_format($quote->subtotal, 2) }}</td>
                        <td>{{ number_format($quote->tax_amount, 2) }}</td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="totals-summary">
            <table class="totals-table">
                <tr>
                    <td class="label">SUBTOTAL</td>
                    <td class="text-right">{{ number_format($quote->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">DESCONTOS COMERCIAIS</td>
                    <td class="text-right">{{ number_format($quote->discount_amount ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">DESCONTO FINANCEIRO</td>
                    <td class="text-right">0.00</td>
                </tr>
                <tr>
                    <td class="label">IVA</td>
                    <td class="text-right">{{ number_format($quote->tax_amount, 2) }}</td>
                </tr>
                <tr class="total-final">
                    <td class="label">TOTAL (MT)</td>
                    <td class="text-right">{{ number_format($quote->total, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="observations">OBSERVAÇÕES</div>

    <div style="margin: 10px 0; font-size: 10px; line-height: 1.4;">
        @if($quote->notes)
            {{ $quote->notes }}<br><br>
        @endif

        <strong>Condições de Pagamento:</strong> Conforme acordo comercial<br>
        <strong>Prazo de Entrega:</strong> A combinar após confirmação do pedido<br>
        <strong>Validade da Proposta:</strong> {{ $quote->valid_until->format('d/m/Y') }}

        @if($quote->terms_conditions)
        <br><br><strong>Termos e Condições:</strong><br>
        {{ $quote->terms_conditions }}
        @endif
    </div>

    <div class="clearfix payment-section">
        @if(file_exists(public_path('bci.svg')))
        <div class="bank-logo">
            <img src="{{ public_path('bci.svg') }}" style="width: 20px;" alt="BCI">
        </div>
        @endif
        <div class="payment-details">
            O pagamento pode ser efectuado em numerário, cheque, depósito ou transferência<br>
            <strong>Banco:</strong> {{ config('company.bank', 'BCI') }} |
            <strong>Conta:</strong> {{ config('company.bank_account', '222 038 724 100 01') }} |
            <strong>NIB:</strong> {{ config('company.bank_nib', '0008 0000 2203 8724 101 13') }}
        </div>
    </div>

    <div class="footer">
        <strong>Data de Impressão:</strong> {{ now()->format('d/m/Y') }} |
        Documento processado por computador © {{ config('app.name') }} |
        <strong>Impresso por:</strong> {{ auth()->user()->name ?? 'ADMIN' }}
    </div>
</body>
</html>