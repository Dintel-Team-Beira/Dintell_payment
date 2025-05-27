<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | TerraMar Logística</title>
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
            background-image: linear-gradient(to right, #0c2572 0%, #0c2572 51%, #0c2572 100%);
        }
        .btn-login:hover {
            background-position: right center;
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-sm login-card animate__animated animate__fadeIn">
        <!-- Logo -->
        <div class="mb-8 text-center animate__animated animate__fadeInDown">
            <img src="{{ asset('logo.png') }}" alt="Dintell Lgo" class="">

        </div>

        <!-- Mensagens de Status/Sessão -->
        @if(session('status'))
            <div class="p-4 mb-6 text-green-700 bg-green-100 rounded-lg animate__animated animate__fadeIn">
                {{ session('status') }}
            </div>
        @endif

        <!-- Formulário -->
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email -->
            <div class="animate__animated animate__fadeIn animate__delay-1s">
                <label for="email" class="block mb-1 text-sm font-medium text-gray-700">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    class="w-full px-4 py-3 transition border border-gray-300 rounded-lg outline-none input-field focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Digite seu e-mail">
                @error('email')
                    <p class="mt-1 text-sm text-red-600 animate__animated animate__fadeIn">{{ $message }}</p>
                @enderror
            </div>

            <!-- Senha -->
            <div class="animate__animated animate__fadeIn animate__delay-2s">
                <label for="password" class="block mb-1 text-sm font-medium text-gray-700">Senha</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full px-4 py-3 transition border border-gray-300 rounded-lg outline-none input-field focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Digite sua senha">
                @error('password')
                    <p class="mt-1 text-sm text-red-600 animate__animated animate__fadeIn">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lembrar de mim e Esqueci a senha -->
            <div class="flex items-center justify-between animate__animated animate__fadeIn animate__delay-2s">
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="remember" class="block ml-2 text-sm text-gray-700">Lembrar de mim</label>
                </div>

            </div>

            <!-- Botão de Login -->
            <div class="animate__animated animate__fadeInUp animate__delay-2s">
                <button
                    type="submit"
                    class="w-full px-4 py-3 font-medium text-white transition duration-200 rounded-lg btn-login hover:shadow-md">
                    Entrar
                </button>
            </div>
        </form>

        <!-- Rodapé -->
        <div class="mt-6 text-xs text-center text-gray-500 animate__animated animate__fadeIn animate__delay-3s">
            <p>Ao continuar, você confirma que leu nossos <a href="#" class="text-blue-600 hover:underline">Termos & Condições</a> e <a href="#" class="text-blue-600 hover:underline">Política de Cookies</a></p>
        </div>
    </div>

    <script>
        // Efeito de digitação no campo de email
        document.addEventListener('DOMContentLoaded', function() {
            const emailField = document.getElementById('email');
            if (emailField && emailField.value === '') {
                const emailText = '@dintell.co.mz';
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
        });
    </script>
</body>
</html>