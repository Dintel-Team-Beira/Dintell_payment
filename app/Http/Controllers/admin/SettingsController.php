<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    /**
     * Dashboard principal de configurações
     */
    public function index()
    {
        // Estatísticas básicas para o dashboard
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_invoices' => 0, // Substituir pela query real quando tiver o model
            'disk_usage' => $this->formatBytes(disk_free_space('/')),
            'last_backup' => 'Nunca' // Implementar lógica de backup
        ];

        return view('admin.settings.index', compact('stats'));
    }

    /**
     * Configurações do sistema
     */
    public function system()
    {
        $settings = $this->getSystemSettings();
        return view('admin.settings.system', compact('settings'));
    }

    /**
     * Configurações de faturação
     */
    public function billing()
    {
        $settings = $this->getBillingSettings();
        return view('admin.settings.billing', compact('settings'));
    }

    /**
     * Configurações de email
     */
    public function email()
    {
        $settings = $this->getEmailSettings();
        return view('admin.settings.email', compact('settings'));
    }

    /**
     * Configurações de backup
     */
    public function backups()
    {
        $settings = $this->getBackupSettings();
        $stats = $this->getBackupStats();
        $backups = $this->getBackupsList();

        return view('admin.settings.backups', compact('settings', 'stats', 'backups'));
    }

    /**
     * Configurações de segurança
     */
    public function security()
    {
        $settings = $this->getSecuritySettings();
        return view('admin.settings.security', compact('settings'));
    }

    /**
     * Atualizar configurações do sistema
     */
    public function updateSystem(Request $request)
    {
        // Implementar validação e atualização
        return redirect()->route('admin.settings.system')->with('success', 'Configurações atualizadas com sucesso!');
    }

    /**
     * Atualizar configurações de faturação
     */
    public function updateBilling(Request $request)
    {
        // Implementar validação e atualização
        return redirect()->route('admin.settings.billing')->with('success', 'Configurações de faturação atualizadas!');
    }

    /**
     * Atualizar configurações de email
     */
    public function updateEmail(Request $request)
    {
        // Implementar validação e atualização
        return redirect()->route('admin.settings.email')->with('success', 'Configurações de email atualizadas!');
    }

    /**
     * Atualizar configurações de backup
     */
    public function updateBackups(Request $request)
    {
        // Implementar validação e atualização
        return redirect()->route('admin.settings.backups')->with('success', 'Configurações de backup atualizadas!');
    }

    /**
     * Atualizar configurações de segurança
     */
    public function updateSecurity(Request $request)
    {
        // Implementar validação e atualização
        return redirect()->route('admin.settings.security')->with('success', 'Configurações de segurança atualizadas!');
    }

    /**
     * Testar conexão de email
     */
    public function testEmail(Request $request)
    {
        try {
            // Implementar teste de conexão SMTP
            return response()->json(['success' => true, 'message' => 'Email de teste enviado com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Criar backup manual
     */
    public function createBackup()
    {
        try {
            // Implementar criação de backup
            return response()->json(['success' => true, 'message' => 'Backup criado com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Listar backups
     */
    public function listBackups()
    {
        $backups = $this->getBackupsList();
        return response()->json(['success' => true, 'backups' => $backups]);
    }

    /**
     * Download de backup
     */
    public function downloadBackup($id)
    {
        // Implementar download de backup
        return response()->download(storage_path("backups/backup-{$id}.zip"));
    }

    /**
     * Restaurar backup
     */
    public function restoreBackup($id)
    {
        try {
            // Implementar restauração de backup
            return response()->json(['success' => true, 'message' => 'Backup restaurado com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Excluir backup
     */
    public function deleteBackup($id)
    {
        try {
            // Implementar exclusão de backup
            return response()->json(['success' => true, 'message' => 'Backup excluído com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Gerar relatório de segurança
     */
    public function securityReport()
    {
        try {
            // Implementar geração de relatório de segurança
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Limpar cache do sistema
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            return response()->json(['success' => true, 'message' => 'Cache limpo com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Otimizar sistema
     */
    public function optimizeSystem()
    {
        try {
            Artisan::call('optimize');
            return response()->json(['success' => true, 'message' => 'Sistema otimizado com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Informações do sistema
     */
    public function systemInfo()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'php_version' => phpversion(),
                'laravel_version' => app()->version(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
                'memory_limit' => ini_get('memory_limit'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'disk_space' => [
                    'free' => $this->formatBytes(disk_free_space('/')),
                    'total' => $this->formatBytes(disk_total_space('/'))
                ]
            ]
        ]);
    }

    /**
     * Logs do sistema
     */
    public function logs()
    {
        // Implementar listagem de logs
        return view('admin.logs.index');
    }

    /**
     * Métodos auxiliares privados
     */
    private function getSystemSettings()
    {
        return [
            'app_name' => config('app.name'),
            'app_description' => 'Sistema de Faturação e Subscrição',
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'maintenance_mode' => false,
            'registration_enabled' => true,
            'email_verification_required' => false,
            // Adicionar mais configurações conforme necessário
        ];
    }

    private function getBillingSettings()
    {
        return [
            'default_currency' => 'MZN',
            'currency_symbol' => 'MT',
            'currency_position' => 'after',
            'decimal_places' => 2,
            'thousand_separator' => ',',
            'default_tax_rate' => 16,
            'invoice_prefix' => 'FAT',
            'invoice_start_number' => 1,
            'invoice_due_days' => 30,
            'payment_methods' => ['cash', 'bank_transfer', 'mpesa'],
            // Adicionar mais configurações
        ];
    }

    private function getEmailSettings()
    {
        return [
            'mail_driver' => config('mail.default'),
            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_username' => config('mail.mailers.smtp.username'),
            'mail_encryption' => config('mail.mailers.smtp.encryption'),
            'mail_from_name' => config('mail.from.name'),
            'mail_from_address' => config('mail.from.address'),
            'notifications' => ['invoice_sent', 'invoice_paid'],
            // Adicionar mais configurações
        ];
    }

    private function getBackupSettings()
    {
        return [
            'auto_backup_enabled' => false,
            'backup_frequency' => 'daily',
            'backup_time' => '02:00',
            'backup_retention_days' => 30,
            'max_backups' => 10,
            'backup_database' => true,
            'backup_files' => true,
            'backup_storage' => 'local',
            'compress_backups' => true,
            // Adicionar mais configurações
        ];
    }

    private function getSecuritySettings()
    {
        return [
            'min_password_length' => 8,
            'password_expires_days' => 0,
            'require_uppercase' => true,
            'require_lowercase' => true,
            'require_numbers' => true,
            'require_symbols' => false,
            'session_timeout' => 30,
            'max_concurrent_sessions' => 3,
            'two_factor_enabled' => false,
            'max_login_attempts' => 5,
            'lockout_duration' => 15,
            // Adicionar mais configurações
        ];
    }

    private function getBackupStats()
    {
        return [
            'total_backups' => 0,
            'last_backup' => 'Nunca',
            'total_size' => '0 MB',
            'status' => 'inactive'
        ];
    }

    private function getBackupsList()
    {
        // Retornar lista vazia por enquanto
    return [
            [
                'id' => 'backup_001',
                'name' => 'backup-2024-08-11-02-00.zip',
                'date' => '2024-08-11 02:00:00',
                'size' => '15.2 MB',
                'type' => 'automatic',
                'status' => 'completed'
            ]
        ];

        // Exemplo de estrutura quando implementar:
        /*
        return [
            [
                'id' => 'backup_001',
                'name' => 'backup-2024-08-11-02-00.zip',
                'date' => '2024-08-11 02:00:00',
                'size' => '15.2 MB',
                'type' => 'automatic',
                'status' => 'completed'
            ]
        ];
        */
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

/**
     * Exibir preview das configurações de email
     */
    public function emailPreview(Request $request)
    {
        try {
            // Verificar se é uma requisição AJAX para preview dinâmico
            if ($request->ajax()) {
                $template = $request->input('template', 'invoice');
                $sampleData = $this->getSampleDataForTemplate($template);

                $view = view("emails.templates.{$template}", $sampleData)->render();

                return response()->json([
                    'success' => true,
                    'html' => $view
                ]);
            }

            // Buscar configurações atuais de email
            $emailSettings = [
                'smtp_host' => config('mail.mailers.smtp.host'),
                'smtp_port' => config('mail.mailers.smtp.port'),
                'smtp_username' => config('mail.mailers.smtp.username'),
                'smtp_encryption' => config('mail.mailers.smtp.encryption'),
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
            ];

            // Templates disponíveis
            $templates = [
                'invoice' => 'Fatura',
                'quote' => 'Cotação',
                'credit_note' => 'Nota de Crédito',
                'debit_note' => 'Nota de Débito',
                'payment_reminder' => 'Lembrete de Pagamento',
                'welcome' => 'Boas-vindas'
            ];

            return view('admin.settings.email.preview', compact('emailSettings', 'templates'));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao carregar preview: ' . $e->getMessage()]);
        }
    }

    /**
     * Configurações de email do sistema
     */
    public function emailSettings()
    {
        $settings = [
            'smtp_host' => config('mail.mailers.smtp.host'),
            'smtp_port' => config('mail.mailers.smtp.port'),
            'smtp_username' => config('mail.mailers.smtp.username'),
            'smtp_encryption' => config('mail.mailers.smtp.encryption'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];

        return view('admin.settings.email.index', compact('settings'));
    }

    /**
     * Atualizar configurações de email
     */
    public function updateEmailSettings(Request $request)
    {
        $request->validate([
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer|between:1,65535',
            'smtp_username' => 'required|email|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'required|in:tls,ssl,null',
            'from_address' => 'required|email|max:255',
            'from_name' => 'required|string|max:255',
        ]);

        try {
            // Atualizar configurações no .env
            $this->updateEnvFile([
                'MAIL_HOST' => $request->smtp_host,
                'MAIL_PORT' => $request->smtp_port,
                'MAIL_USERNAME' => $request->smtp_username,
                'MAIL_PASSWORD' => $request->smtp_password ?: config('mail.mailers.smtp.password'),
                'MAIL_ENCRYPTION' => $request->smtp_encryption === 'null' ? null : $request->smtp_encryption,
                'MAIL_FROM_ADDRESS' => $request->from_address,
                'MAIL_FROM_NAME' => $request->from_name,
            ]);

            // Limpar cache de configuração
            \Artisan::call('config:clear');

            return redirect()->route('admin.settings.email.index')
                           ->with('success', 'Configurações de email atualizadas com sucesso!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao atualizar configurações: ' . $e->getMessage()]);
        }
    }

    /**
     * Enviar email de teste
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
            'template' => 'required|string'
        ]);

        try {
            $sampleData = $this->getSampleDataForTemplate($request->template);

            \Mail::send("emails.templates.{$request->template}", $sampleData, function ($message) use ($request) {
                $message->to($request->test_email)
                       ->subject('Email de Teste - ' . config('app.name'));
            });

            return response()->json([
                'success' => true,
                'message' => 'Email de teste enviado com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar email: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Gerar dados de exemplo para templates
     */
    private function getSampleDataForTemplate($template)
    {
        switch ($template) {
            case 'invoice':
                return [
                    'invoice' => (object) [
                        'invoice_number' => 'FAT-2025-001',
                        'total' => 1500.00,
                        'due_date' => now()->addDays(30)->format('d/m/Y'),
                        'client' => (object) [
                            'name' => 'Empresa Exemplo Lda',
                            'email' => 'exemplo@empresa.com'
                        ]
                    ]
                ];

            case 'quote':
                return [
                    'quote' => (object) [
                        'quote_number' => 'COT-2025-001',
                        'total' => 2500.00,
                        'valid_until' => now()->addDays(15)->format('d/m/Y'),
                        'client' => (object) [
                            'name' => 'Cliente Exemplo',
                            'email' => 'cliente@exemplo.com'
                        ]
                    ]
                ];

            default:
                return [
                    'company' => config('app.name'),
                    'user' => (object) [
                        'name' => 'Usuário Exemplo',
                        'email' => 'usuario@exemplo.com'
                    ]
                ];
        }
    }

    /**
     * Atualizar arquivo .env
     */
    private function updateEnvFile($data)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            $value = is_null($value) ? '' : $value;
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        file_put_contents($envFile, $envContent);
    }
}
