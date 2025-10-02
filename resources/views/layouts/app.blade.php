<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SubManager') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://www.dintell.co.mz/logo.png">

    <!-- favicon icon -->
    <link rel="shortcut icon" href="https://dintell.co.mz/img/favicon.png?v=1753101974">
    <link rel="apple-touch-icon" href="https://dintell.co.mz/images/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="72x72" href="https://dintell.co.mz/../images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="https://dintell.co.mz/../images/apple-touch-icon-114x114.png">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    {{-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>

    </style>
</head>

<body class="font-sans antialiased bg-gray-50" x-data="sidebarData()">
    <div class="flex min-h-screen">
        <!-- Mobile Menu Overlay -->
        <div x-show="sidebarOpen && isMobile" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 lg:hidden" @click="sidebarOpen = false">
            <div class="fixed inset-0 bg-black bg-opacity-25"></div>
        </div>

        <!-- Sidebar -->
        <div :class="{
            'translate-x-0': sidebarOpen || !isMobile,
            '-translate-x-full': !sidebarOpen && isMobile,
            'w-64': !collapsed,
            'w-28': collapsed && !isMobile
        }"
            class="fixed inset-y-0 left-0 z-50 transition-all duration-300 ease-in-out bg-white border-r border-gray-200 shadow-sm lg:static lg:translate-x-0"
            x-cloak>
            <!-- Logo Section -->
            <div class="flex items-center justify-between w-full py-4 align-middle "
                :class="collapsed && !isMobile ? 'py-8' : ''">
                <div class="flex items-center justify-between w-full">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('main.webp') }}" :class="collapsed && !isMobile ? 'w-28 ' : 'w-36'"
                            class="pl-3 transition-all duration-300" />
                    </a>
                    <!-- Toggle Button for Desktop -->
                    <button @click="toggleCollapse()" x-show="!collapsed || isMobile"
                        class="items-center justify-center hidden w-6 h-6 ml-4 font-bold text-gray-400 transition-colors duration-200 rounded lg:flex hover:text-gray-600 hover:bg-gray-100">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                </div>

                <!-- Toggle Button when collapsed (centered) -->
                <button @click="toggleCollapse()" x-show="collapsed && !isMobile"
                    class="items-center hidden w-6 h-6 text-gray-400 transition-colors duration-200 rounded lg:flex hover:text-gray-600 hover:bg-gray-100">
                    <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="px-3">
                <div class="space-y-1">
                    <!-- Título da Seção -->
                    <div x-show="!collapsed || isMobile" class="px-3 py-2">
                        <h3 class="text-xs font-semibold tracking-wider text-gray-500 uppercase">subscrições</h3>
                    </div>

                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group relative {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}"
                        :class="collapsed && !isMobile ? 'justify-center' : ''">
                        <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                        </svg>
                        <span x-show="!collapsed || isMobile">Dashboard</span>

                        <!-- Tooltip para estado colapsado -->
                        <div x-show="collapsed && !isMobile"
                            class="absolute z-50 invisible px-2 py-1 ml-2 text-xs font-medium text-white transition-all duration-200 bg-gray-900 rounded opacity-0 left-full group-hover:opacity-100 group-hover:visible whitespace-nowrap"
                            style="top: 50%; transform: translateY(-50%);">
                            Dashboard
                        </div>
                    </a>

                    <!-- Clientes -->
                    <a href="{{ route('clients.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group relative {{ request()->routeIs('clients.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}"
                        :class="collapsed && !isMobile ? 'justify-center' : ''">
                        <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                        <span x-show="!collapsed || isMobile">Clientes</span>

                        <div x-show="collapsed && !isMobile"
                            class="absolute z-50 invisible px-2 py-1 ml-2 text-xs font-medium text-white transition-all duration-200 bg-gray-900 rounded opacity-0 left-full group-hover:opacity-100 group-hover:visible whitespace-nowrap"
                            style="top: 50%; transform: translateY(-50%);">
                            Clientes
                        </div>
                    </a>

                    <!-- Subscrições -->
                    <a href="{{ route('subscriptions.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group relative {{ request()->routeIs('subscriptions.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}"
                        :class="collapsed && !isMobile ? 'justify-center' : ''">
                        <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span x-show="!collapsed || isMobile">Subscrições</span>

                        <div x-show="collapsed && !isMobile"
                            class="absolute z-50 invisible px-2 py-1 ml-2 text-xs font-medium text-white transition-all duration-200 bg-gray-900 rounded opacity-0 left-full group-hover:opacity-100 group-hover:visible whitespace-nowrap"
                            style="top: 50%; transform: translateY(-50%);">
                            Subscrições
                        </div>
                    </a>

                    <!-- Planos -->
                    <a href="{{ route('plans.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group relative {{ request()->routeIs('plans.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}"
                        :class="collapsed && !isMobile ? 'justify-center' : ''">
                        <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span x-show="!collapsed || isMobile">Planos</span>

                        <div x-show="collapsed && !isMobile"
                            class="absolute z-50 invisible px-2 py-1 ml-2 text-xs font-medium text-white transition-all duration-200 bg-gray-900 rounded opacity-0 left-full group-hover:opacity-100 group-hover:visible whitespace-nowrap"
                            style="top: 50%; transform: translateY(-50%);">
                            Planos
                        </div>
                    </a>

                    <!-- Separador -->
                    <div x-show="!collapsed || isMobile" class="my-4 border-t border-gray-200"></div>

                    <!-- Título da Seção -->
                    <div x-show="!collapsed || isMobile" class="px-3 py-2">
                        <h3 class="text-xs font-semibold tracking-wider text-gray-500 uppercase">Facturação</h3>
                    </div>

                    <!-- Dashboard de Faturação -->
                    <a href="{{ route('billing.dashboard') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group relative {{ request()->routeIs('billing.dashboard') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}"
                        :class="collapsed && !isMobile ? 'justify-center' : ''">
                        <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span x-show="!collapsed || isMobile">Dashboard Facturação</span>

                        <div x-show="collapsed && !isMobile"
                            class="absolute z-50 invisible px-2 py-1 ml-2 text-xs font-medium text-white transition-all duration-200 bg-gray-900 rounded opacity-0 left-full group-hover:opacity-100 group-hover:visible whitespace-nowrap"
                            style="top: 50%; transform: translateY(-50%);">
                            Dashboard Facturação
                        </div>
                    </a>

                    <!-- Faturas -->
                    <a href="{{ route('invoices.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group relative {{ request()->routeIs('invoices.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}"
                        :class="collapsed && !isMobile ? 'justify-center' : ''">
                        <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span x-show="!collapsed || isMobile" class="flex-1">Faturas</span>
                        @php
                            $overdueCount = 3; // Exemplo
                        @endphp
                        @if ($overdueCount > 0)
                            <span x-show="!collapsed || isMobile"
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $overdueCount }}
                            </span>
                        @endif

                        <div x-show="collapsed && !isMobile"
                            class="absolute z-50 invisible px-2 py-1 ml-2 text-xs font-medium text-white transition-all duration-200 bg-gray-900 rounded opacity-0 left-full group-hover:opacity-100 group-hover:visible whitespace-nowrap"
                            style="top: 50%; transform: translateY(-50%);">
                            Faturas @if ($overdueCount > 0)
                                ({{ $overdueCount }})
                            @endif
                        </div>
                    </a>

                    <!-- Cotações -->
                    <a href="{{ route('quotes.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group relative {{ request()->routeIs('quotes.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}"
                        :class="collapsed && !isMobile ? 'justify-center' : ''">
                        <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        <span x-show="!collapsed || isMobile" class="flex-1">Cotações</span>
                        @php
                            $pendingQuotes = 2; // Exemplo
                        @endphp
                        @if ($pendingQuotes > 0)
                            <span x-show="!collapsed || isMobile"
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ $pendingQuotes }}
                            </span>
                        @endif

                        <div x-show="collapsed && !isMobile"
                            class="absolute z-50 invisible px-2 py-1 ml-2 text-xs font-medium text-white transition-all duration-200 bg-gray-900 rounded opacity-0 left-full group-hover:opacity-100 group-hover:visible whitespace-nowrap"
                            style="top: 50%; transform: translateY(-50%);">
                            Cotações @if ($pendingQuotes > 0)
                                ({{ $pendingQuotes }})
                            @endif
                        </div>
                    </a>

                    <!-- Recibos -->
                    <a href="{{ route('receipts.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group relative {{ request()->routeIs('receipts.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}"
                        :class="collapsed && !isMobile ? 'justify-center' : ''">
                        <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                        <span x-show="!collapsed || isMobile" class="flex-1">Recibos</span>

                        <div x-show="collapsed && !isMobile"
                            class="absolute z-50 invisible px-2 py-1 ml-2 text-xs font-medium text-white transition-all duration-200 bg-gray-900 rounded opacity-0 left-full group-hover:opacity-100 group-hover:visible whitespace-nowrap"
                            style="top: 50%; transform: translateY(-50%);">
                            Recibos
                        </div>
                    </a>

                    <!-- Gestão de Stock -->
                    <a href="{{ route('products.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group relative {{ request()->routeIs('products.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}"
                        :class="collapsed && !isMobile ? 'justify-center' : ''">
                        <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span x-show="!collapsed || isMobile">Armazem</span>

                        <div x-show="collapsed && !isMobile"
                            class="absolute z-50 invisible px-2 py-1 ml-2 text-xs font-medium text-white transition-all duration-200 bg-gray-900 rounded opacity-0 left-full group-hover:opacity-100 group-hover:visible whitespace-nowrap"
                            style="top: 50%; transform: translateY(-50%);">
                            Armazem
                        </div>
                    </a>

                    <!-- Menu Dropdown de Ações Rápidas -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="relative flex items-center w-full px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none group"
                            :class="collapsed && !isMobile ? 'justify-center' : ''">
                            <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span x-show="!collapsed || isMobile" class="flex-1 text-left">Ações Rápidas</span>
                            <svg x-show="!collapsed || isMobile" class="w-4 h-4 ml-auto transition-transform"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>

                            <div x-show="collapsed && !isMobile"
                                class="absolute z-50 invisible px-2 py-1 ml-2 text-xs font-medium text-white transition-all duration-200 bg-gray-900 rounded opacity-0 left-full group-hover:opacity-100 group-hover:visible whitespace-nowrap"
                                style="top: 50%; transform: translateY(-50%);">
                                Ações Rápidas
                            </div>
                        </button>

                        <div x-show="open && (!collapsed || isMobile)" x-transition class="mt-2 ml-6 space-y-1">
                            <a href="{{ route('invoices.create') }}"
                                class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:text-gray-900 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Nova Factura
                            </a>

                            <a href="{{ route('quotes.create') }}"
                                class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:text-gray-900 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Nova Cotação
                            </a>

                            <a href="{{ route('credit-notes.index') }}"
                                class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:text-gray-900 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Notas de Crédito
                            </a>

                            <a href="{{ route('debit-notes.index') }}"
                                class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:text-gray-900 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Notas de Débito
                            </a>
                        </div>
                    </div>

                    <!-- Configurações de Faturação -->
                    <a href="{{ route('billing.settings.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group relative {{ request()->routeIs('billing.settings.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}"
                        :class="collapsed && !isMobile ? 'justify-center' : ''">
                        <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span x-show="!collapsed || isMobile">Configurações</span>

                        <div x-show="collapsed && !isMobile"
                            class="absolute z-50 invisible px-2 py-1 ml-2 text-xs font-medium text-white transition-all duration-200 bg-gray-900 rounded opacity-0 left-full group-hover:opacity-100 group-hover:visible whitespace-nowrap"
                            style="top: 50%; transform: translateY(-50%);">
                            Configurações
                        </div>
                    </a>


                    <div x-show="!collapsed || isMobile" class="my-4 border-t border-gray-200"></div>

                    <!-- Logs da API -->
                    <a href="{{ route('api-logs.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group relative {{ request()->routeIs('api-logs.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}"
                        :class="collapsed && !isMobile ? 'justify-center' : ''">
                        <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span x-show="!collapsed || isMobile">Logs da API</span>

                        <div x-show="collapsed && !isMobile"
                            class="absolute z-50 invisible px-2 py-1 ml-2 text-xs font-medium text-white transition-all duration-200 bg-gray-900 rounded opacity-0 left-full group-hover:opacity-100 group-hover:visible whitespace-nowrap"
                            style="top: 50%; transform: translateY(-50%);">
                            Logs da API
                        </div>
                    </a>

                    <!-- Logs de Email -->
                    <a href="{{ route('email-logs.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group relative {{ request()->routeIs('email-logs.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}"
                        :class="collapsed && !isMobile ? 'justify-center' : ''">
                        <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span x-show="!collapsed || isMobile">Logs de Email</span>

                        <div x-show="collapsed && !isMobile"
                            class="absolute z-50 invisible px-2 py-1 ml-2 text-xs font-medium text-white transition-all duration-200 bg-gray-900 rounded opacity-0 left-full group-hover:opacity-100 group-hover:visible whitespace-nowrap"
                            style="top: 50%; transform: translateY(-50%);">
                            Logs de Email
                        </div>
                    </a>




                    {{-- <div class="p-4 mt-6 rounded-lg bg-gray-50">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Status do Sistema</span>
                            <div class="flex items-center">
                                <div class="w-2 h-2 mr-2 bg-green-400 rounded-full"></div>
                                <span class="font-medium text-green-600">Online</span>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            <div class="flex justify-between">
                                <span>Uptime</span>
                                <span>99.9%</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Empresas ativas</span>
                                <span>{{ App\Models\Company::where('status', 'active')->count() }}</span>
                            </div>
                        </div>
                    </div> --}}
                    <div x-show="!collapsed || isMobile" class="my-4 mb-4 border-t border-gray-200"></div>
                    <div class="pt-4">
                        <div class="p-4 rounded-lg bg-gray-50">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Status da Empresa</span>
                                {{-- <div class="flex items-center">
                                    <div class="w-2 h-2 mr-2 bg-green-400 rounded-full"></div>
                                    <span class="font-medium text-green-600">{{ auth()->user()->company->status }}</span>
                                </div> --}}
                            </div>

                            <div class="mt-3 space-y-2 text-xs text-gray-500">
                                <div class="flex justify-between">
                                    <span>Plano</span>

                                    <span class="flex font-medium text-gray-700"> <img
                                            src="{{ asset('facebook-verified.png') }}" alt=""
                                            class="w-4 h-4 mr-1"> {{ auth()->user()->company->Plan->name }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Usuários</span>
                                    <div class="flex flex-col items-end">
                                        @php

                                        @endphp
                                        <span
                                            class="font-medium text-gray-500">{{ auth()->user()->company->users->count() }}
                                            / <a>{{ Auth::user()->Company->Plan->max_users }}Max</a></span>
                                        @if (auth()->user()->company)
                                            @php
                                                $currentUsers = auth()->user()->company->users->count();
                                                $maxUsers = max(auth()->user()->company->max_users ?? 1, 1);
                                                $percentage = min(100, ($currentUsers / $maxUsers) * 100);
                                            @endphp

                                            @php
                                                if ($percentage < 50) {
                                                    $color = 'bg-blue-600';
                                                } elseif ($percentage < 100) {
                                                    $color = 'bg-yellow-500';
                                                } else {
                                                    $color = 'bg-red-600';
                                                }
                                            @endphp
                                            <div class="bg-gray-200 rounded-full h-1.5 mt-1 w-16">
                                                <div class="{{ $color }} h-1.5 rounded-full transition-all duration-300"
                                                    style="width: {{ $percentage }}%"></div>
                                            </div>
                                        @else
                                            <span class="font-medium text-gray-500">N/A</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">

                                    <span>Facturas</span>
                                    <div class="flex flex-col items-end">

                                        <span
                                            class="font-medium text-gray-500">{{ Auth::user()->Company->invoices->count() }}/
                                            <a>{{ Auth::user()->Company->Plan->max_invoices_per_month }}Max</a></span>
                                        @if (auth()->user()->company)
                                            @php
                                                $currentUsers = auth()->user()->company->invoices->count();
                                                $maxUsers = max(
                                                    auth()->user()->Company->Plan->max_invoices_per_month ?? 1,
                                                    1,
                                                );
                                                $percentage = min(100, ($currentUsers / $maxUsers) * 100);
                                            @endphp
                                            @php
                                                if ($percentage < 50) {
                                                    $color = 'bg-blue-600';
                                                } elseif ($percentage < 100) {
                                                    $color = 'bg-yellow-500';
                                                } else {
                                                    $color = 'bg-red-600';
                                                }
                                            @endphp

                                            <div class="bg-gray-200 rounded-full h-1.5 mt-1 w-16">
                                                <div class="{{ $color }} h-1.5 rounded-full transition-all duration-300"
                                                    style="width: {{ $percentage }}%">
                                                </div>
                                            </div>
                                        @else
                                            <span class="font-medium text-gray-500">N/A</span>
                                        @endif
                                    </div>


                                </div>


                            </div>
                        </div>
                    </div>

                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div :class="{
            // 'ml-64': !collapsed && !isMobile,
            // 'ml-20': collapsed && !isMobile,
            'ml-0': isMobile
        }"
            class="flex-1 overflow-hidden transition-all duration-300 ease-in-out">
            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200 shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <!-- Left side with mobile menu button -->
                    <div class="flex items-center space-x-4">
                        <!-- Mobile Menu Button -->
                        <button @click="toggleSidebar()"
                            class="flex items-center justify-center w-8 h-8 text-gray-500 transition-colors duration-200 rounded-lg lg:hidden hover:text-gray-700 hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">@yield('title', 'Dashboard')</h2>
                            <p class="mt-1 text-sm text-gray-600">@yield('subtitle', 'Gerencie suas subscrições e monitore o desempenho')</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Header Actions (from views) -->
                        @yield('header-actions')

                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">


                            <div class="items-center hidden pl-4 space-x-3 border-gray-200 sm:flex ">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg shadow-sm">
                                    <button @click="open = !open"
                                        class="flex items-center text-sm bg-white rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <img class="w-8 h-8 rounded-full"
                                            src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&color=7c3aed&background=ddd6fe"
                                            alt="{{ auth()->user()->name ?? 'User' }}">

                                    </button>
                                </div>

                                <div class="text-right">
                                    <p class="flex items-center text-sm text-gray-600 ">
                                        {{ auth()->user()->name ?? 'User' }}
                                        <img src="{{ asset('facebook-verified.png') }}" alt=""
                                            class="w-4 h-4">
                                    </p>

                                    <p class="flex items-center text-xs text-gray-500 ">
                                        <span
                                            class="font-medium text-blue-600">{{ auth()->user()->company->Plan->name }}</span>

                                    </p>
                                </div>



                            </div>


                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 z-10 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">

                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Meu Perfil
                                </a>

                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Configurações
                                </a>

                                <div class="border-t border-gray-100"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                        Sair
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6 overflow-y-auto">
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="p-4 mb-6 border border-green-200 rounded-md bg-green-50">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L7.53 10.23a.75.75 0 00-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="p-4 mb-6 border border-red-200 rounded-md bg-red-50">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 mb-6 border border-red-200 rounded-md bg-red-50">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Corrija os seguintes erros:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="pl-5 space-y-1 list-disc">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')

            </main>
        </div>
    </div>

    {{-- Support Popup Component --}}
    @auth
        <x-support-popup />
    @endauth

    <!--
