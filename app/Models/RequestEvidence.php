<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestEvidence extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'file_path',
        'file_type',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function isImage(): bool
    {
        return str_starts_with($this->file_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->file_type === 'application/pdf';
    }
}
