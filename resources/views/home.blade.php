@extends('layouts.app')

@section('body_class', 'page-home')
@section('title', 'Luxury Car Rentals in Miami')
@section('meta_description', 'MV Miami Rental offers the best luxury and exotic car rentals in Miami. Rent premium vehicles for your Miami experience.')

@section('content')

{{-- ============================================================
     HERO CAROUSEL
============================================================ --}}
<div class="header-carousel">
    <div id="carouselId" class="carousel slide pointer-event" data-bs-ride="carousel">
        <ol class="carousel-indicators">
            <li data-bs-target="#carouselId" data-bs-slide-to="0" class="active" aria-current="true"></li>
            <li data-bs-target="#carouselId" data-bs-slide-to="1"></li>
            <li data-bs-target="#carouselId" data-bs-slide-to="2"></li>
        </ol>
        <div class="carousel-inner" role="listbox">

            {{-- Slide 1 --}}
            <div class="carousel-item active">
                <x-hero-slide-image
                    slug="slide-1"
                    desktop="theme/img/THOR-Vision-Vehicle-TVV-electric-rv-2.jpg"
                    alt="RV Rental Miami"
                />
                <div class="carousel-caption">
                    <div class="container py-4">
                                <div class="row g-5 hero-row">
                            <div class="col-lg-6 fadeInLeft animated">
                                <div class="d-lg-none hero-mobile-tagline text-center mb-3">
                                    <h2 class="text-white fw-bold mb-2">Get 15% off your rental!</h2>
                                    <p class="text-white-50 small mb-0">Luxury RVs, Cars &amp; more — Miami</p>
                                </div>
                                <div class="rounded p-3 p-md-4 hero-quote-card">
                                    <h4 class="text-white mb-1 hero-card-title">RESERVE YOUR RIDE</h4>
                                    <p class="text-white-50 small mb-2 mb-md-3 hero-card-sub">Pick dates → we’ll confirm availability in minutes</p>
                                    <x-lead-form
                                        variant="hero"
                                        source="homepage"
                                        :vehicles="$featured->merge($allVehicles ?? collect())->unique('id')"
                                    />
                                </div>
                            </div>
                            <div class="col-lg-6 d-none d-lg-flex fadeInRight animated">
                                <div class="text-start">
                                    <h1 class="display-5 text-white">Get 15% off your rental!<br>Choose Your Model</h1>
                                    <p class="text-white-50">Luxury RVs, Cars & more — drive in style across Miami</p>
                                    <a href="{{ route('search') }}" class="btn btn-primary rounded-pill py-3 px-5 me-3">Browse Fleet</a>
                                    <a href="https://wa.me/+17869785809" class="btn btn-secondary rounded-pill py-3 px-5">WhatsApp</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide 2 --}}
            <div class="carousel-item">
                <x-hero-slide-image
                    slug="slide-2"
                    desktop="theme/img/carousel-2.jpg"
                    alt="Car Rental Miami"
                />
                <div class="carousel-caption">
                    <div class="container py-4">
                                <div class="row g-5 hero-row">
                            <div class="col-lg-6 d-none d-lg-flex fadeInLeft animated">
                                <div class="text-start">
                                    <h1 class="display-5 text-white">Luxury Cars<br>for Every Occasion</h1>
                                    <p class="text-white-50">From sports cars to family SUVs — we have it all</p>
                                    <a href="{{ route('search') }}" class="btn btn-primary rounded-pill py-3 px-5">View All Vehicles</a>
                                </div>
                            </div>
                            <div class="col-lg-6 fadeInRight animated">
                                <div class="rounded p-3 p-md-4 hero-quote-card">
                                    <h4 class="text-white mb-2 hero-card-title">QUICK SEARCH</h4>
                                    <form action="{{ route('search') }}" method="GET" class="hero-search-form">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <input class="form-control" name="q" placeholder="Search make, model...">
                                            </div>
                                            <div class="col-12">
                                                <select class="form-select" name="category">
                                                    <option value="">All Categories</option>
                                                    @foreach($categories as $cat)
                                                        @php $ct = $cat->translations->firstWhere('locale', app()->getLocale()) ?? $cat->translations->first(); @endphp
                                                        <option value="{{ $cat->slug }}">{{ $ct->name ?? $cat->slug }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary w-100 py-2 hero-form-cta">
                                                    <i class="fas fa-search me-2"></i>Search Vehicles
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide 3 --}}
            <div class="carousel-item">
                <x-hero-slide-image
                    slug="slide-3"
                    desktop="theme/img/carousel-1.jpg"
                    alt="Luxury Rental"
                />
                <div class="carousel-caption">
                    <div class="container py-4">
                        <div class="row hero-row">
                            <div class="col-lg-8 mx-auto text-center fadeInUp animated">
                                <h1 class="display-4 text-white fw-bold mb-3">Miami's Premier<br>Vehicle Rental Service</h1>
                                <p class="text-white-50 fs-5 mb-4">{{ $totalVehicles ?? '50' }}+ premium vehicles available for rent today</p>
                                <a href="{{ route('booking.step1') }}" class="btn btn-primary rounded-pill py-3 px-5 me-3 fs-5">Book Now</a>
                                <a href="{{ route('search') }}" class="btn btn-secondary rounded-pill py-3 px-5 fs-5">Browse Vehicles</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>
