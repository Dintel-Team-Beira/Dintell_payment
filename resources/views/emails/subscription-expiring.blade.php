<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aviso de Expiração - {{ $company['name'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 2px solid #1a365d;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 150px;
            height: auto;
        }
        .greeting {
            font-size: 16px;
            color: #1a365d;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .alert-box {
            background: #f8f9fa;
            border: 1px solid #dc3545;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .alert-box h3 {
            color: #dc3545;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .info-list {
            margin: 20px 0;
        }
        .info-item {
            border-left: 3px solid #1a365d;
            padding: 10px 15px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 3px;
        }
        .info-item h4 {
            margin: 0 0 5px 0;
            font-size: 12px;
            color: #374151;
            font-weight: 600;
        }
        .info-item p {
            margin: 0;
            font-size: 14px;
            color: #1a365d;
            font-weight: 500;
        }
        .amount-highlight {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin: 20px 0;
        }
        .amount-highlight h4 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 5px 0;
            font-weight: 600;
        }
        .amount-highlight .amount-value {
            font-size: 18px;
            font-weight: bold;
            color: #1a365d;
        }
        .payment-info {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .payment-info h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .payment-info ul {
            margin: 0;
            padding-left: 15px;
            font-size: 12px;
        }
        .payment-info li {
            margin: 8px 0;
        }
        .btn-container {
            text-align: center;
            margin: 20px 0;
        }
        .btn-renew {
            background: #1a365d;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .footer {
            background: #f3f4f6;
            padding: 15px;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
            margin-top: 20px;
            border-top: 1px solid #ddd;
        }
        .contact-info {
            margin-top: 10px;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                padding: 15px;
            }
            .header {
                padding-bottom: 10px;
            }
            .header img {
                max-width: 120px;
            }
            .btn-renew {
                display: block;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <image src="https://beyondbusiness.co.mz/logo.png" alt="DINTELL Logo" style="width: 150px; height: auto;">
        </div>

        <div class="alert-box">
            <h3>Subscrição Próxima do Vencimento</h3>
            <p>Sua subscrição expira em <span style="color: #dc3545; font-weight: bold;">{{ $daysLeft }} dias</span>.</p>
        </div>

        <div class="greeting">
            Prezado(a) {{ $client->name }},
        </div>

        <p>Informamos que sua subscrição está próxima do vencimento. Para evitar a interrupção dos serviços, recomendamos que proceda com a renovação o quanto antes.</p>

        <div class="info-list">
            <div class="info-item">
                <h4>Domínio</h4>
                <p>{{ $subscription->domain }}</p>
            </div>
            <div class="info-item">
                <h4>Plano</h4>
                <p>{{ $plan->name }}</p>
            </div>
            <div class="info-item">
                <h4>Data de Expiração</h4>
                <p style="color: #dc3545; font-weight: bold;">{{ $subscription->ends_at->format('d/m/Y') }}</p>
            </div>
            <div class="info-item">
                <h4>Dias Restantes</h4>
                <p style="color: #dc3545; font-weight: bold;">{{ $daysLeft }} dias</p>
            </div>
        </div>

        <div class="amount-highlight">
            <h4>Valor para Renovação</h4>
            <div class="amount-value">MT {{ number_format($plan->price, 2) }}</div>
        </div>

        <div class="payment-info">
            <h3>Formas de Pagamento</h3>
            <ul>
                <li>Transferência bancária</li>
                <li>Depósito bancário</li>
                <li>Pagamento em numerário</li>
            </ul>
            <p style="margin-top: 10px;">Após o vencimento, os serviços serão <span style="color: #dc3545; font-weight: bold;">suspensos automaticamente</span>. Para reativar, será necessário efetuar o pagamento e aguardar a confirmação.</p>
        </div>


        <div class="footer">
            <p>{{ $company['name'] }} - {{ $company['slogan'] }}</p>
            <div class="contact-info">
                <p>{{ $company['address_maputo'] }} | {{ $company['address_beira'] }}</p>
                <p>Telefone: {{ $company['phone'] }} | Email: {{ $company['email'] }}</p>
            </div>
            <p>© {{ date('Y') }} {{ $company['name'] }}</p>
        </div>
    </div>
</body>
</html>
