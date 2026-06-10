<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use Illuminate\Http\Request;

class AddonController extends Controller
{
    public function index()
    {
        $addons = Addon::with('translations')->orderBy('code')->get();

        return view('admin.addons.index', compact('addons'));
    }

    public function create()
    {
        return view('admin.addons.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:addons,code',
            'price' => 'required|numeric|min:0',
            'title' => 'required|string|max:191',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'is_taxable' => 'nullable|boolean',
        ]);

        $addon = Addon::create([
            'code' => $data['code'],
            'price' => $data['price'],
            'is_active' => $request->boolean('is_active', true),
            'is_taxable' => $request->boolean('is_taxable', true),
        ]);

        $addon->translations()->create([
            'locale' => 'en',
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);

        return redirect()->route('admin.addons.index')->with('success', 'Add-on created.');
    }

    public function edit($id)
    {
        $addon = Addon::with('translations')->findOrFail($id);
        $translation = $addon->translation('en');

        return view('admin.addons.edit', compact('addon', 'translation'));
    }

    public function update(Request $request, $id)
    {
        $addon = Addon::findOrFail($id);

        $data = $request->validate([
            'code' => 'required|string|max:50|unique:addons,code,' . $addon->id,
            'price' => 'required|numeric|min:0',
            'title' => 'required|string|max:191',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'is_taxable' => 'nullable|boolean',
        ]);

        $addon->update([
            'code' => $data['code'],
            'price' => $data['price'],
            'is_active' => $request->boolean('is_active'),
            'is_taxable' => $request->boolean('is_taxable'),
        ]);

        $addon->translations()->updateOrCreate(
            ['locale' => 'en'],
            ['title' => $data['title'], 'description' => $data['description'] ?? null]
        );

        return redirect()->route('admin.addons.index')->with('success', 'Add-on updated.');
    }

    public function destroy($id)
    {
        Addon::findOrFail($id)->delete();

        return redirect()->route('admin.addons.index')->with('success', 'Add-on removed.');
    }
}
