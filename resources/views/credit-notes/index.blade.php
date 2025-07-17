@extends('layouts.app')

@section('title', 'Notas de Crédito')
@section('subtitle', 'Gerencie suas notas de crédito')

@section('header-actions')
<div class="flex space-x-3">
    <a href="{{ route('credit-notes.create') }}"
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-orange-600 rounded-lg hover:bg-orange-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nova Nota de Crédito
    </a>
</div>
@endsection

@section('content')
<!-- Estatísticas -->
<div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m0 0l6 6m-6-6v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h7"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Total de Notas</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $creditNotes->total() }}</p>
            </div>
        </div>
    </div>

    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Valor Total</h3>
                <p class="text-2xl font-bold text-red-600">
                    {{ number_format($creditNotes->sum('total'), 2) }} MT
                </p>
            </div>
        </div>
    </div>

    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Este Mês</h3>
                <p class="text-2xl font-bold text-blue-600">
                    {{ $creditNotes->filter(function($note) {
                        return $note->created_at->isCurrentMonth();
                    })->count() }}
                </p>
            </div>
        </div>
    </div>

    <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-900">Processadas</h3>
                <p class="text-2xl font-bold text-green-600">100%</p>
                <p class="text-xs text-gray-500">Todas processadas</p>
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
        <form method="GET" action="{{ route('credit-notes.index') }}" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Pesquisar por número ou cliente..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                       value="{{ request('search') }}">
            </div>
            <div>
                <select name="client_id" class="px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Todos os clientes</option>
                    @foreach(App\Models\Client::orderBy('name')->get() as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
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
            <button type="submit" class="px-6 py-2 text-white bg-orange-600 rounded-lg hover:bg-orange-700">
                Filtrar
            </button>
            <a href="{{ route('credit-notes.index') }}" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                Limpar
            </a>
        </form>
    </div>
</div>

<!-- Lista de Notas de Crédito -->
<div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Notas de Crédito</h3>
    </div>

    @if($creditNotes->count() > 0)
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
                        Motivo
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
                @foreach($creditNotes as $creditNote)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('credit-notes.show', $creditNote) }}"
                           class="text-sm font-medium text-orange-600 hover:text-orange-900">
                            {{ $creditNote->invoice_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $creditNote->client->name }}</div>
                        <div class="text-sm text-gray-500">{{ $creditNote->client->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($creditNote->relatedInvoice)
                            <a href="{{ route('invoices.show', $creditNote->relatedInvoice) }}"
                               class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $creditNote->relatedInvoice->invoice_number }}
                            </a>
                        @else
                            <span class="text-sm text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                        {{ $creditNote->invoice_date->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="max-w-xs text-sm text-gray-900 truncate">
                            {{ $creditNote->adjustment_reason }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-right text-red-600 whitespace-nowrap">
                        -{{ number_format($creditNote->total, 2) }} MT
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="{{ route('credit-notes.show', $creditNote) }}"
                               class="p-2 text-blue-600 transition-colors rounded-lg bg-blue-50 hover:bg-blue-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <button onclick="downloadPDF({{ $creditNote->id }})"
                                    class="p-2 text-green-600 transition-colors rounded-lg bg-green-50 hover:bg-green-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $creditNotes->withQueryString()->links() }}
    </div>
    @else
    <div class="px-6 py-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 14l6-6m0 0l6 6m-6-6v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h7"/>
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">Nenhuma nota de crédito encontrada</h3>
        <p class="mt-2 text-sm text-gray-500">Comece criando sua primeira nota de crédito.</p>
        <div class="mt-6">
            <a href="{{ route('credit-notes.create') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Criar Nota de Crédito
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
