<!doctype html>
<html class="h-full bg-white" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title }} - {{ config('app.name') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="h-full">
    <x-layout.mobile-sidebar />
    <x-layout.desktop-sidebar />

    <div class="lg:pl-72">
        <x-layout.topbar />

        <main class="py-10">
            <div class="px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
