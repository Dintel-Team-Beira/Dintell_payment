<nav class="bg-white border-b border-gray-200 shadow-sm">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo da empresa -->
                <div class="flex items-center flex-shrink-0">
                    @if($currentCompany ?? null)
                        @if($currentCompany->logo_url)
                            <img class="w-auto h-8" src="{{ $currentCompany->logo_url }}" alt="{{ $currentCompany->name }}">
                        @else
                            <h2 class="text-xl font-bold text-gray-900">{{ $currentCompany->name }}</h2>
                        @endif
                    @else
                        <h2 class="text-xl font-bold text-gray-900">SFS</h2>
                    @endif
                </div>

                <!-- Menu principal -->
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    @isCompanyRoute
                        {{-- URLs com slug da empresa --}}
                        <a href="@companyRoute('dashboard')"
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900 border-b-2 border-indigo-500">
                            Dashboard
                        </a>

                        <a href="@companyRoute('clients.index')"
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700">
                            Clientes
                        </a>

                        <a href="@companyRoute('products.index')"
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700">
                            Produtos
                        </a>

                        <a href="@companyRoute('services.index')"
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700">
                            Serviços
                        </a>

                        <a href="@companyRoute('quotes.index')"
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700">
                            Cotações
                        </a>

                        <a href="@companyRoute('invoices.index')"
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700">
                            Faturas
                        </a>
                    @else
                        {{-- URLs legadas (sem slug) --}}
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900 border-b-2 border-indigo-500">
                            Dashboard
                        </a>

                        <a href="{{ route('clients.index') }}"
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700">
                            Clientes
                        </a>

                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700">
                            Produtos
                        </a>

                        <a href="{{ route('services.index') }}"
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700">
                            Serviços
                        </a>

                        <a href="{{ route('quotes.index') }}"
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700">
                            Cotações
                        </a>

                        <a href="{{ route('invoices.index') }}"
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700">
                            Faturas
                        </a>
                    @endisCompanyRoute
                </div>
            </div>

            <!-- Menu do usuário -->
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                @if($currentCompany ?? null)
                    <div class="relative ml-3">
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="mr-2">{{ $currentCompany->name }}</span>
                            @isCompanyRoute
                                <a href="@companyRoute('settings.index')" class="text-gray-400 hover:text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </a>
                            @else
                                <a href="{{ route('settings.index') }}" class="text-gray-400 hover:text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </a>
                            @endisCompanyRoute
                        </div>
                    </div>
                @endif

                <!-- Botão para mudar de empresa (se aplicável) -->
                @auth
                    @if(auth()->user()->is_super_admin)
                        <div class="ml-3">
                            <a href="{{ route('admin.dashboard') }}"
                               class="text-sm text-blue-600 hover:text-blue-800">
                                Admin
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>
