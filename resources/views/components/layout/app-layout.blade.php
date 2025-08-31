<!doctype html>
<html class="h-full bg-white" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - {{ config('app.name') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
</head>
<body class="h-full">
    <x-layout.mobile-sidebar />
    <x-layout.desktop-sidebar />

    <div class="lg:pl-72">
        <x-layout.topbar />
        {{ $slot }}
    </div>
</body>
</html>
