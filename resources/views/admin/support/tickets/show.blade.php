@extends('layouts.admin')

@section('title', 'Ticket #' . ($ticket['ticket_number'] ?? $ticket['id']))

@section('content')
<div class="container px-6 py-8 mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <div class="flex items-center mb-2">
                    <h1 class="text-3xl font-bold text-gray-900">
                        Ticket #{{ $ticket['ticket_number'] ?? 'TK-' . str_pad($ticket['id'], 6, '0', STR_PAD_LEFT) }}
                    </h1>
                    <span class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($ticket['status'] === 'open') bg-red-100 text-red-800
                        @elseif($ticket['status'] === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($ticket['status'] === 'resolved') bg-green-100 text-green-800
                        @elseif($ticket['status'] === 'closed') bg-gray-100 text-gray-800
                        @else bg-blue-100 text-blue-800 @endif">
                        {{ ucfirst($ticket['status']) }}
                    </span>
                </div>
                <p class="text-gray-600">{{ $ticket['subject'] }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.support.tickets') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar à Lista
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Conversa Principal -->
        <div class="lg:col-span-2">
            <!-- Ticket Original -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            @if(isset($ticket['user']['avatar']) && $ticket['user']['avatar'])
                                <img class="w-10 h-10 rounded-full" src="{{ $ticket['user']['avatar'] }}" alt="{{ $ticket['user']['name'] }}">
                            @else
                                <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full">
                                    <span class="text-sm font-medium text-blue-800">
                                        {{ substr($ticket['user']['name'] ?? 'U', 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">{{ $ticket['user']['name'] ?? 'Usuário' }}</p>
                                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($ticket['created_at'])->diffForHumans() }}</p>
                            </div>
                            <p class="text-sm text-gray-500">{{ $ticket['user']['email'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">{{ $ticket['subject'] }}</h3>
                    <div class="prose text-gray-700 max-w-none">
                        {!! nl2br(e($ticket['description'])) !!}
                    </div>
                </div>
            </div>

            <!-- Respostas -->
            @if(isset($ticket['replies']) && count($ticket['replies']) > 0)
                <div class="mt-6 space-y-6">
                    @foreach($ticket['replies'] as $reply)
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                            <div class="p-6">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        @if(isset($reply['user']['avatar']) && $reply['user']['avatar'])
                                            <img class="w-8 h-8 rounded-full" src="{{ $reply['user']['avatar'] }}" alt="{{ $reply['user']['name'] }}">
                                        @else
                                            <div class="flex items-center justify-center w-8 h-8 {{ $reply['is_admin'] ? 'bg-purple-100' : 'bg-blue-100' }} rounded-full">
                                                <span class="text-xs font-medium {{ $reply['is_admin'] ? 'text-purple-800' : 'text-blue-800' }}">
                                                    {{ substr($reply['user']['name'] ?? 'U', 0, 1) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2">
                                                <p class="text-sm font-medium text-gray-900">{{ $reply['user']['name'] ?? 'Usuário' }}</p>
                                                @if($reply['is_admin'])
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                        Admin
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($reply['created_at'])->diffForHumans() }}</p>
                                        </div>
                                        <div class="prose text-gray-700 max-w-none">
                                            {!! nl2br(e($reply['message'])) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Formulário de Resposta -->
            @if(in_array($ticket['status'], ['open', 'pending']))
                <div class="mt-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Responder ao Ticket</h3>
                    </div>
                    <form id="replyForm" class="p-6">
                        @csrf
                        <div class="mb-4">
                            <label for="message" class="block mb-2 text-sm font-medium text-gray-700">Mensagem</label>
                            <textarea id="message" name="message" rows="4" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Digite sua resposta..."></textarea>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="change_status" id="change_status" class="text-blue-600 border-gray-300 rounded shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Alterar status</span>
                                </label>

                                <select name="new_status" id="new_status" disabled
                                        class="border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="open">Aberto</option>
                                    <option value="pending">Pendente</option>
                                    <option value="resolved">Resolvido</option>
                                    <option value="closed">Fechado</option>
                                </select>
                            </div>

                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <span class="btn-text">Enviar Resposta</span>
                                <span class="hidden btn-loading">
                                    <svg class="w-4 h-4 mr-2 -ml-1 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Enviando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <!-- Sidebar com Informações -->
        <div class="space-y-6">
            <!-- Informações do Ticket -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informações do Ticket</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Prioridade</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($ticket['priority'] === 'low') bg-gray-100 text-gray-800
                                @elseif($ticket['priority'] === 'medium' || $ticket['priority'] === 'normal') bg-blue-100 text-blue-800
                                @elseif($ticket['priority'] === 'high') bg-orange-100 text-orange-800
                                @elseif($ticket['priority'] === 'urgent') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($ticket['priority'] ?? 'normal') }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Categoria</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @php
                                $categories = [
                                    'technical' => 'Técnico',
                                    'billing' => 'Faturação',
                                    'general' => 'Geral',
                                    'feature' => 'Funcionalidade',
                                    'bug' => 'Bug'
                                ];
                            @endphp
                            {{ $categories[$ticket['category']] ?? ucfirst($ticket['category']) }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Criado em</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($ticket['created_at'])->format('d/m/Y H:i') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Última atualização</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($ticket['updated_at'])->diffForHumans() }}
                        </dd>
                    </div>

                    @if(isset($ticket['company']))
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Empresa</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $ticket['company']['name'] }}</dd>
                    </div>
                    @endif

                    @if(isset($ticket['assignedTo']))
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Atribuído para</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $ticket['assignedTo']['name'] }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Ações</h3>
                </div>
                <div class="p-6 space-y-3">
                    @if($ticket['status'] === 'open')
                        <button onclick="updateTicketStatus('pending')"
                                class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-yellow-700 border border-yellow-300 rounded-md shadow-sm bg-yellow-50 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Marcar como Pendente
                        </button>
                    @endif

                    @if(in_array($ticket['status'], ['open', 'pending']))
                        <button onclick="updateTicketStatus('resolved')"
                                class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-green-700 border border-green-300 rounded-md shadow-sm bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Marcar como Resolvido
                        </button>
                    @endif

                    @if($ticket['status'] === 'resolved')
                        <button onclick="updateTicketStatus('closed')"
                                class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-md shadow-sm bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Fechar Ticket
                        </button>
                    @endif

                    @if(in_array($ticket['status'], ['resolved', 'closed']))
                        <button onclick="updateTicketStatus('open')"
                                class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-blue-700 border border-blue-300 rounded-md shadow-sm bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reabrir Ticket
                        </button>
                    @endif

                    <hr class="my-4">

                    <button onclick="assignTicket()"
                            class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-indigo-700 border border-indigo-300 rounded-md shadow-sm bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Atribuir Ticket
                    </button>
                </div>
            </div>

            <!-- Histórico (se disponível) -->
            @if(isset($ticket['metadata']) && is_array($ticket['metadata']))
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Metadados</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-2">
                        @foreach($ticket['metadata'] as $key => $value)
                            <div>
                                <dt class="text-xs font-medium text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                                <dd class="text-xs text-gray-900">{{ is_string($value) ? $value : json_encode($value) }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para Atribuir Ticket -->
<div id="assignModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="assignForm">
                @csrf
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900">Atribuir Ticket</h3>
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700">Atribuir para</label>
                        <select id="assigned_to" name="assigned_to" required
                                class="block w-full py-2 pl-3 pr-10 mt-1 text-base border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Selecione um usuário</option>
                            <!-- As opções serão carregadas via JavaScript -->
                        </select>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Atribuir
                    </button>
                    <button type="button" onclick="closeAssignModal()"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ticketId = {{ $ticket['id'] }};

    // Toggle change status
    document.getElementById('change_status').addEventListener('change', function() {
        document.getElementById('new_status').disabled = !this.checked;
    });

    // Reply form submission
    document.getElementById('replyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitReply();
    });

    // Assign form submission
    if (document.getElementById('assignForm')) {
        document.getElementById('assignForm').addEventListener('submit', function(e) {
            e.preventDefault();
            submitAssignment();
        });
    }

    async function submitReply() {
        const form = document.getElementById('replyForm');
        const formData = new FormData(form);
        const button = form.querySelector('button[type="submit"]');
        const btnText = button.querySelector('.btn-text');
        const btnLoading = button.querySelector('.btn-loading');

        // Show loading
        btnText.classList.add('hidden');
        btnLoading.classList.remove('hidden');
        button.disabled = true;

        try {
            const response = await fetch(`{{ route('admin.support.tickets.reply', $ticket['id']) }}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                showNotification('Resposta enviada com sucesso!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(result.message || 'Erro ao enviar resposta');
            }
        } catch (error) {
            showNotification('Erro ao enviar resposta: ' + error.message, 'error');
        } finally {
            btnText.classList.remove('hidden');
            btnLoading.classList.add('hidden');
            button.disabled = false;
        }
    }

    async function submitAssignment() {
        const form = document.getElementById('assignForm');
        const formData = new FormData(form);

        try {
            const response = await fetch(`{{ route('admin.support.tickets.assign', $ticket['id']) }}`, {
                method: 'PATCH',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                showNotification('Ticket atribuído com sucesso!', 'success');
                closeAssignModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(result.message || 'Erro ao atribuir ticket');
            }
        } catch (error) {
            showNotification('Erro ao atribuir ticket: ' + error.message, 'error');
        }
    }

    window.updateTicketStatus = async function(status) {
        try {
            const response = await fetch(`{{ route('admin.support.tickets.status', $ticket['id']) }}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            });

            const result = await response.json();

            if (result.success) {
                showNotification('Status atualizado com sucesso!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(result.message || 'Erro ao atualizar status');
            }
        } catch (error) {
            showNotification('Erro ao atualizar status: ' + error.message, 'error');
        }
    };

    window.assignTicket = function() {
        document.getElementById('assignModal').classList.remove('hidden');
        loadUsers();
    };

    window.closeAssignModal = function() {
        document.getElementById('assignModal').classList.add('hidden');
    };

    async function loadUsers() {
        try {
            // Simular carregamento de usuários - substitua pela sua rota real
            const users = [
                { id: 1, name: 'Admin Principal' },
                { id: 2, name: 'Suporte Técnico' },
                { id: 3, name: 'Gerente de Suporte' }
            ];

            const select = document.getElementById('assigned_to');
            select.innerHTML = '<option value="">Selecione um usuário</option>';

            users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = user.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Erro ao carregar usuários:', error);
        }
    }

    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());

        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;

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
                <div class="ml-3">
                    <button class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none" onclick="this.closest('.notification').remove()">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
            notification.classList.add('translate-x-0');
        }, 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }
});
</script>
@endpush

