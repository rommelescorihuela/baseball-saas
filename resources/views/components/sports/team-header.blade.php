@props(['team', 'logo' => null])

<div class="w-full text-white" style="background-color: {{ $team->primary_color ?? '#0f172a' }}">
    <div
        class="container mx-auto px-4 py-6 md:py-8 flex flex-col md:flex-row items-center md:items-end justify-between">
        <div class="flex items-center space-x-6">
            <!-- Team Logo Placeholder -->
            <div
                class="bg-white rounded-full p-2 shadow-lg h-24 w-24 flex items-center justify-center text-gray-800 font-bold text-2xl border-4 border-gray-200">
                @if($team->logo)
                    <img src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}" class="h-16 w-16 object-contain">
                @else
                    {{ substr($team->name, 0, 2) }}
                @endif
            </div>

            <!-- Team Info -->
            <div class="text-center md:text-left">
                <h2 class="text-sm font-bold opacity-80 uppercase tracking-widest">{{ $team->league->name ?? 'LEAGUE' }}
                </h2>
                <h1 class="text-4xl md:text-6xl font-extrabold tracking-tighter">{{ $team->name }}</h1>
                <p class="text-sm font-medium mt-1 opacity-90">Overall: 45-20 | Last 10: 7-3</p>
            </div>
        </div>

        <!-- Team Nav -->
        <nav
            class="mt-6 md:mt-0 flex space-x-1 md:space-x-6 text-sm font-bold uppercase tracking-wider overflow-x-auto w-full md:w-auto pb-2 md:pb-0">
            <a href="#" class="hover:text-white/80 border-b-4 border-white pb-1 px-2">Home</a>
            <a href="#"
                class="hover:text-white/80 border-b-4 border-transparent hover:border-white/50 pb-1 transition px-2">Schedule</a>
            <a href="#"
                class="hover:text-white/80 border-b-4 border-transparent hover:border-white/50 pb-1 transition px-2">Roster</a>
            <a href="#"
                class="hover:text-white/80 border-b-4 border-transparent hover:border-white/50 pb-1 transition px-2">Stats</a>
            <a href="#"
                class="hover:text-white/80 border-b-4 border-transparent hover:border-white/50 pb-1 transition px-2">News</a>
        </nav>
    </div>
</div>