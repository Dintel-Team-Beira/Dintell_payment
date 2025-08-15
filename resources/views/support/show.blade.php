{{-- resources/views/support/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->ticket_number)

@section('content')
<div class="container px-4 py-8 mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('support.my-tickets') }}" class="text-blue-600 hover:text-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">
                    Ticket #{{ $ticket->ticket_number }}
                </h1>
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $ticket->status_color }}">
                    {{ ucfirst($ticket->status) }}
                </span>
            </div>
            <p class="mt-2 text-gray-600">{{ $ticket->subject }}</p>
        </div>

        <div class="flex space-x-3">
            @if($ticket->status !== 'closed')
                <button onclick="closeTicket()" class="px-4 py-2 text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700">
                    Fechar Ticket
                </button>
            @else
                <button onclick="reopenTicket()" class="px-4 py-2 text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
                    Reabrir Ticket
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Ticket Details -->
            <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            @if($ticket->user->avatar)
                                <img class="w-12 h-12 rounded-full" src="{{ asset('storage/' . $ticket->user->avatar) }}" alt="{{ $ticket->user->name }}">
                            @else
                                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full">
                                    <span class="text-lg font-medium text-blue-600">{{ substr($ticket->user->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ $ticket->user->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="px-3 py-1 text-sm font-medium rounded-full {{ $ticket->priority_color }}">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <div class="prose max-w-none">
                        {{ $ticket->description }}
                    </div>

                    @if($ticket->attachments && count($ticket->attachments) > 0)
                        <div class="mt-6">
                            <h4 class="mb-3 text-sm font-medium text-gray-900">Anexos:</h4>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                @foreach($ticket->attachments as $index => $attachment)
                                    <a href="{{ route('support.tickets.attachments.download', [$ticket->id, $index]) }}"
                                       class="flex items-center p-3 transition-colors rounded-lg bg-gray-50 hover:bg-gray-100">
                                        <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment['name'] }}</p>
                                            <p class="text-sm text-gray-500">{{ number_format($attachment['size'] / 1024, 1) }} KB</p>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Replies -->
            <div class="space-y-4">
                @foreach($ticket->replies as $reply)
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="p-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    @if($reply->user->avatar)
                                        <img class="w-10 h-10 rounded-full" src="{{ asset('storage/' . $reply->user->avatar) }}" alt="{{ $reply->user->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full {{ $reply->is_internal ? 'bg-yellow-100' : ($reply->user->is_super_admin ? 'bg-purple-100' : 'bg-blue-100') }} flex items-center justify-center">
                                            <span class="{{ $reply->is_internal ? 'text-yellow-600' : ($reply->user->is_super_admin ? 'text-purple-600' : 'text-blue-600') }} font-medium">
                                                {{ substr($reply->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $reply->user->name }}</h4>
                                        @if($reply->user->is_super_admin)
                                            <span class="px-2 py-1 text-xs font-medium text-purple-800 bg-purple-100 rounded-full">Suporte</span>
                                        @endif
                                        @if($reply->is_internal)
                                            <span class="px-2 py-1 text-xs font-medium text-yellow-800 bg-yellow-100 rounded-full">Interno</span>
                                        @endif
                                        @if($reply->is_system)
                                            <span class="px-2 py-1 text-xs font-medium text-gray-800 bg-gray-100 rounded-full">Sistema</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500">{{ $reply->created_at->diffForHumans() }}</p>

                                    <div class="mt-3 prose max-w-none">
                                        {{ $reply->message }}
                                    </div>

                                    @if($reply->attachments && count($reply->attachments) > 0)
                                        <div class="mt-4">
                                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                                @foreach($reply->attachments as $index => $attachment)
                                                    <a href="#" class="flex items-center p-2 text-sm transition-colors rounded-lg bg-gray-50 hover:bg-gray-100">
                                                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                        </svg>
                                                        <span class="truncate">{{ $attachment['name'] }}</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Reply Form -->
            @if($ticket->status !== 'closed')
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">Adicionar Resposta</h3>

                        <form id="replyForm" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="message" class="block mb-2 text-sm font-medium text-gray-700">Mensagem</label>
                                    <textarea id="message" name="message" rows="4"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                              placeholder="Digite sua resposta..." required></textarea>
                                </div>

                                <div>
                                    <label for="attachments" class="block mb-2 text-sm font-medium text-gray-700">Anexos (opcional)</label>
                                    <input type="file" id="attachments" name="attachments[]" multiple
                                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <p class="mt-1 text-sm text-gray-500">Máximo 3 arquivos, 5MB cada. Formatos: JPG, PNG, PDF, DOC, DOCX, TXT</p>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="px-6 py-2 text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                                        Enviar Resposta
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Ticket Info -->
            <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Informações do Ticket</h3>

                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $ticket->status_color }}">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Prioridade</dt>
                            <dd class="mt-1">
                                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $ticket->priority_color }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Categoria</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($ticket->category) }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Criado em</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ticket->created_at->format('d/m/Y H:i') }}</dd>
                        </div>

                        @if($ticket->first_response_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Primeira resposta</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $ticket->first_response_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        @endif

                        @if($ticket->resolved_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Resolvido em</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $ticket->resolved_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        @endif

                        @if($ticket->assignedTo)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Atribuído para</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $ticket->assignedTo->name }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Actions -->
            <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Ações</h3>

                    <div class="space-y-3">
                        @if($ticket->status !== 'closed')
                            <button onclick="closeTicket()" class="w-full px-4 py-2 text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700">
                                Fechar Ticket
                            </button>
                        @else
                            <button onclick="reopenTicket()" class="w-full px-4 py-2 text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
                                Reabrir Ticket
                            </button>
                        @endif

                        <a href="{{ route('support.my-tickets') }}" class="block w-full px-4 py-2 text-center text-gray-700 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                            Ver Todos os Tickets
                        </a>
                    </div>
                </div>
            </div>

            <!-- Satisfaction Rating -->
            @if($ticket->status === 'closed' && !$ticket->satisfaction_rating)
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">Avalie nosso atendimento</h3>

                        <div class="space-y-4">
                            <div>
                                <p class="mb-3 text-sm text-gray-600">Como você avalia a resolução do seu problema?</p>
                                <div class="flex space-x-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button onclick="setRating({{ $i }})"
                                                class="w-8 h-8 text-gray-300 transition-colors rating-star hover:text-yellow-400">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </button>
                                    @endfor
                                </div>
                            </div>

                            <div id="ratingForm" class="hidden">
                                <textarea id="satisfactionComment" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                          placeholder="Comentários adicionais (opcional)"></textarea>
                                <div class="flex mt-3 space-x-3">
                                    <button onclick="submitRating()" class="px-4 py-2 text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                                        Enviar Avaliação
                                    </button>
                                    <button onclick="cancelRating()" class="px-4 py-2 text-gray-700 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($ticket->satisfaction_rating)
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">Sua Avaliação</h3>

                        <div class="space-y-3">
                            <div class="flex space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $ticket->satisfaction_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                                <span class="ml-2 text-sm text-gray-600">({{ $ticket->satisfaction_rating }}/5)</span>
                            </div>

                            @if($ticket->satisfaction_comment)
                                <div class="p-3 mt-3 rounded-lg bg-gray-50">
                                    <p class="text-sm text-gray-700">{{ $ticket->satisfaction_comment }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Help Links -->
            <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Precisa de mais ajuda?</h3>

                    <div class="space-y-3">
                        <a href="#" class="flex items-center p-3 transition-colors rounded-lg bg-gray-50 hover:bg-gray-100">
                            <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Base de Conhecimento</span>
                        </a>

                        <a href="{{ route('support.my-tickets') }}" class="flex items-center p-3 transition-colors rounded-lg bg-gray-50 hover:bg-gray-100">
                            <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Meus Tickets</span>
                        </a>

                        <button onclick="openSupportPopup()" class="flex items-center w-full p-3 transition-colors rounded-lg bg-blue-50 hover:bg-blue-100">
                            <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="text-sm font-medium text-blue-900">Criar Novo Ticket</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Close Ticket Modal -->
<div id="closeTicketModal" class="fixed inset-0 z-50 hidden w-full h-full overflow-y-auto bg-gray-600 bg-opacity-50">
    <div class="relative p-5 mx-auto bg-white border rounded-md shadow-lg top-20 w-96">
        <div class="mt-3">
            <h3 class="mb-4 text-lg font-medium text-gray-900">Fechar Ticket</h3>
            <p class="mb-4 text-sm text-gray-600">Tem certeza que deseja fechar este ticket?</p>

            <textarea id="closeComment" rows="3"
                      class="w-full px-3 py-2 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="Comentário sobre o fechamento (opcional)"></textarea>

            <div class="flex justify-end space-x-3">
                <button onclick="hideCloseModal()" class="px-4 py-2 text-gray-700 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                    Cancelar
                </button>
                <button onclick="confirmCloseTicket()" class="px-4 py-2 text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700">
                    Fechar Ticket
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Notifications -->
<div id="notification" class="fixed z-50 hidden top-4 right-4">
    <div class="max-w-sm p-4 bg-white border border-gray-200 rounded-lg shadow-lg">
        <div class="flex items-center">
            <div id="notificationIcon" class="flex-shrink-0 mr-3"></div>
            <div>
                <p id="notificationMessage" class="text-sm font-medium text-gray-900"></p>
            </div>
            <button onclick="hideNotification()" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 inline-flex h-8 w-8">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Reply form submission
    const replyForm = document.getElementById('replyForm');
    if (replyForm) {
        replyForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            await submitReply();
        });
    }
});

