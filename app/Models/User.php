<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTecnico(): bool
    {
        return $this->role === 'tecnico';
    }

    public function isEstudiante(): bool
    {
        return $this->role === 'estudiante';
    }

    public function isAdminOrTecnico(): bool
    {
        return in_array($this->role, ['admin', 'tecnico']);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * Solicitudes enviadas por este usuario (como estudiante).
     */
    public function submittedRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'student_id');
    }

    /**
     * Solicitudes asignadas a este usuario (como técnico/responsable).
     */
    public function assignedRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'assigned_to');
    }

    /**
     * Historial de acciones realizadas por este usuario.
     */
    public function requestHistories()
    {
        return $this->hasMany(RequestHistory::class, 'user_id');
    }
}
