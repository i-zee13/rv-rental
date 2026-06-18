@extends('layouts.app')

@section('title', 'Search Vehicles')
@section('meta_description', 'Browse our full fleet of luxury and affordable rental vehicles in Miami.')

@section('content')

{{-- Page Header --}}
<div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s"
    style="background: linear-gradient(rgba(0,0,0,0.6),rgba(0,0,0,0.6)), url('/theme/img/carousel-2.jpg') center/cover no-repeat;">
    <div class="container py-5">
        <h1 class="display-3 text-white mb-3 animated slideInDown">Our Fleet</h1>
        <nav aria-label="breadcrumb animated slideInDown">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item text-primary active">Vehicles</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="container py-3">
        <div class="row g-4">

            {{-- ============================================================
                 SIDEBAR FILTERS
            ============================================================ --}}
            <div class="col-lg-3 wow fadeInLeft" data-wow-delay="0.1s">
                <div class="border rounded p-4 mb-4">
                    <h5 class="mb-4 pb-2 border-bottom">Search Vehicles</h5>
                    <form method="GET" action="{{ route('search') }}">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Keyword</label>
                            <input type="text" name="q" value="{{ request('q') }}"
                                class="form-control" placeholder="Make, model, type...">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Category</label>
                            <div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="category" value="" id="cat_all"
                                        {{ !request('category') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="cat_all">All Categories</label>
                                </div>
                                @foreach($categories as $cat)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="category"
                                            value="{{ $cat->slug }}" id="cat_{{ $cat->id }}"
                                            {{ request('category') == $cat->slug ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cat_{{ $cat->id }}">{{ $cat->translatedName() }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                        @if(request('q') || request('category'))
                        <a href="{{ route('search') }}" class="btn btn-outline-secondary w-100 rounded-pill mt-2">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </a>
                        @endif
                    </form>
                </div>

                {{-- Quick Book --}}
                <div class="bg-secondary rounded p-4">
                    <h5 class="text-white mb-3">Quick Book</h5>
                    <p class="text-white-50 small">Ready to book? Start your reservation now.</p>
                    <a href="{{ route('booking.step1') }}" class="btn btn-primary w-100 rounded-pill">
                        <i class="fas fa-car me-2"></i>Book Now
                    </a>
                    <a href="https://wa.me/+17869785809" class="btn btn-light w-100 rounded-pill mt-2">
                        <i class="fab fa-whatsapp me-2"></i>WhatsApp
                    </a>
                </div>
            </div>

            {{-- ============================================================
                 VEHICLES GRID
            ============================================================ --}}
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <p class="mb-0 text-muted">
                        Showing <strong>{{ $vehicles->firstItem() ?? 0 }}–{{ $vehicles->lastItem() ?? 0 }}</strong>
                        of <strong>{{ $vehicles->total() }}</strong> vehicles
                        @if(request('q'))<span> for "<span class="text-primary">{{ request('q') }}</span>"</span>@endif
                    </p>
                </div>

                @if($vehicles->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-car fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No vehicles found</h4>
                    <p class="text-muted">Try adjusting your search filters.</p>
                    <a href="{{ route('search') }}" class="btn btn-primary rounded-pill px-5">Browse All</a>
                </div>
                @else
                <div class="row g-4">
                    @foreach($vehicles as $v)
                        @php $t = $v->translations->firstWhere('locale', app()->getLocale()) ?? $v->translations->first(); @endphp
                        <div class="col-md-6 col-xl-4 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="categories-item p-3">
                                <div class="categories-item-inner">
                                    <div class="categories-img rounded-top" style="height:200px; overflow:hidden;">
                                        <img src="{{ $v->images->first()->path ?? '/theme/img/car-2.png' }}"
                                            class="img-fluid w-100 rounded-top"
                                            style="height:200px; object-fit:cover;"
                                            alt="{{ $t->title ?? $v->make.' '.$v->model }}"
                                            onerror="this.src='/theme/img/car-2.png'">
                                    </div>
                                    <div class="categories-content rounded-bottom p-3">
                                        <h5 class="mb-2">{{ $t->title ?? $v->make.' '.$v->model }}</h5>

                                        {{-- Status badge --}}
                                        <div class="mb-2">
                                            @if($v->status === 'available')
                                                <span class="badge bg-success">Available</span>
                                            @elseif($v->status === 'booked')
                                                <span class="badge bg-danger">Booked</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($v->status) }}</span>
                                            @endif
                                        </div>

                                        <div class="categories-review mb-3">
                                            <div class="d-flex justify-content-center text-secondary">
                                                <i class="fas fa-star"></i><i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i><i class="fas fa-star"></i>
                                                <i class="fas fa-star text-body"></i>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <h5 class="bg-white text-primary rounded-pill py-2 px-4 mb-0 text-center">
                                                ${{ number_format($v->price_per_day, 2) }}/Day
                                            </h5>
                                        </div>

                                        <div class="row gy-2 gx-0 text-center mb-3 small">
                                            <div class="col-4 border-end">
                                                <i class="fa fa-users text-dark"></i> <span>{{ $v->seats ?? '?' }}</span>
                                            </div>
                                            <div class="col-4 border-end">
                                                <i class="fa fa-cogs text-dark"></i> <span>{{ strtoupper(substr($v->transmission ?? 'Auto',0,2)) }}</span>
                                            </div>
                                            <div class="col-4">
                                                <i class="fa fa-gas-pump text-dark"></i> <span>{{ ucfirst(substr($v->fuel_type ?? 'Gas',0,3)) }}</span>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <a href="{{ route('vehicles.show', $v->id) }}"
                                                class="btn btn-secondary rounded-pill flex-fill btn-sm py-2">Details</a>
                                            @if($v->status === 'available')
                                            <a href="{{ route('booking.step1') }}?vehicle_id={{ $v->id }}"
                                                class="btn btn-primary rounded-pill flex-fill btn-sm py-2">Book Now</a>
                                            @else
                                            <span class="btn btn-secondary rounded-pill flex-fill btn-sm py-2 disabled">Unavailable</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-5 d-flex justify-content-center">
                    {{ $vehicles->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
