<div class="clearfix header">
    <div class="company-section">
        <div class="company-info">
            @if($company->logo)
                <img src="{{ Storage::url($company->logo) }}" style="width: 200px;" alt="Logo">
            @else
                <div class="company-name">{{ $company->name}}</div>
            @endif
            <div class="company-details">
                <strong>Contribuinte Nº:</strong> {{ $company->tax_number}}<br>
                {{ $company->address }}<br>
                {{ $company->country }}<br>
                {{ $company->phone}} | {{$company->email }} 
            </div>
        </div>
    </div>

    <div class="client-section">
        <div class="client-box">
            <strong>Exmo.(s) Sr.(s)</strong><br>
            <strong>{{ $invoice->client->name }}</strong><br>
            <strong>Nº CLIENTE:</strong> {{ str_pad($invoice->client->id, 10, '0', STR_PAD_LEFT) }}<br>
            {{ $invoice->client->phone ?? 'N/A' }}<br>
            {{ $invoice->client->address ?? $invoice->client->city ?? 'Moçambique' }}
        </div>
    </div>
</div>

<div class="invoice-header">
    <div class="original-label">Original</div>
    <div class="invoice-title">
        Fatura Nº {{ $invoice->invoice_number }}
    </div>
</div>

@if($invoice->status === 'paid')
    <div class="paid-stamp">
        ✓ FATURA PAGA{{ $invoice->paid_at ? ' EM ' . $invoice->paid_at->format('d/m/Y') : '' }}
        @if($invoice->paid_amount > 0)
            <br>Valor Pago: {{ number_format($invoice->paid_amount, 2) }} MT
        @endif
    </div>
@endif

@if($invoice->isOverdue() && $invoice->status !== 'paid')
    <div class="due-warning due-urgent">
        <strong>FATURA VENCIDA:</strong> Esta fatura venceu em {{ $invoice->due_date->format('d/m/Y') }}
        ({{ $invoice->due_date->diffForHumans() }}). Por favor, regularize o pagamento o mais breve possível.
    </div>
@elseif($invoice->due_date->diffInDays() <= 7 && $invoice->status !== 'paid')
    <div class="due-warning">
        <strong>VENCIMENTO PRÓXIMO:</strong> Esta fatura vence em {{ $invoice->due_date->format('d/m/Y') }}
        ({{ $invoice->due_date->diffForHumans() }}).
    </div>
@endif

<table class="invoice-details no-break">
    <tr>
        <td class="label">V/Nº CONTRIB.</td>
        <td>{{ $invoice->client->nuit ?? 'N/A' }}</td>
        <td class="label">COTAÇÃO</td>
        <td>{{ $invoice->quote ? $invoice->quote->quote_number : '-' }}</td>
        <td class="label">MOEDA</td>
        <td>MT</td>
        <td class="label">CÂMBIO</td>
        <td>1,00</td>
        <td class="label">DATA</td>
        <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td class="label">PRAZO PAG.</td>
        <td>{{ $invoice->payment_terms_days }} dias</td>
        <td class="label">DESC. FIN.</td>
        <td>0.00</td>
        <td class="label">VENCIMENTO</td>
        <td>{{ $invoice->due_date->format('d/m/Y') }}</td>
        <td class="label" colspan="4">
            @if($invoice->due_date->isToday())
                VENCE HOJE
            @elseif($invoice->due_date->isFuture())
                {{ $invoice->due_date->diffInDays() }} DIAS PARA VENCIMENTO
            @else
                VENCIDA HÁ {{ $invoice->due_date->diffInDays() }} DIAS
            @endif
        </td>
    </tr>
</table>

