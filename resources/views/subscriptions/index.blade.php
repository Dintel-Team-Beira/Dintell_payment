@extends('layouts.app')

@section('title', 'Subscrições')

@section('header-actions')
<div class="flex items-center gap-x-4">
    <!-- Search and Filters -->
    <form method="GET" class="flex items-center gap-x-3">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar domínio ou cliente..."
                   class="block w-64 rounded-xl border-0 py-2.5 pl-10 pr-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm transition-all">
        </div>

        <select name="status" class="rounded-xl border-0 py-2.5 pl-4 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-blue-500 sm:text-sm transition-all">
            <option value="">Todos os status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativo</option>
            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspenso</option>
            <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>Trial</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
        </select>

        <select name="plan" class="rounded-xl border-0 py-2.5 pl-4 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-blue-500 sm:text-sm transition-all">
            <option value="">Todos os planos</option>
            @foreach($plans as $plan)
                <option value="{{ $plan->id }}" {{ request('plan') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
            @endforeach
        </select>

        <button type="submit" class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-sm hover:from-blue-700 hover:to-blue-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            Filtrar
        </button>

        @if(request()->hasAny(['search', 'status', 'plan']))
        <a href="{{ route('subscriptions.index') }}" class="inline-flex items-center px-3 py-2 text-sm text-gray-600 transition-all rounded-lg hover:text-gray-900 hover:bg-gray-100">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Limpar
        </a>
        @endif
    </form>

    <!-- Add Subscription Button -->
    <a href="{{ route('subscriptions.create') }}"
       class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-xl shadow-sm hover:from-emerald-700 hover:to-emerald-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-all">
        <svg class="w-4 h-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
        </svg>
        Nova Subscrição
    </a>
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
                        <p class="text-sm font-medium text-blue-100">Total Subscrições</p>
                        <p class="mt-1 text-3xl font-bold text-white">{{ \App\Models\Subscription::count() }}</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
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
                        <p class="text-sm font-medium text-emerald-100">Ativas</p>
                        <p class="mt-1 text-3xl font-bold text-white">{{ \App\Models\Subscription::where('status', 'active')->count() }}</p>
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
                        <p class="text-sm font-medium text-amber-100">Suspensas</p>
                        <p class="mt-1 text-3xl font-bold text-white">{{ \App\Models\Subscription::where('status', 'suspended')->count() }}</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative p-6 overflow-hidden shadow-lg bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl">
            <div class="absolute top-0 right-0 w-24 h-24 -mt-4 -mr-4 rounded-full bg-white/10"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-100">Receita Mensal</p>
                        <p class="mt-1 text-3xl font-bold text-white"> {{ number_format(\App\Models\Subscription::where('last_payment_date', '>=', now()->startOfMonth())->sum('amount_paid'), 0) }} MT</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Subscriptions Cards -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Lista de Subscrições</h2>
                <p class="mt-1 text-gray-600">Gerencie todas as subscrições do sistema</p>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="toggleView('grid')" id="gridView" class="p-2 text-gray-400 transition-all rounded-lg hover:text-gray-600 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </button>
                <button onclick="toggleView('list')" id="listView" class="p-2 text-blue-600 transition-all rounded-lg bg-blue-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Grid View -->
        <div id="gridContainer" class="grid hidden grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($subscriptions as $subscription)
            <div class="overflow-hidden transition-all duration-200 bg-white border border-gray-100 shadow-sm rounded-2xl hover:shadow-lg">
                <!-- Card Header -->
                <div class="p-6 border-b border-gray-50">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 rounded-full {{ $subscription->canAccess() ? 'bg-emerald-400' : 'bg-red-400' }}"></div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $subscription->client->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $subscription->domain }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            {{ $subscription->status === 'active' ? 'bg-emerald-100 text-emerald-800' :
                               ($subscription->status === 'suspended' ? 'bg-amber-100 text-amber-800' :
                                ($subscription->status === 'trial' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                            {{ ucfirst($subscription->status) }}
                        </span>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Plano</span>
                        <span class="font-medium text-gray-900">{{ $subscription->plan->name }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Valor</span>
                        <span class="font-medium text-gray-900">MT {{ number_format($subscription->plan->price, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Expira em</span>
                        <div class="text-right">
                            @if($subscription->ends_at)
                                <div class="font-medium text-gray-900">{{ $subscription->ends_at->format('d/m/Y') }}</div>
                                <div class="text-xs {{ $subscription->days_until_expiry <= 7 ? 'text-red-600' : 'text-gray-500' }}">
                                    {{ $subscription->days_until_expiry > 0 ? (int)$subscription->days_until_expiry . ' dias' : 'Expirado' }}
                                </div>
                            @else
                                <span class="text-gray-500">Sem expiração</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Receita Total</span>
                        <span class="font-medium text-gray-900"> {{ number_format($subscription->total_revenue, 0) }} MT</span>
                    </div>
                </div>

                <!-- Card Actions -->
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('subscriptions.show', $subscription) }}"
                               class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition-all">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Ver
                            </a>

                            @if($subscription->isExpired() || $subscription->status !== 'active')
                                <button onclick="openRenewModal({{ $subscription->id }}, '{{ $subscription->client->name }}', {{ $subscription->plan->price }})"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-100 rounded-lg hover:bg-emerald-200 transition-all">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Renovar
                                </button>
                            @endif
                        </div>

                        <!-- Dropdown Menu -->
                        <div class="relative">
                            <button onclick="toggleDropdown({{ $subscription->id }})" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-200 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>

                            <div id="dropdown-{{ $subscription->id }}" class="absolute right-0 z-10 hidden w-48 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg">
                                <div class="py-1">
                                    <a href="{{ route('subscriptions.edit', $subscription) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar
                                    </a>

                                    @if($subscription->status === 'active')
                                        <button onclick="openSuspendModal({{ $subscription->id }})" class="flex items-center w-full px-4 py-2 text-sm text-amber-700 hover:bg-amber-50">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Suspender
                                        </button>
                                    @elseif($subscription->status === 'suspended')
                                        <form method="POST" action="{{ route('subscriptions.activate', $subscription) }}" class="inline w-full">
                                            @csrf
                                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-emerald-700 hover:bg-emerald-50">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V9a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2z"/>
                                                </svg>
                                                Ativar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- List View (Default) -->
        <div id="listContainer" class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Cliente & Domínio</th>
                            <th class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Plano</th>
                            <th class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Expiração</th>
                            <th class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Receita</th>
                            <th class="px-6 py-4 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($subscriptions as $subscription)
                        <tr class="transition-colors hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-3 {{ $subscription->canAccess() ? 'bg-emerald-400' : 'bg-red-400' }}"></div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $subscription->client->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $subscription->domain }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $subscription->plan->name }}</div>
                                <div class="text-sm text-gray-500">MT {{ number_format($subscription->plan->price, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $subscription->status === 'active' ? 'bg-emerald-100 text-emerald-800' :
                                       ($subscription->status === 'suspended' ? 'bg-amber-100 text-amber-800' :
                                        ($subscription->status === 'trial' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                @if($subscription->ends_at)
                                    <div>{{ $subscription->ends_at->format('d/m/Y') }}</div>
                                    <div class="text-xs {{ $subscription->days_until_expiry <= 7 ? 'text-red-600' : 'text-gray-400' }}">
                                        {{ $subscription->days_until_expiry > 0 ? (int)$subscription->days_until_expiry . ' dias' : 'Expirado' }}
                                    </div>
                                @else
                                    <span class="text-gray-400">Sem expiração</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            {{ number_format($subscription->total_revenue, 0) }} MT
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('subscriptions.show', $subscription) }}"
                                       class="text-sm font-medium text-blue-600 hover:text-blue-900">Ver</a>

                                    @if($subscription->isExpired() || $subscription->status !== 'active')
                                        <button onclick="openRenewModal({{ $subscription->id }}, '{{ $subscription->client->name }}', {{ $subscription->plan->price }})"
                                                class="text-sm font-medium text-emerald-600 hover:text-emerald-900">Renovar</button>
                                    @endif

                                    <a href="{{ route('subscriptions.edit', $subscription) }}"
                                       class="text-sm font-medium text-indigo-600 hover:text-indigo-900">Editar</a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    <p class="mb-2 text-xl font-medium text-gray-900">Nenhuma subscrição encontrada</p>
                                    <p class="mb-6 text-gray-500">Comece criando sua primeira subscrição.</p>
                                    <a href="{{ route('subscriptions.create') }}"
                                       class="inline-flex items-center px-6 py-3 font-medium text-white transition-all bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl hover:from-blue-700 hover:to-blue-800">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Nova Subscrição
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($subscriptions->hasPages())
    <div class="flex items-center justify-between px-6 py-4 bg-white border border-gray-200 rounded-xl">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($subscriptions->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-500 bg-white border border-gray-300 rounded-md cursor-default">
                    Anterior
                </span>
            @else
                <a href="{{ $subscriptions->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700">
                    Anterior
                </a>
            @endif

            @if ($subscriptions->hasMorePages())
                <a href="{{ $subscriptions->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700">
                    Próximo
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium leading-5 text-gray-500 bg-white border border-gray-300 rounded-md cursor-default">
                    Próximo
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Mostrando
                    <span class="font-medium">{{ $subscriptions->firstItem() }}</span>
                    até
                    <span class="font-medium">{{ $subscriptions->lastItem() }}</span>
                    de
                    <span class="font-medium">{{ $subscriptions->total() }}</span>
                    resultados
                </p>
            </div>
            <div>
                {{ $subscriptions->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Enhanced Renew Modal -->
<div id="renewModal" class="fixed inset-0 z-50 hidden w-full h-full overflow-y-auto bg-gray-900 bg-opacity-50 backdrop-blur-sm">
    <div class="relative w-full max-w-md p-5 mx-auto bg-white shadow-2xl rounded-2xl top-20">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Renovar Subscrição</h3>
                <p class="mt-1 text-sm text-gray-600" id="renewClientName"></p>
            </div>
            <button onclick="closeRenewModal()" class="text-gray-400 transition-colors hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="renewForm" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Valor do Pagamento</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">MT</span>
                    </div>
                    <input type="number" step="0.01" name="amount_paid" id="renewAmount" required
                           class="block w-full py-3 pl-12 pr-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Método de Pagamento</label>
                <select name="payment_method" required
                        class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Selecione o método...</option>
                    <option value="mpesa">MPesa</option>
                    <option value="visa">Visa/Mastercard</option>
                    <option value="bank_transfer">Transferência Bancária</option>
                    <option value="cash">Dinheiro</option>
                </select>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Referência do Pagamento</label>
                <input type="text" name="payment_reference" placeholder="Ex: TX123456789"
                       class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <div class="flex items-center justify-end pt-4 space-x-3 border-t border-gray-200">
                <button type="button" onclick="closeRenewModal()"
                        class="px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 rounded-xl hover:bg-gray-200">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-6 py-3 text-sm font-medium text-white transition-all bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-xl hover:from-emerald-700 hover:to-emerald-800">
                    <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Processar Renovação
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Enhanced Suspend Modal -->
<div id="suspendModal" class="fixed inset-0 z-50 hidden w-full h-full overflow-y-auto bg-gray-900 bg-opacity-50 backdrop-blur-sm">
    <div class="relative w-full max-w-md p-5 mx-auto bg-white shadow-2xl rounded-2xl top-20">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Suspender Subscrição</h3>
                <p class="mt-1 text-sm text-gray-600">Esta ação pode ser revertida posteriormente</p>
            </div>
            <button onclick="closeSuspendModal()" class="text-gray-400 transition-colors hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="suspendForm" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Motivo da Suspensão</label>
                <textarea name="reason" rows="4" required
                          class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 sm:text-sm"
                          placeholder="Descreva o motivo da suspensão..."></textarea>
            </div>

            <div class="flex items-center justify-end pt-4 space-x-3 border-t border-gray-200">
                <button type="button" onclick="closeSuspendModal()"
                        class="px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 rounded-xl hover:bg-gray-200">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-6 py-3 text-sm font-medium text-white transition-all bg-gradient-to-r from-amber-600 to-amber-700 rounded-xl hover:from-amber-700 hover:to-amber-800">
                    <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Suspender
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// View Toggle Functions
function toggleView(view) {
    const gridContainer = document.getElementById('gridContainer');
    const listContainer = document.getElementById('listContainer');
    const gridBtn = document.getElementById('gridView');
    const listBtn = document.getElementById('listView');

    if (view === 'grid') {
        gridContainer.classList.remove('hidden');
        listContainer.classList.add('hidden');
        gridBtn.classList.add('text-blue-600', 'bg-blue-50');
        gridBtn.classList.remove('text-gray-400');
        listBtn.classList.remove('text-blue-600', 'bg-blue-50');
        listBtn.classList.add('text-gray-400');
    } else {
        listContainer.classList.remove('hidden');
        gridContainer.classList.add('hidden');
        listBtn.classList.add('text-blue-600', 'bg-blue-50');
        listBtn.classList.remove('text-gray-400');
        gridBtn.classList.remove('text-blue-600', 'bg-blue-50');
        gridBtn.classList.add('text-gray-400');
    }
}

// Dropdown Functions
function toggleDropdown(id) {
    const dropdown = document.getElementById(`dropdown-${id}`);
    dropdown.classList.toggle('hidden');

    // Close other dropdowns
    document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
        if (el.id !== `dropdown-${id}`) {
            el.classList.add('hidden');
        }
    });
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick^="toggleDropdown"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
            el.classList.add('hidden');
        });
    }
});

// Renew Modal Functions
function openRenewModal(subscriptionId, clientName, planPrice) {
    document.getElementById('renewForm').action = `/subscriptions/${subscriptionId}/renew`;
    document.getElementById('renewClientName').textContent = clientName;
    document.getElementById('renewAmount').value = planPrice;
    document.getElementById('renewModal').classList.remove('hidden');
}

function closeRenewModal() {
    document.getElementById('renewModal').classList.add('hidden');
}

// Suspend Modal Functions
function openSuspendModal(subscriptionId) {
    document.getElementById('suspendForm').action = `/subscriptions/${subscriptionId}/suspend`;
    document.getElementById('suspendModal').classList.remove('hidden');
}

function closeSuspendModal() {
    document.getElementById('suspendModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const renewModal = document.getElementById('renewModal');
    const suspendModal = document.getElementById('suspendModal');

    if (event.target === renewModal) {
        closeRenewModal();
    }
    if (event.target === suspendModal) {
        closeSuspendModal();
    }
});

// Form Validation
document.getElementById('renewForm').addEventListener('submit', function(e) {
    const amount = document.getElementById('renewAmount').value;
    const paymentMethod = document.querySelector('select[name="payment_method"]').value;

    if (!amount || amount <= 0) {
        e.preventDefault();
        alert('Por favor, insira um valor válido.');
        return;
    }

    if (!paymentMethod) {
        e.preventDefault();
        alert('Por favor, selecione um método de pagamento.');
        return;
    }
});
</script>

<style>
/* Custom animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.hover\\:scale-105:hover {
    transform: scale(1.05);
}

/* Loading state */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Smooth transitions */
* {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>
@endsection