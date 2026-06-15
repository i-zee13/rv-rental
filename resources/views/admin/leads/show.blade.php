@extends('admin.layout')

@section('title', 'Lead ' . $lead->reference)

@section('content')
<div class="max-w-4xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.leads.index') }}" class="text-gray-500 hover:text-gray-800 text-sm">← Back to Leads</a>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold">{{ $lead->full_name }}</h1>
            <p class="text-gray-500 font-mono text-sm mt-1">{{ $lead->reference }}</p>
        </div>
        @php
            $colors = [
                'new' => 'bg-blue-100 text-blue-700',
                'lead' => 'bg-indigo-100 text-indigo-700',
                'contacted' => 'bg-yellow-100 text-yellow-800',
                'qualified' => 'bg-purple-100 text-purple-700',
                'converted' => 'bg-green-100 text-green-700',
                'spam' => 'bg-red-100 text-red-700',
                'closed' => 'bg-gray-100 text-gray-600',
            ];
        @endphp
        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $colors[$lead->status] ?? '' }}">
            {{ $lead->status_label }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white border rounded-lg p-5">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Contact</h2>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-gray-400">Email</dt><dd class="font-medium"><a href="mailto:{{ $lead->email }}" class="text-indigo-600">{{ $lead->email }}</a></dd></div>
                <div><dt class="text-gray-400">Phone</dt><dd class="font-medium">{{ $lead->phone ?: '—' }}</dd></div>
                <div><dt class="text-gray-400">Source</dt><dd class="capitalize">{{ $lead->source }}</dd></div>
                <div><dt class="text-gray-400">Submitted</dt><dd>{{ $lead->created_at->format('M j, Y g:i A') }}</dd></div>
                @if($lead->contacted_at)
                <div><dt class="text-gray-400">First Contacted</dt><dd>{{ $lead->contacted_at->format('M j, Y g:i A') }}</dd></div>
                @endif
            </dl>
        </div>

        <div class="bg-white border rounded-lg p-5">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Rental Details</h2>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-gray-400">Vehicle</dt><dd class="font-medium">{{ $lead->vehicle_name ?: '—' }}</dd></div>
                @if($lead->property_name)
                <div><dt class="text-gray-400">Property</dt><dd class="font-medium">{{ $lead->property_name }}</dd></div>
                @endif
                <div><dt class="text-gray-400">Pick-up</dt><dd>{{ $lead->pickup_location ?: '—' }} {{ $lead->pickup_time ? "({$lead->pickup_time})" : '' }}</dd></div>
                <div><dt class="text-gray-400">Drop-off</dt><dd>{{ $lead->dropoff_location ?: '—' }} {{ $lead->dropoff_time ? "({$lead->dropoff_time})" : '' }}</dd></div>
                <div><dt class="text-gray-400">Dates</dt>
                    <dd>
                        @if($lead->pickup_date)
                            {{ $lead->pickup_date->format('M j, Y') }} → {{ $lead->dropoff_date?->format('M j, Y') ?? '—' }}
                        @else — @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    @if($lead->message)
    <div class="bg-white border rounded-lg p-5 mb-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Message</h2>
        <p class="text-gray-700 whitespace-pre-wrap">{{ $lead->message }}</p>
    </div>
    @endif

    <div class="bg-white border rounded-lg p-5 mb-6 text-xs text-gray-500 flex flex-wrap gap-4">
        <span>Customer email: {{ $lead->customer_email_sent ? '✓ Sent' : '✗ Not sent' }}</span>
        <span>Admin email: {{ $lead->admin_email_sent ? '✓ Sent' : '✗ Not sent' }}</span>
        <span>IP: {{ $lead->ip_address ?: '—' }}</span>
    </div>

    <div class="bg-white border rounded-lg p-5">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Update Lead</h2>
        <form method="POST" action="{{ route('admin.leads.status', $lead->id) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm">
                    @foreach(\App\Models\Lead::STATUSES as $key => $label)
                        <option value="{{ $key }}" {{ $lead->status == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Internal Notes</label>
                <textarea name="admin_notes" rows="4" class="w-full border rounded-lg px-3 py-2 text-sm"
                    placeholder="Notes for your team...">{{ old('admin_notes', $lead->admin_notes) }}</textarea>
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium">Save Changes</button>
                @if($lead->phone)
                <a href="tel:{{ $lead->phone }}" class="border px-5 py-2 rounded-lg text-sm">Call Customer</a>
                @endif
                <a href="mailto:{{ $lead->email }}" class="border px-5 py-2 rounded-lg text-sm">Email Customer</a>
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $lead->phone ?? '') }}" target="_blank"
                    class="border border-green-500 text-green-700 px-5 py-2 rounded-lg text-sm">WhatsApp</a>
            </div>
        </form>
    </div>
</div>
@endsection
