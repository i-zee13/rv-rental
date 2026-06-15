@props([
    'source' => 'website',
    'vehicles' => collect(),
    'variant' => 'standard',
])

<form action="{{ route('leads.store') }}" method="POST" class="lead-form lead-form--{{ $variant }}" id="lead-form-{{ $variant }}-{{ $source }}">
    @csrf
    <input type="hidden" name="source" value="{{ $source }}">
    <input type="hidden" name="pickup_time" value="{{ old('pickup_time', '12:00 PM') }}">
    <input type="hidden" name="dropoff_time" value="{{ old('dropoff_time', '12:00 PM') }}">

    {{-- Honeypot --}}
    <div style="position:absolute;left:-9999px;" aria-hidden="true">
        <input type="text" name="website_url" tabindex="-1" autocomplete="off">
    </div>

    @if($variant === 'hero')
    {{-- Smart 2-step hero form: trip details first, contact second --}}
    <div class="lead-form-steps" data-lead-form>
        <div class="lead-form-step" data-step="1">
            @if($vehicles->isNotEmpty())
            <div class="mb-2 mb-md-3">
                <select class="form-select" name="vehicle_id">
                    <option value="">Select your vehicle</option>
                    @foreach($vehicles as $v)
                        @php $vt = $v->translations->firstWhere('locale', app()->getLocale()) ?? $v->translations->first(); @endphp
                        <option value="{{ $v->id }}" {{ old('vehicle_id', request('vehicle_id')) == $v->id ? 'selected' : '' }}>
                            {{ $vt->title ?? $v->make.' '.$v->model }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="input-group mb-2 mb-md-3">
                <span class="input-group-text"><i class="fas fa-map-marker-alt text-primary"></i></span>
                <input class="form-control" type="text" name="pickup_location" value="{{ old('pickup_location', 'Miami, FL') }}"
                    placeholder="Pick-up location" required>
            </div>

            <div class="lead-form-dropoff mb-2 mb-md-3" data-dropoff-wrap style="display:none;">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-map-marker-alt text-primary"></i></span>
                    <input class="form-control" type="text" name="dropoff_location" value="{{ old('dropoff_location') }}"
                        placeholder="Drop-off location" data-dropoff-input>
                </div>
            </div>
            <button type="button" class="btn btn-link btn-sm text-white-50 p-0 mb-2 mb-md-3" data-toggle-dropoff>
                + Different drop-off location?
            </button>

            <div class="row g-2 mb-2 mb-md-3">
                <div class="col-6">
                    <label class="form-label text-white-50 small mb-1">Pick-up</label>
                    <input class="form-control" type="date" name="pickup_date" value="{{ old('pickup_date') }}"
                        min="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-6">
                    <label class="form-label text-white-50 small mb-1">Drop-off</label>
                    <input class="form-control" type="date" name="dropoff_date" value="{{ old('dropoff_date') }}"
                        min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                </div>
            </div>

            <button type="button" class="btn btn-primary w-100 py-2 py-md-3 fw-bold rounded-pill hero-form-cta" data-next-step>
                Check Availability <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </div>

        <div class="lead-form-step" data-step="2" style="display:none;">
            <p class="text-white-50 small mb-3"><i class="fas fa-check-circle text-primary me-1"></i> Almost done — how can we reach you?</p>

            <div class="row g-2 mb-3">
                <div class="col-12">
                    <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}"
                        placeholder="Your name *" required>
                </div>
                <div class="col-12 col-sm-6">
                    <input type="tel" class="form-control" name="phone" value="{{ old('phone') }}"
                        placeholder="Phone *" required>
                </div>
                <div class="col-12 col-sm-6">
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                        placeholder="Email *" required>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-light rounded-pill px-4" data-prev-step>
                    <i class="fas fa-arrow-left"></i>
                </button>
                <button type="submit" class="btn btn-light flex-grow-1 py-2 py-md-3 fw-bold rounded-pill hero-form-cta">
                    Book Now
                </button>
            </div>
        </div>
    </div>

    @else
    {{-- Contact / full page form --}}
    <div class="row g-3">
        @if($vehicles->isNotEmpty())
        <div class="col-12">
            <select class="form-select" name="vehicle_id">
                <option value="">Vehicle (optional)</option>
                @foreach($vehicles as $v)
                    @php $vt = $v->translations->firstWhere('locale', app()->getLocale()) ?? $v->translations->first(); @endphp
                    <option value="{{ $v->id }}" {{ old('vehicle_id', request('vehicle_id')) == $v->id ? 'selected' : '' }}>
                        {{ $vt->title ?? $v->make.' '.$v->model }} — ${{ number_format($v->price_per_day, 2) }}/day
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="col-md-6">
            <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" placeholder="Your name *" required>
        </div>
        <div class="col-md-6">
            <input type="tel" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="Phone *" required>
        </div>
        <div class="col-12">
            <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email *" required>
        </div>

        <div class="col-md-6">
            <input class="form-control" type="text" name="pickup_location" value="{{ old('pickup_location') }}" placeholder="Pick-up location">
        </div>
        <div class="col-md-6">
            <input class="form-control" type="text" name="dropoff_location" value="{{ old('dropoff_location') }}" placeholder="Drop-off (optional)">
        </div>
        <div class="col-md-6">
            <input class="form-control" type="date" name="pickup_date" value="{{ old('pickup_date') }}" min="{{ date('Y-m-d') }}" placeholder="Pick-up date">
        </div>
        <div class="col-md-6">
            <input class="form-control" type="date" name="dropoff_date" value="{{ old('dropoff_date') }}" min="{{ date('Y-m-d') }}" placeholder="Drop-off date">
        </div>

        <div class="col-12">
            <textarea class="form-control" name="message" rows="3" placeholder="Anything else we should know? (optional)">{{ old('message') }}</textarea>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill">
                <i class="fas fa-paper-plane me-2"></i>Send Request
            </button>
        </div>
    </div>
    @endif
</form>

@if($variant === 'hero')
@once
@push('scripts')
<script>
document.querySelectorAll('[data-lead-form]').forEach(function (wrap) {
    var form = wrap.closest('form');
    var step1 = wrap.querySelector('[data-step="1"]');
    var step2 = wrap.querySelector('[data-step="2"]');

    @if(old('first_name') || old('email') || $errors->any())
    if (step1 && step2) { step1.style.display = 'none'; step2.style.display = 'block'; }
    @endif
    var dropoffWrap = wrap.querySelector('[data-dropoff-wrap]');
    var dropoffInput = wrap.querySelector('[data-dropoff-input]');
    var toggleDropoff = wrap.querySelector('[data-toggle-dropoff]');

    if (toggleDropoff && dropoffWrap) {
        toggleDropoff.addEventListener('click', function () {
            var show = dropoffWrap.style.display === 'none';
            dropoffWrap.style.display = show ? 'block' : 'none';
            toggleDropoff.textContent = show ? '− Same as pick-up' : '+ Different drop-off location?';
            if (!show && dropoffInput) dropoffInput.value = '';
        });
    }

    wrap.querySelector('[data-next-step]')?.addEventListener('click', function () {
        if (!form.reportValidity()) return;
        step1.style.display = 'none';
        step2.style.display = 'block';
        step2.querySelector('input')?.focus();
    });

    wrap.querySelector('[data-prev-step]')?.addEventListener('click', function () {
        step2.style.display = 'none';
        step1.style.display = 'block';
    });
});
</script>
@endpush
@endonce
@endif
