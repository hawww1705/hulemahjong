<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Support\UploadsHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryManagementController extends Controller
{
    public function index(): View
    {
        $photos = Gallery::orderBy('sort_order')->latest()->get();
        return view('admin.gallery', compact('photos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'category' => ['required', 'string', 'max:80'],
            'caption' => ['nullable', 'string', 'max:240'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:6144'],
            'image_url' => ['nullable', 'url', 'max:500'],
        ]);

        if (!$request->hasFile('image') && empty($validated['image_url'])) {
            return back()->withInput()->with('error', 'Please upload an image file or provide an image URL.');
        }

        $imagePath = $request->hasFile('image')
            ? UploadsHelper::savePublicImage($request->file('image'), 'gallery')
            : $validated['image_url'];

        Gallery::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'caption' => $validated['caption'] ?? null,
            'image_path' => $imagePath,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => true,
            'sort_order' => $validated['sort_order'] ?? (Gallery::max('sort_order') + 1),
        ]);

        return back()->with('success', 'New photo added to Hule Gallery!');
    }

    public function update(Request $request, Gallery $gallery): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'category' => ['required', 'string', 'max:80'],
            'caption' => ['nullable', 'string', 'max:240'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:6144'],
        ]);

        $data = [
            'title' => $validated['title'],
            'category' => $validated['category'],
            'caption' => $validated['caption'] ?? null,
            'sort_order' => $validated['sort_order'],
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('image')) {
            $data['image_path'] = UploadsHelper::savePublicImage($request->file('image'), 'gallery');
        }

        $gallery->update($data);

        return back()->with('success', 'Gallery photo updated.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        $gallery->delete();
        return back()->with('success', 'Photo deleted from gallery.');
    }
}
