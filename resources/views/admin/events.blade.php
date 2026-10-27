@extends('layouts.admin')

@section('title', 'Events & Coaching — Hule Mahjong CMS')
@section('heading', 'Events, Tournaments & Coaching Clinics')
@section('subheading', 'Publish mahjong tournaments, beginner coaching classes, and track participant registrations.')

@section('header_actions')
    <button type="button" onclick="document.getElementById('new-event-panel').classList.toggle('hidden')"
            class="px-4 py-2 rounded-full bg-[#111] hover:bg-[#c3b091] text-white hover:text-[#141310] text-xs font-semibold transition">
        + Publish New Event
    </button>
@endsection

@section('content')
    <!-- Create Event Form -->
    <div id="new-event-panel" class="hidden bg-white rounded-2xl border border-[#dfd3be] p-6 mb-8">
        <h2 class="text-[16px] font-bold mb-4">Publish New Event / Clinic</h2>
        <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf
            <div class="sm:col-span-2">
                <label class="cms-label">Event Title *</label>
                <input type="text" name="title" required placeholder="e.g. Sunday Beginner Mahjong Workshop" class="cms-input">
            </div>
            <div>
                <label class="cms-label">Category *</label>
                <select name="category" class="cms-input">
                    <option value="Tournament">Tournament</option>
                    <option value="Beginner Coaching">Beginner Coaching</option>
                    <option value="Social Night">Social Night</option>
                </select>
            </div>
            <div>
                <label class="cms-label">Location *</label>
                <input type="text" name="location" value="Hule Mahjong PIK 2 — Main Salon" required class="cms-input">
            </div>

            <div>
                <label class="cms-label">Event Date *</label>
                <input type="date" name="event_date" value="{{ now()->addDays(7)->toDateString() }}" required class="cms-input">
            </div>
            <div>
                <label class="cms-label">Start Time *</label>
                <input type="text" name="start_time" value="14:00" required class="cms-input">
            </div>
            <div>
                <label class="cms-label">End Time</label>
                <input type="text" name="end_time" value="18:00" class="cms-input">
            </div>
            <div>
                <label class="cms-label">Entry Fee (Rp, 0 = Free) *</label>
                <input type="number" name="price" value="125000" min="0" required class="cms-input">
            </div>

            <div>
                <label class="cms-label">Seat Quota *</label>
                <input type="number" name="quota" value="24" min="1" required class="cms-input">
            </div>
            <div class="sm:col-span-2">
                <label class="cms-label">Cover Photo</label>
                <input type="file" name="image" accept="image/*" class="cms-input !py-2">
            </div>
            <div class="flex items-center pt-5">
                <label class="inline-flex items-center gap-2 text-xs font-semibold">
                    <input type="checkbox" name="is_featured" value="1" checked>
                    <span>Featured Event</span>
                </label>
            </div>

            <div class="sm:col-span-2 lg:col-span-4">
                <label class="cms-label">Short Excerpt</label>
                <input type="text" name="excerpt" placeholder="One-sentence summary shown on event cards" class="cms-input">
            </div>
            <div class="sm:col-span-2 lg:col-span-4">
                <label class="cms-label">Full Description</label>
                <textarea name="description" rows="3" class="cms-input"></textarea>
            </div>

            <div class="sm:col-span-2 lg:col-span-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-full bg-[#c3b091] hover:bg-[#b59f7c] text-[#141310] text-xs font-bold">
                    Publish Event →
                </button>
            </div>
        </form>
    </div>

    <!-- Existing Events -->
    <div class="space-y-5 mb-10">
        @foreach ($events as $ev)
            <div class="bg-white rounded-2xl border border-[#ebe5d9] p-5 sm:p-6">
                <form method="POST" action="{{ route('admin.events.update', $ev) }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                    @csrf
                    @method('PUT')

                    <div class="lg:col-span-3 space-y-2">
                        <img src="{{ $ev->image_url }}" alt="{{ $ev->title }}" class="w-full aspect-[16/10] object-cover rounded-xl border border-stone-200">
                        <input type="file" name="image" accept="image/*" class="text-xs text-stone-500 w-full">
                        <p class="text-xs font-semibold text-[#8c734b]">
                            Registered: {{ $ev->registered_count }} / {{ $ev->quota }} seats
                        </p>
                    </div>

                    <div class="lg:col-span-9 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="cms-label">Title</label>
                            <input type="text" name="title" value="{{ $ev->title }}" required class="cms-input">
                        </div>
                        <div>
                            <label class="cms-label">Category</label>
                            <select name="category" class="cms-input">
                                @foreach (['Tournament', 'Beginner Coaching', 'Social Night', 'Special Event'] as $cat)
                                    <option value="{{ $cat }}" @selected($ev->category === $cat)>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="cms-label">Date</label>
                            <input type="date" name="event_date" value="{{ $ev->event_date->format('Y-m-d') }}" required class="cms-input">
                        </div>
                        <div>
                            <label class="cms-label">Start – End</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" name="start_time" value="{{ $ev->start_time }}" required class="cms-input">
                                <input type="text" name="end_time" value="{{ $ev->end_time }}" class="cms-input">
                            </div>
                        </div>
                        <div>
                            <label class="cms-label">Price (Rp) & Quota</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="price" value="{{ $ev->price }}" required class="cms-input">
                                <input type="number" name="quota" value="{{ $ev->quota }}" required class="cms-input">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label class="cms-label">Location</label>
                            <input type="text" name="location" value="{{ $ev->location }}" required class="cms-input">
                        </div>

                        <div class="sm:col-span-3">
                            <label class="cms-label">Excerpt</label>
                            <input type="text" name="excerpt" value="{{ $ev->excerpt }}" class="cms-input">
                        </div>

                        <div class="sm:col-span-3">
                            <label class="cms-label">Full Description</label>
                            <textarea name="description" rows="2" class="cms-input">{{ $ev->description }}</textarea>
                        </div>

                        <div class="sm:col-span-3 flex flex-wrap items-center justify-between gap-4 pt-2">
                            <div class="flex items-center gap-4 text-xs font-semibold">
                                <label class="inline-flex items-center gap-1.5">
                                    <input type="checkbox" name="is_active" value="1" @checked($ev->is_active)>
                                    <span>Published</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5">
                                    <input type="checkbox" name="is_featured" value="1" @checked($ev->is_featured)>
                                    <span>Featured</span>
                                </label>
                            </div>
                            <button type="submit" class="px-5 py-2 rounded-full bg-[#111] hover:bg-[#c3b091] text-white hover:text-[#141310] text-xs font-semibold transition">
                                Save Event Changes
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-3 pt-3 border-t border-stone-100 flex justify-between items-center text-xs">
                    <a href="{{ route('events.show', $ev->slug) }}" target="_blank" class="text-[#8c734b] hover:underline font-medium">View Event Page ↗</a>
                    <form method="POST" action="{{ route('admin.events.destroy', $ev) }}" onsubmit="return confirm('Delete event {{ $ev->title }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">Delete Event</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Recent Event Registrations -->
    <div class="bg-white rounded-2xl border border-[#ebe5d9] p-6">
        <h2 class="text-[17px] font-bold mb-4">Recent Event & Coaching Registrations</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="border-b border-stone-200 text-[11px] uppercase tracking-wider text-stone-500">
                    <tr>
                        <th class="py-2.5 pr-3">Reg Code</th>
                        <th class="py-2.5 px-3">Event</th>
                        <th class="py-2.5 px-3">Participant</th>
                        <th class="py-2.5 px-3">Level & Seats</th>
                        <th class="py-2.5 pl-3">Fee</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($registrations as $r)
                        <tr>
                            <td class="py-2.5 pr-3 font-bold">{{ $r->registration_code }}</td>
                            <td class="py-2.5 px-3">{{ $r->event->title ?? '-' }}</td>
                            <td class="py-2.5 px-3">{{ $r->participant_name }} ({{ $r->participant_phone }})</td>
                            <td class="py-2.5 px-3">{{ $r->skill_level }} • {{ $r->seats }} seat(s)</td>
                            <td class="py-2.5 pl-3 font-semibold">Rp {{ number_format($r->total_price, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-5 text-center text-stone-400">No event registrations yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
