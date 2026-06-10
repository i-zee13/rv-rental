@extends('layouts.app')
@section('title', 'Book a Vehicle — Step 2')
@section('content')

<div class="container-fluid page-header py-5 mb-5"
    style="background: linear-gradient(rgba(0,0,0,0.6),rgba(0,0,0,0.6)), url('/theme/img/carousel-1.jpg') center/cover; min-height:200px;">
    <div class="container py-4">
        <h1 class="display-4 text-white mb-2">Book a Vehicle</h1>
        @include('booking.partials.steps', ['current' => 2])
    </div>
</div>

<div class="container py-3 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="border rounded p-4 p-md-5">
                <h4 class="mb-2"><i class="fas fa-plus-circle text-primary me-2"></i>Optional Add-ons</h4>
                <p class="text-muted mb-4">Enhance your rental with these optional extras.</p>

                <form method="POST" action="{{ route('booking.postStep2') }}">
                    @csrf
                    @if($addons->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <p class="mb-1">No add-ons configured yet.</p>
                        <p class="small">Add extras (GPS, insurance, etc.) in <strong>Admin → Add-ons</strong>.</p>
                    </div>
                    @else
                    <div class="row g-3 mb-4">
                        @foreach($addons as $a)
                        <div class="col-md-6">
                            <label class="d-flex align-items-center gap-3 border rounded p-3 cursor-pointer h-100"
                                style="cursor:pointer;"
                                for="addon_{{ $a->id }}">
                                <input type="checkbox" class="form-check-input mt-0 flex-shrink-0"
                                    name="addon_ids[]" value="{{ $a->id }}" id="addon_{{ $a->id }}"
                                    {{ in_array($a->id, old('addon_ids', [])) ? 'checked' : '' }}
                                    style="width:20px;height:20px;">
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $a->name }}</div>
                                    @if($a->description ?? false)
                                    <div class="small text-muted">{{ $a->description }}</div>
                                    @endif
                                </div>
                                <div class="fw-bold text-primary">${{ number_format($a->price, 2) }}</div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <div class="booking-form-actions mt-2">
                        <a href="{{ route('booking.step1') }}" class="btn btn-secondary rounded-pill px-4">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill py-2 px-5 fw-bold">
                            Continue — Details <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
