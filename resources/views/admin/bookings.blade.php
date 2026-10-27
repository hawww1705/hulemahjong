@extends('layouts.admin')

@section('title', 'Bookings Management — Hule Mahjong CMS')
@section('heading', 'Reservations & Walk-Ins')
@section('subheading', 'Verify transfers, reassign automatic tables, or create walk-in bookings.')

@section('header_actions')
    <button type="button" onclick="document.getElementById('walkin-panel').classList.toggle('hidden')"
            class="px-4 py-2 rounded-full bg-[#111] hover:bg-[#c3b091] text-white hover:text-[#141310] text-xs font-semibold transition">
        + New Walk-In Booking
    </button>
@endsection

@section('content')
    <!-- Walk-In Quick Creation Form -->
    <div id="walkin-panel" class="hidden bg-white rounded-2xl border border-[#dfd3be] p-6 mb-6">
        <h2 class="text-[16px] font-bold mb-4">Create Walk-In / Phone Reservation</h2>
        <form method="POST" action="{{ route('admin.bookings.walkin') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf
            <div>
                <label class="cms-label">Suite / Category *</label>
                <select name="table_category_id" required class="cms-input">
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }} ({{ $cat->formatted_weekday_price }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="cms-label">Date *</label>
                <input type="date" name="booking_date" value="{{ now()->toDateString() }}" required class="cms-input">
            </div>
            <div>
                <label class="cms-label">Start Time *</label>
                <select name="start_time" required class="cms-input">
                    @foreach ($slots as $s)
                        <option value="{{ $s->start_time }}">{{ $s->label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="cms-label">Duration (Sessions)</label>
                <select name="sessions_count" class="cms-input">
                    <option value="1">1 Session (2h)</option>
                    <option value="2">2 Sessions (4h)</option>
                    <option value="3">3 Sessions (6h)</option>
                </select>
            </div>
            <div>
                <label class="cms-label">Guest Name *</label>
                <input type="text" name="customer_name" required placeholder="Walk-in guest" class="cms-input">
            </div>
            <div>
                <label class="cms-label">Phone / WhatsApp *</label>
                <input type="text" name="customer_phone" required placeholder="0812..." class="cms-input">
            </div>
            <div>
                <label class="cms-label">Players</label>
                <input type="number" name="players_count" value="4" min="2" max="12" class="cms-input">
            </div>
            <div>
                <label class="cms-label">Payment Status</label>
                <select name="payment_status" class="cms-input">
                    <option value="paid">Paid at Desk</option>
                    <option value="unpaid">Unpaid</option>
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-full bg-[#c3b091] hover:bg-[#b59f7c] text-[#141310] text-xs font-bold">
                    Assign Automatic Table & Save →
                </button>
            </div>
        </form>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('admin.bookings') }}" class="bg-white rounded-2xl border border-[#ebe5d9] p-4 mb-6 flex flex-wrap items-center gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search code, guest, phone..." class="cms-input sm:w-64">
        <input type="date" name="date" value="{{ request('date') }}" class="cms-input sm:w-44">
        <select name="status" class="cms-input sm:w-44">
            <option value="all">All Statuses</option>
            @foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $st)
                <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#111] text-white text-xs font-semibold">Filter</button>
        @if (request()->hasAny(['search', 'date', 'status']))
            <a href="{{ route('admin.bookings') }}" class="text-xs text-stone-500 hover:underline px-2">Reset</a>
        @endif
    </form>

    <!-- Bookings List -->
    <div class="bg-white rounded-2xl border border-[#ebe5d9] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#faf8f4] border-b border-[#ebe5d9] text-[11px] uppercase tracking-wider text-stone-500">
                    <tr>
                        <th class="py-3.5 px-4">Booking</th>
                        <th class="py-3.5 px-4">Guest</th>
                        <th class="py-3.5 px-4">Schedule</th>
                        <th class="py-3.5 px-4">Total & Proof</th>
                        <th class="py-3.5 px-4">Assign Unit / Status / Payment</th>
                        <th class="py-3.5 px-4 text-right">Delete</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($bookings as $b)
                        <tr>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <a href="{{ route('booking.show', $b->booking_code) }}" target="_blank" class="font-bold text-stone-900 hover:text-[#8c734b] underline">
                                    {{ $b->booking_code }}
                                </a>
                                <span class="block text-[11px] text-stone-400">{{ $b->category->name ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-stone-800">{{ $b->customer_name }}</span>
                                <span class="block text-stone-500">{{ $b->customer_phone }} • {{ $b->players_count }}p</span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-semibold">{{ $b->booking_date->format('d M Y') }}</span>
                                <span class="block text-stone-500">{{ $b->start_time }}–{{ $b->end_time }} ({{ $b->sessions_count }}x)</span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-bold text-stone-900">{{ $b->formatted_total }}</span>
                                @if ($b->payment_proof_url)
                                    <a href="{{ $b->payment_proof_url }}" target="_blank" class="block text-[11px] font-semibold text-emerald-700 underline mt-0.5">
                                        View Receipt ↗
                                    </a>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <form method="POST" action="{{ route('admin.bookings.update', $b) }}" class="flex flex-wrap items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="mahjong_table_id" class="rounded-lg border border-stone-200 px-2 py-1.5 text-xs">
                                        <option value="">-- Table --</option>
                                        @foreach ($allTables->where('table_category_id', $b->table_category_id) as $tbl)
                                            <option value="{{ $tbl->id }}" @selected($b->mahjong_table_id === $tbl->id)>{{ $tbl->code }}</option>
                                        @endforeach
                                    </select>

                                    <select name="status" class="rounded-lg border border-stone-200 px-2 py-1.5 text-xs">
                                        @foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $st)
                                            <option value="{{ $st }}" @selected($b->status === $st)>{{ ucfirst($st) }}</option>
                                        @endforeach
                                    </select>

                                    <select name="payment_status" class="rounded-lg border border-stone-200 px-2 py-1.5 text-xs">
                                        @foreach (['unpaid', 'waiting_verification', 'paid', 'refunded'] as $pst)
                                            <option value="{{ $pst }}" @selected($b->payment_status === $pst)>{{ str_replace('_', ' ', ucfirst($pst)) }}</option>
                                        @endforeach
                                    </select>

                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#c3b091] hover:bg-[#b59f7c] text-[#141310] font-semibold text-xs">
                                        Save
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <form method="POST" action="{{ route('admin.bookings.destroy', $b) }}" onsubmit="return confirm('Delete this booking permanently?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-stone-400">No matching bookings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-stone-100">
            {{ $bookings->links() }}
        </div>
    </div>
@endsection
