<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'company_id',
        'assigned_to',
        'subject',
        'description',
        'priority',
        'status',
        'category',
        'first_response_at',
        'resolved_at',
        'closed_at',
        'last_activity_at',
        'satisfaction_rating',
        'satisfaction_comment',
        'attachments',
        'metadata'
    ];

    protected $casts = [
        'attachments' => 'array',
        'metadata' => 'array',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            $ticket->ticket_number = static::generateTicketNumber();
            $ticket->last_activity_at = now();
        });

        static::updating(function ($ticket) {
            $ticket->last_activity_at = now();
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(SupportTicketReply::class, 'ticket_id')->orderBy('created_at');
    }

    public function views(): HasMany
    {
        return $this->hasMany(SupportTicketView::class, 'ticket_id');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeOverdue($query)
    {
        return $query->where('created_at', '<', now()->subHours(24))
                    ->whereIn('status', ['open', 'pending']);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    // Métodos auxiliares
    public static function generateTicketNumber(): string
    {
        $prefix = 'TKT';
        $year = date('Y');
        $month = date('m');

        $lastTicket = static::whereYear('created_at', $year)
                           ->whereMonth('created_at', $month)
                           ->orderByDesc('id')
                           ->first();

        $number = $lastTicket ?
            (int) substr($lastTicket->ticket_number, -4) + 1 : 1;

        return $prefix . '-' . $year . $month . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'resolved' => 'bg-purple-100 text-purple-800',
            'closed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'bg-gray-100 text-gray-800',
            'medium' => 'bg-blue-100 text-blue-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function isOverdue(): bool
    {
        return $this->created_at->lt(now()->subHours(24)) &&
               in_array($this->status, ['open', 'pending']);
    }

    public function markAsViewed(User $user): void
    {
        $this->views()->updateOrCreate(
            ['user_id' => $user->id],
            ['viewed_at' => now()]
        );
    }

    public function hasBeenViewedBy(User $user): bool
    {
        return $this->views()->where('user_id', $user->id)->exists();
    }

    public function close(string $comment = null): void
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now()
        ]);

        if ($comment) {
            $this->replies()->create([
                'user_id' => auth()->id(),
                'message' => $comment,
                'is_system' => true
            ]);
        }
    }

    public function resolve(string $comment = null): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now()
        ]);

        if ($comment) {
            $this->replies()->create([
                'user_id' => auth()->id(),
                'message' => $comment,
                'is_system' => true
            ]);
        }
    }

    public function assignTo(User $user): void
    {
        $this->update([
            'assigned_to' => $user->id,
            'status' => 'in_progress'
        ]);

        $this->replies()->create([
            'user_id' => auth()->id(),
            'message' => "Ticket atribuído para {$user->name}",
            'is_internal' => true,
            'is_system' => true
        ]);
    }

    public function addReply(string $message, User $user, bool $isInternal = false, array $attachments = []): SupportTicketReply
    {
        $reply = $this->replies()->create([
            'user_id' => $user->id,
            'message' => $message,
            'is_internal' => $isInternal,
            'attachments' => $attachments
        ]);

        // Marcar primeira resposta se for de um admin
        if (!$this->first_response_at && $user->is_super_admin) {
            $this->update(['first_response_at' => now()]);
        }

        return $reply;
    }

    // Estatísticas
    public static function getStats(): array
    {
        return [
            'total' => static::count(),
            'open' => static::open()->count(),
            'pending' => static::pending()->count(),
            'in_progress' => static::inProgress()->count(),
            'resolved' => static::resolved()->count(),
            'closed' => static::closed()->count(),
            'overdue' => static::overdue()->count(),
            'unassigned' => static::unassigned()->count(),
            'high_priority' => static::highPriority()->count(),
        ];
    }
}

class SupportTicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_internal',
        'is_system',
        'attachments',
        'read_at'
    ];

    protected $casts = [
        'attachments' => 'array',
        'read_at' => 'datetime',
        'is_internal' => 'boolean',
        'is_system' => 'boolean'
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }
}

class SupportTicketView extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'viewed_at'
    ];

    protected $casts = [
        'viewed_at' => 'datetime'
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

class SupportCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
