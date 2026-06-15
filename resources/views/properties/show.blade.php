@extends('layouts.app')

@php
    $t = $property->translation();
    $img = $property->images->firstWhere('is_primary', true) ?? $property->images->first();
    $title = $t->title ?? $property->fullAddress();
    $heroImg = $img ? $img->publicUrl() : '/theme/img/carousel-1.jpg';
@endphp

@section('title', $title)
@section('meta_description', Str::limit(strip_tags($t->description ?? ''), 155))

@section('content')

<div class="container-fluid page-header py-5 mb-5 wow fadeIn"
    style="background: linear-gradient(rgba(0,0,0,0.65),rgba(0,0,0,0.65)), url('{{ $heroImg }}') center/cover no-repeat;">
    <div class="container py-5">
        <h1 class="display-4 text-white mb-2 animated slideInDown">{{ $title }}</h1>
        <p class="text-white-50 mb-3"><i class="fas fa-map-marker-alt me-2"></i>{{ $property->fullAddress() }}</p>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a class="text-white" href="{{ route('properties.search') }}">Rentals</a></li>
                <li class="breadcrumb-item text-primary active">{{ Str::limit($title, 40) }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="container">
        <div class="row g-5">

            <div class="col-lg-8 wow fadeInLeft" data-wow-delay="0.1s">
                <div class="rounded overflow-hidden mb-4 vehicle-detail-image" style="height:360px;">
                    <img id="propertyMainImg" src="{{ $heroImg }}" alt="{{ $title }}"
                        class="w-100 h-100" style="object-fit:cover;" onerror="this.src='/theme/img/carousel-1.jpg'">
                </div>

                @if($property->images->count() > 1)
                <div class="row g-2 mb-4">
                    @foreach($property->images as $photo)
                    <div class="col-3 col-md-2">
                        <img src="{{ $photo->publicUrl() }}" alt="" class="img-fluid rounded border"
                            style="height:70px;width:100%;object-fit:cover;cursor:pointer;"
                            onclick="document.getElementById('propertyMainImg').src='{{ $photo->publicUrl() }}'">
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="border rounded p-4 mb-4">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @if($property->type)
                            <span class="badge bg-primary">{{ $property->type->translatedName() }}</span>
                        @endif
                        @if($property->status === 'available')
                            <span class="badge bg-success">Available</span>
                        @endif
                        @if($property->furnished)
                            <span class="badge bg-secondary">Furnished</span>
                        @endif
                        @if($property->pets_allowed)
                            <span class="badge bg-secondary"><i class="fas fa-paw"></i> Pet Friendly</span>
                        @endif
                    </div>

                    <div class="row g-3 mb-4 text-center">
                        <div class="col-4 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <i class="fas fa-bed text-primary fa-lg mb-2"></i>
                                <div class="fw-bold">{{ $property->bedrooms }}</div>
                                <small class="text-muted">Bedrooms</small>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <i class="fas fa-bath text-primary fa-lg mb-2"></i>
                                <div class="fw-bold">{{ $property->bathrooms }}</div>
                                <small class="text-muted">Bathrooms</small>
                            </div>
                        </div>
                        @if($property->sqft)
                        <div class="col-4 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <i class="fas fa-ruler-combined text-primary fa-lg mb-2"></i>
                                <div class="fw-bold">{{ number_format($property->sqft) }}</div>
                                <small class="text-muted">Sq Ft</small>
                            </div>
                        </div>
                        @endif
                        <div class="col-4 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <i class="fas fa-dollar-sign text-primary fa-lg mb-2"></i>
                                <div class="fw-bold">{{ $property->displayPrice() }}</div>
                                <small class="text-muted">Rent</small>
                            </div>
                        </div>
                    </div>

                    @if($t && $t->description)
                    <h5 class="mb-3">About This Rental</h5>
                    <div class="text-muted mb-0">{!! $t->description !!}</div>
                    @endif
                </div>

                @if(count($property->amenityLabels()) > 0)
                <div class="border rounded p-4 mb-4">
                    <h5 class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i>Amenities</h5>
                    <div class="row g-2">
                        @foreach($property->amenityLabels() as $amenity)
                        <div class="col-md-6">
                            <span class="d-flex align-items-center small"><i class="fas fa-check text-primary me-2"></i>{{ $amenity }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-4 wow fadeInRight" data-wow-delay="0.1s">
                <div class="bg-secondary rounded p-4 mb-4 sticky-lg-top" style="top:100px;">
                    <h4 class="text-white mb-1">{{ $property->displayPrice() }}</h4>
                    <p class="text-white-50 small mb-3">Ref: {{ $property->reference }}</p>
                    @if($property->security_deposit > 0)
                        <p class="text-white-50 small mb-1">Security deposit: ${{ number_format($property->security_deposit, 0) }}</p>
                    @endif
                    @if($property->min_nights)
                        <p class="text-white-50 small mb-3">Min stay: {{ $property->min_nights }} nights</p>
                    @endif
                    <a href="#inquire" class="btn btn-primary w-100 rounded-pill py-3 mb-2">
                        <i class="fas fa-envelope me-2"></i>Request a Tour
                    </a>
                    <a href="https://wa.me/+17869785809?text={{ urlencode('Hi, I am interested in: ' . $title) }}" class="btn btn-light w-100 rounded-pill py-2">
                        <i class="fab fa-whatsapp me-2"></i>WhatsApp
                    </a>
                </div>

                <div class="border rounded p-4" id="inquire">
                    <h5 class="mb-3">Inquire About This Property</h5>
                    <form action="{{ route('leads.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="source" value="property">
                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                        <div class="mb-3">
                            <input type="text" name="first_name" class="form-control" placeholder="Your name *" required value="{{ old('first_name') }}">
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email *" required value="{{ old('email') }}">
                        </div>
                        <div class="mb-3">
                            <input type="tel" name="phone" class="form-control" placeholder="Phone" value="{{ old('phone') }}">
                        </div>
                        <div class="mb-3">
                            <input type="date" name="pickup_date" class="form-control" min="{{ date('Y-m-d') }}" placeholder="Move-in date">
                        </div>
                        <div class="mb-3">
                            <textarea name="message" class="form-control" rows="3" placeholder="Tell us about your rental needs...">{{ old('message') }}</textarea>
                        </div>
                        <div style="position:absolute;left:-9999px;" aria-hidden="true">
                            <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold">Send Inquiry</button>
                    </form>
                </div>
            </div>
        </div>

        @if($related->isNotEmpty())
        <div class="mt-5 pt-5 border-top">
            <h3 class="mb-4">Similar Rentals</h3>
            <div class="row g-4">
                @foreach($related as $rel)
                <div class="col-md-4">
                    <x-property-card :property="$rel" />
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
