@props(['delay' => '0.1s'])

<div class="home-cards-scroller wow fadeInUp" data-wow-delay="{{ $delay }}">
    <div class="home-cards-track" data-home-autoscroll tabindex="0">
        {{ $slot }}
    </div>
    <div class="home-cards-scrolltrack" aria-hidden="true">
        <div class="home-cards-scrollthumb"></div>
    </div>
</div>
