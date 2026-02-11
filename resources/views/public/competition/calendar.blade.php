<x-layouts.public>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <div class="mb-6">
            <a href="{{ route('public.competition.show', $competition) }}"
                class="text-indigo-600 hover:text-indigo-800 font-medium">
                &larr; Back to Standings
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ $competition->name }} Calendar</h1>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <ul class="divide-y divide-gray-200">
                @forelse($games as $game)
                <li class="p-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="text-sm font-medium text-gray-500 w-24">
                                {{ $game->start_time->format('M d, H:i') }}
                            </div>
                            <div>
                                <div class="text-lg font-bold text-gray-900">
                                    {{ $game->homeTeam->name }} <span class="text-gray-400 text-sm">vs</span> {{
                                    $game->visitorTeam->name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $game->category->name }} &bull; {{ $game->location ?? 'TBD' }}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            @if($game->status === 'finished')
                            <span class="text-lg font-bold text-gray-900">{{ $game->home_score }} - {{
                                $game->visitor_score }}</span>
                            @endif
                            <span
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ ucfirst($game->status) }}
                            </span>
                        </div>
                    </div>
                </li>
                @empty
                <li class="p-4 text-center text-gray-500">No games scheduled.</li>
                @endforelse
            </ul>
        </div>

        <div class="mt-4">
            {{ $games->links() }}
        </div>

    </div>
</x-layouts.public>