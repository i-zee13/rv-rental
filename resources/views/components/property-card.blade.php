@props(['property'])

@php
    $t = $property->translation();
    $img = $property->images->firstWhere('is_primary', true) ?? $property->images->first();
@endphp

<div class="categories-item p-3 h-100">
    <div class="categories-item-inner h-100 d-flex flex-column">
        <div class="categories-img rounded-top position-relative" style="height:200px; overflow:hidden;">
            <img src="{{ $img ? $img->publicUrl() : '/theme/img/carousel-2.jpg' }}"
                class="img-fluid w-100"
                style="height:200px; object-fit:cover;"
                alt="{{ $t->title ?? $property->fullAddress() }}"
                onerror="this.src='/theme/img/carousel-2.jpg'">
            @if($property->featured)
                <span class="badge bg-primary position-absolute top-0 start-0 m-2">Featured</span>
            @endif
            @if($property->pets_allowed)
                <span class="badge bg-secondary position-absolute top-0 end-0 m-2"><i class="fas fa-paw"></i> Pets OK</span>
            @endif
        </div>
        <div class="categories-content rounded-bottom p-3 flex-grow-1 d-flex flex-column">
            @if($property->type)
                <span class="badge bg-light text-dark border mb-2 align-self-start">{{ $property->type->translatedName() }}</span>
            @endif
            <h5 class="mb-1">{{ $t->title ?? $property->fullAddress() }}</h5>
            <p class="text-muted small mb-2"><i class="fas fa-map-marker-alt text-primary me-1"></i>{{ $property->neighborhood ?? $property->city }}</p>

            <div class="d-flex flex-wrap gap-2 small text-muted mb-3">
                <span><i class="fas fa-bed text-primary me-1"></i>{{ $property->bedrooms }} bd</span>
                <span><i class="fas fa-bath text-primary me-1"></i>{{ $property->bathrooms }} ba</span>
                @if($property->sqft)
                    <span><i class="fas fa-ruler-combined text-primary me-1"></i>{{ number_format($property->sqft) }} sqft</span>
                @endif
            </div>

            <div class="mt-auto">
                <h5 class="bg-white text-primary rounded-pill py-2 px-3 mb-3 text-center">
                    {{ $property->displayPrice() }}
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('properties.show', $property) }}" class="btn btn-secondary rounded-pill flex-fill btn-sm py-2">Details</a>
                    <a href="{{ route('properties.show', $property) }}#inquire" class="btn btn-primary rounded-pill flex-fill btn-sm py-2">Inquire</a>
                </div>
            </div>
        </div>
    </div>
</div>
