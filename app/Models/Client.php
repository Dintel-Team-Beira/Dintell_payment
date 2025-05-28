<?php
// app/Models/Client.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'company', 'address',
        'tax_number', 'status', 'contact_preferences', 'last_login'
    ];

    protected $casts = [
        'contact_preferences' => 'array',
        'last_login' => 'datetime'
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }

    public function activeSubscriptions()
    {
        return $this->subscriptions()->where('status', 'active');
    }

    public function totalRevenue()
    {
        return $this->subscriptions()->sum('total_revenue');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

     /**
     * Verificar se tem método de pagamento válido
     */
    public function hasValidPaymentMethod()
    {
        // Implementar lógica específica do seu sistema
        return !empty($this->payment_method_id) || !empty($this->card_token);
    }
}