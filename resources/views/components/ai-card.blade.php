@props(['title'=>null,'subtitle'=>null,'image'=>null,'badge'=>null,'href'=>null])

@php $tag = $href ? 'a' : 'div'; @endphp

<{{ $tag }}
    {{ $href ? "href=$href" : '' }}
    {{ $attributes->merge(['class'=>'glass-card rounded-2xl overflow-hidden group block']) }}>

    @if($image)
    <div class="h-44 overflow-hidden bg-gray-100">
        <img src="{{ $image }}" alt="{{ $title ?? '' }}"
            class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
            loading="lazy">
    </div>
    @endif

    <div class="p-4">
        @if($badge)
            <span class="badge badge-blue mb-2">{{ $badge }}</span>
        @endif
        @if($title)
            <div class="font-bold text-gray-900 text-base leading-snug mb-1 group-hover:text-primary-600 transition">{{ $title }}</div>
        @endif
        @if($subtitle)
            <div class="text-sm text-gray-500">{{ $subtitle }}</div>
        @endif
        {{ $slot ?? '' }}
    </div>
</{{ $tag }}>
