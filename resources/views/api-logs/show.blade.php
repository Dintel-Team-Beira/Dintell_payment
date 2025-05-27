@extends('layouts.app')

@section('title', 'Detalhes do Log #' . $apiLog->id)

@section('header-actions')
<div class="flex items-center space-x-3">
    <a href="{{ route('api-logs.index') }}"
       class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-all">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Voltar aos Logs
    </a>

    <form method="POST" action="{{ route('api-logs.destroy', $apiLog) }}" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-red-700 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition-all"
                onclick="return confirm('Tem certeza que deseja excluir este log?')">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Excluir Log
        </button>
    </form>
</div>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Cabeçalho do Log -->
    <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
        <div class="px-6 py-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Log #{{ $apiLog->id }}</h1>
                    <p class="mt-1 text-gray-600">{{ $apiLog->created_at->format('d/m/Y H:i:s') }} ({{ $apiLog->created_at->diffForHumans() }})</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $apiLog->response_code_color }}">
                        {{ $apiLog->response_code }}
                    </span>
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-900">{{ $apiLog->status_text }}</div>
                        <div class="text-xs text-gray-500">Status Code</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalhes da Requisição -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Informações Gerais -->
        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Informações da Requisição</h3>
            </div>
            <div class="px-6 py-6 space-y-6">
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-500">Domínio</label>
                    <div class="px-4 py-2 text-lg text-gray-900 rounded-lg bg-gray-50">{{ $apiLog->domain }}</div>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-500">Endpoint</label>
                    <div class="px-4 py-2 font-mono text-lg text-gray-900 rounded-lg bg-gray-50">{{ $apiLog->clean_endpoint }}</div>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-500">Endereço IP</label>
                    <div class="px-4 py-2 font-mono text-lg text-gray-900 rounded-lg bg-gray-50">{{ $apiLog->ip_address }}</div>
                </div>

                @if($apiLog->subscription)
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-500">Subscription</label>
                    <div class="text-lg text-gray-900">
                        <a href="{{ route('subscriptions.show', $apiLog->subscription) }}"
                           class="inline-flex items-center px-4 py-2 text-blue-600 transition-colors rounded-lg hover:text-blue-800 bg-blue-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            Subscription #{{ $apiLog->subscription->id }}
                        </a>
                    </div>
                </div>
                @endif

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-500">Data/Hora</label>
                    <div class="space-y-2">
                        <div class="px-4 py-2 text-lg text-gray-900 rounded-lg bg-gray-50">{{ $apiLog->created_at->format('d/m/Y H:i:s') }}</div>
                        <div class="text-sm text-gray-600">{{ $apiLog->created_at->diffForHumans() }}</div>
                        <div class="text-xs text-gray-500">Timezone: {{ $apiLog->created_at->timezoneName }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Agent -->
        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">User Agent</h3>
            </div>
            <div class="px-6 py-6">
                <div class="px-4 py-3 rounded-lg bg-gray-50">
                    <div class="font-mono text-sm text-gray-900 break-all">{{ $apiLog->user_agent ?: 'Não informado' }}</div>
                </div>

                @if($apiLog->user_agent)
                    @php
                        $agent = new \Jenssegers\Agent\Agent();
                        $agent->setUserAgent($apiLog->user_agent);
                    @endphp
                    <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
                        <div>
                            <span class="text-gray-500">Browser:</span>
                            <span class="ml-2 text-gray-900">{{ $agent->browser() ?: 'Desconhecido' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Plataforma:</span>
                            <span class="ml-2 text-gray-900">{{ $agent->platform() ?: 'Desconhecida' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Device:</span>
                            <span class="ml-2 text-gray-900">{{ $agent->device() ?: 'Desktop' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Mobile:</span>
                            <span class="ml-2 text-gray-900">{{ $agent->isMobile() ? 'Sim' : 'Não' }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Request Data -->
    @if($apiLog->request_data && !empty($apiLog->request_data))
    <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Dados da Requisição</h3>
                <button onclick="copyToClipboard('request-data')"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Copiar
                </button>
            </div>
        </div>
        <div class="px-6 py-6">
            <div class="overflow-hidden bg-gray-900 rounded-lg">
                <pre id="request-data" class="p-4 overflow-x-auto text-sm text-green-400"><code>{{ json_encode($apiLog->request_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
            </div>
        </div>
    </div>
    @endif

    <!-- Response Data -->
    @if($apiLog->response_data && !empty($apiLog->response_data))
    <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Dados da Resposta</h3>
                <button onclick="copyToClipboard('response-data')"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Copiar
                </button>
            </div>
        </div>
        <div class="px-6 py-6">
            <div class="overflow-hidden bg-gray-900 rounded-lg">
                <pre id="response-data" class="p-4 overflow-x-auto text-sm text-blue-400"><code>{{ json_encode($apiLog->response_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
            </div>
        </div>
    </div>
    @endif

    <!-- Logs Relacionados -->
    @if($relatedLogs = \App\Models\ApiLog::where('ip_address', $apiLog->ip_address)->where('id', '!=', $apiLog->id)->latest()->limit(5)->get())
        @if($relatedLogs->count() > 0)
        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Outros Logs do mesmo IP</h3>
                <p class="mt-1 text-sm text-gray-600">Últimas 5 requisições do IP {{ $apiLog->ip_address }}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Endpoint</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Data/Hora</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($relatedLogs as $relatedLog)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($relatedLog->clean_endpoint, 50) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $relatedLog->response_code_color }}">
                                    {{ $relatedLog->response_code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $relatedLog->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right whitespace-nowrap">
                                <a href="{{ route('api-logs.show', $relatedLog) }}"
                                   class="font-medium text-blue-600 hover:text-blue-900">Ver</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    const text = element.textContent;

    navigator.clipboard.writeText(text).then(function() {
        showToast('Conteúdo copiado para a área de transferência!', 'success');
    }, function(err) {
        console.error('Erro ao copiar: ', err);
        showToast('Erro ao copiar conteúdo', 'error');
    });
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg transition-all duration-300 transform translate-x-full ${
        type === 'success'
            ? 'bg-green-500 text-white'
            : 'bg-red-500 text-white'
    }`;

    toast.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success'
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                }
            </svg>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(toast);

    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);

    // Animate out and remove
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

// Show success message if redirected from delete
@if(session('success'))
    showToast('{{ session('success') }}', 'success');
@endif
</script>
@endpush