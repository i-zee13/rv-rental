@extends('layouts.app')

@section('title', __('Search Results'))

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold">Search Results</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-3">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($vehicles as $v)
                    @php $t = $v->translations->firstWhere('locale', app()->getLocale()) ?? $v->translations->first(); @endphp
                    <div class="border rounded overflow-hidden">
                        <img src="{{ $v->images->first()->path ?? '/media/placeholder.png' }}" class="w-full h-44 object-cover" alt="{{ $t->title ?? $v->make.' '.$v->model }}">
                        <div class="p-3">
                            <div class="font-semibold">{{ $t->title ?? $v->make.' '.$v->model }}</div>
                            <div class="text-sm text-gray-600">{{ $v->seats }} seats · {{ $v->transmission ?? 'Auto' }}</div>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="text-lg font-bold">${{ number_format($v->price_per_day,2) }}/day</div>
                                <a href="{{ route('vehicles.show', $v->id) }}" class="bg-yellow-500 text-black px-3 py-2 rounded">View</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $vehicles->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
