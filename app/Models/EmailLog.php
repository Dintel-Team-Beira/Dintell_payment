<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'client_id',
        'to_email',
        'subject',
        'type',
        'content',
        'status',
        'sent_at',
        'has_attachment',
        'attachment_path',
        'attachment_name'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'has_attachment' => 'boolean'
    ];

    // Relacionamentos
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Escopos
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeQueued($query)
    {
        return $query->where('status', 'queued');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Métodos
    public function markAsSent()
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'error_message' => null
        ]);
    }

    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage
        ]);
    }

     // Scope para emails com anexos
     public function scopeWithAttachments($query)
     {
         return $query->where('has_attachment', true);
     }

     // Scope por tipo de email
     public function scopeByType($query, $type)
     {
         return $query->where('type', $type);
     }
}