</div>

{{-- ============================================================
     FEATURES
============================================================ --}}
<div class="container-fluid feature py-5">
    <div class="container py-5">
        <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
            <h1 class="display-5 text-capitalize mb-3">Rental <span class="text-primary">Features</span></h1>
            <p class="mb-0">At {{ config('app.name') }}, we make every journey smooth and memorable. Book with us today and experience the convenience of hassle-free car rentals!</p>
        </div>
        <div class="row g-4 align-items-center">
            <div class="col-xl-4">
                <div class="row gy-4 gx-0">
                    <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="feature-item">
                            <div class="feature-icon"><span class="fa fa-trophy fa-2x"></span></div>
                            <div class="ms-4">
                                <h5 class="mb-3">First Class Services</h5>
                                <p class="mb-0">We prioritize customer satisfaction by offering top-notch rental services, ensuring you receive a premium and hassle-free experience from start to finish.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="feature-item">
                            <div class="feature-icon"><span class="fa fa-road fa-2x"></span></div>
                            <div class="ms-4">
                                <h5 class="mb-3">24/7 Road Assistance</h5>
                                <p class="mb-0">Our dedicated support team is available round the clock to assist you on the road — flat tire, battery issue, or any emergency.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-xl-4 wow fadeInUp" data-wow-delay="0.2s">
                <img src="/theme/img/features-img.png" class="img-fluid w-100" style="object-fit: cover;" alt="Features">
            </div>
            <div class="col-xl-4">
                <div class="row gy-4 gx-0">
                    <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="feature-item justify-content-end">
                            <div class="text-end me-4">
                                <h5 class="mb-3">Quality at Minimum</h5>
                                <p class="mb-0">We believe in offering the best value for your money, providing high-quality vehicles and services at the most competitive rates in the market.</p>
                            </div>
                            <div class="feature-icon"><span class="fa fa-tag fa-2x"></span></div>
                        </div>
                    </div>
                    <div class="col-12 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="feature-item justify-content-end">
                            <div class="text-end me-4">
                                <h5 class="mb-3">Free Pick-Up &amp; Drop-Off</h5>
                                <p class="mb-0">Enjoy the convenience of free vehicle pick-up and drop-off services at designated locations, making your rental process effortless.</p>
                            </div>
                            <div class="feature-icon"><span class="fa fa-map-pin fa-2x"></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     ABOUT
