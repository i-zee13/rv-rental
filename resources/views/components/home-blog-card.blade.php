@props(['post'])

@php
    $t = $post->translations->firstWhere('locale', app()->getLocale()) ?? $post->translations->first();
    $title = $t->title ?? 'Untitled';
    $excerpt = Str::limit(strip_tags($t->excerpt ?? $t->content ?? ''), 90);
    $img = $post->featured_image ? asset($post->featured_image) : '/theme/img/blog-1.jpg';
@endphp

<div class="home-mini-card home-mini-card--blog">
    <a href="{{ route('blog.show', $post->slug) }}" class="home-mini-card-img d-block">
        <img src="{{ $img }}" alt="{{ $title }}" loading="lazy" onerror="this.src='/theme/img/features-img.png'">
    </a>
    <div class="home-mini-card-body">
        <time class="home-mini-date">{{ $post->created_at?->format('M j, Y') ?? now()->format('M j, Y') }}</time>
        <h3 class="home-mini-card-title">
            <a href="{{ route('blog.show', $post->slug) }}">{{ Str::limit($title, 48) }}</a>
        </h3>
        @if($excerpt)
            <p class="home-mini-excerpt mb-0">{{ $excerpt }}</p>
        @endif
        <a href="{{ route('blog.show', $post->slug) }}" class="home-mini-read">Read more <i class="fas fa-arrow-right ms-1"></i></a>
    </div>
</div>
