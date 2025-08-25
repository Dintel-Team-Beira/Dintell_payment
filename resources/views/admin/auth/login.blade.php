<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SFS Admin - Sistema de Faturação e Subscrição</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ed 100%);
        }

        .login-card {
            transition: all 0.3s ease;
            transform: translateY(0);
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .input-field {
            transition: all 0.3s ease;
        }

        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .btn-login {
            transition: all 0.3s ease;
            background-size: 200% auto;
            background-image: linear-gradient(to right, #1e40af 0%, #1e40af 51%, #1d4ed8 100%);
        }

        .btn-login:hover {
            background-position: right center;
            transform: translateY(-2px);
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-sm login-card animate__animated animate__fadeIn">

        <!-- Logo -->
        <div class="mb-8 text-center animate__animated animate__fadeInDown">

           <img src="{{ asset('main.webp') }}" alt="Dintell Lgo" class="">
        </div>

        <!-- Mensagens de Status/Sessão -->
        @if(session('success'))
            <div class="p-4 mb-6 text-green-700 bg-green-100 rounded-lg animate__animated animate__fadeIn">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.23 10.71a.75.75 0 00-1.214.882l1.33 1.832a.75.75 0 001.096.074l3.865-5.407z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg animate__animated animate__fadeIn">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Formulário -->
        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5" id="adminLoginForm">
            @csrf

            <!-- Email -->
            <div class="animate__animated animate__fadeIn animate__delay-1s">
                <label for="email" class="block mb-1 text-sm font-medium text-gray-700">
                    Email de Administrador
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="arnaldo.tomo@dintell.co.mz"
                    required
                    autofocus
                    autocomplete="email"
                    class="w-full px-4 py-3 transition border border-gray-300 rounded-lg outline-none input-field focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                    placeholder="admin@sfs.co.mz">
                @error('email')
                    <p class="mt-1 text-sm text-red-600 animate__animated animate__fadeIn">{{ $message }}</p>
                @enderror
            </div>

            <!-- Senha -->
            <div class="animate__animated animate__fadeIn animate__delay-2s">
                <label for="password" class="block mb-1 text-sm font-medium text-gray-700">
                    Senha
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    value="Admin@123"
                    required
                    autocomplete="current-password"
                    class="w-full px-4 py-3 transition border border-gray-300 rounded-lg outline-none input-field focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                    placeholder="Digite sua senha">
                @error('password')
                    <p class="mt-1 text-sm text-red-600 animate__animated animate__fadeIn">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lembrar de mim -->
            <div class="flex items-center justify-between animate__animated animate__fadeIn animate__delay-2s">
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="remember" class="block ml-2 text-sm text-gray-700">
                        Lembrar de mim
                    </label>
                </div>


            </div>

            <!-- Botão de Login -->
            <div class="animate__animated animate__fadeInUp animate__delay-2s">
                <button
                    type="submit"
                    id="loginButton"
                    class="w-full px-4 py-3 font-medium text-white transition duration-200 rounded-lg btn-login hover:shadow-md disabled:opacity-70 disabled:cursor-not-allowed">
                    <span id="buttonText">Acessar Painel Administrativo</span>
                    <div id="buttonLoading" class="flex items-center justify-center hidden">
                        <div class="mr-2 loading-spinner"></div>
                        Autenticando...
                    </div>
                </button>
            </div>
        </form>

        <!-- Divider -->
        <div class="mt-6 animate__animated animate__fadeIn animate__delay-3s">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-6 text-gray-900 bg-white">Ou</span>
                </div>
            </div>
        </div>

        <!-- Link para área do cliente -->
        <div class="mt-6 animate__animated animate__fadeIn animate__delay-3s">
            <a href="{{ route('login') }}"
               class="flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-gray-900 transition-colors duration-200 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Acessar como Cliente
            </a>
        </div>

        <!-- Rodapé -->
        <div class="mt-6 text-xs text-center text-gray-500 animate__animated animate__fadeIn animate__delay-4s">
            <p class="mb-1">
                Ao continuar, você confirma que leu nossos
                <a href="#" class="text-blue-600 hover:underline">Termos & Condições</a>
                e
                <a href="#" class="text-blue-600 hover:underline">Política de Privacidade</a>
            </p>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('adminLoginForm');
            const emailField = document.getElementById('email');
            const passwordField = document.getElementById('password');
            const loginButton = document.getElementById('loginButton');
            const buttonText = document.getElementById('buttonText');
            const buttonLoading = document.getElementById('buttonLoading');

            // Efeito de digitação no campo de email (similar ao exemplo)
            if (emailField.value === '') {
                const emailText = 'admin@sfs.co.mz';
                let i = 0;
                const typingEffect = setInterval(() => {
                    if (i < emailText.length) {
                        emailField.value += emailText.charAt(i);
                        i++;
                    } else {
                        clearInterval(typingEffect);
                    }
                }, 100);
            }

            // Submissão do formulário com loading state
            form.addEventListener('submit', function(e) {
                // Mostrar loading state
                loginButton.disabled = true;
                buttonText.classList.add('hidden');
                buttonLoading.classList.remove('hidden');
                buttonLoading.classList.add('flex');

                // Em um cenário real, o formulário seria enviado normalmente
                // Para demonstração, vou simular um delay
                setTimeout(() => {
                    // Resetar o botão após um tempo (para demo)
                    loginButton.disabled = false;
                    buttonText.classList.remove('hidden');
                    buttonLoading.classList.add('hidden');
                    buttonLoading.classList.remove('flex');
                }, 3000);
            });

            // Validação visual em tempo real
            emailField.addEventListener('blur', function() {
                if (this.value && !this.value.includes('@')) {
                    this.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                    this.classList.remove('border-gray-300', 'focus:ring-blue-500', 'focus:border-blue-500');
                } else {
                    this.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                    this.classList.add('border-gray-300', 'focus:ring-blue-500', 'focus:border-blue-500');
                }
            });

            passwordField.addEventListener('input', function() {
                if (this.value.length > 0 && this.value.length < 6) {
                    this.classList.add('border-yellow-500', 'focus:ring-yellow-500', 'focus:border-yellow-500');
                    this.classList.remove('border-gray-300', 'focus:ring-blue-500', 'focus:border-blue-500', 'border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                } else if (this.value.length >= 6) {
                    this.classList.remove('border-yellow-500', 'focus:ring-yellow-500', 'focus:border-yellow-500', 'border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                    this.classList.add('border-green-500', 'focus:ring-green-500', 'focus:border-green-500');
                } else {
                    this.classList.remove('border-yellow-500', 'focus:ring-yellow-500', 'focus:border-yellow-500', 'border-green-500', 'focus:ring-green-500', 'focus:border-green-500');
                    this.classList.add('border-gray-300', 'focus:ring-blue-500', 'focus:border-blue-500');
                }
            });

            // Auto-focus no campo email
            emailField.focus();

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + Enter para submeter
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    e.preventDefault();
                    form.dispatchEvent(new Event('submit'));
                }
            });

            // Remover mensagens de erro quando o usuário começar a digitar
            [emailField, passwordField].forEach(field => {
                field.addEventListener('input', function() {
                    const errorMessage = this.parentElement.querySelector('.text-red-600');
                    if (errorMessage) {
                        errorMessage.style.opacity = '0';
                        setTimeout(() => {
                            if (errorMessage) {
                                errorMessage.remove();
                            }
                        }, 300);
                    }
                });
            });

            console.log('🔐 SFS Admin Login - Sub360 Style Loaded');
            console.log('📧 Default credentials: arnaldo.tomo@dintell.co.mz');
        });
    </script>
</body>
</html>
