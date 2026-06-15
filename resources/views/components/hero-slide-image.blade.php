@props([
    'slug',
    'desktop',
    'alt' => '',
])

@php
    $desktopPath = ltrim($desktop, '/');
    $heroDir = 'theme/img/hero';
    $mobilePath = $heroDir . '/' . $slug . '-mobile.jpg';
    $tabletPath = $heroDir . '/' . $slug . '-tablet.jpg';
    $mobileSrc = file_exists(public_path($mobilePath)) ? asset($mobilePath) : asset($desktopPath);
    $tabletSrc = file_exists(public_path($tabletPath)) ? asset($tabletPath) : asset($desktopPath);
    $desktopSrc = asset($desktopPath);
@endphp

<picture class="hero-slide-media">
    <source media="(max-width: 575px)" srcset="{{ $mobileSrc }}">
    <source media="(max-width: 991px)" srcset="{{ $tabletSrc }}">
    <img src="{{ $desktopSrc }}" class="hero-slide-img" alt="{{ $alt }}" loading="{{ $slug === 'slide-1' ? 'eager' : 'lazy' }}">
</picture>
