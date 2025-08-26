<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Recibo {{ $receipt->receipt_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
            margin: 20px;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .header {
            margin-bottom: 20px;
            overflow: hidden;
        }

        .company-section {
            float: left;
            width: 60%;
        }

        .company-info {
            overflow: hidden;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 10px;
            line-height: 1.4;
            color: #666;
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

        .receipt-title {
            text-align: center;
            background-color: #4a9b8e;
            color: white;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0;
        }

        .main-info {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .main-info td {
            border: 1px solid #666;
            padding: 6px;
            font-size: 10px;
        }

        .main-info .label {
            background-color: #f5f5f5;
            font-weight: bold;
            width: 20%;
        }

        .amount-box {
            text-align: center;
            background-color: #f0f8f7;
            border: 3px solid #4a9b8e;
            padding: 20px;
            margin: 20px 0;
        }

        .amount-label {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #4a9b8e;
        }

        .amount-value {
            font-size: 24px;
            font-weight: bold;
            color: #2d3748;
        }

        .notes-section {
            background-color: #f9f9f9;
            border-left: 4px solid #4a9b8e;
            padding: 10px;
            margin: 15px 0;
            font-size: 10px;
        }

        .signatures {
            margin: 30px 0;
            overflow: hidden;
        }

        .signature-box {
            float: left;
            width: 48%;
            text-align: center;
            padding: 10px;
        }

        .signature-box:first-child {
            margin-right: 4%;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            height: 30px;
            margin-bottom: 8px;
        }

        .signature-label {
            font-weight: bold;
            font-size: 10px;
            color: #4a9b8e;
        }

        .signature-name {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .cancelled-stamp {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 100px;
            font-weight: bold;
            color: rgba(255, 0, 0, 0.1);
            z-index: -1;
        }

        @media print {
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    @if($receipt->status === 'cancelled')
        <div class="cancelled-stamp">CANCELADO</div>
    @endif

    <!-- Cabeçalho -->
    <div class="clearfix header">
        <div class="company-section">
            <div class="company-info">
                @php
                    $showLogo = false;
                    $logoSrc = '';
                    
                    if ($receipt->company && $receipt->company->logo) {
                        $logoPath = 'storage/' . $receipt->company->logo;
                        $logoFullPath = public_path($logoPath);
                        
                        if (file_exists($logoFullPath)) {
                            $showLogo = true;
                            
                            // Detectar contexto PDF vs HTML
                            $userAgent = request()->userAgent() ?? '';
                            $isPdfContext = str_contains($userAgent, 'dompdf') || 
                                           str_contains($userAgent, 'wkhtmltopdf') ||
                                           request()->has('pdf') ||
                                           app()->runningInConsole() ||
                                           str_contains(url()->current(), '/download') ||
                                           str_contains(url()->current(), '/pdf');
                            
                            $logoSrc = $isPdfContext ? $logoFullPath : asset($logoPath);
                        }
                    }
                @endphp

                @if($showLogo)
                    <img src="{{ $logoSrc }}" style="width: 150px; margin-bottom: 8px;" alt="Logo">
                @else
                    <div class="company-name">{{ $receipt->company->name ?? 'EMPRESA LIMITADA' }}</div>
                @endif

                <div class="company-details">
                    @if($receipt->company)
                        @if($receipt->company->tax_number)
                            <strong>Contribuinte Nº:</strong> {{ $receipt->company->tax_number }}<br>
                        @endif
                        {{ $receipt->company->address ?? 'Endereço da Empresa' }}<br>
                        {{ $receipt->company->country ?? 'Moçambique' }}<br>
                        @if($receipt->company->phone){{ $receipt->company->phone }}@endif
                        @if($receipt->company->email) | {{ $receipt->company->email }}@endif
                    @else
                        Endereço da Empresa<br>
                        Moçambique
                    @endif
                </div>
            </div>
        </div>

        <div class="client-section">
            <div class="client-box">
                <strong>Exmo.(s) Sr.(s)</strong><br>
                <strong>{{ $receipt->client->name }}</strong><br>
                <strong>Nº CLIENTE:</strong> {{ str_pad($receipt->client->id, 10, '0', STR_PAD_LEFT) }}<br>
                {{ $receipt->client->phone ?? 'N/A' }}<br>
                {{ $receipt->client->address ?? ($receipt->client->city ?? 'Moçambique') }}
            </div>
        </div>
    </div>

    <div class="receipt-title">
        RECIBO DE PAGAMENTO Nº {{ $receipt->receipt_number }}
    </div>

    <!-- Informações principais -->
    <table class="main-info">
        <tr>
            <td class="label">DATA PAGAMENTO</td>
            <td>{{ $receipt->payment_date->format('d/m/Y H:i') }}</td>
            <td class="label">MÉTODO</td>
            <td>{{ $receipt->payment_method_label }}</td>
        </tr>
        <tr>
            <td class="label">FATURA REF.</td>
            <td>{{ $receipt->invoice ? $receipt->invoice->invoice_number : 'N/A' }}</td>
            <td class="label">REFERÊNCIA</td>
            <td>{{ $receipt->transaction_reference ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">DATA EMISSÃO</td>
            <td>{{ $receipt->created_at->format('d/m/Y H:i') }}</td>
            <td class="label">EMITIDO POR</td>
            <td>{{ $receipt->issuedBy->name ?? 'Sistema' }}</td>
        </tr>
    </table>

    <!-- Valor recebido -->
    <div class="amount-box">
        <div class="amount-label">VALOR RECEBIDO</div>
        <div class="amount-value">{{ number_format($receipt->amount_paid, 2, ',', '.') }} MT</div>
    </div>

    @if($receipt->notes)
    <div class="notes-section">
        <strong>Observações:</strong> {{ $receipt->notes }}
    </div>
    @endif

    <!-- Assinaturas -->
    <div class="clearfix signatures">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">EMISSOR</div>
            <div class="signature-name">{{ $receipt->issuedBy->name ?? 'Sistema' }}</div>
        </div>
        
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">CLIENTE</div>
            <div class="signature-name">{{ $receipt->client->name }}</div>
        </div>
    </div>

    <!-- Rodapé -->
    <div class="footer">
        <p>Este recibo foi gerado automaticamente em {{ $receipt->created_at->format('d/m/Y H:i') }}</p>
        @if($receipt->status === 'cancelled')
            <p style="color: red; font-weight: bold; margin-top: 5px;">*** RECIBO CANCELADO ***</p>
        @endif
    </div>
</body>
</html>