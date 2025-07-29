<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Config;

class CompanyHelper
{
    /**
     * Gerar URL com slug da empresa atual se disponível
     */
    public static function route($name, $parameters = [], $absolute = true)
    {
        $company = Config::get('app.current_company');
        $companySlug = request()->route('company_slug');

        // Se estamos em uma rota com slug, manter o slug
        if ($companySlug && $company && $company->slug === $companySlug) {
            $parameters = array_merge(['company_slug' => $companySlug], $parameters);
        }

        return route($name, $parameters, $absolute);
    }

    /**
     * Obter empresa atual
     */
    public static function current()
    {
        return Config::get('app.current_company');
    }

    /**
     * Verificar se há empresa no contexto
     */
    public static function hasCompany()
    {
        return Config::get('app.current_company') !== null;
    }

    /**
     * Gerar URL direta (com ou sem slug baseado no contexto atual)
     */
    public static function url($path = '/')
    {
        $companySlug = request()->route('company_slug');

        if ($companySlug) {
            return url("/{$companySlug}" . ($path !== '/' ? '/' . ltrim($path, '/') : ''));
        }

        return url($path);
    }

    /**
     * Verificar se estamos em uma rota com slug
     */
    public static function isCompanyRoute()
    {
        return request()->route('company_slug') !== null;
    }
}
