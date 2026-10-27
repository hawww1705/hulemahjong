<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Support\UploadsHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventManagementController extends Controller
{
    public function index(): View
    {
        $events = Event::withCount('registrations')->orderByDesc('event_date')->get();
        $registrations = EventRegistration::with('event')->latest()->limit(25)->get();

        return view('admin.events', compact('events', 'registrations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:80'],
            'event_date' => ['required', 'date'],
            'start_time' => ['required', 'string', 'max:10'],
            'end_time' => ['nullable', 'string', 'max:10'],
            'location' => ['required', 'string', 'max:180'],
            'price' => ['required', 'integer', 'min:0'],
            'quota' => ['required', 'integer', 'min:1', 'max:500'],
            'excerpt' => ['nullable', 'string', 'max:300'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        $slugBase = Str::slug($validated['title']);
        $slug = $slugBase;
        $i = 1;
        while (Event::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . (++$i);
        }

        $imagePath = $request->hasFile('image')
            ? UploadsHelper::savePublicImage($request->file('image'), 'events')
            : 'images/mahjong/event-tournament.jpg';

        Event::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'category' => $validated['category'],
            'event_date' => $validated['event_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'location' => $validated['location'],
            'price' => $validated['price'],
            'quota' => $validated['quota'],
            'registered_count' => 0,
            'image_path' => $imagePath,
            'excerpt' => $validated['excerpt'] ?: Str::limit((string) ($validated['description'] ?? ''), 150),
            'description' => $validated['description'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => true,
        ]);

        return back()->with('success', "Event '{$validated['title']}' published!");
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:80'],
            'event_date' => ['required', 'date'],
            'start_time' => ['required', 'string', 'max:10'],
            'end_time' => ['nullable', 'string', 'max:10'],
            'location' => ['required', 'string', 'max:180'],
            'price' => ['required', 'integer', 'min:0'],
            'quota' => ['required', 'integer', 'min:1', 'max:500'],
            'excerpt' => ['nullable', 'string', 'max:300'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        $data = [
            'title' => $validated['title'],
            'category' => $validated['category'],
            'event_date' => $validated['event_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'location' => $validated['location'],
            'price' => $validated['price'],
            'quota' => $validated['quota'],
            'excerpt' => $validated['excerpt'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('image')) {
            $data['image_path'] = UploadsHelper::savePublicImage($request->file('image'), 'events');
        }

        $event->update($data);

        return back()->with('success', "Event '{$event->title}' updated.");
    }

    public function destroy(Event $event): RedirectResponse
    {
        $title = $event->title;
        $event->delete();
        return back()->with('success', "Event '{$title}' deleted.");
    }
}