// Variables
let selectedRating = 0;

// Reply Functions
async function submitReply() {
    const form = document.getElementById('replyForm');
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;

    // Show loading state
    submitButton.textContent = 'Enviando...';
    submitButton.disabled = true;

    try {
        const formData = new FormData(form);

        const response = await fetch(`/support/tickets/{{ $ticket->id }}/reply`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Resposta enviada com sucesso!', 'success');
            form.reset();
            // Reload page to show new reply
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(result.message || 'Erro ao enviar resposta');
        }

    } catch (error) {
        console.error('Error submitting reply:', error);
        showNotification('Erro ao enviar resposta. Tente novamente.', 'error');
    } finally {
        submitButton.textContent = originalText;
        submitButton.disabled = false;
    }
}

// Ticket Management Functions
function closeTicket() {
    document.getElementById('closeTicketModal').classList.remove('hidden');
}

function hideCloseModal() {
    document.getElementById('closeTicketModal').classList.add('hidden');
}

async function confirmCloseTicket() {
    const comment = document.getElementById('closeComment').value;

    try {
        const response = await fetch(`/support/tickets/{{ $ticket->id }}/close`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                comment: comment
            })
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Ticket fechado com sucesso!', 'success');
            hideCloseModal();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(result.message || 'Erro ao fechar ticket');
        }

    } catch (error) {
        console.error('Error closing ticket:', error);
        showNotification('Erro ao fechar ticket. Tente novamente.', 'error');
    }
}

