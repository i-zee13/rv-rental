@extends('layouts.app')
@section('title', 'Book a Vehicle — Step 3')
@section('content')

<div class="container-fluid page-header py-5 mb-5"
    style="background: linear-gradient(rgba(0,0,0,0.6),rgba(0,0,0,0.6)), url('/theme/img/carousel-1.jpg') center/cover; min-height:200px;">
    <div class="container py-4">
        <h1 class="display-4 text-white mb-2">Book a Vehicle</h1>
        @include('booking.partials.steps', ['current' => 3])
    </div>
</div>

<div class="container py-3 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @if($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <div class="border rounded p-4 p-md-5">
                <h4 class="mb-4"><i class="fas fa-user text-primary me-2"></i>Your Details</h4>
                <form method="POST" action="{{ route('booking.postStep3') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">First Name <span class="text-danger">*</span></label>
                            <input name="first_name" class="form-control form-control-lg" placeholder="John"
                                value="{{ old('first_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Last Name <span class="text-danger">*</span></label>
                            <input name="last_name" class="form-control form-control-lg" placeholder="Doe"
                                value="{{ old('last_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope text-primary"></i></span>
                                <input type="email" name="email" class="form-control form-control-lg"
                                    placeholder="john@example.com" value="{{ old('email') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phone</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone text-primary"></i></span>
                                <input type="tel" name="phone" class="form-control form-control-lg"
                                    placeholder="+1 (786) 000-0000" value="{{ old('phone') }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Message / Special Requests</label>
                            <textarea name="notes" class="form-control" rows="4"
                                placeholder="Any special requirements or notes...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="booking-form-actions mt-4">
                        <a href="{{ route('booking.step2') }}" class="btn btn-secondary rounded-pill px-4">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill py-2 px-5 fw-bold">
                            Continue — Payment <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
