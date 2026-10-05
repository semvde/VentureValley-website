@props(['title'])

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ html_entity_decode($title ?? config('app.name')) }}</title>

    <link rel="canonical" href="{{ url()->current() }}">
    @stack('head')

    <!-- Scripts -->
    <script src="https://kit.fontawesome.com/d0c003e79c.js" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

<nav>
    <div class="flex items-center justify-between bg-gray-900 text-white px-3 py-3">
        <a href="{{ route('home') }}"><i class="fa-solid fa-angle-left"></i> Website</a>
        <a href="{{ route('profile.edit') }}">Hey, {{ auth()->user()->name }}</a>
    </div>
</nav>

<div class="flex grow max-sm:flex-col">
    <nav class="bg-gray-900 text-white min-w-44">
        <div class="admin-nav flex flex-col overflow-x-auto max-sm:flex-row">
            <a href="{{ route('admin.index') }}"
               class="flex-shrink-0 px-2.5 py-2.5 @if(Route::currentRouteName() === "admin.index") font-semibold bg-[--color-primary] @endif">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
            <hr class="border-2 border-gray-800 max-sm:hidden">
            <a href="{{ route('admin.attracties.index') }}"
               class="flex-shrink-0 px-2.5 py-2.5 @if(request()->routeIs('admin.attracties.*')) font-semibold bg-[--color-primary] @endif">
                <i class="fa-solid fa-ticket"></i> Attracties
            </a>
            <a href="{{ route('admin.parkshows.index') }}"
               class="flex-shrink-0 px-2.5 py-2.5 @if(request()->routeIs('admin.parkshows.*')) font-semibold bg-[--color-primary] @endif">
                <i class="fa-solid fa-masks-theater"></i> Parkshows
            </a>
            <a href="{{ route('admin.restaurants.index') }}"
               class="flex-shrink-0 px-2.5 py-2.5 @if(request()->routeIs('admin.restaurants.*')) font-semibold bg-[--color-primary] @endif">
                <i class="fa-solid fa-utensils"></i> Eten & drinken
            </a>
            <a href="{{ route('admin.souvenirs.index') }}"
               class="flex-shrink-0 px-2.5 py-2.5 @if(request()->routeIs('admin.souvenirs.*')) font-semibold bg-[--color-primary] @endif">
                <i class="fa-solid fa-utensils"></i> Souvenirs
            </a>
            <hr class="border-2 border-gray-800 max-sm:hidden">
            <a href="{{ route('admin.openingstijden.index') }}"
               class="flex-shrink-0 px-2.5 py-2.5 @if(request()->routeIs('admin.openingstijden.*')) font-semibold bg-[--color-primary] @endif">
                <i class="fa-solid fa-clock"></i> Openingstijden
            </a>
        </div>
    </nav>
    <main class="max-w-none min-w-[unset] px-5 py-3 max-sm:w-full" id="main">
        {{ $slot }}
    </main>
</div>

</body>

</html>
