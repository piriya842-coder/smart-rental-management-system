<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dispute extends Model
{
    protected $fillable = [
        'code',
        'category',
        'priority',
        'status',
        'submitted_by',
        'student_id',
        'landlord_id',
        'booking_id',
        'room_id',
        'title',
        'description',
        'evidence_path',
        'admin_note',
        'resolution',
        'outcome_details',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function submitter(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'submitted_by'); }
    public function student(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'student_id'); }
    public function landlord(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'landlord_id'); }
    public function booking(): BelongsTo { return $this->belongsTo(\App\Models\Booking::class, 'booking_id'); }
    public function room(): BelongsTo { return $this->belongsTo(\App\Models\Room::class, 'room_id'); }
    public function resolver(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'resolved_by'); }

    public function evidenceUrl(): ?string
    {
        if (!$this->evidence_path) return null;
        return asset('storage/' . $this->evidence_path);
    }
}