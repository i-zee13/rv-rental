@extends('admin.layout')

@section('title', 'Profile Settings')

@section('content')
@php
    $user = $user ?? auth()->user();
    $avatarUrl = $user->avatarUrl();
@endphp

<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Profile Settings</h2>
        <p class="text-sm text-gray-500 mt-1">Update your name, photo, and password. Email cannot be changed.</p>
    </div>

    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Avatar --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Profile picture</h3>
            <div class="flex flex-col sm:flex-row items-start gap-5">
                <div class="shrink-0">
                    @if($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $user->name }}"
                            class="w-20 h-20 rounded-full object-cover border-2 border-indigo-100 shadow-sm">
                    @else
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold shadow-sm">
                            {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="flex-1 w-full space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload new photo</label>
                        <input type="file" name="avatar" accept="image/*"
                            class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-medium hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP or GIF — max 2 MB</p>
                        @error('avatar')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    @if($avatarUrl)
                        <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="remove_avatar" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            Remove current photo
                        </label>
                    @endif
                </div>
            </div>
        </div>

        {{-- Profile info --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
            <h3 class="font-semibold text-gray-900">Account details</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" value="{{ $user->email }}" disabled readonly
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
                <p class="text-xs text-gray-400 mt-1">Email cannot be changed from this screen.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <input type="text" value="{{ str_replace('_', ' ', ucfirst($user->role ?? 'admin')) }}" disabled readonly
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
            </div>
        </div>

        {{-- Password --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
            <div>
                <h3 class="font-semibold text-gray-900">Change password</h3>
                <p class="text-xs text-gray-500 mt-1">Leave blank if you do not want to change your password.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current password</label>
                <input type="password" name="current_password" autocomplete="current-password"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('current_password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New password</label>
                    <input type="password" name="password" autocomplete="new-password"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('password')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm new password</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.dashboard') }}" class="btn-sm btn-sm-secondary px-4 py-2">Cancel</a>
            <button type="submit" class="btn-sm btn-sm-primary px-5 py-2">Save changes</button>
        </div>
    </form>
</div>
@endsection
