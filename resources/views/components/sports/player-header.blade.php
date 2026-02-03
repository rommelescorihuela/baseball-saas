@props(['player', 'team'])

<div class="relative w-full overflow-hidden text-white"
    style="background: linear-gradient(135deg, {{ $team->primary_color ?? '#0f172a' }} 0%, #000000 100%);">
    <!-- Abstract Background Pattern -->
    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml;base64,...');"></div>

    <div class="container mx-auto px-4 relative z-10 flex flex-col md:flex-row items-center md:items-end pt-12 pb-0">

        <!-- Player Image (Cutout effect) -->
        <div class="w-48 h-48 md:w-64 md:h-64 flex-shrink-0 -mb-4 md:-mb-8 relative z-20">
            <div
                class="w-full h-full rounded-full bg-gray-200 border-4 border-white flex items-center justify-center overflow-hidden shadow-2xl">
                <svg class="h-32 w-32 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>

        <!-- Info -->
        <div class="flex-1 md:ml-8 text-center md:text-left pb-8 md:pb-12">
            <div class="flex items-center justify-center md:justify-start space-x-3 mb-2">
                <span
                    class="text-3xl md:text-5xl font-mono text-white/30 font-bold">#{{ $player->dob ? (substr($player->dob, -2)) : '99' }}</span>
                <!-- Fallback number -->
                <span
                    class="text-xl font-bold uppercase tracking-wider text-brand-secondary">{{ $player->position ?? 'OF' }}</span>
            </div>

            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tighter leading-none mb-2">
                {{ $player->first_name }} <span class="block md:inline">{{ $player->last_name }}</span>
            </h1>

            <div
                class="flex flex-wrap items-center justify-center md:justify-start gap-4 text-sm font-medium text-gray-300">
                <span>{{ $team->name }}</span>
                <span>•</span>
                <span>B/T: R/R</span>
                <span>•</span>
                <span>6' 7" / 282 LBS</span>
                <span>•</span>
                <span>Age: 31</span>
            </div>

            <div class="mt-6 flex space-x-4 justify-center md:justify-start">
                <button
                    class="bg-brand-secondary hover:bg-red-700 text-white font-bold py-2 px-6 rounded-full uppercase tracking-wide text-xs transition">
                    Follow
                </button>
                <button
                    class="bg-white/10 hover:bg-white/20 text-white font-bold py-2 px-6 rounded-full uppercase tracking-wide text-xs transition border border-white/30">
                    Stats
                </button>
            </div>
        </div>

        <!-- Quick Stats Overlay (Big Numbers) -->
        <div class="hidden lg:flex space-x-8 pb-12 opacity-80">
            <div class="text-center">
                <div class="text-4xl font-bold">.322</div>
                <div class="text-xs uppercase tracking-widest text-gray-400">AVG</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold">52</div>
                <div class="text-xs uppercase tracking-widest text-gray-400">HR</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold">114</div>
                <div class="text-xs uppercase tracking-widest text-gray-400">RBI</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold">1.084</div>
                <div class="text-xs uppercase tracking-widest text-gray-400">OPS</div>
            </div>
        </div>
    </div>
</div>