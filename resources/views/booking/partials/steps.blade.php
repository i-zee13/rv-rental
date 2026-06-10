@php
    $steps = ['1' => 'Select Vehicle', '2' => 'Add-ons', '3' => 'Your Details', '4' => 'Payment'];
    $current = (int) ($current ?? 1);
@endphp
<div class="booking-steps text-white-50">
    @foreach($steps as $n => $label)
        @php $num = (int) $n; @endphp
        <div class="step-item {{ $num === $current ? 'active text-white fw-bold' : '' }}">
            <span class="step-circle"
                style="background:{{ $num < $current ? '#198754' : ($num === $current ? 'var(--bs-primary)' : 'rgba(255,255,255,0.25)') }};color:#fff;">
                {{ $num < $current ? '✓' : $n }}
            </span>
            <span class="step-label">{{ $label }}</span>
        </div>
        @if($n < '4')<span class="d-none d-sm-inline text-white-50">›</span>@endif
    @endforeach
</div>
