<?php

if (!function_exists('company_route')) {
    /**
     * Gerar URL com slug da empresa atual
     */
    function company_route($name, $parameters = [], $absolute = true)
    {
        $user = auth()->user();

        if ($user && !$user->is_super_admin && $user->company_id) {
            $company = \App\Models\Company::find($user->company_id);
            if ($company && $company->slug) {
                // Adicionar o slug da empresa aos parâmetros
                $parameters = array_merge(['company_slug' => $company->slug], $parameters);
            }
        }

        return route($name, $parameters, $absolute);
    }
}

if (!function_exists('current_company')) {
    /**
     * Obter empresa atual do contexto
     */
    function current_company()
    {
        return config('app.current_company') ?? request()->attributes->get('company');
    }
}

if (!function_exists('company_url')) {
    /**
     * Gerar URL completa com slug da empresa
     */
    function company_url($path = '', $company = null)
    {
        if (!$company) {
            $company = current_company();
        }

        if (!$company) {
            $user = auth()->user();
            if ($user && !$user->is_super_admin && $user->company_id) {
                $company = \App\Models\Company::find($user->company_id);
            }
        }

        if ($company && $company->slug) {
            $path = ltrim($path, '/');
            return url("/{$company->slug}/{$path}");
        }

        return url($path);
    }
}
