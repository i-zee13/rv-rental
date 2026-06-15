@extends('layouts.app')

@section('title', 'Homes & Apartments for Rent')
@section('meta_description', 'Browse houses, apartments, condos and villas for rent in Miami. Filter by price, beds, baths and amenities.')

@section('content')

<div class="container-fluid page-header py-5 mb-5 wow fadeIn"
    style="background: linear-gradient(rgba(0,0,0,0.6),rgba(0,0,0,0.6)), url('/theme/img/carousel-1.jpg') center/cover no-repeat;">
    <div class="container py-5">
        <h1 class="display-3 text-white mb-3 animated slideInDown">{{ __('ui.homes_apartments') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item text-primary active">{{ __('ui.rentals') }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="container py-3">
        <div class="row g-4">

            <div class="col-lg-3 wow fadeInLeft" data-wow-delay="0.1s">
                <div class="border rounded p-4 mb-4 sticky-lg-top" style="top:100px;">
                    <h5 class="mb-4 pb-2 border-bottom">{{ __('ui.search_rentals') }}</h5>
                    <form method="GET" action="{{ route('properties.search') }}">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Location / Keyword</label>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Neighborhood, address...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Home Type</label>
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->slug }}" {{ request('type') == $type->slug ? 'selected' : '' }}>
                                        {{ $type->translatedName() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Min Beds</label>
                                <select name="beds" class="form-select">
                                    <option value="">Any</option>
                                    @foreach([1,2,3,4,5] as $b)
                                        <option value="{{ $b }}" {{ request('beds') == $b ? 'selected' : '' }}>{{ $b }}+</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Min Baths</label>
                                <select name="baths" class="form-select">
                                    <option value="">Any</option>
                                    @foreach([1,1.5,2,3,4] as $b)
                                        <option value="{{ $b }}" {{ request('baths') == $b ? 'selected' : '' }}>{{ $b }}+</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Min $/mo</label>
                                <input type="number" name="min_price" value="{{ request('min_price') }}" class="form-control" placeholder="0">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Max $/mo</label>
                                <input type="number" name="max_price" value="{{ request('max_price') }}" class="form-control" placeholder="10000">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Sort</label>
                            <select name="sort" class="form-select">
                                <option value="featured" {{ request('sort', 'featured') == 'featured' ? 'selected' : '' }}>Recommended</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="beds_desc" {{ request('sort') == 'beds_desc' ? 'selected' : '' }}>Most Bedrooms</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                            </select>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="pets" value="1" id="filter_pets" {{ request('pets') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="filter_pets">Pet Friendly</label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="furnished" value="1" id="filter_furnished" {{ request('furnished') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="filter_furnished">Furnished</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill">
                            <i class="fas fa-search me-2"></i>Search Rentals
                        </button>
                        @if(request()->query())
                        <a href="{{ route('properties.search') }}" class="btn btn-outline-secondary w-100 rounded-pill mt-2">Clear Filters</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <p class="mb-0 text-muted">
                        <strong>{{ $properties->total() }}</strong> rentals available
                    </p>
                    <a href="{{ route('search') }}" class="btn btn-outline-primary btn-sm rounded-pill">
                        <i class="fas fa-car me-1"></i> Browse Vehicles
                    </a>
                </div>

                @if($properties->isEmpty())
                <div class="text-center py-5 border rounded">
                    <i class="fas fa-home fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No rentals found</h4>
                    <p class="text-muted">Try adjusting your filters or contact us for off-market listings.</p>
                    <a href="{{ route('contact') }}" class="btn btn-primary rounded-pill px-5">Contact Us</a>
                </div>
                @else
                <div class="row g-4">
                    @foreach($properties as $property)
                    <div class="col-md-6 col-xl-4 wow fadeInUp" data-wow-delay="0.1s">
                        <x-property-card :property="$property" />
                    </div>
                    @endforeach
                </div>
                <div class="mt-5 d-flex justify-content-center">{{ $properties->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
