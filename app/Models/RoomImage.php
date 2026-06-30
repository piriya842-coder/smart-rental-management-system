<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class RoomImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'path',
        'is_cover',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function url(): string
    {
        // supports "public/xxx" or "xxx"
        $p = ltrim((string) $this->path, '/');
        if (str_starts_with($p, 'public/')) $p = substr($p, strlen('public/'));

        if (Storage::disk('public')->exists($p)) {
            return Storage::disk('public')->url($p);
        }

        // fallback
        return asset('storage/' . $p);
    }
}
