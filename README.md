# Hule Mahjong — Booking System & Admin CMS

Elevated Social Mahjong Lounge reservation platform & full-featured Content Management System built with **Laravel 10**, **Tailwind CSS**, and **Vite**.

Engineered & Powered by **BENCHCODE™** (`benchcode.dev`).

## Key Features
- **Real-Time Automatic Table Booking Engine**: Checks live availability per physical mahjong table unit across time slots, dates, and suite categories (`Regular Social Lounge`, `Private VIP Suite`, `Imperial VVIP Salon`, `Palembang Lounge`).
- **Dynamic Pricing & Promo Vouchers**: Automatic Weekday vs. Weekend rates, evening peak-hour surcharges, multi-session calculation, and promo code validation (`HULE10`, `FIRSTROUND`).
- **1-Page Printable Official Reservation E-Pass**: Dedicated boarding-pass PDF layout engineered to fit strictly on a single page (Landscape & Portrait) with payment proof upload and WhatsApp concierge integration.
- **Full Admin CMS (`/admin`)**:
  - Executive Dashboard & Live Table Occupancy Matrix
  - Reservations & Walk-In Booking Management
  - Suite Pricing Editor & 1-Click Add/Remove Physical Tables (`+ Tambah 1 Meja Otomatis` / `- Kurangi 1 Meja`)
  - Time Slots & Promo Voucher Management
  - Tournaments, Beginner Coaching Clinics & Participant Registrations
  - Photo & Media Gallery Management
  - Patron Membership Tiers, Applications & Website Settings

## Quick Installation

```bash
git clone https://github.com/hawww1705/hulemahjong.git
cd hulemahjong
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # Windows PowerShell: New-Item database/database.sqlite
php artisan migrate --seed
php artisan serve
```

*(Compiled frontend assets in `public/build` are included. To modify styles: `npm install && npm run dev` or `npm run build`.)*

## Default Admin CMS Login
- **URL**: `http://127.0.0.1:8000/admin/login`
- **Email**: `admin@hulemahjong.id`
- **Password**: `password123`

---
© Hule Mahjong Indonesia • System & E-Pass by **BENCHCODE™**
