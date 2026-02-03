<x-sports-layout>
    <!-- Dynamic Header -->
    <x-sports.team-header :team="$team" />

    <div class="container mx-auto px-4 py-8">

        <!-- Quick Links / Breadcrumbs -->
        <div class="text-sm text-gray-500 mb-8 font-medium uppercase tracking-wide">
            <a href="/" class="hover:text-brand-primary">LEAGUE</a> / <span
                class="text-gray-900 font-bold">{{ $team->name }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Main Content Area -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Latest Team News -->
                <section>
                    <div class="flex items-center justify-between mb-4 border-b border-gray-200 pb-2">
                        <h2 class="text-2xl font-extrabold text-gray-900 uppercase tracking-tight">Latest News</h2>
                        <a href="#" class="text-xs font-bold text-brand-primary hover:underline uppercase">View All</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-sports.news-card title="Game Recap: {{ $team->name }} wins in extra innings" time="1h ago"
                            category="Recap" />
                        <x-sports.news-card title="Manager discusses upcoming rotation changes" time="3h ago"
                            category="Interview" />
                    </div>
                </section>

                <!-- Roster Highlight -->
                <section>
                    <div class="flex items-center justify-between mb-4 border-b border-gray-200 pb-2">
                        <h2 class="text-2xl font-extrabold text-gray-900 uppercase tracking-tight">Active Roster</h2>
                        <a href="#" class="text-xs font-bold text-brand-primary hover:underline uppercase">Full
                            Roster</a>
                    </div>

                    <x-sports.stats-table :headers="['#', 'Player', 'Pos', 'B/T', 'Age']">
                        @foreach($roster as $player)
                            <tr class="odd:bg-white even:bg-brand-light hover:bg-blue-50 transition">
                                <td class="px-4 py-3 font-mono text-gray-500 font-bold">#{{ substr($player->id, 0, 2) }}
                                </td>
                                <td class="px-4 py-3 font-bold text-gray-900">
                                    <a href="{{ route('players.show', ['team' => $team->subdomain, 'player' => $player]) }}"
                                        class="hover:text-brand-primary hover:underline">
                                        {{ $player->first_name }} {{ $player->last_name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-600">{{ $player->position ?? 'ATH' }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">R/R</td>
                                <td class="px-4 py-3 text-gray-500">24</td>
                            </tr>
                        @endforeach
                    </x-sports.stats-table>
                </section>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Team Leaders -->
                <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4 border-b pb-2">Team
                        Leaders</h3>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-gray-200"></div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900">Judge</div>
                                    <div class="text-xs text-gray-500">AVG</div>
                                </div>
                            </div>
                            <div class="text-xl font-bold text-brand-primary">.312</div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-gray-200"></div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900">Soto</div>
                                    <div class="text-xs text-gray-500">HR</div>
                                </div>
                            </div>
                            <div class="text-xl font-bold text-brand-primary">15</div>
                        </div>
                    </div>
                </div>

                <!-- Next Game -->
                <div class="bg-brand-primary rounded-lg p-6 text-white text-center">
                    <div class="text-xs font-bold uppercase tracking-widest opacity-70 mb-2">Next Game</div>
                    <div class="flex items-center justify-center space-x-4 mb-4">
                        <div class="text-2xl font-bold">NYY</div>
                        <div class="text-sm opacity-50">vs</div>
                        <div class="text-2xl font-bold">BOS</div>
                    </div>
                    <div class="text-sm font-medium mb-4">Tomorrow, 7:05 PM ET</div>
                    <button
                        class="w-full bg-white text-brand-primary font-bold py-2 rounded uppercase text-xs tracking-wide hover:bg-gray-100">
                        Get Tickets
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-sports-layout>