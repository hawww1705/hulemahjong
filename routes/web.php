<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BookingManagementController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventManagementController;
use App\Http\Controllers\Admin\GalleryManagementController;
use App\Http\Controllers\Admin\MembershipManagementController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TableManagementController;
use App\Http\Controllers\Admin\TimeSlotController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Website & Booking System Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [BookingController::class, 'index'])->name('home');
Route::get('/api/booking-availability', [BookingController::class, 'availability'])->name('api.availability');
Route::post('/bookings', [BookingController::class, 'store'])->name('booking.store');
Route::get('/check-booking', [BookingController::class, 'lookup'])->name('booking.lookup');
Route::get('/booking/{bookingCode}', [BookingController::class, 'show'])->name('booking.show');
Route::post('/booking/{bookingCode}/payment-proof', [BookingController::class, 'uploadProof'])->name('booking.proof');

Route::get('/tables', [PageController::class, 'tables'])->name('tables');
Route::get('/membership', [PageController::class, 'membership'])->name('membership');
Route::post('/membership/apply', [PageController::class, 'applyMembership'])->name('membership.apply');

Route::get('/events', [PageController::class, 'events'])->name('events');
Route::get('/events/{slug}', [PageController::class, 'eventShow'])->name('events.show');
Route::post('/events/{slug}/register', [PageController::class, 'registerEvent'])->name('events.register');

Route::get('/gallery', [PageController::class, 'gallery'])->name('gallery');

Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'sendInquiry'])->name('contact.send');

/*
|--------------------------------------------------------------------------
| Admin Authentication & CMS Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Bookings Management
        Route::get('/bookings', [BookingManagementController::class, 'index'])->name('bookings');
        Route::post('/bookings/walk-in', [BookingManagementController::class, 'storeWalkIn'])->name('bookings.walkin');
        Route::put('/bookings/{booking}', [BookingManagementController::class, 'update'])->name('bookings.update');
        Route::delete('/bookings/{booking}', [BookingManagementController::class, 'destroy'])->name('bookings.destroy');

        // Tables & Dynamic Pricing Management
        Route::get('/tables', [TableManagementController::class, 'index'])->name('tables');
        Route::post('/tables/categories', [TableManagementController::class, 'storeCategory'])->name('tables.categories.store');
        Route::put('/tables/categories/{category}', [TableManagementController::class, 'updateCategory'])->name('tables.categories.update');
        Route::delete('/tables/categories/{category}', [TableManagementController::class, 'destroyCategory'])->name('tables.categories.destroy');
        Route::post('/tables/categories/{category}/quick-add', [TableManagementController::class, 'quickAddTable'])->name('tables.quick-add');
        Route::post('/tables/categories/{category}/quick-remove', [TableManagementController::class, 'quickRemoveTable'])->name('tables.quick-remove');
        Route::post('/tables/categories/{category}/items', [TableManagementController::class, 'storeTable'])->name('tables.items.store');
        Route::put('/tables/items/{table}', [TableManagementController::class, 'updateTable'])->name('tables.items.update');
        Route::delete('/tables/items/{table}', [TableManagementController::class, 'destroyTable'])->name('tables.items.destroy');

        // Time Slots & Promo Codes
        Route::get('/slots-promos', [TimeSlotController::class, 'index'])->name('slots');
        Route::post('/slots', [TimeSlotController::class, 'storeSlot'])->name('slots.store');
        Route::put('/slots/{slot}', [TimeSlotController::class, 'updateSlot'])->name('slots.update');
        Route::delete('/slots/{slot}', [TimeSlotController::class, 'destroySlot'])->name('slots.destroy');
        Route::post('/promos', [TimeSlotController::class, 'storePromo'])->name('promos.store');
        Route::post('/promos/{promo}/toggle', [TimeSlotController::class, 'togglePromo'])->name('promos.toggle');
        Route::delete('/promos/{promo}', [TimeSlotController::class, 'destroyPromo'])->name('promos.destroy');

        // Events & Registrations
        Route::get('/events', [EventManagementController::class, 'index'])->name('events');
        Route::post('/events', [EventManagementController::class, 'store'])->name('events.store');
        Route::put('/events/{event}', [EventManagementController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventManagementController::class, 'destroy'])->name('events.destroy');

        // Photo Gallery Management
        Route::get('/gallery', [GalleryManagementController::class, 'index'])->name('gallery');
        Route::post('/gallery', [GalleryManagementController::class, 'store'])->name('gallery.store');
        Route::put('/gallery/{gallery}', [GalleryManagementController::class, 'update'])->name('gallery.update');
        Route::delete('/gallery/{gallery}', [GalleryManagementController::class, 'destroy'])->name('gallery.destroy');

        // Memberships & Applications
        Route::get('/memberships', [MembershipManagementController::class, 'index'])->name('memberships');
        Route::post('/memberships/plans', [MembershipManagementController::class, 'storePlan'])->name('memberships.plans.store');
        Route::put('/memberships/plans/{plan}', [MembershipManagementController::class, 'updatePlan'])->name('memberships.plans.update');
        Route::delete('/memberships/plans/{plan}', [MembershipManagementController::class, 'destroyPlan'])->name('memberships.plans.destroy');
        Route::put('/memberships/applications/{application}', [MembershipManagementController::class, 'updateApplication'])->name('memberships.applications.update');

        // Site Settings & Concierge Inbox
        Route::get('/settings', [SettingController::class, 'index'])->name('settings');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/inquiries/{inquiry}/read', [SettingController::class, 'markInquiryRead'])->name('inquiries.read');
        Route::delete('/inquiries/{inquiry}', [SettingController::class, 'destroyInquiry'])->name('inquiries.destroy');
    });
});
