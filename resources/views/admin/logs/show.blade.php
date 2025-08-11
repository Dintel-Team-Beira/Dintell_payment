@extends('layouts.admin')

@section('title', 'Visualizar Log')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $logFile }}</h1>
                    <p class="mt-1 text-sm text-gray-600">Visualização detalhada do arquivo de log</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.logs.download', $logFile) }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 border border-green-200 rounded-md bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download
                    </a>
                    <a href="{{ route('admin.logs.index') }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Voltar para Logs
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-8">
        <!-- Estatísticas do Log -->
        <div class="grid grid-cols-1 gap-5 mb-8 sm:grid-cols-2 lg:grid-cols-5">
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Total de Linhas</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_lines']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Erros</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['error_count']) }}</dd>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.864-.833-2.634 0L4.18 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Avisos</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['warning_count']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Info</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['info_count']) }}</dd>
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Tamanho</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['file_size'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="mb-8 bg-white rounded-lg shadow">
            <div class="px-6 py-4">
                <div class="flex flex-wrap items-center gap-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Filtrar por nível:</label>
                        <select id="levelFilter" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todos os níveis</option>
                            <option value="ERROR">Apenas Erros</option>
                            <option value="WARNING">Apenas Avisos</option>
                            <option value="INFO">Apenas Info</option>
                            <option value="DEBUG">Apenas Debug</option>
                        </select>
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Buscar mensagem:</label>
                        <input type="text" id="messageFilter" placeholder="Digite para buscar..."
                               class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="flex items-end">
                        <button onclick="clearFilters()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Limpar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conteúdo do Log -->
        <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Conteúdo do Log</h3>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">Mostrando últimas 1000 linhas</span>
                        <button onclick="scrollToTop()" class="text-sm text-blue-600 hover:text-blue-800">Ir para o topo</button>
                        <button onclick="scrollToBottom()" class="text-sm text-blue-600 hover:text-blue-800">Ir para o final</button>
                    </div>
                </div>
            </div>

            <div class="max-h-screen overflow-y-auto" id="logContent">
                @if(count($lines) > 0)
                    @foreach($lines as $line)
                        <div class="log-line border-b border-gray-100 px-6 py-3 hover:bg-gray-50 {{ $line['level_class'] ?? '' }}"
                             data-level="{{ $line['level'] ?? '' }}"
                             data-message="{{ strtolower($line['message'] ?? '') }}">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-16 font-mono text-xs text-gray-500">
                                    {{ $line['line_number'] }}
                                </div>

                                @if(!empty($line['formatted_time']))
                                    <div class="flex-shrink-0 w-20 font-mono text-xs text-gray-600">
                                        {{ $line['formatted_time'] }}
                                    </div>
                                @endif

                                @if(!empty($line['level']))
                                    <div class="flex-shrink-0 w-20">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $line['level_class'] }}">
                                            {{ $line['level'] }}
                                        </span>
                                    </div>
                                @endif

                                <div class="flex-1 font-mono text-sm text-gray-900 break-all">
                                    {{ $line['message'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="px-6 py-12 text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-lg font-medium text-gray-900">Log vazio</p>
                        <p class="text-sm text-gray-500">Este arquivo de log não contém nenhuma entrada</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Filtros
document.getElementById('levelFilter').addEventListener('change', applyFilters);
document.getElementById('messageFilter').addEventListener('input', applyFilters);

function applyFilters() {
    const levelFilter = document.getElementById('levelFilter').value;
    const messageFilter = document.getElementById('messageFilter').value.toLowerCase();
    const lines = document.querySelectorAll('.log-line');

    lines.forEach(line => {
        const level = line.getAttribute('data-level');
        const message = line.getAttribute('data-message');

        let showLine = true;

        // Filtro por nível
        if (levelFilter && level !== levelFilter) {
            showLine = false;
        }

        // Filtro por mensagem
        if (messageFilter && !message.includes(messageFilter)) {
            showLine = false;
        }

        line.style.display = showLine ? 'block' : 'none';
    });
}

function clearFilters() {
    document.getElementById('levelFilter').value = '';
    document.getElementById('messageFilter').value = '';

    const lines = document.querySelectorAll('.log-line');
    lines.forEach(line => {
        line.style.display = 'block';
    });
}

function scrollToTop() {
    document.getElementById('logContent').scrollTop = 0;
}

function scrollToBottom() {
    const content = document.getElementById('logContent');
    content.scrollTop = content.scrollHeight;
}

// Auto-scroll para o final ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
});
</script>
@endpush
@endsection
