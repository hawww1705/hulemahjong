<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\SiteSetting;
use App\Support\UploadsHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = SiteSetting::allAsMap();
        $inquiries = Inquiry::latest()->get();

        return view('admin.settings', compact('settings', 'inquiries'));
    }

    public function update(Request $request): RedirectResponse
    {
        $keys = [
            'brand_prefix' => 'general',
            'brand_suffix' => 'general',
            'site_tagline' => 'general',
            'house_policy' => 'general',
            'cancellation_note' => 'booking',
            'banner_eyebrow' => 'banner',
            'banner_title_line1_bold' => 'banner',
            'banner_title_line1_light' => 'banner',
            'banner_title_line2_light' => 'banner',
            'banner_title_line2_bold' => 'banner',
            'banner_description' => 'banner',
            'contact_address' => 'contact',
            'contact_phone' => 'contact',
            'contact_whatsapp' => 'contact',
            'contact_email' => 'contact',
            'contact_instagram' => 'contact',
            'opening_hours' => 'contact',
            'payment_bank_name' => 'payment',
            'payment_account_number' => 'payment',
            'payment_account_holder' => 'payment',
        ];

        foreach ($keys as $key => $group) {
            if ($request->has($key)) {
                SiteSetting::setVal($key, $request->input($key), $group);
            }
        }

        if ($request->hasFile('banner_image_file')) {
            $request->validate(['banner_image_file' => ['image', 'max:6144']]);
            $path = UploadsHelper::savePublicImage($request->file('banner_image_file'), 'banners');
            SiteSetting::setVal('banner_image', $path, 'banner');
        }

        return back()->with('success', 'Website & CMS settings saved!');
    }

    public function markInquiryRead(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->update(['is_read' => true]);
        return back()->with('success', 'Message marked as read.');
    }

    public function destroyInquiry(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();
        return back()->with('success', 'Message removed.');
    }
}
