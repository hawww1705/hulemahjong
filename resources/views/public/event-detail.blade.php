@extends('layouts.public')

@section('title', $event->title . ' — Hule Mahjong')

@section('content')
    <section class="pt-9 pb-8 sm:pt-12 sm:pb-10 px-5 text-center max-w-[780px] mx-auto">
        <nav class="inline-flex items-center gap-2 text-[12px] text-[#767676] mb-3.5">
            <a href="{{ route('home') }}" class="hover:text-[#141413]">Home</a>
            <span class="text-[#b5b5b5]">›</span>
            <a href="{{ route('events') }}" class="hover:text-[#141413]">Events</a>
            <span class="text-[#b5b5b5]">›</span>
            <span class="font-semibold text-[#8c734b]">{{ $event->category }}</span>
        </nav>

        <h1 class="text-[28px] sm:text-[38px] font-semibold text-[#111111] leading-[1.18] tracking-[-0.025em]">
            {{ $event->title }}
        </h1>
        <p class="mt-3 text-[13.5px] text-[#777] font-medium">
            {{ $event->event_date->format('l, d F Y') }} • {{ $event->start_time }}{{ $event->end_time ? ' – ' . $event->end_time : '' }} WIB • {{ $event->location }}
        </p>
    </section>

    <section class="max-w-[1040px] mx-auto px-5 sm:px-10 pb-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

            <div class="lg:col-span-7 space-y-6">
                <div class="rounded-[24px] overflow-hidden border border-[#ece5d8] aspect-[16/10] bg-[#f5f3ef]">
                    <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                </div>

                <div class="bg-[#faf8f4] rounded-[24px] p-6 sm:p-8 border border-[#ece5d8] space-y-4">
                    <h2 class="text-[19px] font-semibold text-[#141414]">Event Overview</h2>
                    <div class="text-[14px] text-[#555] leading-relaxed whitespace-pre-line">{{ $event->description ?: $event->excerpt }}</div>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="bg-white rounded-[24px] p-6 sm:p-7 border border-[#ebe3d4] shadow-summary">
                    <div class="flex items-center justify-between pb-4 border-b border-[#f1eee8]">
                        <div>
                            <p class="text-[10.5px] uppercase tracking-wider text-[#8c734b] font-bold">Entry Fee</p>
                            <p class="text-[24px] font-bold text-[#141414]">{{ $event->formatted_price }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[11.5px] font-semibold {{ $event->remaining_seats > 0 ? 'bg-[#f5efe4] text-[#6e5b3e]' : 'bg-red-50 text-red-700' }}">
                            {{ $event->remaining_seats }} Spots Left
                        </span>
                    </div>

                    @if ($event->remaining_seats > 0)
                        <form method="POST" action="{{ route('events.register', $event->slug) }}" class="mt-5 space-y-4">
                            @csrf
                            <div>
                                <label class="cms-label">Participant Name *</label>
                                <input type="text" name="participant_name" required placeholder="Full name" class="cms-input">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="cms-label">WhatsApp *</label>
                                    <input type="text" name="participant_phone" required placeholder="0812xxxxxxxx" class="cms-input">
                                </div>
                                <div>
                                    <label class="cms-label">Seats</label>
                                    <select name="seats" class="pill-select !rounded-xl !h-[42px]">
                                        @for ($i = 1; $i <= min(4, $event->remaining_seats); $i++)
                                            <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'Seat' : 'Seats' }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="cms-label">Email</label>
                                    <input type="email" name="participant_email" placeholder="you@email.com" class="cms-input">
                                </div>
                                <div>
                                    <label class="cms-label">Skill Level</label>
                                    <select name="skill_level" class="pill-select !rounded-xl !h-[42px]">
                                        <option value="Beginner">Beginner</option>
                                        <option value="Intermediate" selected>Intermediate</option>
                                        <option value="Advanced">Advanced</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="cms-label">Notes (Optional)</label>
                                <input type="text" name="notes" placeholder="Solo player or coming with friends?" class="cms-input">
                            </div>

                            <button type="submit" class="w-full h-[48px] rounded-full bg-[#111111] hover:bg-[#c3b091] text-white hover:text-[#141310] font-semibold text-[13.5px] transition">
                                Confirm Event Seat →
                            </button>
                        </form>
                    @else
                        <div class="mt-5 rounded-2xl bg-stone-50 p-5 text-center text-[13px] text-stone-600">
                            This session is currently full. Contact our WhatsApp Concierge to join the priority waitlist.
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </section>
@endsection
