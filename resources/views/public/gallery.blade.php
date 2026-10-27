@extends('layouts.public')

@section('title', 'Lounge & Interior Gallery — Hule Mahjong')

@section('content')
    <section class="pt-9 pb-10 sm:pt-14 sm:pb-14 px-5 text-center">
        <nav class="inline-flex items-center gap-2 text-[12px] text-[#767676] mb-3.5">
            <a href="{{ route('home') }}" class="hover:text-[#141413]">Home</a>
            <span class="text-[#b5b5b5]">›</span>
            <span class="font-semibold text-[#8c734b]">Gallery</span>
        </nav>

        <h1 class="text-[34px] sm:text-[44px] leading-[1.14] tracking-[-0.03em]">
            <span class="block font-semibold text-[#111111]">Inside</span>
            <span class="block font-light text-[#6b6b6b]">Hule Lounge</span>
        </h1>

        <div class="mt-7 inline-flex flex-wrap items-center justify-center bg-[#f6f4f0] rounded-full p-1.5 border border-[#ece5d8] gap-1">
            @foreach ($categories as $cat)
                <a href="{{ route('gallery', $cat === 'All' ? [] : ['category' => $cat]) }}"
                   class="px-4 py-1.5 rounded-full text-[12.5px] transition {{ $selectedCategory === $cat ? 'bg-[#e7dcc8] text-[#4f3e22] font-semibold' : 'text-[#5c5851] hover:text-[#111] font-medium' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    </section>

    <section class="max-w-[1100px] mx-auto px-5 sm:px-10 pb-20">
        @if ($photos->isEmpty())
            <div class="rounded-3xl bg-[#faf8f4] border border-[#ece5d8] p-12 text-center text-[#666]">
                No photos in this category yet.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($photos as $photo)
                    <button type="button"
                            data-src="{{ $photo->image_url }}"
                            data-title="{{ $photo->title }}"
                            data-caption="{{ $photo->caption }}"
                            class="gallery-trigger text-left group rounded-[22px] overflow-hidden border border-[#ece5d8] bg-white shadow-soft">
                        <div class="relative aspect-[16/11] bg-[#f5f3ef] overflow-hidden">
                            <img src="{{ $photo->image_url }}" alt="{{ $photo->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <span class="absolute top-3 left-3 bg-black/55 backdrop-blur-xs text-white text-[10px] font-semibold uppercase tracking-wider px-3 py-1 rounded-full">
                                {{ $photo->category }}
                            </span>
                        </div>
                        <div class="p-4 sm:p-5">
                            <h2 class="text-[15.5px] font-semibold text-[#151515]">{{ $photo->title }}</h2>
                            @if ($photo->caption)
                                <p class="text-[12.5px] text-[#6d6962] mt-1 line-clamp-2">{{ $photo->caption }}</p>
                            @endif
                        </div>
                    </button>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Lightbox Modal -->
    <div id="lightbox-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/80 backdrop-blur-xs">
        <div class="max-w-[880px] w-full relative">
            <button type="button" id="lightbox-close" class="absolute -top-11 right-0 text-white/85 hover:text-white text-sm font-semibold px-3 py-1 rounded-full bg-white/10">
                Close ✕
            </button>
            <img id="lightbox-img" src="" alt="" class="w-full max-h-[75vh] object-contain rounded-2xl bg-black">
            <div class="mt-3 text-center text-white">
                <p id="lightbox-title" class="text-[16px] font-semibold"></p>
                <p id="lightbox-caption" class="text-[13px] text-white/70 mt-0.5"></p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const modal = document.getElementById('lightbox-modal');
        const img = document.getElementById('lightbox-img');
        const title = document.getElementById('lightbox-title');
        const caption = document.getElementById('lightbox-caption');

        document.querySelectorAll('.gallery-trigger').forEach(btn => {
            btn.addEventListener('click', () => {
                img.src = btn.dataset.src;
                title.textContent = btn.dataset.title || '';
                caption.textContent = btn.dataset.caption || '';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
        });

        const close = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };
        document.getElementById('lightbox-close').addEventListener('click', close);
        modal.addEventListener('click', (e) => { if (e.target === modal) close(); });
    })();
</script>
@endpush
