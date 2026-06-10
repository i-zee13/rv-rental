<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - @yield('title','Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-white border-r hidden md:block">
            <div class="p-4 font-bold">{{ config('app.name') }} Admin</div>
            <nav class="p-4">
                <a href="{{ route('admin.dashboard') }}" class="block py-2">Dashboard</a>
                <a href="{{ route('admin.vehicles.index') }}" class="block py-2">Vehicles</a>
            </nav>
        </aside>

        <div class="flex-1">
            <header class="bg-white border-b p-4 flex items-center justify-between">
                <div class="font-semibold">Admin</div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="text-sm text-red-600">Logout</button>
                </form>
            </header>

            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
