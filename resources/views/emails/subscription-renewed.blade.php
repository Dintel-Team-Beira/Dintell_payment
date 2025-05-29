<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscrição Renovada - {{ $company['name'] }}</title>
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
        }
        .header {
            background: #1a365d;
            color: white;
            padding: 20px;
            text-align: center;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 500;
        }
        .header .slogan {
            font-size: 11px;
            opacity: 0.9;
            margin-top: 5px;
        }
        .content {
            padding: 20px;
        }
        .greeting {
            font-size: 16px;
            color: #1a365d;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .renewal-summary {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .renewal-summary h3 {
            color: #1a365d;
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
        .financial-details {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .financial-details h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .financial-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
        }
        .financial-item:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 14px;
        }
        .next-steps {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .next-steps h3 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .next-steps ul {
            margin: 0;
            padding-left: 15px;
            font-size: 12px;
        }
        .next-steps li {
            margin: 8px 0;
        }
        .company-info {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 12px;
        }
        .company-info h4 {
            color: #1a365d;
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .footer {
            background: #f3f4f6;
            padding: 15px;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
            border-bottom-left-radius: 5px;
            border-bottom-right-radius: 5px;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
            }
            .content {
                padding: 15px;
            }
            .header {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <image src="https://beyondbusiness.co.mz/logo.png" alt="DINTELL Logo" style="width: 150px; height: auto;">
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Prezado(a) {{ $client->name }},
            </div>

            <p>Sua subscrição foi renovada com sucesso. O serviço permanece ativo sem interrupções.</p>

            <!-- Renewal Summary -->
            <div class="renewal-summary">
                <h3>Resumo da Subscrição</h3>
                <p><strong>Domínio:</strong> {{ $subscription->domain }}</p>
                <p><strong>Plano:</strong> {{ $plan->name }}</p>
                <p><strong>Valor Pago:</strong> ${{ number_format($amount, 2) }}</p>
                <p><strong>Data de Renovação:</strong> {{ $renewalDate->format('d/m/Y') }}</p>
                <p><strong>Período Estendido:</strong> {{ $daysExtended}} dias</p>
            </div>

            <!-- Date Comparison -->
            <div class="info-list">
                <div class="info-item">
                    <h4>Data de Expiração Anterior</h4>
                    <p style="color: #dc3545; font-weight: bold;">
                        {{ $oldExpiryDate ? $oldExpiryDate->format('d/m/Y') : 'Expirada' }}
                    </p>
                </div>
                <div class="info-item">
                    <h4>Nova Data de Expiração</h4>
                    <p>{{ $nextBillingDate->format('d/m/Y') }}</p>
                </div>
            </div>

            <!-- Service Features -->
            <div class="info-list">
                <div class="info-item">
                    <h4>Website</h4>
                    <p>{{ $subscription->domain }}</p>
                </div>
                <div class="info-item">
                    <h4>Status</h4>
                    <p style="color: #1a365d; font-weight: bold;">{{ $plan->name }}</p>
                </div>
                <div class="info-item">
                    <h4>Armazenamento</h4>
                    <p>{{ $plan->max_storage }}GB</p>
                </div>
                <div class="info-item">
                    <h4>Tráfego</h4>
                    <p>{{ $plan->max_bandwidth }}GB/mês</p>
                </div>
            </div>

            <!-- Financial Details -->
            <div class="financial-details">
                <h3>Detalhes Financeiros</h3>
                <div class="financial-item">
                    <span>Subtotal:</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="financial-item">
                    <span>IVA (16%):</span>
                    <span>${{ number_format($iva_amount, 2) }}</span>
                </div>
                <div class="financial-item">
                    <span>Total Pago:</span>
                    <span>${{ number_format($amount, 2) }}</span>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="next-steps">
                <h3>Próximos Passos</h3>
                <ul>
                    <li>O seu website continua ativo.</li>
                    <li>Próxima renovação: <strong>{{ $nextBillingDate->format('d/m/Y') }}</strong>.</li>
                    <li>Receberá um lembrete 7 dias antes do vencimento.</li>
                    <li>Comprovativo oficial em anexo.</li>
                </ul>
            </div>

            <!-- Company Information -->
            <div class="company-info">
                <h4>Contactos da {{ $company['name'] }}</h4>
                <p><strong>NUIT:</strong> {{ $company['nuit'] }}</p>
                <p><strong>Maputo:</strong> {{ $company['address_maputo'] }}</p>
                <p><strong>Beira:</strong> {{ $company['address_beira'] }}</p>
                <p><strong>Telefone:</strong> {{ $company['phone'] }}</p>
                <p><strong>Email:</strong> {{ $company['email'] }}</p>
            </div>

            <p style="margin-top: 20px; color: #666;">
                Obrigado por confiar nos nossos serviços.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} {{ $company['name'] }} - {{ $company['slogan'] }}</p>
            <p>Renovação processada em {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
