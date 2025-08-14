<?php

namespace App\Traits;
use App\Models\AdminActivity;
use App\Models\User;
use App\Models\Company;

use Illuminate\Support\Facades\Log;

trait LogsActivity
{
    /**
     * Log activity to Laravel logs
     */
    protected function logActivity(string $action, array $data = []): void
    {
        $user = auth()->user();

        $logData = [
            'action' => $action,
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'Guest',
            'user_email' => $user ? $user->email : null,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now()->toISOString(),
            'data' => $data
        ];

        Log::channel('activity')->info($action, $logData);
    }

    /**
     * Log admin activity
     */
    // protected function logAdminActivity(string $action, array $data = []): void
    // {
    //     $user = auth()->user();

    //     $logData = [
    //         'action' => $action,
    //         'admin_id' => $user ? $user->id : null,
    //         'admin_name' => $user ? $user->name : 'Unknown',
    //         'admin_email' => $user ? $user->email : null,
    //         'ip' => request()->ip(),
    //         'user_agent' => request()->userAgent(),
    //         'url' => request()->fullUrl(),
    //         'method' => request()->method(),
    //         'timestamp' => now()->toISOString(),
    //         'data' => $data
    //     ];

    //     Log::channel('admin')->info($action, $logData);
    // }

    /**
     * Log company activity
     */
    // protected function logCompanyActivity(string $action, array $data = []): void
    // {
    //     $user = auth()->user();
    //     $company = session('current_company');

    //     $logData = [
    //         'action' => $action,
    //         'user_id' => $user ? $user->id : null,
    //         'user_name' => $user ? $user->name : 'Unknown',
    //         'company_id' => $company ? $company->id : null,
    //         'company_name' => $company ? $company->name : null,
    //         'ip' => request()->ip(),
    //         'user_agent' => request()->userAgent(),
    //         'url' => request()->fullUrl(),
    //         'method' => request()->method(),
    //         'timestamp' => now()->toISOString(),
    //         'data' => $data
    //     ];

    //     Log::channel('company')->info($action, $logData);
    // }

    /**
     * Log de atividade geral do admin
     */
    protected function logAdminActivity(string $description, array $properties = [], string $severity = AdminActivity::SEVERITY_LOW, string $category = null): void
    {
        AdminActivity::log(
            $this->getActionFromMethod(),
            $description,
            $properties,
            $severity,
            $category
        );
    }

    /**
     * Log de ações relacionadas a usuários
     */
    protected function logUserActivity(string $action, User $user, string $description, array $properties = []): void
    {
        AdminActivity::logUserAction($action, $user, $description, $properties);
    }

    /**
     * Log de ações relacionadas a empresas
     */
    protected function logCompanyActivity(string $action, Company $company, string $description, array $properties = []): void
    {
        AdminActivity::logCompanyAction($action, $company, $description, $properties);
    }

    /**
     * Log de ações de segurança
     */
    protected function logSecurityActivity(string $description, array $properties = []): void
    {
        AdminActivity::logSecurityAction(
            $this->getActionFromMethod(),
            $description,
            $properties
        );
    }

    /**
     * Log de ações do sistema
     */
    protected function logSystemActivity(string $description, array $properties = []): void
    {
        AdminActivity::logSystemAction(
            $this->getActionFromMethod(),
            $description,
            $properties
        );
    }

    /**
     * Gerar nome da ação baseado no método atual
     */
    private function getActionFromMethod(): string
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);

        // Pegar o método que chamou o log
        $method = $backtrace[2]['function'] ?? 'unknown';
        $controller = class_basename($backtrace[2]['class'] ?? 'Unknown');

        return strtolower($controller) . '_' . $method;
    }

    /**
     * Log automático para ações CRUD
     */
    protected function logCrudAction(string $action, $model, array $additionalProperties = []): void
    {
        $modelName = class_basename($model);
        $modelId = $model->id ?? null;

        $description = match($action) {
            'created' => "Criou {$modelName} #{$modelId}",
            'updated' => "Atualizou {$modelName} #{$modelId}",
            'deleted' => "Deletou {$modelName} #{$modelId}",
            'viewed' => "Visualizou {$modelName} #{$modelId}",
            'exported' => "Exportou dados de {$modelName}",
            default => "Executou ação '{$action}' em {$modelName} #{$modelId}"
        };

        $severity = match($action) {
            'deleted' => AdminActivity::SEVERITY_HIGH,
            'created', 'updated' => AdminActivity::SEVERITY_MEDIUM,
            default => AdminActivity::SEVERITY_LOW
        };

        $category = match($modelName) {
            'User' => AdminActivity::CATEGORY_USER_MANAGEMENT,
            'Company' => AdminActivity::CATEGORY_COMPANY_MANAGEMENT,
            'Invoice' => AdminActivity::CATEGORY_INVOICE_MANAGEMENT,
            default => null
        };

        $properties = array_merge([
            'model_name' => $modelName,
            'model_id' => $modelId,
            'action' => $action,
        ], $additionalProperties);

        AdminActivity::log(
            $this->getActionFromMethod(),
            $description,
            $properties,
            $severity,
            $category,
            $model
        );
    }

    /**
     * Log para tentativas de acesso não autorizado
     */
    protected function logUnauthorizedAccess(string $attemptedAction, array $properties = []): void
    {
        AdminActivity::logSecurityAction(
            'unauthorized_access_attempt',
            "Tentativa de acesso não autorizado: {$attemptedAction}",
            array_merge($properties, [
                'attempted_action' => $attemptedAction,
                'timestamp' => now()->toISOString(),
            ])
        );
    }

    /**
     * Log para mudanças de dados sensíveis
     */
    protected function logSensitiveDataChange(string $dataType, array $oldValues, array $newValues, $model = null): void
    {
        AdminActivity::log(
            'sensitive_data_changed',
            "Alteração de dados sensíveis: {$dataType}",
            [
                'data_type' => $dataType,
                'changes_count' => count($newValues),
            ],
            AdminActivity::SEVERITY_HIGH,
            AdminActivity::CATEGORY_SECURITY,
            $model
        );
    }

    /**
     * Log para exportação de dados
     */
    protected function logDataExport(string $exportType, int $recordCount, array $filters = []): void
    {
        AdminActivity::log(
            'data_exported',
            "Exportou {$recordCount} registros de {$exportType}",
            [
                'export_type' => $exportType,
                'record_count' => $recordCount,
                'filters' => $filters,
                'format' => request('format', 'unknown'),
            ],
            AdminActivity::SEVERITY_MEDIUM,
            AdminActivity::CATEGORY_DATA_EXPORT
        );
    }

    /**
     * Log para login/logout de admins
     */
    protected function logAuthActivity(string $action, User $user): void
    {
        $descriptions = [
            'login' => 'Fez login no painel administrativo',
            'logout' => 'Fez logout do painel administrativo',
            'password_reset' => 'Resetou a senha',
            'profile_updated' => 'Atualizou o perfil',
        ];

        AdminActivity::logSecurityAction(
            "admin_{$action}",
            $descriptions[$action] ?? "Executou ação de autenticação: {$action}",
            [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'action' => $action,
            ]
        );
    }

    /**
     * Log para mudanças de configuração
     */
    protected function logConfigurationChange(string $setting, $oldValue, $newValue): void
    {
        AdminActivity::logSystemAction(
            'configuration_changed',
            "Alterou configuração: {$setting}",
            [
                'setting' => $setting,
                'old_value' => $oldValue,
                'new_value' => $newValue,
            ]
        );
    }
}
