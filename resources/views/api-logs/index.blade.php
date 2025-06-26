@extends('layouts.app')

@section('title', 'Logs da API')

@section('header-actions')
<div class="flex items-center gap-x-4">
    <!-- Filtros -->
    <form method="GET" class="flex items-center gap-x-3">
        {{-- <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar domínio, IP ou endpoint..."
                   class="block w-80 rounded-xl border-0 py-2.5 pl-10 pr-4 text-gray-900 shadow-sm ring-1 ring-inset
                    ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm transition-all">
        </div> --}}

        <select name="status" class="rounded-xl border-0 py-2.5 pl-4 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-blue-500 sm:text-sm transition-all">
            <option value="">Todos os status</option>
            <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Sucesso (2xx)</option>
            <option value="client_error" {{ request('status') === 'client_error' ? 'selected' : '' }}>Erro Cliente (4xx)</option>
            <option value="server_error" {{ request('status') === 'server_error' ? 'selected' : '' }}>Erro Servidor (5xx)</option>
            <option value="error" {{ request('status') === 'error' ? 'selected' : '' }}>Todos os Erros</option>
        </select>

        <select name="domain" class="rounded-xl border-0 py-2.5 pl-4 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-blue-500 sm:text-sm transition-all">
            <option value="">Todos os domínios</option>
            @foreach($domains as $domainOption)
                <option value="{{ $domainOption }}" {{ request('domain') === $domainOption ? 'selected' : '' }}>
                    {{ $domainOption }}
                </option>
            @endforeach
        </select>

        <select name="endpoint" class="rounded-xl border-0 py-2.5 pl-4 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-blue-500 sm:text-sm transition-all">
            <option value="">Todos os endpoints</option>
            @foreach($endpoints as $endpointOption)
                <option value="{{ $endpointOption }}" {{ request('endpoint') === $endpointOption ? 'selected' : '' }}>
                    {{ Str::limit($endpointOption, 30) }}
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

        @if(request()->hasAny(['search', 'status', 'domain', 'endpoint', 'date_from', 'date_to']))
        <a href="{{ route('api-logs.index') }}" class="inline-flex items-center px-3 py-2 text-sm text-gray-600 transition-all rounded-lg hover:text-gray-900 hover:bg-gray-100">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Limpar
        </a>
        @endif
    </form>


</div>
@endsection

@section('content')

