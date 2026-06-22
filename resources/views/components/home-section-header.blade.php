@props([
    'title',
    'subtitle' => null,
    'viewAllUrl' => null,
    'viewAllLabel' => 'View All',
])

<div class="home-section-header d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4 wow fadeInUp" data-wow-delay="0.05s">
    <div class="flex-grow-1">
        <h2 class="home-section-title mb-2">{{ $title }}</h2>
        <div class="home-section-rule"></div>
        @if($subtitle)
            <p class="home-section-sub mb-0 mt-3">{{ $subtitle }}</p>
        @endif
    </div>
    @if($viewAllUrl)
        <a href="{{ $viewAllUrl }}" class="home-section-link text-nowrap">
            {{ $viewAllLabel }} <i class="fas fa-chevron-right ms-1 small"></i>
        </a>
    @endif
</div>
