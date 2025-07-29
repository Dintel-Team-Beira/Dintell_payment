<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Login - {{ config('app.name', 'SFS Admin') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="flex flex-col justify-center min-h-full py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Logo -->
            <div class="flex justify-center">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-600 rounded-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-2xl font-bold text-gray-900">SFS Admin</h1>
                    </div>
                </div>
            </div>

            <h2 class="mt-6 text-3xl font-bold tracking-tight text-center text-gray-900">
                Acesso Administrativo
            </h2>
            <p class="mt-2 text-sm text-center text-gray-600">
                Faça login para acessar o painel administrativo do SaaS
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="px-4 py-8 bg-white shadow sm:rounded-lg sm:px-10">

                <!-- Flash Messages -->
                @if(session('success'))
                <div class="p-4 mb-4 rounded-md bg-green-50">
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
                <div class="p-4 mb-4 rounded-md bg-red-50">
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

                <form class="space-y-6" method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                            Email
                        </label>
                        <div class="mt-2">
                            <input id="email"
                                   name="email"
                                   type="email"
                                   autocomplete="email"
                                   required
                                   value="arnaldo.tomo@dintell.co.mz"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 @error('email') ring-red-500 @enderror"
                                   placeholder="seu.email@empresa.com">
                        </div>
                        @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                            Senha
                        </label>
                        <div class="mt-2">
                            <input id="password"
                                   name="password"
                                   type="password"
                                   value="Admin@123"
                                   autocomplete="current-password"
                                   required
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 @error('password') ring-red-500 @enderror"
                                   placeholder="••••••••">
                        </div>
                        @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember"
                                   name="remember"
                                   type="checkbox"
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-600">
                            <label for="remember" class="block ml-3 text-sm leading-6 text-gray-900">
                                Lembrar-me
                            </label>
                        </div>

                        <div class="text-sm leading-6">
                            <a href="#" class="font-semibold text-blue-600 hover:text-blue-500">
                                Esqueceu a senha?
                            </a>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                                class="flex w-full justify-center rounded-md bg-blue-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
                                id="loginButton">
                            <span class="login-text">Entrar</span>
                            <svg class="hidden w-5 h-5 mr-3 -ml-1 text-white login-spinner animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Divider -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300" />
                        </div>
                        <div class="relative flex justify-center text-sm font-medium leading-6">
                            <span class="px-6 text-gray-900 bg-white">Ou</span>
                        </div>
                    </div>
                </div>

                <!-- Link para área do cliente -->
                <div class="mt-6">
                    <a href="{{ route('login') }}"
                       class="flex w-full justify-center rounded-md bg-white px-3 py-1.5 text-sm font-semibold leading-6 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Acessar como Cliente
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-500">
                    {{ config('app.name') }} Admin Panel v{{ config('app.version', '1.0') }}
                </p>
                <p class="mt-1 text-xs text-gray-500">
                    © {{ date('Y') }} Todos os direitos reservados
                </p>
            </div>
        </div>
    </div>

    <script>
        // Loading state no formulário
        document.querySelector('form').addEventListener('submit', function() {
            const button = document.getElementById('loginButton');
            const text = button.querySelector('.login-text');
            const spinner = button.querySelector('.login-spinner');

            button.disabled = true;
            text.textContent = 'Entrando...';
            spinner.classList.remove('hidden');
        });

        // Auto-focus no campo email
        document.getElementById('email').focus();

        // Validação em tempo real
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        emailInput.addEventListener('blur', function() {
            if (this.value && !this.value.includes('@')) {
                this.classList.add('ring-red-500');
            } else {
                this.classList.remove('ring-red-500');
            }
        });

        passwordInput.addEventListener('input', function() {
            if (this.value.length > 0 && this.value.length < 6) {
                this.classList.add('ring-yellow-500');
                this.classList.remove('ring-red-500');
            } else if (this.value.length >= 6) {
                this.classList.remove('ring-yellow-500', 'ring-red-500');
                this.classList.add('ring-green-500');
            } else {
                this.classList.remove('ring-yellow-500', 'ring-green-500');
            }
        });
    </script>
</body>
</html>
