<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $seoTitle ?? ($title ?? config('app.name', 'Baseball SaaS')) }} - DiamondOS</title>

    <!-- SEO & OpenGraph Meta Tags -->
    <meta name="description"
        content="{{ $seoDescription ?? 'La plataforma profesional de estadísticas y seguimiento en vivo para Béisbol.' }}">
    <meta property="og:title" content="{{ $seoTitle ?? 'DiamondOS Analytics' }}">
    <meta property="og:description"
        content="{{ $seoDescription ?? 'Plataforma oficial de seguimiento de Béisbol Profesional.' }}">
    <meta property="og:image" content="{{ $seoImage ?? asset('img/diamond-os-banner.jpg') }}">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Outfit:wght@400;600;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background-dark font-sans text-slate-100 antialiased">
    <div class="relative flex min-h-screen flex-col overflow-x-hidden pb-32">
        <!-- Top Header (From Stitch) -->
        <header
            class="sticky top-0 z-50 flex items-center justify-between bg-background-dark/80 backdrop-blur-md px-6 py-4">
            <div class="flex items-center gap-2">
                <div class="flex size-10 items-center justify-center rounded-lg bg-primary text-background-dark">
                    <span class="material-symbols-outlined font-bold">sports_baseball</span>
                </div>
                <h1 class="font-display text-xl font-extrabold tracking-tight text-slate-100 italic uppercase">
                    DIAMOND<span class="text-primary">OS</span>
                </h1>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('public.home') }}"
                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400 hover:text-white transition hidden md:block">Home</a>
                <button class="flex size-10 items-center justify-center rounded-full glass text-slate-100">
                    <span class="material-symbols-outlined">notifications</span>
                </button>
                @auth
                    <a href="{{ url('/admin') }}"
                        class="flex size-10 items-center justify-center rounded-full glass text-primary">
                        <span class="material-symbols-outlined">dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 rounded-lg glass text-[10px] font-bold uppercase tracking-widest">Login</a>
                @endauth
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-grow">
            {{ $slot }}
        </main>

        <!-- Bottom Navigation Bar (Stitch Mobile-First Experience) -->
        <nav
            class="fixed bottom-0 left-0 right-0 z-50 px-4 pb-6 pt-2 bg-background-dark/80 backdrop-blur-xl border-t border-white/5">
            <div class="flex justify-between items-center max-w-md mx-auto">
                <a class="flex flex-col items-center gap-1 {{ request()->routeIs('public.home') ? 'text-primary' : 'text-slate-500' }}"
                    href="{{ route('public.home') }}">
                    <span
                        class="material-symbols-outlined {{ request()->routeIs('public.home') ? 'fill-1' : '' }}">home</span>
                    <span class="text-[8px] font-black uppercase tracking-widest">Home</span>
                </a>
                <a class="flex flex-col items-center gap-1 text-slate-500 hover:text-primary transition-colors"
                    href="#">
                    <span class="material-symbols-outlined">analytics</span>
                    <span class="text-[8px] font-black uppercase tracking-widest">Stats</span>
                </a>
                <div class="relative -mt-10">
                    <button
                        class="size-14 rounded-full bg-primary text-background-dark shadow-xl shadow-primary/40 flex items-center justify-center border-4 border-background-dark">
                        <span class="material-symbols-outlined font-bold text-2xl">add</span>
                    </button>
                </div>
                <a class="flex flex-col items-center gap-1 text-slate-500 hover:text-primary transition-colors"
                    href="#competitions-section">
                    <span class="material-symbols-outlined">emoji_events</span>
                    <span class="text-[8px] font-black uppercase tracking-widest">Leagues</span>
                </a>
                <a class="flex flex-col items-center gap-1 text-slate-500 hover:text-primary transition-colors"
                    href="{{ route('login') }}">
                    <span class="material-symbols-outlined">person</span>
                    <span class="text-[8px] font-black uppercase tracking-widest">Profile</span>
                </a>
            </div>
        </nav>
    </div>
</body>

</html>