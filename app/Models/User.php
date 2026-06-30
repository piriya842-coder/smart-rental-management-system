<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',

        'phone',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postcode',

        'student_id',
        'programme',
        'age',
        'gender',

        'company_name',
        'verification_document_path',
        'verification_document_type',
        'address',

        'landlord_status',
        'landlord_verified_at',
        'landlord_rejected_reason',
        'landlord_rejected_at',
        'landlord_rejection_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'landlord_verified_at' => 'datetime',
            'landlord_rejected_at' => 'datetime',
        ];
    }

    // ============================================================
    // ✅ SAVED ROOMS (BOOKMARKS) RELATIONSHIP
    // ============================================================

    public function savedRooms()
    {
        return $this->belongsToMany(\App\Models\Room::class, 'saved_rooms', 'user_id', 'room_id')
            ->withTimestamps();
    }

    public function rooms()
    {
        return $this->hasMany(\App\Models\Room::class, 'landlord_id');
    }

    public function studentProfile()
    {
        return $this->hasOne(\App\Models\StudentProfile::class);
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class, 'student_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(\App\Models\Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(\App\Models\Message::class, 'receiver_id');
    }

    public function bookings()
    {
        return $this->hasMany(\App\Models\Booking::class, 'student_id');
    }

    public function landlordProfile()
    {
        return $this->hasOne(\App\Models\LandlordProfile::class);
    }
}