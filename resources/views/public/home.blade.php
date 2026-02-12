<x-layouts.public>

    {{-- Hero Section --}}
    <div class="bg-indigo-700 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl md:text-6xl">
                The Home of Baseball
            </h1>
            <p class="mt-6 max-w-2xl mx-auto text-xl text-indigo-200">
                Follow your favorite leagues, teams, and players. Live scores, detailed stats, and more.
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- Competitions Grid --}}
        <h2 class="text-2xl font-bold mb-6">Active Competitions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            @forelse($competitions as $competition)
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <h3 class="text-xl font-bold text-gray-900">{{ $competition->name }}</h3>
                <p class="text-indigo-600 font-semibold">{{ $competition->season->league->name }}</p>
                <p class="text-gray-500">{{ $competition->season->name }}</p>
                <a href="{{ route('public.competition.show', $competition) }}"
                    class="mt-4 inline-block text-indigo-600 hover:text-indigo-800 font-medium">
                    View Standings &rarr;
                </a>
            </div>
            @empty
            <p class="text-gray-500">No active competitions at the moment.</p>
            @endforelse
        </div>

        {{-- Upcoming Games --}}
        <h2 class="text-2xl font-bold mb-6">Upcoming Games</h2>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <ul class="divide-y divide-gray-200">
                @forelse($upcomingGames as $game)
                <li class="p-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="text-sm font-medium text-gray-500 w-24">
                                {{ $game->start_time->format('M d, H:i') }}
                            </div>
                            <div>
                                <div class="text-lg font-bold text-gray-900">
                                    {{ $game->homeTeam->name }} vs {{ $game->visitorTeam->name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $game->category->name }} &bull; {{ $game->location ?? 'TBD' }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <span
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ ucfirst($game->status) }}
                            </span>
                        </div>
                    </div>
                </li>
                @empty
                <li class="p-4 text-gray-500 text-center">No upcoming games scheduled.</li>
                @endforelse
            </ul>
        </div>

    </div>

</x-layouts.public>