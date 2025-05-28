<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Aviso de Expiração - {{ $notice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: A4;
            margin: 10mm; /* Margens reduzidas para aproveitar o espaço */
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px; /* Fonte base aumentada */
            line-height: 1.4;
            color: #333;
            margin: 0;
        }

        .header {
            border-bottom: 2px solid #1a365d;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .company-info {
            float: left;
            width: 60%;
        }

        .logo {
            font-size: 28px; /* Aumentado para destaque */
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 5px;
        }

        .slogan {
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
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .notice-title {
            text-align: center;
            font-size: 12px; /* Aumentado */
            font-weight: bold;
            color: #1a365d;
            margin: 20px 0 15px;
            padding: 6px;
            background-color: #f8f9fa;
            border-left: 4px solid #ffc107;
        }

        .notice-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .notice-details th,
        .notice-details td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px; /* Aumentado */
        }

        .notice-details th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #555;
        }

        .alert-section {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            margin: 15px 0;
            border-radius: 5px;
        }

        .alert-title {
            font-size: 14px; /* Aumentado */
            font-weight: bold;
            color: #856404;
            margin-bottom: 8px;
        }

        .subscription-details {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .subscription-details td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            font-size: 11px; /* Aumentado */
        }

        .subscription-details td:first-child {
            font-weight: bold;
            width: 40%;
            background-color: #f8f9fa;
        }

        .amount-section {
            background-color: #f8f9fa;
            padding: 10px;
            margin: 15px 0;
            text-align: center;
            border-radius: 5px;
        }

        .amount-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .amount-value {
            font-size: 14px; /* Aumentado */
            font-weight: bold;
            color: #1a365d;
        }

        .payment-info {
            border: 1px solid #ddd;
            padding: 12px;
            margin: 15px 0;
            background-color: #f9f9f9;
        }

        .payment-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #1a365d;
            font-size: 12px; /* Aumentado */
        }

        .bank-details {
            margin-left: 15px;
            font-size: 11px;
        }

        .important-note {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 12px;
            margin: 15px 0;
            color: #721c24;
            font-size: 11px; /* Aumentado */
        }

        .footer {
            border-top: 1px solid #ddd;
            padding-top: 12px;
            margin-top: 20px;
            font-size: 10px; /* Aumentado */
            color: #666;
            text-align: center;
        }

        /* Evitar quebras de página dentro das seções */
        .header, .notice-title, .notice-details, .alert-section, .subscription-details, .amount-section, .payment-info, .important-note, .footer {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="clearfix header">
        <div class="company-info">
            <image src="https://beyondbusiness.co.mz/logo.png" alt="DINTELL Logo" style="width: 150px; height: auto;">
            <div class="company-details">
                <strong>{{ $company['name'] }}</strong><br>
                Contribuinte Nº: {{ $company['nuit'] }}<br>
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
                {{ $client->name }}<br>
                <strong>Nº CLIENTE:</strong> {{ $client->id }}<br>
                {{ $client->phone ?? '' }}<br>
                {{ $client->address ?? 'Maputo' }}
            </div>
        </div>
    </div>

    <div class="notice-title">
        AVISO DE EXPIRAÇÃO DE SUBSCRIÇÃO<br>
        Nº {{ $notice_number }}
    </div>

    <table class="notice-details">
        <tr>
            <th>DATA DO AVISO</th>
            <th>VENCIMENTO</th>
            <th>DIAS RESTANTES</th>
            <th>SITUAÇÃO</th>
        </tr>
        <tr>
            <td>{{ $notice_date->format('d/m/Y') }}</td>
            <td>{{ $expiration_date->format('d/m/Y') }}</td>
            <td style="color: #dc3545; font-weight: bold;">{{ $days_left }} dias</td>
            <td style="color: #ffc107; font-weight: bold;">PRÓXIMO DO VENCIMENTO</td>
        </tr>
    </table>

    <div class="alert-section">
        <div class="alert-title">ATENÇÃO ESPECIAL</div>
        <p>Sua subscrição expira em <strong>{{ $days_left }} dias</strong>. Para evitar a interrupção dos serviços, proceda com a renovação o quanto antes.</p>
    </div>

    <table class="subscription-details">
        <tr>
            <td>Domínio:</td>
            <td><strong>{{ $subscription->domain }}</strong></td>
        </tr>
        <tr>
            <td>Plano Contratado:</td>
            <td>{{ $plan->name }}</td>
        </tr>
        <tr>
            <td>Período de Subscrição:</td>
            <td>{{ $subscription->starts_at->format('d/m/Y') }} a {{ $subscription->ends_at->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>Status Atual:</td>
            <td style="color: #28a745; font-weight: bold;">ATIVO</td>
        </tr>
    </table>

    <div class="amount-section">
        <div class="amount-label">Valor para Renovação</div>
        <div class="amount-value">MT {{ number_format($renewal_amount, 2) }}</div>
        <div style="font-size: 11px; color: #666; margin-top: 8px;">
            (Inclui IVA de 16%)
        </div>
    </div>

    <div style="border: 1px solid #ddd; padding: 12px; margin: 15px 0;">
        <table style="width: 100%; font-size: 11px;">
            <tr style="background-color: #f8f9fa;">
                <th style="text-align: left; padding: 6px;">SUBTOTAL</th>
                <td style="text-align: right; padding: 6px;">{{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding: 6px;">IVA (16%)</th>
                <td style="text-align: right; padding: 6px;">{{ number_format($iva_amount, 2) }}</td>
            </tr>
            <tr style="background-color: #1a365d; color: white; font-weight: bold;">
                <th style="text-align: left; padding: 8px;">TOTAL (MT)</th>
                <td style="text-align: right; padding: 8px;">{{ number_format($total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="payment-info">
        <div class="payment-title">FORMAS DE PAGAMENTO</div>
        <p>O pagamento pode ser efectuado em numerário, cheque, depósito ou transferência</p>
        <div class="bank-details">
            <strong>Banco:</strong> {{ $company['bank_name'] }}<br>
            <strong>Moeda:</strong> MT<br>
            <strong>Conta:</strong> {{ $company['bank_account'] }}<br>
            <strong>NIB:</strong> {{ $company['bank_nib'] }}
        </div>
    </div>

    <div class="important-note">
        <strong>IMPORTANTE:</strong> Após o vencimento, os serviços serão automaticamente suspensos. Para reativação, será necessário efetuar o pagamento e aguardar confirmação da equipe técnica.
    </div>

    <div class="footer">
        <p>Data de Impressão: {{ $notice_date->format('d/m/Y') }}</p>
        <p>Documento processado por computador | Impresso por: DINTELL ADMIN</p>
        <p style="margin-top: 8px;">Para dúvidas ou esclarecimentos, entre em contacto connosco através do email {{ $company['email'] }} ou telefone {{ $company['phone'] }}</p>
    </div>
</body>
</html>