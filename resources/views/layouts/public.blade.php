<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($siteSettings['brand_prefix'] ?? 'Hule') . ($siteSettings['brand_suffix'] ?? 'Mahjong') . ' — Elevated Social Mahjong Lounge')</title>
    <meta name="description" content="{{ $siteSettings['site_tagline'] ?? 'Elevated Social Mahjong Lounge & Automatic Table Reservations' }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#c3b091] print:bg-white text-[#171614] antialiased p-2 sm:p-4 md:p-6 lg:p-7 print:p-0 min-h-screen print:min-h-0 selection:bg-[#c3b091]/35 selection:text-[#171614]">

    @php
        $brandPrefix = $siteSettings['brand_prefix'] ?? 'Hule';
        $brandSuffix = $siteSettings['brand_suffix'] ?? 'Mahjong';
        $bannerImg = $siteSettings['banner_image'] ?? 'images/mahjong/cta-banner.jpg';
        $bannerUrl = str_starts_with($bannerImg, 'http') ? $bannerImg : asset(ltrim($bannerImg, '/'));
        $waNumber = preg_replace('/\D/', '', $siteSettings['contact_whatsapp'] ?? '6281288997788');
    @endphp

    <!-- Main White Canvas inside #c3b091 Frame -->
    <div class="bg-white max-w-[1360px] print:max-w-none mx-auto min-h-[calc(100vh-3.5rem)] print:min-h-0 rounded-[6px] sm:rounded-[8px] print:rounded-none shadow-[0_24px_80px_rgba(32,25,15,0.16)] print:shadow-none overflow-hidden flex flex-col justify-between">

        <div>
            <!-- Top Navigation Header -->
            <header class="no-print px-5 sm:px-10 lg:px-14 pt-6 pb-4">
                <div class="flex items-center justify-between gap-4">
                    <!-- Brand Wordmark (Matches VincentGolf weight split) -->
                    <a href="{{ route('home') }}" class="inline-flex items-baseline text-[20px] sm:text-[22px] tracking-[-0.03em] text-[#141413] group">
                        <span class="font-normal">{{ $brandPrefix }}</span><span class="font-bold">{{ $brandSuffix }}</span>
                        <span class="ml-1.5 w-1.5 h-1.5 rounded-full bg-[#c3b091] group-hover:scale-125 transition"></span>
                    </a>

                    <!-- Center Floating Pill Navbar -->
                    <nav aria-label="Main Navigation" class="hidden lg:inline-flex items-center bg-[#f6f4f0] rounded-full p-1 border border-[#ede8df]">
                        @php
                            $navItems = [
                                ['label' => 'Reserve', 'route' => 'home', 'active' => request()->routeIs('home', 'booking.*')],
                                ['label' => 'Tables', 'route' => 'tables', 'active' => request()->routeIs('tables')],
                                ['label' => 'Membership', 'route' => 'membership', 'active' => request()->routeIs('membership')],
                                ['label' => 'Events', 'route' => 'events', 'active' => request()->routeIs('events*')],
                                ['label' => 'Gallery', 'route' => 'gallery', 'active' => request()->routeIs('gallery')],
                                ['label' => 'Contact', 'route' => 'contact', 'active' => request()->routeIs('contact')],
                            ];
                        @endphp
                        @foreach ($navItems as $item)
                            <a href="{{ route($item['route']) }}"
                               class="px-4 py-1.5 rounded-full text-[12.5px] transition duration-150 {{ $item['active'] ? 'bg-[#e7dcc8] text-[#4f3e22] font-semibold shadow-[0_1px_2px_rgba(0,0,0,0.04)]' : 'text-[#57544e] hover:text-[#141413] font-medium' }}">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    <!-- Right Pill CTA + Mobile Hamburger -->
                    <div class="flex items-center gap-2.5">
                        @auth
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="hidden sm:inline-flex items-center px-3.5 py-1.5 rounded-full border border-[#c3b091] bg-[#fbf9f5] text-[12px] font-semibold text-[#6e5b3e] hover:bg-[#f2eadb] transition">
                                    CMS Panel
                                </a>
                            @endif
                        @endauth

                        <a href="{{ request()->routeIs('home') ? '#booking-section' : route('home') }}"
                           class="inline-flex items-center gap-2.5 bg-[#c3b091] hover:bg-[#b59f7c] text-[#171512] font-semibold text-[12.5px] rounded-full pl-4 pr-1.5 py-1.5 transition shadow-sm">
                            <span>Book Table</span>
                            <span class="w-7 h-7 rounded-full bg-white text-[#171512] flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7 17L17 7"/><path d="M8 7h9v9"/>
                                </svg>
                            </span>
                        </a>

                        <!-- Mobile Menu Trigger -->
                        <button type="button" id="mobile-menu-btn" aria-label="Toggle Menu"
                                class="lg:hidden w-10 h-10 rounded-full border border-[#e7e2d8] bg-[#faf8f5] flex items-center justify-center text-[#171512]">
                            <svg id="mobile-icon-open" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/>
                            </svg>
                            <svg id="mobile-icon-close" class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Responsive Mobile Navigation Drawer -->
                <div id="mobile-menu-drawer" class="hidden lg:hidden mt-4 rounded-2xl border border-[#ece5d8] bg-[#faf8f4] p-3 space-y-1">
                    @foreach ($navItems as $item)
                        <a href="{{ route($item['route']) }}"
                           class="block px-4 py-2.5 rounded-xl text-[13.5px] {{ $item['active'] ? 'bg-[#e7dcc8] text-[#4f3e22] font-semibold' : 'text-[#44413c] hover:bg-white font-medium' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                    <div class="pt-2 mt-2 border-t border-[#e8e0d2] flex items-center justify-between px-2">
                        <a href="{{ route('contact') }}#lookup" class="text-xs font-semibold text-[#6e5b3e] hover:underline">Check Booking Code</a>
                        <a href="{{ route('admin.login') }}" class="text-xs font-medium text-stone-500 hover:text-stone-800">Admin CMS →</a>
                    </div>
                </div>
            </header>

            <!-- Global Flash Alerts -->
            @if (session('success') || session('error') || $errors->any())
                <div class="no-print max-w-[1040px] mx-auto px-5 sm:px-10 mt-2">
                    @if (session('success'))
                        <div class="rounded-2xl border border-[#d6c5a9] bg-[#faf6ef] px-5 py-3.5 text-[13.5px] text-[#4f3e22] flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-[#9e845b] shrink-0"></span>
                                <span class="font-medium">{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-3.5 text-[13.5px] text-red-800 font-medium">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-3.5 text-[13px] text-red-800">
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Main Page Body -->
            <main>
                @yield('content')
            </main>
        </div>

        <!-- Seamless Bottom Rounded CTA Banner + Obsidian Footer Block (Matches Reference Screenshot) -->
        <div class="no-print mx-2.5 sm:mx-5 mb-2.5 sm:mb-5 rounded-[22px] sm:rounded-[26px] overflow-hidden">
            @hasSection('hide_cta_banner')
            @else
                <!-- Atmospheric Visual Banner -->
                <section class="relative min-h-[400px] sm:min-h-[460px] flex items-center justify-center text-center px-6 py-20 overflow-hidden">
                    <img src="{{ $bannerUrl }}" alt="Hule Mahjong Club" class="absolute inset-0 w-full h-full object-cover object-center scale-[1.01]">
                    <div class="absolute inset-0 bg-gradient-to-b from-[#15120d]/55 via-[#15120d]/45 to-[#0d0e0c]/85"></div>

                    <div class="relative z-10 max-w-[620px] mx-auto">
                        <p class="text-[10px] sm:text-[11px] uppercase tracking-[0.16em] text-[#e7dcc8] font-semibold mb-4">
                            {{ $siteSettings['banner_eyebrow'] ?? '2026 MEMBERSHIP — LIMITED PLACES' }}
                        </p>
                        <h2 class="text-[32px] sm:text-[42px] lg:text-[46px] leading-[1.13] tracking-[-0.03em] text-white">
                            <span class="font-bold">{{ $siteSettings['banner_title_line1_bold'] ?? 'Your First' }}</span>
                            <span class="font-light text-white/95">{{ $siteSettings['banner_title_line1_light'] ?? 'Round' }}</span><br>
                            <span class="font-light text-white/95">{{ $siteSettings['banner_title_line2_light'] ?? 'Starts' }}</span>
                            <span class="font-bold">{{ $siteSettings['banner_title_line2_bold'] ?? 'Here.' }}</span>
                        </h2>
                        <p class="mt-4 text-[13px] sm:text-[14px] leading-[1.65] text-white/80 max-w-[490px] mx-auto">
                            {{ $siteSettings['banner_description'] ?? 'A curated number of Hule Club memberships open each season. Submit your application for priority automatic table reservations, VIP suite access, and tournament invitations.' }}
                        </p>

                        <div class="mt-8 flex flex-wrap items-center justify-center gap-3.5">
                            <a href="{{ route('membership') }}#apply"
                               class="inline-flex items-center gap-3 bg-[#c3b091] hover:bg-[#d2c1a5] text-[#171512] font-semibold text-[12.5px] rounded-full pl-5 pr-1.5 py-1.5 transition shadow-lg">
                                <span>Apply for Membership</span>
                                <span class="w-7 h-7 rounded-full bg-white text-[#171512] flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M7 17L17 7"/><path d="M8 7h9v9"/>
                                    </svg>
                                </span>
                            </a>

                            <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Hi Hule Mahjong Concierge, I would like to inquire about table reservations / membership.') }}"
                               target="_blank" rel="noopener"
                               class="inline-flex items-center gap-3 bg-white/15 hover:bg-white/25 backdrop-blur-md border border-white/60 text-white font-medium text-[12.5px] rounded-full pl-5 pr-1.5 py-1.5 transition">
                                <span>Contact the Concierge</span>
                                <span class="w-7 h-7 rounded-full bg-white text-[#171512] flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M7 17L17 7"/><path d="M8 7h9v9"/>
                                    </svg>
                                </span>
                            </a>
                        </div>
                    </div>
                </section>
            @endif

            <!-- Deep Obsidian Footer -->
            <footer class="bg-[#0d0e0c] text-white px-6 sm:px-12 lg:px-16 pt-14 sm:pt-16 pb-9">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10 pb-12 border-b border-white/10">
                    <!-- Column 1: Brand & Address -->
                    <div class="lg:col-span-4 space-y-5">
                        <a href="{{ route('home') }}" class="inline-flex items-baseline text-[22px] tracking-[-0.03em] text-white">
                            <span class="font-light">{{ $brandPrefix }}</span><span class="font-bold text-[#c3b091]">{{ $brandSuffix }}</span>
                        </a>

                        <div class="space-y-3 text-[12.5px] text-white/65 max-w-[280px]">
                            <div class="flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-[#c3b091] shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M12 21s-7-5.686-7-11a7 7 0 1 1 14 0c0 5.314-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>
                                </svg>
                                <span>{{ $siteSettings['contact_address'] ?? 'Rukan La Riviera PIK 2, Tangerang & Jl. M. Isa, Palembang' }}</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-[#c3b091] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                                <span>{{ $siteSettings['contact_phone'] ?? '+62 812-8899-7788' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: Lounge -->
                    <div class="lg:col-span-2">
                        <h3 class="text-[13px] font-semibold text-white mb-4">Lounge</h3>
                        <ul class="space-y-2.5 text-[12px] text-white/60">
                            <li><a href="{{ route('home') }}" class="hover:text-[#c3b091] transition">Book Automatic Table</a></li>
                            <li><a href="{{ route('tables') }}" class="hover:text-[#c3b091] transition">Suites & Pricing</a></li>
                            <li><a href="{{ route('gallery') }}" class="hover:text-[#c3b091] transition">Interior Gallery</a></li>
                            <li><a href="{{ route('tables') }}#etiquette" class="hover:text-[#c3b091] transition">House Etiquette</a></li>
                        </ul>
                    </div>

                    <!-- Column 3: Club -->
                    <div class="lg:col-span-2">
                        <h3 class="text-[13px] font-semibold text-white mb-4">Club</h3>
                        <ul class="space-y-2.5 text-[12px] text-white/60">
                            <li><a href="{{ route('events', ['category' => 'Beginner Coaching']) }}" class="hover:text-[#c3b091] transition">Beginner Coaching</a></li>
                            <li><a href="{{ route('events', ['category' => 'Tournament']) }}" class="hover:text-[#c3b091] transition">Championships</a></li>
                            <li><a href="{{ route('events', ['category' => 'Social Night']) }}" class="hover:text-[#c3b091] transition">Friday Mixers</a></li>
                            <li><a href="{{ route('events') }}" class="hover:text-[#c3b091] transition">All Events</a></li>
                        </ul>
                    </div>

                    <!-- Column 4: Membership -->
                    <div class="lg:col-span-2">
                        <h3 class="text-[13px] font-semibold text-white mb-4">Membership</h3>
                        <ul class="space-y-2.5 text-[12px] text-white/60">
                            <li><a href="{{ route('membership') }}#apply" class="hover:text-[#c3b091] transition">Apply Now</a></li>
                            <li><a href="{{ route('membership') }}" class="hover:text-[#c3b091] transition">Privilege Tiers</a></li>
                            <li><a href="{{ route('contact') }}#lookup" class="hover:text-[#c3b091] transition">Check My Booking</a></li>
                            <li><a href="{{ route('membership') }}#faq" class="hover:text-[#c3b091] transition">Member FAQs</a></li>
                        </ul>
                    </div>

                    <!-- Column 5: Support -->
                    <div class="lg:col-span-2">
                        <h3 class="text-[13px] font-semibold text-white mb-4">Support</h3>
                        <ul class="space-y-2.5 text-[12px] text-white/60">
                            <li><a href="{{ route('contact') }}" class="hover:text-[#c3b091] transition">Concierge Desk</a></li>
                            <li><a href="{{ route('tables') }}#etiquette" class="hover:text-[#c3b091] transition">No-Gambling Policy</a></li>
                            <li><a href="{{ route('admin.login') }}" class="text-[#c3b091] hover:underline font-medium">Admin CMS Login</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Copyright, BENCHCODE Trademark & Social Circles -->
                <div class="pt-7 flex flex-col lg:flex-row items-center justify-between gap-4">
                    <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-3 text-[11.5px] text-white/45 text-center sm:text-left">
                        <p>© {{ date('Y') }} {{ $brandPrefix }} {{ $brandSuffix }} Indonesia. {{ $siteSettings['house_policy'] ?? 'Strictly recreational & strategic point play.' }}</p>
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-3">
                        <!-- BENCHCODE Official Trademark Badge -->
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-white/15 bg-white/[0.04] text-[11px] text-white/75 tracking-wide">
                            <svg class="w-3.5 h-4 shrink-0" viewBox="0 0 60 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 4H8a4 4 0 0 0-4 4v54a4 4 0 0 0 4 4h10" stroke="#c3b091" stroke-width="7" stroke-linecap="round"/>
                                <path d="M42 4h10a4 4 0 0 1 4 4v54a4 4 0 0 1-4 4H42" stroke="#c3b091" stroke-width="7" stroke-linecap="round"/>
                                <line x1="16" y1="26" x2="34" y2="26" stroke="#27B084" stroke-width="6" stroke-linecap="round"/>
                                <line x1="16" y1="38" x2="28" y2="38" stroke="#27B084" stroke-width="6" stroke-linecap="round"/>
                            </svg>
                            <span>Powered by <strong class="font-bold text-white tracking-wider">BENCHCODE™</strong></span>
                        </span>

                        <div class="flex items-center gap-2">
                            <a href="{{ $siteSettings['contact_instagram'] ?? 'https://instagram.com/hulemahjong.id' }}" target="_blank" rel="noopener" aria-label="Instagram"
                               class="w-8 h-8 rounded-full border border-white/15 hover:border-[#c3b091] text-white/70 hover:text-[#c3b091] flex items-center justify-center transition">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg>
                            </a>
                            <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener" aria-label="WhatsApp"
                               class="w-8 h-8 rounded-full border border-white/15 hover:border-[#c3b091] text-white/70 hover:text-[#c3b091] flex items-center justify-center transition">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                            </a>
                            <a href="{{ route('events') }}" aria-label="Events"
                               class="w-8 h-8 rounded-full border border-white/15 hover:border-[#c3b091] text-white/70 hover:text-[#c3b091] flex items-center justify-center transition">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                            </a>
                            <a href="{{ route('contact') }}" aria-label="Location"
                               class="w-8 h-8 rounded-full border border-white/15 hover:border-[#c3b091] text-white/70 hover:text-[#c3b091] flex items-center justify-center transition">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4l16 16M20 4L4 20"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script>
        (function () {
            const btn = document.getElementById('mobile-menu-btn');
            const drawer = document.getElementById('mobile-menu-drawer');
            const openIcon = document.getElementById('mobile-icon-open');
            const closeIcon = document.getElementById('mobile-icon-close');
            if (btn && drawer) {
                btn.addEventListener('click', function () {
                    const isHidden = drawer.classList.toggle('hidden');
                    openIcon.classList.toggle('hidden', !isHidden);
                    closeIcon.classList.toggle('hidden', isHidden);
                });
            }
        })();
    </script>
    @stack('scripts')
</body>
</html>
