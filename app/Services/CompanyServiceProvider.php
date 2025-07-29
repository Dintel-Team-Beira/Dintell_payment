<?php

// 1. Service Provider atualizado
// app/Providers/CompanyServiceProvider.php

namespace App\Providers;

use App\Helpers\CompanyHelper;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class CompanyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('company', function () {
            return new CompanyHelper();
        });
    }

    public function boot()
    {
        // Diretiva para gerar rotas com slug da empresa
        Blade::directive('companyRoute', function ($expression) {
            return "<?php echo app('company')->route($expression); ?>";
        });

        // Diretiva para obter empresa atual
        Blade::directive('currentCompany', function () {
            return "<?php echo app('company')->current(); ?>";
        });

        // Diretiva para gerar URLs com slug
        Blade::directive('companyUrl', function ($expression) {
            return "<?php echo app('company')->url($expression); ?>";
        });

        // Verificar se há empresa no contexto
        Blade::if('hasCompany', function () {
            return CompanyHelper::hasCompany();
        });

        // Verificar se é uma rota de empresa (com slug)
        Blade::if('isCompanyRoute', function () {
            return request()->route('company_slug') !== null;
        });
    }
}
