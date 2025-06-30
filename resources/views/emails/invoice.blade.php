<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .invoice-info {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Nova Fatura Recebida</h2>
        <p>Olá {{ $invoice->client->name }},</p>

        @if($customMessage)
            <p>{{ $customMessage }}</p>
        @else
            <p>Esperamos que esta mensagem o encontre bem. Temos o prazer de enviar-lhe a fatura em anexo para os serviços prestados.</p>
        @endif
    </div>

    <div class="invoice-info">
        <h3>Detalhes da Fatura</h3>
        <table>
            <tr>
                <th>Número da Fatura:</th>
                <td><strong>{{ $invoice->invoice_number }}</strong></td>
            </tr>
            <tr>
                <th>Data da Fatura:</th>
                <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Data de Vencimento:</th>
                <td>{{ $invoice->due_date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Valor Total:</th>
                <td><strong>{{ number_format($invoice->total, 2) }} MT</strong></td>
            </tr>
            <tr>
                <th>Status:</th>
                <td>
                    @php
                        $statusLabels = [
                            'draft' => 'Rascunho',
                            'sent' => 'Enviada',
                            'paid' => 'Paga',
                            'overdue' => 'Vencida',
                            'cancelled' => 'Cancelada'
                        ];
                    @endphp
                    {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                </td>
            </tr>
        </table>
    </div>

    <div>
        <h3>Resumo dos Itens</h3>
        <table>
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Qtd</th>
                    <th>Preço Unit.</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                    <td>{{ number_format($item->unit_price, 2) }} MT</td>
                    <td>{{ number_format(($item->quantity * $item->unit_price) + (($item->quantity * $item->unit_price) * ($item->tax_rate / 100)), 2) }} MT</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="text-align: right; margin-top: 15px;">
            <p><strong>Subtotal: {{ number_format($invoice->subtotal, 2) }} MT</strong></p>
            <p><strong>IVA: {{ number_format($invoice->tax_amount, 2) }} MT</strong></p>
            <p style="font-size: 18px; color: #007bff;"><strong>TOTAL: {{ number_format($invoice->total, 2) }} MT</strong></p>
        </div>
    </div>

    @if($invoice->notes)
    <div style="margin-top: 20px;">
        <h3>Observações</h3>
        <p style="background-color: #f8f9fa; padding: 15px; border-radius: 5px;">{{ $invoice->notes }}</p>
    </div>
    @endif

    <div>
        <h3>Instruções de Pagamento</h3>
        <p>Por favor, efetue o pagamento até {{ $invoice->due_date->format('d/m/Y') }}.</p>
        <p><strong>Prazo de Pagamento:</strong> {{ $invoice->payment_terms_days }} dias</p>

        @if($invoice->terms_conditions)
        <p><strong>Termos e Condições:</strong></p>
        <p style="font-size: 12px; background-color: #f8f9fa; padding: 10px; border-radius: 5px;">{{ $invoice->terms_conditions }}</p>
        @endif
    </div>

    <div class="footer">
        <p>A fatura completa está anexada a este email em formato PDF.</p>
        <p>Se tiver alguma dúvida sobre esta fatura, não hesite em entrar em contato connosco.</p>
        <p>Obrigado pela sua preferência!</p>
        <br>
        <p><em>Este é um email automático. Por favor, não responda diretamente a esta mensagem.</em></p>
    </div>
</body>
</html>