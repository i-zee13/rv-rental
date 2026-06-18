<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VehicleCategoryController extends Controller
{
    public function index()
    {
        $categories = VehicleCategory::with(['translations'])
            ->withCount('vehicles')
            ->orderBy('slug')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => 'nullable|string|max:100|unique:vehicle_categories,slug',
            'name_en' => 'required|string|max:191',
            'name_es' => 'nullable|string|max:191',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = $data['slug'] ?: Str::slug($data['name_en']);
        $slug = $this->uniqueSlug($slug);

        $category = VehicleCategory::create([
            'slug' => $slug,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $category->translations()->create([
            'locale' => 'en',
            'name' => $data['name_en'],
        ]);

        if (trim($data['name_es'] ?? '') !== '') {
            $category->translations()->create([
                'locale' => 'es',
                'name' => $data['name_es'],
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit($id)
    {
        $category = VehicleCategory::with('translations')->findOrFail($id);
        $en = $category->translations->firstWhere('locale', 'en');
        $es = $category->translations->firstWhere('locale', 'es');

        return view('admin.categories.edit', compact('category', 'en', 'es'));
    }

    public function update(Request $request, $id)
    {
        $category = VehicleCategory::findOrFail($id);

        $data = $request->validate([
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('vehicle_categories', 'slug')->ignore($category->id)],
            'name_en' => 'required|string|max:191',
            'name_es' => 'nullable|string|max:191',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = $data['slug'] ?: Str::slug($data['name_en']);
        if ($slug !== $category->slug) {
            $slug = $this->uniqueSlug($slug, $category->id);
        }

        $category->update([
            'slug' => $slug,
            'is_active' => $request->boolean('is_active'),
        ]);

        $category->translations()->updateOrCreate(
            ['locale' => 'en'],
            ['name' => $data['name_en']]
        );

        $category->translations()->updateOrCreate(
            ['locale' => 'es'],
            ['name' => trim($data['name_es'] ?? '') ?: $data['name_en']]
        );

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function seedDefaults()
    {
        Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\VehicleCategoriesSeeder',
            '--force' => true,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Default categories loaded. Existing categories and your vehicles were not deleted.');
    }

    public function destroy($id)
    {
        $category = VehicleCategory::withCount('vehicles')->findOrFail($id);

        if ($category->vehicles_count > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete — '.$category->vehicles_count.' vehicle(s) use this category.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category removed.');
    }

    protected function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug) ?: 'category';
        $candidate = $base;
        $i = 1;

        while (VehicleCategory::where('slug', $candidate)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $candidate = $base.'-'.$i;
            $i++;
        }

        return $candidate;
    }
}
