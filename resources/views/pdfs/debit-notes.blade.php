<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Nota de Débito {{ $debitNote->invoice_number }}</title>
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
            color: #0c2572;
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
            border: 1px solid #0c2572;
            padding: 10px;
            /* background-color: #fff8e1; */
            font-size: 10px;
        }

        .document-header {
            text-align: left;
            margin: 15px 0;
        }

        .original-label {
            border: 1px solid #0c2572;
            display: inline-block;
            padding: 4px 10px;
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #0c2572;
        }

        .document-title {
            background-color: #0c2572;
            color: white;
            padding: 8px;
            font-size: 14px;
            font-weight: bold;
        }

        /* Debit Note Alert */
        .debit-alert {
            background-color: #fef3c7;
            border: 1px solid #0c2572;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }

        .debit-alert h3 {
            color: #92400e;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .debit-alert p {
            color: #78350f;
            font-size: 10px;
        }

        .document-details {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .document-details td {
            border: 1px solid #0c2572;
            padding: 5px;
            font-size: 9px;
        }

        .document-details .label {
            /* background-color: #fff8e1; */
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

        /* Due Date Section */
        .due-date-section {
            background-color: #fef3c7;
            border: 1px solid #0c2572;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }

        .due-date-section h4 {
            color: #0e4192;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .due-date-section .due-date {
            color: #0f1678;
            font-size: 14px;
            font-weight: bold;
        }

        .overdue-warning {
            color: #dc2626;
            font-weight: bold;
            margin-top: 5px;
        }

        .due-soon {
            color: #059669;
            margin-top: 5px;
        }

        /* Adjustment Reason */
        .adjustment-reason {
            margin: 20px 0;
            padding: 15px;
            background-color: #e1efff;
            border-left: 4px solid #0c2572;
            border-radius: 0 4px 4px 0;
        }

        .adjustment-reason h4 {
            font-size: 11px;
            font-weight: bold;
            color: #0e2292;
            margin-bottom: 5px;
        }

        .adjustment-reason p {
            font-size: 10px;
            color: #1b0f78;
            line-height: 1.4;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #0c2572;
            padding: 6px;
            font-size: 10px;
        }

        .items-table th {
            background-color: #0c2572;
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
            border: 1px solid #0c2572;
            padding: 4px;
            font-size: 9px;
            text-align: center;
        }

        .iva-table th {
            /* background-color: #fff8e1; */
            font-weight: bold;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            border: 1px solid #0c2572;
            padding: 5px;
            font-size: 10px;
        }

        .totals-table .label {
            /* background-color: #fff8e1; */
            font-weight: bold;
        }

        .total-final {
            background-color: #0c2572;
            color: white;
            font-weight: bold;
            font-size: 11px;
        }

        .observations {
            background-color: #0c2572;
            color: white;
            padding: 6px 10px;
            margin: 15px 0;
            font-size: 10px;
            font-weight: bold;
        }

        /* Payment Status */
        .payment-status {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            font-size: 10px;
        }

        .status-paid {
            background-color: #d1fae5;
            border: 1px solid #34d399;
            color: #065f46;
        }

        .status-pending {
            background-color: #fef3c7;
            border: 1px solid #0c2572;
            color: #92400e;
        }

        /* Warning Box */
        .warning-box {
            background-color: #fee2e2;
            border: 1px solid #fca5a5;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            font-size: 10px;
        }

        .warning-box h4 {
            color: #991b1b;
            font-size: 11px;
            margin-bottom: 5px;
        }

        .warning-box p {
            color: #7f1d1d;
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

        .status-draft { background-color: #6c757d; color: white; }
        .status-sent { background-color: #ffc107; color: black; }
        .status-paid { background-color: #28a745; color: white; }
        .status-overdue { background-color: #dc3545; color: white; }
        .status-cancelled { background-color: #6c757d; color: white; }

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
                <strong>{{ $debitNote->client->name }}</strong><br>
                <strong>Nº CLIENTE:</strong> {{ str_pad($debitNote->client->id, 10, '0', STR_PAD_LEFT) }}<br>
                {{ $debitNote->client->phone ?? 'N/A' }}<br>
                {{ $debitNote->client->address ?? $debitNote->client->city ?? 'Moçambique' }}
            </div>
        </div>
    </div>

    <div class="document-header">
        <div class="original-label">Original</div>
        <div class="document-title">
            Nota de Débito Nº {{ $debitNote->invoice_number }}
            {{-- @php
                $statusLabels = [
                    'draft' => 'RASCUNHO',
                    'sent' => 'ENVIADA',
                    'paid' => 'PAGA',
                    'overdue' => 'VENCIDA',
                    'cancelled' => 'CANCELADA'
                ];
            @endphp --}}
            {{-- <span class="status-badge status-{{ $debitNote->status }}">{{ $statusLabels[$debitNote->status] ?? strtoupper($debitNote->status) }}</span> --}}
        </div>
    </div>

    <!-- Debit Note Alert -->
    <!-- <div class="debit-alert">
        <h3>⚠️ COBRANÇA ADICIONAL</h3>
        <p>Este documento representa uma cobrança adicional no valor especificado abaixo.</p>
    </div> -->

    <!-- Related Invoice (if exists) -->
    @if($debitNote->relatedInvoice)
    <div class="related-invoice">
        <h4> FATURA RELACIONADA</h4>
        <p>
            <strong>Número:</strong> {{ $debitNote->relatedInvoice->invoice_number }} |
            <strong>Data:</strong> {{ $debitNote->relatedInvoice->invoice_date->format('d/m/Y') }} |
            <strong>Valor Original:</strong> {{ number_format($debitNote->relatedInvoice->total, 2, ',', '.') }} MT
        </p>
    </div>
    @endif

    <!-- Due Date -->
    <!-- @if($debitNote->due_date)
    <div class="due-date-section">
        <h4>📅 VENCIMENTO</h4>
        <div class="due-date">{{ $debitNote->due_date->format('d/m/Y') }}</div>
        @php
            $daysUntilDue = $debitNote->due_date->diffInDays(now(), false);
        @endphp
        @if($daysUntilDue > 0)
            <p class="overdue-warning">
                ⚠️ VENCIDA HÁ {{ abs($daysUntilDue) }} {{ abs($daysUntilDue) == 1 ? 'DIA' : 'DIAS' }}
            </p>
        @elseif($daysUntilDue < 0)
            <p class="due-soon">
                Vence em {{ abs($daysUntilDue) }} {{ abs($daysUntilDue) == 1 ? 'dia' : 'dias' }}
            </p>
        @endif
    </div>
    @endif -->

    <!-- Adjustment Reason -->
    <div class="adjustment-reason">
        <h4>MOTIVO DA COBRANÇA</h4>
        <p>{{ $debitNote->adjustment_reason }}</p>
    </div>

    <table class="document-details no-break">
        <tr>
            <td class="label">V/Nº CONTRIB.</td>
            <td>{{ $debitNote->client->nuit ?? 'N/A' }}</td>
            <td class="label">FATURA ORIG.</td>
            <td>{{ $debitNote->relatedInvoice->invoice_number ?? '-' }}</td>
            <td class="label">MOEDA</td>
            <td>MT</td>
            <td class="label">CÂMBIO</td>
            <td>1,00</td>
            <td class="label">DATA</td>
            <td>{{ $debitNote->invoice_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">TIPO</td>
            <td>DÉBITO</td>
            <td class="label">VALOR ORIG.</td>
            <td>{{ $debitNote->relatedInvoice ? number_format($debitNote->relatedInvoice->total, 2, ',', '.') : '0,00' }} MT</td>
            <td class="label">VENCIMENTO</td>
            <td>{{ $debitNote->due_date ? $debitNote->due_date->format('d/m/Y') : 'N/A' }}</td>
            <td class="label" colspan="4">
                @if($debitNote->due_date && $debitNote->due_date->isToday())
                    VENCE HOJE
                @elseif($debitNote->due_date && $debitNote->due_date->isFuture())
                    {{ $debitNote->due_date->diffInDays() }} DIAS PARA VENCIMENTO
                @elseif($debitNote->due_date)
                    VENCIDA HÁ {{ $debitNote->due_date->diffInDays() }} DIAS
                @else
                    COBRANÇA ADICIONAL
                @endif
            </td>
        </tr>
    </table>

    <table class="items-table no-break">
        <thead>
            <tr>
                <th style="width: 10%;">ITEM</th>
                <th style="width: 45%;">DESCRIÇÃO</th>
                <th style="width: 10%;">QUANT.</th>
                <th style="width: 15%;">VALOR UNITÁRIO</th>
                <th style="width: 10%;">IVA</th>
                <th style="width: 10%;">VALOR DÉBITO</th>
            </tr>
        </thead>
        <tbody>
            @php
                $subtotalGeral = 0;
                $totalTax = 0;
            @endphp
            @foreach($debitNote->items as $index => $item)
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
                <td class="text-right">{{ number_format($item->quantity, 0) }}</td>
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
                        $taxGroups = $debitNote->items->groupBy('tax_rate');
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
                    <td class="text-right">{{ number_format($debitNote->subtotal, 2) }} MT</td>
                </tr>
                <tr>
                    <td class="label">TOTAL IVA:</td>
                    <td class="text-right">{{ number_format($debitNote->tax_amount, 2) }} MT</td>
                </tr>
                @if($debitNote->discount_amount > 0)
                <tr>
                    <td class="label">DESCONTO:</td>
                    <td class="text-right">-{{ number_format($debitNote->discount_amount, 2) }} MT</td>
                </tr>
                @endif
                <tr class="total-final">
                    <td style="color: #ffffff" class="label">TOTAL A COBRAR:</td>
                    <td class="text-right">{{ number_format($debitNote->total, 2) }} MT</td>
                </tr>
            </table>

            <!-- Payment Status -->
            {{-- @if($debitNote->status === 'paid')
            <div class="payment-status status-paid">
                <p style="font-weight: bold; margin: 0;"> PAGO</p>
                @if($debitNote->paid_at)
                <p style="margin: 5px 0 0 0;">
                    Pago em: {{ $debitNote->paid_at->format('d/m/Y H:i') }}
                </p>
                @endif
            </div>
            @else
            <div class="payment-status status-pending">
                <p style="font-weight: bold; margin: 0;"> PENDENTE</p>
                <p style="margin: 5px 0 0 0;">
                    Aguardando pagamento
                </p>
            </div>
            @endif --}}
        </div>
    </div>

    @if($debitNote->notes)
    <div class="observations">
        OBSERVAÇÕES: {{ $debitNote->notes }}
    </div>
    @endif

    <!-- Payment Instructions -->
    <div class="warning-box">
        <h4>⚠️ IMPORTANTE - INSTRUÇÕES DE PAGAMENTO</h4>
        <p>
            Esta cobrança adicional deve ser paga até a data de vencimento indicada.
            O não pagamento dentro do prazo pode resultar em juros e multas adicionais.
        </p>
    </div>

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
                {{ $debitNote->client->name }}
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Este documento foi processado por computador e é válido sem assinatura e carimbo.</p>
        <p>Gerado em {{ now()->format('d/m/Y H:i:s') }} |
           sub360 v{{ config('app.version', '1.0') }} |
           {{ $settings->company_name ?? config('app.name') }}</p>
        <p style="font-size: 8px; margin-top: 5px; color: #0c2572;">
            <strong>NOTA DE DÉBITO:</strong> Este documento representa uma cobrança adicional ao cliente.
        </p>
        <p style="font-size: 8px; margin-top: 5px; color: #999;">
            Em caso de dúvidas, entre em contato conosco através dos dados informados no cabeçalho deste documento.
        </p>
    </div>
</body>
</html>
