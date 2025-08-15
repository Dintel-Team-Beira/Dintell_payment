@extends('layouts.admin')

@section('title', 'Suporte')

@section('content')
<div class="container px-6 py-8 mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Centro de Suporte</h1>
                <p class="mt-2 text-gray-600">Gerencie tickets e solicitações de suporte</p>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total de Tickets</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pendentes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Resolvidos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['resolved'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-7.5c-.768-.833-2.036-.833-2.804 0l-6.928 7.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Alta Prioridade</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['high_priority'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Ações Rápidas -->
    <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-3">
        <!-- Tickets Recentes -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Tickets Recentes</h3>
                    <a href="{{ route('admin.support.tickets') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        Ver todos
                    </a>
                </div>

                @if(isset($recentTickets) && $recentTickets->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentTickets->take(5) as $ticket)
                        <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-900">
                                        #{{ $ticket->ticket_number }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        @if($ticket->priority === 'high') bg-red-100 text-red-800
                                        @elseif($ticket->priority === 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-600 truncate">{{ $ticket->subject }}</p>
                                <p class="text-xs text-gray-500">{{ $ticket->user->name ?? 'Usuário não encontrado' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @if($ticket->status === 'open') bg-blue-100 text-blue-800
                                    @elseif($ticket->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($ticket->status === 'resolved') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $ticket->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Nenhum ticket encontrado</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Ações Rápidas</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.support.tickets') }}"
                       class="flex items-center p-3 transition-colors border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Gerenciar Tickets</p>
                            <p class="text-xs text-gray-500">Ver e responder tickets</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.support.reports') }}"
                       class="flex items-center p-3 transition-colors border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Relatórios</p>
                            <p class="text-xs text-gray-500">Análises e métricas</p>
                        </div>
                    </a>

                    {{-- Temporariamente comentado até implementação futura
                    <a href="#"
                       class="flex items-center p-3 border border-gray-200 rounded-lg opacity-50 cursor-not-allowed">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Categorias</p>
                            <p class="text-xs text-gray-500">Em desenvolvimento</p>
                        </div>
                    </a>
                    --}}

                    <a href="{{ route('admin.support.settings') }}"
                       class="flex items-center p-3 transition-colors border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="p-2 bg-gray-100 rounded-lg">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Configurações</p>
                            <p class="text-xs text-gray-500">Configurar sistema de suporte</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Estatísticas de Performance -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Performance</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Tempo médio de resposta</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $stats['avg_response_time'] ?? '2h 30min' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Taxa de resolução</span>
                        <span class="text-sm font-medium text-green-600">
                            {{ $stats['resolution_rate'] ?? '87%' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Satisfação do cliente</span>
                        <div class="flex items-center">
                            <div class="flex text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= ($stats['satisfaction_rating'] ?? 4) ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="ml-2 text-sm font-medium text-gray-900">
                                {{ number_format($stats['satisfaction_rating'] ?? 4.2, 1) }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Tickets este mês</span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ $stats['tickets_this_month'] ?? 45 }}
                            </span>
                        </div>
                        <div class="mt-2">
                            <div class="flex items-center">
                                <div class="flex-1 h-2 bg-gray-200 rounded-full">
                                    <div class="h-2 bg-blue-600 rounded-full" style="width: {{ ($stats['tickets_this_month'] ?? 45) / 100 * 100 }}%"></div>
                                </div>
                                <span class="ml-2 text-xs text-gray-500">
                                    vs mês anterior
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos e Relatórios -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Gráfico de Tickets por Categoria -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Tickets por Categoria</h3>
                    <select class="text-sm border-gray-300 rounded-md">
                        <option>Últimos 30 dias</option>
                        <option>Últimos 7 dias</option>
                        <option>Este mês</option>
                    </select>
                </div>

                <div class="space-y-3">
                    @if(isset($categoryStats))
                        @foreach($categoryStats as $category)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-{{ $category['color'] }}-500 mr-3"></div>
                                <span class="text-sm text-gray-700">{{ $category['name'] }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="mr-2 text-sm font-medium text-gray-900">{{ $category['count'] }}</span>
                                <div class="w-20 h-2 bg-gray-200 rounded-full">
                                    <div class="bg-{{ $category['color'] }}-500 h-2 rounded-full" style="width: {{ $category['percentage'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="py-8 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Nenhum dado disponível</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Atividade Recente -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Atividade Recente</h3>
                    <a href="{{ route('admin.support.reports') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        Ver todas
                    </a>
                </div>

                @if(isset($recentActivity) && $recentActivity->count() > 0)
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @foreach($recentActivity->take(5) as $activity)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-{{ $activity['type'] === 'response' ? 'green' : 'blue' }}-500 flex items-center justify-center ring-8 ring-white">
                                                @if($activity['type'] === 'response')
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                                                </svg>
                                                @else
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                                </svg>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    {!! $activity['description'] !!}
                                                </p>
                                            </div>
                                            <div class="text-sm text-right text-gray-500 whitespace-nowrap">
                                                {{ $activity['created_at']->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="py-8 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Nenhuma atividade recente</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom styles for support dashboard */
    .transition-colors {
        transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    /* Hover effects for action cards */
    .hover\:bg-gray-50:hover {
        background-color: #f9fafb;
    }

    /* Progress bar animations */
    .rounded-full {
        border-radius: 9999px;
    }

    /* Custom colors for categories */
    .bg-blue-500 {
        background-color: #3b82f6;
    }

    .bg-green-500 {
        background-color: #10b981;
    }

    .bg-purple-500 {
        background-color: #8b5cf6;
    }

    .bg-yellow-500 {
        background-color: #f59e0b;
    }

    .bg-red-500 {
        background-color: #ef4444;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .grid-cols-1.md\:grid-cols-2.lg\:grid-cols-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .grid-cols-1.md\:grid-cols-2.lg\:grid-cols-3 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-refresh statistics every 30 seconds
        setInterval(function() {
            // Implementation for auto-refresh if needed
            console.log('Auto-refreshing support statistics...');
        }, 30000);

        // Add any interactive features here
        console.log('Support dashboard loaded');
    });
</script>
@endpush
