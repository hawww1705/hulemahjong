@extends('layouts.admin')

@section('title', 'Photo Gallery — Hule Mahjong CMS')
@section('heading', 'Photo & Media Gallery')
@section('subheading', 'Upload interior photos, VIP room showcases, tournament snapshots, and F&B imagery.')

@section('content')
    <!-- Upload Photo Card -->
    <div class="bg-white rounded-2xl border border-[#ebe5d9] p-6 mb-8">
        <h2 class="text-[16px] font-bold mb-4">Add New Gallery Photo</h2>
        <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf
            <div>
                <label class="cms-label">Photo Title *</label>
                <input type="text" name="title" required placeholder="e.g. VIP Sakura Suite" class="cms-input">
            </div>
            <div>
                <label class="cms-label">Category *</label>
                <select name="category" class="cms-input">
                    <option value="Lounge & Interior">Lounge & Interior</option>
                    <option value="VIP Suites">VIP Suites</option>
                    <option value="Tournaments & Community">Tournaments & Community</option>
                    <option value="Food & Beverage">Food & Beverage</option>
                </select>
            </div>
            <div>
                <label class="cms-label">Upload Image File (JPG/PNG)</label>
                <input type="file" name="image" accept="image/*" class="cms-input !py-2">
            </div>
            <div>
                <label class="cms-label">Or External Image URL</label>
                <input type="url" name="image_url" placeholder="https://..." class="cms-input">
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="cms-label">Caption</label>
                <input type="text" name="caption" placeholder="Short editorial caption" class="cms-input">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full h-[42px] rounded-full bg-[#c3b091] hover:bg-[#b59f7c] text-[#141310] text-xs font-bold transition">
                    + Upload to Gallery
                </button>
            </div>
        </form>
    </div>

    <!-- Existing Photos Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach ($photos as $photo)
            <div class="bg-white rounded-2xl border border-[#ebe5d9] overflow-hidden">
                <div class="aspect-[16/10] bg-stone-100 relative">
                    <img src="{{ $photo->image_url }}" alt="{{ $photo->title }}" class="w-full h-full object-cover">
                    <span class="absolute top-3 left-3 bg-black/60 text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full">
                        {{ $photo->category }}
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.gallery.update', $photo) }}" enctype="multipart/form-data" class="p-4 space-y-3">
                    @csrf
                    @method('PUT')

                    <input type="text" name="title" value="{{ $photo->title }}" required class="cms-input !py-1.5 text-xs font-semibold">

                    <div class="grid grid-cols-2 gap-2">
                        <select name="category" class="cms-input !py-1.5 text-xs">
                            @foreach (['Lounge & Interior', 'VIP Suites', 'Tournaments & Community', 'Food & Beverage'] as $cat)
                                <option value="{{ $cat }}" @selected($photo->category === $cat)>{{ $cat }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="sort_order" value="{{ $photo->sort_order }}" class="cms-input !py-1.5 text-xs" placeholder="Order">
                    </div>

                    <input type="text" name="caption" value="{{ $photo->caption }}" placeholder="Caption" class="cms-input !py-1.5 text-xs">
                    <input type="file" name="image" accept="image/*" class="text-[11px] text-stone-500 w-full">

                    <div class="flex items-center justify-between pt-1">
                        <label class="inline-flex items-center gap-1.5 text-xs font-medium">
                            <input type="checkbox" name="is_active" value="1" @checked($photo->is_active)>
                            <span>Visible</span>
                        </label>
                        <button type="submit" class="px-4 py-1.5 rounded-full bg-[#111] text-white text-xs font-semibold">Save</button>
                    </div>
                </form>

                <div class="px-4 pb-4 flex justify-end">
                    <form method="POST" action="{{ route('admin.gallery.destroy', $photo) }}" onsubmit="return confirm('Delete this photo?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-[11px] text-red-500 hover:underline">Delete Photo</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection
