@extends('layouts.admin')

@section('title', 'Memberships — Hule Mahjong CMS')
@section('heading', 'Membership Tiers & Applications')
@section('subheading', 'Configure patron privileges, pricing, and approve incoming member applications.')

@section('header_actions')
    <button type="button" onclick="document.getElementById('new-plan-panel').classList.toggle('hidden')"
            class="px-4 py-2 rounded-full bg-[#111] hover:bg-[#c3b091] text-white hover:text-[#141310] text-xs font-semibold transition">
        + New Membership Tier
    </button>
@endsection

@section('content')
    <!-- Add New Plan Form -->
    <div id="new-plan-panel" class="hidden bg-white rounded-2xl border border-[#dfd3be] p-6 mb-8">
        <h2 class="text-[16px] font-bold mb-4">Create Membership Plan</h2>
        <form method="POST" action="{{ route('admin.memberships.plans.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf
            <div>
                <label class="cms-label">Tier Name *</label>
                <input type="text" name="name" required placeholder="Platinum Patron" class="cms-input">
            </div>
            <div>
                <label class="cms-label">Badge Label</label>
                <input type="text" name="badge" placeholder="VIP Choice" class="cms-input">
            </div>
            <div>
                <label class="cms-label">Price (Rp) *</label>
                <input type="number" name="price" required value="1500000" class="cms-input">
            </div>
            <div>
                <label class="cms-label">Billing Period *</label>
                <input type="text" name="billing_period" required value="per quarter" class="cms-input">
            </div>
            <div class="sm:col-span-2">
                <label class="cms-label">Description</label>
                <textarea name="description" rows="3" class="cms-input"></textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="cms-label">Benefits (1 per line)</label>
                <textarea name="benefits_raw" rows="3" placeholder="20% off all sessions&#10;Free hot tea" class="cms-input"></textarea>
            </div>
            <div class="sm:col-span-2 lg:col-span-4 flex items-center justify-between">
                <label class="inline-flex items-center gap-2 text-xs font-semibold">
                    <input type="checkbox" name="is_featured" value="1">
                    <span>Highlight as Featured Dark Card</span>
                </label>
                <button type="submit" class="px-6 py-2 rounded-full bg-[#c3b091] text-[#141310] text-xs font-bold">Create Tier →</button>
            </div>
        </form>
    </div>

    <!-- Existing Tiers -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        @foreach ($plans as $plan)
            <div class="bg-white rounded-2xl border border-[#ebe5d9] p-5 space-y-3">
                <form method="POST" action="{{ route('admin.memberships.plans.update', $plan) }}" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="cms-label">Name</label>
                            <input type="text" name="name" value="{{ $plan->name }}" required class="cms-input !py-1.5 text-xs font-bold">
                        </div>
                        <div>
                            <label class="cms-label">Badge</label>
                            <input type="text" name="badge" value="{{ $plan->badge }}" class="cms-input !py-1.5 text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="cms-label">Price (Rp)</label>
                            <input type="number" name="price" value="{{ $plan->price }}" required class="cms-input !py-1.5 text-xs font-semibold">
                        </div>
                        <div>
                            <label class="cms-label">Period</label>
                            <input type="text" name="billing_period" value="{{ $plan->billing_period }}" required class="cms-input !py-1.5 text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="cms-label">Description</label>
                        <textarea name="description" rows="2" class="cms-input !py-1.5 text-xs">{{ $plan->description }}</textarea>
                    </div>

                    <div>
                        <label class="cms-label">Benefits (1 per line)</label>
                        <textarea name="benefits_raw" rows="4" class="cms-input !py-1.5 text-xs">{{ implode("\n", $plan->benefits ?? []) }}</textarea>
                    </div>

                    <div class="flex items-center justify-between pt-1 text-xs">
                        <div class="flex items-center gap-3">
                            <label class="inline-flex items-center gap-1"><input type="checkbox" name="is_active" value="1" @checked($plan->is_active)> Active</label>
                            <label class="inline-flex items-center gap-1"><input type="checkbox" name="is_featured" value="1" @checked($plan->is_featured)> Featured</label>
                        </div>
                        <button type="submit" class="px-4 py-1.5 rounded-full bg-[#111] text-white font-semibold">Save</button>
                    </div>
                </form>

                <form method="POST" action="{{ route('admin.memberships.plans.destroy', $plan) }}" onsubmit="return confirm('Delete plan {{ $plan->name }}?')" class="text-right">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-[11px] text-red-500 hover:underline">Delete Plan</button>
                </form>
            </div>
        @endforeach
    </div>

    <!-- Membership Applications Table -->
    <div class="bg-white rounded-2xl border border-[#ebe5d9] p-6">
        <h2 class="text-[17px] font-bold mb-4">Membership Applications</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="border-b border-stone-200 text-[11px] uppercase tracking-wider text-stone-500">
                    <tr>
                        <th class="py-2.5 pr-3">Applicant</th>
                        <th class="py-2.5 px-3">Selected Tier</th>
                        <th class="py-2.5 px-3">Branch & Frequency</th>
                        <th class="py-2.5 px-3">Notes</th>
                        <th class="py-2.5 pl-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($applications as $app)
                        <tr>
                            <td class="py-3 pr-3">
                                <span class="font-bold text-stone-900">{{ $app->applicant_name }}</span>
                                <span class="block text-stone-500">{{ $app->applicant_phone }} • {{ $app->applicant_email }}</span>
                            </td>
                            <td class="py-3 px-3 font-semibold text-[#6e5b3e]">{{ $app->plan->name ?? '-' }}</td>
                            <td class="py-3 px-3">{{ $app->preferred_branch }}<span class="block text-stone-400">{{ $app->playing_frequency }}</span></td>
                            <td class="py-3 px-3 max-w-[220px]">{{ $app->message ?: '-' }}</td>
                            <td class="py-3 pl-3">
                                <form method="POST" action="{{ route('admin.memberships.applications.update', $app) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="rounded-lg border border-stone-200 px-2 py-1 text-xs">
                                        @foreach (['pending', 'contacted', 'approved', 'rejected'] as $st)
                                            <option value="{{ $st }}" @selected($app->status === $st)>{{ ucfirst($st) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="px-3 py-1 rounded-lg bg-[#c3b091] text-[#141310] font-semibold">Save</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-stone-400">No membership applications yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
