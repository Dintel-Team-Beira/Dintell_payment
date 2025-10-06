<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantSlug = $request->route('tenant');

        // dd($tenantSlug);
        if (!$tenantSlug) {
            // Se o parâmetro {tenant} não existir, pode ser um erro de rota
            abort(404);
        }
        $company = Company::where('slug', $tenantSlug)->first();

         if (!$company) {
            abort(404, 'Empresa não encontrada.');
        }
        app()->instance('tenant', $company);

        URL::defaults([
            'tenant' => $company->slug
        ]);
        return $next($request);
    }
}
