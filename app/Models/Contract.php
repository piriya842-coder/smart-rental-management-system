<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_no',
        'booking_id',
        'room_id',
        'student_id',
        'landlord_id',

        'room_title',
        'room_type',

        'start_date',
        'end_date',

        'monthly_rent',
        'deposit_amount',
        'total_amount',

        'status',
        'payment_status',

        'agreed_to_terms',
        'signed_name',
        'signed_at',
        'signature_ip',
        'signature_data',

        'pdf_path',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'end_date'         => 'date',
        'signed_at'        => 'datetime',
        'agreed_to_terms'  => 'boolean',
        'monthly_rent'     => 'decimal:2',
        'deposit_amount'   => 'decimal:2',
        'total_amount'     => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(\App\Models\Booking::class);
    }

    public function room()
    {
        return $this->belongsTo(\App\Models\Room::class);
    }

    public function student()
    {
        return $this->belongsTo(\App\Models\User::class, 'student_id');
    }

    public function landlord()
    {
        return $this->belongsTo(\App\Models\User::class, 'landlord_id');
    }

    public function monthlyRents()
    {
        return $this->hasMany(\App\Models\MonthlyRent::class);
    }

    public function getIsSignedAttribute(): bool
    {
        return !empty($this->signed_at)
            && !empty($this->signed_name)
            && (bool) $this->agreed_to_terms;
    }
}