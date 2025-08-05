<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Faturas</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 landscape;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0c2572;
            padding-bottom: 15px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #0c2572;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
        }

        .report-info {
            font-size: 10px;
            color: #666;
        }

        .filters {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 8px;
        }

        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .summary-box {
            flex: 1;
            background-color: #f8f9fa;
            padding: 10px;
            margin: 0 5px;
            border-radius: 4px;
            text-align: center;
        }

        .summary-label {
            font-size: 8px;
            color: #666;
            margin-bottom: 3px;
        }

        .summary-value {
            font-size: 11px;
            font-weight: bold;
            color: #0c2572;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #666;
            padding: 4px;
            text-align: left;
            font-size: 8px;
        }

        .table th {
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

        .status {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-draft { background-color: #fef3c7; color: #92400e; }
        .status-sent { background-color: #dbeafe; color: #1e40af; }
        .status-paid { background-color: #d1fae5; color: #065f46; }
        .status-overdue { background-color: #fee2e2; color: #991b1b; }
        .status-cancelled { background-color: #f3f4f6; color: #4b5563; }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 7px;
            text-align: center;
            color: #666;
        }

        .totals-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <div class="company-name">
            {{ config('app.company_name', 'Sua Empresa') }}
        </div>
        <div class="report-title">RELATÓRIO DE FATURAS</div>
        <div class="report-info">
            {{ $period }}<br>
            Gerado em {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>

    <!-- Filtros Aplicados -->
    @if(!empty(array_filter($filters)))
    <div class="filters">
        <strong>Filtros Aplicados:</strong>
        @if(isset($filters['status']) && $filters['status'])
            Status: {{ ucfirst($filters['status']) }} |
        @endif
        @if(isset($filters['client_id']) && $filters['client_id'])
            Cliente ID: {{ $filters['client_id'] }} |
        @endif
        @if(isset($filters['date_from']) && $filters['date_from'])
            Data inicial: {{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }} |
        @endif
        @if(isset($filters['date_to']) && $filters['date_to'])
            Data final: {{ \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') }}
        @endif
    </div>
    @endif

    <!-- Resumo -->
    <div class="summary">
        <div class="summary-box">
            <div class="summary-label">Total de Faturas</div>
            <div class="summary-value">{{ $totals['count'] }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Subtotal</div>
            <div class="summary-value">{{ number_format($totals['subtotal'], 2, ',', '.') }} MT</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">IVA</div>
            <div class="summary-value">{{ number_format($totals['tax_amount'], 2, ',', '.') }} MT</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Total Geral</div>
            <div class="summary-value">{{ number_format($totals['total'], 2, ',', '.') }} MT</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Valor Pago</div>
            <div class="summary-value">{{ number_format($totals['paid_amount'], 2, ',', '.') }} MT</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Pendente</div>
            <div class="summary-value">{{ number_format($totals['pending_amount'], 2, ',', '.') }} MT</div>
        </div>
    </div>

    <!-- Tabela de Faturas -->
    <table class="table">
        <thead>
            <tr>
                <th style="width: 8%;">Nº Fatura</th>
                <th style="width: 20%;">Cliente</th>
                <th style="width: 8%;">Data</th>
                <th style="width: 8%;">Vencimento</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 10%;">Subtotal</th>
                <th style="width: 8%;">IVA</th>
                <th style="width: 10%;">Total</th>
                <th style="width: 10%;">Pago</th>
                <th style="width: 10%;">Pendente</th>
            </tr>
