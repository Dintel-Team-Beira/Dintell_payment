@extends('layouts.admin')

@section('title', 'Logs do Sistema')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
 <div class="mx-5 bg-white rounded-md shadow">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Logs do Sistema</h1>
                    <p class="mt-1 text-sm text-gray-600">Visualize e monitore os logs de atividade do sistema</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="clearAllLogs()"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Limpar Todos os Logs
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-8">
        <!-- Estatísticas -->
        <div class="grid grid-cols-1 gap-5 mb-8 sm:grid-cols-2 lg:grid-cols-4">
            <div class="overflow-hidden bg-white rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total de Arquivos</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_files'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Tamanho Total</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_size'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Último Log</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['latest_log'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Mais Antigo</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['oldest_log'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="mb-8 bg-white rounded-lg shadow">
            <div class="px-6 py-4">
                <form method="GET" action="{{ route('admin.logs.index') }}" class="flex flex-wrap items-center gap-4">
                    <div>
                        <select name="type" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todos os Tipos</option>
                            @foreach($stats['types'] as $type => $count)
                                <option value="{{ strtolower($type) }}" {{ request('type') == strtolower($type) ? 'selected' : '' }}>
                                    {{ $type }} ({{ $count }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <input type="date" name="date" value="{{ request('date') }}"
                               class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="flex space-x-2">
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Filtrar
                        </button>
                        <a href="{{ route('admin.logs.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de Logs -->
        <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Arquivo</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tamanho</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nível</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10">
                                        <div class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-lg">
                                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $log['name'] }}</div>
                                        <div class="text-sm text-gray-500">{{ basename($log['name'], '.log') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $log['type'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                {{ $log['size'] }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $log['date']->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log['level'] == 'ERROR')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        ERROR
                                    </span>
                                @elseif($log['level'] == 'WARNING')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        WARNING
                                    </span>
                                @elseif($log['level'] == 'INFO')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        INFO
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $log['level'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.logs.show', $log['name']) }}"
                                       class="text-blue-600 hover:text-blue-900">Ver</a>
                                    <a href="{{ route('admin.logs.download', $log['name']) }}"
                                       class="text-green-600 hover:text-green-900">Download</a>
                                    <button onclick="deleteLog('{{ $log['name'] }}')"
                                            class="text-red-600 hover:text-red-900">Excluir</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                                <div class="flex flex-col items-center py-12">
                                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900">Nenhum log encontrado</p>
                                    <p class="text-sm text-gray-500">Os logs aparecerão aqui quando houver atividade no sistema</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteLog(logName) {
    if (confirm(`Tem certeza que deseja excluir o log ${logName}?`)) {
        fetch(`/admin/logs/${logName}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao excluir log', 'error');
        });
    }
}

function clearAllLogs() {
    if (confirm('ATENÇÃO: Esta ação irá excluir TODOS os logs do sistema. Tem certeza que deseja continuar?')) {
        fetch('/admin/logs/clear', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao limpar logs', 'error');
        });
    }
}

// Sistema de notificações
function showNotification(message, type = 'info') {
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300`;

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
                <button class="text-gray-400 hover:text-gray-600" onclick="this.closest('.notification').remove()">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 5000);
}
</script>
@endpush
@endsection
