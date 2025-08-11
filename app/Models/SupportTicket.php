<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'description',
        'category',
        'priority',
        'status',
        'satisfaction_rating',
        'satisfaction_comment',
        'first_response_at',
        'resolved_at',
        'admin_viewed_at',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'admin_viewed_at' => 'datetime',
        'satisfaction_rating' => 'integer'
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at');
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Scopes
    public function scopeOpen(Builder $query)
    {
        return $query->where('status', 'open');
    }

    public function scopePending(Builder $query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved(Builder $query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed(Builder $query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeHighPriority(Builder $query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeUnassigned(Builder $query)
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeOverdue(Builder $query)
    {
        return $query->where('created_at', '<', now()->subDays(2))
                     ->whereIn('status', ['open', 'pending']);
    }

    // Mutators & Accessors
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'open' => 'bg-red-100 text-red-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'resolved' => 'bg-green-100 text-green-800',
            'closed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => 'bg-blue-100 text-blue-800',
            'normal' => 'bg-gray-100 text-gray-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'open' => 'Aberto',
            'pending' => 'Pendente',
            'resolved' => 'Resolvido',
            'closed' => 'Fechado',
            default => ucfirst($this->status)
        };
    }

    public function getPriorityTextAttribute()
    {
        return match($this->priority) {
            'low' => 'Baixa',
            'normal' => 'Normal',
            'high' => 'Alta',
            'urgent' => 'Urgente',
            default => ucfirst($this->priority)
        };
    }

    public function getCategoryTextAttribute()
    {
        return match($this->category) {
            'technical' => 'Técnico',
            'billing' => 'Faturação',
            'feature' => 'Funcionalidade',
            'bug' => 'Bug',
            'general' => 'Geral',
            default => ucfirst($this->category)
        };
    }

    public function getResponseTimeAttribute()
    {
        if (!$this->first_response_at) {
            return $this->created_at->diffForHumans();
        }

        return $this->created_at->diffForHumans($this->first_response_at);
    }

    public function getResolutionTimeAttribute()
    {
        if (!$this->resolved_at) {
            return null;
        }

        return $this->created_at->diffForHumans($this->resolved_at);
    }

    // Métodos auxiliares
    public function isOpen()
    {
        return $this->status === 'open';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function isOverdue()
    {
        return $this->created_at->lt(now()->subDays(2)) &&
               in_array($this->status, ['open', 'pending']);
    }

    public function hasReplies()
    {
        return $this->replies()->count() > 0;
    }

    public function lastReply()
    {
        return $this->replies()->latest()->first();
    }

    public function markAsViewed()
    {
        if (!$this->admin_viewed_at) {
            $this->update(['admin_viewed_at' => now()]);
        }
    }

    public function setFirstResponse()
    {
        if (!$this->first_response_at) {
            $this->update(['first_response_at' => now()]);
        }
    }

    public function resolve($comment = null)
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now()
        ]);

        if ($comment) {
            $this->replies()->create([
                'user_id' => auth()->id(),
                'message' => $comment,
                'is_internal' => false,
                'is_resolution' => true
            ]);
        }
    }

    public function close($comment = null)
    {
        $this->update(['status' => 'closed']);

        if ($comment) {
            $this->replies()->create([
                'user_id' => auth()->id(),
                'message' => $comment,
                'is_internal' => false,
                'is_closure' => true
            ]);
        }
    }

    public function assignTo(User $user)
    {
        $this->update(['assigned_to' => $user->id]);

        $this->replies()->create([
            'user_id' => auth()->id(),
            'message' => "Ticket atribuído para {$user->name}",
            'is_internal' => true,
            'is_assignment' => true
        ]);
    }

    // Estatísticas estáticas
    public static function getStats()
    {
        return [
            'total' => static::count(),
            'open' => static::open()->count(),
            'pending' => static::pending()->count(),
            'resolved' => static::resolved()->count(),
            'closed' => static::closed()->count(),
            'overdue' => static::overdue()->count(),
            'unassigned' => static::unassigned()->count(),
            'avg_response_time' => static::getAverageResponseTime(),
            'satisfaction_rate' => static::getSatisfactionRate()
        ];
    }

    public static function getAverageResponseTime()
    {
        $tickets = static::whereNotNull('first_response_at')
            ->select('created_at', 'first_response_at')
            ->get();

        if ($tickets->isEmpty()) {
            return 'N/A';
        }

        $totalMinutes = $tickets->sum(function ($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->first_response_at);
        });

        $averageMinutes = $totalMinutes / $tickets->count();

        if ($averageMinutes < 60) {
            return round($averageMinutes) . ' min';
        } elseif ($averageMinutes < 1440) {
            return round($averageMinutes / 60, 1) . ' h';
        } else {
            return round($averageMinutes / 1440, 1) . ' dias';
        }
    }

    public static function getSatisfactionRate()
    {
        $ratedTickets = static::whereNotNull('satisfaction_rating')->get();

        if ($ratedTickets->isEmpty()) {
            return 'N/A';
        }

        $averageRating = $ratedTickets->avg('satisfaction_rating');
        return round(($averageRating / 5) * 100) . '%';
    }

    // Categories and priorities
    public static function getCategories()
    {
        return [
            'technical' => 'Técnico',
            'billing' => 'Faturação',
            'feature' => 'Funcionalidade',
            'bug' => 'Bug',
            'general' => 'Geral'
        ];
    }

    public static function getPriorities()
    {
        return [
            'low' => 'Baixa',
            'normal' => 'Normal',
            'high' => 'Alta',
            'urgent' => 'Urgente'
        ];
    }

    public static function getStatuses()
    {
        return [
            'open' => 'Aberto',
            'pending' => 'Pendente',
            'resolved' => 'Resolvido',
            'closed' => 'Fechado'
        ];
    }
}