@push('styles')
<style>
/* Custom styles for ticket show page */
.prose {
    max-width: none;
}

.prose p {
    margin-bottom: 1em;
}

/* Animation for notifications */
.notification {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Modal backdrop */
#assignModal .fixed.inset-0 {
    backdrop-filter: blur(4px);
}

/* Loading spinner animation */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Button hover effects */
button:hover:not(:disabled) {
    transform: translateY(-1px);
}

button:disabled {
    transform: none;
    opacity: 0.6;
    cursor: not-allowed;
}

/* Status colors */
.status-open {
    background-color: #fef2f2;
    color: #991b1b;
}

.status-pending {
    background-color: #fffbeb;
    color: #92400e;
}

.status-resolved {
    background-color: #f0fdf4;
    color: #166534;
}

.status-closed {
    background-color: #f9fafb;
    color: #374151;
}

/* Priority colors */
.priority-low {
    background-color: #f9fafb;
    color: #374151;
}

.priority-normal {
    background-color: #eff6ff;
    color: #1e40af;
}

.priority-high {
    background-color: #fff7ed;
    color: #c2410c;
}

.priority-urgent {
    background-color: #fef2f2;
    color: #dc2626;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .lg\:col-span-2 {
        grid-column: span 1;
    }

    .space-x-3 > * + * {
        margin-left: 0;
        margin-top: 0.75rem;
    }

    .flex.space-x-3 {
        flex-direction: column;
    }
}

