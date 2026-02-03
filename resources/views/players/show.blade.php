<x-sports-layout>
    <!-- Dynamic Player Header -->
    <x-sports.player-header :player="$player" :team="$player->team" />

    <div class="container mx-auto px-4 py-8">
        <!-- Navigation Tabs -->
        <div
            class="flex space-x-8 border-b border-gray-200 mb-8 text-sm font-bold uppercase tracking-wide overflow-x-auto">
            <a href="#" class="border-b-4 border-brand-primary text-brand-primary pb-3 px-1">Summary</a>
            <a href="#"
                class="border-b-4 border-transparent text-gray-500 hover:text-gray-900 pb-3 px-1 transition">Stats</a>
            <a href="#"
                class="border-b-4 border-transparent text-gray-500 hover:text-gray-900 pb-3 px-1 transition">Game
                Logs</a>
            <a href="#"
                class="border-b-4 border-transparent text-gray-500 hover:text-gray-900 pb-3 px-1 transition">Splits</a>
            <a href="#"
                class="border-b-4 border-transparent text-gray-500 hover:text-gray-900 pb-3 px-1 transition">Bio</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <h3 class="text-xl font-extrabold text-gray-900 uppercase tracking-tight mb-4">Season Stats</h3>

                <x-sports.stats-table :headers="['Year', 'Team', 'G', 'AB', 'R', 'H', 'HR', 'RBI', 'AVG', 'OPS']">
                    @forelse($stats as $stat)
                        <tr class="bg-gray-50 font-bold border-t-2 border-gray-100">
                            <td class="px-4 py-3 text-gray-900">{{ $stat->created_at->format('Y') }}</td>
                            <td class="px-4 py-3 text-gray-900 uppercase">{{ $player->team->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $stat->games_played ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $stat->at_bats ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $stat->runs ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $stat->hits ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $stat->home_runs ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $stat->rbi ?? '-' }}</td>
                            <td class="px-4 py-3 text-brand-primary text-base">{{ $stat->avg ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ $stat->ops ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-6 text-center text-gray-500">No stats available for this player.
                            </td>
                        </tr>
                    @endforelse
                </x-sports.stats-table>

                <div class="mt-8">
                    <h3 class="text-xl font-extrabold text-gray-900 uppercase tracking-tight mb-4">Recent Games</h3>
                    <x-sports.stats-table :headers="['Date', 'Opp', 'Result', 'AB', 'H', 'HR', 'RBI']">
                        <tr class="odd:bg-white even:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-gray-600">Oct 02</td>
                            <td class="px-4 py-3 font-bold">@ BOS</td>
                            <td class="px-4 py-3 text-green-600 font-bold">W 5-2</td>
                            <td class="px-4 py-3">4</td>
                            <td class="px-4 py-3">2</td>
                            <td class="px-4 py-3">1</td>
                            <td class="px-4 py-3">2</td>
                        </tr>
                        <tr class="odd:bg-white even:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-gray-600">Oct 01</td>
                            <td class="px-4 py-3 font-bold">@ BOS</td>
                            <td class="px-4 py-3 text-red-600 font-bold">L 3-4</td>
                            <td class="px-4 py-3">3</td>
                            <td class="px-4 py-3">1</td>
                            <td class="px-4 py-3">0</td>
                            <td class="px-4 py-3">0</td>
                        </tr>
                    </x-sports.stats-table>
                </div>
            </div>

            <!-- Bio Sidebar -->
            <div>
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-100">
                    <h4 class="font-bold text-gray-900 uppercase mb-4">Player Bio</h4>
                    <ul class="text-sm space-y-3">
                        <li class="flex justify-between">
                            <span class="text-gray-500">Born:</span>
                            <span class="font-medium">1992-04-26</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-500">Draft:</span>
                            <span class="font-medium">2013, Rd 1 (32)</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-500">College:</span>
                            <span class="font-medium">Fresno State</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-500">Debut:</span>
                            <span class="font-medium">2016-08-13</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-sports-layout>