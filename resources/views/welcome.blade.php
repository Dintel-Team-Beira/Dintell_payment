<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-screen p-4 bg-gradient-to-br from-gray-50 to-gray-100">
        <!-- Logo Central -->
        <div class="mb-8 text-center">
            <div class="flex items-center justify-center w-24 h-24 mx-auto mb-4 bg-indigo-600 rounded-full shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">SubTrack</h1>
            <p class="mt-1 text-gray-500">Gestão de Subscrições</p>
        </div>

        <!-- Card de Login -->
        <div class="w-full max-w-md p-8 bg-white shadow-sm rounded-xl">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email')" class="sr-only" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </div>
                        <x-text-input
                            id="email"
                            class="block w-full pl-10"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Endereço de email" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <x-input-label for="password" :value="__('Password')" class="sr-only" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <x-text-input
                            id="password"
                            class="block w-full pl-10"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Palavra-passe" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between mb-6">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="text-indigo-600 border-gray-300 rounded shadow-sm focus:ring-indigo-500" name="remember">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Lembrar-me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-indigo-600 hover:text-indigo-500 focus:outline-none" href="{{ route('password.request') }}">
                            {{ __('Esqueceu a palavra-passe?') }}
                        </a>
                    @endif
                </div>

                <!-- Login Button -->
                <button type="submit" class="flex items-center justify-center w-full px-4 py-3 font-medium text-white transition duration-200 bg-indigo-600 rounded-lg hover:bg-indigo-700">
                    <span>{{ __('Entrar') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Footer Links -->
        <div class="mt-8 text-sm text-center text-gray-500">
            <p>Novo no sistema? <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Crie uma conta</a></p>
        </div>
    </div>
</x-guest-layout>