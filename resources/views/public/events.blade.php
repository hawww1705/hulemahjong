@extends('layouts.public')

@section('title', 'Events, Championships & Coaching — Hule Mahjong')

@section('content')
    <section class="pt-9 pb-10 sm:pt-14 sm:pb-14 px-5 text-center">
        <nav class="inline-flex items-center gap-2 text-[12px] text-[#767676] mb-3.5">
            <a href="{{ route('home') }}" class="hover:text-[#141413]">Home</a>
            <span class="text-[#b5b5b5]">›</span>
            <span class="font-semibold text-[#8c734b]">Events & Coaching</span>
        </nav>

        <h1 class="text-[34px] sm:text-[44px] leading-[1.14] tracking-[-0.03em]">
            <span class="block font-semibold text-[#111111]">Tournaments</span>
            <span class="block font-light text-[#6b6b6b]">& Beginner Clinics</span>
        </h1>

        <!-- Category Filter Pills -->
        <div class="mt-7 inline-flex flex-wrap items-center justify-center bg-[#f6f4f0] rounded-full p-1.5 border border-[#ece5d8] gap-1">
            @foreach ($categories as $cat)
                <a href="{{ route('events', $cat === 'All' ? [] : ['category' => $cat]) }}"
                   class="px-4 py-1.5 rounded-full text-[12.5px] transition {{ $selectedCategory === $cat ? 'bg-[#e7dcc8] text-[#4f3e22] font-semibold' : 'text-[#5c5851] hover:text-[#111] font-medium' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    </section>

    <section class="max-w-[1080px] mx-auto px-5 sm:px-10 pb-20">
        @if ($events->isEmpty())
            <div class="rounded-3xl bg-[#faf8f4] border border-[#ece5d8] p-12 text-center text-[#666]">
                No upcoming events in this category right now. Check back soon!
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
                @foreach ($events as $event)
                    <article class="bg-white rounded-[24px] border border-[#ece5d8] shadow-soft overflow-hidden flex flex-col justify-between group">
                        <div>
                            <a href="{{ route('events.show', $event->slug) }}" class="block relative aspect-[16/10] bg-[#f5f3ef] overflow-hidden">
                                <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                <span class="absolute top-3.5 left-3.5 bg-white/95 backdrop-blur-xs text-[#4f3e22] text-[10.5px] font-bold uppercase tracking-wider px-3 py-1 rounded-full">
                                    {{ $event->category }}
                                </span>
                            </a>

                            <div class="p-6">
                                <div class="flex items-center gap-2 text-[11.5px] text-[#8c734b] font-semibold">
                                    <span>{{ $event->event_date->format('D, d M Y') }}</span>
                                    <span>•</span>
                                    <span>{{ $event->start_time }}{{ $event->end_time ? ' – ' . $event->end_time : '' }} WIB</span>
                                </div>

                                <h2 class="text-[19px] font-semibold text-[#141414] leading-snug mt-2 group-hover:text-[#6e5b3e] transition">
                                    <a href="{{ route('events.show', $event->slug) }}">{{ $event->title }}</a>
                                </h2>

                                <p class="text-[12.5px] text-[#666] mt-2 line-clamp-3 leading-relaxed">
                                    {{ $event->excerpt }}
                                </p>
                            </div>
                        </div>

                        <div class="px-6 pb-6 pt-3 border-t border-[#f3efe8] space-y-3">
                            <div class="flex items-center justify-between text-[12px]">
                                <span class="text-[#777]">{{ $event->remaining_seats }} / {{ $event->quota }} spots open</span>
                                <span class="font-bold text-[15px] text-[#141414]">{{ $event->formatted_price }}</span>
                            </div>

                            <a href="{{ route('events.show', $event->slug) }}"
                               class="w-full h-[42px] rounded-full bg-[#111111] hover:bg-[#c3b091] text-white hover:text-[#141310] text-[12.5px] font-semibold flex items-center justify-center gap-2 transition">
                                <span>View & Register</span>
                                <span>↗</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
