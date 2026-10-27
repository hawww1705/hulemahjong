<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CMS Admin — Hule Mahjong')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f7f5f0] text-[#171614] antialiased min-h-screen">

    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Mobile Top Bar -->
        <div class="lg:hidden bg-[#0e0f0d] text-white px-4 py-3.5 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="text-[18px] tracking-tight">
                <span class="font-light">Hule</span><span class="font-bold text-[#c3b091]">CMS</span>
            </a>
            <button type="button" id="cms-mobile-btn" class="px-3 py-1.5 rounded-lg bg-white/10 text-xs font-semibold">
                Menu ☰
            </button>
        </div>

        <!-- Sidebar Navigation -->
        <aside id="cms-sidebar" class="hidden lg:flex lg:w-[265px] shrink-0 bg-[#0e0f0d] text-white flex-col justify-between p-5">
            <div>
                <div class="px-2 pt-1 pb-6 border-b border-white/10 flex items-center justify-between">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-baseline text-[21px] tracking-[-0.03em]">
                        <span class="font-light">Hule</span><span class="font-bold text-[#c3b091]">Mahjong</span>
                        <span class="ml-2 text-[10px] uppercase tracking-widest bg-[#c3b091]/20 text-[#c3b091] px-2 py-0.5 rounded-full font-semibold">CMS</span>
                    </a>
                </div>

                @php
                    $cmsLinks = [
                        ['label' => 'Dashboard & Floor', 'route' => 'admin.dashboard', 'badge' => null],
                        ['label' => 'Bookings', 'route' => 'admin.bookings', 'badge' => $pendingBookingsCount ?? 0],
                        ['label' => 'Tables & Pricing', 'route' => 'admin.tables', 'badge' => null],
                        ['label' => 'Time Slots & Promos', 'route' => 'admin.slots', 'badge' => null],
                        ['label' => 'Events & Clinics', 'route' => 'admin.events', 'badge' => null],
                        ['label' => 'Photo Gallery', 'route' => 'admin.gallery', 'badge' => null],
                        ['label' => 'Memberships', 'route' => 'admin.memberships', 'badge' => $pendingMembershipsCount ?? 0],
                        ['label' => 'Settings & Inbox', 'route' => 'admin.settings', 'badge' => $unreadInquiriesCount ?? 0],
                    ];
                @endphp

                <nav class="mt-6 space-y-1">
                    @foreach ($cmsLinks as $link)
                        @php $active = request()->routeIs($link['route']); @endphp
                        <a href="{{ route($link['route']) }}"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-[13px] font-medium transition
                           {{ $active ? 'bg-[#c3b091] text-[#141310] font-semibold' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <span>{{ $link['label'] }}</span>
                            @if (!empty($link['badge']) && $link['badge'] > 0)
                                <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold {{ $active ? 'bg-[#141310] text-white' : 'bg-[#c3b091] text-[#141310]' }}">
                                    {{ $link['badge'] }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                </nav>
            </div>

            <div class="pt-6 border-t border-white/10 space-y-2.5">
                <a href="{{ route('home') }}" target="_blank"
                   class="w-full py-2.5 px-3.5 rounded-xl border border-white/15 hover:border-[#c3b091] text-[12.5px] text-white/85 hover:text-[#c3b091] flex items-center justify-between transition">
                    <span>Open Live Website</span>
                    <span>↗</span>
                </a>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-2 px-3.5 rounded-xl text-left text-[12px] text-white/50 hover:text-red-300 transition">
                        Sign Out ({{ auth()->user()->name ?? 'Admin' }})
                    </button>
                </form>

                <div class="pt-3 border-t border-white/10 px-2 flex items-center gap-2 text-[10.5px] text-white/45">
                    <svg class="w-3.5 h-4 shrink-0" viewBox="0 0 60 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 4H8a4 4 0 0 0-4 4v54a4 4 0 0 0 4 4h10" stroke="#c3b091" stroke-width="7" stroke-linecap="round"/>
                        <path d="M42 4h10a4 4 0 0 1 4 4v54a4 4 0 0 1-4 4H42" stroke="#c3b091" stroke-width="7" stroke-linecap="round"/>
                        <line x1="16" y1="26" x2="34" y2="26" stroke="#27B084" stroke-width="6" stroke-linecap="round"/>
                        <line x1="16" y1="38" x2="28" y2="38" stroke="#27B084" stroke-width="6" stroke-linecap="round"/>
                    </svg>
                    <span>Powered by <strong class="text-white/80 font-bold tracking-wider">BENCHCODE™</strong></span>
                </div>
            </div>
        </aside>

        <!-- Main CMS Content Area -->
        <div class="flex-1 min-w-0">
            <header class="bg-white border-b border-[#ebe5d9] px-5 sm:px-8 py-5 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-[20px] sm:text-[22px] font-bold text-[#141414] tracking-tight">@yield('heading', 'Dashboard')</h1>
                    <p class="text-[12.5px] text-[#777] mt-0.5">@yield('subheading', 'Manage reservations, table inventory, pricing, events, and media.')</p>
                </div>
                <div class="flex items-center gap-3">
                    @yield('header_actions')
                </div>
            </header>

            <div class="p-5 sm:p-8 max-w-[1320px]">
                @if (session('success'))
                    <div class="mb-6 rounded-2xl border border-[#d6c5a9] bg-[#faf5eb] px-5 py-3.5 text-[13.5px] font-medium text-[#4f3e22]">
                        ✓ {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-3.5 text-[13.5px] font-medium text-red-800">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-3.5 text-[13px] text-red-800">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script>
        (function () {
            const btn = document.getElementById('cms-mobile-btn');
            const sidebar = document.getElementById('cms-sidebar');
            if (btn && sidebar) {
                btn.addEventListener('click', () => {
                    sidebar.classList.toggle('hidden');
                    sidebar.classList.toggle('flex');
                });
            }
        })();
    </script>
</body>
</html>
