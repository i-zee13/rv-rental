@extends('admin.layout')

@section('content')
<div class="max-w-md mx-auto mt-12 bg-white p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Admin Login</h2>
    @if($errors->any())
        <div class="text-red-600 text-sm mb-3">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf
        <label class="block text-sm">Email
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-2 py-2 mt-1">
        </label>
        <label class="block text-sm mt-3">Password
            <input type="password" name="password" class="w-full border rounded px-2 py-2 mt-1">
        </label>
        <div class="mt-4">
            <button class="w-full bg-yellow-500 text-black px-4 py-2 rounded">Login</button>
        </div>
    </form>
</div>
@endsection
