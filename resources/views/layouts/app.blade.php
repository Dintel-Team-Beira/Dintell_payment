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

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-50" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white border-r border-gray-200 shadow-sm">
            <div class="">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}">

                        <img href="/dashboard" src="{{ asset('logo.png') }}" />
                    </a>

                    {{-- <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-r from-blue-500 to-purple-600">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h1 class="ml-3 text-xl font-bold text-gray-900">SubManager</h1> --}}
                </div>
            </div>

            <nav class="px-3 ">
                <div class="space-y-1">
                    <!-- === NOVA SEÇÃO DE FATURAÇÃO === -->
                    <!-- Separador -->
                    <div class="my-4 border-t border-gray-200"></div>

                    <!-- Título da Seção -->
                    <div class="px-3 py-2">
                        <h3 class="text-xs font-semibold tracking-wider text-gray-500 uppercase">subscrições</h3>
                    </div>
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                        </svg>
                        Dashboard
                    </a>

                    <!-- Clientes -->
                    <a href="{{ route('clients.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('clients.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                        Clientes
                    </a>

                    <!-- Subscrições -->
                    <a href="{{ route('subscriptions.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('subscriptions.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Subscrições
                    </a>

                    <!-- Planos -->
                    <a href="{{ route('plans.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('plans.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Planos
                    </a>


                    <!-- === NOVA SEÇÃO DE FATURAÇÃO === -->
                    <!-- Separador -->
                    <div class="my-4 border-t border-gray-200"></div>

                    <!-- Título da Seção -->
                    <div class="px-3 py-2">
                        <h3 class="text-xs font-semibold tracking-wider text-gray-500 uppercase">Facturação</h3>
                    </div>

                    <!-- Dashboard de Faturação -->
                    <a href="{{ route('billing.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('billing.dashboard') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Dashboard Facturação
                    </a>

                    <!-- Faturas -->
                    <a href="{{ route('invoices.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('invoices.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Facturas
                        @php
                        $overdueCount = \App\Models\Invoice::where('status', 'overdue')->count();
                        @endphp
                        @if($overdueCount > 0)
                        <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $overdueCount }}
                        </span>
                        @endif
                    </a>

                    <!-- Cotações -->
                    <a href="{{ route('quotes.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('quotes.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        Cotações
                        @php
                        $pendingQuotes = \App\Models\Quote::where('status', 'sent')->count();
                        @endphp
                        @if($pendingQuotes > 0)
                        <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $pendingQuotes }}
                        </span>
                        @endif
                    </a>

                    <!-- Menu Dropdown de Ações Rápidas -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center w-full px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Ações Rápidas
                            <svg class="w-4 h-4 ml-auto transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition class="mt-2 ml-6 space-y-1">
                            <a href="{{ route('invoices.create') }}" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:text-gray-900 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Nova Factura
                            </a>

                            <a href="{{ route('quotes.create') }}" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:text-gray-900 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Nova Cotação
                            </a>
                        </div>
                    </div>

                    <!-- Configurações de Faturação -->
                    <a href="{{ route('billing.settings.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('billing.settings.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Configurações
                    </a>

                    <!-- === FIM DA SEÇÃO DE FATURAÇÃO === -->



                    <!-- === FIM DA SEÇÃO DE FATURAÇÃO === -->
                    <!-- Separador -->
                    <div class="my-4 border-t border-gray-200"></div>

                    <!-- Logs da API -->
                    <a href="{{ route('api-logs.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('api-logs.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Logs da API
                    </a>

                    <!-- Logs de Email -->
                    <a href="{{ route('email-logs.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('email-logs.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Logs de Email
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200 shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">@yield('title', 'Dashboard')</h2>
                        <p class="mt-1 text-sm text-gray-600">@yield('subtitle', 'Gerencie suas subscrições e monitore o desempenho')</p>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Header Actions (from views) -->
                        @yield('header-actions')


                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-sm bg-white rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <img class="w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&color=7c3aed&background=ddd6fe" alt="{{ auth()->user()->name }}">
                                <span class="ml-2 font-medium text-gray-700">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 ml-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-10 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">

                                <a href="" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Meu Perfil
                                </a>

                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Configurações
                                </a>

                                <div class="border-t border-gray-100"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
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
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L7.53 10.23a.75.75 0 00-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
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
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
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
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
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

    @stack('scripts')


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
                // Inicializar tooltips do Bootstrap
                this.initTooltips();

                // Inicializar máscaras de input
                this.initInputMasks();

                // Configurar CSRF token para requisições AJAX
                this.setupCSRF();

                // Inicializar animações
                this.initAnimations();
            }

            bindEvents() {
                // Event listeners
                document.addEventListener('DOMContentLoaded', () => {
                    this.onDOMReady();
                });

                // Formulários
                this.bindFormEvents();

                // Filtros e pesquisa
                this.bindFilterEvents();

                // Ações em lote
                this.bindBulkActions();
            }

            onDOMReady() {
                // Fade in dos cards
                this.animateCards();

                // Atualizar contadores em tempo real
                this.updateCounters();

                // Verificar notificações
                this.checkNotifications();
            }

            // ==========================================
            // INICIALIZAÇÃO DE COMPONENTES
            // ==========================================

            initTooltips() {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            initInputMasks() {
                // Máscara para valores monetários
                const moneyInputs = document.querySelectorAll('.money-mask');
                moneyInputs.forEach(input => {
                    input.addEventListener('input', this.formatMoney);
                });

                // Máscara para telefone
                const phoneInputs = document.querySelectorAll('.phone-mask');
                phoneInputs.forEach(input => {
                    input.addEventListener('input', this.formatPhone);
                });

                // Máscara para data
                const dateInputs = document.querySelectorAll('.date-mask');
                dateInputs.forEach(input => {
                    input.addEventListener('input', this.formatDate);
                });
            }

            setupCSRF() {
                const token = document.querySelector('meta[name="csrf-token"]');
                if (token) {
                    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
                }
            }

            initAnimations() {
                // Intersection Observer para animações on scroll
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('fade-in');
                        }
                    });
                });

                const animatedElements = document.querySelectorAll('.animate-on-scroll');
                animatedElements.forEach(el => observer.observe(el));
            }

            // ==========================================
            // EVENTOS DE FORMULÁRIO
            // ==========================================

            bindFormEvents() {
                // Envio de formulários com loading
                const forms = document.querySelectorAll('form');
                forms.forEach(form => {
                    form.addEventListener('submit', this.handleFormSubmit);
                });

                // Auto-save em formulários longos
                const autoSaveForms = document.querySelectorAll('.auto-save');
                autoSaveForms.forEach(form => {
                    const inputs = form.querySelectorAll('input, textarea, select');
                    inputs.forEach(input => {
                        input.addEventListener('change', () => this.autoSave(form));
                    });
                });
            }

            handleFormSubmit(event) {
                const form = event.target;
                const submitBtn = form.querySelector('button[type="submit"]');

                if (submitBtn) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processando...';
                    submitBtn.disabled = true;

                    // Restaurar estado original em caso de erro
                    setTimeout(() => {
                        if (submitBtn.disabled) {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }
                    }, 30000); // 30 segundos timeout
                }
            }

            autoSave(form) {
                const formData = new FormData(form);
                const url = form.getAttribute('data-autosave-url');

                if (url) {
                    fetch(url, {
                            method: 'POST'
                            , body: formData
                            , headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.showNotification('Rascunho salvo automaticamente', 'success', 2000);
                            }
                        })
                        .catch(error => {
                            console.warn('Auto-save failed:', error);
                        });
                }
            }

            // ==========================================
            // FILTROS E PESQUISA
            // ==========================================

            bindFilterEvents() {
                // Filtro em tempo real
                const searchInputs = document.querySelectorAll('.live-search');
                searchInputs.forEach(input => {
                    input.addEventListener('input', this.debounce(this.liveSearch, 300));
                });

                // Filtros de status
                const statusFilters = document.querySelectorAll('.status-filter');
                statusFilters.forEach(filter => {
                    filter.addEventListener('change', this.filterByStatus);
                });

                // Filtros de data
                const dateFilters = document.querySelectorAll('.date-filter');
                dateFilters.forEach(filter => {
                    filter.addEventListener('change', this.filterByDate);
                });
            }

            liveSearch(event) {
                const query = event.target.value;
                const tableBody = document.querySelector('.searchable-table tbody');

                if (query.length < 2) {
                    // Mostrar todas as linhas se query for muito curta
                    const rows = tableBody.querySelectorAll('tr');
                    rows.forEach(row => row.style.display = '');
                    return;
                }

                fetch(`/api/search?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        this.updateTable(tableBody, data);
                    })
                    .catch(error => {
                        console.error('Search failed:', error);
                    });
            }

            filterByStatus(event) {
                const status = event.target.value;
                const rows = document.querySelectorAll('.filterable-row');

                rows.forEach(row => {
                    const rowStatus = row.getAttribute('data-status');
                    if (status === 'all' || rowStatus === status) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                this.updateFilterCount();
            }

            filterByDate(event) {
                const dateValue = event.target.value;
                const dateField = event.target.getAttribute('data-field');
                const rows = document.querySelectorAll('.filterable-row');

                rows.forEach(row => {
                    const rowDate = row.getAttribute(`data-${dateField}`);
                    if (!dateValue || this.dateMatches(rowDate, dateValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // ==========================================
            // AÇÕES EM LOTE
            // ==========================================

            bindBulkActions() {
                // Selecionar todos
                const selectAllCheckbox = document.querySelector('.select-all');
                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', this.selectAll);
                }

                // Checkboxes individuais
                const itemCheckboxes = document.querySelectorAll('.item-checkbox');
                itemCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', this.updateBulkActions);
                });

                // Botões de ação em lote
                const bulkActionBtns = document.querySelectorAll('.bulk-action');
                bulkActionBtns.forEach(btn => {
                    btn.addEventListener('click', this.handleBulkAction);
                });
            }

            selectAll(event) {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = event.target.checked;
                });
                this.updateBulkActions();
            }

            updateBulkActions() {
                const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
                const bulkActionsContainer = document.querySelector('.bulk-actions');
                const selectedCount = document.querySelector('.selected-count');

                if (checkedBoxes.length > 0) {
                    bulkActionsContainer.style.display = 'block';
                    selectedCount.textContent = checkedBoxes.length;
                } else {
                    bulkActionsContainer.style.display = 'none';
                }
            }

            handleBulkAction(event) {
                const action = event.target.getAttribute('data-action');
                const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
                const ids = Array.from(checkedBoxes).map(cb => cb.value);

                if (ids.length === 0) {
                    this.showNotification('Selecione pelo menos um item', 'warning');
                    return;
                }

                // Confirmar ação destrutiva
                if (['delete', 'archive'].includes(action)) {
                    if (!confirm(`Tem certeza que deseja ${action === 'delete' ? 'excluir' : 'arquivar'} ${ids.length} item(ns)?`)) {
                        return;
                    }
                }

                this.executeBulkAction(action, ids);
            }

            executeBulkAction(action, ids) {
                const url = `/api/bulk-${action}`;

                fetch(url, {
                        method: 'POST'
                        , headers: {
                            'Content-Type': 'application/json'
                            , 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                        , body: JSON.stringify({
                            ids
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.showNotification(data.message, 'success');
                            location.reload();
                        } else {
                            this.showNotification(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Bulk action failed:', error);
                        this.showNotification('Erro ao executar ação', 'error');
                    });
            }

            // ==========================================
            // UTILITÁRIOS
            // ==========================================

            debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            formatMoney(event) {
                let value = event.target.value;
                value = value.replace(/\D/g, '');
                value = (value / 100).toFixed(2);
                value = value.replace('.', ',');
                value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
                event.target.value = value;
            }

            formatPhone(event) {
                let value = event.target.value;
                value = value.replace(/\D/g, '');
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
                value = value.replace(/(\d{4})-(\d)(\d{4})/, '$1$2-$3');
                event.target.value = value;
            }

            formatDate(event) {
                let value = event.target.value;
                value = value.replace(/\D/g, '');
                value = value.replace(/(\d{2})(\d)/, '$1/$2');
                value = value.replace(/(\d{2})\/(\d{2})(\d)/, '$1/$2/$3');
                event.target.value = value;
            }

            dateMatches(dateStr, filterDate) {
                const date = new Date(dateStr);
                const filter = new Date(filterDate);
                return date.toDateString() === filter.toDateString();
            }

            updateTable(tableBody, data) {
                // Atualizar tabela com novos dados
                tableBody.innerHTML = '';
                data.forEach(item => {
                    const row = this.createTableRow(item);
                    tableBody.appendChild(row);
                });
            }

            createTableRow(item) {
                // Criar linha da tabela baseada no tipo de item
                const row = document.createElement('tr');
                row.classList.add('filterable-row');
                row.setAttribute('data-status', item.status);
                row.setAttribute('data-date', item.date);

                // Adicionar células baseadas no tipo
                if (item.type === 'invoice') {
                    row.innerHTML = this.createInvoiceRowHTML(item);
                } else if (item.type === 'quote') {
                    row.innerHTML = this.createQuoteRowHTML(item);
                }

                return row;
            }

            createInvoiceRowHTML(invoice) {
                return `
            <td>
                <input type="checkbox" class="item-checkbox" value="${invoice.id}">
            </td>
            <td><strong>#${invoice.number}</strong></td>
            <td>${invoice.client_name}</td>
            <td>${this.formatDate(invoice.date)}</td>
            <td>${this.formatDate(invoice.due_date)}</td>
            <td><strong>${this.formatCurrency(invoice.total)}</strong></td>
            <td>${this.getStatusBadge(invoice.status)}</td>
            <td>${this.getActionButtons(invoice)}</td>
        `;
            }

            createQuoteRowHTML(quote) {
                return `
            <td>
                <input type="checkbox" class="item-checkbox" value="${quote.id}">
            </td>
            <td><strong>#${quote.number}</strong></td>
            <td>${quote.client_name}</td>
            <td>${this.formatDate(quote.date)}</td>
            <td>${this.formatDate(quote.valid_until)}</td>
            <td><strong>${this.formatCurrency(quote.total)}</strong></td>
            <td>${this.getStatusBadge(quote.status)}</td>
            <td>${this.getActionButtons(quote)}</td>
        `;
            }

            getStatusBadge(status) {
                const badges = {
                    'paid': '<span class="badge bg-success">Paga</span>'
                    , 'sent': '<span class="badge bg-info">Enviada</span>'
                    , 'overdue': '<span class="badge bg-danger">Vencida</span>'
                    , 'draft': '<span class="badge bg-secondary">Rascunho</span>'
                    , 'accepted': '<span class="badge bg-success">Aceito</span>'
                    , 'rejected': '<span class="badge bg-danger">Rejeitado</span>'
                    , 'pending': '<span class="badge bg-warning">Pendente</span>'
                };
                return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
            }

            getActionButtons(item) {
                return `
            <div class="btn-group btn-group-sm">
                <a href="/faturas/${item.id}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="/faturas/${item.id}/edit" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-edit"></i>
                </a>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteItem(${item.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
            }

            formatCurrency(value) {
                return new Intl.NumberFormat('pt-MZ', {
                    style: 'currency'
                    , currency: 'MZN'
                }).format(value);
            }

            updateFilterCount() {
                const visibleRows = document.querySelectorAll('.filterable-row:not([style*="display: none"])');
                const totalRows = document.querySelectorAll('.filterable-row');
                const counter = document.querySelector('.filter-count');

                if (counter) {
                    counter.textContent = `Mostrando ${visibleRows.length} de ${totalRows.length} registros`;
                }
            }

            // ==========================================
            // ANIMAÇÕES
            // ==========================================

            animateCards() {
                const cards = document.querySelectorAll('.card');
                cards.forEach((card, index) => {
                    setTimeout(() => {
                        card.classList.add('slide-up');
                    }, index * 100);
                });
            }

            updateCounters() {
                const counters = document.querySelectorAll('.counter');
                counters.forEach(counter => {
                    this.animateCounter(counter);
                });
            }

            animateCounter(element) {
                const target = parseInt(element.getAttribute('data-target'));
                const duration = 2000;
                const start = 0;
                const increment = target / (duration / 16);
                let current = start;

                const timer = setInterval(() => {
                    current += increment;
                    element.textContent = Math.floor(current);

                    if (current >= target) {
                        element.textContent = target;
                        clearInterval(timer);
                    }
                }, 16);
            }

            // ==========================================
            // NOTIFICAÇÕES
            // ==========================================

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

            checkNotifications() {
                // Verificar notificações do servidor
                fetch('/api/notifications')
                    .then(response => response.json())
                    .then(data => {
                        if (data.notifications && data.notifications.length > 0) {
                            this.displayNotifications(data.notifications);
                        }
                    })
                    .catch(error => {
                        console.warn('Failed to check notifications:', error);
                    });
            }

            displayNotifications(notifications) {
                notifications.forEach(notification => {
                    this.showNotification(notification.message, notification.type, notification.duration);
                });
            }

            // ==========================================
            // DASHBOARD ESPECÍFICO
            // ==========================================

            initDashboard() {
                this.loadDashboardData();
                this.setupRealTimeUpdates();
            }

            loadDashboardData() {
                // Carregar dados do dashboard via AJAX
                fetch('/api/dashboard/data')
                    .then(response => response.json())
                    .then(data => {
                        this.updateDashboardCards(data);
                        this.updateCharts(data);
                    })
                    .catch(error => {
                        console.error('Failed to load dashboard data:', error);
                    });
            }

            updateDashboardCards(data) {
                // Atualizar cards do dashboard
                Object.keys(data.stats).forEach(key => {
                    const element = document.querySelector(`[data-stat="${key}"]`);
                    if (element) {
                        element.textContent = data.stats[key];
                    }
                });
            }

            updateCharts(data) {
                // Atualizar gráficos com novos dados
                if (window.billingChart) {
                    window.billingChart.data.datasets[0].data = data.chart.paid;
                    window.billingChart.data.datasets[1].data = data.chart.pending;
                    window.billingChart.update();
                }
            }

            setupRealTimeUpdates() {
                // Atualizar dashboard a cada 5 minutos
                setInterval(() => {
                    this.loadDashboardData();
                }, 300000);
            }

            // ==========================================
            // EXPORTAÇÃO E IMPRESSÃO
            // ==========================================

            exportData(format = 'pdf') {
                const currentFilters = this.getCurrentFilters();
                const url = `/export/${format}?${new URLSearchParams(currentFilters)}`;

                // Abrir em nova janela
                window.open(url, '_blank');
            }

            printTable() {
                const printWindow = window.open('', '_blank');
                const tableHTML = document.querySelector('.printable-table').outerHTML;

                printWindow.document.write(`
            <html>
                <head>
                    <title>Relatório de Facturamento</title>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #f2f2f2; }
                        .no-print { display: none !important; }
                    </style>
                </head>
                <body>
                    <h1>Relatório de Facturamento</h1>
                    ${tableHTML}
                </body>
            </html>
        `);

                printWindow.document.close();
                printWindow.print();
            }

            getCurrentFilters() {
                const filters = {};
                const filterInputs = document.querySelectorAll('.filter-input');

                filterInputs.forEach(input => {
                    if (input.value) {
                        filters[input.name] = input.value;
                    }
                });

                return filters;
            }
        }

        // ==========================================
        // FUNÇÕES GLOBAIS
        // ==========================================

        function deleteItem(id, type = 'invoice') {
            if (confirm('Tem certeza que deseja excluir este item?')) {
                fetch(`/api/${type}s/${id}`, {
                        method: 'DELETE'
                        , headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Erro ao excluir item');
                        }
                    });
            }
        }

        function duplicateItem(id, type = 'invoice') {
            fetch(`/api/${type}s/${id}/duplicate`, {
                    method: 'POST'
                    , headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect_url;
                    } else {
                        alert('Erro ao duplicar item');
                    }
                });
        }

        function sendItem(id, type = 'invoice') {
            if (confirm(`Tem certeza que deseja enviar este ${type === 'invoice' ? 'fatura' : 'orçamento'}?`)) {
                fetch(`/api/${type}s/${id}/send`, {
                        method: 'POST'
                        , headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            billing.showNotification(data.message, 'success');
                            location.reload();
                        } else {
                            billing.showNotification(data.message, 'error');
                        }
                    });
            }
        }

        function markAsPaid(id) {
            if (confirm('Marcar esta factura como paga?')) {
                fetch(`/api/invoices/${id}/mark-paid`, {
                        method: 'POST'
                        , headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            billing.showNotification('Factura marcada como paga', 'success');
                            location.reload();
                        } else {
                            billing.showNotification('Erro ao marcar factura como paga', 'error');
                        }
                    });
            }
        }

        // Inicializar sistema quando DOM estiver pronto
        document.addEventListener('DOMContentLoaded', function() {
            window.billing = new BillingSystem();

            // Inicializar dashboard se estivermos na página
            if (document.body.classList.contains('dashboard-page')) {
                billing.initDashboard();
            }
        });

        // Exportar para uso global
        window.BillingSystem = BillingSystem;

    </script>

    <style>
        /* ===========================
   Billing System Custom Styles
   =========================== */

        /* Dashboard Cards Animation */
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

        /* Status Badges */
        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-paid {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-sent {
            background-color: #cce5ff;
            color: #004085;
            border: 1px solid #99d6ff;
        }

        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f1aeb5;
        }

        .status-draft {
            background-color: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
        }

        /* Invoice/Quote Cards */
        .document-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
        }

        .document-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .document-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }

        .document-number {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .document-amount {
            font-size: 1.3rem;
            font-weight: 800;
            color: #27ae60;
        }

        /* Client Avatar */
        .client-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 1rem;
            margin-right: 1rem;
        }

        /* Action Buttons */
        .action-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: all 0.3s ease;
            margin: 0 2px;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        .btn-view {
            background-color: #17a2b8;
            color: white;
        }

        .btn-edit {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn-duplicate {
            background-color: #6c757d;
            color: white;
        }

        .btn-send {
            background-color: #007bff;
            color: white;
        }

        /* Charts */
        .chart-container {
            position: relative;
            height: 350px;
            padding: 1rem;
        }

        .chart-legend {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin: 0 1rem 0.5rem 0;
        }

        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 3px;
            margin-right: 0.5rem;
        }

        /* Tables */
        .billing-table {
            font-size: 0.9rem;
        }

        .billing-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            padding: 1rem 0.75rem;
            border: none;
        }

        .billing-table td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
        }

        .billing-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Overdue Items */
        .overdue-item {
            border-left: 4px solid #dc3545;
            background-color: #fff5f5;
            padding: 1rem;
            margin-bottom: 0.5rem;
            border-radius: 0 8px 8px 0;
            transition: all 0.3s ease;
        }

        .overdue-item:hover {
            background-color: #ffe6e6;
            transform: translateX(5px);
        }

        .overdue-amount {
            font-weight: bold;
            font-size: 1.1rem;
            color: #dc3545;
        }

        /* Progress Bars */
        .custom-progress {
            height: 12px;
            border-radius: 10px;
            background-color: #e9ecef;
            overflow: hidden;
        }

        .custom-progress-bar {
            height: 100%;
            border-radius: 10px;
            transition: width 0.6s ease;
            position: relative;
        }

        .custom-progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255, 255, 255, .2) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .2) 50%, rgba(255, 255, 255, .2) 75%, transparent 75%, transparent);
            background-size: 30px 30px;
            animation: move 2s linear infinite;
        }

        @keyframes move {
            0% {
                background-position: 0 0;
            }

            100% {
                background-position: 30px 30px;
            }
        }

        /* Alerts and Notifications */
        .custom-alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
        }

        .custom-alert::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
        }

        .custom-alert.alert-success::before {
            background-color: #28a745;
        }

        .custom-alert.alert-danger::before {
            background-color: #dc3545;
        }

        .custom-alert.alert-warning::before {
            background-color: #ffc107;
        }

        .custom-alert.alert-info::before {
            background-color: #17a2b8;
        }

        /* Form Styles */
        .form-floating {
            position: relative;
        }

        .form-floating>.form-control {
            height: calc(3.5rem + 2px);
            line-height: 1.25;
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: border-color 0.3s ease;
        }

        .form-floating>.form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .form-floating>label {
            font-weight: 500;
            color: #6c757d;
        }

        /* Sidebar Mobile */
        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                width: 100%;
            }
        }

        /* Loading States */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-spinner {
            width: 3rem;
            height: 3rem;
            border: 0.4em solid #f3f3f3;
            border-top: 0.4em solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive Tables */
        @media (max-width: 768px) {
            .table-responsive-stack tr {
                display: block;
                border: 1px solid #ccc;
                margin-bottom: 10px;
                border-radius: 8px;
                padding: 10px;
                background: white;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .table-responsive-stack td {
                display: block;
                border: none;
                padding: 0.5rem 0;
                text-align: right;
            }

            .table-responsive-stack td::before {
                content: attr(data-label) ": ";
                font-weight: bold;
                text-transform: uppercase;
                color: #667eea;
                float: left;
            }

            .table-responsive-stack th {
                display: none;
            }
        }

        /* Print Styles */
        @media print {

            .sidebar,
            .navbar,
            .btn,
            .action-btn,
            .dropdown,
            .alert {
                display: none !important;
            }

            .main-content {
                margin: 0 !important;
                padding: 0 !important;
            }

            .card {
                border: 1px solid #dee2e6 !important;
                box-shadow: none !important;
                page-break-inside: avoid;
            }

            body {
                background: white !important;
                color: black !important;
            }
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-up {
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Utility Classes */
        .text-shadow {
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%) !important;
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%) !important;
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
        }

        .border-radius-lg {
            border-radius: 15px !important;
        }

        .border-radius-xl {
            border-radius: 20px !important;
        }

        .shadow-soft {
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1) !important;
        }

        .shadow-strong {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
        }

    </style>
</body>
</html>
