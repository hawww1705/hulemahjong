<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MahjongTable;
use App\Models\TableCategory;
use App\Support\UploadsHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TableManagementController extends Controller
{
    public function index(): View
    {
        $categories = TableCategory::with(['tables' => fn ($q) => $q->orderBy('code')])
            ->withCount(['tables', 'activeTables'])
            ->orderBy('sort_order')
            ->get();

        return view('admin.tables', compact('categories'));
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'branch' => ['required', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:1500'],
            'weekday_price' => ['required', 'integer', 'min:0'],
            'weekend_price' => ['required', 'integer', 'min:0'],
            'peak_surcharge' => ['required', 'integer', 'min:0'],
            'session_minutes' => ['required', 'integer', 'min:30', 'max:480'],
            'min_players' => ['required', 'integer', 'min:1', 'max:12'],
            'max_players' => ['required', 'integer', 'min:1', 'max:16'],
            'amenities_raw' => ['nullable', 'string'],
            'initial_tables_count' => ['nullable', 'integer', 'min:0', 'max:30'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        $slugBase = Str::slug($validated['name']);
        $slug = $slugBase;
        $counter = 1;
        while (TableCategory::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . (++$counter);
        }

        $imagePath = $request->hasFile('image')
            ? UploadsHelper::savePublicImage($request->file('image'), 'tables')
            : 'images/mahjong/lounge-regular.jpg';

        $amenities = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) ($validated['amenities_raw'] ?? ''))
        )));

        $category = TableCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'branch' => $validated['branch'],
            'subtitle' => $validated['subtitle'] ?? null,
            'description' => $validated['description'] ?? null,
            'weekday_price' => $validated['weekday_price'],
            'weekend_price' => $validated['weekend_price'],
            'peak_surcharge' => $validated['peak_surcharge'],
            'session_minutes' => $validated['session_minutes'],
            'min_players' => $validated['min_players'],
            'max_players' => $validated['max_players'],
            'image_path' => $imagePath,
            'amenities' => $amenities ?: ['Automatic Shuffler Table', 'F&B Service'],
            'is_active' => true,
            'sort_order' => TableCategory::max('sort_order') + 1,
        ]);

        $initialCount = (int) ($validated['initial_tables_count'] ?? 1);
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $category->name), 0, 3) ?: 'TBL');
        for ($i = 1; $i <= $initialCount; $i++) {
            $this->createSequentialTable($category, $prefix);
        }

        return back()->with('success', "Category '{$category->name}' created with {$initialCount} automatic table(s).");
    }

    public function updateCategory(Request $request, TableCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'branch' => ['required', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:1500'],
            'weekday_price' => ['required', 'integer', 'min:0'],
            'weekend_price' => ['required', 'integer', 'min:0'],
            'peak_surcharge' => ['required', 'integer', 'min:0'],
            'session_minutes' => ['required', 'integer', 'min:30', 'max:480'],
            'min_players' => ['required', 'integer', 'min:1', 'max:12'],
            'max_players' => ['required', 'integer', 'min:1', 'max:16'],
            'amenities_raw' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        $data = [
            'name' => $validated['name'],
            'branch' => $validated['branch'],
            'subtitle' => $validated['subtitle'] ?? null,
            'description' => $validated['description'] ?? null,
            'weekday_price' => $validated['weekday_price'],
            'weekend_price' => $validated['weekend_price'],
            'peak_surcharge' => $validated['peak_surcharge'],
            'session_minutes' => $validated['session_minutes'],
            'min_players' => $validated['min_players'],
            'max_players' => $validated['max_players'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (array_key_exists('amenities_raw', $validated)) {
            $data['amenities'] = array_values(array_filter(array_map('trim', explode(',', (string) $validated['amenities_raw']))));
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = UploadsHelper::savePublicImage($request->file('image'), 'tables');
        }

        $category->update($data);

        return back()->with('success', "Pricing & details for '{$category->name}' updated!");
    }

    public function destroyCategory(TableCategory $category): RedirectResponse
    {
        $name = $category->name;
        $category->delete();

        return back()->with('success', "Category '{$name}' and its tables removed.");
    }

    public function quickAddTable(TableCategory $category): RedirectResponse
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $category->name), 0, 3) ?: 'TBL');
        $table = $this->createSequentialTable($category, $prefix);

        return back()->with('success', "Added physical table {$table->code} ({$table->name}) to {$category->name}.");
    }

    public function quickRemoveTable(TableCategory $category): RedirectResponse
    {
        $lastTable = $category->tables()->orderByDesc('id')->first();
        if (!$lastTable) {
            return back()->with('error', 'No physical tables left to remove in this category.');
        }

        $code = $lastTable->code;
        $lastTable->delete();

        return back()->with('success', "Removed table {$code} from {$category->name}.");
    }

    public function storeTable(Request $request, TableCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:mahjong_tables,code'],
            'name' => ['required', 'string', 'max:100'],
            'floor_zone' => ['required', 'string', 'max:100'],
            'machine_model' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:active,maintenance,inactive'],
        ]);

        MahjongTable::create([
            'table_category_id' => $category->id,
            'code' => strtoupper(trim($validated['code'])),
            'name' => $validated['name'],
            'floor_zone' => $validated['floor_zone'],
            'machine_model' => $validated['machine_model'] ?: 'Hule Whisper-Pro Auto',
            'status' => $validated['status'],
        ]);

        return back()->with('success', "Table {$validated['code']} added successfully.");
    }

    public function updateTable(Request $request, MahjongTable $table): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'floor_zone' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:active,maintenance,inactive'],
        ]);

        $table->update($validated);

        return back()->with('success', "Table {$table->code} updated ({$table->status}).");
    }

    public function destroyTable(MahjongTable $table): RedirectResponse
    {
        $code = $table->code;
        $table->delete();

        return back()->with('success', "Table {$code} removed.");
    }

    private function createSequentialTable(TableCategory $category, string $prefix): MahjongTable
    {
        $n = $category->tables()->count() + 1;
        do {
            $code = sprintf('%s-%02d', $prefix, $n);
            $n++;
        } while (MahjongTable::where('code', $code)->exists());

        return MahjongTable::create([
            'table_category_id' => $category->id,
            'code' => $code,
            'name' => $category->name . ' #' . str_pad((string) ($n - 1), 2, '0', STR_PAD_LEFT),
            'floor_zone' => 'Main Lounge',
            'machine_model' => 'Hule Whisper-Pro Auto',
            'status' => 'active',
        ]);
    }
}
