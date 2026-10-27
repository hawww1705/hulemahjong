@extends('layouts.admin')

@section('title', 'Site Settings & Inbox — Hule Mahjong CMS')
@section('heading', 'Website Content Settings & Concierge Inbox')
@section('subheading', 'Customize brand identity, CTA banner copy & background image, payment bank account, and read guest inquiries.')

@section('content')
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">

        <!-- Left: Global CMS Settings Form -->
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="xl:col-span-7 bg-white rounded-2xl border border-[#ebe5d9] p-6 space-y-6">
            @csrf

            <div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-[#8c734b] mb-3">Brand & House Rules</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="cms-label">Brand Prefix (Regular)</label>
                        <input type="text" name="brand_prefix" value="{{ $settings['brand_prefix'] ?? 'Hule' }}" class="cms-input">
                    </div>
                    <div>
                        <label class="cms-label">Brand Suffix (Bold)</label>
                        <input type="text" name="brand_suffix" value="{{ $settings['brand_suffix'] ?? 'Mahjong' }}" class="cms-input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="cms-label">Booking Cancellation Note</label>
                        <input type="text" name="cancellation_note" value="{{ $settings['cancellation_note'] ?? '' }}" class="cms-input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="cms-label">Recreational House Policy (Footer)</label>
                        <input type="text" name="house_policy" value="{{ $settings['house_policy'] ?? '' }}" class="cms-input">
                    </div>
                </div>
            </div>

            <div class="pt-5 border-t border-stone-100">
                <h2 class="text-sm font-bold uppercase tracking-wider text-[#8c734b] mb-3">CTA Banner Section</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="cms-label">Eyebrow Text</label>
                        <input type="text" name="banner_eyebrow" value="{{ $settings['banner_eyebrow'] ?? '' }}" class="cms-input">
                    </div>
                    <div>
                        <label class="cms-label">Line 1 Bold</label>
                        <input type="text" name="banner_title_line1_bold" value="{{ $settings['banner_title_line1_bold'] ?? 'Your First' }}" class="cms-input">
                    </div>
                    <div>
                        <label class="cms-label">Line 1 Light</label>
                        <input type="text" name="banner_title_line1_light" value="{{ $settings['banner_title_line1_light'] ?? 'Round' }}" class="cms-input">
                    </div>
                    <div>
                        <label class="cms-label">Line 2 Light</label>
                        <input type="text" name="banner_title_line2_light" value="{{ $settings['banner_title_line2_light'] ?? 'Starts' }}" class="cms-input">
                    </div>
                    <div>
                        <label class="cms-label">Line 2 Bold</label>
                        <input type="text" name="banner_title_line2_bold" value="{{ $settings['banner_title_line2_bold'] ?? 'Here.' }}" class="cms-input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="cms-label">Banner Description</label>
                        <textarea name="banner_description" rows="2" class="cms-input">{{ $settings['banner_description'] ?? '' }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="cms-label">Replace Banner Background Photo</label>
                        <input type="file" name="banner_image_file" accept="image/*" class="cms-input !py-2">
                    </div>
                </div>
            </div>

            <div class="pt-5 border-t border-stone-100">
                <h2 class="text-sm font-bold uppercase tracking-wider text-[#8c734b] mb-3">Contact & Payment Settlement</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="cms-label">Branch Address Summary</label>
                        <input type="text" name="contact_address" value="{{ $settings['contact_address'] ?? '' }}" class="cms-input">
                    </div>
                    <div>
                        <label class="cms-label">Display Phone</label>
                        <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '' }}" class="cms-input">
                    </div>
                    <div>
                        <label class="cms-label">WhatsApp Number (628...)</label>
                        <input type="text" name="contact_whatsapp" value="{{ $settings['contact_whatsapp'] ?? '' }}" class="cms-input">
                    </div>
                    <div>
                        <label class="cms-label">Concierge Email</label>
                        <input type="text" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}" class="cms-input">
                    </div>
                    <div>
                        <label class="cms-label">Opening Hours</label>
                        <input type="text" name="opening_hours" value="{{ $settings['opening_hours'] ?? '' }}" class="cms-input">
                    </div>
                    <div>
                        <label class="cms-label">Bank Name</label>
                        <input type="text" name="payment_bank_name" value="{{ $settings['payment_bank_name'] ?? '' }}" class="cms-input">
                    </div>
                    <div>
                        <label class="cms-label">Account Number</label>
                        <input type="text" name="payment_account_number" value="{{ $settings['payment_account_number'] ?? '' }}" class="cms-input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="cms-label">Account Holder Name</label>
                        <input type="text" name="payment_account_holder" value="{{ $settings['payment_account_holder'] ?? '' }}" class="cms-input">
                    </div>
                </div>
            </div>

            <button type="submit" class="px-6 py-3 rounded-full bg-[#111] hover:bg-[#c3b091] text-white hover:text-[#141310] text-xs font-bold transition">
                Save All Website Settings →
            </button>
        </form>

        <!-- Right: Concierge Messages Inbox -->
        <div class="xl:col-span-5 bg-white rounded-2xl border border-[#ebe5d9] p-6 space-y-4">
            <h2 class="text-[17px] font-bold">Concierge Contact Inbox</h2>

            <div class="space-y-3">
                @forelse ($inquiries as $inq)
                    <div class="rounded-xl border {{ $inq->is_read ? 'border-stone-200 bg-white' : 'border-[#c3b091] bg-[#faf6ee]' }} p-4 text-xs space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-bold text-stone-900">{{ $inq->subject }}</p>
                                <p class="text-stone-500">{{ $inq->name }} • {{ $inq->phone }} {{ $inq->email ? '• ' . $inq->email : '' }}</p>
                            </div>
                            <span class="text-[10.5px] text-stone-400 shrink-0">{{ $inq->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-stone-700 leading-relaxed">{{ $inq->message }}</p>

                        <div class="flex items-center justify-end gap-3 pt-1">
                            @if (!$inq->is_read)
                                <form method="POST" action="{{ route('admin.inquiries.read', $inq) }}">
                                    @csrf
                                    <button type="submit" class="text-[#6e5b3e] font-semibold hover:underline">Mark Read</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.inquiries.destroy', $inq) }}" onsubmit="return confirm('Delete message?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-stone-400 py-6 text-center">No inquiries received yet.</p>
                @endforelse
            </div>
        </div>

    </div>
@endsection
