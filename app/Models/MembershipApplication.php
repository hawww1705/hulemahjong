<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'membership_plan_id',
        'applicant_name',
        'applicant_phone',
        'applicant_email',
        'preferred_branch',
        'playing_frequency',
        'message',
        'status',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id');
    }
}