<table class="items-table no-break">
    <thead>
        <tr>
            <th style="width: 15%;">ARTIGO</th>
            <th style="width: 35%;">DESCRIÇÃO</th>
            <th style="width: 8%;">QUANT.</th>
            <th style="width: 12%;">PR. UNITÁRIO</th>
            <th style="width: 10%;">DESCONTOS</th>
            <th style="width: 10%;">IVA</th>
            <th style="width: 10%;">TOTAL LÍQUIDO</th>
        </tr>
    </thead>
    <tbody>
        @php
            $subtotalGeral = 0;
            $totalTax = 0;
            $totalDesconto = 0;
        @endphp
        @foreach($invoice->items as $item)
        @php
            $itemSubtotal = $item->quantity * $item->unit_price;
            $itemDesconto = 0;
            $itemSubtotalComDesconto = $itemSubtotal - $itemDesconto;
            $itemTax = $itemSubtotalComDesconto * (($item->tax_rate ?? 0) / 100);
            $itemTotal = $itemSubtotalComDesconto;

            $subtotalGeral += $itemSubtotal;
            $totalDesconto += $itemDesconto;
            $totalTax += $itemTax;
        @endphp
        <tr>
            <td class="text-center">{{ $item->name ?? strtoupper(substr($item->description, 0, 8)) }}</td>
            <td>
                <strong>{{ $item->description }}</strong>
            </td>
            <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
            <td class="text-right">{{ number_format($item->unit_price, 2) }} MT</td>
            <td class="text-right">{{ number_format($itemDesconto, 2) }} MT</td>
            <td class="text-right">{{ number_format($itemTax, 2) }} MT</td>
            <td class="text-right">{{ number_format($itemTotal, 2) }} MT</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="clearfix summary-row no-break">
    <div class="iva-summary">
        <div class="section-title">QUADRO RESUMO DO IVA</div>
        <table class="iva-table">
            <thead>
                <tr>
                    <th>TAXA</th>
                    <th>INCIDÊNCIA</th>
                    <th>TOTAL IVA</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $taxGroups = $invoice->items->groupBy('tax_rate');
                @endphp
                @foreach($taxGroups as $taxRate => $items)
                @php
                    $taxIncidencia = $items->sum(function($item) {
                        return $item->quantity * $item->unit_price;
                    });
                    $taxAmount = $taxIncidencia * (($taxRate ?? 0) / 100);
                @endphp
                <tr>
                    <td>{{ number_format($taxRate ?? 0, 0) }}.00</td>
                    <td>{{ number_format($taxIncidencia, 2) }}</td>
                    <td>{{ number_format($taxAmount, 2) }}</td>
                </tr>
                @endforeach
                <tr style="font-weight: bold;">
                    <td>TOTAL</td>
                    <td>{{ number_format($subtotalGeral, 2) }}</td>
                    <td>{{ number_format($totalTax, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="totals-summary">
        <div class="section-title">TOTAIS</div>
        <table class="totals-table">
            <tr>
                <td class="label">SUBTOTAL:</td>
                <td class="text-right">{{ number_format($invoice->subtotal, 2) }} MT</td>
            </tr>
            <tr>
                <td class="label">TOTAL DESCONTOS:</td>
                <td class="text-right">{{ number_format($totalDesconto, 2) }} MT</td>
            </tr>
            <tr>
                <td class="label">TOTAL IVA:</td>
                <td class="text-right">{{ number_format($invoice->tax_amount, 2) }} MT</td>
            </tr>
            @if($invoice->paid_amount > 0)
            <tr>
                <td class="label" style="color: #28a745;">VALOR PAGO:</td>
                <td class="text-right" style="color: #28a745;">{{ number_format($invoice->paid_amount, 2) }} MT</td>
            </tr>
            @if($invoice->remaining_amount > 0)
            <tr>
                <td class="label" style="color: #dc3545;">VALOR RESTANTE:</td>
                <td class="text-right" style="color: #dc3545;">{{ number_format($invoice->remaining_amount, 2) }} MT</td>
            </tr>
            @endif
            @endif
            <tr class="total-final">
                <td style="color: #000000;" class="label">TOTAL GERAL:</td>
                <td class="text-right">{{ number_format($invoice->total, 2) }} MT</td>
            </tr>
        </table>
    </div>
</div>

@if($invoice->notes)
<div class="observations">
    OBSERVAÇÕES: {{ $invoice->notes }}
</div>
@endif

<div class="clearfix payment-section">
    <div class="section-title">DADOS PARA PAGAMENTO</div>

    @if(config('company.bank_accounts'))
        @foreach(config('company.bank_accounts') as $bank)
        <div class="clearfix payment-method">
            <div class="bank-logo {{ strtolower($bank['name']) }}-logo">{{ strtoupper($bank['name']) }}</div>
            <div class="payment-details">
                <strong>{{ $bank['bank_name'] ?? $bank['name'] }}</strong><br>
                Nº Conta: {{ $bank['account_number'] }}<br>
                NIB: {{ $bank['nib'] ?? 'N/A' }}
                @if(isset($bank['iban']))
                <br>IBAN: {{ $bank['iban'] }}
                @endif
            </div>
        </div>
        @endforeach
    @else
   <div class="clearfix payment-section">
    <div class="bank-logo">
        <img src="{{ asset('bci.svg') }}"style="width: 20px;" alt="Logo">
    </div>
    <div class="payment-details">
        O pagamento pode ser efectuado em numerário, cheque, depósito ou transferência<br>
        <strong>Conta:</strong> {{ $company->bank_account ?? '222 038 724 100 01' }} |
        <strong>NIB:</strong> {{ $company->bank_nib ?? '0008 0000 2203 8724 101 13' }}
    </div>
</div>
    @endif
</div>

<div class="footer">
    <p>Este documento foi processado por computador e é válido sem assinatura e carimbo.</p>
    <p>Gerado em {{ now()->format('d/m/Y H:i:s') }} |
       Sistema de Faturação v{{ config('app.version', '1.0') }} |
       {{ config('app.name') }}</p>
    @if($invoice->quote)
    <p style="font-size: 8px; margin-top: 5px;">
        Baseada na Cotação: {{ $invoice->quote->quote_number }}
    </p>
    @endif
</div>