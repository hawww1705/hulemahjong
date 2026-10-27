<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TableCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'branch',
        'subtitle',
        'description',
        'weekday_price',
        'weekend_price',
        'peak_surcharge',
        'session_minutes',
        'min_players',
        'max_players',
        'image_path',
        'amenities',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'weekday_price' => 'integer',
        'weekend_price' => 'integer',
        'peak_surcharge' => 'integer',
        'session_minutes' => 'integer',
        'min_players' => 'integer',
        'max_players' => 'integer',
        'amenities' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function tables(): HasMany
    {
        return $this->hasMany(MahjongTable::class);
    }

    public function activeTables(): HasMany
    {
        return $this->hasMany(MahjongTable::class)->where('status', 'active');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function getImageUrlAttribute(): string
    {
        if (!$this->image_path) {
            return asset('images/mahjong/lounge-regular.jpg');
        }
        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }
        return asset(ltrim($this->image_path, '/'));
    }

    public function getFormattedWeekdayPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->weekday_price, 0, ',', '.');
    }

    public function getFormattedWeekendPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->weekend_price, 0, ',', '.');
    }
}
