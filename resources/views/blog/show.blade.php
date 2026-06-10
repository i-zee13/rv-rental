@extends('layouts.app')
@section('title', $translation->title ?? 'Blog Post')
@section('meta_description', Str::limit(strip_tags($translation->excerpt ?? $translation->content ?? ''), 155))
@section('content')

<div class="container-fluid page-header py-5 mb-5 wow fadeIn"
    style="background: linear-gradient(rgba(0,0,0,0.65),rgba(0,0,0,0.65)), url('/theme/img/blog-1.jpg') center/cover; min-height:200px;">
    <div class="container py-5">
        <h1 class="display-4 text-white mb-2 animated slideInDown">{{ Str::limit($translation->title ?? 'Blog Post', 60) }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a class="text-white" href="{{ route('blog.index') }}">Blog</a></li>
                <li class="breadcrumb-item text-primary active">{{ Str::limit($translation->title ?? '', 40) }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5 mb-5">
    <div class="row g-5">
        <div class="col-lg-8 wow fadeInLeft" data-wow-delay="0.1s">
            <article>
                <h1 class="display-6 fw-bold mb-4">{{ $translation->title }}</h1>

                @if($translation->excerpt ?? false)
                <p class="lead text-muted border-start border-primary border-4 ps-3 mb-4">{{ $translation->excerpt }}</p>
                @endif

                <div class="blog-content" style="line-height:1.9; font-size:1.05rem;">
                    {!! $translation->content !!}
                </div>
            </article>

            <div class="d-flex gap-3 mt-5 pt-4 border-top">
                <a href="{{ route('blog.index') }}" class="btn btn-secondary rounded-pill px-4">
                    <i class="fas fa-arrow-left me-2"></i>All Posts
                </a>
                <a href="{{ route('booking.step1') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-car me-2"></i>Book a Vehicle
                </a>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4 wow fadeInRight" data-wow-delay="0.1s">
            <div class="bg-secondary rounded p-4 mb-4">
                <h5 class="text-white mb-3">Ready to Rent?</h5>
                <p class="text-white-50 small">Find your perfect vehicle and book online in minutes.</p>
                <a href="{{ route('booking.step1') }}" class="btn btn-primary w-100 rounded-pill mb-2">
                    <i class="fas fa-car me-2"></i>Book Now
                </a>
                <a href="{{ route('search') }}" class="btn btn-light w-100 rounded-pill">
                    <i class="fas fa-search me-2"></i>Browse Fleet
                </a>
            </div>
            <div class="border rounded p-4">
                <h6 class="fw-bold mb-3 text-primary">Why Choose Us?</h6>
                <p class="mb-2 small"><i class="fa fa-check-circle text-primary me-2"></i>Wide Selection of Vehicles</p>
                <p class="mb-2 small"><i class="fa fa-check-circle text-primary me-2"></i>24/7 Customer Support</p>
                <p class="mb-2 small"><i class="fa fa-check-circle text-primary me-2"></i>Well-Maintained Vehicles</p>
                <p class="mb-0 small"><i class="fa fa-check-circle text-primary me-2"></i>Easy Booking Process</p>
            </div>
        </div>
    </div>
</div>
@endsection
