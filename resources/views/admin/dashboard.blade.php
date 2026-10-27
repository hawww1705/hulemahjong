@extends('layouts.admin')

@section('title', 'Dashboard — Hule Mahjong CMS')
@section('heading', 'Executive Overview & Live Floor')
@section('subheading', 'Monitor real-time automatic table occupancy, revenue, and bookings.')

@section('header_actions')
    <a href="{{ route('admin.tables') }}" class="px-4 py-2 rounded-full bg-[#c3b091] hover:bg-[#b59f7c] text-[#141310] text-xs font-semibold transition">
        + Manage Tables & Prices
    </a>
@endsection

@section('content')
    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-2xl p-5 border border-[#ebe5d9]">
            <p class="text-[11px] font-bold uppercase tracking-wider text-[#8c734b]">Today's Bookings</p>
            <p class="text-[28px] font-bold text-[#141414] mt-1">{{ $todayBookingsCount }}</p>
            <p class="text-xs text-stone-500 mt-1">Active reservations today</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-[#ebe5d9]">
            <p class="text-[11px] font-bold uppercase tracking-wider text-[#8c734b]">Monthly Revenue</p>
            <p class="text-[26px] font-bold text-[#141414] mt-1">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</p>
            <p class="text-xs text-stone-500 mt-1">{{ now()->format('F Y') }} gross bookings</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-[#ebe5d9]">
            <p class="text-[11px] font-bold uppercase tracking-wider text-[#8c734b]">Automatic Tables</p>
            <p class="text-[28px] font-bold text-[#141414] mt-1">{{ $activeTablesCount }} <span class="text-sm font-normal text-stone-400">/ {{ $totalTables }} active</span></p>
            <p class="text-xs text-stone-500 mt-1">{{ $maintenanceTablesCount }} unit(s) in maintenance</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-[#ebe5d9]">
            <p class="text-[11px] font-bold uppercase tracking-wider text-[#8c734b]">Events & Media</p>
            <p class="text-[28px] font-bold text-[#141414] mt-1">{{ $upcomingEventsCount }} <span class="text-sm font-normal text-stone-400">Events</span></p>
            <p class="text-xs text-stone-500 mt-1">{{ $galleryCount }} Gallery Photos • {{ $pendingAppsCount }} Member Apps</p>
        </div>
    </div>

    <!-- Live Floor Schedule Matrix -->
    <div class="bg-white rounded-2xl border border-[#ebe5d9] p-5 sm:p-6 mb-8">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
            <div>
                <h2 class="text-[17px] font-bold text-[#141414]">Live Table Occupancy Matrix</h2>
                <p class="text-xs text-stone-500">Visual slot schedule across all physical mahjong tables for {{ \Carbon\Carbon::parse($scheduleDate)->format('D, d M Y') }}</p>
            </div>
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $scheduleDate }}" class="cms-input !w-auto !py-1.5 text-xs">
                <button type="submit" class="px-4 py-2 rounded-xl bg-[#111] text-white text-xs font-semibold">Load Date</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[780px]">
                <thead>
                    <tr class="border-b border-stone-200 text-[11px] uppercase tracking-wider text-stone-500">
                        <th class="py-2.5 pr-4">Table Unit</th>
                        @foreach ($slots as $slot)
                            <th class="py-2.5 px-2 text-center">{{ $slot->label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-xs">
                    @foreach ($tables as $t)
                        <tr>
                            <td class="py-2.5 pr-4 whitespace-nowrap">
                                <span class="font-bold text-stone-900">{{ $t->code }}</span>
                                <span class="block text-[11px] text-stone-400">{{ $t->category->name ?? '-' }}</span>
                            </td>
                            @foreach ($slots as $slot)
                                @php $cellBooking = $matrix[$t->id][$slot->start_time] ?? null; @endphp
                                <td class="p-1.5 text-center">
                                    @if ($t->status !== 'active')
                                        <span class="block py-1.5 px-2 rounded-lg bg-amber-50 text-amber-700 text-[10.5px] font-medium">
                                            {{ ucfirst($t->status) }}
                                        </span>
                                    @elseif ($cellBooking)
                                        <a href="{{ route('admin.bookings', ['search' => $cellBooking->booking_code]) }}"
                                           class="block py-1.5 px-2 rounded-lg bg-[#e7dcc8] text-[#4f3e22] text-[10.5px] font-semibold truncate max-w-[105px] mx-auto"
                                           title="{{ $cellBooking->customer_name }} ({{ $cellBooking->booking_code }})">
                                            {{ Str::limit($cellBooking->customer_name, 10) }}
                                        </a>
                                    @else
                                        <span class="block py-1.5 px-2 rounded-lg bg-stone-50 text-stone-400 text-[10.5px]">Open</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="bg-white rounded-2xl border border-[#ebe5d9] p-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-[17px] font-bold text-[#141414]">Recent Reservations</h2>
            <a href="{{ route('admin.bookings') }}" class="text-xs font-semibold text-[#8c734b] hover:underline">Manage All →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-stone-200 text-[11px] uppercase tracking-wider text-stone-500">
                        <th class="py-3 pr-3">Code</th>
                        <th class="py-3 px-3">Guest</th>
                        <th class="py-3 px-3">Suite & Unit</th>
                        <th class="py-3 px-3">Schedule</th>
                        <th class="py-3 px-3">Total</th>
                        <th class="py-3 pl-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($recentBookings as $b)
                        <tr>
                            <td class="py-3 pr-3 font-bold text-stone-900">
                                <a href="{{ route('booking.show', $b->booking_code) }}" target="_blank" class="hover:text-[#8c734b] underline">{{ $b->booking_code }}</a>
                            </td>
                            <td class="py-3 px-3">
                                <span class="font-semibold text-stone-800">{{ $b->customer_name }}</span>
                                <span class="block text-stone-400">{{ $b->customer_phone }}</span>
                            </td>
                            <td class="py-3 px-3">
                                {{ $b->category->name ?? '-' }}
                                <span class="block text-[#8c734b] font-semibold">{{ $b->table->code ?? 'Unassigned' }}</span>
                            </td>
                            <td class="py-3 px-3">
                                {{ $b->booking_date->format('d M Y') }}
                                <span class="block text-stone-500">{{ $b->start_time }}–{{ $b->end_time }}</span>
                            </td>
                            <td class="py-3 px-3 font-bold">{{ $b->formatted_total }}</td>
                            <td class="py-3 pl-3">
                                <span class="px-2.5 py-1 rounded-full text-[10.5px] font-semibold uppercase
                                    {{ $b->status === 'confirmed' ? 'bg-[#f2eadb] text-[#534227]' : ($b->status === 'cancelled' ? 'bg-red-50 text-red-700' : 'bg-stone-100 text-stone-700') }}">
                                    {{ $b->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-stone-400">No bookings yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
