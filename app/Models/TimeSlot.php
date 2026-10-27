<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_time',
        'label',
        'is_peak',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_peak' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
