<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscrição Renovada - {{ $company['name'] }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background: #f8f9fa; margin: 0; padding: 0; }
        .container { max-width: 650px; margin: 0 auto; background: white; }
        .header { background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 300; }
        .content { padding: 40px 30px; }
        .renewal-badge { background: #7c3aed; color: white; padding: 8px 16px; border-radius: 20px; font-size: 14px; display: inline-block; margin-bottom: 20px; }
        .renewal-summary { background: #faf5ff; border: 2px solid #a855f7; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .date-comparison { display: grid; grid-template-columns: 1fr auto 1fr; gap: 20px; align-items: center; margin: 20px 0; }
        .date-box { background: white; border: 1px solid #d1d5db; padding: 15px; border-radius: 6px; text-align: center; }
        .date-box.old { border-color: #ef4444; }
        .date-box.new { border-color: #10b981; background: #f0fdf4; }
        .arrow { font-size: 24px; color: #7c3aed; }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .feature-card { background: #f8fafc; padding: 15px; border-radius: 6px; border-left: 4px solid #7c3aed; }
        .company-info { background: #f9fafb; padding: 20px; border-radius: 8px; margin-top: 30px; font-size: 14px; }
        .footer { background: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280; }
        @media (max-width: 600px) {
            .date-comparison { grid-template-columns: 1fr; }
            .arrow { transform: rotate(90deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $company['name'] }}</h1>
            <div style="font-size: 14px; opacity: 0.9; margin-top: 5px;">{{ $company['slogan'] }}</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="renewal-badge">🔄 SUBSCRIÇÃO RENOVADA</div>

            <div style="font-size: 18px; color: #1f2937; margin-bottom: 20px;">
                Excelente, <strong>{{ $client->name }}</strong>!
            </div>

            <p>A sua subscrição foi renovada com sucesso. O seu serviço continua ativo sem interrupções.</p>

            <!-- Renewal Summary -->
            <div class="renewal-summary">
                <h3 style="color: #7c3aed; margin-top: 0;">📋 Resumo da Renovação</h3>
                <p><strong>Domínio:</strong> {{ $subscription->domain }}</p>
                <p><strong>Plano:</strong> {{ $plan->name }}</p>
                <p><strong>Valor Pago:</strong> MT {{ number_format($amount, 2) }}</p>
                <p><strong>Data da Renovação:</strong> {{ $renewalDate->format('d/m/Y H:i') }}</p>
                <p><strong>Período Estendido:</strong> {{ $daysExtended }} dias</p>
            </div>

            <!-- Date Comparison -->
            <div class="date-comparison">
                <div class="date-box old">
                    <h4 style="margin: 0; color: #ef4444;">Data Anterior</h4>
                    <p style="margin: 5px 0 0 0; font-weight: bold;">
                        {{ $oldExpiryDate ? $oldExpiryDate->format('d/m/Y') : 'Expirada' }}
                    </p>
                </div>
                <div class="arrow">➡️</div>
                <div class="date-box new">
                    <h4 style="margin: 0; color: #10b981;">Nova Data</h4>
                    <p style="margin: 5px 0 0 0; font-weight: bold;">
                        {{ $nextBillingDate->format('d/m/Y') }}
                    </p>
                </div>
            </div>

            <!-- Service Features -->
            <div class="features-grid">
                <div class="feature-card">
                    <h4 style="margin: 0 0 5px 0; color: #374151;">🌐 Website</h4>
                    <p style="margin: 0;">{{ $subscription->domain }}</p>
                </div>
                <div class="feature-card">
                    <h4 style="margin: 0 0 5px 0; color: #374151;">📊 Status</h4>
                    <p style="margin: 0; color: #059669; font-weight: bold;">{{ $serviceStatus }}</p>
                </div>
                <div class="feature-card">
                    <h4 style="margin: 0 0 5px 0; color: #374151;">💾 Armazenamento</h4>
                    <p style="margin: 0;">{{ $plan->max_storage_gb }}GB</p>
                </div>
                <div class="feature-card">
                    <h4 style="margin: 0 0 5px 0; color: #374151;">📡 Tráfego</h4>
                    <p style="margin: 0;">{{ $plan->max_bandwidth_gb }}GB/mês</p>
                </div>
            </div>

            <!-- Payment Breakdown -->
            <div style="background: #f8fafc; border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #374151;">💰 Detalhes Financeiros</h3>
                <div style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #e5e7eb;">
                    <span>Subtotal:</span>
                    <span>MT {{ number_format($subtotal, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #e5e7eb;">
                    <span>IVA (16%):</span>
                    <span>MT {{ number_format($iva_amount, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 5px 0; font-weight: bold; font-size: 16px;">
                    <span>Total Pago:</span>
                    <span>MT {{ number_format($amount, 2) }}</span>
                </div>
            </div>

            <!-- Next Steps -->
            <div style="background: #dbeafe; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #1e40af; margin-top: 0;">🎯 Próximos Passos</h3>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>O seu website continua online normalmente</li>
                    <li>Próxima renovação: <strong>{{ $nextBillingDate->format('d/m/Y') }}</strong></li>
                    <li>Receberá lembrete 7 dias antes do vencimento</li>
                    <li>Comprovativo oficial em anexo</li>
                </ul>
            </div>

            <!-- Company Information -->
            <div class="company-info">
                <h4>📞 Contactos da {{ $company['name'] }}</h4>
                <p><strong>NUIT:</strong> {{ $company['nuit'] }}</p>
                <p><strong>Maputo:</strong> {{ $company['address_maputo'] }}</p>
                <p><strong>Beira:</strong> {{ $company['address_beira'] }}</p>
                <p><strong>Telefone:</strong> {{ $company['phone'] }}</p>
                <p><strong>Email:</strong> {{ $company['email'] }}</p>
            </div>

            <p style="margin-top: 30px; color: #6b7280;">
                Obrigado por continuar a confiar nos nossos serviços.
                Estamos sempre aqui para garantir o sucesso do seu projeto digital!
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $company['name'] }} - {{ $company['slogan'] }}</p>
            <p>Renovação processada automaticamente em {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>