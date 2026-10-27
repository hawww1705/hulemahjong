<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PromoCode;
use App\Models\TableCategory;
use App\Services\BookingService;
use App\Support\UploadsHelper;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(private readonly BookingService $bookingService)
    {
    }

    public function index(Request $request): View
    {
        $categories = TableCategory::withCount('activeTables')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $selectedCategory = null;
        if ($request->filled('category')) {
            $selectedCategory = $categories->firstWhere('slug', $request->string('category')->toString());
        }
        $selectedCategory = $selectedCategory ?: $categories->first();

        // Build 21 upcoming days so the 7-day strip can paginate cleanly
        $dates = [];
        for ($i = 0; $i < 21; $i++) {
            $d = now()->copy()->addDays($i);
            $dates[] = [
                'date' => $d->toDateString(),
                'day_short' => $d->format('D'),
                'day_num' => $d->format('d'),
                'month_short' => $d->format('M'),
                'formatted_full' => $d->format('M d, Y'),
                'is_weekend' => $d->isWeekend(),
            ];
        }

        // Default to tomorrow so all slots are fresh and ready to explore, or ?date= if provided
        $defaultDate = $request->filled('date')
            ? Carbon::parse($request->string('date')->toString())->toDateString()
            : now()->addDay()->toDateString();

        if (!collect($dates)->contains('date', $defaultDate)) {
            $defaultDate = $dates[0]['date'];
        }

        $slots = $selectedCategory
            ? $this->bookingService->getSlotsAvailability($selectedCategory, $defaultDate, 1)
            : [];

        $firstAvailableSlot = collect($slots)->firstWhere('available', true) ?: ($slots[0] ?? ['start_time' => '10:00']);
        $defaultTime = $firstAvailableSlot['start_time'];

        $initialPricing = $selectedCategory
            ? $this->bookingService->calculatePricing($selectedCategory, $defaultDate, $defaultTime, 1)
            : null;

        $categoriesPayload = $categories->map(fn (TableCategory $c) => [
            'id' => $c->id,
            'name' => $c->name,
            'slug' => $c->slug,
            'branch' => $c->branch,
            'subtitle' => $c->subtitle,
            'weekday_price' => $c->weekday_price,
            'weekend_price' => $c->weekend_price,
            'peak_surcharge' => $c->peak_surcharge,
            'session_minutes' => $c->session_minutes,
            'min_players' => $c->min_players,
            'max_players' => $c->max_players,
            'active_tables_count' => $c->active_tables_count,
            'image_url' => $c->image_url,
            'amenities' => $c->amenities ?? [],
        ])->values();

        return view('public.reserve', [
            'categories' => $categories,
            'categoriesPayload' => $categoriesPayload,
            'selectedCategory' => $selectedCategory,
            'dates' => $dates,
            'defaultDate' => $defaultDate,
            'defaultTime' => $defaultTime,
            'slots' => $slots,
            'initialPricing' => $initialPricing,
        ]);
    }

    public function availability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'table_category_id' => ['required', 'exists:table_categories,id'],
            'date' => ['required', 'date'],
            'sessions_count' => ['nullable', 'integer', 'min:1', 'max:6'],
            'start_time' => ['nullable', 'string'],
            'promo_code' => ['nullable', 'string', 'max:40'],
        ]);

        $category = TableCategory::withCount('activeTables')->findOrFail($validated['table_category_id']);
        $date = Carbon::parse($validated['date'])->toDateString();
        $sessionsCount = (int) ($validated['sessions_count'] ?? 1);

        $slots = $this->bookingService->getSlotsAvailability($category, $date, $sessionsCount);

        $requestedTime = $validated['start_time'] ?? null;
        $matchedSlot = $requestedTime ? collect($slots)->firstWhere('start_time', $requestedTime) : null;

        if (!$matchedSlot || !$matchedSlot['available']) {
            $matchedSlot = collect($slots)->firstWhere('available', true) ?: ($slots[0] ?? null);
        }

        $activeStartTime = $matchedSlot ? $matchedSlot['start_time'] : '10:00';
        $pricing = $this->bookingService->calculatePricing(
            $category,
            $date,
            $activeStartTime,
            $sessionsCount,
            $validated['promo_code'] ?? null
        );

        return response()->json([
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'branch' => $category->branch,
                'subtitle' => $category->subtitle,
                'image_url' => $category->image_url,
                'active_tables_count' => $category->active_tables_count,
                'min_players' => $category->min_players,
                'max_players' => $category->max_players,
            ],
            'date_formatted' => Carbon::parse($date)->format('M d, Y'),
            'selected_time' => $activeStartTime,
            'selected_slot_available' => $matchedSlot ? $matchedSlot['available'] : false,
            'remaining_tables' => $matchedSlot ? $matchedSlot['remaining_tables'] : 0,
            'slots' => $slots,
            'pricing' => $pricing,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'table_category_id' => ['required', 'exists:table_categories,id'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'string', 'max:5'],
            'sessions_count' => ['required', 'integer', 'min:1', 'max:6'],
            'players_count' => ['required', 'integer', 'min:2', 'max:12'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:35'],
            'customer_email' => ['nullable', 'email', 'max:120'],
            'promo_code' => ['nullable', 'string', 'max:40'],
            'payment_method' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $category = TableCategory::findOrFail($validated['table_category_id']);
        $date = Carbon::parse($validated['booking_date'])->toDateString();
        $sessionsCount = (int) $validated['sessions_count'];

        $pricing = $this->bookingService->calculatePricing(
            $category,
            $date,
            $validated['start_time'],
            $sessionsCount,
            $validated['promo_code'] ?? null
        );

        try {
            $booking = DB::transaction(function () use ($category, $date, $validated, $pricing) {
                $freeTables = $this->bookingService->getAvailableTables(
                    $category,
                    $date,
                    $validated['start_time'],
                    $pricing['duration_minutes']
                );

                if ($freeTables->isEmpty()) {
                    throw new \RuntimeException('Selected time slot was just filled. Please choose another available slot or room type.');
                }

                $assignedTable = $freeTables->first();

                if ($pricing['promo_valid'] && $pricing['promo_code']) {
                    PromoCode::where('code', $pricing['promo_code'])->increment('used_count');
                }

                return Booking::create([
                    'booking_code' => $this->bookingService->generateBookingCode(),
                    'table_category_id' => $category->id,
                    'mahjong_table_id' => $assignedTable->id,
                    'customer_name' => $validated['customer_name'],
                    'customer_phone' => $validated['customer_phone'],
                    'customer_email' => $validated['customer_email'] ?? null,
                    'booking_date' => $date,
                    'start_time' => $pricing['start_time'],
                    'end_time' => $pricing['end_time'],
                    'sessions_count' => $pricing['sessions_count'],
                    'duration_minutes' => $pricing['duration_minutes'],
                    'players_count' => (int) $validated['players_count'],
                    'base_price' => $pricing['base_price'],
                    'discount_amount' => $pricing['discount_amount'],
                    'promo_code' => $pricing['promo_code'],
                    'total_price' => $pricing['total_price'],
                    'status' => 'confirmed',
                    'payment_status' => 'unpaid',
                    'payment_method' => $validated['payment_method'] ?? 'qris_transfer',
                    'notes' => $validated['notes'] ?? null,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('booking.show', $booking->booking_code)
            ->with('success', 'Reservation confirmed! Your automatic mahjong table has been assigned.');
    }

    public function show(string $bookingCode): View
    {
        $booking = Booking::with(['category', 'table'])
            ->where('booking_code', strtoupper(trim($bookingCode)))
            ->firstOrFail();

        return view('public.booking-pass', compact('booking'));
    }

    public function uploadProof(Request $request, string $bookingCode): RedirectResponse
    {
        $booking = Booking::where('booking_code', strtoupper(trim($bookingCode)))->firstOrFail();

        $request->validate([
            'payment_proof' => ['required', 'image', 'max:5120'],
        ]);

        $path = UploadsHelper::savePublicImage($request->file('payment_proof'), 'payments');

        $booking->update([
            'payment_proof_path' => $path,
            'payment_status' => 'waiting_verification',
        ]);

        return back()->with('success', 'Payment receipt uploaded! Our concierge will verify your transfer shortly.');
    }

    public function lookup(Request $request): RedirectResponse
    {
        $code = strtoupper(trim((string) $request->input('booking_code')));
        if ($code === '') {
            return back()->with('error', 'Please enter a valid Booking Code (e.g. HLM-...).');
        }

        $booking = Booking::where('booking_code', $code)->first();
        if (!$booking) {
            return back()->with('error', "No booking found with code {$code}. Please check your code and try again.");
        }

        return redirect()->route('booking.show', $booking->booking_code);
    }
}
