@props([
    'name' => 'image',
    'id' => null,
    'defaultFile' => null,
    'height' => '180',
    'maxSize' => '4M',
    'extensions' => 'png jpg jpeg gif webp',
    'multiple' => false,
    'multipleClass' => false,
    'message' => null,
])

@php
    $inputId = $id ?? 'dropify-' . Str::slug($name, '-') . '-' . uniqid();
@endphp

<input
    type="file"
    name="{{ $name }}"
    id="{{ $inputId }}"
    accept="image/*"
    @if($multiple) multiple @endif
    class="dropify {{ $multipleClass ? 'dropify-multiple' : '' }}"
    data-height="{{ $height }}"
    data-max-file-size="{{ $maxSize }}"
    data-allowed-file-extensions="{{ $extensions }}"
    @if($defaultFile) data-default-file="{{ $defaultFile }}" @endif
    @if($message) data-default-msg="{{ $message }}" @endif
    {{ $attributes }}
>
