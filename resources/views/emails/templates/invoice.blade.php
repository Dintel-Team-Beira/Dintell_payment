
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fatura - {{ $invoice->invoice_number ?? 'FAT-2025-001' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .content {
            padding: 30px 20px;
        }

        .invoice-info {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .invoice-details {
            display: table;
            width: 100%;
            margin: 20px 0;
        }

        .detail-row {
            display: table-row;
        }

        .detail-label, .detail-value {
            display: table-cell;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .detail-label {
            font-weight: 600;
            color: #555;
            width: 40%;
        }

        .detail-value {
            color: #333;
        }

        .total-amount {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }

        .total-amount h3 {
            color: #1976d2;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
            transition: transform 0.2s;
        }

        .cta-button:hover {
            transform: translateY(-2px);
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }

        .footer p {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .contact-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #fff;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .contact-info h4 {
            color: #495057;
            margin-bottom: 10px;
        }

        .contact-info p {
            font-size: 14px;
            color: #6c757d;
            margin: 5px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-paid {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 600px) {
            .email-container {
                margin: 0;
                width: 100%;
            }

            .content {
                padding: 20px 15px;
            }

            .header {
                padding: 20px 15px;
            }

            .header h1 {
                font-size: 24px;
            }

            .invoice-details {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ config('app.name', 'SFS') }}</h1>
            <p>Sistema de Faturação e Subscrição</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2 style="color: #333; margin-bottom: 20px;">
                📧 Nova Fatura Disponível
            </h2>

            <p style="font-size: 16px; margin-bottom: 20px;">
                Olá <strong>{{ $invoice->client->name ?? 'Cliente' }}</strong>,
            </p>

            <p style="margin-bottom: 20px;">
                Uma nova fatura foi gerada para sua conta. Por favor, verifique os detalhes abaixo:
            </p>

            <!-- Invoice Info Box -->
            <div class="invoice-info">
                <h3 style="color: #667eea; margin-bottom: 15px;">
                    Informações da Fatura
                </h3>

                <div class="invoice-details">
                    <div class="detail-row">
                        <div class="detail-label">Número da Fatura:</div>
                        <div class="detail-value">
                            <strong>{{ $invoice->invoice_number ?? 'FAT-2025-001' }}</strong>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Data de Emissão:</div>
                        <div class="detail-value">
                            {{ isset($invoice->invoice_date) ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') : date('d/m/Y') }}
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Data de Vencimento:</div>
                        <div class="detail-value">
                            <strong style="color: #e74c3c;">
                                {{ isset($invoice->due_date) ? \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') : date('d/m/Y', strtotime('+30 days')) }}
                            </strong>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Status:</div>
                        <div class="detail-value">
                            <span class="status-badge status-pending">
                                {{ $invoice->status ?? 'Pendente' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Amount -->
            <div class="total-amount">
                <p style="margin: 0; color: #666; font-size: 14px;">Valor Total</p>
                <h3>{{ number_format($invoice->total ?? 1500, 2, ',', '.') }} MT</h3>
            </div>

            <!-- Call to Action -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="#" class="cta-button">
                    💳 Efetuar Pagamento
                </a>
            </div>

            <!-- Additional Information -->
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #495057; margin-bottom: 15px;">📋 Instruções de Pagamento</h4>
                <ul style="margin: 0; padding-left: 20px; color: #6c757d;">
                    <li>O pagamento deve ser efetuado até a data de vencimento</li>
                    <li>Aceitos pagamentos via transferência bancária, M-Pesa ou presencial</li>
                    <li>Em caso de dúvidas, entre em contato conosco</li>
                    <li>Mantenha este email como comprovante</li>
                </ul>
            </div>

            <!-- Contact Information -->
            <div class="contact-info">
                <h4>📞 Precisa de Ajuda?</h4>
                <p><strong>Email:</strong> contato@sfs.co.mz</p>
                <p><strong>Telefone:</strong> (+258) 84 123 4567</p>
                <p><strong>Horário:</strong> Segunda a Sexta, 8h às 17h</p>
                <p><strong>Endereço:</strong> Maputo, Moçambique</p>
            </div>

            <p style="margin-top: 30px; color: #666;">
                Obrigado por escolher nossos serviços!<br>
                Equipe {{ config('app.name', 'SFS') }}
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                &copy; {{ date('Y') }} {{ config('app.name', 'SFS') }}. Todos os direitos reservados.
            </p>
            <p>
                Este é um email automático, por favor não responda diretamente.
            </p>
            <p style="font-size: 12px; margin-top: 15px;">
                🏢 {{ config('app.name', 'SFS') }} - Sistema de Faturação e Subscrição<br>
                📍 Maputo, Moçambique | 📧 contato@sfs.co.mz
            </p>
        </div>
    </div>
</body>
</html>
