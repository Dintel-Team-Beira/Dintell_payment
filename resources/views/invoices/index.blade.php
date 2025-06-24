@extends('layouts.app')

@section('title', 'Faturas')

@section('header-actions')
<div class="flex items-center gap-x-4">
    <form method="GET" action="{{ route('invoices.index') }}" class="flex items-center gap-x-2">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar faturas..."
                   class="block w-full rounded-md border-0 py-1.5 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
        </div>

        <button type="submit" class="px-3 py-2 text-sm font-semibold text-white bg-gray-600 rounded-md shadow-sm hover:bg-gray-500">
            Filtrar
        </button>

        @if(request()->has('search'))
        <a href="{{ route('invoices.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            Limpar
        </a>
        @endif
    </form>

    <a href="{{ route('invoices.create') }}"
       class="flex items-center px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
        <svg class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
        </svg>
        Nova Fatura
    </a>
</div>
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Faturas</h1>
    </div>

    <!-- Filtros -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Filtros</h3>
            <p class="mt-1 text-sm text-gray-500">Filtre as faturas por status, cliente ou data</p>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('invoices.index') }}">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status"
                                class="mt-1 block w-full rounded-md border-0 py-1.5 pl-3 pr-8 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                            <option value="">Todos</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
                            <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Enviada</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paga</option>
                            <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Vencida</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700">Cliente</label>
                        <select name="client_id" id="client_id"
                                class="mt-1 block w-full rounded-md border-0 py-1.5 pl-3 pr-8 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                            <option value="">Todos os clientes</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700">Data de</label>
                        <input type="date" name="date_from" id="date_from"
                               class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm"
                               value="{{ request('date_from') }}">
                    </div>
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700">Data até</label>
                        <input type="date" name="date_to" id="date_to"
                               class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm"
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="flex items-end">
                        <div class="flex gap-x-2">
                            <button type="submit"
                                    class="px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-500">
                                Filtrar
                            </button>
                            <a href="{{ route('invoices.index') }}"
                               class="px-3 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-md shadow-sm hover:bg-gray-200">
                                Limpar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pendente</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_pending'], 2) }} MT</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Vencido</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_overdue'], 2) }} MT</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pago Este Mês</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_paid_this_month'], 2) }} MT</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="flex items-center">
                <div class="p-2 bg-teal-100 rounded-lg">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Vencidas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['count_overdue'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Faturas -->
    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Lista de Faturas</h3>
            <p class="mt-1 text-sm text-gray-500">Gerencie todas as faturas do sistema</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Número</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Cliente</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Data</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Vencimento</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Total</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('invoices.show', $invoice) }}"
                               class="text-blue-600 hover:text-blue-900">
                                {{ $invoice->invoice_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            {{ $invoice->client->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            {{ $invoice->invoice_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $invoice->isOverdue() && $invoice->status !== 'paid' ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                            {{ $invoice->due_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClasses = [
                                    'draft' => 'bg-gray-100 text-gray-800',
                                    'sent' => 'bg-yellow-100 text-yellow-800',
                                    'paid' => 'bg-green-100 text-green-800',
                                    'overdue' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-gray-300 text-gray-900'
                                ];
                                $statusLabels = [
                                    'draft' => 'Rascunho',
                                    'sent' => 'Enviada',
                                    'paid' => 'Paga',
                                    'overdue' => 'Vencida',
                                    'cancelled' => 'Cancelada'
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$invoice->status] }}">
                                {{ $statusLabels[$invoice->status] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            {{ number_format($invoice->total, 2) }} MT
                        </td>
                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('invoices.show', $invoice) }}"
                                   class="text-blue-600 hover:text-blue-900" title="Ver">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if($invoice->status !== 'paid')
                                    <a href="{{ route('invoices.edit', $invoice) }}"
                                       class="text-yellow-600 hover:text-yellow-900" title="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </a>
                                @endif
                                <a href="{{ route('invoices.download-pdf', $invoice) }}"
                                   class="text-teal-600 hover:text-teal-900" title="Baixar PDF">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2-12H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma fatura encontrada</h3>
                            <p class="mt-1 text-sm text-gray-500">Comece criando sua primeira fatura.</p>
                            <div class="mt-6">
                                <a href="{{ route('invoices.create') }}"
                                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700">
                                    <svg class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
                                    </svg>
                                    Nova Fatura
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
        <div class="px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
            {{ $invoices->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection