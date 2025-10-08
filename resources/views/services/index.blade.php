@extends('layouts.app')

@section('title', 'Gerenciamento de Serviços')

@section('content')
<div class="sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Gerenciamento de Serviços</h1>
                <p class="mt-2 text-gray-600">Gerencie serviços para software house</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                {{-- <button type="button"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 bg-white border border-green-300 rounded-md hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        onclick="exportData()">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exportar
                </button> --}}
                <button type="button"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 bg-white border border-blue-300 rounded-md hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        onclick="openQuickAddModal()">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Adição Rápida
                </button>
                <a href="{{ route('services.create') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Novo Serviço
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros e Estatísticas -->
    <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-3">
        <!-- Filtros -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="p-6">
                    <form method="GET" action="/servicos/dintell" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Buscar:</label>
                            <input type="text"
                                   name="search"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="{{ request('search') }}"
                                   placeholder="Nome ou código">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Categoria:</label>
                            <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Todas</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Complexidade:</label>
                            <select name="complexity" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Todas</option>
                                @foreach(App\Models\Service::getComplexityLevels() as $key => $level)
                                    <option value="{{ $key }}" {{ request('complexity') == $key ? 'selected' : '' }}>
                                        {{ $level }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Status:</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Todos</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativo</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="inline-flex items-center justify-center flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Filtrar
                            </button>
                            <a href="/servicos/dintell" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Estatística Principal -->
        <div>
            <div class="p-6 text-white shadow-sm bg-gradient-to-r from-green-500 to-green-600 rounded-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium tracking-wide text-green-100 uppercase">Serviços Ativos</p>
                        <p class="text-3xl font-bold">{{ App\Models\Service::active()->count() }}</p>
                    </div>
                    <div class="p-3 bg-green-400 rounded-full bg-opacity-30">
                        <svg class="w-8 h-8 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas por Categoria -->
    {{-- <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
        <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Desenvolvimento</p>
                    <p class="text-2xl font-bold text-gray-900">{{ App\Models\Service::where('category', 'desenvolvimento')->count() }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Consultoria</p>
                    <p class="text-2xl font-bold text-gray-900">{{ App\Models\Service::where('category', 'consultoria')->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-cyan-100">
                    <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Design</p>
                    <p class="text-2xl font-bold text-gray-900">{{ App\Models\Service::where('category', 'design')->count() }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Suporte</p>
                    <p class="text-2xl font-bold text-gray-900">{{ App\Models\Service::where('category', 'suporte')->count() }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Tabela de Serviços -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="flex flex-col px-6 py-4 border-b border-gray-200 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900">Lista de Serviços</h3>
            </div>
            <div class="hidden mt-4 bulk-actions sm:mt-0">
                <div class="flex items-center gap-4 px-4 py-2 border border-blue-200 rounded-lg bg-blue-50">
                    <span class="text-sm text-blue-800">
                        <span class="font-semibold selected-count">0</span> selecionados
                    </span>
                    <div class="flex gap-2">
                        <button type="button" class="inline-flex items-center px-3 py-1 text-xs font-medium text-red-700 bg-white border border-red-300 rounded bulk-action hover:bg-red-50" data-action="delete">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Excluir
                        </button>
                        <button type="button" class="inline-flex items-center px-3 py-1 text-xs font-medium text-yellow-700 bg-white border border-yellow-300 rounded bulk-action hover:bg-yellow-50" data-action="deactivate">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                            </svg>
                            Desativar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded select-all focus:ring-blue-500 focus:ring-2">
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Código</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Categoria</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Preço</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Complexidade</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($services as $service)
                        <tr class="transition-colors filterable-row hover:bg-gray-50" data-status="{{ $service->is_active ? 'active' : 'inactive' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded item-checkbox focus:ring-blue-500 focus:ring-2" value="{{ $service->id }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $service->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">{{ $service->name }}</div>
                                    @if($service->description)
                                        <div class="mt-1 text-gray-500">{{ Str::limit($service->description, 40) }}</div>
                                    @endif
                                    @if($service->estimated_hours)
                                        <div class="flex items-center mt-1 text-xs text-blue-600">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $service->estimated_hours }}h estimadas
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{-- {{ App\Models\Service::getCategories()[$service->category] ?? $service->category }} --}}
                                    {{ $service->category?->name ?? 'Sem categoria' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($service->fixed_price > 0)
                                    <div class="text-sm">
                                        <div class="font-semibold text-green-600">{{ $service->formatted_fixed_price }}</div>
                                        <div class="text-xs text-gray-500">Preço fixo</div>
                                    </div>
                                @else
                                    <div class="text-sm">
                                        <div class="font-semibold text-green-600">{{ $service->formatted_hourly_rate }}</div>
                                        <div class="text-xs text-gray-500">Por hora</div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($service->complexity_level == 'baixa') bg-green-100 text-green-800
                                    @elseif($service->complexity_level == 'media') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($service->complexity_level) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($service->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Ativo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Inativo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('services.show', $service) }}"
                                       class="p-1 text-blue-600 rounded hover:text-blue-900 hover:bg-blue-50"
                                       title="Visualizar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('services.edit', $service) }}"
                                       class="p-1 text-gray-600 rounded hover:text-gray-900 hover:bg-gray-50"
                                       title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button type="button"
                                            class="p-1 text-red-600 rounded hover:text-red-900 hover:bg-red-50"
                                            title="Excluir"
                                            onclick="deleteService({{ $service->id }})">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div class="relative">
                                        <button type="button"
                                                class="p-1 text-gray-600 rounded hover:text-gray-900 hover:bg-gray-50 dropdown-toggle"
                                                onclick="toggleDropdown(this)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                            </svg>
                                        </button>
                                        <div class="absolute right-0 z-10 hidden w-48 mt-2 bg-white border border-gray-200 rounded-md shadow-lg dropdown-menu">
                                            <div class="py-1">
                                                <a href="{{ route('services.duplicate', $service) }}"
                                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                    </svg>
                                                    Duplicar
                                                </a>
                                                <a href="#"
                                                   onclick="toggleStatus({{ $service->id }}, {{ $service->is_active ? 'false' : 'true' }})"
                                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    {{ $service->is_active ? 'Desativar' : 'Ativar' }}
                                                </a>
                                                <a href="#"
                                                   onclick="calculatePrice({{ $service->id }})"
                                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                    </svg>
                                                    Calcular Preço
                                                </a>
                                                <hr class="my-1">
                                                <a href="#"
                                                   onclick="deleteService({{ $service->id }})"
                                                   class="flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Excluir
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <h3 class="mb-2 text-lg font-medium text-gray-900">Nenhum serviço encontrado</h3>
                                    <p class="mb-4 text-gray-500">Comece criando seu primeiro serviço</p>
                                    <a href="{{ route('services.create') }}"
                                       class="inline-flex items-center px-4 py-2 font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Criar Primeiro Serviço
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        @if($services->hasPages())
            <div class="flex flex-col items-center justify-between px-6 py-4 border-t border-gray-200 sm:flex-row">
                <div class="mb-4 text-sm text-gray-700 sm:mb-0">
                    Mostrando {{ $services->firstItem() }} até {{ $services->lastItem() }}
                    de {{ $services->total() }} resultados
                </div>
                <div>
                    {{ $services->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Modal de Adição Rápida -->
    <div id="quickAddModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="flex items-center text-lg font-medium text-gray-900">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Adição Rápida de Serviço
                        </h3>
                        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeQuickAddModal()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form id="quickAddForm" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="md:col-span-2">
                                <label class="block mb-1 text-sm font-medium text-gray-700">Nome do Serviço *</label>
                                <input type="text"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       name="name"
                                       required>
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Categoria *</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        name="category"
                                        required>
                                    <option value="">Selecione...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Preço por Hora</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">MT</span>
                                    <input type="number"
                                           class="w-full py-2 pl-10 pr-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           name="hourly_rate"
                                           step="0.01"
                                           min="0">
                                </div>
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Preço Fixo</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">MT</span>
                                    <input type="number"
                                           class="w-full py-2 pl-10 pr-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           name="fixed_price"
                                           step="0.01"
                                           min="0">
                                </div>
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Complexidade *</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        name="complexity_level"
                                        required>
                                    @foreach(App\Models\Service::getComplexityLevels() as $key => $level)
                                        <option value="{{ $key }}">{{ $level }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Descrição</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      name="description"
                                      rows="3"></textarea>
                        </div>

                        <div class="p-3 border border-blue-200 rounded-md bg-blue-50">
                            <div class="flex">
                                <svg class="w-5 h-5 text-blue-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm text-blue-700">
                                    Informe pelo menos o preço por hora OU preço fixo
                                </p>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            form="quickAddForm"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Salvar
                    </button>
                    <button type="button"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            onclick="closeQuickAddModal()">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Calculadora de Preço -->
    <div id="priceCalculatorModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="flex items-center text-lg font-medium text-gray-900">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Calculadora de Preço
                        </h3>
                        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closePriceCalculatorModal()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div id="calculator-content">
                        <!-- Conteúdo será carregado via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Modal functions
function openQuickAddModal() {
    document.getElementById('quickAddModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeQuickAddModal() {
    document.getElementById('quickAddModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('quickAddForm').reset();
}

function closePriceCalculatorModal() {
    document.getElementById('priceCalculatorModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// Dropdown functions
function toggleDropdown(button) {
    const dropdown = button.nextElementSibling;
    const isHidden = dropdown.classList.contains('hidden');

    // Close all dropdowns
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.classList.add('hidden');
    });

    // Toggle current dropdown
    if (isHidden) {
        dropdown.classList.remove('hidden');
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.dropdown-toggle')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});

// Service functions
function deleteService(id) {
    if (confirm('Tem certeza que deseja excluir este serviço?')) {
        fetch(`/api/services/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Serviço excluído com sucesso!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Erro ao excluir serviço: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao excluir serviço', 'error');
        });
    }
}

function toggleStatus(id, status) {
    fetch(`/api/services/${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ is_active: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Status alterado com sucesso!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('Erro ao alterar status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao alterar status', 'error');
    });
}

function calculatePrice(serviceId) {
    const modal = document.getElementById('priceCalculatorModal');

    fetch(`/api/services/${serviceId}`)
        .then(response => response.json())
        .then(service => {
            const content = document.getElementById('calculator-content');
            content.innerHTML = `
                <div class="mb-4">
                    <h4 class="font-semibold text-gray-900">${service.name}</h4>
                    <p class="mt-1 text-sm text-gray-600">${service.description || 'Sem descrição'}</p>
                </div>

                ${service.fixed_price > 0 ? `
                    <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-blue-800">Preço Fixo</p>
                                <p class="text-lg font-bold text-blue-900">${service.fixed_price} MT</p>
                            </div>
                        </div>
                    </div>
                ` : `
                    <div class="space-y-4">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Horas estimadas:</label>
                            <input type="number"
                                   id="estimated-hours"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="${service.estimated_hours || 1}"
                                   min="0.5"
                                   step="0.5">
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Preço por hora:</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">MT</span>
                                <input type="number"
                                       class="w-full py-2 pl-10 pr-3 border border-gray-300 rounded-md bg-gray-50"
                                       value="${service.hourly_rate}"
                                       readonly>
                            </div>
                        </div>
                        <div class="p-4 border border-green-200 rounded-lg bg-green-50">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-green-800">Total estimado</p>
                                    <p class="text-lg font-bold text-green-900">
                                        <span id="total-price">${(service.hourly_rate * (service.estimated_hours || 1)).toFixed(2)}</span> MT
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                `}

                <div class="pt-4 mt-4 border-t border-gray-200">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="font-medium text-gray-700">Complexidade:</p>
                            <p class="text-gray-600">${service.complexity_level}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-700">Categoria:</p>
                            <p class="text-gray-600">${service.category}</p>
                        </div>
                    </div>
                </div>
            `;

            // Se for preço por hora, adicionar listener para recalcular
            if (service.fixed_price <= 0) {
                const hoursInput = document.getElementById('estimated-hours');
                const totalSpan = document.getElementById('total-price');

                hoursInput.addEventListener('input', function() {
                    const hours = parseFloat(this.value) || 0;
                    const total = (hours * service.hourly_rate).toFixed(2);
                    totalSpan.textContent = total;
                });
            }

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao carregar dados do serviço', 'error');
        });
}

function exportData() {
    const params = new URLSearchParams(window.location.search);
    const link = document.createElement('a');
    link.href = `/services/export?${params.toString()}`;
    link.target = '_blank';
    link.click();
    showNotification('Exportação iniciada!', 'info');
}

// Quick add form submission
document.getElementById('quickAddForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const hourlyRate = formData.get('hourly_rate');
    const fixedPrice = formData.get('fixed_price');

    // Validar se pelo menos um preço foi informado
    if (!hourlyRate && !fixedPrice) {
        showNotification('Informe pelo menos o preço por hora ou preço fixo', 'warning');
        return;
    }

    // Show loading state
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Salvando...';
    submitButton.disabled = true;

    fetch('{{ route("services.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Serviço criado com sucesso!', 'success');
            closeQuickAddModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('Erro ao criar serviço: ' + (data.message || 'Erro desconhecido'), 'error');
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao criar serviço', 'error');
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
});

// Bulk actions
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.querySelector('.select-all');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkActions = document.querySelector('.bulk-actions');
    const selectedCount = document.querySelector('.selected-count');

    // Select all functionality
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    // Individual checkbox functionality
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        const count = checkedBoxes.length;

        if (count > 0) {
            bulkActions.classList.remove('hidden');
            selectedCount.textContent = count;
        } else {
            bulkActions.classList.add('hidden');
        }

        // Update select all checkbox state
        if (selectAll) {
            selectAll.checked = count === itemCheckboxes.length && count > 0;
            selectAll.indeterminate = count > 0 && count < itemCheckboxes.length;
        }
    }

    // Bulk action handlers
    document.querySelectorAll('.bulk-action').forEach(button => {
        button.addEventListener('click', function() {
            const action = this.dataset.action;
            const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
            const ids = Array.from(checkedBoxes).map(cb => cb.value);

            if (ids.length === 0) {
                showNotification('Nenhum item selecionado', 'warning');
                return;
            }

            let confirmMessage = '';
            let endpoint = '';
            let method = 'POST';
            let successMessage = '';

            switch (action) {
                case 'delete':
                    confirmMessage = `Tem certeza que deseja excluir ${ids.length} serviço(s)?`;
                    endpoint = '/api/services/bulk-delete';
                    successMessage = 'Serviços excluídos com sucesso!';
                    break;
                case 'deactivate':
                    confirmMessage = `Tem certeza que deseja desativar ${ids.length} serviço(s)?`;
                    endpoint = '/api/services/bulk-deactivate';
                    successMessage = 'Serviços desativados com sucesso!';
                    break;
            }

            if (confirm(confirmMessage)) {
                fetch(endpoint, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(successMessage, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Erro na operação: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Erro na operação', 'error');
                });
            }
        });
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+N para nova adição rápida
    if (e.ctrlKey && e.key === 'n') {
        e.preventDefault();
        openQuickAddModal();
    }

    // Escape para fechar modais
    if (e.key === 'Escape') {
        closeQuickAddModal();
        closePriceCalculatorModal();
    }
});

// Auto-completar templates baseado na categoria
document.querySelector('select[name="category"]')?.addEventListener('change', function() {
    const category = this.value;

    if (category) {
        fetch(`/api/services/templates?category=${category}`)
            .then(response => response.json())
            .then(templates => {
                // Aqui você pode pré-preencher campos baseado na categoria
                console.log('Templates para', category, templates);
            })
            .catch(error => {
                console.warn('Falha ao carregar templates:', error);
            });
    }
});

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;

    const colors = {
        success: 'bg-green-50 border border-green-200 text-green-800',
        error: 'bg-red-50 border border-red-200 text-red-800',
        info: 'bg-blue-50 border border-blue-200 text-blue-800',
        warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800'
    };

    const icons = {
        success: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
        error: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
        info: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
        warning: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>'
    };

    notification.className += ` ${colors[type]}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0 mr-3">
                ${icons[type]}
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-3">
                <button class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none" onclick="this.closest('.notification').remove()">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
        notification.classList.add('translate-x-0');
    }, 100);

    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}

// Search functionality with debounce
let searchTimeout;
const searchInput = document.querySelector('input[name="search"]');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value;

        if (query.length >= 2 || query.length === 0) {
            searchTimeout = setTimeout(() => {
                // Auto-submit form when typing
                this.closest('form').submit();
            }, 500);
        }
    });
}

// Enhanced table interactions
document.addEventListener('DOMContentLoaded', function() {
    // Add loading states to action buttons
    const actionButtons = document.querySelectorAll('a[href*="services/"], button[onclick*="Service"]');
    actionButtons.forEach(button => {
        if (button.tagName === 'A') {
            button.addEventListener('click', function() {
                const icon = this.querySelector('svg');
                if (icon) {
                    icon.classList.add('animate-spin');
                }
            });
        }
    });
});
</script>
@endpush

@push('styles')
<style>
/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Animation for notifications */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.notification {
    animation: slideInRight 0.3s ease-out;
}

/* Loading animation */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Custom focus styles */
.focus\:ring-2:focus {
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

/* Hover transitions */
.transition-colors {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.transition-shadow {
    transition-property: box-shadow;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Responsive improvements */
@media (max-width: 640px) {
    .table-responsive {
        font-size: 0.875rem;
    }

    .overflow-x-auto {
        scrollbar-width: thin;
    }
}
</style>
@endpush
@endsection