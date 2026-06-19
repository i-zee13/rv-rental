@extends('admin.layout')

@section('title', 'FAQs')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="text-xl font-semibold">FAQs</h1>
        <p class="text-sm text-gray-500 mt-1">Manage questions by type — general (pick pages), vehicles, or homes.</p>
    </div>
    <a href="{{ route('admin.faqs.create') }}" class="bg-yellow-500 text-black px-4 py-2 rounded font-medium">Add FAQ</a>
</div>

@if(session('success'))
    <div class="bg-green-50 text-green-700 border border-green-200 rounded-lg px-4 py-3 mb-4 text-sm">{{ session('success') }}</div>
@endif

<div class="admin-table-wrap bg-white border rounded-lg p-3">
    <table class="admin-datatable w-full text-sm display">
        <thead>
            <tr>
                <th class="p-3 text-left">Order</th>
                <th class="p-3 text-left">Type</th>
                <th class="p-3 text-left">Question (EN)</th>
                <th class="p-3 text-left">Pages / Placement</th>
                <th class="p-3 text-left">Status</th>
                <th class="p-3 no-sort">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($faqs as $faq)
                @php
                    $en = $faq->translations->firstWhere('locale', 'en');
                    $pages = collect($faq->page_keys ?? [])->map(fn ($k) => \App\Models\Faq::PAGE_OPTIONS[$k] ?? $k)->take(3);
                @endphp
                <tr class="border-t">
                    <td class="p-3">{{ $faq->sort_order }}</td>
                    <td class="p-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $faq->scope === 'vehicle' ? 'bg-blue-100 text-blue-800' : ($faq->scope === 'property' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700') }}">
                            {{ $faq->scopeLabel() }}
                        </span>
                    </td>
                    <td class="p-3 font-medium max-w-xs truncate">{{ $en->question ?? '—' }}</td>
                    <td class="p-3 text-xs text-gray-600">
                        @if($faq->scope === 'general')
                            {{ $pages->implode(', ') }}
                            @if(count($faq->page_keys ?? []) > 3) … @endif
                        @elseif($faq->scope === 'vehicle')
                            All vehicle pages
                        @else
                            All property pages
                        @endif
                    </td>
                    <td class="p-3">
                        @if($faq->is_active)
                            <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs">Active</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs">Hidden</span>
                        @endif
                    </td>
                    <td class="p-3">
                        <x-admin-table-actions
                            :edit="route('admin.faqs.edit', $faq->id)"
                            :delete-action="route('admin.faqs.destroy', $faq->id)"
                            delete-confirm="Delete this FAQ?"
                        />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-500">
                        No FAQs yet. <a href="{{ route('admin.faqs.create') }}" class="text-indigo-600">Add your first FAQ</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
