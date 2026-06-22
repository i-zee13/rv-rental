@props(['vehicle'])

@php
    $t = $vehicle->translations->firstWhere('locale', app()->getLocale()) ?? $vehicle->translations->first();
    $title = $t->title ?? trim($vehicle->make.' '.$vehicle->model);
    $img = $vehicle->images->first()?->publicUrl() ?? '/theme/img/car-2.png';
@endphp

<div class="home-mini-card">
    <a href="{{ route('vehicles.show', $vehicle) }}" class="home-mini-card-img d-block">
        <img src="{{ $img }}" alt="{{ $title }}" loading="lazy" onerror="this.src='/theme/img/car-2.png'">
        @if($vehicle->featured)
            <span class="home-mini-badge">Featured</span>
        @endif
    </a>
    <div class="home-mini-card-body">
        <h3 class="home-mini-card-title">
            <a href="{{ route('vehicles.show', $vehicle) }}">{{ Str::limit($title, 42) }}</a>
        </h3>
        <div class="home-mini-meta">
            @if($vehicle->category)
                <span>{{ $vehicle->category->translatedName() }}</span>
            @endif
            @if($vehicle->seats)
                <span><i class="fas fa-users"></i> {{ $vehicle->seats }}</span>
            @endif
        </div>
        <div class="home-mini-price-row">
            <div>
                <span class="home-mini-price-label">From</span>
                <span class="home-mini-price">${{ number_format($vehicle->price_per_day, 0) }}<small>/day</small></span>
            </div>
            <a href="{{ route('vehicles.show', $vehicle) }}" class="home-mini-action" title="View details">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
