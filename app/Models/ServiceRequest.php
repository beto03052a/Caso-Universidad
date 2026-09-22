<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $table = 'service_requests';

    protected $fillable = [
        'ticket_code',
        'student_id',
        'assigned_to',
        'category_id',
        'resource_id',
        'title',
        'description',
        'priority',
        'status',
        'attended_at',
        'closed_at',
    ];

    protected $casts = [
        'attended_at' => 'datetime',
        'closed_at'   => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    // ─── Constants ────────────────────────────────────────────────────────────

    public const PRIORITIES = ['baja', 'media', 'alta', 'critica'];
    public const STATUSES   = ['pendiente', 'en_proceso', 'atendida', 'cerrada'];

    /** Valid state machine transitions */
    public const TRANSITIONS = [
        'pendiente'  => ['en_proceso'],
        'en_proceso' => ['atendida'],
        'atendida'   => ['cerrada'],
        'cerrada'    => [],
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class, 'resource_id');
    }

    public function evidences()
    {
        return $this->hasMany(RequestEvidence::class, 'request_id');
    }

    public function histories()
    {
        return $this->hasMany(RequestHistory::class, 'request_id')
                    ->orderBy('created_at', 'desc');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pendiente');
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByAssignee(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        return $query;
    }

    // ─── Accessors / Helpers ──────────────────────────────────────────────────

    public function getPriorityBadgeClassAttribute(): string
    {
        return match ($this->priority) {
            'baja'   => 'bg-info text-dark',
            'media'  => 'bg-primary',
            'alta'   => 'bg-warning text-dark',
            'critica'=> 'bg-danger',
            default  => 'bg-secondary',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pendiente'  => 'bg-secondary',
            'en_proceso' => 'bg-primary',
            'atendida'   => 'bg-success',
            'cerrada'    => 'bg-dark',
            default      => 'bg-secondary',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'baja'   => 'Baja',
            'media'  => 'Media',
            'alta'   => 'Alta',
            'critica'=> 'Crítica',
            default  => ucfirst($this->priority),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pendiente'  => 'Pendiente',
            'en_proceso' => 'En Proceso',
            'atendida'   => 'Atendida',
            'cerrada'    => 'Cerrada',
            default      => ucfirst($this->status),
        };
    }

    /**
     * Returns the valid next statuses from the current one.
     */
    public function allowedNextStatuses(): array
    {
        return self::TRANSITIONS[$this->status] ?? [];
    }

    /**
     * Average attention time in hours (from created_at to attended_at or closed_at).
     * Used as a static helper for dashboard metrics.
     */
    public static function avgAttentionHours(): float
    {
        $records = self::whereNotNull('attended_at')
            ->orWhereNotNull('closed_at')
            ->get(['created_at', 'attended_at', 'closed_at']);

        if ($records->isEmpty()) {
            return 0.0;
        }

        $totalHours = $records->sum(function ($r) {
            $end = $r->attended_at ?? $r->closed_at;
            return $r->created_at->diffInHours($end);
        });

        return round($totalHours / $records->count(), 1);
    }
}
