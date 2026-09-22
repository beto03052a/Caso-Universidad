<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'status',
        'description',
    ];

    // Valid values for type and status
    public const TYPES = ['infraestructura', 'equipamiento', 'servicio'];
    public const STATUSES = ['operativo', 'en_mantenimiento', 'fuera_servicio'];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'resource_id');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'operativo'       => 'bg-success',
            'en_mantenimiento'=> 'bg-warning text-dark',
            'fuera_servicio'  => 'bg-danger',
            default           => 'bg-secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'operativo'       => 'Operativo',
            'en_mantenimiento'=> 'En Mantenimiento',
            'fuera_servicio'  => 'Fuera de Servicio',
            default           => $this->status,
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'infraestructura' => 'Infraestructura',
            'equipamiento'    => 'Equipamiento',
            'servicio'        => 'Servicio',
            default           => $this->type,
        };
    }
}
