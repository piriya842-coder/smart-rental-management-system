<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandlordProfile extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'phone',
        'nric',
        'gender',
        'date_of_birth',
        'company_name',
        'business_registration_no',
        'address_line1',
        'address_line2',
        'postcode',
        'city',
        'state',
        'country',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}