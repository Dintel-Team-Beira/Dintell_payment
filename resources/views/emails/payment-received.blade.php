<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Confirmado - {{ $company['name'] }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background: #f8f9fa; margin: 0; padding: 0; }
        .container { max-width: 650px; margin: 0 auto; background: white; }
        .header { background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 300; }
        .header .slogan { font-size: 14px; opacity: 0.9; margin-top: 5px; }
        .content { padding: 40px 30px; }
        .success-badge { background: #059669; color: white; padding: 8px 16px; border-radius: 20px; font-size: 14px; display: inline-block; margin-bottom: 20px; }
        .amount-highlight { background: #d1fae5; border: 2px solid #10b981; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0; }
        .amount-highlight h2 { color: #059669; margin: 0; font-size: 28px; }
        .receipt-details { background: #f8fafc; border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .receipt-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        .receipt-row:last-child { border-bottom: none; font-weight: bold; }
        .company-info { background: #f9fafb; padding: 20px; border-radius: 8px; margin-top: 30px; font-size: 14px; }
        .footer { background: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $company['name'] }}</h1>
            <div class="slogan">{{ $company['slogan'] }}</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="success-badge">💰 PAGAMENTO CONFIRMADO</div>

            <div style="font-size: 18px; color: #1f2937; margin-bottom: 20px;">
                Obrigado, <strong>{{ $client->name }}</strong>!
            </div>

            <p>Confirmamos o recebimento do seu pagamento. Encontra em anexo o comprovativo oficial para os seus registos.</p>

            <!-- Payment Amount -->
            <div class="amount-highlight">
                <h2>MT {{ number_format($amount, 2) }}</h2>
                <p style="margin: 5px 0 0 0; color: #374151;">Valor pago com sucesso</p>
            </div>

            <!-- Receipt Details -->
            <div class="receipt-details">
                <h3 style="margin-top: 0; color: #374151;">📋 Detalhes do Pagamento</h3>
                <div class="receipt-row">
                    <span>Recibo Nº:</span>
                    <span><strong>{{ $receiptNumber }}</strong></span>
                </div>
                <div class="receipt-row">
                    <span>Data do Pagamento:</span>
                    <span>{{ $paymentDate->format('d/m/Y H:i') }}</span>
                </div>
                <div class="receipt-row">
                    <span>Método de Pagamento:</span>
                    <span>{{ $paymentMethod }}</span>
                </div>
                @if($paymentReference)
                <div class="receipt-row">
                    <span>Referência:</span>
                    <span>{{ $paymentReference }}</span>
                </div>
                @endif
                <div class="receipt-row">
                    <span>Domínio:</span>
                    <span>{{ $subscription->domain }}</span>
                </div>
                <div class="receipt-row">
                    <span>Plano:</span>
                    <span>{{ $plan->name }}</span>
                </div>
                <div class="receipt-row">
                    <span>Subtotal:</span>
                    <span>MT {{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="receipt-row">
                    <span>IVA (16%):</span>
                    <span>MT {{ number_format($iva_amount, 2) }}</span>
                </div>
                <div class="receipt-row">
                    <span>Total:</span>
                    <span>MT {{ number_format($amount, 2) }}</span>
                </div>
            </div>

            <!-- Service Information -->
            <div style="background: #f0f9ff; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #1e40af; margin-top: 0;">🌐 Informações do Serviço</h3>
                <p><strong>Website:</strong> {{ $subscription->domain }}</p>
                <p><strong>Status:</strong> <span style="color: #059669;">✅ Ativo</span></p>
                <p><strong>Próximo Vencimento:</strong> {{ $subscription->ends_at->format('d/m/Y') }}</p>
                <p><strong>Renovação Automática:</strong> {{ $subscription->auto_renew ? 'Ativada' : 'Desativada' }}</p>
            </div>

            <!-- Company Banking Information -->
            <div class="company-info">
                <h4>🏦 Dados Bancários para Future Pagamentos</h4>
                <p><strong>Banco:</strong> {{ $company['bank_name'] ?? 'BCI' }}</p>
                <p><strong>Número da Conta:</strong> {{ $company['bank_account'] ?? '222 038 724 100 01' }}</p>
                <p><strong>NIB:</strong> {{ $company['bank_nib'] ?? '0008 0000 2203 8724 101 13' }}</p>

                <h4 style="margin-top: 15px;">📞 Contactos</h4>
                <p><strong>NUIT:</strong> {{ $company['nuit'] }}</p>
                <p><strong>Telefone:</strong> {{ $company['phone'] }}</p>
                <p><strong>Email:</strong> {{ $company['email'] }}</p>
            </div>

            <p style="margin-top: 30px; color: #6b7280;">
                Este comprovativo serve como prova oficial do pagamento efetuado.
                Guarde-o para os seus registos contabilísticos.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $company['name'] }} - {{ $company['slogan'] }}</p>
            <p>Documento processado automaticamente em {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>