============================================================ --}}
<div class="container-fluid overflow-hidden about py-5">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-xl-6 wow fadeInLeft" data-wow-delay="0.2s">
                <div class="about-item">
                    <div class="pb-5">
                        <h1 class="display-5 text-capitalize">{{ config('app.name') }} <span class="text-primary">About</span></h1>
                        <p class="mb-0">Welcome to {{ config('app.name') }}, your trusted partner for reliable and affordable car rental services. We are committed to providing top-quality vehicles, exceptional customer service, and seamless rental experiences for both short-term and long-term needs.</p>
                    </div>
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="about-item-inner border p-4">
                                <div class="about-icon mb-4">
                                    <img src="/theme/img/about-icon-1.png" class="img-fluid w-50 h-50" alt="Vision">
                                </div>
                                <h5 class="mb-3">Our Vision</h5>
                                <p class="mb-0">To be the leading car rental service provider, offering unparalleled convenience, affordability, and customer satisfaction.</p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="about-item-inner border p-4">
                                <div class="about-icon mb-4">
                                    <img src="/theme/img/about-icon-2.png" class="img-fluid h-50 w-50" alt="Mission">
                                </div>
                                <h5 class="mb-3">Our Mission</h5>
                                <p class="mb-0">To provide high-quality, well-maintained vehicles at competitive prices with outstanding service.</p>
                            </div>
                        </div>
                    </div>
                    <p class="text-item my-4">With 10 years of experience in the car rental industry, we have gained extensive knowledge and expertise in understanding customer needs.</p>
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="text-center rounded bg-secondary p-4">
                                <h1 class="display-6 text-white">10</h1>
                                <h5 class="text-light mb-0">Years Of Experience</h5>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="rounded">
                                <p class="mb-2"><i class="fa fa-check-circle text-primary me-1"></i> Wide Selection of Vehicles</p>
                                <p class="mb-2"><i class="fa fa-check-circle text-primary me-1"></i> 24/7 Customer Support</p>
                                <p class="mb-2"><i class="fa fa-check-circle text-primary me-1"></i> Well-Maintained Vehicles</p>
                                <p class="mb-0"><i class="fa fa-check-circle text-primary me-1"></i> Easy Booking Process</p>
                            </div>
                        </div>
                        <div class="col-lg-5 d-flex align-items-center">
                            <a href="{{ route('about') }}" class="btn btn-primary rounded py-3 px-5">More About Us</a>
                        </div>
                        <div class="col-lg-7">
                            <div class="d-flex align-items-center">
                                <img src="/theme/img/mercalo.jpg" class="img-fluid rounded-circle border border-4 border-secondary" style="width:100px;height:100px;" alt="Founder" onerror="this.src='/theme/img/testimonial-1.jpg'">
                                <div class="ms-4">
                                    <h4>Marcelo Vega</h4>
                                    <p class="mb-0">Co-Founder</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 wow fadeInRight" data-wow-delay="0.2s">
                <div class="about-img">
                    <div class="img-1">
                        <img src="/theme/img/about-img.jpg" class="img-fluid rounded h-100 w-100" alt="About">
                    </div>
                    <div class="img-2">
                        <img src="/theme/img/about-img-1.jpg" class="img-fluid rounded w-100" alt="About">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     COUNTER
============================================================ --}}
<div class="container-fluid counter bg-secondary py-5">
    <div class="container py-5">
        <div class="row g-5">
            @foreach([
                ['icon'=>'fa-thumbs-up','value'=>829,'label'=>'Happy Clients'],
                ['icon'=>'fa-car-alt','value'=>$totalVehicles ?? 56,'label'=>'Number of Cars'],
                ['icon'=>'fa-building','value'=>127,'label'=>'Locations'],
                ['icon'=>'fa-clock','value'=>589,'label'=>'Total Trips'],
            ] as $i => $stat)
            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="{{ ($i*0.2)+0.1 }}s">
                <div class="counter-item text-center">
                    <div class="counter-item-icon mx-auto">
                        <i class="fas {{ $stat['icon'] }} fa-2x"></i>
                    </div>
                    <div class="counter-counting my-3">
                        <span class="text-white fs-2 fw-bold" data-toggle="counter-up">{{ $stat['value'] }}</span>
                        <span class="h1 fw-bold text-white">+</span>
                    </div>
                    <h4 class="text-white mb-0">{{ $stat['label'] }}</h4>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ============================================================
     SERVICES
