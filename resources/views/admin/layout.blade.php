<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — @yield('title', 'Dashboard') | {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-dropify.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .sidebar-link { display:flex; align-items:center; gap:.6rem; padding:.55rem .75rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:#4b5563; transition:all .15s; }
        .sidebar-link:hover { background:#f3f4f6; color:#111827; }
        .sidebar-link.active { background:#eef2ff; color:#4f46e5; font-weight:600; }
        .sidebar-link .icon { font-size:1rem; flex-shrink:0; }
        .admin-table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
        .admin-table-wrap table { min-width:640px; }
        .admin-page-header { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:.75rem; margin-bottom:1rem; }
        .btn-sm { display:inline-flex; align-items:center; justify-content:center; padding:.25rem .625rem; font-size:.75rem; font-weight:500; line-height:1.25; border-radius:.375rem; border:1px solid transparent; transition:background .15s,color .15s; white-space:nowrap; }
        .btn-sm-primary { background:#4f46e5; color:#fff; }
        .btn-sm-primary:hover { background:#4338ca; }
        .btn-sm-secondary { background:#fff; color:#374151; border-color:#d1d5db; }
        .btn-sm-secondary:hover { background:#f9fafb; }
        .btn-sm-warning { background:#eab308; color:#111; }
        .btn-sm-warning:hover { background:#ca8a04; }
        .btn-sm-danger { background:#fff; color:#dc2626; border-color:#fecaca; cursor:pointer; }
        .btn-sm-danger:hover { background:#fef2f2; }
        div.dt-container { font-size:.875rem; }
        div.dt-container .dt-search input { border:1px solid #d1d5db; border-radius:.375rem; padding:.375rem .625rem; margin-left:.5rem; }
        div.dt-container .dt-length select { border:1px solid #d1d5db; border-radius:.375rem; padding:.25rem .5rem; margin:0 .5rem; }
        div.dt-container .dt-paging .dt-paging-button { border-radius:.375rem !important; }
        table.admin-datatable thead th { background:#f9fafb; font-size:.75rem; text-transform:uppercase; letter-spacing:.025em; color:#6b7280; }
        @media (max-width: 639px) {
            .admin-page-header { flex-direction:column; align-items:stretch; }
            .admin-page-header a, .admin-page-header button { text-align:center; }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
<div class="flex min-h-screen">

    {{-- ============================================================
         SIDEBAR
    ============================================================ --}}
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
        class="fixed md:static inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-100 shadow-sm flex flex-col transition-transform duration-200">

        {{-- Logo --}}
        <div class="p-5 border-b border-gray-100">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-yellow-400 flex items-center justify-center text-white font-bold text-sm shadow">MV</div>
                <div>
                    <div class="font-bold text-gray-900 text-sm leading-tight">{{ config('app.name') }}</div>
                    <div class="text-xs text-gray-400">Admin Panel</div>
                </div>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 p-4 space-y-0.5 overflow-y-auto">

            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 pt-2 pb-1">Overview</div>
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="icon">📊</span> Dashboard
            </a>

            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 pt-4 pb-1">Fleet</div>
            <a href="{{ route('admin.vehicles.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}">
                <span class="icon">🚗</span> Vehicles
            </a>
            <a href="{{ route('admin.categories.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <span class="icon">🏷️</span> Categories
            </a>
            <a href="{{ route('admin.properties.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.properties.*') ? 'active' : '' }}">
                <span class="icon">🏠</span> Homes & Apartments
            </a>

            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 pt-4 pb-1">Reservations</div>
            <a href="{{ route('admin.bookings.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                <span class="icon">📋</span> Bookings
            </a>
            <a href="{{ route('admin.addons.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.addons.*') ? 'active' : '' }}">
                <span class="icon">➕</span> Add-ons
            </a>
            <a href="{{ route('admin.leads.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}">
                <span class="icon">📨</span> Leads
            </a>

            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 pt-4 pb-1">Marketing</div>
            <a href="{{ route('admin.seo.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">
                <span class="icon">🔍</span> SEO
            </a>

            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 pt-4 pb-1">Content</div>
            <a href="{{ route('admin.blog.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}">
                <span class="icon">✍️</span> Blog Posts
            </a>
            <a href="{{ route('admin.pages.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                <span class="icon">📄</span> Pages
            </a>
            <a href="{{ route('admin.faqs.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
                <span class="icon">❓</span> FAQs
            </a>
            <a href="{{ route('admin.site-texts.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.site-texts.*') ? 'active' : '' }}">
                <span class="icon">🌐</span> Site Texts (EN/ES)
            </a>
        </nav>

        {{-- Footer --}}
        <div class="p-4 border-t border-gray-100">
            <a href="{{ route('home') }}" target="_blank"
                class="sidebar-link text-gray-500 hover:text-gray-800 mb-1">
                <span class="icon">🌐</span> View Website
            </a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="sidebar-link w-full text-left text-red-500 hover:bg-red-50 hover:text-red-700">
                    <span class="icon">🚪</span> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Mobile backdrop --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
        class="fixed inset-0 bg-black/40 z-30 md:hidden"></div>

    {{-- ============================================================
         MAIN CONTENT
    ============================================================ --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Top Header --}}
        <header class="bg-white border-b border-gray-100 px-4 lg:px-6 py-3 flex items-center justify-between sticky top-0 z-20 shadow-sm">
            <div class="flex items-center gap-3">
                {{-- Mobile sidebar toggle --}}
                <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="font-bold text-gray-900">@yield('title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center gap-3">
                @if(session('success'))
                <div class="hidden md:block bg-green-50 text-green-700 text-xs px-3 py-1.5 rounded-full font-medium border border-green-200">
                    ✓ {{ session('success') }}
                </div>
                @endif
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                    {{ substr(auth()->user()?->name ?? 'A', 0, 1) }}
                </div>
            </div>
        </header>

        {{-- Flash Message (mobile) --}}
        @if(session('success'))
        <div class="md:hidden mx-4 mt-3 bg-green-50 text-green-700 text-sm px-4 py-2 rounded-xl border border-green-200">
            ✓ {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mx-4 mt-3 bg-red-50 text-red-700 text-sm px-4 py-2 rounded-xl border border-red-200">
            ✗ {{ session('error') }}
        </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 p-4 lg:p-6 overflow-auto">
            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
<script src="{{ asset('js/admin-dropify.js') }}"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="{{ asset('js/admin-datatables.js') }}?v=1"></script>
<script>
window.MvAdminAi = {
    descriptionsUrl: @json(route('admin.ai.descriptions')),
    seoUrl: @json(route('admin.ai.seo')),
};
</script>
<script src="{{ asset('js/admin-ai-content.js') }}?v=1"></script>
@stack('scripts')
</body>
</html>
