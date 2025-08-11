<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdminSettingsController extends Controller
{
    /**
     * Configurações do Sistema
     */
    public function system(Request $request)
    {
        $settings = $this->getSystemSettings();

        return view('admin.settings.system', compact('settings'));
    }

    public function updateSystem(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_description' => 'nullable|string|max:500',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'app_favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:1024',
            'timezone' => 'required|string',
            'locale' => 'required|string',
            'date_format' => 'required|string',
            'currency' => 'required|string|max:3',
            'currency_symbol' => 'required|string|max:5',
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string|max:500',
            'registration_enabled' => 'boolean',
            'email_verification_required' => 'boolean',
            'max_users_per_company' => 'required|integer|min:1',
            'max_invoices_per_month' => 'required|integer|min:1',
            'session_lifetime' => 'required|integer|min:1',
            'auto_logout_time' => 'required|integer|min:5',
        ]);

        $settingsData = $request->except(['app_logo', 'app_favicon']);

        // Upload logo se fornecido
        if ($request->hasFile('app_logo')) {
            $logoPath = $request->file('app_logo')->store('system', 'public');
            $settingsData['app_logo'] = $logoPath;
        }

        // Upload favicon se fornecido
        if ($request->hasFile('app_favicon')) {
            $faviconPath = $request->file('app_favicon')->store('system', 'public');
            $settingsData['app_favicon'] = $faviconPath;
        }

        foreach ($settingsData as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Limpar cache de configurações
        Cache::forget('system_settings');

        return redirect()->route('admin.settings.system')
                        ->with('success', 'Configurações do sistema atualizadas com sucesso!');
    }

    /**
     * Configurações de Faturação
     */
    public function billing(Request $request)
    {
        $settings = $this->getBillingSettings();

        return view('admin.settings.billing', compact('settings'));
    }

    public function updateBilling(Request $request)
    {
        $request->validate([
            'default_tax_rate' => 'required|numeric|min:0|max:100',
            'tax_name' => 'required|string|max:50',
            'invoice_prefix' => 'required|string|max:10',
            'invoice_number_format' => 'required|string|max:50',
            'quote_prefix' => 'required|string|max:10',
            'quote_number_format' => 'required|string|max:50',
            'payment_terms_days' => 'required|integer|min:1',
            'late_fee_enabled' => 'boolean',
            'late_fee_type' => 'required|in:fixed,percentage',
            'late_fee_amount' => 'required|numeric|min:0',
            'auto_send_reminders' => 'boolean',
            'reminder_days_before' => 'required|integer|min:1',
            'reminder_days_after' => 'required|integer|min:1',
            'allow_partial_payments' => 'boolean',
            'minimum_payment_amount' => 'nullable|numeric|min:0',
            'invoice_notes' => 'nullable|string|max:1000',
            'invoice_terms' => 'nullable|string|max:1000',
        ]);

        $settingsData = $request->all();

        foreach ($settingsData as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => "billing_{$key}"],
                ['value' => $value]
            );
        }

        Cache::forget('billing_settings');

        return redirect()->route('admin.settings.billing')
                        ->with('success', 'Configurações de faturação atualizadas com sucesso!');
    }

    /**
     * Configurações de Email
     */
    public function email(Request $request)
    {
        $settings = $this->getEmailSettings();

        return view('admin.settings.email', compact('settings'));
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'mail_driver' => 'required|in:smtp,sendmail,mailgun,ses,postmark',
            'mail_host' => 'required_if:mail_driver,smtp|nullable|string',
            'mail_port' => 'required_if:mail_driver,smtp|nullable|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
            'mail_reply_to' => 'nullable|email',
            'email_notifications_enabled' => 'boolean',
            'invoice_email_template' => 'nullable|string',
            'quote_email_template' => 'nullable|string',
            'reminder_email_template' => 'nullable|string',
            'welcome_email_template' => 'nullable|string',
        ]);

        $settingsData = $request->all();

        foreach ($settingsData as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => "email_{$key}"],
                ['value' => $value]
            );
        }

        Cache::forget('email_settings');

        return redirect()->route('admin.settings.email')
                        ->with('success', 'Configurações de email atualizadas com sucesso!');
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        try {
            Mail::raw('Este é um email de teste do sistema SFS.', function($message) use ($request) {
                $message->to($request->test_email)
                        ->subject('Teste de Email - SFS');
            });

            return response()->json([
                'success' => true,
                'message' => 'Email de teste enviado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Configurações de Backup
     */
    public function backups(Request $request)
    {
        $settings = $this->getBackupSettings();
        $backups = $this->getAvailableBackups();

        return view('admin.settings.backups', compact('settings', 'backups'));
    }

    public function updateBackups(Request $request)
    {
        $request->validate([
            'backup_enabled' => 'boolean',
            'backup_frequency' => 'required|in:daily,weekly,monthly',
            'backup_retention_days' => 'required|integer|min:1|max:365',
            'backup_storage' => 'required|in:local,s3,dropbox',
            'backup_database' => 'boolean',
            'backup_files' => 'boolean',
            'backup_notifications' => 'boolean',
            'backup_notification_email' => 'nullable|email',
        ]);

        $settingsData = $request->all();

        foreach ($settingsData as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => "backup_{$key}"],
                ['value' => $value]
            );
        }

        Cache::forget('backup_settings');

        return redirect()->route('admin.settings.backups')
                        ->with('success', 'Configurações de backup atualizadas com sucesso!');
    }

    public function createBackup(Request $request)
    {
        try {
            $filename = 'backup_' . now()->format('Y_m_d_H_i_s') . '.sql';

            // Criar backup da base de dados
            $databaseName = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');

            $backupPath = storage_path('app/backups/' . $filename);

            // Criar diretório se não existir
            if (!File::exists(dirname($backupPath))) {
                File::makeDirectory(dirname($backupPath), 0755, true);
            }

            $command = "mysqldump --user={$username} --password={$password} --host={$host} {$databaseName} > {$backupPath}";
            exec($command);

            return response()->json([
                'success' => true,
                'message' => 'Backup criado com sucesso!',
                'filename' => $filename
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar backup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);

        if (!File::exists($path)) {
            abort(404, 'Backup não encontrado');
        }

        return response()->download($path);
    }

    public function deleteBackup($filename)
    {
        try {
            $path = storage_path('app/backups/' . $filename);

            if (File::exists($path)) {
                File::delete($path);
            }

            return response()->json([
                'success' => true,
                'message' => 'Backup excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir backup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restoreBackup($filename, Request $request)
    {
        try {
            $path = storage_path('app/backups/' . $filename);

            if (!File::exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo de backup não encontrado'
                ], 404);
            }

            // Restaurar backup (implementação básica)
            $databaseName = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');

            $command = "mysql --user={$username} --password={$password} --host={$host} {$databaseName} < {$path}";
            exec($command);

            return response()->json([
                'success' => true,
                'message' => 'Backup restaurado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao restaurar backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ações do Sistema
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            return response()->json([
                'success' => true,
                'message' => 'Cache limpo com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao limpar cache: ' . $e->getMessage()
            ], 500);
        }
    }

    public function optimizeSystem()
    {
        try {
            Artisan::call('optimize');
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            return response()->json([
                'success' => true,
                'message' => 'Sistema otimizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao otimizar sistema: ' . $e->getMessage()
            ], 500);
        }
    }

    public function maintenanceMode(Request $request)
    {
        try {
            if (app()->isDownForMaintenance()) {
                Artisan::call('up');
                $message = 'Modo de manutenção desativado!';
                $isDown = false;
            } else {
                Artisan::call('down', ['--render' => 'errors::503']);
                $message = 'Modo de manutenção ativado!';
                $isDown = true;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_down' => $isDown
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar modo de manutenção: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Informações do Sistema
     */
    public function getDatabaseInfo()
    {
        try {
            $databaseName = config('database.default');
            $connectionConfig = config("database.connections.{$databaseName}");

            // Informações básicas
            $info = [
                'driver' => $connectionConfig['driver'] ?? 'unknown',
                'database' => $connectionConfig['database'] ?? 'unknown',
                'host' => $connectionConfig['host'] ?? 'unknown',
            ];

            // Informações específicas do MySQL/MariaDB
            if ($info['driver'] === 'mysql') {
                $tables = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = ?", [$info['database']]);
                $size = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS MB FROM information_schema.tables WHERE table_schema = ?", [$info['database']]);

                $info['tables_count'] = $tables[0]->count ?? 0;
                $info['size_mb'] = $size[0]->MB ?? 0;
            }

            return response()->json([
                'success' => true,
                'data' => $info
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter informações da base de dados: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSystemInfo()
    {
        try {
            $info = [
                'php_version' => phpversion(),
                'laravel_version' => app()->version(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'memory_limit' => ini_get('memory_limit'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'max_execution_time' => ini_get('max_execution_time'),
                'disk_space' => [
                    'total' => $this->formatBytes(disk_total_space('/')),
                    'free' => $this->formatBytes(disk_free_space('/')),
                    'used' => $this->formatBytes(disk_total_space('/') - disk_free_space('/'))
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $info
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter informações do sistema: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import/Export de Configurações
     */
    public function exportSettings()
    {
        try {
            $settings = SystemSetting::all()->pluck('value', 'key');

            $filename = 'configuracoes_sistema_' . now()->format('Y-m-d_H-i-s') . '.json';

            $data = [
                'exported_at' => now()->toISOString(),
                'app_version' => config('app.version', '1.0.0'),
                'settings' => $settings
            ];

            return response()->json($data)
                            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao exportar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importSettings(Request $request)
    {
        $request->validate([
            'settings_file' => 'required|file|mimes:json'
        ]);

        try {
            $content = file_get_contents($request->file('settings_file')->getRealPath());
            $data = json_decode($content, true);

            if (!isset($data['settings'])) {
                throw new \Exception('Arquivo de configurações inválido');
            }

            foreach ($data['settings'] as $key => $value) {
                SystemSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            // Limpar todos os caches
            Cache::forget('system_settings');
            Cache::forget('billing_settings');
            Cache::forget('email_settings');
            Cache::forget('backup_settings');

            return response()->json([
                'success' => true,
                'message' => 'Configurações importadas com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao importar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Métodos auxiliares privados
     */
    private function getSystemSettings()
    {
        return Cache::remember('system_settings', 3600, function() {
            $settings = SystemSetting::pluck('value', 'key');

            // Valores padrão
            return array_merge([
                'app_name' => config('app.name', 'SFS'),
                'app_description' => 'Sistema de Faturação e Subscrição',
                'timezone' => config('app.timezone', 'Africa/Maputo'),
                'locale' => config('app.locale', 'pt'),
                'date_format' => 'd/m/Y',
                'currency' => 'MZN',
                'currency_symbol' => 'MT',
                'maintenance_mode' => false,
                'registration_enabled' => true,
                'email_verification_required' => false,
                'max_users_per_company' => 50,
                'max_invoices_per_month' => 1000,
                'session_lifetime' => 120,
                'auto_logout_time' => 30,
            ], $settings->toArray());
        });
    }

    private function getBillingSettings()
    {
        return Cache::remember('billing_settings', 3600, function() {
            $settings = SystemSetting::where('key', 'like', 'billing_%')
                                   ->pluck('value', 'key');

            // Remover prefixo 'billing_' das chaves
            $cleanSettings = [];
            foreach ($settings as $key => $value) {
                $cleanKey = str_replace('billing_', '', $key);
                $cleanSettings[$cleanKey] = $value;
            }

            // Valores padrão
            return array_merge([
                'default_tax_rate' => 16,
                'tax_name' => 'IVA',
                'invoice_prefix' => 'INV',
                'invoice_number_format' => '{prefix}-{year}-{number}',
                'quote_prefix' => 'QUO',
                'quote_number_format' => '{prefix}-{year}-{number}',
                'payment_terms_days' => 30,
                'late_fee_enabled' => false,
                'late_fee_type' => 'percentage',
                'late_fee_amount' => 5,
                'auto_send_reminders' => true,
                'reminder_days_before' => 3,
                'reminder_days_after' => 7,
                'allow_partial_payments' => true,
                'minimum_payment_amount' => 0,
            ], $cleanSettings);
        });
    }

    private function getEmailSettings()
    {
        return Cache::remember('email_settings', 3600, function() {
            $settings = SystemSetting::where('key', 'like', 'email_%')
                                   ->pluck('value', 'key');

            // Remover prefixo 'email_' das chaves
            $cleanSettings = [];
            foreach ($settings as $key => $value) {
                $cleanKey = str_replace('email_', '', $key);
                $cleanSettings[$cleanKey] = $value;
            }

            // Valores padrão
            return array_merge([
                'mail_driver' => config('mail.default', 'smtp'),
                'mail_host' => config('mail.mailers.smtp.host', 'localhost'),
                'mail_port' => config('mail.mailers.smtp.port', 587),
                'mail_username' => config('mail.mailers.smtp.username'),
                'mail_password' => config('mail.mailers.smtp.password'),
                'mail_encryption' => config('mail.mailers.smtp.encryption', 'tls'),
                'mail_from_address' => config('mail.from.address', 'noreply@sfs.com'),
                'mail_from_name' => config('mail.from.name', 'SFS'),
                'email_notifications_enabled' => true,
            ], $cleanSettings);
        });
    }

    private function getBackupSettings()
    {
        return Cache::remember('backup_settings', 3600, function() {
            $settings = SystemSetting::where('key', 'like', 'backup_%')
                                   ->pluck('value', 'key');

            // Remover prefixo 'backup_' das chaves
            $cleanSettings = [];
            foreach ($settings as $key => $value) {
                $cleanKey = str_replace('backup_', '', $key);
                $cleanSettings[$cleanKey] = $value;
            }

            // Valores padrão
            return array_merge([
                'backup_enabled' => true,
                'backup_frequency' => 'daily',
                'backup_retention_days' => 30,
                'backup_storage' => 'local',
                'backup_database' => true,
                'backup_files' => false,
                'backup_notifications' => true,
            ], $cleanSettings);
        });
    }

    private function getAvailableBackups()
    {
        $backupPath = storage_path('app/backups');

        if (!File::exists($backupPath)) {
            return [];
        }

        $files = File::files($backupPath);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'filename' => $file->getFilename(),
                'size' => $this->formatBytes($file->getSize()),
                'created_at' => date('d/m/Y H:i:s', $file->getMTime()),
                'path' => $file->getPathname()
            ];
        }

        // Ordenar por data de criação (mais recente primeiro)
        usort($backups, function($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });

        return $backups;
    }

    private function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = ['', 'KB', 'MB', 'GB', 'TB'];

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
}
