<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'message',
        'is_internal',
        'is_status_change',
        'is_assignment',
        'is_resolution',
        'is_closure',
        'metadata'
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'is_status_change' => 'boolean',
        'is_assignment' => 'boolean',
        'is_resolution' => 'boolean',
        'is_closure' => 'boolean',
        'metadata' => 'array'
    ];

    // Relacionamentos
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketReplyAttachment::class);
    }

    // Accessors
    public function getTypeColorAttribute()
    {
        if ($this->is_internal) {
            return 'bg-gray-100 border-gray-300';
        }

        if ($this->is_status_change) {
            return 'bg-blue-100 border-blue-300';
        }

        if ($this->is_resolution) {
            return 'bg-green-100 border-green-300';
        }

        if ($this->is_closure) {
            return 'bg-red-100 border-red-300';
        }

        return 'bg-white border-gray-200';
    }

    public function getTypeIconAttribute()
    {
        if ($this->is_internal) {
            return 'eye-slash';
        }

        if ($this->is_status_change) {
            return 'refresh';
        }

        if ($this->is_resolution) {
            return 'check-circle';
        }

        if ($this->is_closure) {
            return 'x-circle';
        }

        return 'chat';
    }

    public function getTypeTextAttribute()
    {
        if ($this->is_internal) {
            return 'Nota Interna';
        }

        if ($this->is_status_change) {
            return 'Mudança de Status';
        }

        if ($this->is_assignment) {
            return 'Atribuição';
        }

        if ($this->is_resolution) {
            return 'Resolução';
        }

        if ($this->is_closure) {
            return 'Fechamento';
        }

        return 'Resposta';
    }

    // Métodos auxiliares
    public function isFromAdmin()
    {
        return $this->user && $this->user->is_admin;
    }

    public function isFromCustomer()
    {
        return !$this->isFromAdmin();
    }

    public function hasAttachments()
    {
        return $this->attachments()->count() > 0;
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            // Marcar primeira resposta no ticket
            if ($reply->isFromAdmin() && !$reply->is_internal) {
                $reply->ticket->setFirstResponse();
            }

            // Atualizar timestamp do ticket
            $reply->ticket->touch();
        });
    }
}
