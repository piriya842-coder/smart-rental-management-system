<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonthlyRent extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'booking_id',
        'month_label',
        'month_date',
        'due_date',
        'amount',
        'status',
        'receipt_path',
        'method',
        'submitted_at',
        'paid_at',
    ];

    protected $casts = [
        'month_date'    => 'date',
        'due_date'      => 'date',
        'submitted_at'  => 'datetime',
        'paid_at'       => 'datetime',
        'amount'        => 'decimal:2',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}