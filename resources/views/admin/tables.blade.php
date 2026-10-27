@extends('layouts.admin')

@section('title', 'Tables & Pricing — Hule Mahjong CMS')
@section('heading', 'Tables, Suites & Dynamic Pricing')
@section('subheading', 'Set weekday/weekend/peak session rates, upload suite photos, and add/remove physical automatic tables.')

@section('header_actions')
    <button type="button" onclick="document.getElementById('new-cat-panel').classList.toggle('hidden')"
            class="px-4 py-2 rounded-full bg-[#111] hover:bg-[#c3b091] text-white hover:text-[#141310] text-xs font-semibold transition">
        + Add New Suite / Category
    </button>
@endsection

@section('content')
    <!-- Create New Category Panel -->
    <div id="new-cat-panel" class="hidden bg-white rounded-2xl border border-[#dfd3be] p-6 mb-8">
        <h2 class="text-[16px] font-bold mb-4">Create New Table / Suite Category</h2>
        <form method="POST" action="{{ route('admin.tables.categories.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf
            <div class="sm:col-span-2">
                <label class="cms-label">Category / Suite Name *</label>
                <input type="text" name="name" required placeholder="e.g. Emerald VIP Suite" class="cms-input">
            </div>
            <div>
                <label class="cms-label">Branch *</label>
                <input type="text" name="branch" value="PIK 2 — La Riviera" required class="cms-input">
            </div>
            <div>
                <label class="cms-label">Initial Table Count</label>
                <input type="number" name="initial_tables_count" value="2" min="0" max="30" class="cms-input">
            </div>

            <div>
                <label class="cms-label">Weekday Price (Rp) *</label>
                <input type="number" name="weekday_price" value="150000" min="0" required class="cms-input">
            </div>
            <div>
                <label class="cms-label">Weekend Price (Rp) *</label>
                <input type="number" name="weekend_price" value="195000" min="0" required class="cms-input">
            </div>
            <div>
                <label class="cms-label">Peak Surcharge (Rp) *</label>
                <input type="number" name="peak_surcharge" value="20000" min="0" required class="cms-input">
            </div>
            <div>
                <label class="cms-label">Minutes per Session *</label>
                <input type="number" name="session_minutes" value="120" min="30" required class="cms-input">
            </div>

            <div>
                <label class="cms-label">Min Players</label>
                <input type="number" name="min_players" value="4" min="1" class="cms-input">
            </div>
            <div>
                <label class="cms-label">Max Players</label>
                <input type="number" name="max_players" value="6" min="1" class="cms-input">
            </div>
            <div class="sm:col-span-2">
                <label class="cms-label">Subtitle</label>
                <input type="text" name="subtitle" placeholder="Private Room • Automatic Shuffler" class="cms-input">
            </div>

            <div class="sm:col-span-2">
                <label class="cms-label">Amenities (comma-separated)</label>
                <input type="text" name="amenities_raw" value="Automatic Shuffler Table, Complimentary Tea, USB-C Charging" class="cms-input">
            </div>
            <div class="sm:col-span-2">
                <label class="cms-label">Suite Photo (JPG/PNG)</label>
                <input type="file" name="image" accept="image/*" class="cms-input !py-2">
            </div>

            <div class="sm:col-span-2 lg:col-span-4">
                <label class="cms-label">Description</label>
                <textarea name="description" rows="2" class="cms-input" placeholder="Describe the room atmosphere and hardware..."></textarea>
            </div>

            <div class="sm:col-span-2 lg:col-span-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-full bg-[#c3b091] hover:bg-[#b59f7c] text-[#141310] text-xs font-bold">
                    Create Category & Generate Tables →
                </button>
            </div>
        </form>
    </div>

    <!-- Existing Categories & Physical Tables -->
    <div class="space-y-8">
        @foreach ($categories as $category)
            <div class="bg-white rounded-2xl border border-[#ebe5d9] overflow-hidden">
                <!-- Category Top Header with Quick +1 / -1 Table Controls -->
                <div class="bg-[#faf8f4] border-b border-[#ebe5d9] p-5 sm:px-6 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-20 h-14 rounded-xl object-cover border border-[#e5dec9]">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-[17px] font-bold text-[#141414]">{{ $category->name }}</h2>
                                <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-semibold {{ $category->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-stone-200 text-stone-600' }}">
                                    {{ $category->is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </div>
                            <p class="text-xs text-stone-500 mt-0.5">
                                {{ $category->branch }} • <strong>{{ $category->active_tables_count }}</strong> active / {{ $category->tables_count }} total units
                            </p>
                        </div>
                    </div>

                    <!-- Quick Tambah / Kurang Meja Buttons -->
                    <div class="flex flex-wrap items-center gap-2">
                        <form method="POST" action="{{ route('admin.tables.quick-remove', $category) }}" onsubmit="return confirm('Remove the last physical table in this category?')">
                            @csrf
                            <button type="submit" class="px-3.5 py-2 rounded-xl border border-stone-300 hover:border-red-400 text-xs font-semibold text-stone-700 hover:text-red-600 transition">
                                − Kurangi 1 Meja
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.tables.quick-add', $category) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-xl bg-[#c3b091] hover:bg-[#b59f7c] text-[#141310] text-xs font-bold transition">
                                + Tambah 1 Meja Otomatis
                            </button>
                        </form>
                    </div>
                </div>

                <div class="p-5 sm:p-6 grid grid-cols-1 xl:grid-cols-12 gap-8">
                    <!-- Left: Edit Pricing, Photo & Details -->
                    <div class="xl:col-span-6 space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#8c734b]">Pricing & Suite Details</h3>
                        <form method="POST" action="{{ route('admin.tables.categories.update', $category) }}" enctype="multipart/form-data" class="space-y-3.5">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="cms-label">Name</label>
                                    <input type="text" name="name" value="{{ $category->name }}" required class="cms-input">
                                </div>
                                <div>
                                    <label class="cms-label">Branch</label>
                                    <input type="text" name="branch" value="{{ $category->branch }}" required class="cms-input">
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="cms-label">Weekday (Rp)</label>
                                    <input type="number" name="weekday_price" value="{{ $category->weekday_price }}" required class="cms-input font-semibold">
                                </div>
                                <div>
                                    <label class="cms-label">Weekend (Rp)</label>
                                    <input type="number" name="weekend_price" value="{{ $category->weekend_price }}" required class="cms-input font-semibold">
                                </div>
                                <div>
                                    <label class="cms-label">Peak +Rp</label>
                                    <input type="number" name="peak_surcharge" value="{{ $category->peak_surcharge }}" required class="cms-input">
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="cms-label">Session (Min)</label>
                                    <input type="number" name="session_minutes" value="{{ $category->session_minutes }}" required class="cms-input">
                                </div>
                                <div>
                                    <label class="cms-label">Min Guests</label>
                                    <input type="number" name="min_players" value="{{ $category->min_players }}" required class="cms-input">
                                </div>
                                <div>
                                    <label class="cms-label">Max Guests</label>
                                    <input type="number" name="max_players" value="{{ $category->max_players }}" required class="cms-input">
                                </div>
                            </div>

                            <div>
                                <label class="cms-label">Subtitle</label>
                                <input type="text" name="subtitle" value="{{ $category->subtitle }}" class="cms-input">
                            </div>

                            <div>
                                <label class="cms-label">Amenities (comma-separated)</label>
                                <input type="text" name="amenities_raw" value="{{ implode(', ', $category->amenities ?? []) }}" class="cms-input">
                            </div>

                            <div>
                                <label class="cms-label">Description</label>
                                <textarea name="description" rows="2" class="cms-input">{{ $category->description }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-center">
                                <div>
                                    <label class="cms-label">Replace Photo</label>
                                    <input type="file" name="image" accept="image/*" class="text-xs text-stone-600">
                                </div>
                                <label class="inline-flex items-center gap-2 text-xs font-semibold text-stone-700 mt-4">
                                    <input type="checkbox" name="is_active" value="1" @checked($category->is_active) class="rounded">
                                    <span>Visible on Booking Page</span>
                                </label>
                            </div>

                            <div class="pt-2 flex items-center justify-between">
                                <button type="submit" class="px-5 py-2.5 rounded-full bg-[#111] hover:bg-[#c3b091] text-white hover:text-[#141310] text-xs font-semibold transition">
                                    Save Pricing & Details
                                </button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('admin.tables.categories.destroy', $category) }}" onsubmit="return confirm('Delete this entire category and all its tables?')" class="pt-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-[11.5px] text-red-500 hover:underline">Delete entire category</button>
                        </form>
                    </div>

                    <!-- Right: Physical Automatic Tables List -->
                    <div class="xl:col-span-6 space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#8c734b]">Physical Mahjong Table Units ({{ $category->tables->count() }})</h3>

                        <div class="space-y-2.5 max-h-[380px] overflow-y-auto pr-1">
                            @foreach ($category->tables as $tbl)
                                <div class="rounded-xl border border-stone-200 p-3 flex flex-wrap items-center justify-between gap-2 bg-[#fcfbf9]">
                                    <form method="POST" action="{{ route('admin.tables.items.update', $tbl) }}" class="flex-1 flex flex-wrap items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <span class="px-2.5 py-1 rounded-lg bg-[#0e0f0d] text-[#c3b091] text-xs font-bold">{{ $tbl->code }}</span>
                                        <input type="text" name="name" value="{{ $tbl->name }}" class="rounded-lg border border-stone-200 bg-white px-2.5 py-1 text-xs w-36">
                                        <input type="text" name="floor_zone" value="{{ $tbl->floor_zone }}" class="rounded-lg border border-stone-200 bg-white px-2.5 py-1 text-xs w-32">
                                        <select name="status" class="rounded-lg border border-stone-200 bg-white px-2 py-1 text-xs font-semibold
                                            {{ $tbl->status === 'active' ? 'text-emerald-700' : 'text-amber-700' }}">
                                            <option value="active" @selected($tbl->status === 'active')>Active</option>
                                            <option value="maintenance" @selected($tbl->status === 'maintenance')>Maintenance</option>
                                            <option value="inactive" @selected($tbl->status === 'inactive')>Inactive</option>
                                        </select>
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-[#e7dcc8] hover:bg-[#c3b091] text-[#3d301b] text-xs font-semibold">
                                            Update
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.tables.items.destroy', $tbl) }}" onsubmit="return confirm('Delete table {{ $tbl->code }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:underline px-1.5">✕</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>

                        <!-- Custom Table Add Row -->
                        <form method="POST" action="{{ route('admin.tables.items.store', $category) }}" class="pt-3 border-t border-stone-200 grid grid-cols-2 sm:grid-cols-5 gap-2">
                            @csrf
                            <input type="text" name="code" required placeholder="Code (VIP-09)" class="cms-input !py-1.5 text-xs">
                            <input type="text" name="name" required placeholder="Unit Name" class="cms-input !py-1.5 text-xs">
                            <input type="text" name="floor_zone" value="Main Lounge" required class="cms-input !py-1.5 text-xs">
                            <select name="status" class="cms-input !py-1.5 text-xs">
                                <option value="active">Active</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                            <button type="submit" class="rounded-xl bg-[#111] text-white text-xs font-semibold py-1.5 px-3">+ Add Custom</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
