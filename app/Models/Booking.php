<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'table_category_id',
        'mahjong_table_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'booking_date',
        'start_time',
        'end_time',
        'sessions_count',
        'duration_minutes',
        'players_count',
        'base_price',
        'discount_amount',
        'promo_code',
        'total_price',
        'status',
        'payment_status',
        'payment_method',
        'payment_proof_path',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'sessions_count' => 'integer',
        'duration_minutes' => 'integer',
        'players_count' => 'integer',
        'base_price' => 'integer',
        'discount_amount' => 'integer',
        'total_price' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TableCategory::class, 'table_category_id');
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(MahjongTable::class, 'mahjong_table_id');
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        return $this->payment_proof_path ? asset(ltrim($this->payment_proof_path, '/')) : null;
    }
}