/* Enhanced card styling */
.bg-white {
    transition: all 0.2s ease-in-out;
}

.bg-white:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Form styling improvements */
input:focus,
select:focus,
textarea:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 0 2px rgba(59, 130, 246, 0.5);
}

/* Avatar fallback styling */
.bg-blue-100 {
    background-color: #dbeafe;
}

.text-blue-800 {
    color: #1e40af;
}

.bg-purple-100 {
    background-color: #e9d5ff;
}

.text-purple-800 {
    color: #6b21a8;
}

/* Reply message styling */
.prose {
    word-wrap: break-word;
    overflow-wrap: break-word;
}

/* Action buttons spacing */
.space-y-3 > * + * {
    margin-top: 0.75rem;
}

/* Modal styling */
.fixed.inset-0.z-50 {
    backdrop-filter: blur(8px);
    background-color: rgba(0, 0, 0, 0.6);
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Focus improvements */
.focus\:ring-2:focus {
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

.focus\:ring-offset-2:focus {
    box-shadow: 0 0 0 2px #fff, 0 0 0 4px rgba(59, 130, 246, 0.5);
}

/* Icon alignments */
.inline-flex.items-center {
    align-items: center;
}

/* Improved checkbox styling */
input[type="checkbox"]:checked {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

/* Better spacing for description lists */
dl.space-y-2 > * + * {
    margin-top: 0.5rem;
}

/* Enhanced button states */
.bg-yellow-50:hover {
    background-color: #fefce8;
}

.bg-green-50:hover {
    background-color: #f0fdf4;
}

.bg-gray-50:hover {
    background-color: #f9fafb;
}

.bg-blue-50:hover {
    background-color: #eff6ff;
}

.bg-indigo-50:hover {
    background-color: #eef2ff;
}

/* Text overflow handling */
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.break-words {
    overflow-wrap: break-word;
    word-wrap: break-word;
    word-break: break-word;
}
</style>
@endpush
