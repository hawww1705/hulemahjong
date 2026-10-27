<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'event_date',
        'start_time',
        'end_time',
        'location',
        'price',
        'quota',
        'registered_count',
        'image_path',
        'excerpt',
        'description',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'event_date' => 'date',
        'price' => 'integer',
        'quota' => 'integer',
        'registered_count' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function getRemainingSeatsAttribute(): int
    {
        return max(0, $this->quota - $this->registered_count);
    }

    public function getImageUrlAttribute(): string
    {
        if (!$this->image_path) {
            return asset('images/mahjong/event-tournament.jpg');
        }
        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }
        return asset(ltrim($this->image_path, '/'));
    }

    public function getFormattedPriceAttribute(): string
    {
        return $this->price === 0 ? 'Complimentary' : 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
