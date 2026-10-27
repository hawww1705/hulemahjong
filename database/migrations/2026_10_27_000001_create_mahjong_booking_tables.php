<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('table_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('branch')->default('PIK 2 — La Riviera');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('weekday_price')->default(87000);
            $table->unsignedInteger('weekend_price')->default(115000);
            $table->unsignedInteger('peak_surcharge')->default(15000);
            $table->unsignedSmallInteger('session_minutes')->default(120);
            $table->unsignedTinyInteger('min_players')->default(4);
            $table->unsignedTinyInteger('max_players')->default(6);
            $table->string('image_path')->nullable();
            $table->json('amenities')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('mahjong_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_category_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('floor_zone')->default('Main Lounge — 1F');
            $table->string('machine_model')->default('Hule Whisper-Pro Auto');
            $table->string('status')->default('active'); // active, maintenance, inactive
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->string('start_time', 5); // HH:MM
            $table->string('label', 20);
            $table->boolean('is_peak')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->string('discount_type')->default('percentage'); // percentage, fixed
            $table->unsignedInteger('discount_value')->default(10);
            $table->unsignedInteger('max_discount')->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('table_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mahjong_table_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->date('booking_date')->index();
            $table->string('start_time', 5);
            $table->string('end_time', 5);
            $table->unsignedTinyInteger('sessions_count')->default(1);
            $table->unsignedSmallInteger('duration_minutes')->default(120);
            $table->unsignedTinyInteger('players_count')->default(4);
            $table->unsignedInteger('base_price');
            $table->unsignedInteger('discount_amount')->default(0);
            $table->string('promo_code')->nullable();
            $table->unsignedInteger('total_price');
            $table->string('status')->default('confirmed')->index(); // pending, confirmed, completed, cancelled
            $table->string('payment_status')->default('unpaid'); // unpaid, waiting_verification, paid, refunded
            $table->string('payment_method')->default('qris_transfer');
            $table->string('payment_proof_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('Tournament');
            $table->date('event_date')->index();
            $table->string('start_time', 10);
            $table->string('end_time', 10)->nullable();
            $table->string('location')->default('Hule Mahjong PIK 2 — La Riviera');
            $table->unsignedInteger('price')->default(0);
            $table->unsignedSmallInteger('quota')->default(32);
            $table->unsignedSmallInteger('registered_count')->default(0);
            $table->string('image_path')->nullable();
            $table->string('excerpt', 300)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('registration_code')->unique();
            $table->string('participant_name');
            $table->string('participant_phone');
            $table->string('participant_email')->nullable();
            $table->string('skill_level')->default('Intermediate');
            $table->unsignedTinyInteger('seats')->default(1);
            $table->unsignedInteger('total_price')->default(0);
            $table->string('status')->default('confirmed');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->default('Lounge & Interior');
            $table->string('caption')->nullable();
            $table->string('image_path');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('badge')->nullable();
            $table->unsignedInteger('price');
            $table->string('billing_period')->default('per quarter');
            $table->text('description')->nullable();
            $table->json('benefits')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('membership_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_plan_id')->constrained()->cascadeOnDelete();
            $table->string('applicant_name');
            $table->string('applicant_phone');
            $table->string('applicant_email');
            $table->string('preferred_branch')->default('PIK 2 — La Riviera');
            $table->string('playing_frequency')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('pending'); // pending, approved, contacted, rejected
            $table->timestamps();
        });

        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('subject');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('group')->default('general');
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('inquiries');
        Schema::dropIfExists('membership_applications');
        Schema::dropIfExists('membership_plans');
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('events');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('promo_codes');
        Schema::dropIfExists('time_slots');
        Schema::dropIfExists('mahjong_tables');
        Schema::dropIfExists('table_categories');
    }
};
