@extends('layouts.app')

@section('title', 'Logs de API')

@section('header-actions')
<div class="flex items-center gap-x-4">
    <!-- Filters -->
    <form method="GET" class="flex items-center gap-x-2">
        {{-- <div class="relative">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar por domínio, endpoint ou IP..."
                   class="block w-full rounded-md border-0 py-1.5 pl-3 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
        </div> --}}
{{--
        <select name="status" class="rounded-md border-0 py-1.5 pl-3 pr-8 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
            <option value="">Todos os status</option>
            <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Sucesso</option>
            <option value="client_error" {{ request('status') === 'client_error' ? 'selected' : '' }}>Erro do Cliente</option>
            <option value="server_error" {{ request('status') === 'server_error' ? 'selected' : '' }}>Erro do Servidor</option>
            <option value="error" {{ request('status') === 'error' ? 'selected' : '' }}>Todos os Erros</option>
        </select> --}}

        <select name="domain" class="rounded-md border-0 py-1.5 pl-3 pr-8 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
            <option value="">Todos os domínios</option>
            @foreach($domains as $domain)
                <option value="{{ $domain }}" {{ request('domain') === $domain ? 'selected' : '' }}>
                    {{ $domain }}
                </option>
            @endforeach
        </select>

        <input type="date" name="date_from" value="{{ request('date_from') }}"
               class="rounded-md border-0 py-1.5 pl-3 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">

        <input type="date" name="date_to" value="{{ request('date_to') }}"
               class="rounded-md border-0 py-1.5 pl-3 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">

        <button type="submit" class="px-3 py-2 text-sm font-semibold text-white bg-gray-600 rounded-md hover:bg-gray-500">
            Filtrar
        </button>

        @if(request()->hasAny(['search', 'status', 'domain', 'date_from', 'date_to']))
        <a href="{{ route('api-logs.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            Limpar
        </a>
        @endif
    </form>

    <!-- Export Button -->
    <a href="#"
       class="px-3 py-2 text-sm font-semibold text-white bg-green-600 rounded-md hover:bg-green-500">
        Exportar CSV
    </a>

    <!-- Bulk Actions -->
    <form method="POST" action="{{ route('api-logs.cleanup') }}" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('Limpar logs de API antigos (30+ dias)?')"
                class="px-3 py-2 text-sm font-semibold text-white bg-red-600 rounded-md hover:bg-red-500">
            Limpar Logs
        </button>
    </form>
</div>
@endsection

