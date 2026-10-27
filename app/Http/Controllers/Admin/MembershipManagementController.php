<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipApplication;
use App\Models\MembershipPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MembershipManagementController extends Controller
{
    public function index(): View
    {
        $plans = MembershipPlan::orderBy('sort_order')->get();
        $applications = MembershipApplication::with('plan')->latest()->get();

        return view('admin.memberships', compact('plans', 'applications'));
    }

    public function storePlan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'badge' => ['nullable', 'string', 'max:60'],
            'price' => ['required', 'integer', 'min:0'],
            'billing_period' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:800'],
            'benefits_raw' => ['nullable', 'string'],
        ]);

        $benefits = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($validated['benefits_raw'] ?? '')))));

        MembershipPlan::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(4),
            'badge' => $validated['badge'] ?? null,
            'price' => $validated['price'],
            'billing_period' => $validated['billing_period'],
            'description' => $validated['description'] ?? null,
            'benefits' => $benefits,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => true,
            'sort_order' => MembershipPlan::max('sort_order') + 1,
        ]);

        return back()->with('success', 'Membership tier added.');
    }

    public function updatePlan(Request $request, MembershipPlan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'badge' => ['nullable', 'string', 'max:60'],
            'price' => ['required', 'integer', 'min:0'],
            'billing_period' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:800'],
            'benefits_raw' => ['nullable', 'string'],
        ]);

        $benefits = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($validated['benefits_raw'] ?? '')))));

        $plan->update([
            'name' => $validated['name'],
            'badge' => $validated['badge'] ?? null,
            'price' => $validated['price'],
            'billing_period' => $validated['billing_period'],
            'description' => $validated['description'] ?? null,
            'benefits' => $benefits,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', "Membership tier '{$plan->name}' updated.");
    }

    public function destroyPlan(MembershipPlan $plan): RedirectResponse
    {
        $plan->delete();
        return back()->with('success', 'Membership tier deleted.');
    }

    public function updateApplication(Request $request, MembershipApplication $application): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,contacted,approved,rejected'],
        ]);

        $application->update($validated);

        return back()->with('success', "Application status updated to {$application->status}.");
    }
}