============================================================ --}}
<div class="container-fluid service py-5">
    <div class="container py-5">
        <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
            <h1 class="display-5 text-capitalize mb-3">MV Rental <span class="text-primary">Services</span></h1>
            <p class="mb-0">At {{ config('app.name') }}, we offer a variety of services to meet your transportation needs</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['icon'=>'fa-phone-alt','title'=>'Phone Reservation','desc'=>'Easily book your desired vehicle over the phone for quick and convenient service.'],
                ['icon'=>'fa-money-bill-alt','title'=>'Special Rates','desc'=>'Enjoy exclusive discounts and deals tailored for long-term rentals and corporate clients.'],
                ['icon'=>'fa-road','title'=>'Free Rides','desc'=>'Avail exciting offers and occasional free ride promotions as a token of our appreciation.'],
                ['icon'=>'fa-umbrella','title'=>'Life Insurance','desc'=>'Drive with confidence knowing that we offer life insurance coverage for added security.'],
                ['icon'=>'fa-building','title'=>'City to City','desc'=>'Seamless intercity travel solutions for comfortable and stress-free long-distance trips.'],
                ['icon'=>'fa-car-alt','title'=>'Luxury Car Rentals','desc'=>'Experience premium comfort and style with our high-end luxury vehicles.'],
            ] as $i => $svc)
            <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="{{ ($i%3)*0.2+0.1 }}s">
                <div class="service-item p-4">
                    <div class="service-icon mb-4">
                        <i class="fa {{ $svc['icon'] }} fa-2x"></i>
                    </div>
                    <h5 class="mb-3">{{ $svc['title'] }}</h5>
                    <p class="mb-0">{{ $svc['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ============================================================
     VEHICLE CATEGORIES CAROUSEL
============================================================ --}}
@if($featured->isNotEmpty())
<div class="container-fluid categories pb-5">
    <div class="container pb-5">
        <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
            <h1 class="display-5 text-capitalize mb-3">Our <span class="text-primary">Fleet</span></h1>
            <p class="mb-0">At {{ config('app.name') }}, we offer a wide range of vehicles to cater to different customer needs. Browse our top picks below.</p>
        </div>
        <div class="categories-carousel owl-carousel wow fadeInUp" data-wow-delay="0.1s">
            @foreach($featured as $v)
                @php $t = $v->translations->firstWhere('locale', app()->getLocale()) ?? $v->translations->first(); @endphp
                <div class="categories-item p-4">
                    <div class="categories-item-inner">
                        <div class="categories-img rounded-top">
                            <img src="{{ $v->images->first()->path ?? '/theme/img/Midas-Preview.png' }}"
                                class="img-fluid w-100 rounded-top"
                                alt="{{ $t->title ?? $v->make.' '.$v->model }}"
                                onerror="this.src='/theme/img/car-2.png'">
                        </div>
                        <div class="categories-content rounded-bottom p-4">
                            <h4>{{ $t->title ?? $v->make.' '.$v->model }}</h4>
                            <div class="categories-review mb-4">
                                <div class="me-3">4.5 Review</div>
                                <div class="d-flex justify-content-center text-secondary">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i>
                                    <i class="fas fa-star text-body"></i>
                                </div>
                            </div>
                            <div class="me-3 mb-4">Starting From</div>
                            <div class="mb-4">
                                <h4 class="bg-white text-primary rounded-pill py-2 px-4 mb-0">${{ number_format($v->price_per_day, 2) }}/Day</h4>
                            </div>
                            <div class="row gy-2 gx-0 text-center mb-4">
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-users text-dark"></i> <span class="text-body ms-1">{{ $v->seats ?? 4 }} Seat</span>
                                </div>
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-cogs text-dark"></i> <span class="text-body ms-1">{{ strtoupper(substr($v->transmission ?? 'Auto',0,2)) }}</span>
                                </div>
                                <div class="col-4">
                                    <i class="fa fa-gas-pump text-dark"></i> <span class="text-body ms-1">{{ ucfirst($v->fuel_type ?? 'Gas') }}</span>
                                </div>
                                @if($v->year)
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-car text-dark"></i> <span class="text-body ms-1">{{ $v->year }}</span>
                                </div>
                                @endif
                                @if($v->bags)
                                <div class="col-4">
                                    <i class="fa fa-suitcase text-dark"></i> <span class="text-body ms-1">{{ $v->bags }} Bags</span>
                                </div>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('vehicles.show', $v->id) }}" class="btn btn-secondary rounded-pill flex-fill py-2">Details</a>
                                <a href="{{ route('booking.step1') }}?vehicle_id={{ $v->id }}" class="btn btn-primary rounded-pill flex-fill py-2">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('search') }}" class="btn btn-primary rounded-pill py-3 px-5">
                <i class="fas fa-car me-2"></i>View All Vehicles
            </a>
        </div>
    </div>
