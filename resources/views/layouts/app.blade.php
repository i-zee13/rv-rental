<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">
    <meta name="theme-color" content="#192a30">

    {{-- SEO: artesaos/seotools (managed from Admin → SEO) --}}
    {!! \Artesaos\SEOTools\Facades\SEOTools::generate() !!}

    <!-- Google Fonts (match original template) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Space+Grotesk:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Icon Fonts (CDN — local webfonts folder was missing) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Libraries -->
    <link rel="stylesheet" href="{{ asset('theme/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/owl.carousel.min.css') }}">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('theme/css/bootstrap.min.css') }}">

    <!-- Theme Style -->
    <link rel="stylesheet" href="{{ asset('theme/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/theme-fixes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mv-glass.css') }}?v=5">

    @include('partials.booking-chat-assets')
    @stack('styles')
</head>
<body class="@yield('body_class')">

    <!-- Spinner -->
    <div id="spinner" class="bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Topbar -->
    <div class="container-fluid topbar bg-secondary d-none d-xl-block w-100">
        <div class="container">
            <div class="row gx-0 align-items-center" style="height: 45px;">
                <div class="col-lg-6 text-center text-lg-start mb-lg-0">
                    <div class="d-flex flex-wrap">
                        <a href="#" class="text-muted me-4"><i class="fas fa-map-marker-alt text-primary me-2"></i>Miami FL 33122</a>
                        <a href="tel:+17869785809" class="text-muted me-4"><i class="fas fa-phone-alt text-primary me-2"></i>+1 (786) 978-5809</a>
                        <a href="mailto:info@mvmiamirental.com" class="text-muted me-0"><i class="fas fa-envelope text-primary me-2"></i>info@mvmiamirental.com</a>
                    </div>
                </div>
                <div class="col-lg-6 text-center text-lg-end">
                    <div class="d-flex align-items-center justify-content-end">
                        <a href="#" class="btn btn-light btn-sm-square rounded-circle me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-light btn-sm-square rounded-circle me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-light btn-sm-square rounded-circle me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="btn btn-light btn-sm-square rounded-circle me-0"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <div class="container-fluid nav-bar sticky-top px-0 px-lg-4 py-2 py-lg-0">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a href="{{ route('home') }}" class="navbar-brand p-0">
                    <h1 class="text-primary m-0 d-flex align-items-center">
                        <img src="{{ asset('theme/img/logo.png') }}" alt="MV Miami Rental">
                        <span class="brand-text">Miami Rentals</span>
                    </h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav mx-auto py-0">
                        <a href="{{ route('home') }}" class="nav-item nav-link {{ request()->routeIs('home') ? 'active' : '' }}">{{ __('ui.home') }}</a>
                        <a href="{{ route('search') }}" class="nav-item nav-link {{ request()->routeIs('search') ? 'active' : '' }}">{{ __('ui.vehicles') }}</a>
                        <a href="{{ route('blog.index') }}" class="nav-item nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}">{{ __('ui.blog') }}</a>
                        <a href="{{ route('about') }}" class="nav-item nav-link {{ request()->routeIs('about') ? 'active' : '' }}">{{ __('ui.about') }}</a>
                        <a href="{{ route('contact') }}" class="nav-item nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">{{ __('ui.contact') }}</a>
                    </div>
                    <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2 nav-actions mt-3 mt-lg-0">
                        <form method="GET" action="{{ route('set-locale') }}" class="mb-0">
                            <select name="locale" onchange="this.form.submit()" class="form-select form-select-sm w-100">
                                <option value="en" {{ app()->getLocale()=='en'?'selected':'' }}>EN</option>
                                <option value="es" {{ app()->getLocale()=='es'?'selected':'' }}>ES</option>
                            </select>
                        </form>
                        <a href="{{ route('booking.step1') }}" class="btn btn-primary rounded-pill py-2 px-4 text-center">{{ __('ui.get_started') }}</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    <div class="container-fluid footer py-5 wow fadeIn" data-wow-delay="0.2s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="text-white mb-4"><i class="fas fa-car-alt me-3"></i>{{ config('app.name', 'MV Rental') }}</h4>
                        <p>{{ __('ui.footer_about') }}</p>
                        <div class="d-flex">
                            <a href="#" class="btn btn-primary btn-md-square rounded-circle me-3"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="btn btn-primary btn-md-square rounded-circle me-3"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="btn btn-primary btn-md-square rounded-circle me-3"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="btn btn-primary btn-md-square rounded-circle me-0"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="text-white mb-4">{{ __('ui.quick_links') }}</h4>
                        <a href="{{ route('home') }}"><i class="fas fa-angle-right me-2"></i> {{ __('ui.home') }}</a>
                        <a href="{{ route('search') }}"><i class="fas fa-angle-right me-2"></i> {{ __('ui.browse_vehicles') }}</a>
                        <a href="{{ route('blog.index') }}"><i class="fas fa-angle-right me-2"></i> {{ __('ui.blog_news') }}</a>
                        <a href="{{ route('about') }}"><i class="fas fa-angle-right me-2"></i> {{ __('ui.about_us') }}</a>
                        <a href="{{ route('contact') }}"><i class="fas fa-angle-right me-2"></i> {{ __('ui.contact') }}</a>
                        <a href="{{ route('pages.show', ['slug'=>'terms']) }}"><i class="fas fa-angle-right me-2"></i> {{ __('ui.terms') }}</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="text-white mb-4">{{ __('ui.contact_info') }}</h4>
                        <a href="#"><i class="fas fa-map-marker-alt text-primary me-2"></i> Miami FL 33122</a>
                        <a href="tel:+17869785809"><i class="fas fa-phone text-primary me-2"></i> +1 (786) 978-5809</a>
                        <a href="mailto:info@mvmiamirental.com"><i class="fas fa-envelope text-primary me-2"></i> info@mvmiamirental.com</a>
                        <a href="https://wa.me/+17869785809"><i class="fab fa-whatsapp text-primary me-2"></i> {{ __('ui.whatsapp_us') }}</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="text-white mb-4">{{ __('ui.book_vehicle') }}</h4>
                        <p>{{ __('ui.book_vehicle_text') }}</p>
                        <a href="{{ route('booking.step1') }}" class="btn btn-primary rounded-pill py-2 px-4 mb-2">
                            <i class="fas fa-car me-2"></i>{{ __('ui.start_booking') }}
                        </a>
                        <a href="{{ route('search') }}" class="btn btn-secondary rounded-pill py-2 px-4">
                            <i class="fas fa-search me-2"></i>{{ __('ui.browse_fleet') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="container-fluid copyright py-4">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-6 text-center text-md-start mb-md-0">
                    <span class="text-body"><a href="{{ route('home') }}" class="border-bottom text-white">{{ config('app.name', 'MV Rental') }}</a> © {{ date('Y') }}. {{ __('ui.rights') }}</span>
                </div>
                <div class="col-md-6 text-center text-md-end text-body">
                    <a href="{{ route('pages.show', ['slug'=>'privacy']) }}" class="text-white border-bottom me-3">{{ __('ui.privacy') }}</a>
                    <a href="{{ route('pages.show', ['slug'=>'terms']) }}" class="text-white border-bottom">{{ __('ui.terms') }}</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary rounded-circle back-to-top" aria-label="{{ __('ui.back_to_top') }}" title="{{ __('ui.back_to_top') }}"><i class="fa fa-arrow-up"></i></a>

    <!-- JS Libraries -->
    <script src="{{ asset('theme/js/jquery.min.js') }}"></script>
    <script src="{{ asset('theme/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('theme/js/wow.min.js') }}"></script>
    <script src="{{ asset('theme/js/easing.min.js') }}"></script>
    <script src="{{ asset('theme/js/waypoints.min.js') }}"></script>
    <script src="{{ asset('theme/js/counterup.min.js') }}"></script>
    <script src="{{ asset('theme/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('theme/js/main.js') }}"></script>

    @stack('scripts')

    @include('components.booking-chat')
    @include('partials.booking-chat-scripts')
</body>
</html>
