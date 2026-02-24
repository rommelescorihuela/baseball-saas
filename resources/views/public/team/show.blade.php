<x-layouts.public>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ $team->name }}</h1>
            <p class="text-gray-500 text-lg">{{ $team->city ?? 'Unknown City' }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            {{-- Roster --}}
            <div>
                <h2 class="text-xl font-bold mb-4">Roster</h2>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <ul class="divide-y divide-gray-200">
                        @forelse($team->players as $player)
                        <li class="p-4 flex items-center justify-between hover:bg-gray-50">
                            <div>
                                <a href="{{ route('public.player.show', $player) }}"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                    {{ $player->name }} {{ $player->last_name }}
                                </a>
                                <div class="text-sm text-gray-500">#{{ $player->number }} &bull; {{ $player->position }}
                                </div>
                            </div>
                            <div class="text-sm text-gray-500">
                                AVG: {{ $player->currentStats?->avg ?? '.000' }}
                            </div>
                        </li>
                        @empty
                        <li class="p-4 text-center text-gray-500">No players on roster.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Schedule --}}
            <div>
                <h2 class="text-xl font-bold mb-4">Schedule</h2>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <ul class="divide-y divide-gray-200">
                        @forelse($games as $game)
                        <li class="p-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="text-sm font-medium text-gray-500 w-20">
                                        {{ $game->start_time->format('M d') }}
                                    </div>
                                    <div class="text-sm">
                                        @if($game->home_team_id === $team->id)
                                        vs {{ $game->visitorTeam->name }}
                                        @else
                                        @ {{ $game->homeTeam->name }}
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    @if($game->status === 'finished')
                                    <span class="font-bold">
                                        @if($game->home_team_id === $team->id)
                                        {{ $game->home_score }}-{{ $game->visitor_score }}
                                        @else
                                        {{ $game->visitor_score }}-{{ $game->home_score }}
                                        @endif
                                    </span>
                                    @else
                                    <span class="text-xs text-gray-500">{{ ucfirst($game->status) }}</span>
                                    @endif
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="p-4 text-center text-gray-500">No games scheduled.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>

    </div>
</x-layouts.public>