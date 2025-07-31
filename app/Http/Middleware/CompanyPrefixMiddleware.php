<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use App\Models\Company;
class CompanyPrefixMiddleware
{
  public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->company_id) {
            // Redirecionar para criação de empresa
            return redirect()->route('company.create')
                ->with('warning', 'Você precisa configurar uma empresa primeiro.');
        }

        $company = \App\Models\Company::find($user->company_id);

        if (!$company) {
            // Company foi deletada ou não existe
            $user->update(['company_id' => null]);
            return redirect()->route('company.create')
                ->with('error', 'Empresa não encontrada. Configure uma nova empresa.');
        }

        // Compartilhar company para toda a aplicação
        view()->share('currentCompany', $company);

        return $next($request);
    }
}
