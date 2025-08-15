
@extends('admin.layouts.admin')

@section('title', 'Todos os Tickets de Suporte')

@section('content')
<div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold leading-tight text-gray-900">Tickets de Suporte</h1>
                <p class="mt-2 text-gray-600">Gerencie todos os tickets de suporte do sistema</p>
            </div>
            <div class="flex items-center mt-4 space-x-3 sm:mt-0">
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <span class="w-3 h-3 bg-green-400 rounded-full"></span>
                    <span>{{ $stats['open'] ?? 0 }} Abertos</span>
                </div>
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <span class="w-3 h-3 bg-red-400 rounded-full"></span>
                    <span>{{ $stats['high_priority'] ?? 0 }} Urgentes</span>
                </div>
                <a href="{{ route('admin.support.index') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-white rounded-lg shadow">
        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700">Buscar</label>
                    <div class="relative mt-1">
                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ request('search') }}"
                               class="block w-full py-2 pl-10 pr-3 leading-5 placeholder-gray-500 bg-white border border-gray-300 rounded-md focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Buscar por assunto, descrição, número do ticket ou usuário...">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="status" name="status" class="block w-full px-3 py-2 mt-1 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos os status</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Aberto</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Em Andamento</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolvido</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Fechado</option>
                    </select>
                </div>

                <!-- Priority Filter -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700">Prioridade</label>
                    <select id="priority" name="priority" class="block w-full px-3 py-2 mt-1 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todas as prioridades</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Baixa</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Média</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>

                <!-- Assignment Filter -->
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700">Atribuição</label>
                    <select id="assigned_to" name="assigned_to" class="block w-full px-3 py-2 mt-1 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos</option>
                        <option value="unassigned" {{ request('assigned_to') === 'unassigned' ? 'selected' : '' }}>Não Atribuídos</option>
                        {{-- @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ request('assigned_to') == $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }}
                            </option>
                        @endforeach --}}
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="flex items-end space-x-3 lg:col-span-5">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filtrar
                    </button>
                    <a href="{{ route('admin.support.tickets') }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Limpar
                    </a>
                    <div class="flex-1"></div>
                    <div class="flex items-center space-x-2">
                        <label for="bulk-action" class="text-sm font-medium text-gray-700">Ações em lote:</label>
                        <select id="bulk-action" class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Selecione uma ação</option>
                            <option value="assign">Atribuir</option>
                            <option value="close">Fechar</option>
                            <option value="priority">Alterar Prioridade</option>
                        </select>
                        <button type="button"
                                onclick="executeBulkAction()"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Executar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                    {{ $tickets->total() }} Tickets
                    @if(request()->hasAny(['search', 'status', 'priority', 'assigned_to']))
                        <span class="text-sm font-normal text-gray-500">(filtrados)</span>
                    @endif
                </h3>
                <div class="flex items-center space-x-3">
                    <button onclick="exportTickets()"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Exportar
                    </button>
                    <button onclick="refreshTickets()"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Atualizar
                    </button>
                </div>
            </div>
        </div>

        @if($tickets->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="select-all" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                        </th>

<th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Ticket
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Cliente
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Status
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Prioridade
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Atribuído
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Criado
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Última Atividade
                        </th>
                        <th class="relative px-6 py-3">
                            <span class="sr-only">Ações</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($tickets as $ticket)
                    <tr class="hover:bg-gray-50 {{ $ticket->priority === 'urgent' ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="selected_tickets[]" value="{{ $ticket->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded ticket-checkbox focus:ring-blue-500 focus:ring-2">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($ticket->priority === 'urgent')
                                    <svg class="w-4 h-4 mr-2 text-red-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('admin.support.tickets.show', $ticket->id) }}" class="hover:text-blue-600">
                                            #{{ $ticket->ticket_number }}
                                        </a>
                                    </div>
                                    <div class="max-w-xs text-sm text-gray-500 truncate">
                                        {{ $ticket->subject }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10">
                                    @if($ticket->user->avatar)
                                        <img class="w-10 h-10 rounded-full" src="{{ asset('storage/' . $ticket->user->avatar) }}" alt="{{ $ticket->user->name }}">
                                    @else
                                        <div class="flex items-center justify-center w-10 h-10 bg-gray-300 rounded-full">
                                            <span class="text-sm font-medium text-gray-700">{{ substr($ticket->user->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $ticket->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $ticket->user->email }}</div>
                                    @if($ticket->company)
                                        <div class="text-xs text-gray-400">{{ $ticket->company->name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $ticket->status_color }}">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $ticket->priority_color }}">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($ticket->assignedTo)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-6 h-6">
                                        @if($ticket->assignedTo->avatar)
                                            <img class="w-6 h-6 rounded-full" src="{{ asset('storage/' . $ticket->assignedTo->avatar) }}" alt="{{ $ticket->assignedTo->name }}">
                                        @else
                                            <div class="flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full">
                                                <span class="text-xs font-medium text-blue-800">{{ substr($ticket->assignedTo->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-sm text-gray-900">{{ $ticket->assignedTo->name }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-medium text-orange-800 bg-orange-100 rounded-full">
                                    Não Atribuído
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            <div>{{ $ticket->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $ticket->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            @if($ticket->last_activity_at)
                                <div>{{ $ticket->last_activity_at->diffForHumans() }}</div>
                            @else
                                <div>{{ $ticket->created_at->diffForHumans() }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="relative inline-block text-left" x-data="{ open: false }">
                                <button @click="open = !open"
                                        class="inline-flex items-center p-2 text-sm font-medium text-gray-400 bg-white rounded-full hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
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
                                     class="absolute right-0 z-10 w-56 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <div class="py-1">
                                        <a href="{{ route('admin.support.tickets.show', $ticket->id) }}"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Ver Detalhes
                                        </a>

                                        @if(!$ticket->assignedTo)
                                        <button onclick="assignToMe({{ $ticket->id }})"
                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            Atribuir para Mim
                                        </button>
                                        @endif

                                        <button onclick="showAssignModal({{ $ticket->id }})"
                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 12c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/>
                                            </svg>
                                            Reatribuir
                                        </button>

                                        <button onclick="showStatusModal({{ $ticket->id }})"
                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            Alterar Status
                                        </button>

                                        @if($ticket->status !== 'closed')
                                        <button onclick="closeTicket({{ $ticket->id }})"
                                                class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Fechar Ticket
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-white border-t border-gray-200">
            {{ $tickets->appends(request()->query())->links() }}
        </div>
        @else
        <!-- Empty State -->
        <div class="py-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Nenhum ticket encontrado</h3>
            <p class="mt-2 text-gray-500">
                @if(request()->hasAny(['search', 'status', 'priority', 'assigned_to']))
                    Tente ajustar os filtros ou limpar a busca.
                @else
                    Não há tickets de suporte no momento.
                @endif
            </p>
            @if(request()->hasAny(['search', 'status', 'priority', 'assigned_to']))
                <div class="mt-6">
                    <a href="{{ route('admin.support.tickets') }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Limpar Filtros
                    </a>
                </div>
            @endif
        </div>
        @endif
    </div>
</div>

<!-- Assignment Modal -->
<div id="assignModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeAssignModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-6 pt-6 pb-4 bg-white">
                <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900">Atribuir Ticket</h3>

                <div class="space-y-4">
                    <div>
                        <label for="assign-agent" class="block text-sm font-medium text-gray-700">Selecionar Agente</label>
                        <select id="assign-agent" class="block w-full px-3 py-2 mt-1 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Selecione um agente</option>
                            {{-- @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                </div>
            </div>

            <div class="px-6 py-3 bg-gray-50 sm:flex sm:flex-row-reverse">
                <button onclick="executeAssignment()"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Atribuir
                </button>
                <button onclick="closeAssignModal()"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Status Modal -->
<div id="statusModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeStatusModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-6 pt-6 pb-4 bg-white">
                <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900">Alterar Status</h3>

                <div class="space-y-4">
                    <div>
                        <label for="new-status" class="block text-sm font-medium text-gray-700">Novo Status</label>
                        <select id="new-status" class="block w-full px-3 py-2 mt-1 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="open">Aberto</option>
                            <option value="pending">Pendente</option>
                            <option value="in_progress">Em Andamento</option>
                            <option value="resolved">Resolvido</option>
                            <option value="closed">Fechado</option>
                        </select>
                    </div>
                    <div>
                        <label for="status-comment" class="block text-sm font-medium text-gray-700">Comentário (opcional)</label>
                        <textarea id="status-comment" rows="3"
                                  class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Adicione um comentário sobre a mudança de status..."></textarea>
                    </div>
                </div>
            </div>

            <div class="px-6 py-3 bg-gray-50 sm:flex sm:flex-row-reverse">
                <button onclick="executeStatusChange()"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Alterar Status
                </button>
                <button onclick="closeStatusModal()"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentTicketId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.ticket-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Auto-submit form on filter change
    const filterElements = ['status', 'priority', 'assigned_to'];
    filterElements.forEach(elementId => {
        document.getElementById(elementId).addEventListener('change', function() {
            this.form.submit();
        });
    });
});

// Assignment functions
function showAssignModal(ticketId) {
    currentTicketId = ticketId;
    document.getElementById('assignModal').classList.remove('hidden');
}

function closeAssignModal() {
    currentTicketId = null;
    document.getElementById('assignModal').classList.add('hidden');
    document.getElementById('assign-agent').value = '';
}

function assignToMe(ticketId) {
    if (!confirm('Deseja atribuir este ticket para você?')) return;

    executeAssignmentRequest(ticketId, {{ auth()->id() }});
}

function executeAssignment() {
    const agentId = document.getElementById('assign-agent').value;
    if (!agentId) {
        alert('Selecione um agente');
        return;
    }

    executeAssignmentRequest(currentTicketId, agentId);
}

async function executeAssignmentRequest(ticketId, agentId) {
    try {
        const response = await fetch(`/admin/support/tickets/${ticketId}/assign`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                assigned_to: agentId
            })
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Ticket atribuído com sucesso!', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            throw new Error(result.message || 'Erro ao atribuir ticket');
        }

    } catch (error) {
        console.error('Error assigning ticket:', error);
        showNotification('Erro ao atribuir ticket', 'error');
    }

    closeAssignModal();
}

// Status functions
function showStatusModal(ticketId) {
    currentTicketId = ticketId;
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    currentTicketId = null;
    document.getElementById('statusModal').classList.add('hidden');
    document.getElementById('new-status').value = '';
    document.getElementById('status-comment').value = '';
}

async function executeStatusChange() {
    const status = document.getElementById('new-status').value;
    const comment = document.getElementById('status-comment').value;

    try {
        const response = await fetch(`/admin/support/tickets/${currentTicketId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                status: status,
                comment: comment
            })
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Status alterado com sucesso!', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            throw new Error(result.message || 'Erro ao alterar status');
        }

    } catch (error) {
        console.error('Error changing status:', error);
        showNotification('Erro ao alterar status', 'error');
    }

    closeStatusModal();
}

// Close ticket function
async function closeTicket(ticketId) {
    if (!confirm('Deseja fechar este ticket?')) return;

    try {
        const response = await fetch(`/admin/support/tickets/${ticketId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                status: 'closed',
                comment: 'Ticket fechado pelo administrador'
            })
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Ticket fechado com sucesso!', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            throw new Error(result.message || 'Erro ao fechar ticket');
        }

    } catch (error) {
        console.error('Error closing ticket:', error);
        showNotification('Erro ao fechar ticket', 'error');
    }
}

// Bulk actions
function executeBulkAction() {
    const action = document.getElementById('bulk-action').value;
    const selectedTickets = Array.from(document.querySelectorAll('.ticket-checkbox:checked')).map(cb => cb.value);

    if (!action) {
        alert('Selecione uma ação');
        return;
    }

    if (selectedTickets.length === 0) {
        alert('Selecione pelo menos um ticket');
        return;
    }

    if (!confirm(`Deseja executar esta ação em ${selectedTickets.length} ticket(s)?`)) {
        return;
    }

    // Implement bulk actions based on the selected action
    console.log('Bulk action:', action, 'Tickets:', selectedTickets);
    showNotification(`Ação ${action} executada em ${selectedTickets.length} tickets`, 'info');
}

// Utility functions
function refreshTickets() {
    window.location.reload();
}

function exportTickets() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'true');
    window.open(`${window.location.pathname}?${params.toString()}`);
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300`;

    const colors = {
        success: 'bg-green-50 border border-green-200 text-green-800',
        error: 'bg-red-50 border border-red-200 text-red-800',
        info: 'bg-blue-50 border border-blue-200 text-blue-800',
        warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800'
    };

    notification.className += ` ${colors[type]}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-1">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <button onclick="this.closest('div').remove()" class="ml-3 -mx-1.5 -my-1.5 rounded-lg focus:ring-2 p-1.5 inline-flex h-8 w-8">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>
@endpush

@push('styles')
<style>
/* Custom styles for tickets list */
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}

/* Status and priority colors */
.status-open { background-color: #dcfce7; color: #166534; }
.status-pending { background-color: #fef3c7; color: #92400e; }
.status-in-progress, .status-in_progress { background-color: #dbeafe; color: #1e40af; }
.status-resolved { background-color: #f3e8ff; color: #7c3aed; }
.status-closed { background-color: #f3f4f6; color: #374151; }

.priority-low { background-color: #f3f4f6; color: #374151; }
.priority-medium { background-color: #dbeafe; color: #1e40af; }
.priority-high { background-color: #fed7aa; color: #c2410c; }
.priority-urgent { background-color: #fecaca; color: #dc2626; }

/* Table hover effects */
tbody tr:hover {
    background-color: #f9fafb;
}

tbody tr.bg-red-50:hover {
    background-color: #fef2f2;
}

/* Dropdown animation */
[x-cloak] { display: none !important; }

/* Mobile responsive table */
@media (max-width: 768px) {
    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
    }

    table {
        font-size: 0.875rem;
    }

    .px-6 {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .py-4 {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
    }
}

/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Filter form enhancements */
.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-5 {
    gap: 1rem;
    align-items: end;
}

@media (max-width: 1024px) {
    .lg\\:col-span-2 {
        grid-column: span 1;
    }

    .lg\\:col-span-5 {
        grid-column: span 1;
    }
}

/* Loading states */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Focus states */
input:focus,
select:focus,
button:focus,
a:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Avatar improvements */
.rounded-full {
    object-fit: cover;
}

/* Action button hover effects */
.hover\\:bg-gray-100:hover {
    background-color: #f3f4f6;
}

.hover\\:bg-red-50:hover {
    background-color: #fef2f2;
}

/* Modal backdrop */
.bg-opacity-75 {
    backdrop-filter: blur(4px);
}

/* Checkbox styling */
input[type="checkbox"]:checked {
    background-color: currentColor;
    border-color: transparent;
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m13.854 3.646-7.5 7.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6 10.293l7.146-7.147a.5.5 0 0 1 .708.708z'/%3e%3c/svg%3e");
}

/* Notification animations */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.notification-enter {
    animation: slideInRight 0.3s ease-out;
}

/* Badge styling */
.inline-flex.items-center {
    align-items: center;
}

/* Priority urgent special styling */
.priority-urgent {
    animation: pulse 2s infinite;
    box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
}

/* Status indicators */
.status-indicator {
    display: inline-flex;
    align-items: center;
}

.status-indicator::before {
    content: '';
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 8px;
}

.status-open::before { background-color: #22c55e; }
.status-pending::before { background-color: #f59e0b; }
.status-in-progress::before, .status-in_progress::before { background-color: #3b82f6; }
.status-resolved::before { background-color: #8b5cf6; }
.status-closed::before { background-color: #6b7280; }

/* Enhanced table styling */
table {
    border-collapse: separate;
    border-spacing: 0;
}

th {
    background-color: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

td {
    border-bottom: 1px solid #f3f4f6;
}

/* Search input enhancement */
input[type="text"] {
    transition: all 0.2s ease;
}

input[type="text"]:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Button enhancements */
button, a.inline-flex {
    transition: all 0.2s ease;
}

button:hover:not(:disabled), a.inline-flex:hover {
    transform: translateY(-1px);
}

button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* Bulk actions styling */
#bulk-action {
    min-width: 150px;
}

/* Empty state styling */
.text-center svg {
    margin: 0 auto;
}

/* Stats counters in header */
.flex.items-center.space-x-2 {
    white-space: nowrap;
}

.w-3.h-3 {
    flex-shrink: 0;
}

/* Responsive grid adjustments */
@media (max-width: 640px) {
    .sm\\:flex.sm\\:items-center.sm\\:justify-between {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .mt-4.sm\\:mt-0 {
        margin-top: 0;
        width: 100%;
    }

    .flex.items-center.mt-4.space-x-3.sm\\:mt-0 {
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
    }
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }

    table {
        page-break-inside: avoid;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }

    thead {
        display: table-header-group;
    }
}
</style>
@endpush
@endsection{{-- resources/views/admin/support/tickets/index.blade.php --}}
