<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\MahjongTable;
use App\Models\PromoCode;
use App\Models\TableCategory;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BookingService
{
    public function timeToMinutes(string $hhmm): int
    {
        [$h, $m] = array_pad(explode(':', $hhmm), 2, '0');
        return ((int) $h * 60) + (int) $m;
    }

    public function minutesToTime(int $totalMinutes): string
    {
        $normalized = $totalMinutes % 1440;
        $h = intdiv($normalized, 60);
        $m = $normalized % 60;
        return sprintf('%02d:%02d', $h, $m);
    }

    /**
     * Find available physical MahjongTable models for a category, date, start time, and duration.
     */
    public function getAvailableTables(
        TableCategory $category,
        string $date,
        string $startTime,
        int $durationMinutes,
        ?int $excludeBookingId = null
    ): Collection {
        $activeTables = MahjongTable::where('table_category_id', $category->id)
            ->where('status', 'active')
            ->orderBy('code')
            ->get();

        if ($activeTables->isEmpty()) {
            return collect();
        }

        $reqStart = $this->timeToMinutes($startTime);
        $reqEnd = $reqStart + $durationMinutes;

        $bookingsQuery = Booking::where('table_category_id', $category->id)
            ->whereDate('booking_date', $date)
            ->whereNotIn('status', ['cancelled']);

        if ($excludeBookingId) {
            $bookingsQuery->where('id', '!=', $excludeBookingId);
        }

        $existingBookings = $bookingsQuery->get();

        $occupiedTableIds = [];
        $unassignedOverlaps = 0;

        foreach ($existingBookings as $b) {
            $bStart = $this->timeToMinutes($b->start_time);
            $bEnd = $bStart + (int) $b->duration_minutes;

            // Interval overlap check: [reqStart, reqEnd) overlaps [bStart, bEnd)
            if ($reqStart < $bEnd && $reqEnd > $bStart) {
                if ($b->mahjong_table_id) {
                    $occupiedTableIds[$b->mahjong_table_id] = true;
                } else {
                    $unassignedOverlaps++;
                }
            }
        }

        $freeTables = $activeTables->reject(fn ($t) => isset($occupiedTableIds[$t->id]))->values();

        if ($unassignedOverlaps > 0) {
            $freeTables = $freeTables->slice($unassignedOverlaps)->values();
        }

        return $freeTables;
    }

    /**
     * Return availability and dynamic price for every active TimeSlot on a given date and category.
     */
    public function getSlotsAvailability(
        TableCategory $category,
        string $date,
        int $sessionsCount = 1
    ): array {
        $slots = TimeSlot::where('is_active', true)->orderBy('sort_order')->orderBy('start_time')->get();
        $totalActiveTables = MahjongTable::where('table_category_id', $category->id)
            ->where('status', 'active')
            ->count();

        $durationMinutes = max(60, $category->session_minutes * max(1, $sessionsCount));
        $result = [];

        foreach ($slots as $slot) {
            $freeTables = $this->getAvailableTables($category, $date, $slot->start_time, $durationMinutes);
            $pricing = $this->calculatePricing($category, $date, $slot->start_time, $sessionsCount, null, $slot);

            // Check if slot is already in the past for today
            $slotDateTime = Carbon::parse($date . ' ' . $slot->start_time);
            $isPast = $slotDateTime->lt(now()->subMinutes(15));

            $remaining = $isPast ? 0 : $freeTables->count();

            $result[] = [
                'id' => $slot->id,
                'start_time' => $slot->start_time,
                'label' => $slot->label,
                'is_peak' => (bool) $slot->is_peak,
                'available' => $remaining > 0,
                'is_past' => $isPast,
                'remaining_tables' => $remaining,
                'total_tables' => $totalActiveTables,
                'price' => $pricing['total_price'],
                'formatted_price' => 'Rp ' . number_format($pricing['total_price'], 0, ',', '.'),
            ];
        }

        return $result;
    }

    /**
     * Calculate complete dynamic pricing including weekday/weekend, peak surcharge, sessions, and promo code.
     */
    public function calculatePricing(
        TableCategory $category,
        string $date,
        string $startTime,
        int $sessionsCount = 1,
        ?string $promoCodeStr = null,
        ?TimeSlot $slotModel = null
    ): array {
        $sessionsCount = max(1, min(6, $sessionsCount));
        $carbonDate = Carbon::parse($date);
        $isWeekend = $carbonDate->isWeekend();

        $slot = $slotModel ?: TimeSlot::where('start_time', $startTime)->first();
        $isPeak = $slot ? (bool) $slot->is_peak : ($this->timeToMinutes($startTime) >= 18 * 60);

        $sessionRate = $isWeekend ? $category->weekend_price : $category->weekday_price;
        $peakPerSession = $isPeak ? $category->peak_surcharge : 0;

        $unitPrice = $sessionRate + $peakPerSession;
        $basePrice = $unitPrice * $sessionsCount;

        $discountAmount = 0;
        $appliedCode = null;
        $promoValid = false;
        $promoMessage = null;

        if ($promoCodeStr !== null && trim($promoCodeStr) !== '') {
            $cleanCode = strtoupper(trim($promoCodeStr));
            $promo = PromoCode::where('code', $cleanCode)->first();
            if ($promo && $promo->isValid()) {
                $discountAmount = $promo->calculateDiscount($basePrice);
                $appliedCode = $promo->code;
                $promoValid = true;
                $promoMessage = "Promo {$promo->code} applied (-Rp " . number_format($discountAmount, 0, ',', '.') . ')';
            } else {
                $promoMessage = 'Promo code is invalid or expired.';
            }
        }

        $totalPrice = max(0, $basePrice - $discountAmount);
        $durationMinutes = $category->session_minutes * $sessionsCount;
        $endTime = $this->minutesToTime($this->timeToMinutes($startTime) + $durationMinutes);

        return [
            'is_weekend' => $isWeekend,
            'rate_label' => $isWeekend ? 'Weekend Rate' : 'Weekday Rate',
            'is_peak' => $isPeak,
            'session_rate' => $sessionRate,
            'peak_surcharge' => $peakPerSession * $sessionsCount,
            'sessions_count' => $sessionsCount,
            'duration_minutes' => $durationMinutes,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'base_price' => $basePrice,
            'discount_amount' => $discountAmount,
            'promo_code' => $appliedCode,
            'promo_valid' => $promoValid,
            'promo_message' => $promoMessage,
            'total_price' => $totalPrice,
            'formatted_base' => 'Rp ' . number_format($basePrice, 0, ',', '.'),
            'formatted_discount' => 'Rp ' . number_format($discountAmount, 0, ',', '.'),
            'formatted_total' => 'Rp ' . number_format($totalPrice, 0, ',', '.'),
        ];
    }

    public function generateBookingCode(): string
    {
        do {
            $code = 'HLM-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));
        } while (Booking::where('booking_code', $code)->exists());

        return $code;
    }
}
