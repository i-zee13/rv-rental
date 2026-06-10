<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogPost;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::with('translations')->paginate(20);
        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.blog.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => 'required|string|unique:blog_posts,slug',
            'status' => 'required|in:draft,published',
            'title_en' => 'required|string',
            'content_en' => 'nullable|string',
            'title_es' => 'nullable|string',
            'content_es' => 'nullable|string',
            'featured_image' => 'nullable|image|max:4096',
        ]);

        $featuredImage = null;
        if ($request->hasFile('featured_image')) {
            $featuredImage = Storage::url($request->file('featured_image')->store('blog', 'public'));
        }

        $post = BlogPost::create([
            'slug' => $data['slug'],
            'status' => $data['status'],
            'author_id' => auth()->id(),
            'featured_image' => $featuredImage,
        ]);

        $post->translations()->createMany([
            ['locale'=>'en','title'=>$data['title_en'],'excerpt'=>null,'content'=>$data['content_en']],
            ['locale'=>'es','title'=>$data['title_es'] ?? null,'excerpt'=>null,'content'=>$data['content_es'] ?? null],
        ]);

        return redirect()->route('admin.blog.index')->with('success','Post created');
    }

    public function edit($id)
    {
        $post = BlogPost::with('translations')->findOrFail($id);
        $t_en = $post->translations->firstWhere('locale','en');
        $t_es = $post->translations->firstWhere('locale','es');
        return view('admin.blog.edit', compact('post','t_en','t_es'));
    }

    public function update(Request $request, $id)
    {
        $post = BlogPost::findOrFail($id);
        $data = $request->validate([
            'slug' => 'required|string|unique:blog_posts,slug,'.$post->id,
            'status' => 'required|in:draft,published',
            'title_en' => 'required|string',
            'content_en' => 'nullable|string',
            'title_es' => 'nullable|string',
            'content_es' => 'nullable|string',
            'featured_image' => 'nullable|image|max:4096',
        ]);

        $updates = ['slug' => $data['slug'], 'status' => $data['status']];
        if ($request->hasFile('featured_image')) {
            $updates['featured_image'] = Storage::url(
                $request->file('featured_image')->store('blog', 'public')
            );
        }
        $post->update($updates);
        $post->translations()->updateOrCreate(['locale'=>'en'], ['title'=>$data['title_en'],'content'=>$data['content_en'] ?? null]);
        $post->translations()->updateOrCreate(['locale'=>'es'], ['title'=>$data['title_es'] ?? null,'content'=>$data['content_es'] ?? null]);

        return redirect()->route('admin.blog.index')->with('success','Post updated');
    }

    public function destroy($id)
    {
        $post = BlogPost::findOrFail($id);
        $post->delete();
        return redirect()->route('admin.blog.index')->with('success','Post deleted');
    }
}