<x-subscription-popup
    :company="auth()->user()->company"
    {{-- :plan="auth()->user()->plan" --}}
    :plan="auth()->user()->company->plan ?? null"
    :force-show="true"
/>
-->
   @if (request()->routeIs(['dashboard', 'billing.*', 'quotes.*','invoices.*','receipts.*']))
   {{-- {{ Route::currentRouteName() }} --}}
   {{-- Apenas exibido em páginas críticas: dashboard, faturação, fatura, cotação e recibo --}}
    <x-subscription-popup-advanced :company="auth()->user()->company" :plan="auth()->user()->company->plan ?? null" />       
   @endif

    <x-loading />
    {{-- <x-subscription-popup /> --}}

    @stack('scripts')

    <!-- Alpine.js Data -->
    <script>
        function sidebarData() {
            return {
                sidebarOpen: false,
                collapsed: localStorage.getItem('sidebar-collapsed') === 'true',
                isMobile: window.innerWidth < 1024,

                init() {
                    // Listener para mudanças de tamanho da janela
                    this.handleResize();
                    window.addEventListener('resize', () => {
                        this.handleResize();
                    });

                    // Se for mobile, sempre iniciar fechado
                    if (this.isMobile) {
                        this.sidebarOpen = false;
                        this.collapsed = false;
                    }
                },

                handleResize() {
                    this.isMobile = window.innerWidth < 1024;

                    // Se mudou para mobile, fechar o sidebar
                    if (this.isMobile) {
                        this.sidebarOpen = false;
                        this.collapsed = false;
                    } else {
                        // Se mudou para desktop, restaurar estado do collapse
                        this.collapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                    }
                },

                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                },

                toggleCollapse() {
                    if (!this.isMobile) {
                        this.collapsed = !this.collapsed;
                        localStorage.setItem('sidebar-collapsed', this.collapsed);
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* === Billing System Custom Styles === */
        .stat-card {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .stat-card:hover::before {
            left: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        /* === SELECT2 TAILWIND CSS CUSTOMIZATION === */
        .select2-container {
            width: 100% !important;
            font-family: inherit !important;
        }

        .select2-container--default .select2-selection--single {
            height: 48px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            padding: 0 1rem !important;
            display: flex !important;
            align-items: center !important;
            background-color: #ffffff !important;
            font-size: 0.875rem !important;
            line-height: 1.25rem !important;
            transition: all 0.15s ease-in-out !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        }

        .select2-container--default .select2-selection--single:hover {
            border-color: #9ca3af !important;
        }
    </style>

    <!-- Original Billing System JavaScript (mantido conforme o original) -->
    <script>
        /**
         * Billing System JavaScript Functions
         * Funcionalidades interativas para o sistema de faturamento
         */

        class BillingSystem {
            constructor() {
                this.init();
                this.bindEvents();
            }

            init() {
                this.initTooltips();
                this.initInputMasks();
                this.setupCSRF();
                this.initAnimations();
            }

            bindEvents() {
                document.addEventListener('DOMContentLoaded', () => {
                    this.onDOMReady();
                });
                this.bindFormEvents();
                this.bindFilterEvents();
                this.bindBulkActions();
            }

            onDOMReady() {
                this.animateCards();
                this.updateCounters();
                this.checkNotifications();
            }

            initTooltips() {
                // Implementação dos tooltips
            }

            initInputMasks() {
                // Implementação das máscaras de input
            }

            setupCSRF() {
                const token = document.querySelector('meta[name="csrf-token"]');
                if (token && window.axios) {
                    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
                }
            }

            initAnimations() {
                // Implementação das animações
            }

            bindFormEvents() {
                // Eventos de formulário
            }

            bindFilterEvents() {
                // Eventos de filtro
            }

            bindBulkActions() {
                // Ações em lote
            }

            animateCards() {
                // Animação dos cards
            }

            updateCounters() {
                // Atualização dos contadores
            }

            checkNotifications() {
                // Verificação de notificações
            }

            showNotification(message, type = 'info', duration = 5000) {
                const notification = document.createElement('div');
                notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
                notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';

                notification.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, duration);
            }

            // Outros métodos da classe BillingSystem...
        }

        // Inicializar sistema quando DOM estiver pronto
        document.addEventListener('DOMContentLoaded', function() {
            window.billing = new BillingSystem();

            if (document.body.classList.contains('dashboard-page')) {
                billing.initDashboard();
            }
        });

        window.BillingSystem = BillingSystem;
    </script>

</body>

</html>
