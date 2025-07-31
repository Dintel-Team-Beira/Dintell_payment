@extends('layouts.app')

@section('title', 'Logs de Email')

@section('header-actions')
<div class="flex items-center gap-x-4">
    <!-- Filters -->
    <form method="GET" class="flex items-center gap-x-3">
        {{-- <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar email, assunto ou cliente..."
                   class="block w-80 rounded-xl border-0 py-2.5 pl-10 pr-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm transition-all">
        </div> --}}

        <select name="status" class="rounded-xl border-0 py-2.5 pl-4 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-blue-500 sm:text-sm transition-all">
            <option value="">Todos os status</option>
            <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Enviado</option>
            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Falhou</option>
            <option value="queued" {{ request('status') === 'queued' ? 'selected' : '' }}>Na fila</option>
        </select>

        <select name="type" class="rounded-xl border-0 py-2.5 pl-4 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-blue-500 sm:text-sm transition-all">
            <option value="">Todos os tipos</option>
            @foreach($emailTypes as $type)
                <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                    {{ ucfirst($type) }}
                </option>
            @endforeach
        </select>

        <input type="date" name="date_from" value="{{ request('date_from') }}"
               class="rounded-xl border-0 py-2.5 pl-4 pr-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-blue-500 sm:text-sm transition-all">

        <input type="date" name="date_to" value="{{ request('date_to') }}"
               class="rounded-xl border-0 py-2.5 pl-4 pr-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-blue-500 sm:text-sm transition-all">

        <button type="submit" class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-sm hover:from-blue-700 hover:to-blue-800 transition-all">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            Filtrar
        </button>

        @if(request()->hasAny(['search', 'status', 'type', 'date_from', 'date_to']))
        <a href="{{ route('settings.email-logs.index') }}" class="inline-flex items-center px-3 py-2 text-sm text-gray-600 transition-all rounded-lg hover:text-gray-900 hover:bg-gray-100">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Limpar
        </a>
        @endif
    </form>

    <!-- Action Buttons -->
    {{-- <div class="flex items-center space-x-3">
        <button onclick="openTestModal()" class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl shadow-sm hover:bg-gray-50 transition-all">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Testar Email
        </button>

        <a href="{{ route('email-logs.export', request()->query()) }}"
           class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl hover:bg-emerald-100 transition-all">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Exportar
        </a>
    </div> --}}
