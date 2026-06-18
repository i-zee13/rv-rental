<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::with('translations')->orderByDesc('id')->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => 'required|string|unique:pages,slug',
            'is_published' => 'nullable|boolean',
            'title_en' => 'required|string',
            'content_en' => 'nullable|string',
            'meta_title_en' => 'nullable|string',
            'meta_description_en' => 'nullable|string',
            'title_es' => 'nullable|string',
            'content_es' => 'nullable|string',
            'meta_title_es' => 'nullable|string',
            'meta_description_es' => 'nullable|string',
        ]);

        $page = Page::create(['slug'=>$data['slug'],'is_published'=>!empty($data['is_published'])]);

        $page->translations()->createMany([
            ['locale'=>'en','title'=>$data['title_en'],'content'=>$data['content_en'] ?? null,'meta_title'=>$data['meta_title_en'] ?? null,'meta_description'=>$data['meta_description_en'] ?? null],
            ['locale'=>'es','title'=>$data['title_es'] ?? null,'content'=>$data['content_es'] ?? null,'meta_title'=>$data['meta_title_es'] ?? null,'meta_description'=>$data['meta_description_es'] ?? null],
        ]);

        return redirect()->route('admin.pages.index')->with('success','Page created');
    }

    public function edit($id)
    {
        $page = Page::with('translations')->findOrFail($id);
        $t_en = $page->translations->firstWhere('locale','en');
        $t_es = $page->translations->firstWhere('locale','es');
        return view('admin.pages.edit', compact('page','t_en','t_es'));
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);
        $data = $request->validate([
            'slug' => 'required|string|unique:pages,slug,'.$page->id,
            'is_published' => 'nullable|boolean',
            'title_en' => 'required|string',
            'content_en' => 'nullable|string',
            'meta_title_en' => 'nullable|string',
            'meta_description_en' => 'nullable|string',
            'title_es' => 'nullable|string',
            'content_es' => 'nullable|string',
            'meta_title_es' => 'nullable|string',
            'meta_description_es' => 'nullable|string',
        ]);

        $page->update(['slug'=>$data['slug'],'is_published'=>!empty($data['is_published'])]);

        // update/create translations
        $page->translations()->updateOrCreate(['locale'=>'en'], ['title'=>$data['title_en'],'content'=>$data['content_en'] ?? null,'meta_title'=>$data['meta_title_en'] ?? null,'meta_description'=>$data['meta_description_en'] ?? null]);
        $page->translations()->updateOrCreate(['locale'=>'es'], ['title'=>$data['title_es'] ?? null,'content'=>$data['content_es'] ?? null,'meta_title'=>$data['meta_title_es'] ?? null,'meta_description'=>$data['meta_description_es'] ?? null]);

        return redirect()->route('admin.pages.index')->with('success','Page updated');
    }

    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success','Page deleted');
    }
}
