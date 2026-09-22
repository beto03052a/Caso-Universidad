<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'user_id',
        'action',
        'comment',
        'previous_status',
        'new_status',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
