@extends('layouts.app')

@section('title', 'Clientes')

@section('header-actions')
<div class="flex items-center gap-x-4">
    <!-- Search and Filters -->
    <form method="GET" class="flex items-center gap-x-2">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar clientes..."
                   class="block w-full rounded-md border-0 py-1.5 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
        </div>

        <select name="status" class="rounded-md border-0 py-1.5 pl-3 pr-8 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
            <option value="">Todos os status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativo</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
            <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Bloqueado</option>
        </select>

        <button type="submit" class="px-3 py-2 text-sm font-semibold text-white bg-gray-600 rounded-md shadow-sm hover:bg-gray-500">
            Filtrar
        </button>

        @if(request()->hasAny(['search', 'status']))
        <a href="{{ company_route('clients.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            Limpar
        </a>
        @endif
    </form>

    <!-- Add Client Button -->
    <a href="{{ company_route('clients.create') }}"
       class="flex px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
        </svg>
        Novo Cliente
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total de Clientes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Client::count() }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Clientes Ativos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Client::where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Receita Total</p>
                    <p class="text-2xl font-bold text-gray-900">MT {{ number_format(\App\Models\Subscription::sum('total_revenue'), 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Lista de Clientes</h3>
            <p class="mt-1 text-sm text-gray-500">Gerencie todos os clientes do sistema</p>
        </div>

        <div class="">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Cliente
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Contato
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Subscrições
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Receita Total
                        </th>
                          <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            <span class="sr-only">Ações</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($clients ?? [] as $client)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600">
                                        <span class="text-sm font-medium text-white">
                                            {{ substr($client->name ?? 'N', 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $client->name ?? 'N/A' }}</div>
                                    @if($client->company ?? false)
                                        <div class="text-sm text-gray-500">{{ $client->company }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $client->email ?? 'N/A' }}</div>
                            @if($client->phone ?? false)
                                <div class="text-sm text-gray-500">{{ $client->phone }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ ($client->status ?? 'active') === 'active' ? 'bg-green-100 text-green-800' :
                                   (($client->status ?? 'active') === 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($client->status ?? 'active') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900">{{ $client->subscriptions_count ?? 0 }}</span>
                                <span class="ml-2 text-xs text-gray-500">
                                    ({{ $client->active_subscriptions_count ?? 0 }} ativas)
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            MT {{ number_format($client->subscriptions_sum_total_revenue ?? 0, 2) }}
                        </td>
                        <td class="relative justify-between px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                            <!-- Dropdown de Ações -->
                            <div class="relative inline-block text-left" x-data="{ isOpen: false }">
                                <button @click="isOpen = !isOpen" @click.away="isOpen = false"
                                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                    Ações
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="isOpen"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 z-10 w-56 mt-2 origin-top-right bg-white border border-gray-200 rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                                    <div class="py-1">
                                        <!-- Ver Cliente -->
                                        <a href="{{ company_route('clients.show', $client) }}"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Ver Detalhes
                                        </a>

                                        <!-- Editar Cliente -->
                                        <a href="{{ company_route('clients.edit', $client) }}"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Editar Cliente
                                        </a>

                                        <!-- Subscrições -->
                                        <a href="{{ company_route('subscriptions.index', ['client_id' => $client->id]) }}"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Ver Subscrições
                                            @if(($client->subscriptions_count ?? 0) > 0)
                                                <span class="ml-auto px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded-full">
                                                    {{ $client->subscriptions_count }}
                                                </span>
                                            @endif
                                        </a>

                                        <!-- Nova Subscrição -->
                                        <a href="{{ company_route('subscriptions.create', ['client_id' => $client->id]) }}"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Nova Subscrição
                                        </a>

                                        <!-- Divisor -->
                                        <div class="border-t border-gray-100"></div>

                                        <!-- Toggle Status -->
                                        <form method="POST" action="{{ company_route('clients.toggle-status', $client) }}" class="inline w-full">
                                            @csrf
                                            <button type="submit"
                                                    class="flex items-center w-full px-4 py-2 text-sm hover:bg-gray-100
                                                        {{ ($client->status ?? 'active') === 'active' ? 'text-yellow-700 hover:text-yellow-900' : 'text-green-700 hover:text-green-900' }}"
                                                    onclick="return confirm('Tem certeza que deseja {{ ($client->status ?? 'active') === 'active' ? 'desativar' : 'ativar' }} este cliente?')">
                                                @if(($client->status ?? 'active') === 'active')
                                                    <svg class="w-4 h-4 mr-3 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                                    </svg>
                                                    Desativar Cliente
                                                @else
                                                    <svg class="w-4 h-4 mr-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Ativar Cliente
                                                @endif
                                            </button>
                                        </form>


                                        <!-- Deletar Cliente (apenas se não tiver subscrições ativas) -->
                                        @if(($client->active_subscriptions_count ?? 0) === 0)
                                        <div class="border-t border-gray-100"></div>
                                        <form method="POST" action="{{ company_route('clients.destroy', $client) }}" class="inline w-full">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50 hover:text-red-900"
                                                    onclick="return confirm('Tem certeza que deseja deletar este cliente? Esta ação não pode ser desfeita.')">
                                                <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Deletar Cliente
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Botões de Ação Rápida (alternativos) -->
                            <div class="flex items-center justify-end ml-4 space-x-1">
                                <!-- Quick View -->
                                <a href="{{ company_route('clients.show', $client) }}"
                                   class="inline-flex items-center p-1.5 text-gray-400 hover:text-blue-600 rounded-full hover:bg-blue-50"
                                   title="Ver detalhes">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                <!-- Quick Edit -->
                                <a href="{{ company_route('clients.edit', $client) }}"
                                   class="inline-flex items-center p-1.5 text-gray-400 hover:text-indigo-600 rounded-full hover:bg-indigo-50"
                                   title="Editar cliente">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>

                                <!-- Status Badge Clicável -->
                                <form method="POST" action="{{ company_route('clients.toggle-status', $client) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full border-2 border-transparent hover:border-gray-200 transition-colors duration-200
                                                {{ ($client->status ?? 'active') === 'active' ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}"
                                            title="Clique para {{ ($client->status ?? 'active') === 'active' ? 'desativar' : 'ativar' }}"
                                            onclick="return confirm('Deseja {{ ($client->status ?? 'active') === 'active' ? 'desativar' : 'ativar' }} este cliente?')">
                                        @if(($client->status ?? 'active') === 'active')
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Ativo
                                        @else
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            Inativo
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum cliente encontrado</h3>
                            <p class="mt-1 text-sm text-gray-500">Comece criando seu primeiro cliente.</p>
                            <div class="mt-6">
                                <a href="{{ company_route('clients.create') }}"
                                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700">
                                    <svg class="w-5 h-5 mr-2 -ml-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
                                    </svg>
                                    Novo Cliente
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($clients) && $clients->hasPages())
        <div class="px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
            {{ $clients->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