</div>
@endif

{{-- ============================================================
     PROCESS STEPS
============================================================ --}}
<div class="container-fluid steps py-5">
    <div class="container py-5">
        <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
            <h1 class="display-5 text-capitalize text-white mb-3">MV Rental<span class="text-primary"> Process</span></h1>
            <p class="mb-0 text-white">At {{ config('app.name') }}, we make every journey smooth and memorable. Book with us today!</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                <div class="steps-item p-4 mb-4">
                    <h4>Come In Contact</h4>
                    <p class="mb-0">Reach out via phone, email, or visit our location to get expert assistance in selecting the best vehicle for your needs.</p>
                    <div class="setps-number">01.</div>
                </div>
            </div>
            <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                <div class="steps-item p-4 mb-4">
                    <h4>Choose A Car</h4>
                    <p class="mb-0">Browse our diverse fleet and select a vehicle that suits your budget, preferences, and travel requirements.</p>
                    <div class="setps-number">02.</div>
                </div>
            </div>
            <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.5s">
                <div class="steps-item p-4 mb-4">
                    <h4>Enjoy Driving</h4>
                    <p class="mb-0">Hit the road with confidence, knowing that you are driving a well-maintained, insured rental vehicle from {{ config('app.name') }}.</p>
                    <div class="setps-number">03.</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     BLOG
============================================================ --}}
@if(isset($latestPosts) && $latestPosts->isNotEmpty())
<div class="container-fluid blog py-5">
    <div class="container py-5">
        <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
            <h1 class="display-5 text-capitalize mb-3">MV Rental<span class="text-primary"> Blog &amp; News</span></h1>
            <p class="mb-0">Stay informed with the latest updates, travel tips, and industry insights through our blog.</p>
        </div>
        <div class="row g-4">
            @foreach($latestPosts as $i => $post)
                @php $pt = $post->translations->firstWhere('locale', app()->getLocale()) ?? $post->translations->first(); @endphp
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="{{ ($i*0.2)+0.1 }}s">
                    <div class="blog-item">
                        <div class="blog-img">
                            <img src="/theme/img/blog-{{ ($i%3)+1 }}.jpg" class="img-fluid rounded-top w-100" alt="{{ $pt->title ?? '' }}"
                                onerror="this.src='/theme/img/features-img.png'">
                        </div>
                        <div class="blog-content rounded-bottom p-4">
                            <div class="blog-date">{{ $post->created_at ? $post->created_at->format('d M Y') : date('d M Y') }}</div>
                            <div class="blog-comment my-3">
                                <div class="small"><span class="fa fa-user text-primary"></span><span class="ms-2">Admin</span></div>
                            </div>
                            <a href="{{ route('blog.show', $post->slug) }}" class="h4 d-block mb-3">
                                {{ $pt->title ?? 'Untitled' }}
                            </a>
                            <p class="mb-3">{{ Str::limit(strip_tags($pt->excerpt ?? $pt->content ?? ''), 100) }}</p>
                            <a href="{{ route('blog.show', $post->slug) }}">
                                Read More <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('blog.index') }}" class="btn btn-primary rounded-pill py-3 px-5">View All Posts</a>
        </div>
    </div>
