@extends('layouts.admin')

@section('title', 'Time Slots & Promo Codes — Hule Mahjong CMS')
@section('heading', 'Operating Time Slots & Promo Vouchers')
@section('subheading', 'Configure session start times, peak-hour surcharges, and discount codes.')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Left: Booking Time Slots -->
        <div class="lg:col-span-6 bg-white rounded-2xl border border-[#ebe5d9] p-6 space-y-5">
            <div>
                <h2 class="text-[17px] font-bold">Session Time Slots</h2>
                <p class="text-xs text-stone-500">These pills appear in the "SELECT TIME" grid on the Reserve page.</p>
            </div>

            <form method="POST" action="{{ route('admin.slots.store') }}" class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pb-4 border-b border-stone-100">
                @csrf
                <input type="text" name="start_time" required placeholder="HH:MM (15:00)" class="cms-input !py-2 text-xs">
                <input type="text" name="label" placeholder="Label (15:00)" class="cms-input !py-2 text-xs">
                <label class="inline-flex items-center gap-1.5 text-xs font-medium text-stone-700 px-2">
                    <input type="checkbox" name="is_peak" value="1">
                    <span>Peak Rate</span>
                </label>
                <button type="submit" class="rounded-xl bg-[#c3b091] hover:bg-[#b59f7c] text-[#141310] text-xs font-bold py-2">+ Add Slot</button>
            </form>

            <div class="space-y-2.5">
                @foreach ($slots as $slot)
                    <div class="rounded-xl border border-stone-200 p-3 flex flex-wrap items-center justify-between gap-2 bg-[#fcfbf9]">
                        <form method="POST" action="{{ route('admin.slots.update', $slot) }}" class="flex flex-wrap items-center gap-2.5 flex-1">
                            @csrf
                            @method('PUT')
                            <input type="number" name="sort_order" value="{{ $slot->sort_order }}" class="w-14 rounded-lg border border-stone-200 px-2 py-1 text-xs" title="Sort Order">
                            <input type="text" name="start_time" value="{{ $slot->start_time }}" class="w-20 rounded-lg border border-stone-200 px-2 py-1 text-xs font-bold">
                            <input type="text" name="label" value="{{ $slot->label }}" class="w-24 rounded-lg border border-stone-200 px-2 py-1 text-xs">

                            <label class="inline-flex items-center gap-1 text-xs text-stone-700">
                                <input type="checkbox" name="is_peak" value="1" @checked($slot->is_peak)>
                                <span>Peak</span>
                            </label>
                            <label class="inline-flex items-center gap-1 text-xs text-stone-700">
                                <input type="checkbox" name="is_active" value="1" @checked($slot->is_active)>
                                <span>Active</span>
                            </label>

                            <button type="submit" class="px-3 py-1 rounded-lg bg-[#e7dcc8] text-[#3d301b] text-xs font-semibold">Save</button>
                        </form>

                        <form method="POST" action="{{ route('admin.slots.destroy', $slot) }}" onsubmit="return confirm('Remove slot {{ $slot->label }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:underline px-1">✕</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right: Promo Vouchers -->
        <div class="lg:col-span-6 bg-white rounded-2xl border border-[#ebe5d9] p-6 space-y-5">
            <div>
                <h2 class="text-[17px] font-bold">Promo & Voucher Codes</h2>
                <p class="text-xs text-stone-500">Create percentage or fixed Rupiah discounts for players.</p>
            </div>

            <form method="POST" action="{{ route('admin.promos.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3 pb-5 border-b border-stone-100">
                @csrf
                <div>
                    <label class="cms-label">Code *</label>
                    <input type="text" name="code" required placeholder="MAHJONG25" class="cms-input uppercase">
                </div>
                <div>
                    <label class="cms-label">Type *</label>
                    <select name="discount_type" class="cms-input">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Rupiah (Rp)</option>
                    </select>
                </div>
                <div>
                    <label class="cms-label">Value (% or Rp) *</label>
                    <input type="number" name="discount_value" required placeholder="15 or 25000" class="cms-input">
                </div>
                <div>
                    <label class="cms-label">Max Discount Cap (Optional Rp)</label>
                    <input type="number" name="max_discount" placeholder="50000" class="cms-input">
                </div>
                <div>
                    <label class="cms-label">Valid Until</label>
                    <input type="date" name="valid_until" class="cms-input">
                </div>
                <div>
                    <label class="cms-label">Description</label>
                    <input type="text" name="description" placeholder="Weekend promo" class="cms-input">
                </div>
                <div class="sm:col-span-2 flex justify-end">
                    <button type="submit" class="px-5 py-2 rounded-full bg-[#111] text-white text-xs font-semibold">+ Create Promo Code</button>
                </div>
            </form>

            <div class="space-y-2.5">
                @foreach ($promoCodes as $promo)
                    <div class="rounded-xl border border-stone-200 p-3.5 flex items-center justify-between gap-3 bg-[#fcfbf9] text-xs">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm text-[#141414]">{{ $promo->code }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $promo->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-stone-200 text-stone-600' }}">
                                    {{ $promo->is_active ? 'Active' : 'Paused' }}
                                </span>
                            </div>
                            <p class="text-stone-500 mt-0.5">
                                {{ $promo->discount_type === 'percentage' ? $promo->discount_value . '% OFF' : 'Rp ' . number_format($promo->discount_value, 0, ',', '.') . ' OFF' }}
                                • Used {{ $promo->used_count }}{{ $promo->usage_limit ? '/' . $promo->usage_limit : '' }}x
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('admin.promos.toggle', $promo) }}">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg border border-stone-300 text-xs font-medium hover:bg-stone-100">
                                    {{ $promo->is_active ? 'Pause' : 'Enable' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.promos.destroy', $promo) }}" onsubmit="return confirm('Delete promo {{ $promo->code }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline px-1">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
@endsection
