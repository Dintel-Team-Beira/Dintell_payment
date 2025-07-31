@extends('layouts.app')

@section('title', 'Notas de Débito')
@section('subtitle', 'Gerencie suas notas de débito')

@section('header-actions')
<div class="flex space-x-3">
    <a href="{{ company_route('debit-notes.create') }}"
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-yellow-600 rounded-lg hover:bg-yellow-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nova Nota de Débito
    </a>
</div>
@endsection

@section('content')
<!-- Estatísticas -->
<div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Total de Notas</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $debitNotes->total() }}</p>
            </div>
        </div>
    </div>

    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Valor Total</h3>
                <p class="text-2xl font-bold text-green-600">
                    {{ number_format($debitNotes->sum('total'), 2) }} MT
                </p>
            </div>
        </div>
    </div>

    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Pendentes</h3>
                <p class="text-2xl font-bold text-blue-600">
                    {{ $debitNotes->where('status', 'sent')->count() }}
                </p>
            </div>
        </div>
    </div>

    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Vencidas</h3>
                <p class="text-2xl font-bold text-red-600">
                    {{ $debitNotes->filter(function($note) {
                        return $note->status !== 'paid' && $note->due_date < now();
                    })->count() }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="mb-8 bg-white border border-gray-200 shadow-sm rounded-xl">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Filtros</h3>
    </div>
    <div class="p-6">
        <form method="GET" action="{{ company_route('debit-notes.index') }}" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Pesquisar por número ou cliente..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500"
                       value="{{ request('search') }}">
            </div>
            <div>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Todos os status</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Enviada</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paga</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Vencida</option>
                </select>
            </div>
            <div>
                <input type="date" name="date_from" class="px-4 py-2 border border-gray-300 rounded-lg"
                       value="{{ request('date_from') }}">
            </div>
            <div>
                <input type="date" name="date_to" class="px-4 py-2 border border-gray-300 rounded-lg"
                       value="{{ request('date_to') }}">
            </div>
            <button type="submit" class="px-6 py-2 text-white bg-yellow-600 rounded-lg hover:bg-yellow-700">
                Filtrar
            </button>
            <a href="{{ company_route('debit-notes.index') }}" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                Limpar
            </a>
        </form>
    </div>
</div>

<!-- Lista de Notas de Débito -->
<div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Notas de Débito</h3>
    </div>

    @if($debitNotes->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        Número
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        Cliente
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        Fatura Relacionada
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        Data
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        Vencimento
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        Status
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                        Valor
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($debitNotes as $debitNote)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ company_route('debit-notes.show', $debitNote) }}"
                           class="text-sm font-medium text-yellow-600 hover:text-yellow-900">
                            {{ $debitNote->invoice_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $debitNote->client->name }}</div>
                        <div class="text-sm text-gray-500">{{ $debitNote->client->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($debitNote->relatedInvoice)
                            <a href="{{ company_route('invoices.show', $debitNote->relatedInvoice) }}"
                               class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $debitNote->relatedInvoice->invoice_number }}
                            </a>
                        @else
                            <span class="text-sm text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                        {{ $debitNote->invoice_date->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                        @if($debitNote->isOverdue() && $debitNote->status !== 'paid')
                            <span class="font-medium text-red-600">
                                {{ $debitNote->due_date->format('d/m/Y') }}
                            </span>
                        @else
                            {{ $debitNote->due_date->format('d/m/Y') }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusClasses = [
                                'sent' => 'bg-yellow-100 text-yellow-800',
                                'paid' => 'bg-green-100 text-green-800',
                                'overdue' => 'bg-red-100 text-red-800'
                            ];
                            $statusLabels = [
                                'sent' => 'Enviada',
                                'paid' => 'Paga',
                                'overdue' => 'Vencida'
                            ];
                            $status = $debitNote->isOverdue() && $debitNote->status !== 'paid' ? 'overdue' : $debitNote->status;
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$status] }}">
                            {{ $statusLabels[$status] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-right text-green-600 whitespace-nowrap">
                        +{{ number_format($debitNote->total, 2) }} MT
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="{{ company_route('debit-notes.show', $debitNote) }}"
                               class="p-2 text-blue-600 transition-colors rounded-lg bg-blue-50 hover:bg-blue-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            @if($debitNote->status !== 'paid')
                                <button onclick="markAsPaid({{ $debitNote->id }})"
                                        class="p-2 text-green-600 transition-colors rounded-lg bg-green-50 hover:bg-green-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $debitNotes->withQueryString()->links() }}
    </div>
    @else
    <div class="px-6 py-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4v16m8-8H4"/>
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">Nenhuma nota de débito encontrada</h3>
        <p class="mt-2 text-sm text-gray-500">Comece criando sua primeira nota de débito.</p>
        <div class="mt-6">
            <a href="{{ company_route('debit-notes.create') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Criar Nota de Débito
            </a>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function markAsPaid(debitNoteId) {
    if (confirm('Deseja marcar esta nota de débito como paga?')) {
        // Implementar chamada AJAX
    }
}
</script>
@endpush
@endsection
