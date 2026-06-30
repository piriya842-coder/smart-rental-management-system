<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'landlord_id',
        'room_id',
        'contract_start_date',
        'contract_end_date',
        'deposit_amount',
        'monthly_rent',
        'total_due',
        'status',
        'student_note',

        'cancelled_at',
        'cancelled_reason',
        'cancel_requested_at',
        'cancel_request_reason',
        'refunded_at',
    ];

    protected $casts = [
        'contract_start_date' => 'date',
        'contract_end_date'   => 'date',
        'deposit_amount'      => 'integer',
        'monthly_rent'        => 'decimal:2',
        'total_due'           => 'decimal:2',

        'cancelled_at'        => 'datetime',
        'cancel_requested_at' => 'datetime',
        'refunded_at'         => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }

    public function contract()
    {
        return $this->hasOne(\App\Models\Contract::class, 'booking_id');
    }

    public function canCancelBeforePayment(): bool
    {
        return strtolower((string)$this->status) === 'pending';
    }

    public function canRequestCancelRefund(): bool
    {
        return in_array(strtolower((string)$this->status), ['payment_submitted', 'paid'], true);
    }

    public function isActiveForInventory(): bool
    {
        return in_array(strtolower((string)$this->status), [
            'pending',
            'payment_submitted',
            'paid',
            'cancel_requested',
        ], true);
    }

    public function review(): HasOne
{
    return $this->hasOne(\App\Models\Review::class, 'booking_id');
}

public function messages()
{
    return $this->hasMany(\App\Models\Message::class, 'booking_id')->latest();
}

}