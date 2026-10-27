<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Gallery;
use App\Models\Inquiry;
use App\Models\MembershipApplication;
use App\Models\MembershipPlan;
use App\Models\TableCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController extends Controller
{
    public function tables(): View
    {
        $categories = TableCategory::with(['tables' => fn ($q) => $q->orderBy('code')])
            ->withCount('activeTables')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('public.tables', compact('categories'));
    }

    public function membership(): View
    {
        $plans = MembershipPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('public.membership', compact('plans'));
    }

    public function applyMembership(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'membership_plan_id' => ['required', 'exists:membership_plans,id'],
            'applicant_name' => ['required', 'string', 'max:120'],
            'applicant_phone' => ['required', 'string', 'max:35'],
            'applicant_email' => ['required', 'email', 'max:120'],
            'preferred_branch' => ['required', 'string', 'max:100'],
            'playing_frequency' => ['nullable', 'string', 'max:100'],
            'message' => ['nullable', 'string', 'max:600'],
        ]);

        MembershipApplication::create($validated + ['status' => 'pending']);

        return back()->with('success', 'Thank you! Your Hule Club membership application has been received by our Membership Committee.');
    }

    public function events(Request $request): View
    {
        $selectedCategory = $request->query('category', 'All');

        $query = Event::where('is_active', true)->orderBy('event_date')->orderBy('start_time');
        if ($selectedCategory && $selectedCategory !== 'All') {
            $query->where('category', $selectedCategory);
        }

        $events = $query->get();
        $categories = ['All', 'Tournament', 'Beginner Coaching', 'Social Night'];

        return view('public.events', compact('events', 'categories', 'selectedCategory'));
    }

    public function eventShow(string $slug): View
    {
        $event = Event::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $otherEvents = Event::where('is_active', true)
            ->where('id', '!=', $event->id)
            ->orderBy('event_date')
            ->limit(2)
            ->get();

        return view('public.event-detail', compact('event', 'otherEvents'));
    }

    public function registerEvent(Request $request, string $slug): RedirectResponse
    {
        $event = Event::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'participant_name' => ['required', 'string', 'max:120'],
            'participant_phone' => ['required', 'string', 'max:35'],
            'participant_email' => ['nullable', 'email', 'max:120'],
            'skill_level' => ['required', 'in:Beginner,Intermediate,Advanced'],
            'seats' => ['required', 'integer', 'min:1', 'max:4'],
            'notes' => ['nullable', 'string', 'max:400'],
        ]);

        $seats = (int) $validated['seats'];

        try {
            $reg = DB::transaction(function () use ($event, $validated, $seats) {
                $lockedEvent = Event::lockForUpdate()->findOrFail($event->id);
                if ($lockedEvent->remaining_seats < $seats) {
                    throw new \RuntimeException('Not enough remaining spots for this event.');
                }

                $lockedEvent->increment('registered_count', $seats);

                return EventRegistration::create([
                    'event_id' => $lockedEvent->id,
                    'registration_code' => 'EVT-' . strtoupper(Str::random(6)),
                    'participant_name' => $validated['participant_name'],
                    'participant_phone' => $validated['participant_phone'],
                    'participant_email' => $validated['participant_email'] ?? null,
                    'skill_level' => $validated['skill_level'],
                    'seats' => $seats,
                    'total_price' => $lockedEvent->price * $seats,
                    'status' => 'confirmed',
                    'notes' => $validated['notes'] ?? null,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return back()->with(
            'success',
            "Spot reserved! Your registration code is {$reg->registration_code}. Our host will reach out via WhatsApp."
        );
    }

    public function gallery(Request $request): View
    {
        $selectedCategory = $request->query('category', 'All');
        $categories = ['All', 'Lounge & Interior', 'VIP Suites', 'Tournaments & Community', 'Food & Beverage'];

        $query = Gallery::where('is_active', true)->orderBy('sort_order')->latest();
        if ($selectedCategory && $selectedCategory !== 'All') {
            $query->where('category', $selectedCategory);
        }

        $photos = $query->get();

        return view('public.gallery', compact('photos', 'categories', 'selectedCategory'));
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    public function sendInquiry(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:35'],
            'email' => ['nullable', 'email', 'max:120'],
            'subject' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:1500'],
        ]);

        Inquiry::create($validated);

        return back()->with('success', 'Your message has been sent to the Hule Concierge desk. We will reply shortly!');
    }
}
