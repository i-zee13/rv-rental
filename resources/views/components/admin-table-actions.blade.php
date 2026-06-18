@props([
    'view' => null,
    'viewTarget' => '_blank',
    'edit' => null,
    'deleteAction' => null,
    'deleteConfirm' => 'Are you sure you want to delete this?',
])

<div class="flex flex-wrap items-center gap-1.5">
    @if($view)
        <a href="{{ $view }}" target="{{ $viewTarget }}" class="btn-sm btn-sm-secondary">View</a>
    @endif
    @if($edit)
        <a href="{{ $edit }}" class="btn-sm btn-sm-primary">Edit</a>
    @endif
    @if($deleteAction)
        <form action="{{ $deleteAction }}" method="POST" class="inline"
            onsubmit="return confirm(@json($deleteConfirm))">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-sm btn-sm-danger">Delete</button>
        </form>
    @endif
    {{ $slot }}
</div>
