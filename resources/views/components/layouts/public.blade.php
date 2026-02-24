<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Baseball SaaS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 text-gray-900">
    <div class="min-h-screen">
        {{-- Navigation --}}
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        {{-- Logo --}}
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('public.home') }}" class="font-bold text-2xl text-indigo-600">
                                Baseball SaaS
                            </a>
                        </div>

                        {{-- Links --}}
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('public.home') }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('public.home') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                                Home
                            </a>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        @if (Route::has('login'))
                        <div class="space-x-4">
                            @auth
                            <a href="{{ url('/admin') }}" class="text-sm text-gray-700 underline">Admin</a>
                            @else
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Log in</a>
                            @endauth
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        {{-- Page Content --}}
        <main>
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="bg-white border-t mt-12">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-gray-500 text-sm">
                    &copy; {{ date('Y') }} Baseball SaaS. All rights reserved.
                </p>
            </div>
        </footer>
    </div>
</body>

</html>