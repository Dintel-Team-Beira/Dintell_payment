<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $receipt->receipt_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #28a745;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: normal;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            margin-bottom: 20px;
        }
        .receipt-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 25px 0;
            border-left: 4px solid #28a745;
        }
        .receipt-info h3 {
            margin-top: 0;
            color: #28a745;
            font-size: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        tr {
            border-bottom: 1px solid #e9ecef;
        }
        tr:last-child {
            border-bottom: none;
        }
        th {
            padding: 10px 0;
            text-align: left;
            font-weight: 600;
            color: #666;
            width: 45%;
        }
        td {
            padding: 10px 0;
            text-align: right;
        }
        .total-row {
            background-color: #d4edda;
            margin: 10px -20px -20px -20px;
            padding: 15px 20px;
        }
        .total-row th,
        .total-row td {
            font-size: 18px;
            color: #28a745;
            padding: 0;
        }
        .message {
            background-color: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 25px 20px;
            color: #666;
            font-size: 13px;
            line-height: 1.8;
        }
        .footer p {
            margin: 8px 0;
        }
        .divider {
            height: 1px;
            background-color: #e9ecef;
            margin: 20px 0;
        }
        .highlight {
            color: #28a745;
            font-weight: 600;
        }
        .disclaimer {
            font-style: italic;
            color: #999;
            font-size: 12px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
        }
        .checkmark {
            font-size: 48px;
            color: #28a745;
            text-align: center;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pagamento Recebido</h1>
        </div>

        <div class="content">
            <!-- <div class="checkmark">✓</div> -->
            
            <div class="greeting">
                <p>Estimado(a) <strong>{{ $receipt->client->name }}</strong>,</p>
            </div>

            
            <p>Confirmamos o recebimento do seu pagamento. O recibo oficial encontra-se em anexo.</p>
           

            <div class="receipt-info">
                <h3>Informações do Recibo</h3>
                <table>
                    <tr>
                        <th>Número do Recibo:</th>
                        <td><strong>{{ $receipt->receipt_number }}</strong></td>
                    </tr>
                    <tr>
                        <th>Data de Emissão:</th>
                        <td>{{ $receipt->payment_date->format('d/m/Y') }}</td>
                    </tr>
                </table>
                
                <div class="total-row">
                    <table>
                        <tr>
                            <th>Valor Pago:</th>
                            <td><strong>{{ number_format($receipt->amount_paid, 2, ',', '.') }} MT</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="divider"></div>
            <p>O recibo completo encontra-se em anexo em formato PDF.</p>
        </div>

        <div class="footer">
            <p>Agradecemos a sua preferência.</p>
            <div class="disclaimer">
                <p>Este é um email automático. Por favor, não responda directamente a esta mensagem. Para qualquer questão, utilize os nossos canais oficiais de contacto.</p>
            </div>
        </div>
    </div>
</body>
</html>