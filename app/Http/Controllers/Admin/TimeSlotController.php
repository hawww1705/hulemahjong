<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use App\Models\TimeSlot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimeSlotController extends Controller
{
    public function index(): View
    {
        $slots = TimeSlot::orderBy('sort_order')->orderBy('start_time')->get();
        $promoCodes = PromoCode::latest()->get();

        return view('admin.slots-promos', compact('slots', 'promoCodes'));
    }

    public function storeSlot(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'start_time' => ['required', 'regex:/^\d{2}:\d{2}$/'],
            'label' => ['nullable', 'string', 'max:20'],
            'is_peak' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        TimeSlot::create([
            'start_time' => $validated['start_time'],
            'label' => $validated['label'] ?: $validated['start_time'],
            'is_peak' => $request->boolean('is_peak'),
            'is_active' => true,
            'sort_order' => $validated['sort_order'] ?? (TimeSlot::max('sort_order') + 1),
        ]);

        return back()->with('success', "Time slot {$validated['start_time']} added.");
    }

    public function updateSlot(Request $request, TimeSlot $slot): RedirectResponse
    {
        $validated = $request->validate([
            'start_time' => ['required', 'regex:/^\d{2}:\d{2}$/'],
            'label' => ['required', 'string', 'max:20'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $slot->update([
            'start_time' => $validated['start_time'],
            'label' => $validated['label'],
            'sort_order' => $validated['sort_order'],
            'is_peak' => $request->boolean('is_peak'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', "Time slot {$slot->label} updated.");
    }

    public function destroySlot(TimeSlot $slot): RedirectResponse
    {
        $slot->delete();
        return back()->with('success', 'Time slot removed.');
    }

    public function storePromo(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:40', 'unique:promo_codes,code'],
            'description' => ['nullable', 'string', 'max:200'],
            'discount_type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'integer', 'min:1'],
            'max_discount' => ['nullable', 'integer', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'valid_until' => ['nullable', 'date'],
        ]);

        PromoCode::create([
            'code' => strtoupper(trim($validated['code'])),
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'max_discount' => $validated['max_discount'] ?? null,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'valid_until' => $validated['valid_until'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Promo code created!');
    }

    public function togglePromo(PromoCode $promo): RedirectResponse
    {
        $promo->update(['is_active' => !$promo->is_active]);
        return back()->with('success', "Promo {$promo->code} status toggled.");
    }

    public function destroyPromo(PromoCode $promo): RedirectResponse
    {
        $promo->delete();
        return back()->with('success', 'Promo code deleted.');
    }
}
