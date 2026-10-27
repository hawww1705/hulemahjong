<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Inquiry;
use App\Models\MembershipApplication;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            static $settings = null;
            if ($settings === null) {
                $settings = Schema::hasTable('site_settings') ? SiteSetting::allAsMap() : [];
            }
            $view->with('siteSettings', $settings);
        });

        View::composer('admin.*', function ($view) {
            $pendingBookingsCount = Schema::hasTable('bookings')
                ? Booking::whereIn('status', ['pending'])->orWhere('payment_status', 'waiting_verification')->count()
                : 0;
            $unreadInquiriesCount = Schema::hasTable('inquiries')
                ? Inquiry::where('is_read', false)->count()
                : 0;
            $pendingMembershipsCount = Schema::hasTable('membership_applications')
                ? MembershipApplication::where('status', 'pending')->count()
                : 0;

            $view->with(compact('pendingBookingsCount', 'unreadInquiriesCount', 'pendingMembershipsCount'));
        });
    }
}
