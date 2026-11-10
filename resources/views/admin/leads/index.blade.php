@extends('admin.layout')

@section('title', 'Leads')

@section('content')
<div>
    <div class="admin-page-header flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold">Leads</h1>
            <p class="text-sm text-gray-500 mt-1">Manage inquiries from website forms</p>
        </div>
        <div class="flex flex-wrap gap-2 text-xs">
            @foreach(\App\Models\Lead::STATUSES as $key => $label)
                <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                    {{ $label }}: <strong>{{ $counts[$key] ?? 0 }}</strong>
                </span>
            @endforeach
        </div>
    </div>

    <form method="GET" class="flex flex-col sm:flex-row gap-3 mb-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, email, reference..."
            class="border rounded-lg px-3 py-2 text-sm flex-1">
        <select name="status" class="border rounded-lg px-3 py-2 text-sm">
            <option value="">All statuses</option>
            @foreach(\App\Models\Lead::STATUSES as $key => $label)
                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium">Filter</button>
        @if(request()->hasAny(['q','status']))
            <a href="{{ route('admin.leads.index') }}" class="border px-4 py-2 rounded-lg text-sm text-center">Clear</a>
        @endif
    </form>

    <div class="admin-table-wrap bg-white border rounded-lg p-3">
        <table class="admin-datatable w-full text-sm display">
                <thead class="text-left text-xs uppercase text-gray-500">
                    <tr>
                        <th class="p-3">Ref</th>
                        <th class="p-3">Customer</th>
                        <th class="p-3 hidden md:table-cell">Vehicle</th>
                        <th class="p-3 hidden lg:table-cell">Dates</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 hidden sm:table-cell">Source</th>
                        <th class="p-3">Date</th>
                        <th class="p-3 no-sort">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3 font-mono text-xs">{{ $lead->reference }}</td>
                        <td class="p-3">
                            <div class="font-medium">{{ $lead->full_name }}</div>
                            <div class="text-gray-500 text-xs">{{ $lead->email }}</div>
                        </td>
                        <td class="p-3 hidden md:table-cell text-gray-600">{{ $lead->vehicle_name ?: '—' }}</td>
                        <td class="p-3 hidden lg:table-cell text-gray-600 text-xs">
                            @if($lead->pickup_date)
                                {{ $lead->pickup_date->format('M j') }} → {{ $lead->dropoff_date?->format('M j, Y') ?? '—' }}
                            @else — @endif
                        </td>
                        <td class="p-3">
                            <form method="POST" action="{{ route('admin.leads.status', $lead->id) }}" class="inline-block">
                                @csrf
                                <select name="status" onchange="this.form.submit()"
                                    class="border rounded-lg px-2 py-1 text-xs font-medium
                                    @switch($lead->status)
                                        @case('new') bg-blue-50 text-blue-700 border-blue-200 @break
                                        @case('lead') bg-indigo-50 text-indigo-700 border-indigo-200 @break
                                        @case('contacted') bg-yellow-50 text-yellow-800 border-yellow-200 @break
                                        @case('qualified') bg-purple-50 text-purple-700 border-purple-200 @break
                                        @case('converted') bg-green-50 text-green-700 border-green-200 @break
                                        @case('spam') bg-red-50 text-red-700 border-red-200 @break
                                        @default bg-gray-50 text-gray-600 border-gray-200
                                    @endswitch">
                                    @foreach(\App\Models\Lead::STATUSES as $key => $label)
                                        <option value="{{ $key }}" {{ $lead->status == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="p-3 hidden sm:table-cell text-gray-500 capitalize">{{ $lead->source }}</td>
                        <td class="p-3 text-gray-500 text-xs whitespace-nowrap">{{ $lead->created_at->format('M j, H:i') }}</td>
                        <td class="p-3">
                            <x-admin-table-actions
                                :view="route('admin.leads.show', $lead->id)"
                                view-target="_self"
                            />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-gray-400">No leads yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
    </div>
</div>
@endsection