<div class="space-y-8">
    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="relative p-6 overflow-hidden shadow-lg bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl">
            <div class="absolute top-0 right-0 w-24 h-24 -mt-4 -mr-4 rounded-full bg-white/10"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-100">Total de Requests</p>
                        <p class="mt-1 text-3xl font-bold text-white">{{ number_format($logs->total()) }}</p>
                        <p class="mt-1 text-xs text-blue-200">Últimas 24h</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
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
                        <p class="text-sm font-medium text-emerald-100">Sucessos (2xx)</p>
                        <p class="mt-1 text-3xl font-bold text-white">{{ number_format($logs->where('response_code', '>=', 200)->where('response_code', '<', 300)->count()) }}</p>
                        <p class="mt-1 text-xs text-emerald-200">Taxa de sucesso</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                        <p class="text-sm font-medium text-amber-100">Erros Cliente (4xx)</p>
                        <p class="mt-1 text-3xl font-bold text-white">{{ number_format($logs->where('response_code', '>=', 400)->where('response_code', '<', 500)->count()) }}</p>
                        <p class="mt-1 text-xs text-amber-200">Requer atenção</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
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
                        <p class="text-sm font-medium text-red-100">Erros Servidor (5xx)</p>
                        <p class="mt-1 text-3xl font-bold text-white">{{ number_format($logs->where('response_code', '>=', 500)->count()) }}</p>
                        <p class="mt-1 text-xs text-red-200">Críticos</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Logs -->
    <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
            <!-- Botões de Ação -->
    <div class="flex items-center p-5 space-x-3">


        <button onclick="openCleanupModal()"
                class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 transition-all">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Limpar Antigos
        </button>

        <button onclick="openBulkDeleteModal()"
                class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-red-700 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition-all">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Excluir Selecionados
        </button>
    </div>
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Logs da API</h3>
                    <p class="mt-1 text-gray-600">Histórico de todas as requisições à API</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="toggleAutoRefresh()" id="autoRefreshBtn"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Auto-refresh
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            <input type="checkbox" id="selectAll" class="rounded">
                        </th>
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Domínio & Endpoint</th>
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">IP & User Agent</th>
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Data/Hora</th>
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr class="transition-colors hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="rounded log-checkbox" value="{{ $log->id }}">
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $log->domain }}</div>
                                <div class="text-sm text-gray-600">{{ $log->clean_endpoint }}</div>
                                @if($log->subscription)
                                    <div class="text-xs text-blue-600">
                                        <a href="{{ route('subscriptions.show', $log->subscription) }}" class="hover:underline">
                                            Sub #{{ $log->subscription->id }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $log->response_code_color }}">
                                    {{ $log->response_code }}
                                </span>
                                <span class="ml-2 text-xs text-gray-500">{{ $log->status_text }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm text-gray-900">{{ $log->ip_address }}</div>
                                <div class="text-xs text-gray-500">{{ Str::limit($log->user_agent, 40) }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            <div>{{ $log->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs">{{ $log->created_at->format('H:i:s') }}</div>
                            <div class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end space-x-3">
                                <a href="{{ route('api-logs.show', $log) }}"
                                   class="text-sm font-medium text-blue-600 hover:text-blue-900">Ver</a>

                                <form method="POST" action="{{ route('api-logs.destroy', $log) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-sm font-medium text-red-600 hover:text-red-900"
                                            onclick="return confirm('Excluir este log?')">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <p class="mb-2 text-xl font-medium text-gray-900">Nenhum log encontrado</p>
                                <p class="text-gray-500">Os logs de API aparecerão aqui conforme as requisições forem feitas.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Cleanup Modal -->
<div id="cleanupModal" class="fixed inset-0 z-50 hidden w-full h-full overflow-y-auto bg-gray-900 bg-opacity-50 backdrop-blur-sm">
    <div class="relative w-full max-w-md p-5 mx-auto bg-white shadow-2xl rounded-2xl top-20">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Limpar Logs Antigos</h3>
                <p class="mt-1 text-sm text-gray-600">Remover logs mais antigos que X dias</p>
            </div>
            <button onclick="closeCleanupModal()" class="text-gray-400 transition-colors hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('api-logs.cleanup') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Manter logs dos últimos</label>
                <select name="days" class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="7">7 dias</option>
                    <option value="30" selected>30 dias</option>
                    <option value="60">60 dias</option>
                    <option value="90">90 dias</option>
                </select>
            </div>

            <div class="flex items-center justify-end pt-4 space-x-3 border-t border-gray-200">
              {{-- Continuação do arquivo api-logs/index.blade.php --}}

              <button type="button" onclick="closeCleanupModal()"
              class="px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 rounded-xl hover:bg-gray-200">
          Cancelar
      </button>
      <button type="submit"
              class="px-6 py-3 text-sm font-medium text-white transition-all bg-gradient-to-r from-amber-600 to-amber-700 rounded-xl hover:from-amber-700 hover:to-amber-800">
          Confirmar Limpeza
      </button>
  </div>
</form>
</div>
</div>

<!-- Bulk Delete Modal -->
<div id="bulkDeleteModal" class="fixed inset-0 z-50 hidden w-full h-full overflow-y-auto bg-gray-900 bg-opacity-50 backdrop-blur-sm">
<div class="relative w-full max-w-md p-5 mx-auto bg-white shadow-2xl rounded-2xl top-20">
<div class="flex items-start justify-between mb-6">
  <div>
      <h3 class="text-xl font-semibold text-gray-900">Excluir Logs Selecionados</h3>
      <p class="mt-1 text-sm text-gray-600">Esta ação não pode ser desfeita</p>
  </div>
  <button onclick="closeBulkDeleteModal()" class="text-gray-400 transition-colors hover:text-gray-600">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
  </button>
</div>

<form method="POST" action="{{ route('api-logs.bulk-delete') }}" id="bulkDeleteForm" class="space-y-5">
  @csrf
  <div class="p-4 border border-red-200 bg-red-50 rounded-xl">
      <div class="flex">
          <div class="flex-shrink-0">
              <svg class="w-5 h-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
              </svg>
          </div>
          <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">Atenção</h3>
              <div class="mt-2 text-sm text-red-700">
                  <p>Você está prestes a excluir <strong id="selectedCount">0</strong> log(s). Esta ação é irreversível.</p>
              </div>
          </div>
      </div>
  </div>

  <div class="flex items-center justify-end pt-4 space-x-3 border-t border-gray-200">
      <button type="button" onclick="closeBulkDeleteModal()"
              class="px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 rounded-xl hover:bg-gray-200">
          Cancelar
      </button>
      <button type="submit"
              class="px-6 py-3 text-sm font-medium text-white transition-all bg-gradient-to-r from-red-600 to-red-700 rounded-xl hover:from-red-700 hover:to-red-800">
          Excluir Logs
      </button>
  </div>
</form>
</div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
// Auto-refresh functionality
let autoRefreshInterval = null;
let isAutoRefreshing = false;

// Select all checkbox functionality
const selectAllCheckbox = document.getElementById('selectAll');
const logCheckboxes = document.querySelectorAll('.log-checkbox');

if (selectAllCheckbox) {
selectAllCheckbox.addEventListener('change', function() {
  logCheckboxes.forEach(checkbox => {
      checkbox.checked = this.checked;
  });
  updateBulkDeleteButton();
});
}

// Individual checkbox change
logCheckboxes.forEach(checkbox => {
checkbox.addEventListener('change', function() {
  updateSelectAllCheckbox();
  updateBulkDeleteButton();
});
});

function updateSelectAllCheckbox() {
const checkedBoxes = document.querySelectorAll('.log-checkbox:checked');
const allBoxes = document.querySelectorAll('.log-checkbox');

if (selectAllCheckbox) {
  selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < allBoxes.length;
  selectAllCheckbox.checked = checkedBoxes.length === allBoxes.length && allBoxes.length > 0;
}
}

function updateBulkDeleteButton() {
const checkedBoxes = document.querySelectorAll('.log-checkbox:checked');
const bulkDeleteBtn = document.querySelector('[onclick="openBulkDeleteModal()"]');

if (bulkDeleteBtn) {
  bulkDeleteBtn.disabled = checkedBoxes.length === 0;
  bulkDeleteBtn.classList.toggle('opacity-50', checkedBoxes.length === 0);
  bulkDeleteBtn.classList.toggle('cursor-not-allowed', checkedBoxes.length === 0);
}
}

// Initialize bulk delete button state
updateBulkDeleteButton();
});

// Auto-refresh toggle
function toggleAutoRefresh() {
const btn = document.getElementById('autoRefreshBtn');
if (!btn) return;

if (window.autoRefreshInterval) {
clearInterval(window.autoRefreshInterval);
window.autoRefreshInterval = null;
btn.classList.remove('bg-green-100', 'text-green-700');
btn.classList.add('bg-blue-50', 'text-blue-700');
btn.innerHTML = `
  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
  </svg>
  Auto-refresh
`;
} else {
window.autoRefreshInterval = setInterval(() => {
  window.location.reload();
}, 30000); // 30 seconds

btn.classList.remove('bg-blue-50', 'text-blue-700');
btn.classList.add('bg-green-100', 'text-green-700');
btn.innerHTML = `
  <svg class="w-3 h-3 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
  </svg>
  Ativo (30s)
`;
}
}

// Cleanup Modal functions
function openCleanupModal() {
document.getElementById('cleanupModal').classList.remove('hidden');
}

function closeCleanupModal() {
document.getElementById('cleanupModal').classList.add('hidden');
}

// Bulk Delete Modal functions
function openBulkDeleteModal() {
const checkedBoxes = document.querySelectorAll('.log-checkbox:checked');

if (checkedBoxes.length === 0) {
alert('Por favor, selecione pelo menos um log para excluir.');
return;
}

// Update selected count
document.getElementById('selectedCount').textContent = checkedBoxes.length;

// Add hidden inputs for selected IDs
const form = document.getElementById('bulkDeleteForm');
const existingInputs = form.querySelectorAll('input[name="log_ids[]"]');
existingInputs.forEach(input => input.remove());

checkedBoxes.forEach(checkbox => {
const input = document.createElement('input');
input.type = 'hidden';
input.name = 'log_ids[]';
input.value = checkbox.value;
form.appendChild(input);
});

document.getElementById('bulkDeleteModal').classList.remove('hidden');
}

function closeBulkDeleteModal() {
document.getElementById('bulkDeleteModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
const cleanupModal = document.getElementById('cleanupModal');
const bulkDeleteModal = document.getElementById('bulkDeleteModal');

if (event.target === cleanupModal) {
closeCleanupModal();
}

if (event.target === bulkDeleteModal) {
closeBulkDeleteModal();
}
});

// Close modals with ESC key
document.addEventListener('keydown', function(event) {
if (event.key === 'Escape') {
closeCleanupModal();
closeBulkDeleteModal();
}
});

// Show toast messages
@if(session('success'))
showToast('{{ session('success') }}', 'success');
@endif

@if(session('error'))
showToast('{{ session('error') }}', 'error');
@endif

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
</script>
@endpush