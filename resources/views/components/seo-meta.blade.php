{{--
    Central SEO head tags — populated by SeoManager via $seoHead.
    Fallback chain: entity SEO → page SEO → global defaults.
--}}
@php
    $seo = $seoHead ?? [];
    $title = $seo['title'] ?? config('app.name');
    $description = $seo['description'] ?? '';
    $keywords = $seo['keywords'] ?? null;
    $canonical = $seo['canonical'] ?? url()->current();
    $robots = $seo['robots'] ?? 'index,follow';
    $og = $seo['og'] ?? [];
    $twitter = $seo['twitter'] ?? [];
    $jsonLd = $seo['json_ld'] ?? null;
    $verification = $seo['verification'] ?? [];
    $siteName = config('app.name', 'MV Miami Rental');
@endphp

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
@if($keywords)
<meta name="keywords" content="{{ is_array($keywords) ? implode(', ', $keywords) : $keywords }}">
@endif
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonical }}">

<meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
<meta property="og:type" content="{{ $og['type'] ?? 'website' }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $og['title'] ?? $title }}">
<meta property="og:description" content="{{ $og['description'] ?? $description }}">
<meta property="og:url" content="{{ $canonical }}">
@if(!empty($og['image']))
<meta property="og:image" content="{{ $og['image'] }}">
@endif

<meta name="twitter:card" content="{{ $twitter['card'] ?? 'summary_large_image' }}">
<meta name="twitter:title" content="{{ $twitter['title'] ?? $og['title'] ?? $title }}">
<meta name="twitter:description" content="{{ $twitter['description'] ?? $og['description'] ?? $description }}">
@if(!empty($twitter['site']))
<meta name="twitter:site" content="{{ $twitter['site'] }}">
@endif
@if(!empty($og['image']))
<meta name="twitter:image" content="{{ $og['image'] }}">
@endif

@if(!empty($verification['google']))
<meta name="google-site-verification" content="{{ $verification['google'] }}">
@endif
@if(!empty($verification['bing']))
<meta name="msvalidate.01" content="{{ $verification['bing'] }}">
@endif

@if(!empty($jsonLd))
<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
@endif
