<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'max_discount',
        'usage_limit',
        'used_count',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'integer',
        'max_discount' => 'integer',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        if ($this->valid_until && Carbon::parse($this->valid_until)->endOfDay()->isPast()) {
            return false;
        }
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }
        return true;
    }

    public function calculateDiscount(int $baseAmount): int
    {
        if (!$this->isValid() || $baseAmount <= 0) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            $discount = (int) round($baseAmount * ($this->discount_value / 100));
            if ($this->max_discount !== null && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
            return min($discount, $baseAmount);
        }

        return min($this->discount_value, $baseAmount);
    }
}
