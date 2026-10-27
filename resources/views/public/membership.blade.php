@extends('layouts.public')

@section('title', 'Club Membership — Hule Mahjong')
@section('hide_cta_banner', '1')

@section('content')
    <section class="pt-9 pb-11 sm:pt-14 sm:pb-16 px-5 text-center">
        <nav class="inline-flex items-center gap-2 text-[12px] text-[#767676] mb-3.5">
            <a href="{{ route('home') }}" class="hover:text-[#141413]">Home</a>
            <span class="text-[#b5b5b5]">›</span>
            <span class="font-semibold text-[#8c734b]">Membership</span>
        </nav>

        <h1 class="text-[34px] sm:text-[44px] leading-[1.14] tracking-[-0.03em]">
            <span class="block font-semibold text-[#111111]">Hule Club</span>
            <span class="block font-light text-[#6b6b6b]">Patron Membership</span>
        </h1>
        <p class="max-w-[540px] mx-auto mt-4 text-[14px] text-[#68655f] leading-relaxed">
            Priority access to private VIP suites, waived peak-hour surcharges, personal tile lockers, and seasonal tournament invitations.
        </p>
    </section>

    <!-- Membership Tiers Grid -->
    <section class="max-w-[1080px] mx-auto px-5 sm:px-10 pb-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
            @foreach ($plans as $plan)
                <div class="rounded-[26px] p-7 sm:p-8 flex flex-col justify-between border transition
                    {{ $plan->is_featured
                        ? 'bg-[#0e0f0d] text-white border-[#0e0f0d] shadow-2xl md:-translate-y-2'
                        : 'bg-white text-[#141414] border-[#ece5d8] shadow-soft' }}">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-5">
                            <h2 class="text-[20px] font-semibold tracking-tight">{{ $plan->name }}</h2>
                            @if ($plan->badge)
                                <span class="px-3 py-1 rounded-full text-[10.5px] font-bold uppercase tracking-wider
                                    {{ $plan->is_featured ? 'bg-[#c3b091] text-[#141310]' : 'bg-[#f5efe4] text-[#6e5b3e]' }}">
                                    {{ $plan->badge }}
                                </span>
                            @endif
                        </div>

                        <div class="pb-5 border-b {{ $plan->is_featured ? 'border-white/10' : 'border-[#f1eee8]' }}">
                            <span class="text-[30px] font-bold tracking-[-0.03em]">{{ $plan->formatted_price }}</span>
                            <span class="text-[12.5px] {{ $plan->is_featured ? 'text-white/60' : 'text-[#777]' }}"> / {{ $plan->billing_period }}</span>
                            <p class="mt-2.5 text-[13px] leading-relaxed {{ $plan->is_featured ? 'text-white/75' : 'text-[#666]' }}">
                                {{ $plan->description }}
                            </p>
                        </div>

                        <ul class="mt-6 space-y-3 text-[13px]">
                            @foreach ($plan->benefits ?? [] as $benefit)
                                <li class="flex items-start gap-2.5">
                                    <span class="w-4 h-4 rounded-full {{ $plan->is_featured ? 'bg-[#c3b091]/25 text-[#c3b091]' : 'bg-[#f2eadb] text-[#6e5b3e]' }} flex items-center justify-center text-[10px] shrink-0 mt-0.5">✓</span>
                                    <span class="{{ $plan->is_featured ? 'text-white/85' : 'text-[#333]' }}">{{ $benefit }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <a href="#apply" onclick="document.getElementById('plan-select').value='{{ $plan->id }}'"
                       class="mt-8 w-full h-[46px] rounded-full font-semibold text-[13px] flex items-center justify-center transition
                       {{ $plan->is_featured
                            ? 'bg-[#c3b091] hover:bg-[#d5c4a8] text-[#141310]'
                            : 'bg-[#111111] hover:bg-[#c3b091] text-white hover:text-[#141310]' }}">
                        Select {{ $plan->name }}
                    </a>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Online Membership Application Form -->
    <section id="apply" class="max-w-[780px] mx-auto px-5 sm:px-10 pb-20">
        <div class="bg-[#faf8f4] rounded-[26px] border border-[#e7dcc8] p-6 sm:p-10">
            <div class="text-center mb-7">
                <p class="text-[10.5px] font-bold uppercase tracking-[0.14em] text-[#8c734b]">Seasonal Intake</p>
                <h2 class="text-[26px] font-semibold text-[#141414] mt-1">Submit Membership Application</h2>
                <p class="text-[13px] text-[#666] mt-1">Our Membership Secretary reviews applications within 24 hours.</p>
            </div>

            <form method="POST" action="{{ route('membership.apply') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="cms-label">Selected Tier *</label>
                        <select id="plan-select" name="membership_plan_id" required class="pill-select !rounded-xl">
                            @foreach ($plans as $p)
                                <option value="{{ $p->id }}" @selected($p->is_featured)>{{ $p->name }} — {{ $p->formatted_price }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="cms-label">Home Lounge Branch *</label>
                        <select name="preferred_branch" required class="pill-select !rounded-xl">
                            <option value="PIK 2 — La Riviera">PIK 2 — La Riviera (Tangerang)</option>
                            <option value="Palembang — Jl. M. Isa">Palembang — Jl. M. Isa</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="cms-label">Full Name *</label>
                        <input type="text" name="applicant_name" required placeholder="Your full name" class="cms-input">
                    </div>
                    <div>
                        <label class="cms-label">WhatsApp Number *</label>
                        <input type="text" name="applicant_phone" required placeholder="0812xxxxxxxx" class="cms-input">
                    </div>
                    <div>
                        <label class="cms-label">Email Address *</label>
                        <input type="email" name="applicant_email" required placeholder="you@email.com" class="cms-input">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="cms-label">Usual Playing Frequency</label>
                        <select name="playing_frequency" class="pill-select !rounded-xl">
                            <option value="1-2 times / week">1–2 times per week</option>
                            <option value="3+ times / week">3+ times per week</option>
                            <option value="Weekends & Social Events">Weekends & Social Events</option>
                            <option value="New Beginner">New Beginner Learning Mahjong</option>
                        </select>
                    </div>
                    <div>
                        <label class="cms-label">Notes / Referral (Optional)</label>
                        <input type="text" name="message" placeholder="Referred by member or special notes" class="cms-input">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full h-[48px] rounded-full bg-[#111111] hover:bg-[#c3b091] text-white hover:text-[#141310] font-semibold text-[13.5px] transition">
                        Submit Application to Concierge →
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
