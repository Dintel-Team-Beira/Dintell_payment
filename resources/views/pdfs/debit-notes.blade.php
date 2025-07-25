<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota de Débito {{ $debitNote->invoice_number }}</title>
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
            border-bottom: 2px solid #f59e0b;
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
            color: #f59e0b;
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
            color: #f59e0b;
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

        /* Debit Note Alert */
        .debit-alert {
            background-color: #fef3c7;
            border: 1px solid #fbbf24;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }

        .debit-alert h3 {
            color: #92400e;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .debit-alert p {
            color: #78350f;
            font-size: 11px;
        }

        /* Status indicator */
        .status-badge {
            background-color: #f59e0b;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
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
            background-color: #fff8e1;
            border-left: 4px solid #f59e0b;
            border-radius: 0 4px 4px 0;
        }

        .adjustment-reason h4 {
            font-size: 12px;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 5px;
        }

        .adjustment-reason p {
            font-size: 11px;
            color: #78350f;
            line-height: 1.4;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }

        .items-table th {
            background-color: #f59e0b;
            color: white;
            padding: 12px 8px;
            font-size: 11px;
            font-weight: bold;
            text-align: left;
            border: 1px solid #d97706;
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
            background-color: #fff3cd;
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
            background-color: #f59e0b !important;
            color: white !important;
            font-size: 14px !important;
            font-weight: bold !important;
        }

        /* Due date highlight */
        .due-date-section {
            background-color: #fef3c7;
            border: 1px solid #fbbf24;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }

        .due-date-section h4 {
            color: #92400e;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .due-date-section .due-date {
            color: #78350f;
            font-size: 16px;
            font-weight: bold;
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

        /* Currency formatting */
        .currency {
            font-family: 'Courier New', monospace;
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

        /* Warning styling */
        .warning-box {
            background-color: #fee2e2;
            border: 1px solid #fca5a5;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }

        .warning-box h4 {
            color: #991b1b;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .warning-box p {
            color: #7f1d1d;
            font-size: 11px;
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
                <div class="document-title">NOTA DE DÉBITO</div>
                <div class="document-number">{{ $debitNote->invoice_number }}</div>
                <div class="document-date">
                    Data: {{ $debitNote->invoice_date->format('d/m/Y') }}
                </div>
                <div class="status-badge">
                    @php
                        $statusLabels = [
                            'draft' => 'RASCUNHO',
                            'sent' => 'ENVIADA',
                            'paid' => 'PAGA',
                            'overdue' => 'VENCIDA',
                            'cancelled' => 'CANCELADA',
                        ];
                    @endphp
                    {{ $statusLabels[$debitNote->status] ?? strtoupper($debitNote->status) }}
                </div>
            </div>
        </div>

        <!-- Debit Note Alert -->
        <div class="debit-alert">
            <h3>⚠️ COBRANÇA ADICIONAL</h3>
            <p>Este documento representa uma cobrança adicional no valor especificado abaixo.</p>
        </div>

        <!-- Client Information -->
        <div class="client-section">
            <div class="section-title">DADOS DO CLIENTE</div>
            <div class="client-info">
                <div class="client-name">{{ $debitNote->client->name }}</div>
                <div class="client-details">
                    @if($debitNote->client->email)
                        Email: {{ $debitNote->client->email }}<br>
                    @endif
                    @if($debitNote->client->phone)
                        Telefone: {{ $debitNote->client->phone }}<br>
                    @endif
                    @if($debitNote->client->address)
                        Endereço: {{ $debitNote->client->address }}<br>
                    @endif
                    @if($debitNote->client->nuit)
                        NUIT: {{ $debitNote->client->nuit }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Related Invoice (if exists) -->
        @if($debitNote->relatedInvoice)
        <div class="related-invoice">
            <h4>📄 FATURA RELACIONADA</h4>
            <p>
                <strong>Número:</strong> {{ $debitNote->relatedInvoice->invoice_number }} |
                <strong>Data:</strong> {{ $debitNote->relatedInvoice->invoice_date->format('d/m/Y') }} |
                <strong>Valor Original:</strong> <span class="currency">{{ number_format($debitNote->relatedInvoice->total, 2, ',', '.') }} MT</span>
            </p>
        </div>
        @endif

        <!-- Due Date -->
        @if($debitNote->due_date)
        <div class="due-date-section">
            <h4>📅 VENCIMENTO</h4>
            <div class="due-date">{{ $debitNote->due_date->format('d/m/Y') }}</div>
            @php
                $daysUntilDue = $debitNote->due_date->diffInDays(now(), false);
            @endphp
            @if($daysUntilDue > 0)
                <p style="color: #dc2626; font-weight: bold; margin-top: 5px;">
                    ⚠️ VENCIDA HÁ {{ abs($daysUntilDue) }} {{ abs($daysUntilDue) == 1 ? 'DIA' : 'DIAS' }}
                </p>
            @elseif($daysUntilDue < 0)
                <p style="color: #059669; margin-top: 5px;">
                    Vence em {{ abs($daysUntilDue) }} {{ abs($daysUntilDue) == 1 ? 'dia' : 'dias' }}
                </p>
            @endif
        </div>
        @endif

        <!-- Adjustment Reason -->
        <div class="adjustment-reason">
            <h4>MOTIVO DA COBRANÇA</h4>
            <p>{{ $debitNote->adjustment_reason }}</p>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 55%;">DESCRIÇÃO</th>
                    <th style="width: 10%;" class="text-center">QTDE</th>
                    <th style="width: 15%;" class="text-right">VALOR UNIT.</th>
                    <th style="width: 15%;" class="text-right">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($debitNote->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                    <td class="text-right currency">{{ number_format($item->unit_price, 2, ',', '.') }} MT</td>
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
                @if($debitNote->notes)
                <div class="notes-section">
                    <div class="section-title">OBSERVAÇÕES</div>
                    <div class="notes-content">
                        {{ $debitNote->notes }}
                    </div>
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
            </div>
            <div class="totals-right">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal:</td>
                        <td class="value currency">{{ number_format($debitNote->subtotal, 2, ',', '.') }} MT</td>
                    </tr>
                    <tr>
                        <td class="label">IVA:</td>
                        <td class="value currency">{{ number_format($debitNote->tax_amount, 2, ',', '.') }} MT</td>
                    </tr>
                    @if($debitNote->discount_amount > 0)
                    <tr>
                        <td class="label">Desconto:</td>
                        <td class="value currency">-{{ number_format($debitNote->discount_amount, 2, ',', '.') }} MT</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label total-final">TOTAL A COBRAR:</td>
                        <td class="value total-final currency">{{ number_format($debitNote->total, 2, ',', '.') }} MT</td>
                    </tr>
                </table>

                <!-- Payment Status -->
                @if($debitNote->status === 'paid')
                <div style="margin-top: 20px; padding: 10px; background-color: #d1fae5; border: 1px solid #34d399; border-radius: 4px; text-align: center;">
                    <p style="color: #065f46; font-weight: bold; margin: 0;">✅ PAGO</p>
                    @if($debitNote->paid_at)
                    <p style="color: #047857; font-size: 10px; margin: 5px 0 0 0;">
                        Pago em: {{ $debitNote->paid_at->format('d/m/Y H:i') }}
                    </p>
                    @endif
                </div>
                @else
                <div style="margin-top: 20px; padding: 10px; background-color: #fef3c7; border: 1px solid #fbbf24; border-radius: 4px; text-align: center;">
                    <p style="color: #92400e; font-weight: bold; margin: 0;">⏳ PENDENTE</p>
                    <p style="color: #78350f; font-size: 10px; margin: 5px 0 0 0;">
                        Aguardando pagamento
                    </p>
                </div>
                @endif
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
                    {{ $debitNote->client->name }}
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Este documento foi gerado eletronicamente em {{ now()->format('d/m/Y H:i:s') }}<br>
                Nota de Débito {{ $debitNote->invoice_number }} - {{ $settings->company_name ?? 'Sistema de Faturação' }}
            </p>
            <p style="margin-top: 10px; font-size: 9px; color: #999;">
                Em caso de dúvidas, entre em contato conosco através dos dados informados no cabeçalho deste documento.
            </p>
        </div>
    </div>
</body>
</html>