</div>
@endif

{{-- ============================================================
     CTA BANNER
============================================================ --}}
<div class="container-fluid banner pb-5 wow zoomInDown" data-wow-delay="0.1s">
    <div class="container pb-5">
        <div class="banner-item rounded">
            <img src="/theme/img/banner-1.jpg" class="img-fluid rounded w-100" alt="Book Now" onerror="this.style.display='none'">
            <div class="banner-content">
                <h2 class="text-primary">Rent Your Car</h2>
                <h1 class="text-white">Interested in Renting?</h1>
                <p class="text-white">Don't hesitate and send us a message.</p>
                <div class="banner-btn">
                    <a href="https://wa.me/+17869785809" class="btn btn-secondary rounded-pill py-3 px-4 px-md-5 me-2">
                        <i class="fab fa-whatsapp me-2"></i>WhatsApp
                    </a>
                    <a href="{{ route('booking.step1') }}" class="btn btn-primary rounded-pill py-3 px-4 px-md-5 ms-2">
                        <i class="fas fa-car me-2"></i>Book Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     TESTIMONIALS
============================================================ --}}
<div class="container-fluid testimonial pb-5">
    <div class="container pb-5">
        <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
            <h1 class="display-5 text-capitalize mb-3">Our Clients<span class="text-primary"> Reviews</span></h1>
            <p class="mb-0">At {{ config('app.name') }}, customer satisfaction is at the heart of everything we do.</p>
        </div>
        <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
            @foreach([
                ['name'=>'Ahmed R.','review'=>4,'text'=>'Excellent service! The booking process was smooth and the car was in perfect condition. Highly recommended for anyone visiting Miami!'],
                ['name'=>'Sarah K.','review'=>3,'text'=>'Great customer support and excellent pricing. The free pick-up and drop-off made everything so convenient. Will definitely rent again!'],
                ['name'=>'Faisal M.','review'=>3,'text'=>'I rented a luxury car for my wedding, and it was a dream experience! The vehicle was spotless, and the team was extremely helpful.'],
                ['name'=>'Maria L.','review'=>5,'text'=>'Best rental company in Miami! Affordable prices, wide selection of vehicles, and outstanding customer service. 5 stars!'],
            ] as $i => $review)
            <div class="testimonial-item">
                <div class="testimonial-quote"><i class="fa fa-quote-right fa-2x"></i></div>
                <div class="testimonial-inner p-4">
                    <img src="/theme/img/testimonial-{{ ($i%3)+1 }}.jpg" class="img-fluid" alt="{{ $review['name'] }}"
                        onerror="this.src='/theme/img/about-img.jpg'">
                    <div class="ms-4">
                        <h4>{{ $review['name'] }}</h4>
                        <p>Verified Customer</p>
                        <div class="d-flex text-primary">
                            @for($s=1;$s<=5;$s++)
                                <i class="fas fa-star{{ $s > $review['review'] ? ' text-body' : '' }}"></i>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="border-top rounded-bottom p-4">
                    <p class="mb-0">{{ $review['text'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
