@extends('layouts.app')
@section('title', 'Book a Vehicle — Step 1')
@section('content')

<div class="container-fluid page-header py-5 mb-5"
    style="background: linear-gradient(rgba(0,0,0,0.6),rgba(0,0,0,0.6)), url('/theme/img/carousel-1.jpg') center/cover; min-height:200px;">
    <div class="container py-4">
        <h1 class="display-4 text-white mb-2">Book a Vehicle</h1>
        @include('booking.partials.steps', ['current' => 1])
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
                <h4 class="mb-4"><i class="fas fa-car text-primary me-2"></i>Select Vehicle &amp; Dates</h4>
                <form method="POST" action="{{ route('booking.postStep1') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Vehicle <span class="text-danger">*</span></label>
                        <select name="vehicle_id" class="form-select form-select-lg" required>
                            <option value="">— Choose a vehicle —</option>
                            @foreach($vehicles as $v)
                                @php $vt = $v->translations->firstWhere('locale', app()->getLocale()) ?? $v->translations->first(); @endphp
                                <option value="{{ $v->id }}" {{ (old('vehicle_id') == $v->id || request('vehicle_id') == $v->id) ? 'selected' : '' }}>
                                    {{ $vt->title ?? $v->make.' '.$v->model }} — ${{ number_format($v->price_per_day,2) }}/day
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Start Date <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar-alt text-primary"></i></span>
                                <input type="date" name="start_date" class="form-control form-control-lg"
                                    value="{{ old('start_date') }}" min="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">End Date <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar-alt text-primary"></i></span>
                                <input type="date" name="end_date" class="form-control form-control-lg"
                                    value="{{ old('end_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><i class="fas fa-map-marker-alt text-primary me-1"></i> Pickup Location</label>
                            <input name="pickup_location" class="form-control form-control-lg"
                                placeholder="City or Airport" value="{{ old('pickup_location') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><i class="fas fa-flag-checkered text-primary me-1"></i> Dropoff Location</label>
                            <input name="dropoff_location" class="form-control form-control-lg"
                                placeholder="Same or different location" value="{{ old('dropoff_location') }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill py-3 px-5 fw-bold w-100 w-sm-auto">
                        Continue — Add-ons <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
