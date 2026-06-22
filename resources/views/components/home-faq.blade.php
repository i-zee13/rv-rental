@props(['faqs', 'title' => null, 'subtitle' => null])

@if($faqs->isNotEmpty())
@php
    $heading = $title ?? __('ui.faq_title', [], app()->getLocale()) ?: 'Frequently Asked Questions';
    $sub = $subtitle ?? __('ui.faq_sub', [], app()->getLocale()) ?: 'Quick answers about rentals, bookings, and our Miami fleet.';
@endphp

<section class="home-faq-section py-5" id="home-faq">
    <div class="container py-4">
        <div class="text-center mx-auto mb-5 wow fadeInUp" style="max-width:640px;">
            <h2 class="home-section-title mb-2">{{ $heading }}</h2>
            <div class="home-section-rule mx-auto"></div>
            <p class="home-section-sub mt-3 mb-0">{{ $sub }}</p>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-lg-4 d-none d-lg-block wow fadeInLeft">
                <div class="home-faq-aside">
                    <div class="home-faq-aside-icon"><i class="fas fa-question-circle"></i></div>
                    <h3 class="h5 fw-bold mb-2">Need help?</h3>
                    <p class="text-muted small mb-3">Search common questions or message us on WhatsApp for a fast reply.</p>
                    <a href="https://wa.me/+17869785809" class="btn btn-success rounded-pill btn-sm" target="_blank" rel="noopener">
                        <i class="fab fa-whatsapp me-1"></i> WhatsApp Us
                    </a>
                </div>
            </div>
            <div class="col-lg-8 wow fadeInRight">
                <div class="home-faq-search-wrap mb-3">
                    <input type="search" class="form-control home-faq-search" placeholder="Search FAQs…" data-faq-filter="#homeFaqAccordion .accordion-item">
                </div>
                <div class="accordion home-faq-accordion" id="homeFaqAccordion">
                    @foreach($faqs as $i => $faq)
                        @php $t = $faq->translation(); @endphp
                        @if($t && filled($t->question))
                        <div class="accordion-item" data-faq-text="{{ strtolower($t->question.' '.$t->answer) }}">
                            <h3 class="accordion-header" id="home-faq-h-{{ $faq->id }}">
                                <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#home-faq-b-{{ $faq->id }}"
                                    aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">
                                    {{ $t->question }}
                                </button>
                            </h3>
                            <div id="home-faq-b-{{ $faq->id }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}"
                                data-bs-parent="#homeFaqAccordion">
                                <div class="accordion-body text-muted">{!! nl2br(e($t->answer)) !!}</div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
                <p class="home-faq-empty text-muted small mt-3 d-none">No matching questions — try another keyword or <a href="{{ route('contact') }}">contact us</a>.</p>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.querySelectorAll('.home-faq-search').forEach(function (input) {
    input.addEventListener('input', function () {
        var q = this.value.trim().toLowerCase();
        var items = document.querySelectorAll('#homeFaqAccordion .accordion-item');
        var shown = 0;
        items.forEach(function (item) {
            var match = !q || (item.getAttribute('data-faq-text') || '').includes(q);
            item.classList.toggle('d-none', !match);
            if (match) shown++;
        });
        var empty = document.querySelector('.home-faq-empty');
        if (empty) empty.classList.toggle('d-none', shown > 0);
    });
});
</script>
@endpush
@endif
