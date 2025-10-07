<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') - {{ config('app.name', 'SFS Admin') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="h-full">
    <div class="min-h-full" x-data="{ sidebarOpen: false }">
        <!-- Off-canvas menu for mobile -->
        <div x-show="sidebarOpen" class="relative z-50 lg:hidden" x-description="Off-canvas menu for mobile" style="display: none;">
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80" style="display: none;"></div>

            <div class="fixed inset-0 flex">
                <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" x-description="Off-canvas menu, show/hide based on off-canvas menu state." class="relative flex flex-1 w-full max-w-xs mr-16" style="display: none;">
                    <div x-show="sidebarOpen" x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-description="Close button, show/hide based on off-canvas menu state." class="absolute top-0 flex justify-center w-16 pt-5 left-full" style="display: none;">
                        <button type="button" class="-m-2.5 p-2.5" @click="sidebarOpen = false">
                            <span class="sr-only">Fechar sidebar</span>
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Sidebar component -->
                    <div class="flex flex-col px-6 pb-2 overflow-y-auto bg-white shadow-xl grow gap-y-5 ring-1 ring-gray-900/10">
                        {{-- <div class="flex items-center h-16 shrink-0">
                            <img class="w-auto h-8" src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}">
                            <span class="ml-3 text-xl font-bold text-gray-900">SFS Admin</span>
                        </div> --}}
                        <nav class="flex flex-col flex-1">
                            @include('admin.partials.navigation')
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Static sidebar for desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            <div class="flex flex-col px-6 overflow-y-auto bg-white border-r border-gray-200 grow gap-y-5">
                {{-- <div class="flex items-center h-16 shrink-0">
                    <img class="w-auto h-8" src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}">
                    <span class="ml-3 text-xl font-bold text-gray-900">SFS Admin</span>
                </div> --}}
                <nav class="flex flex-col flex-1">
                    @include('admin.partials.navigation')
                </nav>
            </div>
        </div>

        <div class="lg:pl-72">
            <!-- Header -->
            <div class="sticky top-0 z-40 flex items-center h-16 px-4 bg-white border-b border-gray-200 shadow-sm shrink-0 gap-x-4 sm:gap-x-6 sm:px-6 lg:px-8">
                <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="sidebarOpen = true">
                    <span class="sr-only">Abrir sidebar</span>
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Separator -->
                <div class="w-px h-6 bg-gray-200 lg:hidden" aria-hidden="true"></div>

                <div class="flex self-stretch flex-1 gap-x-4 lg:gap-x-6">
                    <!-- Search -->
                    <form class="relative flex flex-1" action="#" method="GET">
                        <label for="search-field" class="sr-only">Buscar</label>
                        <svg class="absolute inset-y-0 left-0 w-5 h-full text-gray-400 pointer-events-none" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                        <input id="search-field" class="block w-full h-full py-0 pl-8 pr-0 text-gray-900 border-0 placeholder:text-gray-400 focus:ring-0 sm:text-sm" placeholder="Buscar empresas, usuários..." type="search" name="search">
                    </form>

                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <!-- Notifications -->
                        <button type="button" class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500" onclick="showNotifications()">
                            <span class="sr-only">Ver notificações</span>
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                            <!-- Notification badge -->
                            @if(session('admin_notifications_count', 0) > 0)
                            <span class="absolute flex w-3 h-3 -mt-2 -ml-2">
                                <span class="absolute inline-flex w-full h-full bg-red-400 rounded-full opacity-75 animate-ping"></span>
                                <span class="relative inline-flex w-3 h-3 bg-red-500 rounded-full"></span>
                            </span>
                            @endif
                        </button>

                        <!-- Separator -->
                        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true"></div>

                        <!-- Header Actions -->
                        <div class="flex items-center">
                            @hasSection('header-actions')
                                @yield('header-actions')
                            @endif
                        </div>

                        <!-- Profile dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" class="-m-1.5 flex items-center p-1.5" @click="open = !open">
                                <span class="sr-only">Abrir menu do usuário</span>
                                <img class="w-8 h-8 rounded-full bg-gray-50" src="https://ui-avatars.com/api/?background=3b82f6&color=fff&name={{ urlencode(auth()->user()->name) }}" alt="{{ auth()->user()->name }}">
                                <span class="hidden lg:flex lg:items-center">
                                    <span class="ml-4 text-sm font-semibold leading-6 text-gray-900">{{ auth()->user()->name }}</span>
                                    <svg class="w-5 h-5 ml-2 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none" style="display: none;">

                                @if(session('impersonate_admin'))
                                <a href="{{ route('admin.companies.stop-impersonation') }}" class="block px-3 py-1 text-sm leading-6 text-red-600 hover:bg-gray-50">
                                    Parar Impersonificação
                                </a>
                                <div class="border-t border-gray-100"></div>
                                @endif

                                {{-- <a href="{{ route('billing.dashboard') }}" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">
                                    Sistema Principal
                                </a> --}}

                                <a href="#" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">
                                    Perfil
                                </a>

                                <div class="border-t border-gray-100"></div>

                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="block w-full px-3 py-1 text-sm leading-6 text-left text-gray-900 hover:bg-gray-50">
                                        Sair
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <main class="py-10">
                <div class="px-4 sm:px-6 lg:px-8">
                    <!-- Flash Messages -->
                    @if(session('success'))
                    <div class="p-4 mb-6 rounded-md bg-green-50">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.23 10.71a.75.75 0 00-1.214.882l1.33 1.832a.75.75 0 001.096.074l3.865-5.407z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="p-4 mb-6 rounded-md bg-red-50">
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

                    @if(session('info'))
                    <div class="p-4 mb-6 rounded-md bg-blue-50">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="p-4 mb-6 rounded-md bg-red-50">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm text-red-700">
                                    <p class="mb-2 font-medium">Encontramos alguns erros:</p>
                                    <ul class="pl-5 space-y-1 list-disc">
                                        @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Page Content -->
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Notification Panel -->
    <div id="notificationPanel" class="fixed inset-y-0 right-0 z-50 transition-transform duration-300 ease-in-out transform translate-x-full bg-white shadow-xl w-96" style="display: none;">
        <div class="flex flex-col h-full">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Notificações</h2>
                <button onclick="hideNotifications()" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="flex-1 p-6 overflow-y-auto">
                <div id="notificationContent">
                    <!-- Notifications will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')

    <script>
        // Global functions
        function showNotifications() {
            const panel = document.getElementById('notificationPanel');
            panel.style.display = 'block';
            setTimeout(() => {
                panel.classList.remove('translate-x-full');
            }, 10);

            // Load notifications via AJAX
            loadNotifications();
        }

        function hideNotifications() {
            const panel = document.getElementById('notificationPanel');
            panel.classList.add('translate-x-full');
            setTimeout(() => {
                panel.style.display = 'none';
            }, 300);
        }

        function loadNotifications() {
            // Implementar carregamento de notificações
            const content = document.getElementById('notificationContent');
            content.innerHTML = `
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Nova empresa criada</p>
                            <p class="text-sm text-gray-500">TechStart Lda foi cadastrada no sistema</p>
                            <p class="mt-1 text-xs text-gray-400">2 minutos atrás</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-8 h-8 bg-yellow-100 rounded-full">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Trial expirando</p>
                            <p class="text-sm text-gray-500">3 empresas têm trial expirando em 24h</p>
                            <p class="mt-1 text-xs text-gray-400">1 hora atrás</p>
                        </div>
                    </div>
                </div>
            `;
        }

        // Close notifications when clicking outside
        document.addEventListener('click', function(e) {
            const panel = document.getElementById('notificationPanel');
            const trigger = e.target.closest('[onclick="showNotifications()"]');

            if (!panel.contains(e.target) && !trigger && !panel.classList.contains('translate-x-full')) {
                hideNotifications();
            }
        });
    </script>
</body>
</html>
