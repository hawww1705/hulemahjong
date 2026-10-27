<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MahjongTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_category_id',
        'code',
        'name',
        'floor_zone',
        'machine_model',
        'status',
        'notes',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TableCategory::class, 'table_category_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
