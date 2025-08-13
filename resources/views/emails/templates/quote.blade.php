<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cotação - {{ $quote->quote_number ?? 'COT-2025-001' }}</title>
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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

        .quote-info {
            background-color: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .quote-details {
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
            background-color: #ecfdf5;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }

        .total-amount h3 {
            color: #059669;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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

        .validity-warning {
            background-color: #fef3c7;
            border: 1px solid #fbbf24;
            color: #92400e;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
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
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .status-sent {
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }

        .status-expired {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5;
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

            .quote-details {
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
                📋 Nova Cotação Disponível
            </h2>

            <p style="font-size: 16px; margin-bottom: 20px;">
                Olá <strong>{{ $quote->client->name ?? 'Cliente' }}</strong>,
            </p>

            <p style="margin-bottom: 20px;">
                Preparamos uma cotação personalizada para você. Confira os detalhes abaixo:
            </p>

            <!-- Quote Info Box -->
            <div class="quote-info">
                <h3 style="color: #059669; margin-bottom: 15px;">
                    Informações da Cotação
                </h3>

                <div class="quote-details">
                    <div class="detail-row">
                        <div class="detail-label">Número da Cotação:</div>
                        <div class="detail-value">
                            <strong>{{ $quote->quote_number ?? 'COT-2025-001' }}</strong>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Data de Emissão:</div>
                        <div class="detail-value">
                            {{ isset($quote->quote_date) ? \Carbon\Carbon::parse($quote->quote_date)->format('d/m/Y') : date('d/m/Y') }}
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Válida até:</div>
                        <div class="detail-value">
                            <strong style="color: #d97706;">
                                {{ isset($quote->valid_until) ? \Carbon\Carbon::parse($quote->valid_until)->format('d/m/Y') : date('d/m/Y', strtotime('+15 days')) }}
                            </strong>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Status:</div>
                        <div class="detail-value">
                            <span class="status-badge status-sent">
                                {{ $quote->status ?? 'Enviada' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Amount -->
            <div class="total-amount">
                <p style="margin: 0; color: #666; font-size: 14px;">Valor Total da Cotação</p>
                <h3>{{ number_format($quote->total ?? 2500, 2, ',', '.') }} MT</h3>
            </div>

            <!-- Validity Warning -->
            <div class="validity-warning">
                <h4 style="margin-bottom: 10px; display: flex; align-items: center;">
                    ⏰ <span style="margin-left: 8px;">Atenção - Prazo de Validade</span>
                </h4>
                <p style="margin: 0; font-size: 14px;">
                    Esta cotação é válida até <strong>{{ isset($quote->valid_until) ? \Carbon\Carbon::parse($quote->valid_until)->format('d/m/Y') : date('d/m/Y', strtotime('+15 days')) }}</strong>.
                    Após esta data, os preços e condições poderão ser alterados.
                </p>
            </div>

            <!-- Call to Action -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="#" class="cta-button">
                    ✅ Aceitar Cotação
                </a>
            </div>

            <!-- Services/Products Summary -->
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #495057; margin-bottom: 15px;">📦 Resumo dos Itens Cotados</h4>
                <ul style="margin: 0; padding-left: 20px; color: #6c757d;">
                    <li>Produtos e serviços personalizados conforme sua necessidade</li>
                    <li>Preços competitivos e condições especiais</li>
                    <li>Suporte técnico incluído</li>
                    <li>Garantia de qualidade em todos os itens</li>
                </ul>

                <p style="margin-top: 15px; font-size: 14px; color: #6c757d;">
                    <strong>Observação:</strong> Esta cotação inclui todos os custos mencionados. Não há taxas ocultas.
                </p>
            </div>

            <!-- Next Steps -->
            <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #059669; margin-bottom: 15px;">🚀 Próximos Passos</h4>
                <ol style="margin: 0; padding-left: 20px; color: #065f46;">
                    <li style="margin-bottom: 8px;">Analise os itens e valores da cotação</li>
                    <li style="margin-bottom: 8px;">Entre em contato caso tenha dúvidas</li>
                    <li style="margin-bottom: 8px;">Confirme sua aceitação antes do prazo de validade</li>
                    <li style="margin-bottom: 8px;">Aguarde o processo de faturação e entrega</li>
                </ol>
            </div>

            <!-- Contact Information -->
            <div class="contact-info">
                <h4>📞 Dúvidas? Estamos aqui para ajudar!</h4>
                <p><strong>Email:</strong> vendas@sfs.co.mz</p>
                <p><strong>Telefone:</strong> (+258) 84 123 4567</p>
                <p><strong>WhatsApp:</strong> (+258) 87 123 4567</p>
                <p><strong>Horário:</strong> Segunda a Sexta, 8h às 17h</p>
                <p><strong>Endereço:</strong> Maputo, Moçambique</p>
            </div>

            <p style="margin-top: 30px; color: #666;">
                Agradecemos sua confiança e esperamos fazer negócios com você!<br>
                <strong>Equipe de Vendas - {{ config('app.name', 'SFS') }}</strong>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                &copy; {{ date('Y') }} {{ config('app.name', 'SFS') }}. Todos os direitos reservados.
            </p>
            <p>
                Este é um email automático relacionado à sua cotação.
            </p>
            <p style="font-size: 12px; margin-top: 15px; color: #9ca3af;">
                🏢 {{ config('app.name', 'SFS') }} - Sistema de Faturação e Subscrição<br>
                📍 Maputo, Moçambique | 📧 vendas@sfs.co.mz | 📱 (+258) 84 123 4567
            </p>
        </div>
    </div>
</body>
</html>
