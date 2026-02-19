<x-layouts.public>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- Player Profile Header --}}
        <div class="bg-white shadow rounded-lg p-6 mb-8 flex items-center space-x-6">
            <div
                class="h-24 w-24 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 text-4xl font-bold">
                {{ substr($player->name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $player->name }} {{ $player->last_name }}</h1>
                <p class="text-xl text-gray-500">
                    #{{ $player->number }} &bull; {{ $player->position }}
                </p>
                <p class="text-indigo-600 font-medium">
                    <a href="{{ route('public.team.show', $player->team) }}">{{ $player->team->name }}</a>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- Career / Season Stats Overview --}}
            <div class="md:col-span-1">
                <h2 class="text-xl font-bold mb-4">Season Stats</h2>
                <div class="bg-indigo-700 text-white rounded-lg p-6 shadow-lg">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center border-r border-indigo-500 pb-4">
                            <span class="block text-3xl font-bold">{{ $player->currentStats?->avg ?? '.000' }}</span>
                            <span class="text-indigo-200 text-xs uppercase tracking-wider">AVG</span>
                        </div>
                        <div class="text-center pb-4">
                            <span class="block text-3xl font-bold">{{ $player->currentStats?->hr ?? 0 }}</span>
                            <span class="text-indigo-200 text-xs uppercase tracking-wider">HR</span>
                        </div>
                        <div class="text-center border-r border-indigo-500">
                            <span class="block text-3xl font-bold">{{ $player->currentStats?->rbi ?? 0 }}</span>
                            <span class="text-indigo-200 text-xs uppercase tracking-wider">RBI</span>
                        </div>
                        <div class="text-center">
                            <span class="block text-3xl font-bold">{{ $player->currentStats?->h ?? 0 }}</span>
                            <span class="text-indigo-200 text-xs uppercase tracking-wider">HITS</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Games Log --}}
            <div class="md:col-span-2">
                <h2 class="text-xl font-bold mb-4">Recent Games</h2>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Opponent</th>
                                <th class="px-4 py-3">AB</th>
                                <th class="px-4 py-3">R</th>
                                <th class="px-4 py-3">H</th>
                                <th class="px-4 py-3">RBI</th>
                                <th class="px-4 py-3">AVG</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($player->stats->sortByDesc(fn($s) => $s->game->start_time)->take(10) as $stat)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $stat->game->start_time->format('M d') }}</td>
                                <td class="px-4 py-3">
                                    @if($stat->game->home_team_id === $player->team_id)
                                    vs {{ $stat->game->visitorTeam->name }}
                                    @else
                                    @ {{ $stat->game->homeTeam->name }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $stat->ab }}</td>
                                <td class="px-4 py-3">{{ $stat->r }}</td>
                                <td class="px-4 py-3">{{ $stat->h }}</td>
                                <td class="px-4 py-3">{{ $stat->rbi }}</td>
                                <td class="px-4 py-3 font-medium">
                                    {{ $stat->ab > 0 ? number_format($stat->h / $stat->ab, 3, '.', '') : '.000' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">No game data available.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</x-layouts.public>