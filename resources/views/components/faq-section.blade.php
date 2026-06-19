@props(['faqs', 'title' => null, 'id' => 'faqAccordion'])

@if($faqs->isNotEmpty())
@php
    $heading = $title ?? __('ui.faq_title', [], app()->getLocale()) ?: 'Frequently Asked Questions';
    $schemaItems = $faqs->map(function ($faq) {
        $t = $faq->translation();
        return [
            '@type' => 'Question',
            'name' => $t?->question,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => strip_tags($t?->answer ?? ''),
            ],
        ];
    })->filter(fn ($item) => filled($item['name']))->values();
@endphp

<section class="mv-faq-section py-5" aria-labelledby="{{ $id }}-heading">
    <div class="container">
        <h3 class="mb-4 wow fadeInUp" id="{{ $id }}-heading">{{ $heading }}</h3>
        <div class="accordion wow fadeInUp" id="{{ $id }}" data-wow-delay="0.1s">
            @foreach($faqs as $i => $faq)
                @php $t = $faq->translation(); @endphp
                @if($t && filled($t->question))
                <div class="accordion-item border rounded mb-2 overflow-hidden">
                    <h4 class="accordion-header" id="{{ $id }}-head-{{ $faq->id }}">
                        <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#{{ $id }}-body-{{ $faq->id }}"
                            aria-expanded="{{ $i === 0 ? 'true' : 'false' }}"
                            aria-controls="{{ $id }}-body-{{ $faq->id }}">
                            {{ $t->question }}
                        </button>
                    </h4>
                    <div id="{{ $id }}-body-{{ $faq->id }}"
                        class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}"
                        aria-labelledby="{{ $id }}-head-{{ $faq->id }}"
                        data-bs-parent="#{{ $id }}">
                        <div class="accordion-body text-muted">
                            {!! nl2br(e($t->answer)) !!}
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</section>

@if($schemaItems->isNotEmpty())
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => $schemaItems,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
@endif
