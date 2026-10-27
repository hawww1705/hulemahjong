<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\MahjongTable;
use App\Models\TableCategory;
use App\Models\TimeSlot;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingManagementController extends Controller
{
    public function __construct(private readonly BookingService $bookingService)
    {
    }

    public function index(Request $request): View
    {
        $query = Booking::with(['category', 'table'])->latest('booking_date')->latest();

        if ($request->filled('search')) {
            $s = trim($request->string('search')->toString());
            $query->where(function ($q) use ($s) {
                $q->where('booking_code', 'like', "%{$s}%")
                  ->orWhere('customer_name', 'like', "%{$s}%")
                  ->orWhere('customer_phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        $bookings = $query->paginate(15)->withQueryString();
        $categories = TableCategory::with(['tables' => fn ($q) => $q->where('status', 'active')->orderBy('code')])
            ->orderBy('sort_order')
            ->get();
        $slots = TimeSlot::where('is_active', true)->orderBy('sort_order')->get();
        $allTables = MahjongTable::orderBy('code')->get();

        return view('admin.bookings', compact('bookings', 'categories', 'slots', 'allTables'));
    }

    public function storeWalkIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'table_category_id' => ['required', 'exists:table_categories,id'],
            'booking_date' => ['required', 'date'],
            'start_time' => ['required', 'string', 'max:5'],
            'sessions_count' => ['required', 'integer', 'min:1', 'max:6'],
            'players_count' => ['required', 'integer', 'min:2', 'max:12'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:35'],
            'payment_status' => ['required', 'in:unpaid,paid'],
            'notes' => ['nullable', 'string', 'max:400'],
        ]);

        $category = TableCategory::findOrFail($validated['table_category_id']);
        $date = Carbon::parse($validated['booking_date'])->toDateString();
        $pricing = $this->bookingService->calculatePricing($category, $date, $validated['start_time'], (int) $validated['sessions_count']);

        $freeTables = $this->bookingService->getAvailableTables($category, $date, $validated['start_time'], $pricing['duration_minutes']);
        if ($freeTables->isEmpty()) {
            return back()->with('error', 'No active tables available in this category for the chosen date & time.');
        }

        $booking = Booking::create([
            'booking_code' => $this->bookingService->generateBookingCode(),
            'table_category_id' => $category->id,
            'mahjong_table_id' => $freeTables->first()->id,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'booking_date' => $date,
            'start_time' => $pricing['start_time'],
            'end_time' => $pricing['end_time'],
            'sessions_count' => $pricing['sessions_count'],
            'duration_minutes' => $pricing['duration_minutes'],
            'players_count' => (int) $validated['players_count'],
            'base_price' => $pricing['base_price'],
            'discount_amount' => 0,
            'total_price' => $pricing['total_price'],
            'status' => 'confirmed',
            'payment_status' => $validated['payment_status'],
            'payment_method' => 'walk_in',
            'notes' => $validated['notes'] ?? 'Walk-in / Admin reservation',
        ]);

        return back()->with('success', "Walk-in booking {$booking->booking_code} created on table {$freeTables->first()->code}!");
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled'],
            'payment_status' => ['required', 'in:unpaid,waiting_verification,paid,refunded'],
            'mahjong_table_id' => ['nullable', 'exists:mahjong_tables,id'],
        ]);

        $booking->update($validated);

        return back()->with('success', "Booking {$booking->booking_code} updated.");
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $code = $booking->booking_code;
        $booking->delete();

        return back()->with('success', "Booking {$code} deleted.");
    }
}
