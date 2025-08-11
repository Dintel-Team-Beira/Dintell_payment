<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description'
    ];

    protected $casts = [
        'value' => 'string'
    ];

    /**
     * Obter valor de uma configuração
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Definir valor de uma configuração
     */
    public static function set($key, $value, $type = 'string', $group = 'general')
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group
            ]
        );
    }

    /**
     * Obter múltiplas configurações por grupo
     */
    public static function getGroup($group)
    {
        return static::where('group', $group)->pluck('value', 'key');
    }

    /**
     * Verificar se uma configuração existe
     */
    public static function has($key)
    {
        return static::where('key', $key)->exists();
    }

    /**
     * Deletar uma configuração
     */
    public static function forget($key)
    {
        return static::where('key', $key)->delete();
    }

    /**
     * Obter valor convertido baseado no tipo
     */
    public function getConvertedValueAttribute()
    {
        switch ($this->type) {
            case 'boolean':
                return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $this->value;
            case 'float':
                return (float) $this->value;
            case 'array':
            case 'json':
                return json_decode($this->value, true);
            default:
                return $this->value;
        }
    }

    /**
     * Scope para configurações de sistema
     */
    public function scopeSystem($query)
    {
        return $query->where('group', 'system');
    }

    /**
     * Scope para configurações de faturação
     */
    public function scopeBilling($query)
    {
        return $query->where('group', 'billing');
    }

    /**
     * Scope para configurações de email
     */
    public function scopeEmail($query)
    {
        return $query->where('group', 'email');
    }

    /**
     * Scope para configurações de backup
     */
    public function scopeBackup($query)
    {
        return $query->where('group', 'backup');
    }
}
