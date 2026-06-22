@extends('layouts.app')
@section('title', 'Payment')
@section('content')

<div class="container-fluid page-header py-5 mb-0"
    style="background: linear-gradient(rgba(0,0,0,0.72),rgba(0,0,0,0.72)), url('/theme/img/carousel-1.jpg') center/cover; min-height:200px;">
    <div class="container py-4">
        <h1 class="display-4 text-white mb-2">Complete Your Booking</h1>
        <p class="text-white-50 mb-3">Secure checkout — pay now or reserve and pay at pickup</p>
        @include('booking.partials.steps', ['current' => 4])
    </div>
</div>

<div class="container py-5 mb-5 checkout-page">
    @if(session('error'))
    <div class="alert alert-danger rounded-3 mb-4">{{ session('error') }}</div>
    @endif

    <div class="row g-4 g-xl-5">
        {{-- Order summary sidebar --}}
        <div class="col-lg-5 order-lg-2">
            <div class="checkout-summary-card sticky-lg-top" style="top:100px;">
                <div class="checkout-summary-header">
                    <h5 class="mb-0 text-white"><i class="fas fa-receipt me-2"></i>Booking Summary</h5>
                </div>

                <div class="checkout-vehicle-preview">
                    <img src="{{ $quote['vehicle_image'] }}" alt="{{ $quote['vehicle_title'] }}"
                        onerror="this.src='/theme/img/car-2.png'">
                    <div>
                        <div class="checkout-vehicle-name">{{ $quote['vehicle_title'] }}</div>
                        <div class="checkout-vehicle-meta text-white-50 small">
                            {{ $quote['days'] }} {{ Str::plural('day', $quote['days']) }} · ${{ number_format($quote['daily_rate'], 2) }}/day
                        </div>
                    </div>
                </div>

                <div class="checkout-summary-body">
                    <div class="checkout-detail-row">
                        <span class="label"><i class="fas fa-calendar-alt me-2 text-primary"></i>Dates</span>
                        <span class="value">{{ $quote['start']->format('M j') }} → {{ $quote['end']->format('M j, Y') }}</span>
                    </div>
                    @if(!empty($step1['pickup_location']))
                    <div class="checkout-detail-row">
                        <span class="label"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Pick-up</span>
                        <span class="value">{{ $step1['pickup_location'] }}</span>
                    </div>
                    @endif
                    @if(!empty($step1['dropoff_location']))
                    <div class="checkout-detail-row">
                        <span class="label"><i class="fas fa-flag-checkered me-2 text-primary"></i>Drop-off</span>
                        <span class="value">{{ $step1['dropoff_location'] }}</span>
                    </div>
                    @endif
                    <div class="checkout-detail-row">
                        <span class="label"><i class="fas fa-user me-2 text-primary"></i>Guest</span>
                        <span class="value">{{ $step3['first_name'] }} {{ $step3['last_name'] }}</span>
                    </div>
                    <div class="checkout-detail-row">
                        <span class="label"><i class="fas fa-envelope me-2 text-primary"></i>Email</span>
                        <span class="value text-break">{{ $step3['email'] }}</span>
                    </div>

                    <hr class="checkout-divider">

                    <div class="checkout-line">
                        <span>Vehicle ({{ $quote['days'] }} days)</span>
                        <span>${{ number_format($quote['base'], 2) }}</span>
                    </div>
                    @foreach($quote['selected_addons'] as $addon)
                    <div class="checkout-line">
                        <span>{{ $addon->name }}</span>
                        <span>${{ number_format($addon->price, 2) }}</span>
                    </div>
                    @endforeach
                    <div class="checkout-line text-muted">
                        <span>Taxes ({{ intval($quote['tax_rate'] * 100) }}%)</span>
                        <span>${{ number_format($quote['taxes'], 2) }}</span>
                    </div>

                    <div class="checkout-total">
                        <span>Total due</span>
                        <span class="checkout-total-amount">${{ number_format($quote['total'], 2) }}</span>
                    </div>
                </div>

                <div class="checkout-trust-badges">
                    <span><i class="fas fa-shield-alt text-success"></i> Fully insured</span>
                    <span><i class="fas fa-lock text-success"></i> SSL secure</span>
                    <span><i class="fas fa-undo text-success"></i> Free cancellation</span>
                </div>
            </div>
        </div>

        {{-- Payment options --}}
        <div class="col-lg-7 order-lg-1">
            <div class="checkout-payment-card mb-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="mb-0 fw-bold">Choose Payment</h4>
                    <div class="checkout-card-brands d-none d-sm-flex gap-2">
                        <span class="brand-pill">VISA</span>
                        <span class="brand-pill">MC</span>
                        <span class="brand-pill">AMEX</span>
                    </div>
                </div>

                {{-- Stripe --}}
                @if($stripeEnabled ?? false)
                <div class="checkout-option checkout-option--featured">
                    <div class="checkout-option-badge">Recommended</div>
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="checkout-option-icon bg-primary text-white">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">Pay with Card</h5>
                            <p class="text-muted small mb-0">Instant confirmation via Stripe. Visa, Mastercard, Amex &amp; more.</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('booking.createCheckout') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold checkout-pay-btn">
                            <i class="fas fa-lock me-2"></i>Pay ${{ number_format($quote['total'], 2) }} Securely
                        </button>
                    </form>
                </div>

                <div class="checkout-or"><span>or reserve now</span></div>
                @else
                <div class="alert alert-info rounded-3 mb-4 small">
                    <i class="fas fa-info-circle me-2"></i>Online card payment is being set up. You can confirm your reservation now and pay at pickup.
                </div>
                @endif

                {{-- Pay later --}}
                <div class="checkout-option">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="checkout-option-icon bg-secondary text-white">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">Reserve Without Payment</h5>
                            <p class="text-muted small mb-0">Hold your vehicle now — pay when you pick up in Miami. No card required.</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('booking.confirm') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-dark w-100 py-3 rounded-pill fw-semibold">
                            Confirm Reservation — Pay at Pickup
                        </button>
                    </form>
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <a href="{{ route('booking.step3') }}" class="btn btn-link text-decoration-none text-muted px-0">
                    <i class="fas fa-arrow-left me-2"></i>Back to your details
                </a>
                <div class="checkout-secure-note">
                    <i class="fas fa-lock text-success me-2"></i>
                    <span class="small text-muted">256-bit encrypted · Your data is never stored on our servers</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
