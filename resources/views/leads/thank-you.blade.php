@extends('layouts.app')

@section('title', 'Thank You')
@section('content')

<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center wow fadeInUp">
            <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                style="width:90px;height:90px;">
                <i class="fas fa-check fa-3x text-white"></i>
            </div>
            <h1 class="display-5 fw-bold mb-3">Thank You!</h1>
            <p class="text-muted fs-5 mb-4">We've received your inquiry. Our team will contact you shortly.</p>

            @if($reference)
            <div class="bg-secondary rounded p-4 mb-4 text-white">
                <div class="text-white-50 small mb-1">Your Reference Number</div>
                <div class="fs-3 fw-bold">{{ $reference }}</div>
            </div>
            @endif

            <p class="text-muted small mb-4">
                <i class="fas fa-envelope text-primary me-1"></i>
                A confirmation email has been sent to your inbox.
            </p>

            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('home') }}" class="btn btn-primary rounded-pill py-2 px-5">Back to Home</a>
                <a href="{{ route('search') }}" class="btn btn-secondary rounded-pill py-2 px-5">Browse Vehicles</a>
                <a href="https://wa.me/+17869785809" class="btn btn-success rounded-pill py-2 px-4">
                    <i class="fab fa-whatsapp me-2"></i>WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
