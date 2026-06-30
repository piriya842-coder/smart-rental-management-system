<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $table = 'student_profiles';

    protected $fillable = [
        'user_id',
        'date_of_birth',
        'nric_passport',          // ✅ correct column
        'nationality',
        'race',
        'religion',
        'phone',
        'gender',
        'address_line1',
        'address_line2',
        'postcode',
        'city',
        'state',
        'country',
        'emergency_name',
        'emergency_phone',
        'emergency_relationship',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}