<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'registration_code',
        'participant_name',
        'participant_phone',
        'participant_email',
        'skill_level',
        'seats',
        'total_price',
        'status',
        'notes',
    ];

    protected $casts = [
        'seats' => 'integer',
        'total_price' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
