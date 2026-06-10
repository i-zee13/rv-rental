@extends('layouts.app')

@section('title', __('Home'))

@section('content')
<div class="space-y-8">
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-gray-50 rounded p-6">
                <h1 class="text-3xl font-bold mb-2">{{ config('app.name') }}</h1>
                <p class="text-gray-600">Premium vehicle & RV rental marketplace — demo homepage.</p>
                <form action="{{ route('search') }}" method="GET" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-2">
                    <input name="q" placeholder="Search vehicles or locations" class="border rounded px-3 py-2" />
                    <select name="category" class="border rounded px-3 py-2">
                        <option value="">All categories</option>
                        @foreach($categories as $cat)
                            @php $t = $cat->translations->firstWhere('locale', app()->getLocale()) ?? $cat->translations->first(); @endphp
                            <option value="{{ $cat->slug }}">{{ $t->name ?? $cat->slug }}</option>
                        @endforeach
                    </select>
                    <button class="bg-yellow-500 text-black px-4 py-2 rounded font-semibold">Search</button>
                </form>
            </div>
        </div>

        <aside>
            <div class="bg-white border rounded p-4">
                <h3 class="font-semibold mb-2">Featured</h3>
                @foreach($featured as $f)
                    @php $t = $f->translations->firstWhere('locale', app()->getLocale()) ?? $f->translations->first(); @endphp
                    <div class="flex items-center gap-3 py-2 border-b">
                        <img src="{{ $f->images->first()->path ?? '/media/placeholder.png' }}" alt="{{ $t->title ?? $f->make.' '.$f->model }}" class="w-16 h-12 object-cover rounded">
                        <div>
                            <div class="text-sm font-medium">{{ $t->title ?? $f->make.' '.$f->model }}</div>
                            <div class="text-xs text-gray-500">${{ number_format($f->price_per_day,2) }} / day</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </aside>
    </section>

    <section>
        <h2 class="text-xl font-semibold mb-4">Popular Categories</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($categories as $cat)
                @php $t = $cat->translations->firstWhere('locale', app()->getLocale()) ?? $cat->translations->first(); @endphp
                <a href="{{ route('search', ['category' => $cat->slug]) }}" class="block border rounded p-4 text-center hover:shadow">
                    <div class="font-medium">{{ $t->name ?? $cat->slug }}</div>
                </a>
            @endforeach
        </div>
    </section>
</div>
@endsection
