<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AdminSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
        'validation_rules'
    ];

    protected $casts = [
        'value' => 'json',
        'is_public' => 'boolean',
        'validation_rules' => 'array'
    ];

    const TYPES = [
        'string' => 'Texto',
        'integer' => 'Número Inteiro',
        'float' => 'Número Decimal',
        'boolean' => 'Verdadeiro/Falso',
        'json' => 'JSON',
        'array' => 'Array',
        'file' => 'Arquivo',
        'image' => 'Imagem',
        'email' => 'Email',
        'url' => 'URL',
        'password' => 'Senha'
    ];

    const GROUPS = [
        'system' => 'Sistema',
        'billing' => 'Faturação',
        'email' => 'Email',
        'notifications' => 'Notificações',
        'security' => 'Segurança',
        'backups' => 'Backups',
        'integrations' => 'Integrações',
        'appearance' => 'Aparência',
        'performance' => 'Performance'
    ];

    // Scopes
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Static methods para facilitar o uso
    public static function get($key, $default = null)
    {
        return Cache::remember("admin_setting_{$key}", 3600, function() use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->getValue() : $default;
        });
    }

    public static function set($key, $value, $type = 'string', $group = 'system')
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group
            ]
        );

        Cache::forget("admin_setting_{$key}");
        Cache::forget('admin_settings_all');

        return $setting;
    }

    public static function getGroup($group)
    {
        return Cache::remember("admin_settings_group_{$group}", 3600, function() use ($group) {
            return self::byGroup($group)
                      ->pluck('value', 'key')
                      ->map(function($value, $key) {
                          $setting = self::where('key', $key)->first();
                          return $setting ? $setting->getValue() : $value;
                      });
        });
    }

    public static function getAllSettings()
    {
        return Cache::remember('admin_settings_all', 3600, function() {
            return self::all()->mapWithKeys(function($setting) {
                return [$setting->key => $setting->getValue()];
            });
        });
    }

    // Instance methods
    public function getValue()
    {
        switch ($this->type) {
            case 'boolean':
                return (bool) $this->value;
            case 'integer':
                return (int) $this->value;
            case 'float':
                return (float) $this->value;
            case 'array':
            case 'json':
                return is_array($this->value) ? $this->value : json_decode($this->value, true);
            default:
                return $this->value;
        }
    }

    public function getFormattedValue()
    {
        $value = $this->getValue();

        switch ($this->type) {
            case 'boolean':
                return $value ? 'Sim' : 'Não';
            case 'password':
                return str_repeat('*', 8);
            case 'array':
            case 'json':
                return is_array($value) ? implode(', ', $value) : $value;
            case 'file':
            case 'image':
                return $value ? basename($value) : 'Nenhum arquivo';
            default:
                return $value;
        }
    }

    // Configurações padrão do sistema
    public static function getDefaultSettings()
    {
        return [
            // Sistema
            'system_name' => [
                'value' => 'SFS - Sistema de Faturação',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Nome do sistema',
                'is_public' => true
            ],
            'system_version' => [
                'value' => '1.0.0',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Versão do sistema',
                'is_public' => true
            ],
            'system_maintenance' => [
                'value' => false,
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Modo de manutenção',
                'is_public' => false
            ],
            'max_companies' => [
                'value' => 100,
                'type' => 'integer',
                'group' => 'system',
                'description' => 'Máximo de empresas permitidas',
                'is_public' => false
            ],
            'max_users_per_company' => [
                'value' => 10,
                'type' => 'integer',
                'group' => 'system',
                'description' => 'Máximo de usuários por empresa',
                'is_public' => false
            ],

            // Faturação
            'default_currency' => [
                'value' => 'MZN',
                'type' => 'string',
                'group' => 'billing',
                'description' => 'Moeda padrão do sistema',
                'is_public' => true
            ],
            'default_tax_rate' => [
                'value' => 17.00,
                'type' => 'float',
                'group' => 'billing',
                'description' => 'Taxa de IVA padrão (%)',
                'is_public' => true
            ],
            'invoice_number_format' => [
                'value' => 'FAT-{YYYY}-{MM}-{####}',
                'type' => 'string',
                'group' => 'billing',
                'description' => 'Formato de numeração das faturas',
                'is_public' => false
            ],
            'quote_number_format' => [
                'value' => 'COT-{YYYY}-{MM}-{####}',
                'type' => 'string',
                'group' => 'billing',
                'description' => 'Formato de numeração das cotações',
                'is_public' => false
            ],
            'default_payment_terms' => [
                'value' => 30,
                'type' => 'integer',
                'group' => 'billing',
                'description' => 'Prazo de pagamento padrão (dias)',
                'is_public' => true
            ],

            // Email
            'smtp_host' => [
                'value' => '',
                'type' => 'string',
                'group' => 'email',
                'description' => 'Servidor SMTP',
                'is_public' => false
            ],
            'smtp_port' => [
                'value' => 587,
                'type' => 'integer',
                'group' => 'email',
                'description' => 'Porta SMTP',
                'is_public' => false
            ],
            'smtp_username' => [
                'value' => '',
                'type' => 'string',
                'group' => 'email',
                'description' => 'Usuário SMTP',
                'is_public' => false
            ],
            'smtp_password' => [
                'value' => '',
                'type' => 'password',
                'group' => 'email',
                'description' => 'Senha SMTP',
                'is_public' => false
            ],
            'mail_from_address' => [
                'value' => 'noreply@sistema.com',
                'type' => 'email',
                'group' => 'email',
                'description' => 'Email remetente padrão',
                'is_public' => false
            ],
            'mail_from_name' => [
                'value' => 'Sistema de Faturação',
                'type' => 'string',
                'group' => 'email',
                'description' => 'Nome remetente padrão',
                'is_public' => false
            ],

            // Notificações
            'send_invoice_notifications' => [
                'value' => true,
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enviar notificações de faturas',
                'is_public' => false
            ],
            'send_overdue_reminders' => [
                'value' => true,
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enviar lembretes de vencimento',
                'is_public' => false
            ],
            'overdue_reminder_days' => [
                'value' => [3, 7, 15],
                'type' => 'array',
                'group' => 'notifications',
                'description' => 'Dias para envio de lembretes',
                'is_public' => false
            ],
            'admin_notification_email' => [
                'value' => 'admin@sistema.com',
                'type' => 'email',
                'group' => 'notifications',
                'description' => 'Email para notificações administrativas',
                'is_public' => false
            ],

            // Segurança
            'password_min_length' => [
                'value' => 8,
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Tamanho mínimo da senha',
                'is_public' => false
            ],
            'session_timeout' => [
                'value' => 120,
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Timeout da sessão (minutos)',
                'is_public' => false
            ],
            'max_login_attempts' => [
                'value' => 5,
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Máximo de tentativas de login',
                'is_public' => false
            ],
            'two_factor_enabled' => [
                'value' => false,
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'Autenticação de dois fatores',
                'is_public' => false
            ],

            // Backups
            'backup_enabled' => [
                'value' => true,
                'type' => 'boolean',
                'group' => 'backups',
                'description' => 'Backups automáticos habilitados',
                'is_public' => false
            ],
            'backup_frequency' => [
                'value' => 'daily',
                'type' => 'string',
                'group' => 'backups',
                'description' => 'Frequência dos backups',
                'is_public' => false
            ],
            'backup_retention_days' => [
                'value' => 30,
                'type' => 'integer',
                'group' => 'backups',
                'description' => 'Dias de retenção dos backups',
                'is_public' => false
            ],
            'backup_storage_path' => [
                'value' => 'backups',
                'type' => 'string',
                'group' => 'backups',
                'description' => 'Caminho de armazenamento dos backups',
                'is_public' => false
            ],

            // Performance
            'cache_enabled' => [
                'value' => true,
                'type' => 'boolean',
                'group' => 'performance',
                'description' => 'Cache habilitado',
                'is_public' => false
            ],
            'cache_ttl' => [
                'value' => 3600,
                'type' => 'integer',
                'group' => 'performance',
                'description' => 'TTL do cache (segundos)',
                'is_public' => false
            ],
            'pagination_per_page' => [
                'value' => 20,
                'type' => 'integer',
                'group' => 'performance',
                'description' => 'Itens por página',
                'is_public' => true
            ]
        ];
    }

    public static function seedDefaults()
    {
        $defaults = self::getDefaultSettings();

        foreach ($defaults as $key => $config) {
            self::updateOrCreate(
                ['key' => $key],
                $config
            );
        }

        // Clear cache
        Cache::flush();
    }

    // Validação de configurações
    public function validate($value)
    {
        if (!$this->validation_rules) {
            return true;
        }

        $validator = validator(['value' => $value], ['value' => $this->validation_rules]);
        return $validator->passes();
    }

    // Helper methods para tipos específicos
    public static function getSystemSettings()
    {
        return self::getGroup('system');
    }

    public static function getBillingSettings()
    {
        return self::getGroup('billing');
    }

    public static function getEmailSettings()
    {
        return self::getGroup('email');
    }

    public static function getSecuritySettings()
    {
        return self::getGroup('security');
    }

    public static function clearCache()
    {
        Cache::forget('admin_settings_all');

        foreach (self::GROUPS as $group => $name) {
            Cache::forget("admin_settings_group_{$group}");
        }

        $keys = self::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("admin_setting_{$key}");
        }
    }
}
