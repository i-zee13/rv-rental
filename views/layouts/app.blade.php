<!doctype html>
+<html lang="{{ app()->getLocale() }}">
+<head>
+    <meta charset="utf-8">
+    <meta name="viewport" content="width=device-width, initial-scale=1">
+    <title>@yield('title', config('app.name', 'MV Rental'))</title>
+    <meta name="description" content="@yield('meta_description', 'Premium vehicle & RV rental marketplace')">
+
+    <!-- Tailwind CDN -->
+    <script src="https://cdn.tailwindcss.com"></script>
+    <!-- Alpine.js -->
+    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
+</head>
+<body class="bg-white text-gray-900">
+    <header class="bg-white border-b">
+        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
+            <a href="/" class="font-bold text-xl">{{ config('app.name', 'MV Rental') }}</a>
+            <nav class="hidden md:flex gap-4 items-center">
+                <a href="/" class="text-sm">Home</a>
+                <a href="/search" class="text-sm">Search</a>
+                <a href="/blog" class="text-sm">Blog</a>
+                <a href="/admin" class="text-sm bg-yellow-500 text-black px-3 py-2 rounded">Admin</a>
+                <div class="ml-4">
+                    <form method="GET" action="/set-locale">
+                        <select name="locale" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm">
+                            <option value="en" {{ app()->getLocale()=='en' ? 'selected' : '' }}>English</option>
+                            <option value="es" {{ app()->getLocale()=='es' ? 'selected' : '' }}>Español</option>
+                        </select>
+                    </form>
+                </div>
+            </nav>
+            <div class="md:hidden">
+                <button x-data @click="open=true">Menu</button>
+            </div>
+        </div>
+    </header>
+
+    <main class="container mx-auto px-4 py-8">
+        @yield('content')
+    </main>
+
+    <footer class="bg-gray-50 border-t mt-12">
+        <div class="container mx-auto px-4 py-6 text-sm text-gray-600">
+            © {{ date('Y') }} {{ config('app.name', 'MV Rental') }}. All rights reserved.
+        </div>
+    </footer>
+</body>
+</html>
+