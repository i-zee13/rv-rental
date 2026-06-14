<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Login') | {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('theme/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/bootstrap-icons.css') }}">

    <style>
        :root {
            --auth-bg: #0f172a;
            --auth-accent: #f5c518;
            --auth-accent-hover: #e0b015;
        }

        body {
            font-family: 'Lato', sans-serif;
            min-height: 100vh;
            margin: 0;
            background: var(--auth-bg);
        }

        .auth-shell {
            min-height: 100vh;
        }

        .auth-form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.5rem;
        }

        .auth-form-wrap {
            width: 100%;
            max-width: 420px;
        }

        .auth-logo {
            max-height: 72px;
            width: auto;
            display: block;
            margin: 0 auto 1.25rem;
        }

        .auth-brand-fallback {
            width: 72px;
            height: 72px;
            border-radius: 1rem;
            background: linear-gradient(135deg, #6366f1, var(--auth-accent));
            color: #fff;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
        }

        .auth-subtitle {
            color: rgba(255, 255, 255, 0.72);
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-label {
            color: #fff;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .auth-input,
        .auth-input:focus {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #fff;
            min-height: 48px;
            box-shadow: none;
        }

        .auth-input::placeholder {
            color: rgba(255, 255, 255, 0.45);
        }

        .auth-input:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: var(--auth-accent);
        }

        .auth-input.is-invalid {
            border-color: #ef4444;
        }

        .auth-toggle {
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-left: 0;
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.75);
        }

        .auth-toggle:hover {
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
        }

        .btn-auth {
            background: var(--auth-accent);
            border: none;
            color: #111;
            font-weight: 700;
            min-height: 50px;
        }

        .btn-auth:hover,
        .btn-auth:focus {
            background: var(--auth-accent-hover);
            color: #111;
        }

        .auth-cover {
            position: relative;
            min-height: 320px;
            background-size: cover;
            background-position: center;
        }

        .auth-cover::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.35), rgba(15, 23, 42, 0.75));
        }

        .auth-cover-content {
            position: relative;
            z-index: 1;
            color: #fff;
            padding: 3rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .auth-cover-content h2 {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .auth-back-link {
            color: rgba(255, 255, 255, 0.65);
            text-decoration: none;
            font-size: 0.925rem;
        }

        .auth-back-link:hover {
            color: #fff;
        }

        .invalid-feedback {
            color: #fca5a5;
        }

        @media (min-width: 992px) {
            .auth-cover {
                min-height: 100vh;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="container-fluid auth-shell">
        <div class="row g-0">
            <div class="col-12 col-lg-5 col-xl-4 auth-form-panel">
                <div class="auth-form-wrap">
                    @yield('content')
                </div>
            </div>

            <div class="col-lg-7 col-xl-8 d-none d-lg-block">
                <div class="auth-cover" style="background-image: url('{{ asset('theme/img/carousel-1.jpg') }}');">
                    <div class="auth-cover-content">
                        <h2>{{ config('app.name', 'MV Miami Rental') }}</h2>
                        <p class="mb-0 opacity-75">Manage vehicles, bookings, leads, and content from one place.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('theme/js/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
