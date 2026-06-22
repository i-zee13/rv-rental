@props(['property'])

@php
    $t = $property->translation();
    $title = $t->title ?? $property->address_line1;
    $img = $property->images->first()?->publicUrl() ?? '/theme/img/carousel-2.jpg';
@endphp

<div class="home-mini-card">
    <a href="{{ route('properties.show', $property) }}" class="home-mini-card-img d-block">
        <img src="{{ $img }}" alt="{{ $title }}" loading="lazy" onerror="this.src='/theme/img/carousel-2.jpg'">
        @if($property->featured)
            <span class="home-mini-badge">Featured</span>
        @endif
    </a>
    <div class="home-mini-card-body">
        <h3 class="home-mini-card-title">
            <a href="{{ route('properties.show', $property) }}">{{ Str::limit($title, 42) }}</a>
        </h3>
        <div class="home-mini-meta">
            <span><i class="fas fa-map-marker-alt"></i> {{ $property->neighborhood ?? $property->city }}</span>
            <span>{{ $property->bedrooms }} bd · {{ $property->bathrooms }} ba</span>
        </div>
        <div class="home-mini-price-row">
            <div>
                <span class="home-mini-price-label">From</span>
                <span class="home-mini-price">${{ number_format($property->price_per_month, 0) }}<small>/mo</small></span>
            </div>
            <a href="{{ route('properties.show', $property) }}" class="home-mini-action" title="View listing">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
