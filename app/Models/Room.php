<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_id',
        'status',
        'title',
        'description',
        'address',
        'city',
        'state',
        'postcode',
        'distance_km',
        'price_monthly',
        'room_type',
        'capacity',
        'available_slots',
        'gender_preference',
        'facilities',
        'cover_image',
        'is_available',
        'is_published',
    ];

    protected $casts = [
        'facilities'      => 'array',
        'is_available'    => 'boolean',
        'is_published'    => 'boolean',
        'price_monthly'   => 'decimal:2',
        'distance_km'     => 'decimal:2',
        'capacity'        => 'integer',
        'available_slots' => 'integer',
    ];

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * ✅ IMPORTANT: relationship name MUST be "images"
     * so your controller can do ->with('images')
     */
    public function images(): HasMany
    {
        return $this->hasMany(RoomImage::class, 'room_id');
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saved_rooms', 'room_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Optional helper (safe URL)
     */
    public function coverUrl(): string
    {
        $placeholder = asset('images/slider/slide1.jpg');

        if (!$this->cover_image) return $placeholder;

        $path = trim((string) $this->cover_image);
        if ($path === '') return $placeholder;

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        $base = basename($path);
        if (Storage::disk('public')->exists($base)) {
            return Storage::disk('public')->url($base);
        }

        return $placeholder;
    }

   public function isSingle(): bool
{
    $type = strtolower((string)($this->room_type ?? 'single'));
    return in_array($type, ['single', 'studio', 'master'], true);
}

public function isShared(): bool
{
    $type = strtolower((string)($this->room_type ?? ''));
    return str_contains($type, 'shared');
}

public function isBookable(): bool
{
    // If landlord turned off availability, respect it
    if ($this->is_available === false) {
        return false;
    }

    if ($this->isShared()) {
        // shared must have slots
        return (int)($this->available_slots ?? 0) > 0;
    }

    // single/studio/master: available if is_available true
    return true;
}

public function reviews(): HasMany
{
    return $this->hasMany(\App\Models\Review::class, 'room_id');
}

}