</div>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Enhanced Stats Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="relative p-6 overflow-hidden shadow-lg bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl">
            <div class="absolute top-0 right-0 w-24 h-24 -mt-4 -mr-4 rounded-full bg-white/10"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-100">Total de Emails</p>
                        <p class="mt-1 text-3xl font-bold text-white">{{ number_format($stats['total']) }}</p>
                        <p class="mt-1 text-xs text-blue-200">Hoje: {{ $stats['today'] }}</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative p-6 overflow-hidden shadow-lg bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl">
            <div class="absolute top-0 right-0 w-24 h-24 -mt-4 -mr-4 rounded-full bg-white/10"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-emerald-100">Enviados</p>
                        <p class="mt-1 text-3xl font-bold text-white">{{ number_format($stats['sent']) }}</p>
                        <p class="mt-1 text-xs text-emerald-200">Taxa: {{ $stats['success_rate'] }}%</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative p-6 overflow-hidden shadow-lg bg-gradient-to-br from-red-500 to-red-600 rounded-2xl">
            <div class="absolute top-0 right-0 w-24 h-24 -mt-4 -mr-4 rounded-full bg-white/10"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-100">Falharam</p>
                        <p class="mt-1 text-3xl font-bold text-white">{{ number_format($stats['failed']) }}</p>
                        <p class="mt-1 text-xs text-red-200">Precisam atenção</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative p-6 overflow-hidden shadow-lg bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl">
            <div class="absolute top-0 right-0 w-24 h-24 -mt-4 -mr-4 rounded-full bg-white/10"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-amber-100">Na Fila</p>
                        <p class="mt-1 text-3xl font-bold text-white">{{ number_format($stats['queued']) }}</p>
                        <p class="mt-1 text-xs text-amber-200">Aguardando envio</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Types Chart -->
    @if($stats['by_type']->count() > 0)
    <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-2xl">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Emails por Tipo</h3>
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            @foreach($stats['by_type'] as $typeData)
            <div class="p-4 text-center bg-gray-50 rounded-xl">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($typeData->count) }}</div>
                <div class="text-sm text-gray-600 capitalize">{{ $typeData->type }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Failures -->
    @if($stats['recent_failures']->count() > 0)
    <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Falhas Recentes</h3>
            <button onclick="openBulkResendModal()" class="text-sm font-medium text-red-600 hover:text-red-700">
                Reenviar Selecionados
            </button>
        </div>
        <div class="space-y-3">
            @foreach($stats['recent_failures'] as $failure)
            <div class="flex items-center justify-between p-3 border border-red-200 rounded-lg bg-red-50">
                <div class="flex items-center space-x-3">
                    <input type="checkbox" class="failure-checkbox" value="{{ $failure->id }}">
                    <div>
                        <div class="font-medium text-gray-900">{{ $failure->to_email }}</div>
                        <div class="text-sm text-gray-600">{{ $failure->subject }}</div>
                        <div class="text-xs text-red-600">{{ Str::limit($failure->error_message, 50) }}</div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-gray-500">{{ $failure->created_at->diffForHumans() }}</span>
                    <button onclick="resendEmail({{ $failure->id }})"
                            class="text-sm text-red-600 hover:text-red-800">
                        Reenviar
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Email Logs Table -->
    <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Logs de Email</h3>
                    <p class="mt-1 text-gray-600">Histórico completo de emails enviados</p>
                </div>
                @if($stats['failed'] > 0)
                <button onclick="openCleanupModal()" class="text-sm text-gray-600 hover:text-gray-800">
                    Limpar Logs Antigos
                </button>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            <input type="checkbox" id="selectAll" class="rounded">
                        </th>
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Email & Assunto</th>
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Cliente/Domínio</th>
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($emailLogs as $log)
                    <tr class="transition-colors hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="rounded email-checkbox" value="{{ $log->id }}">
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <div class="font-medium text-gray-900">{{ $log->to_email }}</div>
                                <div class="text-sm text-gray-600">{{ Str::limit($log->subject, 50) }}</div>
                                @if($log->error_message)
                                    <div class="mt-1 text-xs text-red-600">{{ Str::limit($log->error_message, 60) }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $log->type === 'suspended' ? 'bg-amber-100 text-amber-800' :
                                   ($log->type === 'activated' ? 'bg-emerald-100 text-emerald-800' :
                                    ($log->type === 'expiring' ? 'bg-blue-100 text-blue-800' :
                                     ($log->type === 'payment' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'))) }}">
                                {{ ucfirst($log->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($log->status === 'sent')
                                    <div class="w-2 h-2 mr-2 rounded-full bg-emerald-400"></div>
                                    <span class="text-sm font-medium text-emerald-800">Enviado</span>
                                @elseif($log->status === 'failed')
                                    <div class="w-2 h-2 mr-2 bg-red-400 rounded-full"></div>
                                    <span class="text-sm font-medium text-red-800">Falhou</span>
                                @else
                                    <div class="w-2 h-2 mr-2 rounded-full bg-amber-400"></div>
                                    <span class="text-sm font-medium text-amber-800">Na fila</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                @if($log->client)
                                    <div class="text-sm font-medium text-gray-900">{{ $log->client->name }}</div>
                                @endif
                                @if($log->subscription)
                                    <div class="text-sm text-gray-600">{{ $log->subscription->domain }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            <div>{{ $log->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs">{{ $log->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end space-x-3">
                                <a href="{{ route('email-logs.show', $log) }}"
                                   class="text-sm font-medium text-blue-600 hover:text-blue-900">Ver</a>

                                @if($log->status === 'failed')
                                    <button onclick="resendEmail({{ $log->id }})"
                                            class="text-sm font-medium text-red-600 hover:text-red-900">Reenviar</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <p class="mb-2 text-xl font-medium text-gray-900">Nenhum log de email encontrado</p>
                                <p class="text-gray-500">Os logs de email aparecerão aqui conforme forem enviados.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($emailLogs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $emailLogs->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Test Email Modal -->
<div id="testModal" class="fixed inset-0 z-50 hidden w-full h-full overflow-y-auto bg-gray-900 bg-opacity-50 backdrop-blur-sm">
    <div class="relative w-full max-w-md p-5 mx-auto bg-white shadow-2xl rounded-2xl top-20">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Testar Email</h3>
                <p class="mt-1 text-sm text-gray-600">Envie um email de teste para verificar a configuração</p>
            </div>
            <button onclick="closeTestModal()" class="text-gray-400 transition-colors hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="testForm" class="space-y-5">
            @csrf
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Email de Destino</label>
                <input type="email" name="email" required
                       class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       placeholder="exemplo@email.com">
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Tipo de Teste</label>
                <select name="type" required
                        class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="test">Email de Teste Básico</option>
                    <option value="suspended">Simulação de Suspensão</option>
                    <option value="activated">Simulação de Ativação</option>
                    <option value="expiring">Simulação de Expiração</option>
                    <option value="payment">Simulação de Pagamento</option>
                </select>
            </div>

            <div class="flex items-center justify-end pt-4 space-x-3 border-t border-gray-200">
                <button type="button" onclick="closeTestModal()"
                        class="px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 rounded-xl hover:bg-gray-200">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-6 py-3 text-sm font-medium text-white transition-all bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl hover:from-blue-700 hover:to-blue-800">
                    <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Enviar Teste
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Resend Modal -->
<div id="bulkResendModal" class="fixed inset-0 z-50 hidden w-full h-full overflow-y-auto bg-gray-900 bg-opacity-50 backdrop-blur-sm">
    <div class="relative w-full max-w-md p-5 mx-auto bg-white shadow-2xl rounded-2xl top-20">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Reenviar Emails</h3>
                <p class="mt-1 text-sm text-gray-600">Reenviar emails selecionados com falha</p>
            </div>
            <button onclick="closeBulkResendModal()" class="text-gray-400 transition-colors hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="bulkResendForm" method="POST" action="{{ route('email-logs.bulk-resend') }}" class="space-y-5">
            @csrf
            <div id="selectedEmailsList" class="space-y-2">
                <!-- Selected emails will be populated here -->
            </div>

            <div class="flex items-center justify-end pt-4 space-x-3 border-t border-gray-200">
                <button type="button" onclick="closeBulkResendModal()"
                        class="px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 rounded-xl hover:bg-gray-200">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-6 py-3 text-sm font-medium text-white transition-all bg-gradient-to-r from-red-600 to-red-700 rounded-xl hover:from-red-700 hover:to-red-800">
                    Reenviar Selecionados
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Cleanup Modal -->
<div id="cleanupModal" class="fixed inset-0 z-50 hidden w-full h-full overflow-y-auto bg-gray-900 bg-opacity-50 backdrop-blur-sm">
    <div class="relative w-full max-w-md p-5 mx-auto bg-white shadow-2xl rounded-2xl top-20">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Limpar Logs Antigos</h3>
                <p class="mt-1 text-sm text-gray-600">Remover logs de email mais antigos que X dias</p>
            </div>
            <button onclick="closeCleanupModal()" class="text-gray-400 transition-colors hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('email-logs.cleanup') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Dias para manter</label>
                <input type="number" name="days" value="30" min="1" max="365" required
                       class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">Logs mais antigos que este número de dias serão removidos</p>
            </div>

            <div class="flex items-center justify-end pt-4 space-x-3 border-t border-gray-200">
                <button type="button" onclick="closeCleanupModal()"
                        class="px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 rounded-xl hover:bg-gray-200">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-6 py-3 text-sm font-medium text-white transition-all bg-gradient-to-r from-red-600 to-red-700 rounded-xl hover:from-red-700 hover:to-red-800"
                        onclick="return confirm('Tem certeza que deseja remover logs antigos? Esta ação não pode ser desfeita.')">
                    <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Limpar Logs
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Modal Functions
function openTestModal() {
    document.getElementById('testModal').classList.remove('hidden');
}

function closeTestModal() {
    document.getElementById('testModal').classList.add('hidden');
}

function openBulkResendModal() {
    const checkedBoxes = document.querySelectorAll('.failure-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Selecione pelo menos um email com falha para reenviar.');
        return;
    }

    const selectedList = document.getElementById('selectedEmailsList');
    selectedList.innerHTML = '';

    checkedBoxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'email_ids[]';
        input.value = checkbox.value;
        selectedList.appendChild(input);
    });

    document.getElementById('bulkResendModal').classList.remove('hidden');
}

function closeBulkResendModal() {
    document.getElementById('bulkResendModal').classList.add('hidden');
}

function openCleanupModal() {
    document.getElementById('cleanupModal').classList.remove('hidden');
}

function closeCleanupModal() {
    document.getElementById('cleanupModal').classList.add('hidden');
}

// Individual email resend
function resendEmail(emailId) {
    if (confirm('Reenviar este email?')) {
        fetch(`/email-logs/${emailId}/resend`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao reenviar email: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao reenviar email');
        });
    }
}

// Test email form submission
document.getElementById('testForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="inline w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Enviando...';

    fetch('{{ route("email.test") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Email de teste enviado com sucesso!');
            closeTestModal();
            location.reload();
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao enviar email de teste');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Select all checkboxes
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.email-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Auto-refresh stats every 30 seconds
setInterval(function() {
    fetch('{{ route("api.email.stats") }}')
        .then(response => response.json())
        .then(data => {
            // Update stats cards if needed
            console.log('Stats updated:', data);
        })
        .catch(error => console.error('Error updating stats:', error));
}, 30000);

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const modals = ['testModal', 'bulkResendModal', 'cleanupModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
});

// Bulk actions
function getSelectedEmails() {
    const checkedBoxes = document.querySelectorAll('.email-checkbox:checked');
    return Array.from(checkedBoxes).map(cb => cb.value);
}

// Enhanced search with debounce
let searchTimeout;
const searchInput = document.querySelector('input[name="search"]');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            // Auto-submit form after 500ms of no typing
            if (this.value.length > 2 || this.value.length === 0) {
                this.form.submit();
            }
        }, 500);
    });
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        searchInput?.focus();
    }

    // Escape to close modals
    if (e.key === 'Escape') {
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
            modal.classList.add('hidden');
        });
    }
});

// Auto-hide success/error messages
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert, .flash-message');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);
</script>

<style>
/* Custom animations and transitions */
.animate-pulse-slow {
    animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Hover effects for cards */
.hover\\:scale-102:hover {
    transform: scale(1.02);
}

/* Loading states */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Success/Error message styling */
.flash-message {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    max-width: 400px;
    padding: 12px 16px;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.flash-success {
    background-color: #d1fae5;
    border: 1px solid #a7f3d0;
    color: #065f46;
}

.flash-error {
    background-color: #fee2e2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

/* Table row hover effects */
tbody tr:hover {
    background-color: #f9fafb;
    transition: background-color 0.15s ease-in-out;
}

/* Status indicator animations */
.status-indicator {
    animation: pulse 2s infinite;
}

/* Filter badge styling */
.filter-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    background-color: #e5e7eb;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    color: #374151;
}

/* Responsive table improvements */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }

    .table-responsive th,
    .table-responsive td {
        padding: 0.5rem;
    }
}
</style>
@endsection
