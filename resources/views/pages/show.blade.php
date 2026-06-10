@extends('layouts.app')
@section('title', $translation->meta_title ?? $translation->title ?? 'Page')
@section('meta_description', $translation->meta_description ?? Str::limit(strip_tags($translation->content ?? ''), 155))
@section('content')

<div class="container-fluid page-header py-5 mb-5 wow fadeIn"
    style="background: linear-gradient(rgba(0,0,0,0.6),rgba(0,0,0,0.6)), url('/theme/img/carousel-2.jpg') center/cover; min-height:200px;">
    <div class="container py-5">
        <h1 class="display-3 text-white mb-3 animated slideInDown">{{ $translation->title ?? 'Page' }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item text-primary active">{{ $translation->title ?? 'Page' }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5 mb-5">
    <div class="row g-5">
        <div class="col-lg-8 wow fadeInLeft" data-wow-delay="0.1s">
            <div class="border rounded p-4 p-md-5" style="line-height:1.9;">
                {!! $translation->content !!}
            </div>
        </div>
        <div class="col-lg-4 wow fadeInRight" data-wow-delay="0.1s">
            <div class="bg-secondary rounded p-4 mb-4">
                <h5 class="text-white mb-3">Book a Vehicle</h5>
                <p class="text-white-50 small">Ready to hit the road? Find your perfect vehicle today.</p>
                <a href="{{ route('booking.step1') }}" class="btn btn-primary w-100 rounded-pill mb-2">
                    <i class="fas fa-car me-2"></i>Book Now
                </a>
                <a href="{{ route('search') }}" class="btn btn-light w-100 rounded-pill">Browse Fleet</a>
            </div>
            <div class="border rounded p-4">
                <h6 class="fw-bold mb-3 text-primary">Contact Us</h6>
                <p class="mb-2 small"><i class="fas fa-phone text-primary me-2"></i>+1 (786) 978-5809</p>
                <p class="mb-2 small"><i class="fas fa-envelope text-primary me-2"></i>info@mvmiamirental.com</p>
                <p class="mb-0 small"><i class="fas fa-map-marker-alt text-primary me-2"></i>Miami FL 33122</p>
            </div>
        </div>
    </div>
</div>
@endsection
