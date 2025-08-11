<?php

namespace App\Http\Controllers\Admin;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
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

    public function backups(Request $request)
    {
        $backups = $this->getBackupsList();
        $settings = $this->getBackupSettings();

        return view('admin.settings.backups', compact('backups', 'settings'));
    }

    public function updateBackups(Request $request)
    {
        $request->validate([
            'auto_backup_enabled' => 'boolean',
            'backup_frequency' => 'required|in:daily,weekly,monthly',
            'backup_time' => 'required|string',
            'backup_retention_days' => 'required|integer|min:1|max:365',
            'backup_storage' => 'required|in:local,s3,google',
            'include_files' => 'boolean',
            'include_database' => 'boolean',
            'notification_email' => 'nullable|email',
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
            Artisan::call('backup:run');

            return response()->json([
                'success' => true,
                'message' => 'Backup criado com sucesso!'
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
        $backupPath = storage_path("app/backups/{$filename}");

        if (!file_exists($backupPath)) {
            return redirect()->route('admin.settings.backups')
                            ->with('error', 'Arquivo de backup não encontrado!');
        }

        return response()->download($backupPath);
    }

    public function deleteBackup($filename)
    {
        $backupPath = storage_path("app/backups/{$filename}");

        if (file_exists($backupPath)) {
            unlink($backupPath);
        }

        return response()->json([
            'success' => true,
            'message' => 'Backup deletado com sucesso!'
        ]);
    }

    public function restoreBackup(Request $request, $filename)
    {
        $request->validate([
            'confirm_restore' => 'required|accepted'
        ]);

        try {
            // Esta seria a lógica para restaurar o backup
            // Por segurança, isso deveria ser feito via command line
            Artisan::call('backup:restore', ['filename' => $filename]);

            return response()->json([
                'success' => true,
                'message' => 'Backup restaurado com sucesso! O sistema será reiniciado.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao restaurar backup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clearCache(Request $request)
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

    public function optimizeSystem(Request $request)
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
        $request->validate([
            'enable' => 'required|boolean',
            'message' => 'nullable|string|max:500'
        ]);

        try {
            if ($request->enable) {
                Artisan::call('down', [
                    '--message' => $request->message ?: 'Sistema em manutenção'
                ]);
                $message = 'Modo de manutenção ativado!';
            } else {
                Artisan::call('up');
                $message = 'Modo de manutenção desativado!';
            }

            // Atualizar configuração
            SystemSetting::updateOrCreate(
                ['key' => 'maintenance_mode'],
                ['value' => $request->enable]
            );

            if ($request->message) {
                SystemSetting::updateOrCreate(
                    ['key' => 'maintenance_message'],
                    ['value' => $request->message]
                );
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar modo de manutenção: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDatabaseInfo()
    {
        try {
            $connection = DB ::connection();
            $database = $connection->getDatabaseName();

            // Informações básicas do banco
            $tables = \DB::select('SHOW TABLES');
            $tableCount = count($tables);

            // Tamanho do banco (MySQL)
            $sizeQuery = "SELECT
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'DB Size in MB'
                FROM information_schema.tables
                WHERE table_schema = ?";

            $size = \DB::select($sizeQuery, [$database]);
            $dbSize = $size[0]->{'DB Size in MB'} ?? 0;

            // Estatísticas das tabelas principais
            $tableStats = [
                'users' => \DB::table('users')->count(),
                'companies' => \DB::table('companies')->count(),
                'invoices' => \DB::table('invoices')->count(),
                'clients' => \DB::table('clients')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'database_name' => $database,
                    'table_count' => $tableCount,
                    'database_size' => $dbSize . ' MB',
                    'table_stats' => $tableStats
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter informações do banco: ' . $e->getMessage()
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
                'max_execution_time' => ini_get('max_execution_time'),
                'memory_limit' => ini_get('memory_limit'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'disk_space' => $this->getDiskSpace(),
                'extensions' => $this->getRequiredExtensions()
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

    // Métodos auxiliares privados
    private function getSystemSettings()
    {
        return Cache::remember('system_settings', 3600, function() {
            $settings = SystemSetting::whereIn('key', [
                'app_name', 'app_description', 'app_logo', 'app_favicon',
                'timezone', 'locale', 'date_format', 'currency', 'currency_symbol',
                'maintenance_mode', 'maintenance_message', 'registration_enabled',
                'email_verification_required', 'max_users_per_company',
                'max_invoices_per_month', 'session_lifetime', 'auto_logout_time'
            ])->pluck('value', 'key');

            // Valores padrão
            return array_merge([
                'app_name' => 'SFS - Sistema de Faturação',
                'app_description' => 'Sistema completo de faturação e subscrição',
                'timezone' => 'Africa/Maputo',
                'locale' => 'pt_BR',
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
                'mail_driver' => 'smtp',
                'mail_host' => 'smtp.gmail.com',
                'mail_port' => 587,
                'mail_encryption' => 'tls',
                'mail_from_address' => 'noreply@sfs.co.mz',
                'mail_from_name' => 'SFS Sistema',
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
                'auto_backup_enabled' => true,
                'backup_frequency' => 'daily',
                'backup_time' => '02:00',
                'backup_retention_days' => 30,
                'backup_storage' => 'local',
                'include_files' => true,
                'include_database' => true,
            ], $cleanSettings);
        });
    }

    private function getBackupsList()
    {
        $backupPath = storage_path('app/backups');
        $backups = [];

        if (is_dir($backupPath)) {
            $files = scandir($backupPath);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                    $filePath = $backupPath . '/' . $file;
                    $backups[] = [
                        'filename' => $file,
                        'size' => $this->formatBytes(filesize($filePath)),
                        'created_at' => date('d/m/Y H:i:s', filemtime($filePath))
                    ];
                }
            }
        }

        // Ordenar por data de criação (mais recente primeiro)
        usort($backups, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $backups;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function getDiskSpace()
    {
        $totalSpace = disk_total_space('/');
        $freeSpace = disk_free_space('/');
        $usedSpace = $totalSpace - $freeSpace;

        return [
            'total' => $this->formatBytes($totalSpace),
            'used' => $this->formatBytes($usedSpace),
            'free' => $this->formatBytes($freeSpace),
            'usage_percentage' => round(($usedSpace / $totalSpace) * 100, 2)
        ];
    }

    private function getRequiredExtensions()
    {
        $required = [
            'openssl', 'pdo', 'mbstring', 'tokenizer', 'xml', 'ctype',
            'json', 'bcmath', 'curl', 'fileinfo', 'gd', 'zip'
        ];

        $extensions = [];
        foreach ($required as $ext) {
            $extensions[$ext] = extension_loaded($ext);
        }

        return $extensions;
    }

    public function exportSettings(Request $request)
    {
        $settings = SystemSetting::all()->pluck('value', 'key');

        $filename = 'configuracoes_sistema_' . now()->format('Y-m-d_H-i-s') . '.json';

        $data = [
            'exported_at' => now()->toISOString(),
            'app_version' => config('app.version', '1.0.0'),
            'settings' => $settings
        ];

        return response()->json($data)
                        ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
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

            // Limpar cache
            Cache::forget('system_settings');
            Cache::forget('billing_settings');
            Cache::forget('email_settings');
            Cache::forget('backup_settings');

            return redirect()->back()
                            ->with('success', 'Configurações importadas com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Erro ao importar configurações: ' . $e->getMessage());
        }
    }
}
