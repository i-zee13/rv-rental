@extends('admin.auth.layout')

@section('title', 'Admin Login')

@section('content')
    @php
        $logoPath = public_path('theme/img/logo.png');
    @endphp

    @if(file_exists($logoPath))
        <img src="{{ asset('theme/img/logo.png') }}" alt="{{ config('app.name') }}" class="auth-logo">
    @else
        <div class="auth-brand-fallback">MV</div>
    @endif

    <p class="auth-subtitle">Sign in to the admin panel</p>

    @if($errors->any())
        <div class="alert alert-danger py-2 mb-4" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.post') }}" novalidate>
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label auth-label">Email Address</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="form-control auth-input @error('email') is-invalid @enderror"
                placeholder="you@example.com"
                required
                autocomplete="email"
                autofocus
            >
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="form-label auth-label">Password</label>
            <div class="input-group">
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-control auth-input @error('password') is-invalid @enderror"
                    placeholder="Enter your password"
                    required
                    autocomplete="current-password"
                >
                <button type="button" class="input-group-text auth-toggle" id="togglePassword" aria-label="Show password">
                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-auth btn-lg w-100 mb-3">
            Sign In
        </button>

        <div class="text-center">
            <a href="{{ route('home') }}" class="auth-back-link">
                <i class="bi bi-arrow-left me-1"></i> Back to website
            </a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    document.getElementById('togglePassword')?.addEventListener('click', function () {
        const input = document.getElementById('password');
        const icon = document.getElementById('togglePasswordIcon');
        const isHidden = input.type === 'password';

        input.type = isHidden ? 'text' : 'password';
        icon.classList.toggle('bi-eye', !isHidden);
        icon.classList.toggle('bi-eye-slash', isHidden);
    });
</script>
@endpush
