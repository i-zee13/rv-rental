@props([
    'name' => 'images[]',
    'groupId' => 'dropify-group',
    'addLabel' => '+ Add another image',
    'height' => '160',
])

<div data-dropify-group id="{{ $groupId }}" {{ $attributes }}>
    <div class="mb-2" data-dropify-template>
        <x-dropify-input :name="$name" :height="$height" />
    </div>
</div>
<button type="button" class="text-sm text-indigo-600 font-medium hover:underline mt-1" data-dropify-add="#{{ $groupId }}">
    {{ $addLabel }}
</button>
