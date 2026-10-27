<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\MahjongTable;
use App\Models\MembershipApplication;
use App\Models\TimeSlot;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly BookingService $bookingService)
    {
    }

    public function index(Request $request): View
    {
        $scheduleDate = $request->filled('date')
            ? Carbon::parse($request->string('date')->toString())->toDateString()
            : now()->toDateString();

        $todayBookingsCount = Booking::whereDate('booking_date', now()->toDateString())
            ->whereNotIn('status', ['cancelled'])
            ->count();

        $monthRevenue = Booking::whereMonth('booking_date', now()->month)
            ->whereYear('booking_date', now()->year)
            ->whereNotIn('status', ['cancelled'])
            ->sum('total_price');

        $totalTables = MahjongTable::count();
        $activeTablesCount = MahjongTable::where('status', 'active')->count();
        $maintenanceTablesCount = MahjongTable::where('status', 'maintenance')->count();

        $upcomingEventsCount = Event::where('is_active', true)
            ->whereDate('event_date', '>=', now()->toDateString())
            ->count();

        $galleryCount = Gallery::count();
        $pendingAppsCount = MembershipApplication::where('status', 'pending')->count();

        $recentBookings = Booking::with(['category', 'table'])
            ->latest()
            ->limit(8)
            ->get();

        // Build Floor Schedule Matrix for $scheduleDate
        $tables = MahjongTable::with('category')->orderBy('table_category_id')->orderBy('code')->get();
        $slots = TimeSlot::where('is_active', true)->orderBy('sort_order')->orderBy('start_time')->get();
        $dayBookings = Booking::whereDate('booking_date', $scheduleDate)
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $matrix = [];
        foreach ($tables as $t) {
            $matrix[$t->id] = [];
            foreach ($slots as $s) {
                $slotMin = $this->bookingService->timeToMinutes($s->start_time);
                $matched = $dayBookings->first(function (Booking $b) use ($t, $slotMin) {
                    if ($b->mahjong_table_id !== $t->id) {
                        return false;
                    }
                    $bStart = $this->bookingService->timeToMinutes($b->start_time);
                    $bEnd = $bStart + $b->duration_minutes;
                    return $slotMin >= $bStart && $slotMin < $bEnd;
                });

                $matrix[$t->id][$s->start_time] = $matched;
            }
        }

        return view('admin.dashboard', compact(
            'scheduleDate',
            'todayBookingsCount',
            'monthRevenue',
            'totalTables',
            'activeTablesCount',
            'maintenanceTablesCount',
            'upcomingEventsCount',
            'galleryCount',
            'pendingAppsCount',
            'recentBookings',
            'tables',
            'slots',
            'matrix'
        ));
    }
}
