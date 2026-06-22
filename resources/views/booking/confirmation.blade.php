@extends('layouts.app')
@section('title', 'Booking Confirmed!')
@section('content')

<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center wow fadeInUp">

            <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                style="width:90px;height:90px;">
                <i class="fas fa-check fa-3x text-white"></i>
            </div>

            <h1 class="display-5 fw-bold mb-2">{{ ($paid ?? false) ? 'Payment Successful!' : 'Booking Confirmed!' }}</h1>
            <p class="text-muted mb-4">
                @if($paid ?? false)
                    Thank you! Your payment was received and your reservation is confirmed.
                @else
                    Thank you! Your reservation has been successfully created.
                @endif
            </p>

            <div class="bg-secondary rounded p-4 mb-4 text-start text-white">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-white-50">
                    <span class="text-white-50">Booking Reference</span>
                    <strong class="fs-5">{{ $booking->reference }}</strong>
                </div>
                <div class="row g-3 small">
                    <div class="col-6">
                        <div class="text-white-50">Vehicle</div>
                        <div class="fw-bold">{{ $vehicle->make }} {{ $vehicle->model }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-white-50">Status</div>
                        @if($paid ?? false)
                            <span class="badge bg-success">Paid &amp; Confirmed</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ ucfirst($booking->status) }}</span>
                        @endif
                    </div>
                    <div class="col-6">
                        <div class="text-white-50">Start Date</div>
                        <div class="fw-bold">{{ \Carbon\Carbon::parse($booking->start_date)->format('M d, Y') }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-white-50">End Date</div>
                        <div class="fw-bold">{{ \Carbon\Carbon::parse($booking->end_date)->format('M d, Y') }}</div>
                    </div>
                    @if($booking->pickup_location)
                    <div class="col-12">
                        <div class="text-white-50">Pickup</div>
                        <div class="fw-bold">{{ $booking->pickup_location }}</div>
                    </div>
                    @endif
                    @if($booking->total)
                    <div class="col-12 pt-2 border-top border-white-50">
                        <div class="d-flex justify-content-between">
                            <span class="text-white-50">Total Amount</span>
                            <span class="fw-bold fs-5">${{ number_format($booking->total, 2) }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <p class="text-muted small mb-4">
                <i class="fas fa-envelope text-primary me-1"></i>
                A confirmation email has been sent to <strong>{{ $booking->email }}</strong>
            </p>

            <div class="d-flex gap-3 justify-content-center flex-wrap confirmation-actions">
                <a href="{{ route('home') }}" class="btn btn-primary rounded-pill py-2 px-5">
                    <i class="fas fa-home me-2"></i>Back to Home
                </a>
                <a href="{{ route('search') }}" class="btn btn-secondary rounded-pill py-2 px-5">
                    <i class="fas fa-car me-2"></i>Browse More
                </a>
                <a href="https://wa.me/+17869785809?text={{ urlencode('My booking reference is ' . $booking->reference) }}"
                    class="btn btn-success rounded-pill py-2 px-4">
                    <i class="fab fa-whatsapp me-2"></i>WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
