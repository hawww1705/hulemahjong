@extends('layouts.public')

@section('title', 'Booking Pass ' . $booking->booking_code . ' — Hule Mahjong')

@section('content')
    @php
        $brandPrefix = $siteSettings['brand_prefix'] ?? 'Hule';
        $brandSuffix = $siteSettings['brand_suffix'] ?? 'Mahjong';
        $waNumber = preg_replace('/\D/', '', $siteSettings['contact_whatsapp'] ?? '6281288997788');
        $waMessage = "Hi Hule Mahjong Concierge! Here is my reservation:\n"
            . "Code: {$booking->booking_code}\n"
            . "Name: {$booking->customer_name}\n"
            . "Suite: " . ($booking->category->name ?? '-') . " (" . ($booking->table->code ?? 'Assigned at lounge') . ")\n"
            . "Date: " . $booking->booking_date->format('d M Y') . "\n"
            . "Time: {$booking->start_time} - {$booking->end_time} WIB\n"
            . "Total: {$booking->formatted_total}";
    @endphp

    <!-- =========================================================
         SCREEN VIEW (Interactive Digital Pass + Upload Form)
         Hidden automatically during Print/PDF via .no-print
         ========================================================= -->
    <div class="no-print">
        <section class="pt-9 pb-10 sm:pt-14 sm:pb-12 px-5 text-center">
            <nav class="inline-flex items-center gap-2 text-[12px] text-[#767676] mb-3.5">
                <a href="{{ route('home') }}" class="hover:text-[#141413]">Home</a>
                <span class="text-[#b5b5b5]">›</span>
                <span class="font-semibold text-[#8c734b]">Booking Pass</span>
            </nav>

            <h1 class="text-[32px] sm:text-[42px] leading-[1.14] tracking-[-0.03em]">
                <span class="block font-semibold text-[#111111]">Reservation</span>
                <span class="block font-light text-[#6b6b6b]">Digital Pass</span>
            </h1>
        </section>

        <section class="max-w-[960px] mx-auto px-5 sm:px-10 pb-20 sm:pb-24">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- Digital Ticket Card -->
                <div class="lg:col-span-7 bg-white rounded-[26px] border border-[#ebe3d4] shadow-summary overflow-hidden">
                    <div class="bg-[#0e0f0d] text-white px-6 sm:px-8 py-6 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.16em] text-[#c3b091] font-semibold">Booking Reference</p>
                            <p class="text-[22px] sm:text-[26px] font-bold tracking-tight mt-0.5">{{ $booking->booking_code }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-[11px] font-semibold uppercase tracking-wider
                                {{ $booking->status === 'cancelled' ? 'bg-red-500/20 text-red-300' : 'bg-[#c3b091] text-[#141310]' }}">
                                {{ $booking->status }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-[11px] font-medium border border-white/25 text-white/90 uppercase tracking-wider">
                                {{ str_replace('_', ' ', $booking->payment_status) }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8 space-y-6">
                        <div class="flex items-center gap-4 pb-6 border-b border-[#f1eee8]">
                            <img src="{{ $booking->category->image_url ?? asset('images/mahjong/lounge-regular.jpg') }}"
                                 alt="{{ $booking->category->name ?? 'Suite' }}"
                                 class="w-24 h-16 rounded-xl object-cover shrink-0">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wider text-[#8c734b]">
                                    {{ $booking->category->branch ?? 'Hule Mahjong Lounge' }}
                                </p>
                                <h2 class="text-[18px] font-semibold text-[#141414]">{{ $booking->category->name ?? 'Mahjong Table' }}</h2>
                                <p class="text-[12.5px] text-[#666] mt-0.5">
                                    Assigned Table: <strong class="text-[#141414]">{{ $booking->table ? $booking->table->code . ' (' . $booking->table->name . ')' : 'Assigned on arrival' }}</strong>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-5 text-[13px]">
                            <div>
                                <p class="text-[10.5px] uppercase tracking-wider text-[#888]">Guest Name</p>
                                <p class="font-semibold text-[#151515] mt-1">{{ $booking->customer_name }}</p>
                            </div>
                            <div>
                                <p class="text-[10.5px] uppercase tracking-wider text-[#888]">WhatsApp</p>
                                <p class="font-semibold text-[#151515] mt-1">{{ $booking->customer_phone }}</p>
                            </div>
                            <div>
                                <p class="text-[10.5px] uppercase tracking-wider text-[#888]">Players</p>
                                <p class="font-semibold text-[#151515] mt-1">{{ $booking->players_count }} Players</p>
                            </div>
                            <div>
                                <p class="text-[10.5px] uppercase tracking-wider text-[#888]">Date</p>
                                <p class="font-semibold text-[#151515] mt-1">{{ $booking->booking_date->format('D, M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-[10.5px] uppercase tracking-wider text-[#888]">Session Time</p>
                                <p class="font-semibold text-[#151515] mt-1">{{ $booking->start_time }} – {{ $booking->end_time }} WIB</p>
                            </div>
                            <div>
                                <p class="text-[10.5px] uppercase tracking-wider text-[#888]">Duration</p>
                                <p class="font-semibold text-[#151515] mt-1">{{ $booking->sessions_count }} Session ({{ $booking->duration_minutes / 60 }}h)</p>
                            </div>
                        </div>

                        @if ($booking->notes)
                            <div class="rounded-xl bg-[#faf8f4] border border-[#eee7da] px-4 py-3 text-[12.5px] text-[#5c5549]">
                                <span class="font-semibold text-[#171614]">Notes:</span> {{ $booking->notes }}
                            </div>
                        @endif

                        <div class="pt-4 border-t border-[#f1eee8] space-y-2 text-[13px]">
                            <div class="flex justify-between text-[#6b6b6b]">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($booking->base_price, 0, ',', '.') }}</span>
                            </div>
                            @if ($booking->discount_amount > 0)
                                <div class="flex justify-between text-[#6e5b3e]">
                                    <span>Promo ({{ $booking->promo_code }})</span>
                                    <span>-Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-baseline pt-2 text-[#111]">
                                <span class="font-semibold">Total Amount</span>
                                <span class="text-[22px] font-bold">{{ $booking->formatted_total }}</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3 pt-2">
                            <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode($waMessage) }}" target="_blank" rel="noopener"
                               class="flex-1 min-w-[200px] h-[46px] rounded-full bg-[#c3b091] hover:bg-[#b59f7c] text-[#171512] font-semibold text-[13px] inline-flex items-center justify-center gap-2 transition">
                                <span>Confirm via WhatsApp</span>
                                <span>↗</span>
                            </a>
                            <button type="button" onclick="window.print()"
                                    class="h-[46px] px-6 rounded-full bg-[#111111] hover:bg-[#2a241b] text-white text-[13px] font-medium inline-flex items-center justify-center gap-2 transition">
                                <span>Print / Save 1-Page PDF</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Payment & Transfer Instructions -->
                <div class="lg:col-span-5 space-y-5">
                    <div class="bg-[#faf8f4] rounded-[24px] p-6 sm:p-7 border border-[#ebe3d4]">
                        <p class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-[#8c734b]">Payment Settlement</p>
                        <h3 class="text-[18px] font-semibold text-[#141414] mt-1">Transfer & Verification</h3>
                        <p class="text-[12.5px] text-[#666] mt-1.5 leading-relaxed">
                            Transfer the exact booking amount below and upload your transfer receipt to lock your automatic mahjong table immediately.
                        </p>

                        <div class="mt-5 rounded-2xl bg-white border border-[#e8dfd0] p-4 space-y-2 text-[13px]">
                            <div class="flex justify-between">
                                <span class="text-[#777]">Bank</span>
                                <span class="font-semibold">{{ $siteSettings['payment_bank_name'] ?? 'BCA (Bank Central Asia)' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#777]">Account No.</span>
                                <span class="font-bold tracking-wide text-[#111]">{{ $siteSettings['payment_account_number'] ?? '8830-9918-27' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#777]">Account Name</span>
                                <span class="font-medium">{{ $siteSettings['payment_account_holder'] ?? 'PT Hule Mahjong Indonesia' }}</span>
                            </div>
                        </div>

                        @if ($booking->payment_proof_url)
                            <div class="mt-5">
                                <p class="text-[11px] font-semibold uppercase tracking-wider text-[#5c7a3e] mb-2">✓ Receipt Uploaded</p>
                                <img src="{{ $booking->payment_proof_url }}" alt="Payment Receipt" class="w-full max-h-52 object-cover rounded-xl border border-[#e5dec9]">
                            </div>
                        @endif

                        @if ($booking->payment_status !== 'paid')
                            <form method="POST" action="{{ route('booking.proof', $booking->booking_code) }}" enctype="multipart/form-data" class="mt-5 space-y-3">
                                @csrf
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-[#333]">
                                    {{ $booking->payment_proof_url ? 'Replace Payment Proof' : 'Upload Payment Proof (JPG/PNG)' }}
                                </label>
                                <input type="file" name="payment_proof" accept="image/*" required
                                       class="block w-full text-[12.5px] text-stone-600 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#ebe1cf] file:text-[#4f3e22] hover:file:bg-[#dfd1b8]">
                                <button type="submit" class="w-full h-[44px] rounded-full bg-[#111111] hover:bg-[#2a241b] text-white text-[13px] font-medium transition">
                                    Submit Payment Receipt
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            </div>
        </section>
    </div>

    <!-- =========================================================
         DEDICATED 1-PAGE PRINT / PDF SHEET
         Engineered to fit strictly on 1 sheet in BOTH Landscape & Portrait
         ========================================================= -->
    <div id="print-pass-sheet" class="hidden print-only-sheet max-w-[960px] mx-auto bg-white text-[#141413]">
        <div class="rounded-[18px] border-2 border-[#c3b091] overflow-hidden">

            <!-- Print Top Header Bar -->
            <div class="bg-[#0e0f0d] text-white px-6 py-4 flex items-center justify-between gap-4 border-b-2 border-[#c3b091]">
                <div>
                    <div class="flex items-baseline gap-1 text-[21px] tracking-[-0.03em]">
                        <span class="font-light text-white">{{ $brandPrefix }}</span><span class="font-bold text-[#c3b091]">{{ $brandSuffix }}</span>
                        <span class="ml-2 text-[9.5px] uppercase tracking-[0.18em] text-[#e7dcc8] font-semibold border border-[#c3b091]/50 px-2.5 py-0.5 rounded-full">
                            Official Reservation Pass
                        </span>
                    </div>
                    <p class="text-[10.5px] text-white/70 mt-1">
                        {{ $siteSettings['contact_address'] ?? 'Rukan La Riviera PIK 2, Tangerang & Jl. M. Isa, Palembang' }} • {{ $siteSettings['contact_phone'] ?? '+62 812-8899-7788' }}
                    </p>
                </div>

                <div class="text-right">
                    <p class="text-[9.5px] uppercase tracking-[0.16em] text-[#c3b091] font-bold">Booking Reference</p>
                    <p class="text-[20px] font-extrabold tracking-tight text-white leading-tight">{{ $booking->booking_code }}</p>
                    <div class="mt-1 inline-flex items-center gap-1.5">
                        <span class="px-2.5 py-0.5 rounded-full text-[9.5px] font-bold uppercase tracking-wider bg-[#c3b091] text-[#141310] border border-[#c3b091]">
                            {{ $booking->status }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[9.5px] font-bold uppercase tracking-wider bg-white text-[#141310] border border-stone-300">
                            {{ str_replace('_', ' ', $booking->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Print Compact 2-Column Boarding-Pass Body -->
            <div class="p-6 grid grid-cols-12 gap-6 items-stretch bg-white">

                <!-- Left 8 Cols: Suite & Guest Details -->
                <div class="col-span-8 flex flex-col justify-between space-y-4">
                    <div class="flex items-center gap-4 pb-3.5 border-b border-[#ece5d8]">
                        <img src="{{ $booking->category->image_url ?? asset('images/mahjong/lounge-regular.jpg') }}"
                             alt="Suite"
                             class="w-20 h-14 rounded-xl object-cover border border-[#dfd3be] shrink-0">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#8c734b]">
                                {{ $booking->category->branch ?? 'Hule Mahjong Lounge' }}
                            </p>
                            <h2 class="text-[17px] font-bold text-[#141413] leading-snug">
                                {{ $booking->category->name ?? 'Automatic Mahjong Table' }}
                            </h2>
                            <p class="text-[11.5px] text-[#4a463f]">
                                Assigned Unit: <strong class="text-[#141413]">{{ $booking->table ? $booking->table->code . ' (' . $booking->table->name . ')' : 'Assigned at Concierge' }}</strong>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-y-3.5 gap-x-4 text-[12px]">
                        <div>
                            <p class="text-[9.5px] font-bold uppercase tracking-wider text-[#888277]">Guest Name</p>
                            <p class="font-bold text-[#141413] mt-0.5">{{ $booking->customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-[9.5px] font-bold uppercase tracking-wider text-[#888277]">WhatsApp / Phone</p>
                            <p class="font-semibold text-[#141413] mt-0.5">{{ $booking->customer_phone }}</p>
                        </div>
                        <div>
                            <p class="text-[9.5px] font-bold uppercase tracking-wider text-[#888277]">Players</p>
                            <p class="font-semibold text-[#141413] mt-0.5">{{ $booking->players_count }} Players</p>
                        </div>
                        <div>
                            <p class="text-[9.5px] font-bold uppercase tracking-wider text-[#888277]">Reservation Date</p>
                            <p class="font-bold text-[#141413] mt-0.5">{{ $booking->booking_date->format('D, M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[9.5px] font-bold uppercase tracking-wider text-[#888277]">Session Time</p>
                            <p class="font-bold text-[#141413] mt-0.5">{{ $booking->start_time }} – {{ $booking->end_time }} WIB</p>
                        </div>
                        <div>
                            <p class="text-[9.5px] font-bold uppercase tracking-wider text-[#888277]">Duration</p>
                            <p class="font-semibold text-[#141413] mt-0.5">{{ $booking->sessions_count }} Session ({{ $booking->duration_minutes / 60 }}h)</p>
                        </div>
                    </div>

                    @if ($booking->notes)
                        <div class="rounded-lg bg-[#faf7f1] border border-[#e7dcc8] px-3 py-1.5 text-[11px] text-[#534227]">
                            <strong>Guest Note:</strong> {{ Str::limit($booking->notes, 120) }}
                        </div>
                    @endif

                    <div class="pt-2 border-t border-[#f0ebe1] text-[10px] text-[#6d675d]">
                        • Present this pass at the Hule Concierge Desk 10 minutes prior to session time. {{ $siteSettings['cancellation_note'] ?? 'Free rescheduling up to 24h before session time.' }}<br>
                        • {{ $siteSettings['house_policy'] ?? 'Strictly recreational & strategic point play. Gambling of any kind is prohibited.' }}
                    </div>
                </div>

                <!-- Right 4 Cols: Fare Summary, Bank Settlement & Verification Stub -->
                <div class="col-span-4 rounded-2xl bg-[#faf7f1] border border-[#e5d9c3] p-4 flex flex-col justify-between">
                    <div class="space-y-2 text-[11.5px]">
                        <p class="text-[9.5px] font-bold uppercase tracking-[0.14em] text-[#8c734b]">Payment Summary</p>

                        <div class="flex justify-between text-[#5e584e]">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($booking->base_price, 0, ',', '.') }}</span>
                        </div>
                        @if ($booking->discount_amount > 0)
                            <div class="flex justify-between text-[#6e5b3e] font-medium">
                                <span>Promo ({{ $booking->promo_code }})</span>
                                <span>-Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="pt-2 border-t border-[#e5d9c3] flex justify-between items-baseline">
                            <span class="font-bold text-[#141413]">Total Amount</span>
                            <span class="text-[18px] font-extrabold text-[#141413]">{{ $booking->formatted_total }}</span>
                        </div>
                    </div>

                    <div class="my-3 rounded-xl bg-white border border-[#e5d9c3] p-3 text-[10.5px] space-y-1">
                        <p class="font-bold uppercase tracking-wider text-[#8c734b] text-[9px]">Transfer Settlement</p>
                        <p class="font-semibold text-[#141413]">{{ $siteSettings['payment_bank_name'] ?? 'BCA (Bank Central Asia)' }}</p>
                        <p class="font-bold text-[12px] tracking-wide text-[#141413]">{{ $siteSettings['payment_account_number'] ?? '8830-9918-27' }}</p>
                        <p class="text-[#666]">a/n {{ $siteSettings['payment_account_holder'] ?? 'PT Hule Mahjong Indonesia' }}</p>
                    </div>

                    <!-- Clean Barcode / Pass Verification Strip -->
                    <div class="text-center pt-1">
                        <svg class="w-full h-7 mx-auto text-[#141413]" viewBox="0 0 220 28" fill="currentColor" aria-hidden="true">
                            @for ($bar = 4; $bar < 214; $bar += 6)
                                <rect x="{{ $bar }}" y="2" width="{{ ($bar % 12 === 0) ? 3 : (($bar % 9 === 0) ? 1.5 : 2.2) }}" height="24" rx="0.5"/>
                            @endfor
                        </svg>
                        <p class="text-[9px] font-mono tracking-[0.2em] text-[#555] mt-1">{{ $booking->booking_code }} • ISSUED {{ now()->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Print Bottom Footer with BENCHCODE™ Official Trademark -->
            <div class="bg-[#faf8f4] border-t border-[#e5d9c3] px-6 py-2.5 flex items-center justify-between text-[10px] text-[#5e584e]">
                <span>© {{ date('Y') }} {{ $brandPrefix }} {{ $brandSuffix }} Indonesia — Valid Digital Reservation Pass</span>

                <span class="inline-flex items-center gap-1.5 font-semibold text-[#141413]">
                    <svg class="w-3 h-3.5 shrink-0" viewBox="0 0 60 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 4H8a4 4 0 0 0-4 4v54a4 4 0 0 0 4 4h10" stroke="#1E5FAF" stroke-width="7" stroke-linecap="round"/>
                        <path d="M42 4h10a4 4 0 0 1 4 4v54a4 4 0 0 1-4 4H42" stroke="#1E5FAF" stroke-width="7" stroke-linecap="round"/>
                        <line x1="16" y1="26" x2="34" y2="26" stroke="#27B084" stroke-width="6" stroke-linecap="round"/>
                        <line x1="16" y1="38" x2="28" y2="38" stroke="#27B084" stroke-width="6" stroke-linecap="round"/>
                    </svg>
                    <span>System & E-Pass by <strong class="tracking-wider font-extrabold">BENCHCODE™</strong> (benchcode.dev)</span>
                </span>
            </div>

        </div>
    </div>
@endsection
