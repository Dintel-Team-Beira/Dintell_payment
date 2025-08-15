<!-- Admin Navigation -->
<ul role="list" class="flex flex-col flex-1 gap-y-7">
    <li>
        <ul role="list" class="-mx-2 space-y-1">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('admin.dashboard') }}"
                   class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Dashboard
                </a>
            </li>

            <!-- Empresas -->
            <li>
                <div x-data="{ open: {{ request()->routeIs('admin.companies.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold {{ request()->routeIs('admin.companies.*') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.companies.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Empresas
                        <svg class="ml-auto h-5 w-5 shrink-0 {{ request()->routeIs('admin.companies.*') ? 'text-blue-600 rotate-90' : 'text-gray-400 group-hover:text-blue-600' }}"
                             :class="{ 'rotate-90': open }"
                             viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <ul x-show="open" x-transition class="px-2 mt-1 space-y-1">
                        <li>
                            <a href="{{ route('admin.companies.index') }}"
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.companies.index') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                                Listar Empresas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.companies.create') }}"
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.companies.create') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                                Nova Empresa
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Usuários -->
            <li>
                <a href="{{ route('admin.users.index') }}"
                   class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.users.*') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    Usuários
                </a>
            </li>

            <!-- Faturas Globais -->
            <li>
                <a href="{{ route('admin.invoices.index') }}"
                   class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.invoices.*') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.invoices.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    Faturas do Sistema
                </a>
            </li>

            <!-- Relatórios -->
            <li>
                <div x-data="{ open: {{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold {{ request()->routeIs('admin.reports.*') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.reports.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                        Relatórios
                        <svg class="ml-auto h-5 w-5 shrink-0 {{ request()->routeIs('admin.reports.*') ? 'text-blue-600 rotate-90' : 'text-gray-400 group-hover:text-blue-600' }}"
                             :class="{ 'rotate-90': open }"
                             viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <ul x-show="open" x-transition class="px-2 mt-1 space-y-1">
                        <li>
                            <a href="{{ route('admin.reports.revenue') }}"
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.reports.revenue') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                                Receita
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.reports.clients') }}"
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.reports.clients') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                                Clientes
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.reports.usage') }}"
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.reports.usage') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                                Uso do Sistema
                            </a>
                        </li>
                    </ul>
                </div>
            </li>


            <!-- Planos -->
            <li>
                <div x-data="{ open: {{ request()->routeIs('admin.plans.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold {{ request()->routeIs('admin.plans.*') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.plans.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Planos
                        <svg class="ml-auto h-5 w-5 shrink-0 {{ request()->routeIs('admin.plans.*') ? 'text-blue-600 rotate-90' : 'text-gray-400 group-hover:text-blue-600' }}"
                             :class="{ 'rotate-90': open }"
                             viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <ul x-show="open" x-transition class="px-2 mt-1 space-y-1">
                        <li>
                            <a href="{{ route('admin.plans.index') }}"
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.plans.index') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                                Listar Planos
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.plans.create') }}"
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.plans.create') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                                Novo Plano
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Configurações -->
            <li>
                <div x-data="{ open: {{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold {{ request()->routeIs('admin.settings.*') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.settings.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Configurações
                        <svg class="ml-auto h-5 w-5 shrink-0 {{ request()->routeIs('admin.settings.*') ? 'text-blue-600 rotate-90' : 'text-gray-400 group-hover:text-blue-600' }}"
                             :class="{ 'rotate-90': open }"
                             viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </button>
                      <ul x-show="open" x-transition class="px-2 mt-1 space-y-1">
                        {{-- <li>
                            <a href="{{ route('admin.settings.index') }}"
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.settings.index') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                                Dashboard
                            </a>
                        </li> --}}
                        <li>
                            <a href="{{ route('admin.settings.system') }}"
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.settings.system') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                                Sistema
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.billing') }}"
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.settings.billing') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                                Faturação
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.email.index') }}"
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.settings.email') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                                Email
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.backups') }}"
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.settings.backups') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                                Backups
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.security') }}"
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.settings.security') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                                Segurança
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </li>

    <!-- Seção de Monitoramento -->
    <li>
        <div class="text-xs font-semibold leading-6 text-gray-400">MONITORAMENTO</div>
        <ul role="list" class="mt-2 -mx-2 space-y-1">
            <li>
                <a href="{{ route('admin.logs.index') }}"
                   class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.logs.*') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.logs.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 18H9M1.5 8.25h7.5m-7.5 3.75h7.5m-7.5 3.75h7.5" />
                    </svg>
                    Logs do Sistema
                </a>
            </li>

            <li>
                <a href="{{ route('admin.monitoring.performance') }}"
                   class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.monitoring.*') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.monitoring.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l-1-3m1 3l-1-3m-16.5-3h16.5" />
                    </svg>
                    Performance
                </a>
            </li>

            <li>
                <a href="{{ route('admin.monitoring.health') }}"
                   class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.monitoring.health') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.monitoring.health') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg>
                    Health Check
                    <span class="ml-auto">
                        <span class="flex w-2 h-2">
                            <span class="absolute inline-flex w-2 h-2 bg-green-400 rounded-full opacity-75 animate-ping"></span>
                            <span class="relative inline-flex w-2 h-2 bg-green-500 rounded-full"></span>
                        </span>
                    </span>
                </a>
            </li>
        </ul>
    </li>

    <!-- Seção de Suporte -->
    <li class="mt-auto">
        <div class="text-xs font-semibold leading-6 text-gray-400">SUPORTE</div>
        <ul role="list" class="mt-2 -mx-2 space-y-1">
            <li>
   <!-- SISTEMA DE SUPORTE - NOVA SEÇÃO -->
            <li>
                <div x-data="{ open: {{ request()->routeIs('admin.support.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold {{ request()->routeIs('admin.support.*') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.support.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a9.027 9.027 0 011.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 00-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 010 9.424m-4.138-3.448l-3.448 4.138m3.448-4.138a9.014 9.014 0 01-9.424 0m4.138-3.448l-4.138-3.448M7.288 19.67l3.448-4.138M7.288 19.67a9.014 9.014 0 01-1.306-1.652m1.306 1.652l-1.306-1.652m0 0a9.027 9.027 0 01-1.652-1.306M4.33 16.712l4.138-3.448M4.33 16.712a9.014 9.014 0 000-9.424m4.138 3.448L4.33 7.288" />
                        </svg>
                        Suporte
                        <!-- Badge de notificação para tickets pendentes -->
                        @php
                            $pendingTickets = \App\Models\SupportTicket::whereIn('status', ['open', 'pending'])->count();
                        @endphp
                        @if($pendingTickets > 0)
                            <span class="inline-flex items-center px-2 py-1 ml-2 text-xs font-medium text-red-700 rounded-full bg-red-50 ring-1 ring-inset ring-red-600/10">
                                {{ $pendingTickets }}
                            </span>
                        @endif
                        <svg class="ml-auto h-5 w-5 shrink-0 {{ request()->routeIs('admin.support.*') ? 'text-blue-600 rotate-90' : 'text-gray-400 group-hover:text-blue-600' }}"
                             :class="{ 'rotate-90': open }"
                             viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <ul x-show="open" x-transition class="px-2 mt-1 space-y-1">
                        <li>
                            <a href="{{ route('admin.support.index') }}"
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.support.index') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.support.tickets') }}"
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.support.tickets*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                                Todos os Tickets
                                @if($pendingTickets > 0)
                                    <span class="ml-auto inline-flex items-center rounded-full bg-red-50 px-1.5 py-0.5 text-xs font-medium text-red-700">
                                        {{ $pendingTickets }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.support.reports') }}"
                               class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.support.reports') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Relatórios
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
 <!-- Seção de Ajuda e Suporte -->
    <li class="mt-auto">
        <div class="text-xs font-semibold leading-6 text-gray-400">AJUDA & DOCUMENTAÇÃO</div>
        <ul role="list" class="mt-2 -mx-2 space-y-1">
            <li>
                <a href="{{ route('admin.help.documentation') }}"
                   class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.help.documentation') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.help.documentation') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    Documentação
                </a>
            </li>

            <li>
                <a href="{{ route('admin.help.api-docs') }}"
                   class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.help.api-docs') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.help.api-docs') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
                    </svg>
                    API Docs
                </a>
            </li>

            <li>
                <a href="{{ route('admin.help.changelog') }}"
                   class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.help.changelog') ? 'bg-gray-50 text-blue-600' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' }}">
                    <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.help.changelog') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    Changelog
                </a>
            </li>
        </ul>
    </li>
</ul>

<!-- Status do Sistema -->
<div class="p-4 mt-6 rounded-lg bg-gray-50">
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
</div>
