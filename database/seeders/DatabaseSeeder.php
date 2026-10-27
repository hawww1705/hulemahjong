<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\Inquiry;
use App\Models\MahjongTable;
use App\Models\MembershipPlan;
use App\Models\PromoCode;
use App\Models\SiteSetting;
use App\Models\TableCategory;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin CMS User
        User::updateOrCreate(
            ['email' => 'admin@hulemahjong.id'],
            [
                'name' => 'Hule Concierge Admin',
                'phone' => '+62 812-8899-7788',
                'role' => 'admin',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Site Settings (CMS Editable)
        $settings = [
            ['key' => 'brand_prefix', 'group' => 'general', 'value' => 'Hule'],
            ['key' => 'brand_suffix', 'group' => 'general', 'value' => 'Mahjong'],
            ['key' => 'site_tagline', 'group' => 'general', 'value' => 'Elevated Social Mahjong Lounge & Private Suites'],
            ['key' => 'house_policy', 'group' => 'general', 'value' => 'Strictly recreational & strategic point play. Gambling of any kind is prohibited.'],
            ['key' => 'cancellation_note', 'group' => 'booking', 'value' => 'Free rescheduling up to 24h before session time.'],
            ['key' => 'banner_eyebrow', 'group' => 'banner', 'value' => '2026 MEMBERSHIP — LIMITED PLACES'],
            ['key' => 'banner_title_line1_bold', 'group' => 'banner', 'value' => 'Your First'],
            ['key' => 'banner_title_line1_light', 'group' => 'banner', 'value' => 'Round'],
            ['key' => 'banner_title_line2_light', 'group' => 'banner', 'value' => 'Starts'],
            ['key' => 'banner_title_line2_bold', 'group' => 'banner', 'value' => 'Here.'],
            ['key' => 'banner_description', 'group' => 'banner', 'value' => 'A curated number of Hule Club memberships open each season. Submit your application for priority automatic table reservations, VIP suite access, and tournament invitations.'],
            ['key' => 'banner_image', 'group' => 'banner', 'value' => 'images/mahjong/cta-banner.jpg'],
            ['key' => 'contact_address', 'group' => 'contact', 'value' => 'Rukan La Riviera PIK 2, Tangerang & Jl. M. Isa, Palembang'],
            ['key' => 'contact_phone', 'group' => 'contact', 'value' => '+62 812-8899-7788'],
            ['key' => 'contact_whatsapp', 'group' => 'contact', 'value' => '6281288997788'],
            ['key' => 'contact_email', 'group' => 'contact', 'value' => 'concierge@hulemahjong.id'],
            ['key' => 'contact_instagram', 'group' => 'contact', 'value' => 'https://instagram.com/hulemahjong.id'],
            ['key' => 'opening_hours', 'group' => 'contact', 'value' => 'Daily 10:00 – 24:00 WIB'],
            ['key' => 'payment_bank_name', 'group' => 'payment', 'value' => 'BCA (Bank Central Asia)'],
            ['key' => 'payment_account_number', 'group' => 'payment', 'value' => '8830-9918-27'],
            ['key' => 'payment_account_holder', 'group' => 'payment', 'value' => 'PT Hule Mahjong Indonesia'],
        ];

        foreach ($settings as $s) {
            SiteSetting::updateOrCreate(['key' => $s['key']], $s);
        }

        // 3. Time Slots (8 slots matching 5 + 3 pill grid rhythm)
        $slots = [
            ['start_time' => '10:00', 'label' => '10:00', 'is_peak' => false, 'sort_order' => 1],
            ['start_time' => '11:30', 'label' => '11:30', 'is_peak' => false, 'sort_order' => 2],
            ['start_time' => '13:00', 'label' => '13:00', 'is_peak' => false, 'sort_order' => 3],
            ['start_time' => '14:30', 'label' => '14:30', 'is_peak' => false, 'sort_order' => 4],
            ['start_time' => '16:00', 'label' => '16:00', 'is_peak' => false, 'sort_order' => 5],
            ['start_time' => '17:30', 'label' => '17:30', 'is_peak' => true, 'sort_order' => 6],
            ['start_time' => '19:30', 'label' => '19:30', 'is_peak' => true, 'sort_order' => 7],
            ['start_time' => '21:30', 'label' => '21:30', 'is_peak' => true, 'sort_order' => 8],
        ];

        foreach ($slots as $slot) {
            TimeSlot::updateOrCreate(['start_time' => $slot['start_time']], $slot + ['is_active' => true]);
        }

        // 4. Table Categories & Physical Automatic Tables
        $regular = TableCategory::updateOrCreate(
            ['slug' => 'regular-social-lounge'],
            [
                'name' => 'Regular Social Lounge',
                'branch' => 'PIK 2 — La Riviera',
                'subtitle' => 'Open Social Hall • Whisper-Quiet Auto Table',
                'description' => 'Bright, architecturally curated open lounge featuring whisper-quiet automatic mahjong tables, ergonomic seating, and full artisanal tea & dim sum service.',
                'weekday_price' => 87000,
                'weekend_price' => 115000,
                'peak_surcharge' => 15000,
                'session_minutes' => 120,
                'min_players' => 4,
                'max_players' => 5,
                'image_path' => 'images/mahjong/lounge-regular.jpg',
                'amenities' => ['Automatic Shuffler Table', 'Score Reference Cards', 'USB-C Table Charging', 'F&B Table Service'],
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $vip = TableCategory::updateOrCreate(
            ['slug' => 'private-vip-suite'],
            [
                'name' => 'Private VIP Suite',
                'branch' => 'PIK 2 — La Riviera',
                'subtitle' => '3×3m Enclosed Acoustic Room • Dedicated Butler Call',
                'description' => 'Intimate private 3×3m suite wrapped in warm champagne oak and acoustic travertine panels. Ideal for uninterrupted rounds with friends and complimentary hot oolong.',
                'weekday_price' => 175000,
                'weekend_price' => 225000,
                'peak_surcharge' => 25000,
                'session_minutes' => 120,
                'min_players' => 4,
                'max_players' => 6,
                'image_path' => 'images/mahjong/vip-suite.jpg',
                'amenities' => ['Private Acoustic Room', 'Treyo Ultra-Silent Auto Table', 'Complimentary Oolong Pot', 'Bluetooth Lounge Audio', 'Air Purifier'],
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $vvip = TableCategory::updateOrCreate(
            ['slug' => 'imperial-vvip-salon'],
            [
                'name' => 'Imperial VVIP Salon',
                'branch' => 'PIK 2 — La Riviera',
                'subtitle' => 'Grand Private Salon • Tea Bar & Lounge Sofa',
                'description' => 'Our flagship private sanctuary featuring bespoke champagne-gold automatic mahjong hardware, private lounge sofa seating, artisanal tea service, and priority host attention.',
                'weekday_price' => 295000,
                'weekend_price' => 360000,
                'peak_surcharge' => 35000,
                'session_minutes' => 120,
                'min_players' => 4,
                'max_players' => 8,
                'image_path' => 'images/mahjong/vvip-imperial.jpg',
                'amenities' => ['Grand Private Salon', 'Bespoke Gold Series Table', 'Lounge Sofa & Tea Bar', 'Complimentary Dim Sum Platter', 'Dedicated Concierge'],
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        $palembang = TableCategory::updateOrCreate(
            ['slug' => 'palembang-social-lounge'],
            [
                'name' => 'Palembang Social Lounge',
                'branch' => 'Palembang — Jl. M. Isa',
                'subtitle' => 'Main Club Hall • Beginner & Pro Friendly',
                'description' => 'Located on Jl. M. Isa Palembang with 15 modern automatic mahjong tables, friendly table hosts for newcomers, and signature refreshments.',
                'weekday_price' => 50000,
                'weekend_price' => 70000,
                'peak_surcharge' => 10000,
                'session_minutes' => 120,
                'min_players' => 4,
                'max_players' => 5,
                'image_path' => 'images/mahjong/lounge-regular.jpg',
                'amenities' => ['Automatic Shuffler Table', 'Beginner Rule Guide', 'F&B Menu Access', 'Free Wi-Fi'],
                'is_active' => true,
                'sort_order' => 4,
            ]
        );

        // Seed Physical Tables
        $physicalTables = [
            [$regular->id, 'REG-01', 'Social Auto #01', '1F — Main Lounge', 'Hule Whisper-Pro S1', 'active'],
            [$regular->id, 'REG-02', 'Social Auto #02', '1F — Main Lounge', 'Hule Whisper-Pro S1', 'active'],
            [$regular->id, 'REG-03', 'Social Auto #03', '1F — Main Lounge', 'Hule Whisper-Pro S1', 'active'],
            [$regular->id, 'REG-04', 'Social Auto #04', '1F — Main Lounge', 'Hule Whisper-Pro S1', 'active'],
            [$regular->id, 'REG-05', 'Social Auto #05', '1F — Main Lounge', 'Hule Whisper-Pro S1', 'active'],
            [$regular->id, 'REG-06', 'Social Auto #06', '1F — Window Bay', 'Hule Whisper-Pro S1', 'maintenance'],

            [$vip->id, 'VIP-01', 'VIP Sakura Suite', '2F — Private Wing', 'Treyo Champagne Silent', 'active'],
            [$vip->id, 'VIP-02', 'VIP Jade Suite', '2F — Private Wing', 'Treyo Champagne Silent', 'active'],
            [$vip->id, 'VIP-03', 'VIP Alabaster Suite', '2F — Private Wing', 'Treyo Champagne Silent', 'active'],

            [$vvip->id, 'VVIP-01', 'Imperial Salon East', '3F — Penthouse Wing', 'Hule Bespoke Gold Edition', 'active'],
            [$vvip->id, 'VVIP-02', 'Imperial Salon West', '3F — Penthouse Wing', 'Hule Bespoke Gold Edition', 'active'],

            [$palembang->id, 'PLB-01', 'Palembang Auto #01', 'Main Hall', 'Hule Whisper-Pro S1', 'active'],
            [$palembang->id, 'PLB-02', 'Palembang Auto #02', 'Main Hall', 'Hule Whisper-Pro S1', 'active'],
            [$palembang->id, 'PLB-03', 'Palembang Auto #03', 'Main Hall', 'Hule Whisper-Pro S1', 'active'],
            [$palembang->id, 'PLB-04', 'Palembang Auto #04', 'Main Hall', 'Hule Whisper-Pro S1', 'active'],
        ];

        foreach ($physicalTables as [$catId, $code, $name, $zone, $model, $status]) {
            MahjongTable::updateOrCreate(
                ['code' => $code],
                [
                    'table_category_id' => $catId,
                    'name' => $name,
                    'floor_zone' => $zone,
                    'machine_model' => $model,
                    'status' => $status,
                ]
            );
        }

        // 5. Promo Codes
        PromoCode::updateOrCreate(
            ['code' => 'HULE10'],
            [
                'description' => '10% Welcome Discount (up to Rp 50.000)',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'max_discount' => 50000,
                'usage_limit' => 200,
                'valid_until' => now()->addMonths(6)->toDateString(),
                'is_active' => true,
            ]
        );

        PromoCode::updateOrCreate(
            ['code' => 'FIRSTROUND'],
            [
                'description' => 'Rp 20.000 off your first mahjong session',
                'discount_type' => 'fixed',
                'discount_value' => 20000,
                'usage_limit' => 100,
                'valid_until' => now()->addMonths(6)->toDateString(),
                'is_active' => true,
            ]
        );

        // 6. Seed Realistic Sample Bookings (including 1 fully booked slot on VIP tomorrow at 13:00 so disabled pill state is demonstrated!)
        $tomorrow = now()->addDay()->toDateString();
        $today = now()->toDateString();
        $vipTables = MahjongTable::where('table_category_id', $vip->id)->where('status', 'active')->get();

        foreach ($vipTables as $idx => $vt) {
            Booking::updateOrCreate(
                ['booking_code' => 'HLM-DEMO-VIP' . ($idx + 1)],
                [
                    'table_category_id' => $vip->id,
                    'mahjong_table_id' => $vt->id,
                    'customer_name' => ['Clarissa Tanoto', 'Kevin Wijaya', 'Michelle Halim'][$idx] ?? 'Member Guest',
                    'customer_phone' => '08128000110' . ($idx + 1),
                    'customer_email' => 'guest' . ($idx + 1) . '@example.com',
                    'booking_date' => $tomorrow,
                    'start_time' => '13:00',
                    'end_time' => '15:00',
                    'sessions_count' => 1,
                    'duration_minutes' => 120,
                    'players_count' => 4,
                    'base_price' => 175000,
                    'discount_amount' => 0,
                    'total_price' => 175000,
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                ]
            );
        }

        $regTable1 = MahjongTable::where('code', 'REG-01')->first();
        if ($regTable1) {
            Booking::updateOrCreate(
                ['booking_code' => 'HLM-DEMO-REG1'],
                [
                    'table_category_id' => $regular->id,
                    'mahjong_table_id' => $regTable1->id,
                    'customer_name' => 'Brandon Kusuma',
                    'customer_phone' => '081299887766',
                    'customer_email' => 'brandon@example.com',
                    'booking_date' => $today,
                    'start_time' => '19:30',
                    'end_time' => '21:30',
                    'sessions_count' => 1,
                    'duration_minutes' => 120,
                    'players_count' => 4,
                    'base_price' => 102000,
                    'discount_amount' => 0,
                    'total_price' => 102000,
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                ]
            );
        }

        // 7. Seed Events
        $events = [
            [
                'slug' => 'hule-champagne-cup-2026',
                'title' => 'Hule Champagne Cup — Autumn Championship',
                'category' => 'Tournament',
                'event_date' => now()->addDays(10)->toDateString(),
                'start_time' => '13:00',
                'end_time' => '19:00',
                'location' => 'Hule Mahjong PIK 2 — Main Salon',
                'price' => 250000,
                'quota' => 32,
                'registered_count' => 24,
                'image_path' => 'images/mahjong/event-tournament.jpg',
                'excerpt' => 'Four-round strategic point tournament featuring automatic tables, official referees, bespoke trophies, and afternoon dim sum reception.',
                'description' => "Join our flagship seasonal tournament celebrating pure strategic tile play. Players compete across four timed 60-minute hanchan/rounds using official Hule recreational point scoring.\n\nEntry includes welcome specialty oolong tea, artisanal dim sum reception, tournament scorecard kit, and eligibility for the Hule Champagne Trophy & VIP lounge credits.",
                'is_featured' => true,
            ],
            [
                'slug' => 'mahjong-101-guided-beginner-clinic',
                'title' => 'Mahjong 101: Guided Beginner & Etiquette Clinic',
                'category' => 'Beginner Coaching',
                'event_date' => now()->addDays(4)->toDateString(),
                'start_time' => '15:00',
                'end_time' => '17:30',
                'location' => 'Hule Mahjong PIK 2 — Coaching Lounge',
                'price' => 125000,
                'quota' => 16,
                'registered_count' => 9,
                'image_path' => 'images/mahjong/event-coaching.jpg',
                'excerpt' => 'Never called "Hule" before? Learn tile suits, melds, automatic table etiquette, and winning hand patterns with our patient table coaches.',
                'description' => "Designed specifically for complete newcomers and casual players looking to build confidence. Our resident hosts walk you step-by-step through tile recognition, Chow/Pung/Kong combinations, how automatic mahjong tables work, and your first guided practice rounds.\n\nCome solo or bring friends — we pair solo learners into welcoming tables of four.",
                'is_featured' => true,
            ],
            [
                'slug' => 'friday-tiles-and-tea-social-mixer',
                'title' => 'Friday Night Tiles & Artisanal Tea Social',
                'category' => 'Social Night',
                'event_date' => now()->addDays(7)->toDateString(),
                'start_time' => '19:00',
                'end_time' => '22:00',
                'location' => 'Hule Mahjong PIK 2 & Palembang',
                'price' => 95000,
                'quota' => 28,
                'registered_count' => 18,
                'image_path' => 'images/mahjong/gallery-fnb.jpg',
                'excerpt' => 'Solo-player friendly Friday evening mixer! Rotate tables every hour, meet fellow mahjong enthusiasts, and enjoy curated cold-brew teas.',
                'description' => "No full group of four? No problem. Friday Night Tiles & Tea matches solo and duo players into balanced tables based on comfort level. Includes 3 hours of open play plus a complimentary signature iced latte or oolong pot.",
                'is_featured' => false,
            ],
        ];

        foreach ($events as $ev) {
            Event::updateOrCreate(['slug' => $ev['slug']], $ev + ['is_active' => true]);
        }

        // 8. Seed Gallery
        $galleryItems = [
            ['title' => 'Main Social Lounge — PIK 2', 'category' => 'Lounge & Interior', 'caption' => 'Sunlit champagne-oak architecture with whisper-quiet automatic tables.', 'image_path' => 'images/mahjong/lounge-regular.jpg', 'is_featured' => true, 'sort_order' => 1],
            ['title' => 'Private VIP Acoustic Suite', 'category' => 'VIP Suites', 'caption' => 'Enclosed 3×3m VIP suites crafted for intimate four-player sessions.', 'image_path' => 'images/mahjong/vip-suite.jpg', 'is_featured' => true, 'sort_order' => 2],
            ['title' => 'Imperial Penthouse VVIP Salon', 'category' => 'VIP Suites', 'caption' => 'Bespoke gold-series automatic mahjong table with private lounge seating.', 'image_path' => 'images/mahjong/vvip-imperial.jpg', 'is_featured' => true, 'sort_order' => 3],
            ['title' => 'Seasonal Point Championship', 'category' => 'Tournaments & Community', 'caption' => 'Competitive recreational strategy rounds in our main hall.', 'image_path' => 'images/mahjong/event-tournament.jpg', 'is_featured' => true, 'sort_order' => 4],
            ['title' => 'Beginner Coaching & Tile Mastery', 'category' => 'Tournaments & Community', 'caption' => 'Hands-on coaching clinics guiding newcomers to their first winning hand.', 'image_path' => 'images/mahjong/event-coaching.jpg', 'is_featured' => false, 'sort_order' => 5],
            ['title' => 'Artisanal Tea & Dim Sum Service', 'category' => 'Food & Beverage', 'caption' => 'Roasted tieguanyin oolong, layered iced lattes, and handmade dim sum tableside.', 'image_path' => 'images/mahjong/gallery-fnb.jpg', 'is_featured' => true, 'sort_order' => 6],
            ['title' => 'Ivory & Champagne Tile Craft', 'category' => 'Lounge & Interior', 'caption' => 'Custom-weighted tournament tiles engineered for effortless tactile play.', 'image_path' => 'images/mahjong/cta-banner.jpg', 'is_featured' => false, 'sort_order' => 7],
        ];

        foreach ($galleryItems as $g) {
            Gallery::updateOrCreate(['title' => $g['title']], $g + ['is_active' => true]);
        }

        // 9. Seed Membership Plans
        $plans = [
            [
                'slug' => 'social-club-pass',
                'name' => 'Social Club',
                'badge' => 'Essential',
                'price' => 450000,
                'billing_period' => 'per quarter',
                'description' => 'Tailored for regular weekday & weekend players who enjoy our open social lounge.',
                'benefits' => [
                    '15% off all Regular Social Lounge sessions',
                    'Priority 7-day advance slot booking',
                    'Complimentary hot oolong tea on every visit',
                    'Free entry to 1 Friday Social Mixer per month',
                ],
                'is_featured' => false,
                'sort_order' => 1,
            ],
            [
                'slug' => 'jade-privilege-member',
                'name' => 'Jade Privilege',
                'badge' => 'Most Popular',
                'price' => 1150000,
                'billing_period' => 'per quarter',
                'description' => 'Our signature tier combining complimentary VIP suite hours, peak waivers, and event priority.',
                'benefits' => [
                    '3× Complimentary 2-Hour Private VIP Suite vouchers',
                    '20% off Regular & VIP Suite bookings anytime',
                    'Peak-hour surcharge waived on all bookings',
                    '14-day priority reservation window',
                    'Priority registration for Hule Championships',
                ],
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'slug' => 'grandmaster-vip-circle',
                'name' => 'Grandmaster Circle',
                'badge' => 'Limited Intake',
                'price' => 3800000,
                'billing_period' => 'per year',
                'description' => 'By-application annual patronage with Imperial VVIP Salon privileges and personal tile locker.',
                'benefits' => [
                    '10× Complimentary 2-Hour VIP / Imperial VVIP sessions',
                    '25% privilege rate across PIK 2 & Palembang lounges',
                    'Dedicated engraved personal mahjong tile locker',
                    'Complimentary entry to all seasonal tournaments',
                    'Direct WhatsApp Concierge line for same-day holds',
                ],
                'is_featured' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            MembershipPlan::updateOrCreate(['slug' => $plan['slug']], $plan + ['is_active' => true]);
        }

        // 10. Sample Inquiry
        Inquiry::updateOrCreate(
            ['email' => 'natasha.lim@example.com', 'subject' => 'Private Corporate Mahjong Gathering (16 Guests)'],
            [
                'name' => 'Natasha Lim',
                'phone' => '08119203344',
                'message' => 'Hi Hule team, we would like to book 4 tables for a private team social next Friday afternoon with dim sum catering. Could you share package options?',
                'is_read' => false,
            ]
        );
    }
}
