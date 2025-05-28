<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Aviso de Expiração - {{ $notice_number }}</title>
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

        .company-info {
            float: left;
            width: 60%;
        }

        .logo {
            width: 150px;
            height: auto;
            margin-bottom: 5px;
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
            font-size: 18px;
            font-weight: bold;
            color: white;
            margin: 20px 0 15px;
            padding: 12px;
            background-color: #dc3545;
            border-radius: 5px;
        }

        .status-alert {
            background-color: #f8d7da;
            border: 2px solid #dc3545;
            padding: 12px;
            margin: 15px 0;
            text-align: center;
            border-radius: 5px;
        }

        .status-alert h3 {
            color: #721c24;
            margin-bottom: 8px;
            font-size: 14px;
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
            font-size: 11px;
        }

        .notice-details th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #555;
        }

        .expired-status {
            background-color: #dc3545;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        .grace-period-section {
            background-color: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }

        .grace-title {
            font-size: 14px;
            font-weight: bold;
            color: #0c5460;
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
            font-size: 11px;
        }

        .subscription-details td:first-child {
            font-weight: bold;
            width: 40%;
            background-color: #f8f9fa;
        }

        .amount-section {
            background-color: #f8f9fa;
            padding: 15px;
            margin: 15px 0;
            text-align: center;
            border-radius: 5px;
        }

        .amount-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .amount-value {
            font-size: 20px;
            font-weight: bold;
            color: #1a365d;
        }

        .timeline-section {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 15px 0;
            background-color: #f9f9f9;
            page-break-before: always; /* Força a quebra para a segunda página */
        }

        .timeline-title {
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .timeline-item {
            margin: 8px 0;
            padding: 8px;
            border-left: 4px solid #ddd;
            background-color: white;
        }

        .timeline-item.expired {
            border-left-color: #dc3545;
        }

        .timeline-item.grace {
            border-left-color: #17a2b8;
        }

        .timeline-item.suspended {
            border-left-color: #6c757d;
        }

        .consequences-section {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 12px;
            margin: 15px 0;
        }

        .consequences-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 8px;
            font-size: 14px;
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
            font-size: 12px;
        }

        .bank-details {
            margin-left: 15px;
            font-size: 11px;
        }

        .urgent-notice {
            background-color: #dc3545;
            color: white;
            padding: 12px;
            text-align: center;
            margin: 15px 0;
            font-weight: bold;
            border-radius: 5px;
        }

        .important-notice {
            background-color: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 12px;
            margin: 15px 0;
        }

        .footer {
            border-top: 1px solid #ddd;
            padding-top: 12px;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
            text-align: center;
        }

        /* Evitar quebras indesejadas dentro das seções */
        .header, .notice-title, .status-alert, .notice-details, .grace-period-section, .subscription-details, .amount-section, .timeline-section, .consequences-section, .payment-info, .urgent-notice, .important-notice, .footer {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <!-- Página 1 -->
    <div class="clearfix header">
        <div class="company-info">
            <img src="https://beyondbusiness.co.mz/logo.png" alt="DINTELL Logo" class="logo">
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
        ⚠️ AVISO DE SUBSCRIÇÃO EXPIRADA<br>
        Nº {{ $notice_number }}
    </div>

    <div class="status-alert">
        <h3>SUBSCRIÇÃO EXPIRADA</h3>
        <p>Esta subscrição expirou em {{ $expiration_date->format('d/m/Y H:i') }} e requer renovação urgente.</p>
    </div>

    <table class="notice-details">
        <tr>
            <th>DATA DO AVISO</th>
            <th>DATA DE EXPIRAÇÃO</th>
            <th>PERÍODO DE CARÊNCIA</th>
            <th>STATUS</th>
        </tr>
        <tr>
            <td>{{ $notice_date->format('d/m/Y') }}</td>
            <td style="color: #dc3545; font-weight: bold;">{{ $expiration_date->format('d/m/Y H:i') }}</td>
            <td>Até {{ $grace_period_end->format('d/m/Y') }}</td>
            <td class="expired-status">EXPIRADO</td>
        </tr>
    </table>

    <div class="grace-period-section">
        <div class="grace-title">⏰ PERÍODO DE CARÊNCIA ATIVO</div>
        <p>Os serviços continuarão funcionando durante <strong>{{ $grace_period_days }} dias</strong> após a expiração.</p>
        <p><strong>Fim do período de carência:</strong> {{ $grace_period_end->format('d/m/Y') }}</p>
        <p style="color: #721c24; font-weight: bold;">Após esta data, todos os serviços serão suspensos.</p>
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
            <td style="color: #dc3545; font-weight: bold;">EXPIRADO</td>
        </tr>
    </table>

    <div class="amount-section">
        <div class="amount-label">Valor para Renovação Imediata</div>
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
            @if($grace_period_end->isPast())
            <tr style="background-color: #dc3545; color: white; font-weight: bold;">
                <th style="text-align: left; padding: 8px;">TAXA DE ATRASO (5%)</th>
                <td style="text-align: right; padding: 8px;">{{ number_format($late_fee, 2) }}</td>
            </tr>
            <tr style="background-color: #721c24; color: white; font-weight: bold;">
                <th style="text-align: left; padding: 8px;">TOTAL COM ATRASO (MT)</th>
                <td style="text-align: right; padding: 8px;">{{ number_format($total_with_late_fee, 2) }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Página 2 -->
    <div class="timeline-section">
        <div class="timeline-title">CRONOGRAMA DE AÇÕES</div>
        <div class="timeline-item expired">
            <strong>{{ $expiration_date->format('d/m/Y') }}</strong> - Subscrição Expirada<br>
            <small>Período de carência iniciado automaticamente</small>
        </div>
        <div class="timeline-item grace">
            <strong>{{ $grace_period_end->format('d/m/Y') }}</strong> - Fim do Período de Carência<br>
            <small>Últimos {{ $grace_period_days }} dias para renovar sem penalidades</small>
        </div>
        <div class="timeline-item suspended">
            <strong>Após {{ $grace_period_end->format('d/m/Y') }}</strong> - Suspensão Total<br>
            <small>Todos os serviços serão completamente suspensos</small>
        </div>
    </div>

    <div class="consequences-section">
        <div class="consequences-title">⚠️ CONSEQUÊNCIAS DA NÃO RENOVAÇÃO</div>
        <ul style="margin-left: 20px;">
            <li>Website ficará completamente offline</li>
            <li>Emails corporativos serão suspensos</li>
            <li>Acesso ao painel administrativo será bloqueado</li>
            <li>Backup de dados será mantido por apenas 30 dias</li>
            <li>Taxa adicional de 5% será aplicada após o período de carência</li>
            <li>Possível perda permanente de dados após 30 dias</li>
        </ul>
    </div>

    <div class="payment-info">
        <div class="payment-title">FORMAS DE PAGAMENTO URGENTE</div>
        <p><strong>Para renovação imediata, utilize uma das seguintes opções:</strong></p>
        <div class="bank-details">
            <strong>1. Transferência Bancária (Recomendado):</strong><br>
            Banco: {{ $company['bank_name'] }}<br>
            Moeda: MT<br>
            Conta: {{ $company['bank_account'] }}<br>
            NIB: {{ $company['bank_nib'] }}<br><br>
            <strong>2. Depósito Bancário:</strong><br>
            Enviar comprovativo para {{ $company['email'] }}<br><br>
            <strong>3. Pagamento Presencial:</strong><br>
            {{ $company['address_maputo'] }}<br>
            {{ $company['address_beira'] }}<br>
            Horário: Segunda a Sexta, 08:00 às 17:00
        </div>
    </div>

    <div class="urgent-notice">
        🚨 AÇÃO URGENTE REQUERIDA 🚨<br>
        Entre em contacto imediatamente: {{ $company['phone'] }} | {{ $company['email'] }}
    </div>

    <div class="important-notice">
        <strong>IMPORTANTE:</strong> Este aviso serve como notificação oficial da expiração da sua subscrição.
        A DINTELL não se responsabiliza por eventuais perdas de dados ou interrupções de serviço
        decorrentes da não renovação dentro do prazo estabelecido.
    </div>

    <div class="footer">
        <p>Data de Impressão: {{ $notice_date->format('d/m/Y H:i') }}</p>
        <p>Documento processado por computador | Impresso por: DINTELL ADMIN</p>
        <p style="margin-top: 8px;">
            <strong>Contacto de Emergência:</strong> {{ $company['phone'] }} | {{ $company['email'] }}<br>
            Disponível 24/7 para renovações urgentes
        </p>
    </div>
</body>
</html>