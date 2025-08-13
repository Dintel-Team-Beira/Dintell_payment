@extends('layouts.admin')

@section('title', 'Fatura #' . $invoice->invoice_number)

@section('header-actions')
<div class="flex items-center gap-x-4">
    <a href="{{ route('admin.invoices.index') }}"
       class="flex items-center px-3 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Voltar
    </a>

    <div class="flex items-center space-x-2">
        @if($invoice->status !== 'paid')
        <form action="{{ route('admin.invoices.mark-as-paid', $invoice) }}" method="POST" class="inline">
            @csrf
            <button type="submit"
                    class="flex items-center px-3 py-2 text-sm font-semibold text-green-700 bg-green-100 border border-green-200 rounded-md hover:bg-green-200"
                    onclick="return confirm('Marcar esta fatura como paga?')">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Marcar como Paga
            </button>
        </form>
        @endif

        <div class="relative inline-block text-left" x-data="{ open: false }">
            <button type="button"
                    class="flex items-center px-3 py-2 text-sm font-semibold text-blue-700 bg-blue-100 border border-blue-200 rounded-md hover:bg-blue-200"
                    @click="open = !open">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exportar
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open"
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 z-10 w-48 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                <div class="py-1">
                    <a href="{{ route('admin.invoices.pdf', $invoice) }}"
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download PDF
                    </a>
                    <a href="{{ route('admin.invoices.view-pdf', $invoice) }}"
                       target="_blank"
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Visualizar PDF
                    </a>
                    <a href="{{ route('admin.invoices.print', $invoice) }}"
                       target="_blank"
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Imprimir
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <!-- Header da Fatura -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center">
                    @if($invoice->company && $invoice->company->logo)
                    <img src="{{ Storage::url($invoice->company->logo) }}" alt="{{ $invoice->company->name }}" class="w-16 h-16 mr-4 rounded-lg">
                    @else
                    <div class="flex items-center justify-center w-16 h-16 mr-4 bg-gray-100 rounded-lg">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    @endif

                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Fatura #{{ $invoice->invoice_number }}</h1>
                        <div class="flex items-center mt-2 space-x-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' :
                                   ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' :
                                   ($invoice->status === 'sent' ? 'bg-blue-100 text-blue-800' :
                                   ($invoice->status === 'draft' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800'))) }}">
                                {{ ucfirst($invoice->status) }}
                            </span>

                            @if($invoice->payment_method)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $invoice->payment_method_label }}
                            </span>
                            @endif

                            @if($invoice->document_type && $invoice->document_type !== 'invoice')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                {{ $invoice->document_type_label }}
                            </span>
                            @endif
                        </div>
                        <div class="flex items-center mt-2 space-x-4 text-sm text-gray-500">
                            <span>Criado: {{ $invoice->invoice_date->format('d/m/Y') }}</span>
                            <span>Vencimento: {{ $invoice->due_date->format('d/m/Y') }}</span>
                            @if($invoice->paid_at)
                            <span>Pago: {{ $invoice->paid_at->format('d/m/Y H:i') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <div class="text-3xl font-bold text-gray-900">{{ number_format($invoice->total, 2) }} MT</div>
                    @if($invoice->paid_amount > 0 && $invoice->paid_amount < $invoice->total)
                    <div class="text-sm text-green-600">Pago: {{ number_format($invoice->paid_amount, 2) }} MT</div>
                    <div class="text-sm text-red-600">Pendente: {{ number_format($invoice->total - $invoice->paid_amount, 2) }} MT</div>
                    @elseif($invoice->status === 'paid')
                    <div class="text-sm text-green-600">✓ Totalmente Pago</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Informações Principais -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Dados da Empresa e Cliente -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Empresa -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Empresa</h3>
                    </div>
                    <div class="p-6">
                        @if($invoice->company)
                        <div class="space-y-2">
                            <div class="text-sm">
                                <span class="font-medium text-gray-900">{{ $invoice->company->name }}</span>
                            </div>
                            <div class="text-sm text-gray-600">{{ $invoice->company->email }}</div>
                            @if($invoice->company->phone)
                            <div class="text-sm text-gray-600">{{ $invoice->company->phone }}</div>
                            @endif
                            @if($invoice->company->address)
                            <div class="text-sm text-gray-600">{{ $invoice->company->address }}</div>
                            @endif
                            @if($invoice->company->tax_number)
                            <div class="text-sm text-gray-600">NUIT: {{ $invoice->company->tax_number }}</div>
                            @endif
                            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $invoice->company->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($invoice->company->status) }}
                            </div>
                        </div>
                        @else
                        <div class="text-sm text-gray-500">Empresa não encontrada</div>
                        @endif
                    </div>
                </div>

                <!-- Cliente -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Cliente</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-2">
                            <div class="text-sm">
                                <span class="font-medium text-gray-900">{{ $invoice->client?->name }}</span>
                            </div>
                            <div class="text-sm text-gray-600">{{ $invoice->client?->email }}</div>
                            @if($invoice->client?->phone)
                            <div class="text-sm text-gray-600">{{ $invoice->client?->phone }}</div>
                            @endif
                            @if($invoice->client?->company)
                            <div class="text-sm text-gray-600">{{ $invoice->client?->company }}</div>
                            @endif
                            @if($invoice->client?->address)
                            <div class="text-sm text-gray-600">{{ $invoice->client?->address }}</div>
                            @endif
                            @if($invoice->client?->tax_number)
                            <div class="text-sm text-gray-600">NUIT: {{ $invoice->client?->tax_number }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Itens da Fatura -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Itens da Fatura</h3>
                        <span class="text-sm text-gray-500">{{ $invoice->items->count() }} item(s)</span>
                    </div>
                </div>
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Item</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Qtd</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Preço Unit.</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">IVA</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($invoice->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                    @if($item->description)
                                    <div class="text-sm text-gray-500">{{ $item->description }}</div>
                                    @endif
                                    <div class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mt-1">
                                        {{ ucfirst($item->type) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-center text-gray-900">
                                    {{ number_format($item->quantity, 2) }} {{ $item->unit }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">
                                    {{ number_format($item->unit_price, 2) }} MT
                                </td>
                                <td class="px-6 py-4 text-sm text-center text-gray-900">
                                    {{ number_format($item->tax_rate, 2) }}%
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-right text-gray-900">
                                    {{ number_format($item->total_price, 2) }} MT
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals Section -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex justify-end">
                        <div class="w-64 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium text-gray-900">{{ number_format($invoice->subtotal, 2) }} MT</span>
                            </div>
                            @if($invoice->discount_amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">
                                    Desconto
                                    @if($invoice->discount_percentage > 0)
                                    ({{ number_format($invoice->discount_percentage, 2) }}%)
                                    @endif:
                                </span>
                                <span class="font-medium text-red-600">-{{ number_format($invoice->discount_amount, 2) }} MT</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">IVA:</span>
                                <span class="font-medium text-gray-900">{{ number_format($invoice->tax_amount, 2) }} MT</span>
                            </div>
                            <div class="flex justify-between pt-2 text-lg font-bold border-t border-gray-200">
                                <span class="text-gray-900">Total:</span>
                                <span class="text-gray-900">{{ number_format($invoice->total, 2) }} MT</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Observações -->
            @if($invoice->notes || $invoice->terms_conditions)
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Observações e Termos</h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($invoice->notes)
                    <div>
                        <h4 class="mb-2 text-sm font-medium text-gray-900">Observações</h4>
                        <p class="text-sm text-gray-600">{{ $invoice->notes }}</p>
                    </div>
                    @endif

                    @if($invoice->terms_conditions)
                    <div>
                        <h4 class="mb-2 text-sm font-medium text-gray-900">Termos e Condições</h4>
                        <p class="text-sm text-gray-600">{{ $invoice->terms_conditions }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Resumo Rápido -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Resumo</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Status:</span>
                        <span class="text-sm font-medium text-gray-900">{{ ucfirst($invoice->status) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Tipo:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $invoice->document_type_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Método de Pagamento:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $invoice->payment_method_label }}</span>
                    </div>
                    @if($invoice->is_cash_sale)
                    <div class="p-3 border border-green-200 rounded-lg bg-green-50">
                        <div class="text-sm font-medium text-green-800">Venda à Dinheiro</div>
                        @if($invoice->cash_received > 0)
                        <div class="text-sm text-green-600">Recebido: {{ number_format($invoice->cash_received, 2) }} MT</div>
                        @endif
                        @if($invoice->change_given > 0)
                        <div class="text-sm text-green-600">Troco: {{ number_format($invoice->change_given, 2) }} MT</div>
                        @endif
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Prazo de Pagamento:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $invoice->payment_terms_days }} dias</span>
                    </div>
                    @if($invoice->due_date->isPast() && $invoice->status !== 'paid')
                    <div class="p-3 border border-red-200 rounded-lg bg-red-50">
                        <div class="text-sm font-medium text-red-800">Vencida há {{ $invoice->due_date->diffInDays() }} dias</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Histórico de Pagamentos -->
            @if($invoice->payments && $invoice->payments->count() > 0)
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Histórico de Pagamentos</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach($invoice->payments as $payment)
                        <div class="flex items-center justify-between p-3 border border-green-200 rounded-lg bg-green-50">
                            <div>
                                <div class="text-sm font-medium text-green-800">{{ number_format($payment->amount, 2) }} MT</div>
                                <div class="text-xs text-green-600">{{ $payment->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="text-xs text-green-600">{{ $payment->method }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Ações Administrativas -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Ações</h3>
                </div>
                <div class="p-6 space-y-3">
                    @if($invoice->status !== 'paid')
                    <form action="{{ route('admin.invoices.mark-as-paid', $invoice) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-green-700 border border-green-200 rounded-md bg-green-50 hover:bg-green-100"
                                onclick="return confirm('Marcar como paga?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Marcar como Paga
                        </button>
                    </form>
                    @endif

                    <div class="flex items-center space-x-2">
                        <select name="status" id="status-select" class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="draft" {{ $invoice->status === 'draft' ? 'selected' : '' }}>Rascunho</option>
                            <option value="sent" {{ $invoice->status === 'sent' ? 'selected' : '' }}>Enviada</option>
                            <option value="paid" {{ $invoice->status === 'paid' ? 'selected' : '' }}>Paga</option>
                            <option value="overdue" {{ $invoice->status === 'overdue' ? 'selected' : '' }}>Vencida</option>
                            <option value="cancelled" {{ $invoice->status === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                        <button type="button"
                                onclick="updateStatus()"
                                class="px-3 py-2 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover:bg-blue-100">
                            Atualizar
                        </button>
                    </div>

                