async function reopenTicket() {
    if (!confirm('Tem certeza que deseja reabrir este ticket?')) {
        return;
    }

    try {
        const response = await fetch(`/support/tickets/{{ $ticket->id }}/reopen`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Ticket reaberto com sucesso!', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(result.message || 'Erro ao reabrir ticket');
        }

    } catch (error) {
        console.error('Error reopening ticket:', error);
        showNotification('Erro ao reabrir ticket. Tente novamente.', 'error');
    }
}

// Rating Functions
function setRating(rating) {
    selectedRating = rating;

    // Update stars
    const stars = document.querySelectorAll('.rating-star');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });

    // Show form
    document.getElementById('ratingForm').classList.remove('hidden');
}

function cancelRating() {
    selectedRating = 0;

    // Reset stars
    const stars = document.querySelectorAll('.rating-star');
    stars.forEach(star => {
        star.classList.remove('text-yellow-400');
        star.classList.add('text-gray-300');
    });

    // Hide form
    document.getElementById('ratingForm').classList.add('hidden');
    document.getElementById('satisfactionComment').value = '';
}

async function submitRating() {
    if (selectedRating === 0) {
        showNotification('Por favor, selecione uma avaliação.', 'error');
        return;
    }

    const comment = document.getElementById('satisfactionComment').value;

    try {
        const response = await fetch(`/support/tickets/{{ $ticket->id }}/rate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                rating: selectedRating,
                comment: comment
            })
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Avaliação enviada com sucesso!', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(result.message || 'Erro ao enviar avaliação');
        }

    } catch (error) {
        console.error('Error submitting rating:', error);
        showNotification('Erro ao enviar avaliação. Tente novamente.', 'error');
    }
}

// Notification Functions
function showNotification(message, type = 'info') {
    const notification = document.getElementById('notification');
    const messageEl = document.getElementById('notificationMessage');
    const iconEl = document.getElementById('notificationIcon');

    messageEl.textContent = message;

    // Set icon based on type
    if (type === 'success') {
        iconEl.innerHTML = `
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        `;
    } else if (type === 'error') {
        iconEl.innerHTML = `
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
        `;
    } else {
        iconEl.innerHTML = `
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        `;
    }

    notification.classList.remove('hidden');

    // Auto hide after 5 seconds
    setTimeout(() => {
        hideNotification();
    }, 5000);
}

function hideNotification() {
    document.getElementById('notification').classList.add('hidden');
}

// Integration with support popup
function openSupportPopup() {
    if (typeof toggleSupportPopup === 'function') {
        toggleSupportPopup();
    } else {
        // Fallback - redirect to create ticket page
        window.location.href = '{{ route("support.my-tickets") }}';
    }
}
</script>
@endpush

@push('styles')
<style>
/* Custom styles for ticket view */
.prose {
    color: #374151;
    line-height: 1.6;
}

.prose p {
    margin-bottom: 1rem;
}

.prose ul, .prose ol {
    margin-bottom: 1rem;
    padding-left: 1.5rem;
}

.prose li {
    margin-bottom: 0.25rem;
}

.prose a {
    color: #3b82f6;
    text-decoration: underline;
}

.prose a:hover {
    color: #1d4ed8;
}

.prose strong {
    font-weight: 600;
}

.prose em {
    font-style: italic;
}

.prose code {
    background-color: #f3f4f6;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
}

.prose pre {
    background-color: #1f2937;
    color: #f9fafb;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    margin: 1rem 0;
}

.prose pre code {
    background-color: transparent;
    padding: 0;
    color: inherit;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}

@media (max-width: 768px) {
    .grid-cols-1.lg\\:grid-cols-3 {
        grid-template-columns: 1fr;
    }

    .flex.items-center.justify-between {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .flex.space-x-3 {
        width: 100%;
        justify-content: stretch;
    }

    .flex.space-x-3 > * {
        flex: 1;
        text-align: center;
    }
}

/* Animation for notifications */
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

#notification {
    animation: slideIn 0.3s ease-out;
}

/* Loading states */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Star rating hover effects */
.rating-star:hover {
    transform: scale(1.1);
}

/* File upload styling */
input[type="file"] {
    padding: 0.5rem !important;
}

/* Modal backdrop */
.modal-backdrop {
    backdrop-filter: blur(4px);
}

/* Smooth transitions */
* {
    transition: all 0.2s ease;
}

/* Focus states */
button:focus,
input:focus,
textarea:focus,
select:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Custom scrollbar for textarea */
textarea::-webkit-scrollbar {
    width: 6px;
}

textarea::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

textarea::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

textarea::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endpush
@endsection
