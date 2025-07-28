<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the admin login form
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $this->checkTooManyFailedAttempts($request);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        // Tentar autenticar
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Verificar se é super admin
            if (!$user->is_super_admin) {
                Auth::logout();

                RateLimiter::hit($this->throttleKey($request));

                throw ValidationException::withMessages([
                    'email' => 'Credenciais inválidas para acesso administrativo.',
                ]);
            }

            // Limpar tentativas de login
            RateLimiter::clear($this->throttleKey($request));

            // Regenerar sessão
            $request->session()->regenerate();

            // Registrar login
            $user->update([
                'last_login_at' => now(),
                'last_activity_at' => now(),
                'login_ip' => $request->ip(),
            ]);

            // Log da atividade (usando Log facade em vez do activity)
            Log::info('Admin login realizado', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString()
            ]);

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', "Bem-vindo de volta, {$user->name}!");
        }

        // Login falhou
        RateLimiter::hit($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => 'As credenciais fornecidas não coincidem com nossos registros.',
        ]);
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Log da atividade
        if ($user) {
            Log::info('Admin logout realizado', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'ip' => $request->ip(),
                'timestamp' => now()->toISOString()
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Logout realizado com sucesso.');
    }

    /**
     * Check for too many failed attempts
     */
    protected function checkTooManyFailedAttempts(Request $request)
    {
        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => "Muitas tentativas de login. Tente novamente em {$seconds} segundos.",
            ]);
        }
    }

    /**
     * Get the throttle key for the given request
     */
    protected function throttleKey(Request $request): string
    {
        return Str::lower($request->input('email')).'|'.$request->ip();
    }
}
