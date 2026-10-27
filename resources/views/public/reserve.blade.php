@extends('layouts.public')

@section('title', 'Reserve Mahjong Table — Hule Mahjong')

@section('content')
    <!-- Centered Breadcrumb & Stacked Two-Tone Heading (Section 2 of Reference Screenshot) -->
    <section class="pt-9 pb-11 sm:pt-14 sm:pb-16 px-5 text-center">
        <nav aria-label="Breadcrumb" class="inline-flex items-center gap-2 text-[12px] text-[#767676] mb-3.5">
            <a href="{{ route('home') }}" class="hover:text-[#141413] transition">Home</a>
            <span class="text-[#b5b5b5]">›</span>
            <a href="{{ route('tables') }}" class="hover:text-[#141413] transition">Lounge</a>
            <span class="text-[#b5b5b5]">›</span>
            <span class="font-semibold text-[#8c734b]">Book Table</span>
        </nav>

        <h1 class="text-[34px] sm:text-[42px] lg:text-[44px] leading-[1.14] tracking-[-0.03em]">
            <span class="block font-semibold text-[#111111]">Reserve</span>
            <span class="block font-light text-[#6b6b6b]">Mahjong Table</span>
        </h1>
    </section>

    <!-- Main Two-Column Interactive Booking Engine (Section 3 of Reference Screenshot) -->
    <section id="booking-section" class="max-w-[1040px] mx-auto px-5 sm:px-10 pb-20 sm:pb-28">
        @if ($categories->isEmpty())
            <div class="rounded-3xl border border-[#ece5d8] bg-[#faf8f4] p-10 text-center">
                <p class="text-base font-medium text-stone-700">No active mahjong table categories configured yet. Add tables in the Admin CMS.</p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">

                <!-- LEFT COLUMN: Interactive Slot, Date, Suite & Players Controls -->
                <div class="lg:col-span-7 space-y-8">

                    <!-- 1. Time Slots Grid (Matches SELECT TIME pill grid in reference) -->
                    <div>
                        <div class="flex items-center justify-between mb-3.5">
                            <label class="text-[10.5px] font-bold uppercase tracking-[0.09em] text-[#1a1a1a]">
                                Select Time
                            </label>
                            <span id="rate-type-badge" class="text-[11px] font-medium text-[#8c734b] bg-[#f7f2e8] px-2.5 py-0.5 rounded-full border border-[#e8dcc8]">
                                {{ $initialPricing['rate_label'] ?? 'Weekday Rate' }}
                            </span>
                        </div>

                        <div id="time-slots-grid" class="flex flex-wrap gap-2.5">
                            @foreach ($slots as $slot)
                                @php
                                    $isSelected = $slot['start_time'] === $defaultTime && $slot['available'];
                                @endphp
                                <button type="button"
                                        data-time="{{ $slot['start_time'] }}"
                                        data-available="{{ $slot['available'] ? '1' : '0' }}"
                                        @disabled(!$slot['available'])
                                        class="slot-pill min-w-[88px] sm:min-w-[94px] h-[42px] px-4 rounded-full border text-[13.5px] font-semibold transition duration-150 flex items-center justify-center gap-1
                                        {{ !$slot['available']
                                            ? 'border-[#efefef] bg-[#fcfcfc] text-[#c4c4c4] cursor-not-allowed'
                                            : ($isSelected
                                                ? 'border-[#c3b091] bg-[#f2eadb] text-[#534227] shadow-[0_2px_8px_rgba(195,176,145,0.22)]'
                                                : 'border-[#e5e5e5] bg-white text-[#181818] hover:border-[#c3b091]') }}">
                                    <span>{{ $slot['label'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- 2. 7-Day Date Cards Strip (Matches AVAILABLE SLOTS Sat 24..Fri 30 vertical cards) -->
                    <div>
                        <div class="flex items-center justify-between mb-3.5">
                            <label class="text-[10.5px] font-bold uppercase tracking-[0.09em] text-[#1a1a1a]">
                                Available Dates
                            </label>
                            <div class="inline-flex items-center gap-1.5">
                                <span id="current-month-label" class="text-[11.5px] font-medium text-[#707070] mr-1"></span>
                                <button type="button" id="prev-week-btn" aria-label="Previous 7 days"
                                        class="w-7 h-7 rounded-full border border-[#e6e6e6] hover:border-[#c3b091] flex items-center justify-center text-xs text-[#444] disabled:opacity-30 disabled:cursor-not-allowed transition">
                                    ‹
                                </button>
                                <button type="button" id="next-week-btn" aria-label="Next 7 days"
                                        class="w-7 h-7 rounded-full border border-[#e6e6e6] hover:border-[#c3b091] flex items-center justify-center text-xs text-[#444] disabled:opacity-30 disabled:cursor-not-allowed transition">
                                    ›
                                </button>
                            </div>
                        </div>

                        <div id="dates-strip" class="grid grid-cols-7 gap-1.5 sm:gap-2.5">
                            <!-- Populated & hydrated cleanly by JS from $dates -->
                        </div>
                    </div>

                    <!-- 3. Table / Suite Selector + Players & Session Duration (Matches sleek rounded pill select) -->
                    <div class="space-y-5 pt-1">
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <label for="category-select" class="text-[10.5px] font-bold uppercase tracking-[0.09em] text-[#1a1a1a]">
                                    Table / Suite Category
                                </label>
                                <a href="{{ route('tables') }}" class="text-[11px] font-medium text-[#8c734b] hover:underline">
                                    Compare Suites →
                                </a>
                            </div>
                            <select id="category-select" class="pill-select">
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected($selectedCategory && $selectedCategory->id === $cat->id)>
                                        {{ $cat->name }} ({{ $cat->branch }}) — {{ $cat->formatted_weekday_price }}/sesi
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="players-select" class="block text-[10.5px] font-bold uppercase tracking-[0.09em] text-[#1a1a1a] mb-2.5">
                                    Players
                                </label>
                                <select id="players-select" class="pill-select">
                                    <option value="4" selected>4 Players (Standard Table)</option>
                                    <option value="5">5 Players (+1 Rotating)</option>
                                    <option value="6">6 Players (Suite Group)</option>
                                </select>
                            </div>

                            <div>
                                <label for="sessions-select" class="block text-[10.5px] font-bold uppercase tracking-[0.09em] text-[#1a1a1a] mb-2.5">
                                    Duration
                                </label>
                                <select id="sessions-select" class="pill-select">
                                    <option value="1" selected>1 Session (2 Hours)</option>
                                    <option value="2">2 Sessions (4 Hours)</option>
                                    <option value="3">3 Sessions (6 Hours)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Optional Promo Voucher Input -->
                        <div class="pt-1">
                            <button type="button" id="toggle-promo-btn" class="text-[12px] font-medium text-[#7c6642] hover:text-[#534227] inline-flex items-center gap-1.5">
                                <span>Have a promo code?</span>
                                <span class="text-[11px] text-[#9b896b]">(e.g. HULE10, FIRSTROUND)</span>
                            </button>
                            <div id="promo-box" class="hidden mt-2.5 flex items-center gap-2">
                                <input type="text" id="promo-input" placeholder="Enter voucher code"
                                       class="h-[42px] flex-1 rounded-full border border-[#e5e5e5] px-4 text-[13px] uppercase tracking-wider focus:border-[#c3b091] focus:outline-none">
                                <button type="button" id="apply-promo-btn"
                                        class="h-[42px] px-5 rounded-full bg-[#f2eadb] hover:bg-[#e7dcc8] text-[#534227] text-[12.5px] font-semibold transition">
                                    Apply
                                </button>
                            </div>
                            <p id="promo-feedback" class="mt-1.5 text-[11.5px] hidden"></p>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Elevated Summary Card (Matches Summary Card in Reference Screenshot) -->
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-[24px] p-6 sm:p-7 border border-[#f0ede6] shadow-summary lg:sticky lg:top-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-[20px] font-semibold text-[#141414] tracking-[-0.02em]">Summary</h2>
                            <span id="summary-availability-pill" class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-[#f5efe4] text-[#6e5b3e]">
                                Checking...
                            </span>
                        </div>

                        <!-- Featured Suite / Lounge Photo -->
                        <div class="relative rounded-[14px] overflow-hidden mb-6 bg-[#f6f4f0] aspect-[16/10]">
                            <img id="summary-image"
                                 src="{{ $selectedCategory->image_url }}"
                                 alt="{{ $selectedCategory->name }}"
                                 class="w-full h-full object-cover transition duration-300">
                            <div class="absolute bottom-2.5 left-2.5 bg-black/55 backdrop-blur-md text-white text-[10.5px] font-medium px-3 py-1 rounded-full">
                                <span id="summary-branch">{{ $selectedCategory->branch }}</span>
                            </div>
                        </div>

                        <!-- Breakdown Key-Value Rows -->
                        <div class="divide-y divide-[#f1f0ec] text-[13px]">
                            <div class="py-3 flex items-center justify-between gap-4">
                                <span class="text-[10.5px] font-medium uppercase tracking-[0.07em] text-[#7a7a7a]">Date</span>
                                <span id="summary-date" class="font-semibold text-[#151515]">
                                    {{ \Carbon\Carbon::parse($defaultDate)->format('M d, Y') }}
                                </span>
                            </div>

                            <div class="py-3 flex items-center justify-between gap-4">
                                <span class="text-[10.5px] font-medium uppercase tracking-[0.07em] text-[#7a7a7a]">Time</span>
                                <span id="summary-time" class="font-semibold text-[#151515]">
                                    {{ $defaultTime }} – {{ $initialPricing['end_time'] ?? '12:00' }} WIB
                                </span>
                            </div>

                            <div class="py-3 flex items-center justify-between gap-4">
                                <span class="text-[10.5px] font-medium uppercase tracking-[0.07em] text-[#7a7a7a]">Players</span>
                                <span id="summary-players" class="font-semibold text-[#151515]">4 Total</span>
                            </div>

                            <div class="py-3 flex items-center justify-between gap-4">
                                <span class="text-[10.5px] font-medium uppercase tracking-[0.07em] text-[#7a7a7a]">Suite / Table</span>
                                <span id="summary-category" class="font-semibold text-[#151515] text-right">
                                    {{ $selectedCategory->name }}
                                </span>
                            </div>

                            <div id="summary-discount-row" class="py-3 hidden items-center justify-between gap-4">
                                <span class="text-[10.5px] font-medium uppercase tracking-[0.07em] text-[#8c734b]">Promo Discount</span>
                                <span id="summary-discount" class="font-semibold text-[#6e5b3e]">-Rp 0</span>
                            </div>
                        </div>

                        <!-- Total Row -->
                        <div class="pt-4 pb-5 border-t border-[#f1f0ec] flex items-baseline justify-between">
                            <span class="text-[13px] text-[#6b6b6b]">Total</span>
                            <span id="summary-total" class="text-[23px] font-bold text-[#111111] tracking-[-0.02em]">
                                {{ $initialPricing['formatted_total'] ?? $selectedCategory->formatted_weekday_price }}
                            </span>
                        </div>

                        <!-- Primary Confirm Button -->
                        <button type="button" id="open-confirm-modal-btn"
                                class="w-full h-[48px] rounded-full bg-[#111111] hover:bg-[#2a241b] text-white text-[13.5px] font-medium transition shadow-sm disabled:opacity-40 disabled:cursor-not-allowed">
                            Confirm Booking
                        </button>

                        <p class="text-[11px] text-[#8a8a8a] text-center mt-3.5">
                            {{ $siteSettings['cancellation_note'] ?? 'Free rescheduling up to 24h before session time.' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Guest Details & Confirmation Modal -->
            <div id="booking-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/55 backdrop-blur-xs">
                <div class="bg-white rounded-[26px] max-w-[500px] w-full p-6 sm:p-8 shadow-2xl border border-[#ece5d8] relative max-h-[92vh] overflow-y-auto">
                    <button type="button" id="close-booking-modal" aria-label="Close"
                            class="absolute top-5 right-5 w-9 h-9 rounded-full bg-[#f6f4f0] hover:bg-[#ebe3d4] flex items-center justify-center text-[#333] transition">
                        ✕
                    </button>

                    <p class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-[#8c734b] mb-1">Complete Reservation</p>
                    <h3 class="text-[22px] font-semibold text-[#141414] tracking-[-0.02em]">Guest Information</h3>

                    <!-- Mini Summary Strip inside Modal -->
                    <div class="mt-4 mb-5 rounded-2xl bg-[#faf7f2] border border-[#ebe2d2] p-4 flex items-center justify-between gap-3 text-[12.5px]">
                        <div>
                            <p id="modal-summary-title" class="font-semibold text-[#171614]">{{ $selectedCategory->name }}</p>
                            <p id="modal-summary-datetime" class="text-[#6b665e] mt-0.5"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] uppercase tracking-wider text-[#8c734b] font-semibold">Total</p>
                            <p id="modal-summary-price" class="text-[16px] font-bold text-[#141414]"></p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('booking.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="table_category_id" id="form-category-id" value="{{ $selectedCategory->id }}">
                        <input type="hidden" name="booking_date" id="form-date" value="{{ $defaultDate }}">
                        <input type="hidden" name="start_time" id="form-time" value="{{ $defaultTime }}">
                        <input type="hidden" name="sessions_count" id="form-sessions" value="1">
                        <input type="hidden" name="players_count" id="form-players" value="4">
                        <input type="hidden" name="promo_code" id="form-promo" value="">

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-[#333] mb-1.5">Full Name *</label>
                            <input type="text" name="customer_name" required placeholder="e.g. Clarissa Tanoto"
                                   value="{{ old('customer_name') }}"
                                   class="w-full h-[44px] rounded-xl border border-[#e3dfd7] px-4 text-[13.5px] focus:border-[#c3b091] focus:outline-none">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-[#333] mb-1.5">WhatsApp / Phone *</label>
                                <input type="text" name="customer_phone" required placeholder="0812xxxxxxxx"
                                       value="{{ old('customer_phone') }}"
                                       class="w-full h-[44px] rounded-xl border border-[#e3dfd7] px-4 text-[13.5px] focus:border-[#c3b091] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-[#333] mb-1.5">Email (Optional)</label>
                                <input type="email" name="customer_email" placeholder="you@email.com"
                                       value="{{ old('customer_email') }}"
                                       class="w-full h-[44px] rounded-xl border border-[#e3dfd7] px-4 text-[13.5px] focus:border-[#c3b091] focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-[#333] mb-1.5">Payment Preference</label>
                            <select name="payment_method" class="pill-select !rounded-xl !h-[44px]">
                                <option value="qris_transfer">QRIS / Bank BCA Transfer</option>
                                <option value="pay_at_lounge">Pay at Concierge Desk (Hold 15m before slot)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-[#333] mb-1.5">Special Requests (Optional)</label>
                            <textarea name="notes" rows="2" placeholder="e.g. Need beginner rule card, hot tieguanyin tea pot..."
                                      class="w-full rounded-xl border border-[#e3dfd7] p-3.5 text-[13px] focus:border-[#c3b091] focus:outline-none">{{ old('notes') }}</textarea>
                        </div>

                        <p class="text-[11px] text-[#7a746a] bg-[#faf8f4] rounded-xl px-3.5 py-2.5 border border-[#eee7da]">
                            By confirming, you agree to Hule Mahjong’s house etiquette: strictly social & recreational strategy play (zero gambling).
                        </p>

                        <button type="submit"
                                class="w-full h-[48px] rounded-full bg-[#111111] hover:bg-[#c3b091] hover:text-[#141413] text-white font-semibold text-[13.5px] transition">
                            Lock Automatic Table Now →
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </section>
@endsection

@push('scripts')
@if ($categories->isNotEmpty())
<script>
    (function () {
        const allDates = @json($dates);
        const availabilityUrl = @json(route('api.availability'));

        let state = {
            categoryId: Number(document.getElementById('category-select').value),
            date: @json($defaultDate),
            time: @json($defaultTime),
            sessions: 1,
            players: 4,
            promo: '',
            weekPage: 0,
            slotAvailable: true,
        };

        // Determine initial weekPage containing defaultDate
        const initialDateIdx = allDates.findIndex(d => d.date === state.date);
        if (initialDateIdx >= 0) {
            state.weekPage = Math.floor(initialDateIdx / 7);
        }

        const datesStrip = document.getElementById('dates-strip');
        const prevWeekBtn = document.getElementById('prev-week-btn');
        const nextWeekBtn = document.getElementById('next-week-btn');
        const monthLabel = document.getElementById('current-month-label');
        const slotsGrid = document.getElementById('time-slots-grid');
        const categorySelect = document.getElementById('category-select');
        const playersSelect = document.getElementById('players-select');
        const sessionsSelect = document.getElementById('sessions-select');

        const summaryImage = document.getElementById('summary-image');
        const summaryBranch = document.getElementById('summary-branch');
        const summaryDate = document.getElementById('summary-date');
        const summaryTime = document.getElementById('summary-time');
        const summaryPlayers = document.getElementById('summary-players');
        const summaryCategory = document.getElementById('summary-category');
        const summaryTotal = document.getElementById('summary-total');
        const summaryAvailPill = document.getElementById('summary-availability-pill');
        const summaryDiscountRow = document.getElementById('summary-discount-row');
        const summaryDiscount = document.getElementById('summary-discount');
        const rateTypeBadge = document.getElementById('rate-type-badge');
        const confirmBtn = document.getElementById('open-confirm-modal-btn');

        function renderDateStrip() {
            const start = state.weekPage * 7;
            const slice = allDates.slice(start, start + 7);
            prevWeekBtn.disabled = state.weekPage === 0;
            nextWeekBtn.disabled = (start + 7) >= allDates.length;

            if (slice.length > 0) {
                monthLabel.textContent = slice[0].month_short + (slice[0].month_short !== slice[slice.length - 1].month_short ? ' – ' + slice[slice.length - 1].month_short : '');
            }

            datesStrip.innerHTML = slice.map(item => {
                const active = item.date === state.date;
                const cls = active
                    ? 'border-[#c3b091] bg-[#f2eadb] text-[#534227] shadow-[0_2px_8px_rgba(195,176,145,0.2)]'
                    : 'border-[#e6e6e6] bg-white text-[#181818] hover:border-[#c3b091]';
                const dayCls = active ? 'text-[#6e5b3e]' : 'text-[#6b6b6b]';
                return `
                    <button type="button" data-date="${item.date}"
                        class="date-card h-[84px] sm:h-[88px] rounded-[14px] border flex flex-col items-center justify-between py-3.5 px-1 transition duration-150 ${cls}">
                        <span class="text-[11px] sm:text-[11.5px] font-medium ${dayCls}">${item.day_short}</span>
                        <span class="text-[15px] font-bold">${item.day_num}</span>
                    </button>
                `;
            }).join('');

            datesStrip.querySelectorAll('.date-card').forEach(btn => {
                btn.addEventListener('click', () => {
                    state.date = btn.dataset.date;
                    renderDateStrip();
                    refreshAvailability();
                });
            });
        }

        function renderSlots(slots) {
            slotsGrid.innerHTML = slots.map(slot => {
                const active = slot.available && slot.start_time === state.time;
                let cls = 'border-[#e5e5e5] bg-white text-[#181818] hover:border-[#c3b091]';
                if (!slot.available) {
                    cls = 'border-[#efefef] bg-[#fcfcfc] text-[#c4c4c4] cursor-not-allowed';
                } else if (active) {
                    cls = 'border-[#c3b091] bg-[#f2eadb] text-[#534227] shadow-[0_2px_8px_rgba(195,176,145,0.22)]';
                }
                const peakDot = slot.is_peak && slot.available
                    ? '<span class="w-1.5 h-1.5 rounded-full bg-[#c3b091]" title="Evening Peak Slot"></span>'
                    : '';
                return `
                    <button type="button"
                        data-time="${slot.start_time}"
                        ${!slot.available ? 'disabled' : ''}
                        class="slot-pill min-w-[88px] sm:min-w-[94px] h-[42px] px-4 rounded-full border text-[13.5px] font-semibold transition duration-150 flex items-center justify-center gap-1.5 ${cls}">
                        <span>${slot.label}</span>
                        ${peakDot}
                    </button>
                `;
            }).join('');

            slotsGrid.querySelectorAll('.slot-pill').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (btn.disabled) return;
                    state.time = btn.dataset.time;
                    refreshAvailability();
                });
            });
        }

        async function refreshAvailability() {
            const params = new URLSearchParams({
                table_category_id: state.categoryId,
                date: state.date,
                sessions_count: state.sessions,
                start_time: state.time,
                promo_code: state.promo,
            });

            try {
                const res = await fetch(`${availabilityUrl}?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const data = await res.json();

                state.time = data.selected_time;
                state.slotAvailable = Boolean(data.selected_slot_available);

                renderSlots(data.slots);

                summaryImage.src = data.category.image_url;
                summaryImage.alt = data.category.name;
                summaryBranch.textContent = data.category.branch;
                summaryDate.textContent = data.date_formatted;
                summaryTime.textContent = `${data.pricing.start_time} – ${data.pricing.end_time} WIB`;
                summaryPlayers.textContent = `${state.players} Total`;
                summaryCategory.textContent = data.category.name;
                summaryTotal.textContent = data.pricing.formatted_total;

                rateTypeBadge.textContent = data.pricing.rate_label + (data.pricing.is_peak ? ' • Peak' : '');

                if (data.pricing.discount_amount > 0) {
                    summaryDiscountRow.classList.remove('hidden');
                    summaryDiscountRow.classList.add('flex');
                    summaryDiscount.textContent = `-${data.pricing.formatted_discount}`;
                } else {
                    summaryDiscountRow.classList.add('hidden');
                    summaryDiscountRow.classList.remove('flex');
                }

                const promoFeedback = document.getElementById('promo-feedback');
                if (state.promo && data.pricing.promo_message) {
                    promoFeedback.classList.remove('hidden');
                    promoFeedback.textContent = data.pricing.promo_message;
                    promoFeedback.className = `mt-1.5 text-[11.5px] font-medium ${data.pricing.promo_valid ? 'text-[#5c7a3e]' : 'text-red-600'}`;
                } else {
                    promoFeedback.classList.add('hidden');
                }

                if (state.slotAvailable && data.remaining_tables > 0) {
                    summaryAvailPill.textContent = `${data.remaining_tables} Table${data.remaining_tables > 1 ? 's' : ''} Available`;
                    summaryAvailPill.className = 'text-[11px] font-semibold px-2.5 py-1 rounded-full bg-[#f5efe4] text-[#6e5b3e]';
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Confirm Booking';
                } else {
                    summaryAvailPill.textContent = 'Fully Booked';
                    summaryAvailPill.className = 'text-[11px] font-semibold px-2.5 py-1 rounded-full bg-red-50 text-red-700';
                    confirmBtn.disabled = true;
                    confirmBtn.textContent = 'No Tables Left on This Date';
                }

                // Sync hidden form inputs
                document.getElementById('form-category-id').value = state.categoryId;
                document.getElementById('form-date').value = state.date;
                document.getElementById('form-time').value = state.time;
                document.getElementById('form-sessions').value = state.sessions;
                document.getElementById('form-players').value = state.players;
                document.getElementById('form-promo').value = data.pricing.promo_valid ? state.promo : '';

                document.getElementById('modal-summary-title').textContent = `${data.category.name} (${data.category.branch})`;
                document.getElementById('modal-summary-datetime').textContent = `${data.date_formatted} • ${data.pricing.start_time}–${data.pricing.end_time} WIB • ${state.players} Players`;
                document.getElementById('modal-summary-price').textContent = data.pricing.formatted_total;

            } catch (err) {
                console.error(err);
            }
        }

        prevWeekBtn.addEventListener('click', () => {
            if (state.weekPage > 0) {
                state.weekPage--;
                renderDateStrip();
            }
        });

        nextWeekBtn.addEventListener('click', () => {
            if ((state.weekPage + 1) * 7 < allDates.length) {
                state.weekPage++;
                renderDateStrip();
            }
        });

        categorySelect.addEventListener('change', () => {
            state.categoryId = Number(categorySelect.value);
            refreshAvailability();
        });

        playersSelect.addEventListener('change', () => {
            state.players = Number(playersSelect.value);
            summaryPlayers.textContent = `${state.players} Total`;
            document.getElementById('form-players').value = state.players;
        });

        sessionsSelect.addEventListener('change', () => {
            state.sessions = Number(sessionsSelect.value);
            refreshAvailability();
        });

        document.getElementById('toggle-promo-btn').addEventListener('click', () => {
            document.getElementById('promo-box').classList.toggle('hidden');
        });

        document.getElementById('apply-promo-btn').addEventListener('click', () => {
            state.promo = document.getElementById('promo-input').value.trim();
            refreshAvailability();
        });

        // Modal open/close
        const modal = document.getElementById('booking-modal');
        confirmBtn.addEventListener('click', () => {
            if (!state.slotAvailable) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });

        document.getElementById('close-booking-modal').addEventListener('click', () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });

        renderDateStrip();
        refreshAvailability();
    })();
</script>
@endif
@endpush
