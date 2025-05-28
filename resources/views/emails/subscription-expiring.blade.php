<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aviso de Expiração - DINTELL</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 20px;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e5e5;
            margin-bottom: 30px;
        }
        .logo {
            color: #1a365d;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .slogan {
            color: #666;
            font-size: 12px;
            font-style: italic;
        }
        .alert-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 5px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .info-table td {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 40%;
            color: #555;
        }
        .amount-highlight {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        .amount-value {
            font-size: 20px;
            font-weight: bold;
            color: #1a365d;
        }
        .btn-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn-renew {
            background-color: #1a365d;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            transition: background-color 0.3s;
        }
        .btn-renew:hover {
            background-color: #2c5282;
        }
        .footer {
            border-top: 2px solid #e5e5e5;
            padding-top: 20px;
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .contact-info {
            margin-top: 15px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- <div class="logo">dintell</div> -->
            <!-- <div class="slogan">beyond technology, intelligence.</div> -->
            <image src="https://beyondbusiness.co.mz/logo.png" alt="DINTELL Logo" style="width: 150px; height: auto;">
        </div>

        <div class="alert-box">
            <div class="alert-title">⏰ Atenção: Subscrição próxima do vencimento</div>
            <p>Sua subscrição expira em <strong>{{ $daysLeft }} dias</strong>.</p>
        </div>

        <p>Prezado(a) <strong>{{ $client->name }}</strong>,</p>

        <p>Informamos que sua subscrição está próxima do vencimento. Para evitar a interrupção dos serviços, solicitamos que proceda com a renovação o quanto antes.</p>

        <table class="info-table">
            <tr>
                <td>Domínio:</td>
                <td><strong>{{ $subscription->domain }}</strong></td>
            </tr>
            <tr>
                <td>Plano:</td>
                <td><strong>{{ $plan->name }}</strong></td>
            </tr>
            <tr>
                <td>Data de Expiração:</td>
                <td><strong>{{ $subscription->ends_at->format('d/m/Y') }}</strong></td>
            </tr>
            <tr>
                <td>Dias Restantes:</td>
                <td><strong style="color: #dc3545;">{{ $daysLeft }} dias</strong></td>
            </tr>
        </table>

        <div class="amount-highlight">
            <div>Valor para Renovação</div>
            <div class="amount-value">MT {{ number_format($plan->price, 2) }}</div>
        </div>


        <p><strong>Importante:</strong> Após o vencimento, os serviços serão suspensos automaticamente. Para reativar, será necessário efetuar o pagamento e aguardar a confirmação.</p>

        <p><strong>Formas de Pagamento:</strong></p>
        <ul>
            <li>Transferência bancária</li>
            <li>Depósito bancário</li>
            <li>Pagamento em numerário</li>
        </ul>

    </div>
</body>
</html>