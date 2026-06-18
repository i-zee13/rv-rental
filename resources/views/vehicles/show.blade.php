@extends('layouts.app')

@php
    $t = $vehicle->translations->firstWhere('locale', app()->getLocale()) ?? $vehicle->translations->first();
    $vehicleTitle = $t->title ?? $vehicle->make.' '.$vehicle->model;
    $mainImg = $vehicle->images->first()?->publicUrl() ?? '/theme/img/car-2.png';
    $heroImg = $vehicle->images->first()?->publicUrl() ?? '/theme/img/carousel-1.jpg';
@endphp

@section('title', $vehicleTitle)
@section('meta_description', Str::limit(strip_tags($t->description ?? ''), 155))

@section('content')

{{-- Page Header --}}
<div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s"
    style="background: linear-gradient(rgba(0,0,0,0.65),rgba(0,0,0,0.65)), url('{{ $heroImg }}') center/cover no-repeat;">
    <div class="container py-5">
        <h1 class="display-3 text-white mb-3 animated slideInDown">{{ $vehicleTitle }}</h1>
        <nav aria-label="breadcrumb animated slideInDown">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a class="text-white" href="{{ route('search') }}">Vehicles</a></li>
                <li class="breadcrumb-item text-primary active">{{ $vehicleTitle }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="container">
        <div class="row g-5">

            {{-- ============================================================
                 LEFT: Images + Description
            ============================================================ --}}
            <div class="col-lg-8 wow fadeInLeft" data-wow-delay="0.1s">

                {{-- Main Image --}}
                <div class="position-relative mb-4 rounded overflow-hidden vehicle-detail-image">
                    <img src="{{ $mainImg }}"
                        alt="{{ $vehicleTitle }}"
                        class="vehicle-main-img w-100 h-100 rounded"
                        style="object-fit:cover;"
                        onerror="this.src='/theme/img/car-2.png'">
                    {{-- Status badge --}}
                    <div class="position-absolute top-0 start-0 m-3">
                        @if($vehicle->status === 'available')
                            <span class="badge bg-success fs-6 px-3 py-2">✓ Available</span>
                        @else
                            <span class="badge bg-danger fs-6 px-3 py-2">{{ ucfirst($vehicle->status) }}</span>
                        @endif
                    </div>
                </div>

                {{-- Thumbnail Gallery --}}
                @if($vehicle->images->count() > 1)
                <div class="row g-2 mb-4">
                    @foreach($vehicle->images as $img)
                    <div class="col-3 col-md-2">
                        <img src="{{ $img->publicUrl() }}" alt=""
                            class="img-fluid rounded border border-2"
                            style="height:70px;width:100%;object-fit:cover;cursor:pointer;"
                            onclick="document.querySelector('.vehicle-main-img').src='{{ $img->publicUrl() }}'"
                            onerror="this.src='/theme/img/car-2.png'">
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Vehicle Details --}}
                <div class="border rounded p-4 mb-4">
                    <h2 class="mb-1">{{ $vehicleTitle }}</h2>
                    @if($vehicle->category)
                        @php $catT = $vehicle->category->translations->firstWhere('locale', app()->getLocale()) ?? $vehicle->category->translations->first(); @endphp
                        <span class="badge bg-primary mb-3">{{ $catT->name ?? $vehicle->category->slug }}</span>
                    @endif

                    @if($t && $t->description)
                    <hr>
                    <h5 class="mb-3">Description</h5>
                    <div class="text-muted">{!! $t->description !!}</div>
                    @endif
                </div>

                {{-- Specifications --}}
                <div class="border rounded p-4 mb-4">
                    <h5 class="mb-4"><i class="fas fa-cogs text-primary me-2"></i>Specifications</h5>
                    <div class="row g-3">
                        @foreach([
                            ['icon'=>'fa-users','label'=>'Seats','value'=>$vehicle->seats ?? '—'],
                            ['icon'=>'fa-suitcase','label'=>'Bags','value'=>$vehicle->bags ?? '—'],
                            ['icon'=>'fa-cogs','label'=>'Transmission','value'=>ucfirst($vehicle->transmission ?? 'Automatic')],
                            ['icon'=>'fa-gas-pump','label'=>'Fuel','value'=>ucfirst($vehicle->fuel_type ?? 'Gasoline')],
                            ['icon'=>'fa-calendar','label'=>'Year','value'=>$vehicle->year ?? '—'],
                            ['icon'=>'fa-tag','label'=>'Make','value'=>$vehicle->make ?? '—'],
                            ['icon'=>'fa-car','label'=>'Model','value'=>$vehicle->model ?? '—'],
                            ['icon'=>'fa-dollar-sign','label'=>'Price/Day','value'=>'$'.number_format($vehicle->price_per_day,2)],
                        ] as $spec)
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 text-center h-100">
                                <i class="fas {{ $spec['icon'] }} fa-lg text-primary mb-2"></i>
                                <div class="small text-muted">{{ $spec['label'] }}</div>
                                <div class="fw-bold">{{ $spec['value'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if($t && $t->specs)
                <div class="border rounded p-4">
                    <h5 class="mb-3"><i class="fas fa-list text-primary me-2"></i>Additional Details</h5>
                    <div class="text-muted">{!! $t->specs !!}</div>
                </div>
                @endif
            </div>

            {{-- ============================================================
                 RIGHT: Booking Sidebar
            ============================================================ --}}
            <div class="col-lg-4 wow fadeInRight" data-wow-delay="0.1s">
                <div class="position-lg-sticky vehicle-booking-sidebar">

                    {{-- Price & Book Card --}}
                    <div class="bg-secondary rounded p-4 mb-4">
                        <div class="text-white-50 small mb-1">Starting From</div>
                        <h2 class="text-white mb-0">${{ number_format($vehicle->price_per_day, 2) }}</h2>
                        <p class="text-white-50 small mb-4">per day (taxes may apply)</p>

                        @if($vehicle->status === 'available')
                        <a href="{{ route('booking.step1') }}?vehicle_id={{ $vehicle->id }}"
                            class="btn btn-primary w-100 rounded-pill py-3 mb-2 fw-bold fs-5">
                            <i class="fas fa-car me-2"></i>Book This Vehicle
                        </a>
                        @else
                        <div class="btn btn-secondary w-100 rounded-pill py-3 mb-2 disabled">Currently Unavailable</div>
                        @endif

                        <a href="{{ route('contact', ['vehicle_id' => $vehicle->id]) }}"
                            class="btn btn-outline-light w-100 rounded-pill py-2 mb-2">
                            <i class="fas fa-envelope me-2"></i>Request a Quote
                        </a>

                        <a href="https://wa.me/+17869785809?text=Hi, I'm interested in {{ urlencode($vehicleTitle) }}"
                            class="btn btn-light w-100 rounded-pill py-2">
                            <i class="fab fa-whatsapp text-success me-2"></i>Ask on WhatsApp
                        </a>
                    </div>

                    {{-- Quick Specs --}}
                    <div class="border rounded p-4 mb-4">
                        <h6 class="fw-bold mb-3 text-primary">Quick Overview</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted"><i class="fas fa-users me-2"></i>Passengers</span>
                                <strong>{{ $vehicle->seats ?? '?' }}</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted"><i class="fas fa-cogs me-2"></i>Transmission</span>
                                <strong>{{ ucfirst($vehicle->transmission ?? 'Automatic') }}</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted"><i class="fas fa-gas-pump me-2"></i>Fuel</span>
                                <strong>{{ ucfirst($vehicle->fuel_type ?? 'Gasoline') }}</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2">
                                <span class="text-muted"><i class="fas fa-suitcase me-2"></i>Bags</span>
                                <strong>{{ $vehicle->bags ?? '?' }}</strong>
                            </li>
                        </ul>
                    </div>

                    {{-- Trust Badges --}}
                    <div class="border rounded p-4">
                        <h6 class="fw-bold mb-3">Why Book with Us?</h6>
                        <p class="mb-2 small"><i class="fa fa-check-circle text-primary me-2"></i>Free Cancellation Available</p>
                        <p class="mb-2 small"><i class="fa fa-check-circle text-primary me-2"></i>No Hidden Fees</p>
                        <p class="mb-2 small"><i class="fa fa-check-circle text-primary me-2"></i>24/7 Road Assistance</p>
                        <p class="mb-0 small"><i class="fa fa-check-circle text-primary me-2"></i>Fully Insured Vehicles</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related Vehicles --}}
        @if(isset($related) && $related->isNotEmpty())
        <div class="mt-5">
            <h3 class="mb-4 wow fadeInUp">Similar Vehicles</h3>
            <div class="row g-4">
                @foreach($related as $rv)
                    @php $rt = $rv->translations->firstWhere('locale', app()->getLocale()) ?? $rv->translations->first(); @endphp
                    <div class="col-md-4 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="categories-item p-3">
                            <div class="categories-item-inner">
                                <div class="categories-img rounded-top">
                                    <img src="{{ $rv->images->first()?->publicUrl() ?? '/theme/img/car-2.png' }}"
                                        class="img-fluid w-100 rounded-top" style="height:180px;object-fit:cover;"
                                        alt="{{ $rt->title ?? $rv->make.' '.$rv->model }}"
                                        onerror="this.src='/theme/img/car-2.png'">
                                </div>
                                <div class="categories-content rounded-bottom p-3">
                                    <h5 class="mb-2">{{ $rt->title ?? $rv->make.' '.$rv->model }}</h5>
                                    <div class="mb-3">
                                        <span class="bg-white text-primary rounded-pill px-3 py-1 fw-bold border border-primary">
                                            ${{ number_format($rv->price_per_day,2) }}/Day
                                        </span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('vehicles.show', $rv) }}" class="btn btn-secondary rounded-pill flex-fill btn-sm">Details</a>
                                        <a href="{{ route('booking.step1') }}?vehicle_id={{ $rv->id }}" class="btn btn-primary rounded-pill flex-fill btn-sm">Book</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
